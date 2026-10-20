import { createApp } from './app.js';
const port = Number(process.env.PORT || 8070);
const host = process.env.HOST || '127.0.0.1';
createApp({ token: process.env.ADMIN_TOKEN, whatsapp: process.env.WHATSAPP_NUMBER }).listen(port, host, () => console.log(`Nuscode: http://${host}:${port}`));
