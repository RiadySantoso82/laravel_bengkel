# Arsitektur & Teknologi

## Ringkasan

Aplikasi Manajemen Bengkel berbasis web dengan backend Laravel 8 dan frontend Blade + JavaScript (Chart.js, FontAwesome). Database MySQL dengan FIFO untuk perhitungan HPP/stok. Multi-role: admin, kasir, mekanik.

---

## 1. Stack Teknologi

### Backend
- **Laravel 8** (PHP 7.4)
- **Eloquent ORM** — Active Record style
- **Blade templating** — server-side templates
- **Intervention Image (opsional)** — kompres foto (diganti fallback native PHP)
- **Laravel Built-in Queue** — future async handling

### Frontend
- **FontAwesome 6.5.1 (CDN)** — ikon UI
- **Chart.js 4.4.7 (CDN)** — grafik dashboard
- **CSS Flexbox/Grid** — layout responsive (desktop + mobile)

### Database
- **MySQL 8**
- **SQLite** — untuk test (opsional, saat ini test MySQL terpisah)

### Testing
- **PHPUnit** + Laravel `RefreshDatabase` Trait
- Database test: `bengkel_test` (MySQL)

---

## 2. Autentikasi & Role

| Role | Kode | Akses Login |
|------|------|-------------|
| Admin | `admin` | Full akses + User mgmt + Reset data |
| Kasir | `kasir` | Transaksi + Master data + Laporan |
| Mekanik | `mekanik` | Home menu 'mechanic' — dashboard, services, history, profile |

Login menggunakan `LoginController` (tidak menggunakan Laravel Jetstream/Breeze/UI). Role disimpan di kolom `role` tabel `users`.

Setelah login, redirect:
- `mekanik` → `/mechanic/dashboard`
- `admin/kasir` → `/dashboard`

---

## 3. Struktur Direktori Utama

```
app/
├── Console/         # Artisan commands
├── Exceptions/      # Handler.php — custom error handling
├── Http/
│   ├── Controllers/
│   │   ├── Auth/            LoginController
│   │   ├── DashboardController
│   │   ├── MechanicDashboardController
│   │   ├── ServiceOrderController, InvoiceController
│   │   ├── SparepartController
│   │   ├── SalesOrderController (Penjualan retail)
│   │   ├── PartRequestController, PartReturnController
│   │   ├── StockAdjustmentController
│   │   ├── ReportController (Pendapatan, Stock, Sales, Services)
│   │   ├── SettingController (Reset Data)
│   │   ├── CashCategoryController, CashTransactionController
│   │   └── UserController, MechanicController, CustomerController, VehicleController, SupplierController...
│   └── Middleware/
├── Models/
├── Services/
│   └── FifoService.php       # ALokasi batch FIFO
└── Providers/

database/
├── migrations/
└── seeders/
    └── DatabaseSeeder.php

resources/
├── views/
│   ├── layouts/
│   ├── auth/
│   ├── dashboard/
│   ├── master/         # sparepart_category, unit, service_category, payment_method
│   ├── customer, vehicle, supplier, mechanic, sparepart
│   ├── service_order, invoice, sales_order
│   ├── part_request, part_return
│   ├── stock_adjustment
│   ├── cash_category, cash_transaction
│   ├── user, setting
│   ├── report/
│   ├── queue/          # Display antrian publik
│   └── mechanic/       # Dashboard, services, detail, history, profile

docs/
├── erd-aplikasi-bengkel.mermaid
├── architecture.md
├── *.html (dokumen desain UI)
```

---

## 4. Routing (`routes/web.php`)

Pattern route grouping:

```
ROUTE: GET /
  → redirect login

ROUTE: guest
  → /login (GET + POST)

ROUTE: auth
  → /dashboard (GET) — admin/kasir
  → /customers, /vehicles, /suppliers, /mechanics, /spareparts ...
  → /service-orders, /invoices ...
  → /sales-orders ... 
  → /part-requests, /part-returns
  → /stock-adjustments
  → /cash-transactions
  → /users
  → /settings, /settings/reset
  → /reports/* ...
  
ROUTE: mechanic (prefix: mechanic/)
  → /dashboard
  → /services
  → /services/{id} (detail)
  → /services/{id}/status (update status)
  → /services/{id}/progress (save checklist)
  → /services/{id}/request-part
  → /history
  → /profile, /profile (POST)
  → /part-detail/{id}/return

ROUTE: public
  → /queue (display antrian tanpa login)
```

---

## 5. FIFO (First In First Out)

Stok sparepart menggunakan FIFO untuk kalkulasi HPP dan alokasi stok keluar.

### Service `app/Services/FifoService.php`

| Method | Fungsi |
|--------|--------|
| `createBatch(partId, qty, buyPrice, receivedDate)` | Buat batch baru saat stok masuk |
| `allocateOut(movement, partId, qty)` | Alokasi stok keluar dari batch tertua |

### Batch selection
1. Cari semua batch dari `stock_batches` dengan `part_id` = X
2. Urut ascending by `received_date`
3. Ambil qty_remaining sampai kebutuhan terpenuhi
4. Sisa kebutuhan diambil dari batch berikutnya

---

## 6. Payment System

Pembayaran terpisah dari header transaksi dan disimpan di `payment_transactions`.

- `reference_type`: `sales_order` atau `invoice`
- `reference_id`: FK polymorfik ke tabel yang dimaksud
- Field spesifik per metode (tunai: amount_received, change_amount; kartu: card_type, card_last4, dll)

---

## 7. Error Handling

`app/Exceptions/Handler.php`:
- Validation errors: redirect back with `<br>` separated messages
- General exceptions: redirect back with `session('error')`
- JSON requests: return JSON error response

---

## 8. Security

- CSRF protection on all forms
- Role check middleware in each Controller `__construct`:
  - `auth()->user()->role !== 'admin'` abort 403
  - `!in_array(role, ['admin', 'kasir'])` abort 403
- Validation on all `store` and `update` methods
- XSS protected by Blade `{{ }}` auto-escape

---

## 9. Cara Menambahkan Modul Baru

1. Migration → `database/migrations/`
2. Model → `app/Models/`
3. Controller → `app/Http/Controllers/`
4. Views → `resources/views/<nama_modul>/`
5. Routes → `routes/web.php` di grup middleware `auth`
6. Sidebar → `resources/views/layouts/sidebar.blade.php`
7. Reset → `app/Http/Controllers/SettingController` (tambah nama tabel)
8. Test → `tests/Feature/`

---

## 10. Standar Kode

- **File naming**: PascalCase untuk Controller/Model, snake_case untuk view file
- **DB table naming**: `snake_case` plural
- **Route naming**: `kebab-case` → prefix route alias `kebab-case`
- **Role check**: di constructor Controller, bukan di method
- **View inheritance**: layouts/app.blade.php → @yield('title') + @stack('styles') + @push('scripts')
