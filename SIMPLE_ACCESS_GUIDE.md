# Simple File Access Guide
## Making Your Project Accessible to Users

This guide shows you the simplest ways to make your AURAK Tutoring Platform accessible to users.

---

## 🎯 Option 1: Share on Local Network (Easiest)

### **If users are on the same network (same WiFi/LAN):**

1. **Find your computer's IP address:**
   - **Mac:** Open Terminal, type: `ifconfig | grep "inet "`
   - **Windows:** Open Command Prompt, type: `ipconfig`
   - Look for IPv4 address (e.g., `192.168.1.100`)

2. **Make sure XAMPP is running:**
   - Start Apache and MySQL in XAMPP Control Panel

3. **Share the URL with users:**
   ```
   http://YOUR_IP_ADDRESS/SE.PROJECT/homepage.html
   ```
   Example: `http://192.168.1.100/SE.PROJECT/homepage.html`

4. **Users can access:**
   - Homepage: `http://YOUR_IP/SE.PROJECT/homepage.html`
   - Login: `http://YOUR_IP/SE.PROJECT/LOGIN.HTML`
   - Dashboard: `http://YOUR_IP/SE.PROJECT/dashboard.html`

---

## 🎯 Option 2: Share Files via USB/Cloud

### **Package and share the project:**

1. **Create a ZIP file of your project:**
   - Right-click on `SE.PROJECT` folder
   - Select "Compress" or "Create Archive"
   - Share the ZIP file

2. **Users need to:**
   - Install XAMPP on their computer
   - Extract files to `C:\xampp\htdocs\SE.PROJECT\` (Windows) or `/Applications/XAMPP/xamppfiles/htdocs/SE.PROJECT/` (Mac)
   - Start Apache and MySQL
   - Access: `http://localhost/SE.PROJECT/homepage.html`

---

## 🎯 Option 3: Use a Simple File Server

### **Python Simple Server (No XAMPP needed for static files):**

1. **Open Terminal/Command Prompt in project folder:**
   ```bash
   cd /Applications/XAMPP/xamppfiles/htdocs/SE.PROJECT
   ```

2. **Start Python server:**
   ```bash
   # Python 3
   python3 -m http.server 8000
   
   # Or Python 2
   python -m SimpleHTTPServer 8000
   ```

3. **Share URL:**
   ```
   http://YOUR_IP_ADDRESS:8000/homepage.html
   ```
   **Note:** This only works for HTML/CSS/JS files. PHP won't work without a PHP server.

---

## 🎯 Option 4: Use ngrok (Access from Internet)

### **Make local server accessible from anywhere:**

1. **Install ngrok:**
   - Download from: https://ngrok.com/
   - Free account required

2. **Start XAMPP** (Apache and MySQL)

3. **Run ngrok:**
   ```bash
   ngrok http 80
   ```
   Or if XAMPP uses port 8080:
   ```bash
   ngrok http 8080
   ```

4. **Get public URL:**
   - ngrok will give you a URL like: `https://abc123.ngrok.io`
   - Share: `https://abc123.ngrok.io/SE.PROJECT/homepage.html`

5. **Users can access from anywhere!**

---

## 🎯 Option 5: Upload to Free Hosting (Simplest for Internet Access)

### **Free hosting options:**

1. **000webhost** (Free):
   - Sign up: https://www.000webhost.com/
   - Upload files via File Manager
   - Get free subdomain: `yourproject.000webhostapp.com`

2. **InfinityFree** (Free):
   - Sign up: https://www.infinityfree.net/
   - Upload files
   - Get free subdomain

3. **GitHub Pages** (Free, but PHP won't work):
   - Only for static HTML/CSS/JS
   - Won't work for your PHP backend

---

## ✅ Quick Checklist

**For Local Network Access:**
- [ ] XAMPP running (Apache + MySQL)
- [ ] Find your IP address
- [ ] Share URL: `http://YOUR_IP/SE.PROJECT/homepage.html`
- [ ] Users on same network can access

**For File Sharing:**
- [ ] Create ZIP of project folder
- [ ] Share ZIP file
- [ ] Users install XAMPP and extract files
- [ ] Users access via `http://localhost/SE.PROJECT/homepage.html`

**For Internet Access:**
- [ ] Use ngrok (temporary) or
- [ ] Upload to free hosting (permanent)

---

## 🔧 Troubleshooting

### **Users can't access:**
- Check firewall settings (allow port 80)
- Ensure XAMPP Apache is running
- Verify IP address is correct
- Check users are on same network

### **Database errors:**
- Ensure MySQL is running in XAMPP
- Check database exists: `aurak_tutoring`
- Verify `config/db.php` has correct credentials

---

## 📝 Recommended: Local Network Access

**Easiest method for testing/demo:**

1. Start XAMPP
2. Find your IP: `192.168.1.XXX`
3. Share: `http://192.168.1.XXX/SE.PROJECT/homepage.html`
4. Done! Users can access from their browsers

---

**That's it! Choose the method that works best for your situation.** 🚀

