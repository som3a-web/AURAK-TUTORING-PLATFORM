# Official Website Deployment Guide
## AURAK Peer Tutoring Platform

This guide will help you deploy your website to make it an official, publicly accessible website.

---

## 📋 Pre-Deployment Checklist

### 1. **Choose a Web Hosting Provider**

**Recommended Options:**

#### Option A: Shared Hosting (Easiest & Cheapest)
- **cPanel Hosting** (e.g., Bluehost, HostGator, SiteGround)
- **Cost:** $3-10/month
- **Best for:** Small to medium websites
- **Includes:** PHP, MySQL, cPanel control panel

#### Option B: Cloud Hosting (Professional)
- **AWS (Amazon Web Services)**
- **Google Cloud Platform**
- **Microsoft Azure**
- **Cost:** Pay-as-you-go or $10-50/month
- **Best for:** Scalable, professional deployments

#### Option C: VPS (Virtual Private Server)
- **DigitalOcean, Linode, Vultr**
- **Cost:** $5-20/month
- **Best for:** More control, better performance

#### Option D: University Hosting (Recommended for AURAK)
- **Check if AURAK provides web hosting for official projects**
- **Contact IT department for server access**
- **May be free or low-cost for official university projects**

---

## 🚀 Step-by-Step Deployment Process

### **Step 1: Prepare Your Files**

1. **Create a deployment package:**
   ```bash
   # Files to upload:
   - All HTML files (homepage.html, dashboard.html, LOGIN.HTML, aboutus.html)
   - api/ folder (all PHP files)
   - config/ folder (db.php - will need updating)
   - image.html/ folder (all images)
   ```

2. **Remove development files:**
   - Delete `db-test.php/` folder
   - Delete `message_test.html/` folder
   - Delete `create_admin.php` (or secure it)

---

### **Step 2: Database Setup**

#### A. Export Your Local Database

1. **Using phpMyAdmin:**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Select `aurak_tutoring` database
   - Click "Export" tab
   - Choose "Quick" export method
   - Format: SQL
   - Click "Go" to download `.sql` file

2. **Using Command Line:**
   ```bash
   mysqldump -u root -p aurak_tutoring > aurak_tutoring_backup.sql
   ```

#### B. Import to Production Database

1. **Access hosting control panel (cPanel)**
2. **Open phpMyAdmin**
3. **Create new database:**
   - Database name: `aurak_tutoring` (or as provided by host)
   - Note the database name, username, and password
4. **Import the SQL file:**
   - Select the database
   - Click "Import" tab
   - Choose your `.sql` file
   - Click "Go"

---

### **Step 3: Update Database Configuration**

**Edit `config/db.php` for production:**

```php
<?php
// config/db.php - PRODUCTION VERSION

// Hide errors in production
ini_set('display_errors', 0);
error_reporting(0);

// PRODUCTION DATABASE CREDENTIALS
// Get these from your hosting provider
$host    = 'localhost';  // Usually 'localhost' on shared hosting
$db      = 'your_hosting_db_name';  // Database name from hosting
$user    = 'your_hosting_db_user';  // Database username from hosting
$pass    = 'your_hosting_db_password';  // Database password from hosting
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Log error but don't expose details to users
    error_log('Database connection failed: ' . $e->getMessage());
    http_response_code(500);
    die('Database connection failed. Please contact support.');
}

function getPDO() {
    global $pdo;
    return $pdo;
}
```

---

### **Step 4: Upload Files to Server**

#### **Method A: Using cPanel File Manager**

1. **Login to cPanel**
2. **Open "File Manager"**
3. **Navigate to `public_html` folder** (or `www` or `htdocs`)
4. **Upload all files:**
   - Upload entire project folder
   - Or upload files maintaining folder structure

#### **Method B: Using FTP Client**

1. **Get FTP credentials from hosting provider:**
   - FTP Host: `ftp.yourdomain.com`
   - FTP Username: (provided by host)
   - FTP Password: (provided by host)
   - Port: 21 (or 22 for SFTP)

2. **Use FTP client:**
   - **FileZilla** (free): https://filezilla-project.org/
   - **WinSCP** (Windows)
   - **Cyberduck** (Mac)

3. **Connect and upload:**
   - Connect to server
   - Navigate to `public_html` or `www` folder
   - Upload all project files maintaining structure

#### **Method C: Using Git (Advanced)**

```bash
# If hosting supports Git
git init
git add .
git commit -m "Initial deployment"
git remote add origin your-repo-url
git push origin main
```

---

### **Step 5: Configure Domain**

#### **If you have a domain:**

1. **Point domain to hosting:**
   - Update DNS nameservers (provided by hosting)
   - Or update A record to hosting IP address

2. **Common domain options:**
   - `tutoring.aurak.ac.ae` (subdomain of AURAK)
   - `peertutoring.aurak.ac.ae`
   - Custom domain: `aurak-tutoring.com`

