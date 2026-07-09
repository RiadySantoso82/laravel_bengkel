# Aplikasi Manajemen Bengkel

Aplikasi manajemen bengkel berbasis Laravel 8 dengan MySQL. Mencakup manajemen servis kendaraan, stok sparepart (FIFO), penjualan retail, dan multi-role user (admin, kasir, mekanik).

## Requirements

- PHP 7.4+
- Composer
- MySQL
- PHP Extensions: PDO, mbstring, xml, curl, gd (atau imagick untuk kompresi foto)

## Instalasi

```bash
git clone <repo-url>
cd bengkel
composer install
copy .env.example .env
# Isi DB_USERNAME, DB_PASSWORD, DB_DATABASE di .env
php artisan key:generate
php artisan storage:link
php artisan migrate --seed
php artisan serve
```

## User Default

| Role | Username | Password |
|------|----------|----------|
| Admin/Owner | `admin` | `admin123` |
| Kasir | `kasir` | `kasir123` |
| Mekanik | `mekanik` | `mekanik123` |

---

## 📋 Role & Hak Akses

| Fitur | Admin | Kasir | Mekanik |
|-------|-------|-------|---------|
| Dashboard utama | ✅ | ✅ | — |
| Dashboard mekanik | — | — | ✅ |
| Service Order (CRUD) | ✅ | ✅ | — |
| Daftar Servis Saya | — | — | ✅ |
| Riwayat Servis | — | — | ✅ |
| Invoice | ✅ | ✅ | — |
| Penjualan (retail) | ✅ | ✅ | — |
| Part Request (fulfill) | ✅ | ✅ | — |
| Part Request (request) | — | — | ✅ |
| Retur Part | ✅ | ✅ | — |
| Stock Adjustment | ✅ | ✅ | — |
| Laporan Stok | ✅ | ✅ | — |
| Data Master (pelanggan, sparepart, dll) | ✅ | ✅ | — |
| Manajemen User | ✅ | — | — |
| Reset Data | ✅ | — | — |

---

## 📁 Struktur Modul

### 1. Service Order (Servis Kendaraan)

Alur: **Antri → Dikerjakan → Tunggu Part → Selesai → Diambil**

| Status | Keterangan |
|--------|------------|
| `queued` | Antri, belum dikerjakan |
| `in_progress` | Sedang dikerjakan mekanik |
| `waiting_part` | Menunggu part dari gudang |
| `done` | Pengerjaan selesai |
| `picked_up` | Kendaraan sudah diambil pelanggan |

- Mekanik hanya bisa melihat & mengupdate service order yang di-assign ke dirinya
- Admin/kasir bisa membuat & mengelola semua service order
- Setiap service order bisa memiliki banyak item (jasa/part)
- **Walk-in**: dukungan pelanggan tidak tetap, input manual plat & info kendaraan

### 2. Part Request (Permintaan Part)

Alur: **Mekanik request → Gudang fulfill → Mekanik terima → (opsional) Retur**

**Status (Header):**
| Status | Keterangan |
|--------|------------|
| `requested` | Menunggu dipenuhi |
| `partial` | Sebagian sudah dipenuhi |
| `fulfilled` | Semua sudah dipenuhi |

**Status (Detail):**
| Status | Keterangan |
|--------|------------|
| `pending` | Belum dipenuhi |
| `fulfilled` | Sudah dipenuhi |
| `partial` | Sebagian dipenuhi |
| `returned` | Dikembalikan oleh mekanik |

- Mekanik request part dari halaman detail service order (modal multi-item)
- Admin/kasir fulfill per baris detail (qty parsial bisa)
- Saat fulfill: stok keluar via FIFO, stock_movement tercatat
- Mekanik bisa retur part (alasan: tidak cocok / tidak dipakai)
- Retur menunggu konfirmasi admin sebelum stok balik

### 3. Stock Management (FIFO)

Sistem menggunakan **FIFO (First In First Out)** untuk perhitungan HPP dan alokasi stok.

**Tabel:**
| Tabel | Fungsi |
|-------|--------|
| `stock_batches` | Batch stok masuk (qty_in, qty_remaining, buy_price, received_date) |
| `stock_movements` | Catatan pergerakan stok (in/out) dengan source_type & source_id |
| `stock_movement_allocations` | Alokasi stok keluar dari batch tertentu (FIFO) |

Alur FIFO:
1. Stok **masuk** (purchase / adjustment / retur) → buat `stock_batch` baru
2. Stok **keluar** (penjualan / part request) → sistem ambil dari batch tertua (`received_date`) yang masih punya `qty_remaining`
3. Satu transaksi keluar bisa mengambil dari banyak batch jika batch pertama tidak cukup
4. HPP dihitung dari `buy_price` tiap batch yang terpakai

**Source Types:**
| Source | Keterangan |
|--------|------------|
| `purchase_order` | Pembelian dari supplier |
| `part_request` | Part keluar ke mekanik |
| `part_return` | Part kembali ke gudang |
| `stock_adjustment` | Koreksi manual / saldo awal |
| `sales_order_detail` | Penjualan retail |
| `service_order_detail` | Pemakaian part di servis |

