# HospitalBack - Hospital management system logic

![Php]
![Codeigniter]
![MySQL]

## 📋 Overview

**HospitalBack** is a REST API with **Codeigniter** for managing a local hospital.
It will allow authorised health personnel to perform CRUD operations,medical act(vital record and consultation management),timetable generation and sale for the pharmacy,medical report generation in a **MYSQL database** secured using JWT authentication

The project follows **Codeigniter best practices**, with a layered MVC architecture and API documentation

## ✨ Features

-Patient management

- Personnel management
- User authentication with JWT
- Role Based Access control
- Medical act management
- Prescription management
- Stock and Drug Sale management
- Medical report generation

# 🛠️ Tech Stack

| Technology    | Description           |
| ------------- | --------------------- |
| PHP 8         | Programming language  |
| Codeigniter 4 | Application framework |
| JWT           | Authentication        |
| MYSQL         | Relational database   |
| Composer      | Dependency management |
| WAMP          | Local web server      |

## 🏗️ Architecture

The application follows aN **MVC architecture**:

**Controller**
Handles incoming HTTP request,validates data and returns API responses
**Model**
Interacrs with the MYSQL database
**View**
As the project is done in an API form, the view was developped with vue.js javascript progressive framework

## 📁 Project Structure

app
├── Config
├── Controllers
├── Database
│
├── Migrations
│
└── Seeds
├── Filters
├── Helpers
├── Libraries
├── Models
└── Services

public

writable

.env

composer.json

## ✅ Prerequisites

Before running the project,install:

- **PHP 8.x**
- **Composer**
- **MySQL**
- **WAMP**
- **Git**

Check installed versions:

    php -v
    composer -V
    mysql --version

## 🔧 Installation

Clone the repository:

```bash
git clone https://github.com/your-username/HospitalBack.git
cd HospitalBack
```

Install dependencies:

composer install

---

## 🗄️ Database Setup

```SQL
CREATE DATABASE hospiss;
```

Configure your .env file:

database.default.hostname = localhost
database.default.database = hospiss
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306

Run migrations:

php spark migrate

---

## 🚀 Running the Application

🚀 Running the Application

Start the development server:

php spark serve

The API will be available at:

http://localhost:8080

## 📮 Example API Endpoints

| Method | Endpoint        | Description                    | Status Code |
| ------ | --------------- | ------------------------------ | ----------- |
| GET    | /patient        | Retrieve all patients          | 200 OK      |
| GET    | /product        | Retrieve all pharmacy products | 200 OK      |
| OK     |
| POST   | /perso/connect  | Authenticate a personnel user  | 200 CREATED |
| POST   | /patient/create | Create a new patient           | 201         |

## 🔮 Future Improvements

- Appointment scheduling
- Laboratory management
- Docker deployment

## 👤 Author

**WANDJI Berenice**

Github: [@kenmoe](https://github.com/GyzShaNice)

Email: [kenmarcbertrand@gmail.com](berenicewandji02@gmail.com)

## 🙏 Acknowledgements

- CodeIgniter 4
- Composer
- MySQL
