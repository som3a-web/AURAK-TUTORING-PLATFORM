# Quick Deployment Checklist
## AURAK Peer Tutoring Platform - Official Website

### ✅ Pre-Deployment

- [ ] Choose hosting provider (recommended: AURAK IT or cPanel hosting)
- [ ] Get hosting account and credentials
- [ ] Export database from local XAMPP (phpMyAdmin → Export)
- [ ] Backup all project files

### ✅ Database Setup

- [ ] Create database on hosting server (via cPanel)
- [ ] Note database name, username, password
- [ ] Import SQL file to production database
- [ ] Verify tables created correctly

### ✅ File Upload

- [ ] Upload all HTML files (homepage.html, dashboard.html, LOGIN.HTML, aboutus.html)
- [ ] Upload `api/` folder with all PHP files
- [ ] Upload `image.html/` folder with all images
- [ ] Upload `config/` folder
- [ ] Upload `.htaccess` file (for security)

### ✅ Configuration

- [ ] Update `config/db.php` with production database credentials
- [ ] Set `display_errors = 0` in production
- [ ] Test database connection
- [ ] Remove or secure test files

### ✅ Domain & SSL

- [ ] Configure domain/subdomain
- [ ] Install SSL certificate (HTTPS)
- [ ] Test website loads correctly
- [ ] Update all internal links if needed

### ✅ Testing

- [ ] Test homepage loads
- [ ] Test login functionality
- [ ] Test registration
- [ ] Test dashboard access
- [ ] Test API endpoints
- [ ] Test on mobile devices
- [ ] Test all forms work

### ✅ Security

- [ ] Verify `.htaccess` is active
- [ ] Check file permissions (644 for files, 755 for folders)
- [ ] Remove development files
- [ ] Enable HTTPS/SSL
- [ ] Test error handling

### ✅ Final Steps

- [ ] Create admin account on production
- [ ] Set up regular backups
- [ ] Monitor for errors
- [ ] Update contact information if needed
- [ ] Share URL with AURAK community

---

## 🚀 Quick Start Commands

### Export Database:
```bash
# Via phpMyAdmin: Export → Quick → SQL → Go
# Or via command line:
mysqldump -u root -p aurak_tutoring > backup.sql
```

### Upload Files:
- **cPanel File Manager:** Upload to `public_html/`
- **FTP:** Use FileZilla to upload to `public_html/`

### Update Database Config:
Edit `config/db.php` with production credentials from hosting provider.

---

## 📞 Need Help?

1. **AURAK IT Department** - For official university hosting
2. **Hosting Support** - For technical issues
3. **See DEPLOYMENT_GUIDE.md** - For detailed instructions

---

**Your website will be live at:** `https://yourdomain.com` or `https://tutoring.aurak.ac.ae`