### 4. Sales Order (Penjualan Retail)

Penjualan sparepart langsung (tanpa melalui servis kendaraan).

**Status Pembayaran:**
| Status | Keterangan |
|--------|------------|
| `pending` | Belum dibayar |
| `paid` | Lunas |

- Kasir melayani penjualan walk-in atau pelanggan terdaftar
- Keranjang belanja dengan qty +/−, hapus item
- Pencarian sparepart via modal (hanya menampilkan yang punya stok > 0)
- FIFO allocation otomatis setiap kali transaksi
- HPP tercatat di `sales_order_details.cost_price`
- Tampilan margin (Total - HPP) di halaman detail

### 5. Stock Adjustment

Koreksi manual stok sparepart (saldo awal, stock opname, barang rusak/hilang).

- Pencarian sparepart via modal popup (dengan filter kategori)
- **Tambah stok** (+): buat batch baru dengan harga beli
- **Kurangi stok** (−): alokasi FIFO dari batch tertua
- Validasi: pengurangan tidak boleh melebihi stok saat ini
- Hapus adjustment: jika penambahan stok, cek dulu stok tidak boleh minus setelah dihapus

### 6. Checklist & Foto Servis

- Admin mengelola master **checklist item** (aktif/nonaktif)
- Mekanik mencentang checklist + catatan per item di halaman detail servis
- Checklist dan notes otomatis nonaktif jika status servis sudah selesai
- Foto kendaraan (sebelum/sesudah) bisa diupload, otomatis dikompres (max 1200px, JPG 75%)
- Foto bisa dilihat via lightbox klik

### 7. Laporan

| Menu | Keterangan |
|------|------------|
| Sisa Stok | Status stok per sparepart (normal/menipis/habis) |
| Stock Movement | Histori mutasi stok dengan filter part, tipe, tanggal |

---

## 🗄️ Database Schema (ERD)

Lihat file `docs/erd-aplikasi-bengkel.mermaid` untuk diagram ERD lengkap.

### Master Data
- `users` — login & role (admin/kasir/mekanik)
- `customers` — data pelanggan (is_walk_in untuk walk-in)
- `vehicles` — kendaraan per pelanggan
- `suppliers` — data supplier
- `mechanics` — data mekanik (terhubung ke users via user_id)
- `spareparts` — master sparepart (stock_qty virtual dari stock_batches)
- `sparepart_categories` — kategori sparepart
- `units` — satuan sparepart (pcs, liter, dll)
- `service_types` — master jasa servis
- `service_categories` — kategori jasa servis
- `payment_methods` — metode pembayaran
- `checklist_items` — master item checklist servis

### Transaksi
- `service_orders` — work order servis
- `service_order_details` — item jasa/part dalam servis
- `service_order_checklist` — histori checklist per servis
- `service_order_photos` — foto kondisi kendaraan
- `part_requests` — permintaan part dari mekanik
- `part_request_details` — rincian part yang diminta
- `part_returns` — retur part oleh mekanik
- `invoices` — invoice dari service order
- `sales_orders` — penjualan retail sparepart
- `sales_order_details` — rincian penjualan + cost_price (FIFO)

### Inventory
- `stock_movements` — ledger pergerakan stok (in/out)
- `stock_batches` — batch stok masuk (FIFO)
- `stock_movement_allocations` — alokasi FIFO stok keluar
- `stock_adjustments` — koreksi manual stok

### Pembelian
- `suppliers` — data supplier
- `purchase_orders` — PO pembelian
- `purchase_order_details` — rincian PO

---

## 🔄 Alur Proses

### Servis Kendaraan
```
Pelanggan datang → Kasir buat Service Order (pilih pelanggan, kendaraan, mekanik, keluhan, item)
  → Mekanik lihat di "Daftar Servis Saya" → Mulai Kerjakan
  → Jika butuh part: Request Part (pilih part & qty)
  → Admin/kasir fulfill part request (stok keluar via FIFO)
  → Mekanik konfirmasi part sesuai (status balik ke dikerjakan)
  → Jika part tidak cocok: Retur (pilih alasan)
  → Admin konfirmasi retur (stok balik)
  → Mekanik centang checklist + foto → Tandai Selesai
  → Kasir buat Invoice → Pelanggan bayar → Status Diambil
```

### Penjualan Retail
```
(Pelanggan datang beli part langsung)
  → Kasir buka menu Penjualan
  → Pilih / input pelanggan
  → Cari sparepart via modal → tambah ke keranjang
  → Atur qty
  → Pilih metode bayar
  → Proses (FIFO allocation otomatis)
  → Stok berkurang sesuai batch tertua
```

### Reset Data (Admin Only)
```
Menu Pengaturan → Reset Data
  → Semua tabel transaksi di-truncate
  → Seeder jalan ulang (user default, master data)
  → Logout otomatis
```

---

## 🧪 Testing

```bash
# Jalankan server
php artisan serve --port=8001

# Login sebagai:
# admin / admin123   → full akses
# kasir / kasir123   → transaksi & master
# mekanik / mekanik123 → panel mekanik
```
