# Technical Background

## Research Repository System

---

## 1. Hardware Specifications

The system is designed to run on a standard development/production environment with the following minimum hardware requirements:

| Component           | Minimum Specification                  | Recommended Specification              |
| ------------------- | -------------------------------------- | -------------------------------------- |
| **Processor (CPU)** | Intel Core i5 / AMD Ryzen 5 (4+ cores) | Intel Core i7 / AMD Ryzen 7 (6+ cores) |
| **RAM**             | 8 GB                                   | 16 GB or higher                        |
| **Storage**         | 20 GB free space (SSD preferred)       | 50 GB+ SSD                             |
| **Network**         | Broadband internet connection          | Stable broadband connection            |
| **Display**         | 1366x768 resolution                    | 1920x1080 or higher                    |

### Additional Hardware Considerations

- **AI Service (Python Microservice):** The sentence-transformers model (`all-MiniLM-L6-v2`) requires additional memory. A minimum of 4 GB RAM should be allocated for the Python AI service alone.
- **Database Server:** MySQL requires sufficient disk I/O for read/write operations, especially as the research repository grows.
- **File Storage:** PDF research documents are stored on the server filesystem, requiring scalable storage as the repository expands.

---

## 2. Software Specifications

### 2.1 Operating System

| Platform    | Version                                             |
| ----------- | --------------------------------------------------- |
| **Windows** | Windows 10 / Windows 11 (Development)               |
| **Linux**   | Ubuntu 22.04+ / Debian 12+ (Production Recommended) |
| **macOS**   | macOS Ventura+ (Development)                        |

### 2.2 Web Server

| Software                | Version                   | Purpose                                  |
| ----------------------- | ------------------------- | ---------------------------------------- |
| **Apache / Nginx**      | Apache 2.4+ / Nginx 1.24+ | Production web server (optional)         |
| **PHP Built-in Server** | PHP 8.3+                  | Development server (`php artisan serve`) |

### 2.3 Database

| Software   | Version | Purpose                                                      |
| ---------- | ------- | ------------------------------------------------------------ |
| **MySQL**  | 8.0+    | Primary database for the application                         |
| **SQLite** | 3.x     | Alternative/fallback database (configured in `.env.example`) |

### 2.4 Runtime Environments

| Software    | Version | Purpose                           |
| ----------- | ------- | --------------------------------- |
| **PHP**     | ^8.3    | Backend application runtime       |
| **Node.js** | 18.x+   | Frontend asset compilation (Vite) |
| **Python**  | 3.9+    | AI microservice runtime           |

### 2.5 Key Software Dependencies

#### PHP (Composer) - Backend

| Package             | Version | Purpose                    |
| ------------------- | ------- | -------------------------- |
| `laravel/framework` | ^13.8   | Core PHP framework         |
| `laravel/tinker`    | ^3.0    | Interactive shell          |
| `laravel/breeze`    | ^2.4    | Authentication scaffolding |
| `phpunit/phpunit`   | ^12.5   | Testing framework          |

#### Node.js (npm) - Frontend

| Package              | Version | Purpose              |
| -------------------- | ------- | -------------------- |
| `vite`               | ^8.0    | Frontend build tool  |
| `tailwindcss`        | ^3.1    | CSS framework        |
| `alpinejs`           | ^3.4.2  | JavaScript framework |
| `chart.js`           | ^4.5.1  | Data visualization   |
| `@tailwindcss/forms` | ^0.5.2  | Form styling         |

#### Python (pip) - AI Microservice

| Package                 | Version | Purpose                       |
| ----------------------- | ------- | ----------------------------- |
| `flask`                 | Latest  | Web framework for AI API      |
| `sentence-transformers` | Latest  | NLP model for text embeddings |
| `scikit-learn`          | Latest  | Cosine similarity computation |
| `numpy`                 | Latest  | Numerical array operations    |

### 2.6 Third-Party Services

| Service                               | Purpose                                                |
| ------------------------------------- | ------------------------------------------------------ |
| **Gmail SMTP** (`smtp.gmail.com:587`) | Email delivery for OTP verification and password reset |
| **Google reCAPTCHA** (optional)       | Bot protection for forms                               |

---

## 3. Programming Environment

### 3.1 Application Architecture

The system follows a **client-server architecture** with two primary backend services:

