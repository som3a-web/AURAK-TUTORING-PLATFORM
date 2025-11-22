# Deploy to Render.com - Step by Step Guide
## Make Your AURAK Tutoring Platform Accessible Online (Like auraks-dine.onrender.com)

This guide will help you deploy your website to **Render.com** (free hosting) so anyone can access it from anywhere!

---

## 🎯 What is Render?

**Render** is a cloud platform (like Heroku) that lets you host websites for free. Your friend's site `auraks-dine.onrender.com` is hosted there.

**Free Tier Includes:**
- ✅ Free web hosting
- ✅ Free database (PostgreSQL or MySQL)
- ✅ Free SSL certificate (HTTPS)
- ✅ Custom subdomain: `yourproject.onrender.com`
- ⚠️ **Note:** Free tier spins down after 15 minutes of inactivity (wakes up on first request)

---

## 📋 Prerequisites

1. **GitHub Account** (free) - https://github.com
2. **Render Account** (free) - https://render.com
3. **Your project files** (already have this!)

---

## 🚀 Step-by-Step Deployment

### **Step 1: Prepare Your Project for Git**

1. **Create a `.gitignore` file** in your project root:
   ```
   # Database config (don't upload passwords!)
   config/db.php
   
   # OS files
   .DS_Store
   Thumbs.db
   
   # IDE files
   .vscode/
   .idea/
   *.swp
   ```

2. **Create a `render.yaml` file** (optional, for easy setup):
   ```yaml
   services:
     - type: web
       name: aurak-tutoring
       env: php
       buildCommand: ""
       startCommand: php -S 0.0.0.0:$PORT -t .
       envVars:
         - key: PHP_VERSION
           value: 8.2
     - type: pgsql
       name: aurak-tutoring-db
       plan: free
   ```

### **Step 2: Upload to GitHub**

1. **Go to GitHub.com** and sign in (or create account)

2. **Create a new repository:**
   - Click "New" or "+" → "New repository"
   - Name: `aurak-tutoring-platform`
   - Make it **Public** (free hosting requires public repos)
   - Click "Create repository"

3. **Upload your files:**
   
   **Option A: Using GitHub Desktop (Easiest)**
   - Download GitHub Desktop: https://desktop.github.com
   - Install and sign in
   - Click "Add" → "Add Existing Repository"
   - Select your `SE.PROJECT` folder
   - Click "Publish repository"
   - Make sure it's **Public**

   **Option B: Using Command Line**
   ```bash
   cd /Applications/XAMPP/xamppfiles/htdocs/SE.PROJECT
   
   # Initialize git
   git init
   git add .
   git commit -m "Initial commit - AURAK Tutoring Platform"
   
   # Connect to GitHub (replace YOUR_USERNAME)
   git remote add origin https://github.com/YOUR_USERNAME/aurak-tutoring-platform.git
   git branch -M main
   git push -u origin main
   ```

   **Option C: Using GitHub Web Interface**
   - In your new repository, click "uploading an existing file"
   - Drag and drop all your project files
   - Click "Commit changes"

### **Step 3: Set Up Database on Render**

1. **Go to Render.com** and sign up (free): https://render.com

2. **Create a PostgreSQL Database:**
   - Click "New +" → "PostgreSQL"
   - Name: `aurak-tutoring-db`
   - Database: `aurak_tutoring`
   - User: `aurak_user` (or auto-generated)
   - Region: Choose closest to you
   - Plan: **Free**
   - Click "Create Database"

