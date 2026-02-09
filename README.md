# 🎓 Mock Test Platform

Government Exam Style Mock Test Platform built with CodeIgniter 4.6.4 and Shield 1.2.0.

## 🚀 Quick Start

### 1. Start XAMPP
- Start Apache and MySQL services

### 2. Create Default Admin Users
```bash
php create_default_users.php
```

### 3. Login
Visit: **http://localhost:8080/login**

**Default Credentials:**
```
Super Admin:    superadmin / admin123
Exam Expert:    exam_expert / expert123
Scheduler:      scheduler / admin123
Student:        student / student123
```

## 📋 Quick Links

| Role | Dashboard | Admin Panel |
|------|-----------|-------------|
| **Student** | [Dashboard](http://localhost:8080/dashboard) | - |
| **Exam Expert** | [Dashboard](http://localhost:8080/dashboard) | [Subjects](http://localhost:8080/admin/subjects) • [Questions](http://localhost:8080/admin/questions) • [Exams](http://localhost:8080/admin/exams) |
| **Scheduler** | [Dashboard](http://localhost:8080/dashboard) | [Schedule Exams](http://localhost:8080/admin/exams) |
| **Super Admin** | [Dashboard](http://localhost:8080/dashboard) | [Users](http://localhost:8080/admin/users) • [All Admin Features](http://localhost:8080/admin/subjects) |

## 📚 Documentation

**Complete documentation:** [DOCUMENTATION.md](DOCUMENTATION.md)

Includes:
- ✅ Full feature list
- ✅ System architecture
- ✅ Database schema
- ✅ Role & permissions guide
- ✅ API endpoints
- ✅ Troubleshooting guide
- ✅ Changelog

## 🛠️ Useful Commands

**Check Database Data:**
```bash
php check_data.php
```

**Create Admin User:**
```bash
php create_admin_user.php
```

**Run Migrations:**
```bash
php spark migrate
```

**Seed Test Data:**
```bash
php spark db:seed AdminTestSeeder
```

**Start Development Server:**
```bash
php spark serve
```

## ✨ Key Features

- ✅ **Admin System** - Subject, Question & Exam Management
- ✅ **Live Preview** - See questions as they'll appear in exam
- ✅ **Image Support** - Upload images for questions and options
- ✅ **Scheduling** - Set exam start/end times with countdown
- ✅ **Countdown Timer** - Students see time remaining until exam
- ✅ **Role-Based Access** - 4 user roles with granular permissions
- ✅ **Performance** - Handles 50-100 concurrent users
- ✅ **Optimized** - 85% reduction in database queries

## 📊 Current Test Data

**Subjects:** 5 (MATH, LOGIC, ENG, GK, GS)
**Questions:** 50 (10 per subject)
**Exams:** 1 (SSC CGL Tier-1 Mock Test)
**Users:** 4 default users created

## 🎯 Typical Workflow

### For Exam Experts:
1. Login as `exam_expert`
2. Create subjects → [/admin/subjects](http://localhost:8080/admin/subjects)
3. Create questions with preview → [/admin/questions](http://localhost:8080/admin/questions)
4. Build exams → [/admin/exams/create](http://localhost:8080/admin/exams/create)

### For Schedulers:
1. Login as `scheduler`
2. Go to exams → [/admin/exams](http://localhost:8080/admin/exams)
3. Click "Schedule" on exam
4. Set date/time and activate

### For Students:
1. Login as `student`
2. See available exams → [/dashboard](http://localhost:8080/dashboard)
3. Watch countdown timer
4. Take exam when available

## 📁 Project Structure

```
exam/
├── app/
│   ├── Controllers/Admin/    # Admin panel controllers
│   ├── Models/               # Database models
│   ├── Views/                # UI views
│   └── Database/             # Migrations & seeders
├── writable/uploads/         # Image uploads
├── DOCUMENTATION.md          # Complete documentation
├── create_admin_user.php     # CLI user creation
├── create_default_users.php  # Batch user creation
└── check_data.php            # Database checker
```

## 🔧 Configuration Files

- **Database:** `app/Config/Database.php`
- **Sessions:** `app/Config/Session.php`
- **Roles:** `app/Config/AuthGroups.php`
- **Routes:** `app/Config/Routes.php`

## 📞 Need Help?

1. Check [DOCUMENTATION.md](DOCUMENTATION.md) for detailed guides
2. Run `php check_data.php` to verify database state
3. Check troubleshooting section in documentation

---

**Version:** 1.3.8
**Last Updated:** 2026-01-07
**Framework:** CodeIgniter 4.6.4 + Shield 1.2.0
