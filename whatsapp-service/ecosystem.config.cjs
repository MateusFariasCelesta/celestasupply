module.exports = {
  apps: [
    {
      name        : 'celesta-whatsapp',
      script      : 'src/index.js',
      node_args   : '--experimental-sqlite',
      watch       : false,
      autorestart : true,
      restart_delay: 6000,
      max_restarts: 20,
      env: {
        NODE_ENV : 'production',
        PORT     : 3001,
      },
    },
  ],
}
