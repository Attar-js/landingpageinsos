# ERD Notasi Chen — Sistem Inovasi Sosial (Project Akhir)

Diagram ini mengikuti **notasi Chen** seperti contoh referensi:
- **Persegi** = entitas
- **Elips** = atribut (garis bawah = primary key)
- **Belah ketupat** = relasi
- **1 / N** = kardinalitas

Sistem memakai **2 database**:
| Database | Koneksi | Isi |
|----------|---------|-----|
| Aplikasi | `DB_*` | User, kelompok, penilaian, peer review, verifikasi |
| Dashboard | `DASHBOARD_DB_*` | Proposal, laporan akhir, luaran (file upload) |

---

## Cara render diagram (jadi gambar PNG/SVG)

### Opsi A — PlantUML Online (gratis)
1. Buka https://www.plantuml.com/plantuml/uml/
2. Salin isi file:
   - `docs/erd/erd-app-database.puml` → diagram database aplikasi
   - `docs/erd/erd-dashboard-database.puml` → diagram database dashboard
3. Klik **Submit** → unduh PNG/SVG

### Opsi B — VS Code / Cursor
1. Install extension **PlantUML**
2. Buka file `.puml` → preview (Alt+D)
3. Export PNG untuk lampiran skripsi

### Opsi C — Draw.io / Lucidchart
Gunakan tabel atribut di bawah sebagai acuan menggambar manual (paling mirip contoh referensi Anda).

---

## Diagram 1 — Database Aplikasi (alur utama)

**Entitas pusat:** `Kelompok` (groups) — alur konversi KKN berpusat di kelompok, bukan `kkn_pendaftar`.

### Relasi utama

| Relasi | Entitas 1 | Kard. | Entitas 2 | Kard. | Keterangan |
|--------|-----------|-------|-----------|-------|------------|
| Membimbing | User (dosen) | 1 | Kelompok | N | `groups.dosen_id` |
| Memimpin | User (ketua) | 1 | Kelompok | N | `groups.leader_id` |
| Memiliki Anggota | Kelompok | 1 | Anggota Kelompok | N | `group_members` |
| Menjadi Anggota | User (mahasiswa) | N | Anggota Kelompok | N | via `group_members.mahasiswa_id` |
| Mengajukan Pembimbing | Kelompok | 1 | Permintaan Pembimbing | N | `supervisor_requests` |
| Ditujukan Kepada | User (dosen) | 1 | Permintaan Pembimbing | N | `supervisor_requests.supervisor_id` |
| Menilai | User (dosen) | 1 | Penilaian | N | `penilaian.dosen_id` |
| Dinilai | User (mahasiswa) | 1 | Penilaian | N | `penilaian.mahasiswa_nim` |
| Melakukan Review | Kelompok | 1 | Peer Review | N | `peer_review.group_id` |
| Memberi Review | User (reviewer) | 1 | Peer Review | N | `peer_review.reviewer_id` |
| Menerima Review | User (reviewee) | 1 | Peer Review | N | `peer_review.reviewee_id` |
| Memverifikasi | Kelompok | 1 | Verifikasi Dokumen | 1 | `group_document_reviews` |
| Memverifikasi Dokumen | User (dosen/tim) | 1 | Verifikasi Dokumen | N | `reviewed_by` |
| Mengisi Rubrik | Kelompok | 1 | Rubrik CPMK | 1 | `group_cpmk_rubrics` |
| Mengisi Rubrik CPMK | User (ketua) | 1 | Rubrik CPMK | N | `filled_by` |
| Mengunggah Form | User (dosen) | 1 | Form Kesediaan | N | `form_kesediaan.user_nim` |

---

## Diagram 2 — Database Dashboard (dokumen)

Relasi ke aplikasi **logis** (bukan FK database): `user_nim` + `judul_kegiatan`.

| Relasi | Entitas 1 | Kard. | Entitas 2 | Kard. |
|--------|-----------|-------|-----------|-------|
| Mengunggah Proposal | User | 1 | Proposal | N |
| Terhubung Judul | Kelompok | 1 | Proposal | N |
| Mengunggah Laporan | User | 1 | Laporan Akhir | N |
| Terhubung Judul | Kelompok | 1 | Laporan Akhir | N |
| Mengunggah Luaran | User | 1 | Luaran | N |
| Terhubung Judul | Kelompok | 1 | Luaran | N |

---

## Tabel Atribut Entitas

### 1. User (`users`)

