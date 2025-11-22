# AURAK Peer Tutoring Platform

Official Peer Tutoring Platform for American University of Ras Al Khaimah (AURAK).

## 🌐 Live Website

**Deployed on Render:** [https://your-project-name.onrender.com](https://your-project-name.onrender.com)

## 📋 Features

- **Student Dashboard:** Request sessions, view upcoming sessions, chat with tutors
- **Tutor Dashboard:** Manage availability, accept/reject sessions, communicate with students
- **Admin Dashboard:** Manage tutors, assign sessions, monitor system health
- **Real-time Messaging:** Chat interface for students and tutors
- **Session Management:** Request, approve, and track tutoring sessions
- **Profile Management:** Update personal information
- **Subject Management:** Tutors can add/remove subjects they teach

## 🚀 Quick Start (Local Development)

1. **Install XAMPP:**
   - Download from: https://www.apachefriends.org/
   - Install and start Apache + MySQL

2. **Clone/Download this repository:**
   ```bash
   cd /Applications/XAMPP/xamppfiles/htdocs/
   git clone <your-repo-url> SE.PROJECT
   ```

3. **Set up database:**
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Create database: `aurak_tutoring`
   - Import schema (if you have SQL file)

4. **Configure database:**
   - Copy `config/db.php.render` to `config/db.php`
   - Update credentials if needed (default works for XAMPP)

5. **Access website:**
   - Homepage: http://localhost/SE.PROJECT/homepage.html
   - Login: http://localhost/SE.PROJECT/LOGIN.HTML
   - Dashboard: http://localhost/SE.PROJECT/dashboard.html

## 🌍 Deployment to Render

See **[RENDER_DEPLOYMENT_GUIDE.md](RENDER_DEPLOYMENT_GUIDE.md)** for complete step-by-step instructions.

**Quick Summary:**
1. Upload project to GitHub (public repository)
2. Create account on Render.com
3. Create PostgreSQL database on Render
4. Create Web Service on Render
5. Add environment variables
6. Deploy!

## 📁 Project Structure

```
SE.PROJECT/
├── homepage.html          # Landing page
├── LOGIN.HTML             # Login/Sign up page
├── dashboard.html         # Main dashboard (role-based)
├── aboutus.html           # About Us page
├── api/                   # PHP API endpoints
│   ├── student/           # Student APIs
│   ├── tutors/            # Tutor APIs
│   └── admin/             # Admin APIs
├── config/                # Configuration files
│   └── db.php             # Database connection (not in git)
├── image.html/            # Images and assets
└── README.md              # This file
```

## 🛠️ Technology Stack

- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **Backend:** PHP 8.2+
- **Database:** MySQL (local) / PostgreSQL (Render)
- **Hosting:** Render.com (free tier)

## 👥 User Roles

1. **Student:** Request sessions, view upcoming sessions, chat with tutors
2. **Tutor:** Manage availability, accept sessions, communicate with students
3. **Admin:** Manage system, assign tutors, monitor health

## 📝 Notes

- Database config (`config/db.php`) is gitignored for security
- Use `config/db.php.render` as template for production
- Free Render tier spins down after 15 min inactivity
- First request after spin-down takes 30-60 seconds

## 🔗 Links

- **Official AURAK Website:** https://aurak.ac.ae
- **AURAK FAQ:** https://aurak.ac.ae/life-at-aurak/faq
- **Render Documentation:** https://render.com/docs

## 📄 License

© 2025 American University of Ras Al Khaimah (AURAK). All rights reserved.

---

**Built with ❤️ for AURAK students**

