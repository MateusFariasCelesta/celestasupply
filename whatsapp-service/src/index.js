import makeWASocket, {
  useMultiFileAuthState,
  DisconnectReason,
  fetchLatestBaileysVersion,
} from '@whiskeysockets/baileys'
import express    from 'express'
import qrcode     from 'qrcode'
import { DatabaseSync } from 'node:sqlite'
import pino       from 'pino'
import { fileURLToPath } from 'url'
import path       from 'path'
import dotenv     from 'dotenv'

dotenv.config()

const __dirname   = path.dirname(fileURLToPath(import.meta.url))
const ROOT        = path.join(__dirname, '..')
const PORT        = process.env.PORT        || 3001
const API_TOKEN   = process.env.API_TOKEN   || ''
const WA_PHONE    = (process.env.WA_PHONE_NUMBER || '').replace(/\D/g, '')

// ── Banco de dados ────────────────────────────────────────────────────────────
const db = new DatabaseSync(path.join(ROOT, 'messages.db'))

db.exec(`
  CREATE TABLE IF NOT EXISTS messages (
    id         INTEGER  PRIMARY KEY AUTOINCREMENT,
    to_number  TEXT     NOT NULL,
    body       TEXT     NOT NULL,
    status     TEXT     NOT NULL DEFAULT 'pending',
    error      TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    sent_at    DATETIME
  )
`)

const stmtInsert     = db.prepare('INSERT INTO messages (to_number, body) VALUES (?, ?)')
const stmtMarkSent   = db.prepare("UPDATE messages SET status = 'sent',   sent_at = CURRENT_TIMESTAMP WHERE id = ?")
const stmtMarkFailed = db.prepare("UPDATE messages SET status = 'failed', error   = ?                 WHERE id = ?")
const stmtPending    = db.prepare("SELECT * FROM messages WHERE status = 'pending' ORDER BY id")

// ── Estado global ─────────────────────────────────────────────────────────────
let sock             = null
let qrDataUrl        = null
let connectionStatus = 'disconnected'  // disconnected | qr_pending | connected
let queue            = []              // { id, to, body }
let processing       = false

const logger = pino({ level: 'silent' })
const sleep  = (ms) => new Promise((r) => setTimeout(r, ms))
const clean  = (phone) => phone.replace(/\D/g, '')
const toJid  = (phone) => {
  let n = clean(phone)
  if (n.length <= 11) n = '55' + n   // sem DDI → assume Brasil
  return `${n}@s.whatsapp.net`
}

// ── Fila de envio ─────────────────────────────────────────────────────────────
async function processQueue() {
  if (processing || connectionStatus !== 'connected' || queue.length === 0) return
  processing = true

  while (queue.length > 0 && connectionStatus === 'connected') {
    const { id, to, body } = queue[0]
    try {
      await sock.sendMessage(toJid(to), { text: body })
      stmtMarkSent.run(id)
      console.log(`[WA] ✓ Enviado para ${to} (id=${id})`)
    } catch (err) {
      stmtMarkFailed.run(String(err?.message ?? err), id)
      console.error(`[WA] ✗ Falha ao enviar para ${to} (id=${id}):`, err?.message)
    }
    queue.shift()
    if (queue.length > 0) await sleep(1200) // intervalo entre mensagens
  }

  processing = false
}

function reloadPendingFromDb() {
  queue = stmtPending.all().map(({ id, to_number, body }) => ({ id, to: to_number, body }))
  console.log(`[WA] ${queue.length} mensagem(ns) pendente(s) na fila`)
}

