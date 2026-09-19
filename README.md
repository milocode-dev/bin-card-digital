# Bin Card Digital

Aplikasi web internal untuk mencatat barang masuk/keluar gudang hotel, menggantikan bin card kertas. Dipakai oleh tim cost control, fokus awal di General Store, dengan rencana ekspansi ke 2 gudang lain di kemudian hari.

## Latar Belakang

Berdasarkan pengalaman di cost control gudang:
- Sering lupa menulis bin card di kertas
- Barang untuk satu store requisition sering tersebar di beberapa sisi gudang (sisi A/B/C) — staff harus bolak-balik
- Sulit cek stok tanpa datang langsung ke general store

## Manfaat yang Ditargetkan

1. Memudahkan inventory check di general store
2. Menghindari lupa menulis bin card di kertas
3. Mempermudah pergerakan di store — cukup buka HP dari tempat berdiri, langsung input masuk/keluar
4. Memudahkan saat open store tanpa harus ke general store

## Fitur MVP

1. **CRUD barang gudang**
2. **Filter/search barang** — termasuk cek detail stok
3. **Kartu per barang** — riwayat transaksi + saldo berjalan per item; ini inti aplikasi, versi digital dari bin card itu sendiri
4. **Stock opname** — bandingkan stok fisik vs sistem, catat selisih sebagai transaksi adjustment
5. **Export PDF**, dikerjakan berurutan karena berbagi mesin PDF yang sama:
   - Kartu barang (riwayat + saldo berjalan satu item)
   - Laporan stok saat ini (snapshot untuk opname)
   - Laporan mutasi per periode (filter tanggal + departemen)

## Prinsip Desain Inti

- **Ledger (`transactions`) adalah sumber kebenaran.** Baris di sana tidak pernah diedit/dihapus. Kesalahan dikoreksi lewat transaksi `adjustment` baru — persis seperti bin card kertas yang dicoret lalu ditulis baris baru, bukan dihapus.
- **`items.stock` adalah cache**, bukan fakta. Dijaga sinkron lewat setiap transaksi, dan sebaiknya ada fitur "hitung ulang dari ledger" untuk jaga-jaga kalau melenceng.
- **`stock_after` disimpan di tiap baris transaksi**, supaya kartu barang tidak perlu SUM berulang, dan tampilannya menyerupai bin card asli.
- **Race condition ditangani secara sengaja:**
  - `DB::transaction()` → menjamin *atomicity* (kalau satu langkah gagal, semua batal)
  - `lockForUpdate()` di dalam transaction yang sama → menjamin *isolation* (baris yang sedang diproses tidak bisa dibaca/diubah proses lain sampai commit selesai)
  - Keduanya dibutuhkan bersama: atomicity mencegah data setengah-jalan, isolation mencegah lost update saat dua orang input barang yang sama nyaris bersamaan.
- **Stok tidak boleh minus** — transaksi keluar yang membuat stok minus ditolak, diarahkan ke stock opname/adjustment.
- **Role dan lokasi kerja dipisah** — `role` (admin/user) tidak mengandung nama gudang di dalamnya; penempatan gudang ada di kolom/tabel terpisah.

## ERD

```
users
├─ id
├─ name
├─ email
├─ password
├─ role            enum: admin, user
├─ warehouse_id    FK → warehouses.id (nullable)
└─ timestamps

warehouses
├─ id
├─ name                     contoh: "General Store"
└─ timestamps

departments
├─ id
├─ name                     contoh: F&B, Housekeeping, Engineering
└─ timestamps

items
├─ id
├─ name
├─ code             nullable
├─ unit             nullable
├─ location         nullable
├─ min_stock        nullable — untuk alert reorder
├─ stock            cache; sumber kebenaran tetap di transactions
├─ warehouse_id     FK → warehouses.id
├─ deleted_at       (soft delete)
└─ timestamps

transactions
├─ id
├─ user_id           FK → users.id
├─ item_id           FK → items.id
├─ department_id     FK → departments.id (nullable)
├─ warehouse_id      FK → warehouses.id
├─ type               enum: masuk, keluar, adjustment
├─ quantity           immutable setelah disimpan
├─ stock_after        saldo berjalan setelah transaksi ini
├─ reference_no       nullable
├─ notes              nullable
├─ transaction_date   tanggal kejadian (beda dari created_at)
└─ timestamps
```

## Kegunaan Masing-Masing Field

