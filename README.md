# Administrasi RT - Full Stack Application

Aplikasi administrasi RT modern dengan Laravel 12 (Backend) dan React 19 (Frontend) yang dibangun dengan arsitektur Clean Architecture.

##  Teknologi

### Backend
- **Laravel 12** - PHP Framework
- **PHP 8.3+** - Programming Language
- **MySQL/SQLite** - Database
- **Laravel Sanctum** - Authentication
- **Repository Pattern** - Data Access Layer
- **Service Layer** - Business Logic
- **Form Request Validation** - Input Validation
- **API Resources** - Response Formatting

### Frontend
- **React 19** - UI Library
- **React Router 7** - Routing
- **Axios** - HTTP Client
- **TanStack Query** - Data Fetching
- **React Hook Form** - Form Management
- **TailwindCSS** - Styling
- **Recharts** - Charts & Graphs
- **Lucide React** - Icons

## 📋 Fitur

### Dashboard
- Total Rumah, Rumah Dihuni, Rumah Kosong
- Total Penghuni, Penghuni Tetap, Penghuni Kontrak
- Total Pemasukan & Pengeluaran Bulan Ini
- Saldo Keuangan
- Grafik Pemasukan vs Pengeluaran (12 bulan)
- Grafik Pembayaran Iuran

### Penghuni (Residents)
- CRUD lengkap dengan validasi
- Upload Foto KTP
- Status Tetap/Kontrak
- Riwayat Penghuni per Rumah
- Pencarian & Filter

### Rumah (Houses)
- CRUD lengkap
- Status Dihuni/Tidak Dihuni
- Riwayat Penghuni
- Relasi dengan Penghuni Aktif

### Pembayaran (Payments)
- Pencatatan Pembayaran
- Bayar Bulanan/Multiple Bulan/Tahun Penuh
- Generate Kode Pembayaran Otomatis
- Upload Bukti Bayar

### Tagihan (Bills)
- Generate Tagihan Otomatis
- Filter by Bulan/Tahun/Status
- Summary Tagihan

### Pengeluaran (Expenses)
- CRUD Pengeluaran
- Kategori Pengeluaran
- Upload Bukti Nota

### Laporan (Reports)
- Ringkasan Bulanan
- Grafik 12 Bulan
- Detail Pemasukan & Pengeluaran
- Export PDF & Excel (Coming Soon)

### Activity Logs
- Tracking semua aktivitas sistem
- User, Action, Module, Description
- IP Address & User Agent

## 🗄️ Database Schema

### Tables
- `users` - User management
- `residents` - Data penghuni
- `houses` - Data rumah
- `resident_house_histories` - Riwayat penghuni per rumah
- `payment_types` - Jenis iuran (Satpam, Kebersihan)
- `monthly_bills` - Tagihan bulanan
- `payments` - Pembayaran
- `expense_categories` - Kategori pengeluaran
- `expenses` - Data pengeluaran
- `activity_logs` - Log aktivitas

## 🔧 Instalasi

### Prerequisites
- PHP 8.3+
- Composer
- Node.js 18+
- MySQL/SQLite

### Backend Setup

1. **Clone repository**
```bash
cd backend
```

2. **Install dependencies**
```bash
composer install
```

3. **Environment configuration**
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```env
DB_CONNECTION=sqlite
# atau untuk MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=administrasi_rt
# DB_USERNAME=root
# DB_PASSWORD=
```

4. **Create database (untuk MySQL)**
```bash
mysql -u root -p
CREATE DATABASE administrasi_rt CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

5. **Run migrations & seeders**
```bash
php artisan migrate:fresh --seed
```

6. **Start server**
```bash
php artisan serve --port=8000
```

Backend akan berjalan di `http://localhost:8000`

### Frontend Setup

1. **Install dependencies**
```bash
cd frontend
npm install
```

2. **Start development server**
```bash
npm run dev
```

Frontend akan berjalan di `http://localhost:5173`

## 🔐 Akun Default

Setelah menjalankan seeder, Anda bisa login dengan:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@rtjagoan.test | password |
| Ketua RT | ketua@rtjagoan.test | password |
| Bendahara | bendahara@rtjagoan.test | password |

## 📁 Struktur Project