// ── Conexão Baileys ───────────────────────────────────────────────────────────
async function connect() {
  const { state, saveCreds } = await useMultiFileAuthState(path.join(ROOT, 'sessions'))
  const { version }          = await fetchLatestBaileysVersion()

  sock = makeWASocket({ version, auth: state, logger })

  // Pairing code (se WA_PHONE_NUMBER estiver configurado e ainda não autenticado)
  if (WA_PHONE && !state.creds.registered) {
    // Aguarda o socket estar pronto para receber o pedido
    await new Promise((r) => setTimeout(r, 3000))
    try {
      const code = await sock.requestPairingCode(WA_PHONE)
      const formatted = code.match(/.{1,4}/g)?.join('-') ?? code
      console.log('\n╔══════════════════════════════╗')
      console.log(`║  Código de pareamento: ${formatted}  ║`)
      console.log('╚══════════════════════════════╝')
      console.log('\nNo WhatsApp: Configurações → Dispositivos conectados → Conectar com número de telefone\n')
    } catch (err) {
      console.error('[WA] Erro ao solicitar código:', err.message)
    }
  }

  sock.ev.on('connection.update', async (update) => {
    const { connection, lastDisconnect, qr } = update

    // QR como fallback (quando WA_PHONE_NUMBER não está configurado)
    if (qr) {
      qrDataUrl        = await qrcode.toDataURL(qr)
      connectionStatus = 'qr_pending'
      console.log('\n[WA] Escaneie o QR abaixo com o WhatsApp:\n')
      qrcode.toString(qr, { type: 'terminal', small: true }, (err, str) => {
        if (!err) console.log(str)
      })
    }

    if (connection === 'close') {
      qrDataUrl        = null
      connectionStatus = 'disconnected'
      const code = lastDisconnect?.error?.output?.statusCode

      if (code === DisconnectReason.loggedOut) {
        console.log('[WA] Deslogado. Delete a pasta sessions/ e reinicie para gerar novo QR.')
      } else {
        console.log(`[WA] Desconectado (código ${code}). Reconectando em 6s...`)
        setTimeout(connect, 6000)
      }
    }

    if (connection === 'open') {
      qrDataUrl        = null
      connectionStatus = 'connected'
      console.log('[WA] ✅ Conectado ao WhatsApp!')
      reloadPendingFromDb()
      processQueue()
    }
  })

  sock.ev.on('creds.update', saveCreds)
}

// ── Express ───────────────────────────────────────────────────────────────────
const app = express()
app.use(express.json())

function auth(req, res, next) {
  if (!API_TOKEN) return next()
  const token = req.headers.authorization?.replace('Bearer ', '') || req.query.token
  if (token !== API_TOKEN) return res.status(401).json({ error: 'Não autorizado' })
  next()
}

// Health check (sem auth — para Railway/Render/balanceadores)
app.get('/health', (_req, res) => {
  res.json({ ok: true, status: connectionStatus, queue: queue.length })
})

// Status da conexão
app.get('/status', auth, (_req, res) => {
  res.json({ status: connectionStatus, queued: queue.length })
})

// QR como JSON (base64)
app.get('/qr', auth, (_req, res) => {
  if (!qrDataUrl) {
    return res.status(404).json({ error: 'Sem QR disponível', status: connectionStatus })
  }
  res.json({ qr: qrDataUrl, status: connectionStatus })
})

// Página HTML com QR — abra no navegador para escanear
app.get('/qr-page', auth, (_req, res) => {
  const body = qrDataUrl
    ? `<img src="${qrDataUrl}" style="width:280px;height:280px;display:block;margin:0 auto"/>
       <p style="text-align:center;color:#555;margin-top:12px">Escaneie com o WhatsApp</p>`
    : `<p style="text-align:center;font-size:18px">
         Status: <strong>${connectionStatus}</strong>
         ${connectionStatus === 'connected' ? '✅' : '⏳'}
       </p>`

  res.send(`<!doctype html><html><head><meta charset="utf-8">
    <title>CelestaSupply — WhatsApp</title></head>
    <body style="font-family:sans-serif;padding:40px;max-width:400px;margin:auto">
      <h2 style="text-align:center">CelestaSupply WhatsApp</h2>
      ${body}
      <script>setTimeout(()=>location.reload(),3000)</script>
    </body></html>`)
})

// Enviar mensagem
app.post('/send', auth, (req, res) => {
  const { to, message } = req.body

  if (!to || !message) {
    return res.status(400).json({ error: '"to" e "message" são obrigatórios' })
  }

  const number = clean(to)
  if (number.length < 10) {
    return res.status(400).json({ error: 'Número inválido' })
  }

  const { lastInsertRowid: id } = stmtInsert.run(number, message)
  queue.push({ id, to: number, body: message })
  processQueue()

  res.status(202).json({ id, status: 'queued' })
})

// Histórico de mensagens
app.get('/messages', auth, (_req, res) => {
  const rows = db.prepare('SELECT * FROM messages ORDER BY created_at DESC LIMIT 200').all()
  res.json(rows)
})

// ── Start ─────────────────────────────────────────────────────────────────────
app.listen(PORT, () => {
  console.log(`[WA] Serviço iniciado na porta ${PORT}`)
  connect()
})