| Atribut | Tipe | Keterangan |
|---------|------|------------|
| **idUser** | BIGINT (PK) | Primary key |
| username | VARCHAR | Login |
| password | VARCHAR | Hash password |
| role | VARCHAR | mahasiswa, dosen, tim_penciri, admin |
| nim | VARCHAR | NIM mahasiswa |
| nip | VARCHAR | NIP dosen |
| program_studi | VARCHAR | Prodi mahasiswa |
| nama | VARCHAR | Nama lengkap (`name`) |
| email | VARCHAR | Email unik |
| status | VARCHAR | active / inactive |
| phone_number | VARCHAR | Opsional |
| user_type | VARCHAR | Tipe user |

### 2. Kelompok (`groups`)

| Atribut | Tipe | Keterangan |
|---------|------|------------|
| **idKelompok** | BIGINT (PK) | Primary key |
| nama_kelompok | VARCHAR | Nama kelompok KKN |
| judul_kegiatan | TEXT | Judul kegiatan |
| lokasi_kkn | VARCHAR | Lokasi KKN |
| deskripsi_kegiatan | TEXT | Deskripsi |
| nama_mitra | VARCHAR | Mitra |
| lokasi_mitra | VARCHAR | Lokasi mitra |
| dosen_id | FK → users | Dosen pembimbing |
| leader_id | FK → users | Ketua kelompok |
| assigned_by | FK → users | Admin assigner |
| assigned_at | TIMESTAMP | Waktu assign |
| supervisor_approved_at | TIMESTAMP | Persetujuan dosen |
| status | VARCHAR | pending, assigned, approved, rejected |
| progress_verifikasi | INT | 0–100% |
| proposal_review_status | VARCHAR | Status review proposal tim penciri |
| proposal_review_note | TEXT | Catatan review proposal |
| catatan | TEXT | Catatan dosen |

### 3. Anggota Kelompok (`group_members`)

| Atribut | Tipe | Keterangan |
|---------|------|------------|
| **idAnggota** | BIGINT (PK) | Primary key |
| group_id | FK → groups | Kelompok |
| mahasiswa_id | FK → users | Mahasiswa anggota |
| role | VARCHAR | leader / member |
| status | VARCHAR | active / inactive |
| dropped_at | TIMESTAMP | Waktu keluar kelompok |
| drop_reason | TEXT | Alasan keluar |

### 4. Permintaan Pembimbing (`supervisor_requests`)

| Atribut | Tipe | Keterangan |
|---------|------|------------|
| **idPermintaan** | BIGINT (PK) | Primary key |
| group_id | FK → groups | Kelompok pengaju |
| supervisor_id | FK → users | Dosen dituju |
| requested_by | FK → users | Mahasiswa pengaju |
| status | VARCHAR | pending, approved, rejected |
| note | TEXT | Catatan |
| responded_at | TIMESTAMP | Waktu respons |

### 5. Penilaian (`penilaian`)

| Atribut | Tipe | Keterangan |
|---------|------|------------|
| **idPenilaian** | BIGINT (PK) | Primary key |
| mahasiswa_nim | VARCHAR | NIM mahasiswa dinilai |
| dosen_id | FK → users | Dosen penilai |
| proposal_kegiatan | DECIMAL | Nilai komponen |
| asistensi | DECIMAL | Nilai komponen |
| peer_review | DECIMAL | Nilai komponen |
| laporan_akhir | DECIMAL | Nilai komponen |
| presentasi_akhir | DECIMAL | Nilai komponen |
| pembimbing_lapangan | DECIMAL | Nilai komponen |
| nilai_akhir | DECIMAL | Nilai total |
| catatan | TEXT | Catatan penilaian |
| tanggal_penilaian | TIMESTAMP | Waktu penilaian |

### 6. Peer Review (`peer_review`)

| Atribut | Tipe | Keterangan |
|---------|------|------------|
| **idPeerReview** | BIGINT (PK) | Primary key |
| group_id | FK → groups | Kelompok |
| reviewer_id | FK → users | Pemberi review |
| reviewee_id | FK → users | Penerima review |
| judul_kegiatan | VARCHAR | Judul kegiatan |
| kontribusi_kegiatan | DECIMAL | Skor aspek |
| tanggung_jawab | DECIMAL | Skor aspek |
| kerjasama_tim | DECIMAL | Skor aspek |
| inisiatif_motivasi | DECIMAL | Skor aspek |
| final_score | DECIMAL | Skor akhir |
| status | ENUM | pending, approved, rejected |
| catatan | TEXT | Catatan |
| submitted_at | TIMESTAMP | Waktu submit |