```
backend/
├── app/
│   ├── Enums/
│   │   └── ResidentStatus.php
│   ├── Exceptions/
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   │   ├── AuthController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── ResidentController.php
│   │   │   ├── HouseController.php
│   │   │   ├── PaymentController.php
│   │   │   ├── ExpenseController.php
│   │   │   ├── BillController.php
│   │   │   └── ReportController.php
│   │   ├── Requests/
│   │   │   ├── StoreResidentRequest.php
│   │   │   ├── StoreHouseRequest.php
│   │   │   ├── StorePaymentRequest.php
│   │   │   └── StoreExpenseRequest.php
│   │   └── Resources/
│   │       ├── ResidentResource.php
│   │       ├── HouseResource.php
│   │       ├── PaymentResource.php
│   │       └── ExpenseResource.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Resident.php
│   │   ├── House.php
│   │   ├── ResidentHouseHistory.php
│   │   ├── PaymentType.php
│   │   ├── MonthlyBill.php
│   │   ├── Payment.php
│   │   ├── ExpenseCategory.php
│   │   ├── Expense.php
│   │   └── ActivityLog.php
│   ├── Providers/
│   │   └── RepositoryServiceProvider.php
│   ├── Repositories/
│   │   ├── Contracts/
│   │   │   ├── BaseRepositoryInterface.php
│   │   │   ├── ResidentRepositoryInterface.php
│   │   │   ├── HouseRepositoryInterface.php
│   │   │   ├── PaymentRepositoryInterface.php
│   │   │   └── ExpenseRepositoryInterface.php
│   │   ├── BaseRepository.php
│   │   ├── ResidentRepository.php
│   │   ├── HouseRepository.php
│   │   ├── PaymentRepository.php
│   │   └── ExpenseRepository.php
│   └── Services/
│       ├── DashboardService.php
│       ├── ResidentService.php
│       ├── HouseService.php
│       ├── PaymentService.php
│       ├── ExpenseService.php
│       ├── BillService.php
│       └── ReportService.php
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 2024_01_01_000001_create_residents_table.php
│   │   ├── 2024_01_01_000002_create_houses_table.php
│   │   ├── 2024_01_01_000003_create_payment_types_table.php
│   │   └── 2024_01_01_000004_create_expenses_table.php
│   └── seeders/
│       └── DatabaseSeeder.php
└── routes/
    └── api.php

frontend/
├── src/
│   ├── components/
│   │   └── AppLayout.jsx
│   ├── pages/
│   │   ├── LoginPage.jsx
│   │   ├── DashboardPage.jsx
│   │   ├── ResidentsPage.jsx
│   │   ├── HousesPage.jsx
│   │   ├── PaymentsPage.jsx
│   │   ├── BillsPage.jsx
│   │   ├── ExpensesPage.jsx
│   │   ├── ReportsPage.jsx
│   │   └── ActivityLogsPage.jsx
│   ├── lib/
│   │   └── api.js
│   ├── App.jsx
│   └── main.jsx
├── package.json
├── vite.config.js
├── tailwind.config.js
└── postcss.config.js
```

## 🔌 API Endpoints

### Authentication
- `POST /api/login` - Login
- `POST /api/logout` - Logout
- `GET /api/me` - Get current user

### Dashboard
- `GET /api/dashboard` - Get dashboard data

### Residents
- `GET /api/residents` - List residents
- `GET /api/residents/{id}` - Get resident detail
- `POST /api/residents` - Create resident
- `PUT /api/residents/{id}` - Update resident
- `DELETE /api/residents/{id}` - Delete resident

### Houses
- `GET /api/houses` - List houses
- `GET /api/houses/{id}` - Get house detail
- `POST /api/houses` - Create house
- `PUT /api/houses/{id}` - Update house
- `DELETE /api/houses/{id}` - Delete house
- `GET /api/houses/{id}/history` - Get house history

### Payments
- `GET /api/payments` - List payments
- `POST /api/payments` - Create payment
- `DELETE /api/payments/{id}` - Delete payment

### Bills
- `GET /api/bills` - List bills
- `POST /api/bills/generate` - Generate monthly bills
- `GET /api/bills/summary` - Get bills summary

### Expenses
- `GET /api/expenses` - List expenses
- `GET /api/expenses/{id}` - Get expense detail
- `POST /api/expenses` - Create expense
- `PUT /api/expenses/{id}` - Update expense
- `DELETE /api/expenses/{id}` - Delete expense

### Reports
- `GET /api/reports/summary` - Get monthly summary
- `GET /api/reports/chart` - Get yearly chart data
- `GET /api/reports/detail` - Get detailed report

### Reference Data
- `GET /api/payment-types` - Get payment types
- `GET /api/expense-categories` - Get expense categories

## 🎨 UI/UX Features

- **Modern Dashboard** - Clean, intuitive interface
- **Dark Mode** - Full dark mode support
- **Responsive Design** - Mobile & Desktop friendly
- **Sidebar Navigation** - Easy navigation
- **Charts & Graphs** - Visual data representation
- **Loading States** - Better UX
- **Error Handling** - User-friendly error messages
- **Search & Filter** - Quick data access

## 🧪 Testing

### Backend Testing
```bash
cd backend
php artisan test
```

### Frontend Testing
```bash
cd frontend
npm test
```

## 📝 Development Notes

### Architecture Pattern
- **Repository Pattern** untuk abstraksi data access
- **Service Layer** untuk business logic
- **Form Request** untuk validasi
- **API Resources** untuk response formatting
- **Dependency Injection** untuk testability

### Key Features Implemented
✅ Clean Architecture
✅ Repository Pattern
✅ Service Layer
✅ Form Request Validation
✅ API Resources
✅ Sanctum Authentication
✅ Activity Logging
✅ Soft Deletes
✅ Pagination, Search, Filter, Sort
✅ File Upload (KTP, Bukti Bayar, Nota)
✅ Automatic Bill Generation
✅ Comprehensive Seeder
✅ Dark Mode
✅ Responsive UI
✅ Charts & Analytics

## 🚀 Deployment

### Backend
```bash
cd backend
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan db:seed --force
```

### Frontend
```bash
cd frontend
npm run build
```

## 📊 Database Seeder

Seeder akan membuat:
- 3 Users (Admin, Ketua RT, Bendahara)
- 2 Payment Types (Satpam: 100rb, Kebersihan: 15rb)
- 6 Expense Categories
- 20 Residents (15 Tetap + 5 Kontrak)
- 20 Houses (15 Dihuni + 5 Kosong)
- 12 Bulan Tagihan & Pembayaran
- Pengeluaran 12 Bulan
- Activity Logs

## 🤝 Contributing

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License.

## 👨‍💻 Author

Built with ❤️ for Administrasi RT

## 📞 Support

For support, email admin@rtjagoan.test or create an issue in the repository.

---

**Note**: This is a skill fit test project demonstrating full-stack development capabilities with Laravel 12 and React 19, implementing Clean Architecture, Repository Pattern, and modern UI/UX practices.