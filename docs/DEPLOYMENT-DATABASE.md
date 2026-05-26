# Panduan Database — Deployment Sementara

## Ringkasan

Aplikasi memakai **dua koneksi database**:

| Koneksi | Env | Isi utama |
|---------|-----|-----------|
| `mysql` (default) | `DB_*` | users, groups, penilaian, form_kesediaan, peer_review, dll. |
| `dashboard` | `DASHBOARD_DB_*` | proposal, laporan_akhir, luaran (file upload) |

Migrasi lama **tidak lengkap** untuk fresh install (kolom `users`, `penilaian`, tabel dokumen kosong).  
Migrasi sinkronisasi **2026_05_22** menutup gap tersebut.

---

## Langkah deploy database (server baru / kosong)

### 1. Buat database

```sql
CREATE DATABASE inovasi_sosial CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE dashboardta CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

*(Sesuaikan nama dengan `.env`)*

### 2. Atur `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=inovasi_sosial
DB_USERNAME=...
DB_PASSWORD=...

DASHBOARD_DB_HOST=127.0.0.1
DASHBOARD_DB_DATABASE=dashboardta
DASHBOARD_DB_USERNAME=...
DASHBOARD_DB_PASSWORD=...

# URL API dashboard (jika upload file lewat HTTP)
DASHBOARD_URL=http://your-dashboard-host
```

### 3. Jalankan migrasi

```bash
php artisan migrate --force
```

Ini menjalankan semua migrasi + **sync_app_schema** + **sync_dashboard_document_tables**.

### 4. (Opsional) Seed user awal

```bash
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=DosenSeeder
# dll.
```

### 5. Storage & cache

```bash
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Server yang sudah punya data lama

Jangan `migrate:fresh` (akan menghapus data).

```bash
php artisan migrate --force
```

Migrasi `2026_05_22_*` hanya **menambah** kolom/tabel yang belum ada.

---

## Cek migrasi

```bash
php artisan migrate:status
```

---

## Tabel wajib per fitur (app DB)

| Fitur | Tabel |
|-------|--------|
| Login / role | `users` |
| Daftar kelompok | `groups`, `group_members`, `supervisor_requests` |
| Validasi proposal (status) | kolom di `groups` |
| Validasi laporan/luaran | `group_document_reviews` |
| Rubrik CPMK | `group_cpmk_rubrics` |
| Form kesediaan | `form_kesediaan` |
| Peer review | `peer_review` |
| Penilaian dosen | `penilaian` |
| Nilai CPMK file | `nilai_cpmk` |
| Legacy pendaftaran | `kkn_pendaftar`, `kkn_anggota` |

## Tabel wajib (dashboard DB)

| Fitur | Tabel |
|-------|--------|
| Upload proposal | `proposal` |
| Upload laporan akhir | `laporan_akhir` |
| Upload luaran | `luaran` |

---

## Masalah umum

| Gejala | Penyebab | Solusi |
|--------|----------|--------|
| Login gagal / kolom nim tidak ada | Migrasi users belum jalan | `php artisan migrate` |
| Upload laporan/luaran gagal | Dashboard DB / API salah | Cek `DASHBOARD_DB_*` dan `DASHBOARD_URL` |
| Penilaian error | Kolom skor belum ada | Jalankan migrasi `2026_05_22_100000` |
| `renameColumn` error | Butuh `doctrine/dbal` | `composer require doctrine/dbal` lalu migrate lagi |

---

## Perintah fresh install (hanya development)

```bash
php artisan migrate:fresh --seed
```

**Jangan** dipakai di production jika sudah ada data pengguna.
