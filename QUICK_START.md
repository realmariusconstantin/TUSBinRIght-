# ⚡ Quick Start Guide

## TL;DR - Just want to run it NOW?

### 1️⃣ Install Frontend Dependencies
```bash
cd C:\Users\marius\Desktop\tusbinrightfrontend
npm install
```

### 2️⃣ Install Backend Dependencies
```bash
cd C:\xampp\htdocs\tusbinright
composer install
```

### 3️⃣ Start XAMPP
- Open XAMPP Control Panel
- Start **Apache**
- Start **MySQL**

### 4️⃣ Setup Database
- Go to http://localhost/phpmyadmin/
- Create database: `tusbinright`
- Import: `tusbinright.sql`

### 5️⃣ Configure Backend
Edit `C:\xampp\htdocs\tusbinright\.env`:
```ini
CI_ENVIRONMENT = development
database.default.hostname = localhost
database.default.database = tusbinright
database.default.username = root
database.default.password = 
app.baseURL = 'http://localhost/tusbinright/public/'
JWT_SECRET_KEY = 'change-this-to-something-secure'
```

### 6️⃣ Start Frontend
```bash
cd C:\Users\marius\Desktop\tusbinrightfrontend
npm run dev
```

### 7️⃣ Open Browser
Frontend: http://localhost:5173/

---

## That's it! 🎉

For detailed setup instructions, see: **SETUP_GUIDE.md**
