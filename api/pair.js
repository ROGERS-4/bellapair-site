// api/pair.js
const crypto = require('crypto');

// In-memory storage (Vercel compatible)
const codes = new Map();

export default async function handler(req, res) {
    // Enable CORS
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

        // Clean phone number
        const cleanPhone = phone.replace(/\D/g, '');
        
        // Generate 8-digit code
        const code = Math.floor(10000000 + Math.random() * 90000000).toString();
        
        // Store temporarily (in production, use Redis or database)
        codes.set(code, {
            phone: cleanPhone,
            createdAt: Date.now(),
            used: false,
            paired: false
        });

        // Clean old codes every hour
        setTimeout(() => {
            codes.delete(code);
        }, 15 * 60 * 1000); // 15 minutes

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

        // Check if expired (15 minutes)
        if (Date.now() - session.createdAt > 15 * 60 * 1000) {
            codes.delete(code);
            return res.json({ valid: false, reason: 'expired' });
        }

        return res.json({ 
            valid: true, 
            phone: session.phone,
            used: session.used,
            paired: session.paired
        });
    }

    // ===== MARK AS PAIRED =====
    else if (action === 'paired' && req.method === 'POST') {
        const { code } = req.body;
        
        const session = codes.get(code);
        if (session) {
            session.paired = true;
            session.pairedAt = Date.now();
            codes.set(code, session);
        }
        
        return res.json({ success: true });
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
