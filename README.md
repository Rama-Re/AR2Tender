# 🏗️ AR2Tender — Tender Management Backend (Laravel)

**AR2Tender** is the backend system for a digital tendering platform, developed using **Laravel**. It handles all server-side logic required to manage tenders, user roles, bid submissions, evaluations, and result publication.

> 📌 **This repository includes only the backend (Laravel)**. The frontend (Flutter) is maintained separately and is not part of this repo.

---

## 📌 Project Objective

AR2Tender aims to modernize and digitize the entire tendering workflow — from creating and publishing tenders to securely submitting offers, forming evaluation committees, and announcing results.  
The system is role-based, secure, and highly configurable.

---

## 👥 User Roles & Responsibilities

The backend system defines flexible role-based access. Some user types can **act in more than one role** depending on the context:

| Role             | Description |
|------------------|-------------|
| **Admin**         | Manages the system, verifies users, and oversees all operations |
| **Company**       | Can either **publish tenders** or **submit offers** to existing ones |
| **Bidder**        | Technically a "Company" acting as a bidder (via role switch) |
| **Employee**      | Company staff who may also be assigned as **committee members** |
| **Viewer**        | Can browse public tenders without registration |
| **Committee Member** | Selected employees who evaluate offers and issue judgments |

---

## 🔐 Key Backend Features

- JWT-based Authentication & Authorization
- Role switching logic (Company as publisher or bidder)
- Tender publishing (with filters: specialty, location, visibility)
- Secure offer submission with encrypted files
- Committee-based evaluation with final judgment
- Email verification and password recovery (via SMTP)
- REST API endpoints with JSON responses

---

## 🛠️ Tech Stack

- **Laravel 8+** (PHP)
- **MySQL / MariaDB**
- **Eloquent ORM**
- **JWT Authentication**
- **SMTP** (email services)
- **Carbon** (date/time handling)

---

## 📦 Setup Instructions

```bash
# Clone the backend repo
git clone https://github.com/Rama-Re/AR2Tender.git
cd AR2Tender

# Install dependencies
composer install

# Setup environment file
cp .env.example .env
php artisan key:generate

# Set DB credentials in .env
php artisan migrate

# Start development server
php artisan serve
```

---

## 🗃️ Main Entities (Database Models)

| Table / Model Name       | Description                                                                 |
|--------------------------|-----------------------------------------------------------------------------|
| `users`                  | Base user accounts (admin, company, employee, etc.)                         |
| `companies`              | Company profiles (linked 1:1 with users)                                    |
| `company_locations`      | Branches and addresses of each company                                      |
| `phones`                 | Phone numbers related to company locations                                  |
| `locations`              | Cities linked to each branch                                                |
| `countries`              | Country master data                                                         |
| `tenders`                | Tenders created by companies                                                |
| `tender_files`           | Files attached to each tender (e.g., documents, specs)                      |
| `submit_forms`           | Offers submitted by companies (linked to specific tenders)                 |
| `supplier_files`         | Files attached to each submitted offer                                      |
| `selective_countries`    | Countries targeted by selective tenders                                     |
| `selective_companies`    | Specific companies allowed to access a tender                               |
| `selective_specialty`    | Field of specialty targeting in tenders                                     |
| `employees`              | Company staff who can be assigned roles (like committee members)            |
| `committees`             | Evaluation committees created per tender                                    |
| `committee_members`      | Employees added as members of specific committees                           |
| `judgment_of_committees` | Individual scores and judgments by committee members                        |
| `tender_results`         | Final decision for a specific tender submission                             |
| `fcm_tokens`             | Device tokens for push notifications via Firebase                          |


---


## 🧩 Entity Relationship Diagram (ERD)

Here's a high-level view of the database structure for the AR2Tender backend:

![Tendering System ERD - Project 1](https://github.com/user-attachments/assets/6d984452-50da-45a1-abc9-950804c75354)


---


## 📄 License

This backend project is for academic and demonstration purposes only. Not licensed for commercial use.

---

## 📄 Project Report

You can download the full system analysis and design report here:  
📥 [View Report (PDF)](docs/AR2Tender_Report.pdf)
