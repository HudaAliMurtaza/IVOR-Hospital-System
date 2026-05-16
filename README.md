# Ivor Paine Memorial Hospital — Management System
## CS 204 Database Systems | Dr. Ejaz Ahmed | NUCES Islamabad
### Group Members: Huda Ali (24i-0701) | Zayna Azam (24i-0852) | Amna Tahir (24i-0770)

---

## Project Structure

```
hospital/
├── index.html              ← Full frontend (single-page application)
├── api.php                 ← Backend REST API (all CRUD + queries)
├── includes/
│   └── db.php              ← Database connection config
├── sql/
│   ├── 01_DDL.sql          ← Table creation script (Milestone 2)
│   ├── 02_Insertions.sql   ← Sample data (30 patients, 13 doctors…)
│   └── 03_Queries.sql      ← All 12 analytical queries (Milestone 3)
└── README.md               ← This file
```

---

## Setup Instructions

### Step 1 — Database
1. Open **SQL Server Management Studio (SSMS)**
2. Run `sql/01_DDL.sql` → creates the `IvorPaineHospital` database and all 13 tables
3. Run `sql/02_Insertions.sql` → inserts all sample data
4. (Optional) Run `sql/03_Queries.sql` to verify all 12 queries work

### Step 2 — Web Server
You need **PHP 8.0+** with the SQL Server PDO driver:
- **Windows:** Use [XAMPP](https://www.apachefriends.org/) or [WAMP](https://www.wampserver.com/)
  - Install `pdo_sqlsrv` driver from [Microsoft PHP Drivers](https://docs.microsoft.com/en-us/sql/connect/php/download-drivers-php-sql-server)
- **Linux/Mac:** Use `php-odbc` with FreeTDS, or Docker

### Step 3 — Configure DB credentials
Edit `includes/db.php`:
```php
define('DB_SERVER', 'localhost');      // your SQL Server host
define('DB_NAME',   'IvorPaineHospital');
define('DB_USER',   'sa');             // your login
define('DB_PASS',   'YourPassword');   // your password
```

### Step 4 — Run
Place the entire `hospital/` folder inside your web server's document root:
- XAMPP: `C:/xampp/htdocs/hospital/`
- WAMP:  `C:/wamp64/www/hospital/`

Then open: **http://localhost/hospital/**

---

## API Reference

All requests go to `api.php?resource=<name>` with optional query params.

### Resources & Endpoints

| Resource | GET | POST | PUT | DELETE |
|---|---|---|---|---|
| `dashboard` | Stats + charts | — | — | — |
| `patients` | List/search | Add patient | Update | Delete (cascades PT) |
| `doctors` | List/filter | Add doctor | Update | Delete |
| `nurses` | List/filter | Add nurse | Update | Delete |
| `consultants` | List with team size | Add | Update specialty | Delete |
| `wards` | List with counts | Add | Update | Delete |
| `beds` | Filter by status/ward | Add | Update | Delete |
| `specialties` | List all | Add | Update | Delete |
| `complaints` | List all | Add | Update | Delete |
| `treatments` | List all | Add | Update | Delete |
| `patient_treatment` | Filter by patient | Add record | Close (DateEnded) | Delete |
| `performance` | Filter by staff | Add | — | Delete |
| `prev_experience` | Filter by staff | Add | — | Delete |
| `care_units` | Filter by ward | Add | — | Delete |

### Named Queries

`api.php?resource=query&name=<query_name>[&params]`

| Query Name | Parameters | Description |
|---|---|---|
| `q1_consultant_teams` | — | Consultants and their doctor teams |
| `q2_ward_nursing` | — | Wards with sisters, care units, staff nurses |
| `q3_patient_treatments` | — | All patients with complaints & treatments |
| `q4_junior_housemen` | — | Junior housemen, patients, staff nurses |
| `q5_unique_specialty` | — | Consultants with unique specialties |
| `q6_treatment_experience` | — | Treatments with doctor experience history |
| `q7_multi_complaint` | — | Patients with >1 complaint |
| `q8_grouped_by_treatment` | — | Patients grouped by treatment/complaint |
| `q9_doctor_performance` | `staff_no` | Performance history for one doctor |
| `q10_patient_detail` | `patient_no` | Full medical detail for one patient |
| `q11_treatments_by_date` | `complaint_code`, `date_from`, `date_to` | Treatments in date range |
| `q12_staff_positions` | — | Staff count by position/type |

---

## Features

### Dashboard
- 8 live stat cards (patients, doctors, nurses, beds, etc.)
- Bed occupancy chart per ward
- Doctor count by position chart
- 5 most recent admissions

### Clinical
- **Patients** — search by name, filter by ward, add/delete with bed auto-update
- **Treatments** — full patient treatment history, filter by patient, add/close records
- **Complaints** — master complaint list with add/delete

### Staff
- **Doctors** — filter by name/position, add with consultant assignment
- **Consultants** — team size, specialty view
- **Nurses** — filter by name/type, care unit assignment
- **Performance** — grade history, filter by staff number

### Hospital
- **Wards** — bed & care unit counts
- **Beds** — filter by status/ward, availability tracking
- **Specialties** — master list

### Analytics
- All 12 SQL queries from Milestone 3
- Interactive parameter inputs for Q9, Q10, Q11
- Results rendered as sortable tables

---

## Database Schema Summary

```
SPECIALTY ──< WARD ──< CARE_UNIT ──< NURSE
                  └──< BED ──< PATIENT ──< PATIENT_TREATMENT
                                    │              ├──> COMPLAINT
CONSULTANT (IS-A) ──< DOCTOR ──────┘              ├──> TREATMENT
                          └──< PREV_EXPERIENCE     └──> DOCTOR
                          └──< PERFORMANCE
```

13 tables | 5 consultants | 13 doctors | 30 patients | 35 beds | 42 treatment records
