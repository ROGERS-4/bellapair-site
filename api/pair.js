// api/pair.js
const crypto = require('crypto');

// In-memory storage
const codes = new Map();

// Your BWM panel pairing service URL
const BOT_PAIR_SERVICE = 'http://localhost:3001'; // Change to your BWM panel IP

export default async function handler(req, res) {
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

    if (req.method === 'OPTIONS') {
        return res.status(200).end();
    }

    const { action } = req.query;

    // ===== GENERATE CODE =====
    if (action === 'generate' && req.method === 'POST') {
        const { phone } = req.body;
        
        if (!phone) {
            return res.status(400).json({ error: 'Phone number required' });
        }

        const cleanPhone = phone.replace(/\D/g, '');
        const code = Math.floor(10000000 + Math.random() * 90000000).toString();
        
        // Store in memory
        codes.set(code, {
            phone: cleanPhone,
            createdAt: Date.now(),
            status: 'pending',
            paired: false
        });

        // Notify bot service about new pairing request
        try {
            await fetch(`${BOT_PAIR_SERVICE}/new-pair`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ code, phone: cleanPhone })
            });
        } catch (e) {
            console.log('Bot service not reachable, will retry later');
        }

        // Auto cleanup after 15 minutes
        setTimeout(() => {
            codes.delete(code);
        }, 15 * 60 * 1000);

        return res.json({ 
            success: true, 
            code: code,
            phone: cleanPhone
        });
    }

    // ===== VERIFY CODE =====
    else if (action === 'verify' && req.method === 'GET') {
        const { code } = req.query;
        
        if (!code) {
            return res.status(400).json({ error: 'Code required' });
        }

        const session = codes.get(code);
        
        if (!session) {
            return res.json({ valid: false, reason: 'expired' });
        }

        // Check if expired
        if (Date.now() - session.createdAt > 15 * 60 * 1000) {
            codes.delete(code);
            return res.json({ valid: false, reason: 'expired' });
        }

        return res.json({ 
            valid: true, 
            phone: session.phone,
            status: session.status,
            paired: session.paired
        });
    }

    // ===== PAIRING SUCCESS CALLBACK FROM BOT =====
    else if (action === 'pairing-success' && req.method === 'POST') {
        const { code } = req.body;
        
        const session = codes.get(code);
        if (session) {
            session.paired = true;
            session.status = 'paired';
            session.pairedAt = Date.now();
            codes.set(code, session);
            
            return res.json({ success: true });
        }
        
        return res.json({ success: false, error: 'Code not found' });
    }

    // ===== GET CHANNEL LINK =====
    else if (action === 'channel') {
        return res.json({
            name: "𝐒𝐄𝟕𝐄𝐍𝐒 𝐒𝐈𝐓𝐘",
            link: "https://whatsapp.com/channel/0029VbBR3ib3LdQQlEG3vd1x"
        });
    }

    return res.status(404).json({ error: 'Action not found' });
}