### 7. Verifikasi Dokumen (`group_document_reviews`)

| Atribut | Tipe | Keterangan |
|---------|------|------------|
| **idVerifikasi** | BIGINT (PK) | Primary key |
| group_id | FK → groups | Kelompok (unik) |
| reviewed_by | FK → users | Verifikator |
| status | ENUM | pending, approved, rejected |
| laporan_status | VARCHAR | Status laporan |
| artikel_status | VARCHAR | Status artikel |
| video_status | VARCHAR | Status video |
| note | TEXT | Catatan umum |
| laporan_note | TEXT | Catatan laporan |
| artikel_note | TEXT | Catatan artikel |
| video_note | TEXT | Catatan video |
| reviewed_at | TIMESTAMP | Waktu verifikasi |

### 8. Rubrik CPMK (`group_cpmk_rubrics`)

| Atribut | Tipe | Keterangan |
|---------|------|------------|
| **idRubrik** | BIGINT (PK) | Primary key |
| group_id | FK → groups | Kelompok (unik) |
| deskripsi_p5 | TEXT | Deskripsi CPMK P5 |
| deskripsi_c3 | TEXT | Deskripsi CPMK C3 |
| deskripsi_a2 | TEXT | Deskripsi CPMK A2 |
| skor_p5 | DECIMAL | Skor P5 |
| skor_c3 | DECIMAL | Skor C3 |
| skor_a2 | DECIMAL | Skor A2 |
| catatan | TEXT | Catatan |
| filled_by | FK → users | Pengisi (ketua) |

### 9. Form Kesediaan (`form_kesediaan`)

| Atribut | Tipe | Keterangan |
|---------|------|------------|
| **idForm** | BIGINT (PK) | Primary key |
| judul_kegiatan | VARCHAR | Judul kegiatan |
| file_name | VARCHAR | Nama file PDF |
| file_path | VARCHAR | Path storage |
| user_nim | VARCHAR | NIM dosen pengunggah |
| status | VARCHAR | pending, approved, rejected |
| catatan | TEXT | Catatan verifikasi |

---

## Entitas Dashboard (`DASHBOARD_DB_*`)

### 10. Proposal (`proposal`)

| Atribut | Tipe | Keterangan |
|---------|------|------------|
| **idProposal** | BIGINT (PK) | Primary key |
| judul_kegiatan | VARCHAR | Kunci logis ke kelompok |
| user_nim | VARCHAR | Kunci logis ke user |
| file_path | VARCHAR | Path file |
| file_name | VARCHAR | Nama file |
| file_size | BIGINT | Ukuran byte |
| file_content | LONGTEXT | Konten (opsional) |
| file_mime_type | VARCHAR | MIME type |
| status | VARCHAR | Status verifikasi |
| catatan | TEXT | Catatan |

### 11. Laporan Akhir (`laporan_akhir`)

| Atribut | Tipe | (sama struktur dengan Proposal) |
|---------|------|-----------------------------------|

### 12. Luaran (`luaran`)

| Atribut | Tipe | Keterangan |
|---------|------|------------|
| **idLuaran** | BIGINT (PK) | Primary key |
| judul_kegiatan | VARCHAR | Kunci logis |
| user_nim | VARCHAR | Kunci logis |
| video_aftermovie | VARCHAR | Link video |
| artikel_link | VARCHAR | Link artikel |
| artikel_file_path | VARCHAR | Path file artikel |
| artikel_file_name | VARCHAR | Nama file artikel |
| file_path | VARCHAR | Path file tambahan |
| status | VARCHAR | Status verifikasi |
| catatan | TEXT | Catatan |

---

## Entitas Legacy (tidak dipusatkan di alur baru)

| Entitas | Tabel | Catatan |
|---------|-------|---------|
| Pendaftar KKN | `kkn_pendaftar` | Masih dibuat saat registrasi awal |
| Anggota Pendaftar | `kkn_anggota` | Anggota legacy |
| Nilai CPMK PDF | `nilai_cpmk` | Upload PDF tim penciri |

---

## File diagram

| File | Isi |
|------|-----|
| `docs/erd/erd-app-database.puml` | ERD Chen — DB aplikasi |
| `docs/erd/erd-dashboard-database.puml` | ERD Chen — DB dashboard |

---

*Diagram diselaraskan dengan migrasi Laravel per Mei 2026.*
