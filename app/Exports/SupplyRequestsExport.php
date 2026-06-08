<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment as XlAlign;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SupplyRequestsExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithTitle,
    WithCustomStartCell,
    WithEvents
{
    public function __construct(
        private Collection $data,
        private string     $reportTitle  = 'Relatório de Solicitações',
        private string     $filterDesc   = 'Todos os registros (sem filtros)',
        private Collection $summary      = new Collection(),
        private bool       $showBreakdown = true,
    ) {}

    // WithHeadings escreve na startCell; dados começam na linha seguinte.
    public function startCell(): string { return 'A9'; }

    public function collection(): Collection
    {
        return $this->data->map(fn($sr) => [
            $sr->code,
            $sr->title,
            $sr->costCenter->name,
            $sr->user->name,
            $sr->urgency->label(),
            $sr->status->label(),
            $sr->created_at->format('d/m/Y'),
            $sr->items->count(),
        ]);
    }

    public function headings(): array
    {
        return ['Código', 'Título', 'Centro de Custo', 'Solicitante', 'Urgência', 'Status', 'Data', 'Itens'];
    }

    public function title(): string { return 'Relatório'; }

    // Cabeçalho da tabela na linha 9 (= startCell)
    public function styles(Worksheet $sheet): array
    {
        return [
            9 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
                'alignment' => ['vertical' => XlAlign::VERTICAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 12, 'B' => 44, 'C' => 24, 'D' => 24, 'E' => 12, 'F' => 26, 'G' => 12, 'H' => 8];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet   = $event->sheet->getDelegate();
                $lastRow = 9 + $this->data->count(); // heading em 9, dados de 10+

                // ── Linha 1: Logo (esquerda) + Data (direita) ──
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setPath(public_path('images/celesta-mineracao-logo.png'));
                $drawing->setHeight(46);
                $drawing->setCoordinates('A1');
                $drawing->setOffsetX(8);
                $drawing->setOffsetY(6);
                $drawing->setWorksheet($sheet);

                $sheet->mergeCells('E1:H1');
                $sheet->setCellValue('E1', 'Gerado em ' . Carbon::now()->format('d/m/Y H:i'));
                $sheet->getStyle('E1')->applyFromArray([
                    'font'      => ['size' => 9, 'color' => ['rgb' => '9CA3AF']],
                    'alignment' => ['horizontal' => XlAlign::HORIZONTAL_RIGHT, 'vertical' => XlAlign::VERTICAL_BOTTOM],
                ]);

                // ── Linha 2: Separador azul escuro ──
                $sheet->mergeCells('A2:H2');
                $sheet->getStyle('A2:H2')->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1E3A5F');

                // ── Linha 3: Espaçador ──

                // ── Linha 4: Título do relatório ──
                $sheet->mergeCells('A4:H4');
                $sheet->setCellValue('A4', $this->reportTitle);
                $sheet->getStyle('A4')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 18, 'color' => ['rgb' => '1E3A5F']],
                    'alignment' => ['vertical' => XlAlign::VERTICAL_CENTER],
                ]);

                // ── Linha 5: Barra de filtros (faixa laranja + texto) ──
                $sheet->getStyle('A5')->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F5A623');
                $sheet->mergeCells('B5:H5');
                $sheet->setCellValue('B5', $this->filterDesc);
                $sheet->getStyle('B5:H5')->applyFromArray([
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0F4FA']],
                    'font'      => ['size' => 9, 'color' => ['rgb' => '374151']],
                    'alignment' => ['vertical' => XlAlign::VERTICAL_CENTER],
                ]);

                // ── Linha 6: Resumo (faixa azul + totais) ──
                $sheet->getStyle('A6')->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1E3A5F');
                $sheet->getStyle('B6:H6')->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F0F4FA');

                $summaryStr = 'Total: ' . $this->data->count();
                if ($this->showBreakdown && $this->summary->isNotEmpty()) {
                    foreach ($this->summary as $statusValue => $count) {
                        $summaryStr .= '   ·   ' . \App\Enums\RequestStatus::from($statusValue)->label() . ': ' . $count;
                    }
                }
                $sheet->mergeCells('B6:H6');
                $sheet->setCellValue('B6', $summaryStr);
                $sheet->getStyle('B6')->applyFromArray([
                    'font'      => ['size' => 9, 'color' => ['rgb' => '1E3A5F']],
                    'alignment' => ['vertical' => XlAlign::VERTICAL_CENTER],
                ]);

                // ── Linha 7: Espaçador ──

                // ── Linha 8: Espaçador ──

                // ── Alturas ──
                foreach ([1 => 44, 2 => 3, 3 => 4, 4 => 26, 5 => 18, 6 => 18, 7 => 4, 8 => 4, 9 => 22] as $row => $h) {
                    $sheet->getRowDimension($row)->setRowHeight($h);
                }

                // ── AutoFilter no cabeçalho (linha 9) ──
                $sheet->setAutoFilter("A9:H{$lastRow}");

                // ── Linhas de dados: altura + zebra ──
                for ($i = 10; $i <= $lastRow; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(16);
                    if ($i % 2 === 0) {
                        $sheet->getStyle("A{$i}:H{$i}")
                            ->getFill()->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('EEF2F9');
                    }
                }

                // ── Bordas ──
                if ($this->data->isNotEmpty()) {
                    $sheet->getStyle("A9:H{$lastRow}")->getBorders()->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN)
                        ->getColor()->setRGB('CBD5E1');
                }

                // ── Congelar cabeçalho ──
                $sheet->freezePane('A10');
            },
        ];
    }
}