3. **Save Database Credentials:**
   - Copy the **Internal Database URL** (you'll need this)
   - It looks like: `postgresql://user:password@host:5432/dbname`

### **Step 4: Deploy Web Service on Render**

1. **Create New Web Service:**
   - Click "New +" → "Web Service"
   - Connect your GitHub account
   - Select your repository: `aurak-tutoring-platform`
   - Click "Connect"

2. **Configure Web Service:**
   - **Name:** `aurak-tutoring-platform`
   - **Environment:** `PHP`
   - **Region:** Choose closest to you
   - **Branch:** `main`
   - **Root Directory:** Leave empty (or `/` if needed)
   - **Build Command:** Leave empty
   - **Start Command:** `php -S 0.0.0.0:$PORT -t .`
   - **Plan:** **Free**

3. **Add Environment Variables:**
   Click "Advanced" → "Add Environment Variable":
   
   ```
   DB_HOST=your-db-host.onrender.com
   DB_NAME=aurak_tutoring
   DB_USER=your-db-user
   DB_PASS=your-db-password
   DB_PORT=5432
   ```

   **OR** use the Internal Database URL:
   ```
   DATABASE_URL=postgresql://user:pass@host:5432/dbname
   ```

4. **Click "Create Web Service"**

5. **Wait for Deployment:**
   - Render will build and deploy your site
   - Takes 5-10 minutes on first deploy
   - You'll see logs in real-time

### **Step 5: Update Database Configuration**

Since Render uses **PostgreSQL** (not MySQL), you need to update your PHP code:

1. **Update `config/db.php`:**
   ```php
   <?php
   // Render PostgreSQL connection
   $db_url = getenv('DATABASE_URL');
   
   if ($db_url) {
       // Parse PostgreSQL URL
       $url = parse_url($db_url);
       $host = $url['host'];
       $port = $url['port'] ?? 5432;
       $dbname = ltrim($url['path'], '/');
       $user = $url['user'];
       $pass = $url['pass'];
       
       try {
           $pdo = new PDO(
               "pgsql:host=$host;port=$port;dbname=$dbname",
               $user,
               $pass,
               [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
           );
       } catch (PDOException $e) {
           die("Database connection failed: " . $e->getMessage());
       }
   } else {
       // Local XAMPP MySQL connection (for development)
       $host = 'localhost';
       $db = 'aurak_tutoring';
       $user = 'root';
       $pass = '';
       
       try {
           $pdo = new PDO(
               "mysql:host=$host;dbname=$db;charset=utf8mb4",
               $user,
               $pass,
               [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
           );
       } catch (PDOException $e) {
           die("Database connection failed: " . $e->getMessage());
       }
   }
   ?>
   ```

2. **Update SQL Queries:**
   - PostgreSQL uses different syntax than MySQL
   - Change `LIMIT` to `LIMIT` (same)
   - Change backticks `` ` `` to double quotes `"` for identifiers
   - Change `AUTO_INCREMENT` to `SERIAL` or `BIGSERIAL`

### **Step 6: Import Database Schema**

1. **Export your local database:**
   ```bash
   # In XAMPP, use phpMyAdmin or:
   mysqldump -u root -p aurak_tutoring > database.sql
   ```

2. **Convert MySQL to PostgreSQL:**
   - Use online converter: https://www.sqlines.com/online
   - Or manually update SQL syntax

3. **Import to Render PostgreSQL:**
   - Use Render's database dashboard
   - Or connect via psql and import

### **Step 7: Get Your Live URL**

Once deployed, Render gives you:
- **URL:** `https://aurak-tutoring-platform.onrender.com`
- **Or custom:** `https://your-custom-name.onrender.com`

**Share this URL with anyone!** 🌐

---

## 🔧 Alternative: Keep MySQL (Use ClearDB/JawsDB)

If you want to keep MySQL instead of PostgreSQL:

1. **Use JawsDB (Free MySQL):**
   - Sign up: https://www.jawsdb.com
   - Create free MySQL database
   - Get connection string
   - Add to Render environment variables

2. **Update `config/db.php`:**
   ```php
   <?php
   $jawsdb_url = getenv('JAWSDB_URL');
   
   if ($jawsdb_url) {
       $url = parse_url($jawsdb_url);
       $host = $url['host'];
       $port = $url['port'] ?? 3306;
       $dbname = ltrim($url['path'], '/');
       $user = $url['user'];
       $pass = $url['pass'];
       
       $pdo = new PDO(
           "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
           $user,
           $pass,
           [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
       );
   } else {
       // Local XAMPP
       $pdo = new PDO(
           "mysql:host=localhost;dbname=aurak_tutoring;charset=utf8mb4",
           'root',
           '',
           [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
       );
   }
   ?>
   ```

---

## ✅ Quick Checklist

- [ ] Created GitHub account
- [ ] Uploaded project to GitHub (public repository)
- [ ] Created Render account
- [ ] Created PostgreSQL database on Render
- [ ] Created Web Service on Render
- [ ] Added environment variables
- [ ] Updated `config/db.php` for PostgreSQL
- [ ] Imported database schema
- [ ] Tested live website
- [ ] Shared URL with users!

---

## 🎉 After Deployment

Your website will be live at:
```
https://aurak-tutoring-platform.onrender.com
```

**Features:**
- ✅ Accessible from anywhere
- ✅ HTTPS (secure)
- ✅ Free hosting
- ✅ Automatic deployments when you push to GitHub

---

## 🔄 Updating Your Website

1. **Make changes locally**
2. **Commit and push to GitHub:**
   ```bash
   git add .
   git commit -m "Updated homepage"
   git push
   ```
3. **Render automatically redeploys!** (takes 2-5 minutes)

---

## ⚠️ Important Notes

1. **Free Tier Limitations:**
   - Spins down after 15 min inactivity
   - First request after spin-down takes 30-60 seconds
   - 750 hours/month free (enough for most projects)

2. **Database:**
   - PostgreSQL is free on Render
   - MySQL requires external service (JawsDB free tier available)

3. **File Uploads:**
   - Render's filesystem is read-only
   - Use cloud storage (AWS S3, Cloudinary) for uploads

4. **Environment Variables:**
   - Never commit passwords to GitHub
   - Use Render's environment variables

---

## 🆘 Troubleshooting

**Website shows "Application Error":**
- Check Render logs (in dashboard)
- Verify database connection
- Check environment variables

**Database connection fails:**
- Verify DATABASE_URL is correct
- Check database is running
- Ensure firewall allows connections

**Changes not showing:**
- Wait 2-5 minutes for redeploy
- Clear browser cache
- Check GitHub push was successful

---

## 📚 Resources

- **Render Docs:** https://render.com/docs
- **PHP on Render:** https://render.com/docs/deploy-php
- **PostgreSQL Guide:** https://render.com/docs/databases
- **GitHub Guide:** https://guides.github.com

---

**That's it! Your website will be live just like your friend's! 🚀**

Need help with a specific step? Let me know!

