# <p align="center"> 🎓 Smart Academic Management System (SAMS) </p>

<p align="center">
  <img src="https://img.icons8.com/external-flaticons-flat-flat-icons/512/external-academic-university-flaticons-flat-flat-icons.png" width="120" />
</p>

<p align="center">
  <b>A modern full-stack Academic Management System built for real-time student and faculty interaction.</b>
</p>

---


## 🚀 About the Project

Smart Academic Management System (SAMS) is a full-stack web application designed to simplify academic operations for institutions.

It provides a **role-based system** for:

👨‍🏫 Admin (Lecturers)  
👨‍🎓 Students  

The platform enables:

- 📊 Real-time attendance tracking  
- 📚 Marks & grade management  
- 📢 Class-based announcements  
- 🧑‍🎓 Student dashboards with analytics  
- 🔐 Secure authentication with Email OTP  

---


## ✨ Features

### 🌐 Public Module
- Modern landing page
- Login & Registration (OTP verification)
- Auto-generated Student ID

### 👨‍🏫 Admin Panel
- Dashboard (stats + charts)
- Attendance management (real-time)
- Marks & grade system
- Class & subject management
- Announcements (global & class-specific)

### 👨‍🎓 Student Panel
- Personal dashboard
- Attendance analytics (chart view)
- Marks & grades
- Class materials
- Announcements (filtered by class)

---


## 🛠️ Tech Stack

### 🎨 Frontend

  <img src="https://skillicons.dev/icons?i=html,tailwindcss,js&perline=4" />


### ⚙️ Backend

  <img src="https://skillicons.dev/icons?i=php&perline=4" />

### 🗄️ Database

  <img src="https://skillicons.dev/icons?i=mysql&perline=4" />


### ☁️ Deployment

  <img src="https://skillicons.dev/icons?i=aws&perline=4" />   
  <img src="https://img.shields.io/badge/Render-46E3B7?style=for-the-badge&logo=render&logoColor=black"/>

### ☁️ Version Control

  <img src="https://skillicons.dev/icons?i=git,github&perline=4" />

---


## 👨‍💻 Team Workflow (Git)

We follow a simple Git workflow:


feature branch → Pull Request → main

---


## 🌿 Branch Naming

### 🌐 Public Module
- feature/login 
- feature/register

### 👨‍🏫 Admin Panel
- feature/admin-dashboard
- feature/admin-attendance
- feature/admin-marks
- feature/admin-class
- feature/admin-announcements
- feature/admin-logout

### 👨‍🎓 Student Panel
- feature/student-dashboard
- feature/student-attendance
- feature/student-marks
- feature/student-announcements
- feature/student-logout

---


## 🔄 Git Workflow (Step-by-Step)

  ### 1️⃣ Clone the Repository
  ```bash
  git clone https://github.com/your-username/academic-management-system.git
  cd academic-management-system
  ```
  
  ### 2️⃣ Create a Feature Branch
  ```bash
  git checkout -b feature/login-system
  ```
  
  ### 3️⃣ Add & Commit Changes
  ```bash
  git add .
  git commit -m "Added login and OTP verification"
  ```
  
  ### 4️⃣ Push to GitHub
  ```bash
  git push origin feature/login-system
  ```
  
  ### 5️⃣ Create Pull Request (PR)
  - Go to GitHub
  - Click Compare & Pull Request
  - Base: main
  - Add description
  - Submit PR
  
  ### 6️⃣ Merge PR
  - Teammate reviews
  - Click Merge
  - Delete branch
  
  ### 7️⃣ Always Pull Latest Code Before Work
  ```bash
  git checkout main
  git pull origin main
  ```

---


## ⚔️ Handling Merge Conflicts
```bash
git checkout main
git pull origin main
git checkout feature/login-system
git merge main
```

---


## 🤝 Contribution
- Fork repo
- Create branch
- Make changes
- Submit PR