```
┌─────────────────────────────────────────────────────────┐
│                    Client Browser                        │
│  (HTML + Tailwind CSS + Alpine.js + Chart.js)           │
└────────────────────┬────────────────────────────────────┘
                     │ HTTP Requests
                     ▼
┌─────────────────────────────────────────────────────────┐
│              Laravel Web Application (PHP 8.3+)          │
│  ┌─────────────┐  ┌──────────────┐  ┌───────────────┐  │
│  │ Controllers  │  │   Models     │  │   Services    │  │
│  │ (MVC Pattern)│  │ (Eloquent ORM)│  │ (Business     │  │
│  │              │  │              │  │  Logic)       │  │
│  └─────────────┘  └──────────────┘  └───────┬───────┘  │
│                                              │          │
│  ┌─────────────┐  ┌──────────────┐          │          │
│  │  Views      │  │  Middleware  │          │          │
│  │ (Blade)     │  │ (Auth/Admin) │          │          │
│  └─────────────┘  └──────────────┘          │          │
└─────────────────────────────────────────────┼──────────┘
                                              │ HTTP (Port 5000)
                                              ▼
┌─────────────────────────────────────────────────────────┐
│              Python AI Microservice (Flask)              │
│  ┌─────────────────────────────────────────────────┐    │
│  │  SentenceTransformer (all-MiniLM-L6-v2)         │    │
│  │  + Cosine Similarity (scikit-learn)             │    │
│  └─────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────┘
                                              │
                                              ▼
┌─────────────────────────────────────────────────────────┐
│                    MySQL Database                        │
│  Tables: users, researches, research_categories,        │
│  research_embeddings, otp_codes, search_logs,           │
│  similarity_logs, sessions, cache, jobs, migrations     │
└─────────────────────────────────────────────────────────┘
```

### 3.2 Technology Stack Summary

| Layer                 | Technology                                 | Version               |
| --------------------- | ------------------------------------------ | --------------------- |
| **Frontend**          | Blade Templates + Tailwind CSS + Alpine.js | Laravel 13.x          |
| **Backend Framework** | Laravel (PHP)                              | ^13.8                 |
| **Database ORM**      | Eloquent ORM                               | Laravel 13.x          |
| **Database**          | MySQL                                      | 8.0+                  |
| **AI/NLP Service**    | Flask + sentence-transformers              | Python 3.9+           |
| **NLP Model**         | all-MiniLM-L6-v2                           | Sentence-Transformers |
| **Build Tool**        | Vite                                       | ^8.0                  |
| **Authentication**    | Laravel Breeze (with OTP)                  | ^2.4                  |
| **Email**             | SMTP (Gmail)                               | TLS 587               |

### 3.3 Development Tools

| Tool                   | Purpose                    |
| ---------------------- | -------------------------- |
| **Visual Studio Code** | Primary IDE                |
| **Composer**           | PHP dependency management  |
| **npm**                | Node.js package management |
| **pip**                | Python package management  |
| **Git**                | Version control            |
| **Artisan CLI**        | Laravel command-line tool  |
| **PHPUnit**            | PHP testing framework      |

### 3.4 Database Schema Overview

The system uses the following primary database tables:

| Table                 | Purpose                                                                                |
| --------------------- | -------------------------------------------------------------------------------------- |
| `users`               | User accounts (students, admins) with role-based access                                |
| `researches`          | Research paper metadata (title, abstract, objectives, authors, year, PDF path, status) |
| `research_categories` | Categorization of research papers                                                      |
| `research_embeddings` | Vector embeddings for semantic search                                                  |
| `otp_codes`           | One-time password codes for email verification                                         |
| `search_logs`         | Logging of user search queries                                                         |
| `similarity_logs`     | Logging of similarity check operations                                                 |
| `sessions`            | User session data                                                                      |
| `cache`               | Application cache                                                                      |
| `jobs`                | Queue job management                                                                   |

### 3.5 Key System Features

1. **User Authentication & Role Management**
    - Registration with email OTP verification
    - Login with session management
    - Role-based access: Student, Admin
    - Password reset via OTP email

2. **Research Management**
    - CRUD operations for research papers (Student)
    - Admin approval/rejection workflow with rejection reasons
    - PDF file upload and storage
    - Research categorization

3. **AI-Powered Features**
    - **Title Checker:** Compares research titles for similarity using NLP embeddings
    - **Similarity Checker:** Compares both title and objectives for overall similarity scoring
    - **Semantic Search:** Vector-based search using cosine similarity on embeddings

4. **Repository**
    - Browse and search approved research papers
    - View research details (authenticated users)

5. **Admin Dashboard**
    - User management (CRUD)
    - Category management (CRUD)
    - Research approval workflow
    - Analytics with Chart.js

### 3.6 Network Configuration

| Service             | Host           | Port           | Protocol |
| ------------------- | -------------- | -------------- | -------- |
| Laravel Application | 127.0.0.1      | 8000 (default) | HTTP     |
| Python AI Service   | 0.0.0.0        | 5000           | HTTP     |
| MySQL Database      | 127.0.0.1      | 3306           | TCP      |
| Gmail SMTP          | smtp.gmail.com | 587            | SMTP/TLS |

### 3.7 Environment Configuration

The application uses a `.env` file for environment-specific configuration:

- **Database:** MySQL connection (`research_repository` database)
- **Mail:** Gmail SMTP with app-specific password
- **Session:** Database-driven sessions
- **Queue:** Database-driven queue connection
- **Cache:** Database-driven cache store
- **AI Service URL:** `http://127.0.0.1:5000` (configured in `AIService.php`)

---

_Document generated from project analysis - June 2026_
