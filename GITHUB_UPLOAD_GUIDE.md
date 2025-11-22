# What to Upload to GitHub - Quick Guide

## ✅ YES - Upload These Files

**All your main project files:**
- ✅ `homepage.html`
- ✅ `LOGIN.HTML`
- ✅ `dashboard.html`
- ✅ `aboutus.html`
- ✅ All files in `api/` folder (all PHP files)
- ✅ All files in `image.html/` folder (images, logos)
- ✅ `README.md`
- ✅ `RENDER_DEPLOYMENT_GUIDE.md`
- ✅ `DEPLOYMENT_GUIDE.md`
- ✅ `SIMPLE_ACCESS_GUIDE.md`
- ✅ `.gitignore` (this file - very important!)
- ✅ `config/db.php.render` (template file)
- ✅ `config/db.php.production.example` (example file)

**Basically: Upload everything EXCEPT what's listed below!**

---

## ❌ NO - DO NOT Upload These Files

**Files that contain passwords or are not needed:**

1. ❌ `config/db.php` - Contains your local database password
   - This will be created on Render with different credentials
   - The `.gitignore` file automatically excludes this

2. ❌ `config/test_db.php` - Just for testing locally

3. ❌ `.DS_Store` - Mac system file (automatically excluded)

4. ❌ Any backup files (`.bak`, `.backup`)

5. ❌ Log files (`*.log`, `error_log`)

6. ❌ Temporary files (`*.tmp`, `*.temp`)

---

## 🔒 Why Not Upload `config/db.php`?

**Security!** This file contains:
- Database username: `root`
- Database password: (your local password)
- Database host: `localhost`

**On Render, you'll use:**
- Different database credentials (provided by Render)
- Environment variables (secure way to store passwords)

---

## 📋 Step-by-Step Upload Process

### **Option 1: Using GitHub Desktop (Easiest)**

1. **Download GitHub Desktop:** https://desktop.github.com
2. **Install and sign in**
3. **Add your project:**
   - Click "File" → "Add Local Repository"
   - Click "Choose..." and select your `SE.PROJECT` folder
   - Click "Add Repository"
4. **GitHub Desktop automatically respects `.gitignore`**
   - Files in `.gitignore` won't be uploaded
   - You'll see which files will be uploaded
5. **Commit and push:**
   - Write a message: "Initial commit - AURAK Tutoring Platform"
   - Click "Commit to main"
   - Click "Publish repository"
   - Make sure it's **Public** (required for free Render hosting)

### **Option 2: Using GitHub Web Interface**

1. **Go to GitHub.com** and create new repository
2. **Name it:** `aurak-tutoring-platform`
3. **Make it Public**
4. **Click "uploading an existing file"**
5. **Drag and drop these folders/files:**
   - `api/` (entire folder)
   - `image.html/` (entire folder)
   - `homepage.html`
   - `LOGIN.HTML`
   - `dashboard.html`
   - `aboutus.html`
   - `README.md`
   - `RENDER_DEPLOYMENT_GUIDE.md`
   - `.gitignore`
   - `config/db.php.render`
   - All other `.md` files
6. **DO NOT upload:**
   - `config/db.php` (will be blocked anyway)
   - `config/test_db.php`
7. **Click "Commit changes"**

### **Option 3: Using Command Line**

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/SE.PROJECT

# Initialize git (if not already done)
git init

# Add all files (respects .gitignore automatically)
git add .

# Check what will be uploaded (optional)
git status

# Commit
git commit -m "Initial commit - AURAK Tutoring Platform"

# Connect to GitHub (replace YOUR_USERNAME)
git remote add origin https://github.com/YOUR_USERNAME/aurak-tutoring-platform.git

# Push to GitHub
git branch -M main
git push -u origin main
```

---

## ✅ Verify What Will Be Uploaded

**Before uploading, check:**

1. **`.gitignore` file exists** in your project root
2. **`config/db.php` is NOT in the list** of files to upload
3. **All your HTML, PHP, and image files ARE included**

**Using command line:**
```bash
git status
```

This shows:
- ✅ Files that WILL be uploaded (in green)
- ❌ Files that WON'T be uploaded (ignored, in gray)

---

## 🔍 Quick Checklist

Before uploading to GitHub:

- [ ] `.gitignore` file exists in project root
- [ ] `config/db.php` is NOT being uploaded (check git status)
- [ ] All HTML files are included
- [ ] All PHP files in `api/` are included
- [ ] All images in `image.html/` are included
- [ ] `README.md` is included
- [ ] Repository is set to **Public** (for free Render hosting)

---

## 🎯 Summary

**Upload:** Everything except `config/db.php` and test files.

**The `.gitignore` file I created will automatically:**
- ✅ Exclude `config/db.php` (your local database config)
- ✅ Exclude system files (`.DS_Store`, etc.)
- ✅ Exclude temporary files
- ✅ Include all your important project files

**Just upload everything, and `.gitignore` will protect sensitive files!**

---

## 🆘 What If I Already Uploaded `config/db.php`?

**Don't worry!** But you should:

1. **Remove it from GitHub:**
   ```bash
   git rm --cached config/db.php
   git commit -m "Remove db.php from repository"
   git push
   ```

2. **Add it to `.gitignore`** (already done)

3. **Change your local database password** (just to be safe)

---

**Ready to upload? Follow one of the options above! 🚀**