#### **If using hosting subdomain:**
   - Usually: `yourproject.hostingprovider.com`
   - Or: `yourproject.yourdomain.com`

---

### **Step 6: Security Configuration**

#### **A. Update File Permissions**

```bash
# Via SSH or File Manager:
chmod 644 *.html *.php
chmod 755 api/ config/ image.html/
chmod 600 config/db.php  # More secure
```

#### **B. Create `.htaccess` for Security**

Create `/.htaccess` file in root:

```apache
# Security Headers
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
</IfModule>

# Hide sensitive files
<FilesMatch "^(config|\.htaccess|\.git)">
    Order allow,deny
    Deny from all
</FilesMatch>

# Prevent directory listing
Options -Indexes

# Force HTTPS (if SSL certificate installed)
# RewriteEngine On
# RewriteCond %{HTTPS} off
# RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

#### **C. Secure Database Config**

- Never commit `config/db.php` with real credentials to public repos
- Use environment variables if possible
- Keep database credentials private

---

### **Step 7: Test Your Deployment**

1. **Test Homepage:**
   - Visit: `https://yourdomain.com/homepage.html`
   - Check all images load
   - Test navigation links

2. **Test Login:**
   - Visit: `https://yourdomain.com/LOGIN.HTML`
   - Try logging in with test account
   - Verify dashboard loads

3. **Test API Endpoints:**
   - Check browser console for errors
   - Test session creation
   - Test message sending

4. **Test Database:**
   - Verify all database operations work
   - Check session management
   - Test user registration

---

## 🔒 Security Best Practices

### **1. SSL Certificate (HTTPS)**
- **Required for official website**
- Most hosting providers offer free SSL (Let's Encrypt)
- Enable in cPanel or contact hosting support

### **2. Regular Backups**
- **Database backups:** Weekly or daily
- **File backups:** Before major updates
- Use hosting backup tools or manual exports

### **3. Update PHP Version**
- Ensure PHP 7.4+ or PHP 8.x
- Check in cPanel or contact hosting

### **4. Remove Development Files**
- Delete test files
- Remove debug code
- Hide error messages in production

---

## 📝 Post-Deployment Checklist

- [ ] All files uploaded correctly
- [ ] Database imported and connected
- [ ] Database credentials updated in `config/db.php`
- [ ] SSL certificate installed (HTTPS working)
- [ ] Domain configured and pointing correctly
- [ ] All pages accessible and loading
- [ ] Login/Registration working
- [ ] API endpoints responding
- [ ] Images loading correctly
- [ ] Mobile responsive design working
- [ ] Error handling configured
- [ ] Backups scheduled
- [ ] Contact information updated
- [ ] Official AURAK branding verified

---

## 🆘 Troubleshooting

### **Database Connection Errors:**
- Verify database credentials
- Check database name, username, password
- Ensure database exists on server
- Check if `localhost` is correct (some hosts use different hostname)

### **File Not Found Errors:**
- Check file paths (case-sensitive on Linux servers)
- Verify folder structure matches local
- Check file permissions

### **Session Issues:**
- Verify PHP sessions enabled
- Check session save path permissions
- Clear browser cookies and test

### **API Errors:**
- Check browser console for errors
- Verify API file paths
- Check CORS settings if needed

---

## 📞 Support Resources

### **For AURAK Official Deployment:**
1. **Contact AURAK IT Department**
   - Email: IT support email
   - Request official hosting for university project
   - May provide subdomain: `tutoring.aurak.ac.ae`

2. **University Web Services**
   - Check if AURAK has web hosting services
   - May require project approval process

### **For Commercial Hosting:**
- Hosting provider support (usually 24/7)
- cPanel documentation
- PHP/MySQL documentation

---

## 🎯 Recommended Hosting Providers

### **For UAE/AURAK:**
1. **UAE-based hosting** (better local performance)
2. **Cloudflare** (CDN for faster loading)
3. **AURAK IT infrastructure** (if available)

### **Popular Options:**
- **Bluehost** - Easy cPanel, good support
- **SiteGround** - Fast, reliable
- **Hostinger** - Affordable, good performance
- **DigitalOcean** - VPS, more control

---

## ✅ Final Steps

1. **Update all internal links** to use production domain
2. **Test on multiple devices** (desktop, tablet, mobile)
3. **Submit to search engines** (Google Search Console)
4. **Monitor performance** and errors
5. **Set up analytics** (Google Analytics)
6. **Create admin account** on production
7. **Document deployment** for future updates

---

## 📧 Need Help?

If you encounter issues during deployment:
1. Check hosting provider documentation
2. Contact hosting support
3. Review error logs in cPanel
4. Test database connection separately
5. Verify file permissions

---

**Good luck with your official website deployment! 🚀**