### `users`
| Field | Kegunaan |
|---|---|
| `role` | Membedakan wewenang: `admin` bisa CRUD item & kelola user; `user` input transaksi & lihat data |
| `warehouse_id` | Menandai staff ini bertugas di gudang mana; nullable karena admin bisa tidak terikat satu gudang |

### `warehouses`
| Field | Kegunaan |
|---|---|
| `name` | Nama gudang; diseed "General Store" dulu, gudang lain ditambah belakangan tanpa ubah struktur |

### `departments`
| Field | Kegunaan |
|---|---|
| `name` | Daftar departemen tetap (F&B, Housekeeping, dst.) untuk dropdown saat input transaksi keluar, menghindari free-text yang tidak konsisten |

### `items`
| Field | Kegunaan |
|---|---|
| `code` | Kode barang, mempercepat pencarian/scan |
| `unit` | Satuan (pcs/box/kg) — menghindari ambiguitas angka stok |
| `location` | Posisi rak/sisi gudang — langsung menjawab masalah "barang mencar" |
| `min_stock` | Ambang batas untuk alert reorder |
| `stock` | Cache stok saat ini, dihitung ulang dari `transactions` bila melenceng |
| `deleted_at` | Soft delete — item dengan riwayat transaksi tidak boleh hilang total |

### `transactions`
| Field | Kegunaan |
|---|---|
| `type` | `masuk` / `keluar` / `adjustment` — adjustment dipakai untuk koreksi & hasil stock opname, bukan edit langsung |
| `quantity` | Jumlah barang bergerak; tidak diedit setelah tersimpan, koreksi lewat baris `adjustment` baru |
| `stock_after` | Saldo setelah transaksi ini — bikin kartu barang tidak perlu hitung ulang tiap saat |
| `reference_no` | Nomor dokumen acuan (SR/DO/PO), nullable |
| `notes` | Keterangan tambahan, nullable |
| `transaction_date` | Tanggal kejadian sebenarnya, bisa beda dari `created_at` (input belakangan) |

## Langkah Kerja

### 1. Persiapan
- [✅] Setup project Laravel baru
- [✅] Setup database & koneksi
- [✅] Buat migration: `warehouses`, `departments`, `items`, `transactions` (kolom `role` & `warehouse_id` ditambahkan ke `users` bawaan)
- [✅] Buat seeder: `warehouses` (General Store), `departments` (daftar departemen hotel)

### 2. Autentikasi & Role
- [✅] Setup login/register (atau seed user admin manual dulu)
- [✅] Middleware/gate untuk membedakan akses `admin` vs `user`

### 3. Modul Items
- [✅] CRUD item (create, read, update, soft delete)
- [] Halaman list item dengan filter/search (nama, code, kategori jika ada)
- [] Validasi field wajib vs nullable

### 4. Modul Transactions
- [ ] Form input transaksi masuk/keluar
- [ ] Logika update stok: `DB::transaction()` + `lockForUpdate()` pada baris item
- [ ] Validasi stok tidak boleh minus, dengan pesan yang mengarahkan ke penyesuaian stok
- [ ] Simpan `stock_after` di tiap baris transaksi
- [ ] Halaman riwayat transaksi per item (kartu barang)

### 5. Stock Opname
- [ ] Form opname: daftar item + stok sistem (read-only) + input stok fisik
- [ ] Generate transaksi `adjustment` otomatis untuk tiap item yang selisih

### 6. Export PDF
- [ ] PDF kartu barang (riwayat + saldo berjalan, satu item)
- [ ] PDF laporan stok saat ini (snapshot untuk opname)
- [ ] PDF laporan mutasi per periode (filter tanggal + departemen)

### 7. Penyempurnaan
- [ ] Fitur "hitung ulang stok dari ledger" (rekonsiliasi cache)
- [ ] Optimasi tampilan mobile (target 3–4 tap per transaksi)
- [ ] Disable tombol submit setelah ditekan (cegah transaksi dobel)
- [ ] Testing skenario dua user input item yang sama bersamaan

### 8. Menyusul (belum untuk MVP)
- [ ] Ekspansi ke gudang ke-2 dan ke-3 (tinggal tambah baris di `warehouses`)
- [ ] Kategori item (`category_id`) jika daftar item makin banyak
- [ ] Sambungkan dengan open store departement lain lalu barang yang diambil beserta data pengambilan masuk ke dalam 
      notifikasi warehouse user
- [ ] Halaman khusus untuk inventory check