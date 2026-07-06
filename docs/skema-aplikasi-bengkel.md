# Skema & Fitur Aplikasi Manajemen Bengkel

## 1. Konsep Umum

Aplikasi ini ditujukan untuk bengkel (motor/mobil) yang ingin mengelola:
- Antrian & servis kendaraan
- Data pelanggan & kendaraan
- Stok sparepart
- Keuangan (kasir, invoice, laporan)
- Mekanik & jadwal kerja

Bisa berbentuk web app, aplikasi mobile (untuk pemilik/mekanik), atau keduanya (multi-platform dengan backend yang sama).

---

## 2. Aktor / Role Pengguna

| Role | Akses Utama |
|---|---|
| **Owner/Admin** | Semua fitur, laporan keuangan, manajemen user |
| **Kasir/Front Office** | Input servis masuk, buat invoice, terima pembayaran |
| **Mekanik** | Lihat tugas servis, update status pekerjaan, checklist |
| **Pelanggan** (opsional, jika ada app customer) | Booking servis, lihat riwayat, lihat status antrian |

---

## 3. Modul & Fitur Utama

### A. Manajemen Pelanggan & Kendaraan
- Data pelanggan (nama, HP, alamat, email)
- Data kendaraan (plat nomor, merk, tipe, tahun, no. rangka/mesin)
- Riwayat servis per kendaraan
- Reminder servis berkala (misal servis rutin 3 bulan/3000 km)

### B. Manajemen Servis / Work Order
- Input keluhan pelanggan
- Estimasi biaya & waktu pengerjaan
- Assign mekanik ke pekerjaan
- Status pekerjaan: **Antri → Dikerjakan → Menunggu Part → Selesai → Diambil**
- Checklist pengecekan kendaraan (misal: cek oli, rem, aki, dll)
- Upload foto kondisi kendaraan (sebelum/sesudah)
- Approval biaya tambahan (jika ada temuan kerusakan lain)

### C. Manajemen Sparepart & Stok
- Master data sparepart (kode, nama, harga beli, harga jual, stok)
- Kategori sparepart
- Stok masuk (dari supplier) & stok keluar (dipakai servis)
- Alert stok minimum
- Riwayat pemakaian part per servis

### D. Kasir & Pembayaran
- Buat invoice otomatis dari work order
- Rincian biaya jasa + sparepart
- Metode pembayaran (cash, transfer, QRIS, kartu)
- Diskon/promo
- Cetak/kirim invoice (PDF/WhatsApp)

### E. Booking & Antrian (opsional untuk pelanggan)
- Booking servis online (pilih tanggal & jam)
- Nomor antrian & estimasi waktu tunggu
- Notifikasi status servis via WhatsApp/SMS/push notification

### F. Manajemen Mekanik
- Data mekanik & spesialisasi
- Jadwal kerja/shift
- Beban kerja (jumlah job aktif per mekanik)
- Rating/performance mekanik (opsional)

### G. Laporan & Analitik
- Laporan omzet harian/bulanan
- Laporan jasa terlaris & part terlaris
- Laporan performa mekanik
- Laporan stok & pembelian part
- Grafik tren pelanggan (baru vs repeat)

### H. Pengaturan Sistem
- Manajemen user & hak akses (role-based)
- Master data jasa/tarif servis
- Konfigurasi cabang (jika multi-cabang)
- Backup data

---

## 4. Skema Database (Entitas Utama)

```
CUSTOMER
- customer_id (PK)
- name
- phone
- email
- address
- created_at

VEHICLE
- vehicle_id (PK)
- customer_id (FK)
- plate_number
- brand
- model
- year
- chassis_number
- engine_number

SERVICE_ORDER (Work Order)
- order_id (PK)
- vehicle_id (FK)
- customer_id (FK)
- mechanic_id (FK)
- complaint
- status (queued, in_progress, waiting_part, done, picked_up)
- created_at
- estimated_finish
- actual_finish

SERVICE_ORDER_DETAIL
- detail_id (PK)
- order_id (FK)
- type (jasa/part)
- item_id (FK ke SERVICE_TYPE atau SPAREPART)
- qty
- price

SERVICE_TYPE (Master Jasa)
- service_type_id (PK)
- name
- base_price
- estimated_duration

SPAREPART
- part_id (PK)
- code
- name
- category
- buy_price
- sell_price
- stock_qty
- min_stock

STOCK_MOVEMENT
- movement_id (PK)
- part_id (FK)
- type (in/out)
- qty
- reference_id (order_id atau purchase_id)
- created_at

MECHANIC
- mechanic_id (PK)
- name
- specialization
- phone
- status (active/inactive)

INVOICE
- invoice_id (PK)
- order_id (FK)
- total_amount
- discount
- payment_method
- payment_status
- created_at

USER (untuk login sistem)
- user_id (PK)
- username
- password_hash
- role (admin, kasir, mekanik)
```

**Relasi utama:**
- 1 Customer → banyak Vehicle
- 1 Vehicle → banyak Service Order
- 1 Service Order → banyak Service Order Detail (jasa & part yang dipakai)
- 1 Service Order → 1 Invoice
- 1 Sparepart → banyak Stock Movement

---

## 5. Alur Proses Utama (Flow Servis)

1. Pelanggan datang / booking online
2. Kasir buat **Service Order** baru → input keluhan & kendaraan
3. Assign mekanik → mekanik mulai kerjakan, update status
4. Jika butuh part tambahan → sistem cek stok → jika kurang, butuh approval biaya tambahan
5. Servis selesai → mekanik update status "Done"
6. Kasir buat **Invoice** dari Service Order → pelanggan bayar
7. Data tersimpan sebagai riwayat servis kendaraan tsb
8. Sistem set reminder servis berikutnya

---

## 6. Rekomendasi Tech Stack (opsional)

- **Frontend web**: React / Vue
- **Mobile**: Flutter atau React Native (agar 1 codebase Android+iOS)
- **Backend**: Node.js (Express/Nest) atau Laravel
- **Database**: PostgreSQL atau MySQL
- **Notifikasi**: WhatsApp Business API / Firebase Cloud Messaging
- **Hosting**: VPS atau cloud (bisa mulai dari shared hosting untuk skala kecil)

---

## 7. Fitur Tambahan (Nice to Have)

- Integrasi pembayaran QRIS/e-wallet
- Program membership/poin loyalitas pelanggan
- Multi-cabang dengan laporan konsolidasi
- Marketplace sparepart terintegrasi
- Chatbot WhatsApp untuk booking otomatis
