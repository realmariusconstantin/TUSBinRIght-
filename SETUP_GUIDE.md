# 🚀 TUSBinRight - Complete Setup Guide

This guide will walk you through setting up both the **frontend (Vue.js)** and **backend (CodeIgniter 4)** for the TUSBinRight application.

---

## 📋 Prerequisites

Before you begin, make sure you have the following installed:

### Required Software:
- **Node.js** (v20.19.0 or v22.12.0+) - [Download here](https://nodejs.org/)
- **npm** (comes with Node.js)
- **PHP** (v8.1+) - Already installed with XAMPP
- **Composer** - [Download here](https://getcomposer.org/)
- **XAMPP** - Already installed (includes Apache & MySQL)
- **Git** - For version control

### Check Your Installations:
```bash
# Check Node.js version
node --version

# Check npm version
npm --version

# Check PHP version
php --version

# Check Composer version
composer --version
```

---

## 🎨 Frontend Setup (Vue.js)

### Step 1: Navigate to Frontend Directory
```bash
cd C:\Users\marius\Desktop\tusbinrightfrontend
```

### Step 2: Install Dependencies
```bash
npm install
```

This will install all the required packages:
- ✅ **Vue 3** (v3.5.22) - Frontend framework
- ✅ **Vue Router** (v4.5.1) - Routing
- ✅ **Axios** (v1.12.2) - HTTP client for API calls
- ✅ **@zxing/browser** & **@zxing/library** - Barcode/QR code scanning
- ✅ **html5-qrcode** (v2.3.8) - Additional QR code support
- ✅ **Vite** (v7.1.10) - Build tool & dev server
- ✅ **@vitejs/plugin-vue** - Vue plugin for Vite

### Step 3: Run Development Server
```bash
npm run dev
```

**Expected Output:**
```
  VITE v7.1.10  ready in XXX ms

  ➜  Local:   http://localhost:5173/
  ➜  Network: use --host to expose
  ➜  press h + enter to show help
```

The frontend will be available at: **http://localhost:5173/**

### Step 4: Build for Production (Optional)
```bash
npm run build
```

This creates optimized production files in the `dist/` folder.

---

## 🔧 Backend Setup (CodeIgniter 4 with PHP)

### Step 1: Navigate to Backend Directory
```bash
cd C:\xampp\htdocs\tusbinright
```

### Step 2: Install PHP Dependencies
```bash
composer install
```

This will install:
- ✅ **CodeIgniter 4** framework
- ✅ **firebase/php-jwt** - JWT authentication
- ✅ **laminas/laminas-escaper** - Security library
- ✅ Development tools (PHPUnit, Faker, etc.)

### Step 3: Set Up Environment Configuration

1. **Copy the environment file:**
   ```bash
   # If you don't have a .env file, create one from the example
   copy env .env
   ```

2. **Edit the `.env` file** (located at `C:\xampp\htdocs\tusbinright\.env`):
   ```ini
   # Set environment to development
   CI_ENVIRONMENT = development
   
   # Database configuration
   database.default.hostname = localhost
   database.default.database = tusbinright
   database.default.username = root
   database.default.password = 
   database.default.DBDriver = MySQLi
   database.default.DBPrefix =
   database.default.port = 3306
   
   # Base URL
   app.baseURL = 'http://localhost/tusbinright/public/'
   
   # JWT Secret Key (change this to a random string)
   JWT_SECRET_KEY = 'your-secret-key-here-change-this-to-something-secure'
   JWT_TIME_TO_LIVE = 3600
   ```

### Step 4: Set Up Database

1. **Start XAMPP:**
   - Open XAMPP Control Panel
   - Start **Apache** module
   - Start **MySQL** module

2. **Create Database:**
   - Open phpMyAdmin: http://localhost/phpmyadmin/
   - Create a new database named `tusbinright`
   - Import the SQL file if you have one: `tusbinright.sql`
   
   **OR via command line:**
   ```bash
   # Open MySQL
   mysql -u root -p
   
   # Create database
   CREATE DATABASE tusbinright;
   USE tusbinright;
   
   # Import SQL file
   source C:\xampp\htdocs\tusbinright\tusbinright.sql;
   ```

### Step 5: Set Proper Permissions
Make sure the `writable` folder is writable:
```bash
# On Windows, right-click the 'writable' folder
# Properties → Security → Edit → Allow "Full control" for your user
```

### Step 6: Test Backend
Open your browser and visit: **http://localhost/tusbinright/public/**

---

## 🔗 Connecting Frontend to Backend

### Frontend API Configuration

Check the file: `C:\Users\marius\Desktop\tusbinrightfrontend\src\lib\api.js`

Make sure it points to your backend:
```javascript
import axios from 'axios';

const api = axios.create({
    baseURL: 'http://localhost/tusbinright/public/api', // Your backend API URL
    withCredentials: true, // Important for cookies (JWT)
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
});

export default api;
```

---

## 🏃 Running the Complete Application

### Step-by-Step Startup:

1. **Start XAMPP:**
   - Open XAMPP Control Panel
   - Click "Start" for **Apache**
   - Click "Start" for **MySQL**

2. **Start Frontend Dev Server:**
   ```bash
   cd C:\Users\marius\Desktop\tusbinrightfrontend
   npm run dev
   ```

3. **Open Your Browser:**
   - Frontend: http://localhost:5173/
   - Backend API: http://localhost/tusbinright/public/

4. **Test the Application:**
   - Register a new user
   - Login with credentials
   - Test the QR/Barcode scanner
   - Check profile dropdown menu

---

## 📁 Project Structure Overview

### Frontend Structure:
```
tusbinrightfrontend/
├── src/
│   ├── components/          # Reusable components
│   │   ├── Login/          # Login component
│   │   ├── Registration/   # Registration component
│   │   ├── navbar/         # Navigation bar
│   │   └── Footer/         # Footer component
│   ├── view/               # Page components
│   │   ├── Home/           # Home page
│   │   ├── Materials/      # Material pages (Plastic, Glass, etc.)
│   │   └── RecyclingInfo/  # Recycling info page
│   ├── composables/        # Vue composables (useAuth, etc.)
│   ├── lib/                # Utilities (api.js)
│   └── main.js             # App entry point
├── router.js               # Route definitions
├── package.json            # Dependencies
└── vite.config.js          # Vite configuration
```

### Backend Structure:
```
tusbinright/
├── app/
│   ├── Controllers/        # API Controllers
│   ├── Models/             # Database Models
│   ├── Config/             # Configuration files
│   └── Database/           # Migrations & Seeds
├── public/                 # Public web root
│   └── index.php          # Entry point
├── writable/              # Logs, cache, uploads
└── composer.json          # PHP Dependencies
```

---

## 🐛 Troubleshooting

### Frontend Issues:

**Problem: `npm install` fails**
- Solution: Delete `node_modules` and `package-lock.json`, then run `npm install` again
- Try: `npm cache clean --force` then `npm install`

**Problem: Port 5173 already in use**
- Solution: Kill the process or change port in `vite.config.js`:
  ```javascript
  export default defineConfig({
    server: { port: 3000 }
  })
  ```

**Problem: Module not found errors**
- Solution: Check import paths use `@/` alias
- Example: `import { useAuth } from '@/composables/useAuth';`

### Backend Issues:

**Problem: 404 errors on backend**
- Solution: Check `.htaccess` file exists in `public/` folder
- Enable `mod_rewrite` in Apache

**Problem: Database connection failed**
- Solution: Check `.env` database credentials
- Make sure MySQL is running in XAMPP

**Problem: CORS errors**
- Solution: Check backend CORS configuration in `app/Config/Cors.php`
- Make sure `withCredentials: true` is set in frontend API

---

## 🎯 Next Steps

1. ✅ **Test all features:**
   - User registration & login
   - QR/Barcode scanning
   - Material information pages
   - Profile dropdown menu

2. ✅ **Implement missing pages:**
   - Profile page (`/profile`)
   - Admin page (`/admin`)

3. ✅ **Deploy to production:**
   - Build frontend: `npm run build`
   - Upload to web server
   - Configure production database

---

## 📚 Useful Commands Reference

### Frontend:
```bash
npm install           # Install dependencies
npm run dev          # Start dev server
npm run build        # Build for production
npm run preview      # Preview production build
```

### Backend:
```bash
composer install     # Install dependencies
composer update      # Update dependencies
php spark serve      # Start built-in PHP server (alternative)
php spark migrate    # Run database migrations
```

### Git:
```bash
git status           # Check status
git add .            # Stage all changes
git commit -m "msg"  # Commit changes
git push             # Push to remote
git pull             # Pull from remote
```

---

## 🆘 Need Help?

- **Vue 3 Docs:** https://vuejs.org/
- **Vue Router Docs:** https://router.vuejs.org/
- **CodeIgniter 4 Docs:** https://codeigniter.com/user_guide/
- **Vite Docs:** https://vitejs.dev/

---

**Happy Coding! 🎉**
