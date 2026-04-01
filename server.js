import express from "express"
import { makeWASocket, useMultiFileAuthState } from "@whiskeysockets/baileys"
import { Boom } from "@hapi/boom"
import archiver from "archiver"
import fs from "fs"
import path from "path"

const PORT = process.env.PORT || 3000
const app = express()

app.use(express.json())
app.use(express.static("public"))

let pairingCode = null
let phoneNumber = null
let currentBot = null

// Ensure `auth` folder exists
const authDir = path.join(__dirname, "auth")
if (!fs.existsSync(authDir)) {
    fs.mkdirSync(authDir, { recursive: true })
}

async function createBot() {
    const { state, saveCreds } = await useMultiFileAuthState("auth")

    const sock = makeWASocket({
        auth: state,
        printQRInTerminal: false,
    })

    sock.ev.on("creds.update", saveCreds)

    sock.ev.on("connection.update", async (update) => {
        const { connection, lastDisconnect } = update

        if (!sock.authState.creds.registered) {
            pairingCode = null
            phoneNumber = null
        }

        if (connection === "close") {
            console.log("Connection closed:", lastDisconnect?.error?.message)
        } else if (connection === "open") {
            console.log("✅ Pairsite Baileys connected!")

            // SEND auth.zip to the user's WhatsApp
            if (phoneNumber) {
                const jid = phoneNumber + "@s.whatsapp.net"
                await sendAuthZipToWhatsApp(sock, jid)
            }
        }
    })

    return sock
}

currentBot = await createBot()

// --- API: request pairing code ---
app.post("/api/requestPairingCode", async (req, res) => {
    const { phone } = req.body
    if (!phone) {
        return res.status(400).json({ error: "Phone number required" })
    }

    const cleanPhone = phone.replace(/D/g, "")
    if (!cleanPhone) {
        return res.status(400).json({ error: "Invalid phone number" })
    }

    if (!currentBot.authState.creds.registered) {
        pairingCode = await currentBot.requestPairingCode(cleanPhone)
        phoneNumber = cleanPhone
    }

    return res.json({
        code: pairingCode,
        phone: phoneNumber,
    })
})

// --- API: get current pairing code (for UI) ---
app.get("/api/pairingCode", (req, res) => {
    return res.json({
        code: pairingCode,
        phone: phoneNumber,
        registered: !!currentBot?.authState?.creds?.registered
    })
})

// --- Helper: send auth.zip as WhatsApp document ---
async function sendAuthZipToWhatsApp(sock, jid) {
    const archive = archiver("zip", { zlib: { level: 9 } })
    const outputPath = path.join(__dirname, "auth.zip")
    const output = fs.createWriteStream(outputPath)

    return new Promise((resolve, reject) => {
        output.on("close", async () => {
            try {
                await sock.sendMessage(jid, {
                    document: { url: outputPath },
                    fileName: "auth.zip",
                    caption: "🔐 Your QUEEN BELLA MD session files. Put this in `queen-bella-md/auth/` and upload to KataBump."
                })
                console.log("✅ Sent auth.zip to WhatsApp:", jid)
                resolve()
            } catch (err) {
                console.error("Failed to send auth.zip:", err.message)
                reject(err)
            }
        })

        archive.on("error", (err) => {
            console.error("ZIP error:", err)
            reject(err)
        })

        archive.pipe(output)

        const authPath = path.join(__dirname, "auth")
        if (fs.existsSync(authPath)) {
            archive.directory(authPath, "auth")
        }

        archive.finalize()
    })
}

app.listen(PORT, () => {
    console.log(`QUEEN BELLA Pairsite running on http://localhost:${PORT}`)
})