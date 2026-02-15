// pair.php - This runs on your Node.js VPS
// Save this as a separate service on your BWM panel

const { default: makeWASocket, useMultiFileAuthState } = require('@whiskeysockets/baileys');
const express = require('express');
const fs = require('fs-extra');
const path = require('path');
const app = express();

app.use(express.json());

app.post('/pair', async (req, res) => {
    const { phone, code } = req.body;
    
    try {
        const { state, saveCreds } = await useMultiFileAuthState(`./sessions/${code}`);
        
        const sock = makeWASocket({
            auth: state,
            printQRInTerminal: false
        });
        
        // Request pairing code
        const pairingCode = await sock.requestPairingCode(phone);
        
        // Save session when ready
        sock.ev.on('creds.update', saveCreds);
        
        sock.ev.on('connection.update', async (update) => {
            if (update.connection === 'open') {
                // Read session file
                const sessionData = await fs.readJson(`./sessions/${code}/creds.json`);
                
                // Send to user via WhatsApp
                await sock.sendMessage(phone + '@s.whatsapp.net', {
                    document: Buffer.from(JSON.stringify(sessionData, null, 2)),
                    mimetype: 'application/json',
                    fileName: `QUEEN_BELLA_SESSION.json`,
                    caption: '✅ Your bot session is ready!'
                });
                
                // Mark as used in PHP
                await fetch('http://your-domain.com/mark-used.php', {
                    method: 'POST',
                    body: new URLSearchParams({ code })
                });
            }
        });
        
        res.json({ success: true, code: pairingCode });
    } catch (e) {
        res.json({ success: false, error: e.message });
    }
});

app.listen(3001, () => {
    console.log('Pairing service running on port 3001');
});
