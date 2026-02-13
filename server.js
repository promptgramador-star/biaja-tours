const express = require('express');
const mysql = require('mysql2');
const cors = require('cors');
const bodyParser = require('body-parser');
const path = require('path');
require('dotenv').config();

const app = express();
const port = process.env.PORT || 3000;

// Middleware
app.use(cors());
app.use(bodyParser.json());
app.use(express.static(path.join(__dirname, '.')));

// Database Connection
// Database Connection
const db = mysql.createConnection({
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    database: process.env.DB_NAME || 'biaja_db',
    port: process.env.DB_PORT || 3306,
    multipleStatements: true
});

db.connect(err => {
    if (err) {
        console.error('❌ Error connecting to MySQL:', err.message);
        console.error('   Hint: Check your .env file. If you are running locally, ensure DB_HOST is correct (use 127.0.0.1 or the remote IP if applicable).');
        console.error('   Current Config -> Host:', process.env.DB_HOST, '| User:', process.env.DB_USER, '| DB:', process.env.DB_NAME);
        console.log('Running without DB connection (Demo Mode for static files will work, API will fail)');
        return;
    }
    console.log('✅ Connected to MySQL Database');

    // Initialize DB (Simple auto-init for demo purposes)
    const fs = require('fs');
    try {
        const schema = fs.readFileSync('schema.sql', 'utf8');
        db.query(schema, (err, results) => {
            if (err) console.error('Schema Init Error:', err);
            else console.log('Database initialized');
        });
    } catch (e) {
        console.log('schema.sql not found or error reading it');
    }
});

const multer = require('multer');

// Configure Multer Storage
const storage = multer.diskStorage({
    destination: (req, file, cb) => {
        cb(null, 'img/uploads/');
    },
    filename: (req, file, cb) => {
        cb(null, Date.now() + path.extname(file.originalname)); // Append extension
    }
});

const upload = multer({ storage: storage });

// Serve Uploads Directory
app.use('/img/uploads', express.static(path.join(__dirname, 'img/uploads')));

// API Routes

// Login (Simple Insecure Demo - Production should use bcrypt & JWT)
app.post('/api/auth/login', (req, res) => {
    const { username, password } = req.body;
    console.log(`Login attempt for: ${username}`);

    if (!db || db.state === 'disconnected') {
        console.error('Database not connected during login attempt');
        return res.status(500).json({ error: 'Database connection failed' });
    }

    const query = 'SELECT * FROM users WHERE username = ? AND password_hash = ?';

    db.query(query, [username, password], (err, results) => {
        if (err) {
            console.error('Login Query Error:', err);
            return res.status(500).json({ error: err.message });
        }
        if (results.length > 0) {
            console.log(`User logged in: ${username}`);
            res.json({ success: true, user: { id: results[0].id, username: results[0].username } });
        } else {
            console.warn(`Invalid login attempt for: ${username}`);
            res.status(401).json({ error: 'Credenciales inválidas' });
        }
    });
});

// Get Offers
app.get('/api/offers', (req, res) => {
    db.query('SELECT * FROM offers ORDER BY created_at DESC', (err, results) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json(results);
    });
});

// Create Offer with Image Upload
app.post('/api/offers', upload.single('image'), (req, res) => {
    const { hotel_name, price, deadline } = req.body;
    let image_url = req.body.image_url; // Fallback to URL if provided (legacy/optional)

    if (req.file) {
        image_url = `img/uploads/${req.file.filename}`;
    }

    if (!image_url) {
        return res.status(400).json({ error: 'Image is required (either file upload or URL)' });
    }

    const query = 'INSERT INTO offers (hotel_name, price, deadline, image_url) VALUES (?, ?, ?, ?)';

    db.query(query, [hotel_name, price, deadline, image_url], (err, results) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json({ success: true, id: results.insertId, image_url: image_url });
    });
});

// Start Server
app.listen(port, () => {
    console.log(`Server running at http://localhost:${port}`);
});
