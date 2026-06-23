# Gawean — Dokumentasi Codebase Lengkap (Bahasa Indonesia)

> **Tema**: WordPress Hello Elementor Child Theme  
> **File**: `function.php` (8.717 baris — arsitektur satu file)  
> **Prefiks DB**: `wp9y_`  
> **Situs Live**: https://profesional-indonesia.com  
> **PHP**: 8.1.34  
> **Konsep**: Tanpa plugin, tanpa ketergantungan page builder — semua logika dalam satu file. Sistem user kustom, tabel kustom, auth kustom.

---

## Daftar Isi

1. [Gambaran Arsitektur](#1-gambaran-arsitektur)
2. [Memulai / Pengembangan](#2-memulai--pengembangan)
3. [Skema Database](#3-skema-database)
4. [Sistem User Kustom (Bukan WP Users)](#4-sistem-user-kustom-bukan-wp-users)
5. [Session & Alur Auth](#5-session--alur-auth)
6. [Sistem Registrasi](#6-sistem-registrasi)
7. [Referensi Shortcode](#7-referensi-shortcode)
8. [Endpoint AJAX](#8-endpoint-ajax)
9. [Panel Admin](#9-panel-admin)
10. [Dashboard Member](#10-dashboard-member)
11. [Sistem Draft Profil](#11-sistem-draft-profil)
12. [Sistem Portofolio (Foto & Video)](#12-sistem-portofolio-foto--video)
13. [Sistem Kategori (Many-to-Many)](#13-sistem-kategori-many-to-many)
14. [Sistem Domisili](#14-sistem-domisili)
15. [Sistem Tag](#15-sistem-tag)
16. [Halaman Frontend Publik](#16-halaman-frontend-publik)
17. [Alur Reset Password](#17-alur-reset-password)
18. [Sistem Artikel (WordPress Posts)](#18-sistem-artikel-wordpress-posts)
19. [Iklan Popup](#19-iklan-popup)
20. [Carousel](#20-carousel)
21. [Proxy API untuk Wilayah Indonesia](#21-proxy-api-untuk-wilayah-indonesia)
22. [Penanganan Upload Media](#22-penanganan-upload-media)
23. [Pola CSS / JS & Konvensi](#23-pola-css--js--konvensi)
24. [Referensi WordPress Hooks](#24-referensi-wordpress-hooks)
25. [Peta Fungsi Lengkap (Rentang Baris)](#25-peta-fungsi-lengkap-rentang-baris)
26. [Pertimbangan Keamanan](#26-pertimbangan-keamanan)
27. [Bug & Keanehan yang Diketahui](#27-bug--keanehan-yang-diketahui)

---

## 1. Gambaran Arsitektur

Ini adalah **child theme WordPress satu file** di mana semua logika kustom, styling, dan JavaScript berada di `function.php` (8.717 baris). Theme ini mewarisi Hello Elementor tetapi mengimplementasikan:

- **Sistem user/keanggotaan kustom sepenuhnya** dengan tabel database sendiri, otentikasi (session PHP), dan manajemen role
- Manajemen portofolio (upload foto + tautan video YouTube) dengan alur persetujuan admin
- Sistem kategori many-to-many untuk item portofolio
- Halaman direktori dan arsip publik
- Reset password via token email
- Sistem iklan popup
- Pengiriman artikel dari frontend
- Carousel menggunakan Swiper.js

**Keputusan Kunci**: Situs TIDAK menggunakan akun pengguna WordPress untuk member. Ia memiliki tabel `wp9y_personel` yang menyimpan semua data member. PHP `$_SESSION` digunakan untuk auth. "Shadow" WP user dibuat saat login hanya untuk mengaktifkan fitur upload media WordPress.

---

## 2. Memulai / Pengembangan

```bash
# Tidak perlu build step
# Edit function.php langsung
# Upload ke: wp-content/themes/gawean/

# Cek sintaks sebelum deploy
php -l function.php

# Tidak ada package.json, npm, atau composer
# Semua aset dimuat dari CDN atau di-inline
```

### Konvensi Pengembangan
- Semua CSS/JS di-inline melalui callback aksi `wp_footer`, `admin_head`, `admin_footer`
- Library CDN: Swiper.js, Select2, DataTables, jQuery
- Warna tema: `#d4af37` (emas), `#1a1a1a` (gelap), `#333` (border)
- Semua AJAX menggunakan `admin-ajax.php`
- Tidak ada file CSS/JS eksternal untuk kode kustom

---

## 3. Skema Database

Semua tabel dibuat otomatis melalui `portfolio_kategori_setup_db()` (baris 7946, dipanggil pada `init`).

### `wp9y_personel`
Tabel member utama.

| Kolom | Tipe | Deskripsi |
|---|---|---|
| `id` | BIGINT AUTO_INCREMENT PK | Primary key |
| `username` | VARCHAR(100) | Nama login unik (dari nama_panggilan) |
| `password` | VARCHAR(255) | Di-hash dengan `wp_hash_password()` |
| `email` | VARCHAR(100) | Unik, juga digunakan untuk login |
| `nama_lengkap` | VARCHAR(255) | Nama lengkap |
| `nama_panggilan` | VARCHAR(100) | Nama panggilan (hanya nama depan yang disimpan) |
| `kode_nama` | VARCHAR(50) | Format: `{NOMOR}-{KODE_POSISI}` mis. `0001-FDE` |
| `foto_profil` | VARCHAR(500) | URL foto profil |
| `cv_url` | VARCHAR(500) | URL CV PDF |
| `sertifikat_multiple` | LONGTEXT | Array JSON dari URL gambar sertifikat |
| `no_hp` | VARCHAR(50) | Nomor telepon |
| `tanggal_lahir` | DATE | Tanggal lahir |
| `domisili` | TEXT | Domisili (lihat Sistem Domisili) |
| `posisi` | VARCHAR(50) | Kode posisi dipisah koma (F,V,D,E,X,A,P) |
| `tag` | TEXT | Tag dipisah koma |
| `deskripsi` | TEXT | Deskripsi diri |
| `peralatan` | TEXT | Daftar peralatan |
| `pricelist_perhari` | VARCHAR(50) | `dibawah_1jt`, `1jt_3jt`, `diatas_3jt` |
| `pricelist` | LONGTEXT | Pricelist detail HTML (konten wp_editor) |
| `porto_links` | TEXT | Array JSON dari URL portofolio eksternal |
| `facebook` | VARCHAR(500) | URL media sosial |
| `instagram` | VARCHAR(500) | |
| `tiktok` | VARCHAR(500) | |
| `thread` | VARCHAR(500) | |
| `youtube` | VARCHAR(500) | |
| `status` | VARCHAR(20) | `pending`, `approved`, `non-aktif` |
| `rekomendasi` | VARCHAR(10) | `ya` atau `tidak` (flag rekomendasi) |
| `show_sosmed` | TINYINT(1) | Toggle visibilitas media sosial di profil publik |
| `reset_token` | VARCHAR(100) | Token reset password |
| `reset_expiry` | DATETIME | Waktu kadaluwarsa token |
| `created_at` | DATETIME | Otomatis diisi saat insert |

### `wp9y_personel_draft_edit`
Menyimpan edit profil yang tertunda menunggu persetujuan admin.

| Kolom | Tipe | Deskripsi |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | |
| `personel_id` | BIGINT UNIQUE | FK ke personel.id |
| `draft_data` | LONGTEXT | Objek JSON dari semua field yang diubah |
| `updated_at` | DATETIME | Timestamp update otomatis |

### `wp9y_portofolio`
Item portofolio foto.

| Kolom | Tipe | Deskripsi |
|---|---|---|
| `id` | BIGINT AUTO_INCREMENT PK | |
| `personel_id` | BIGINT | FK ke personel.id |
| `judul` | VARCHAR(255) | Judul |
| `foto_url` | VARCHAR(500) | URL file foto |
| `tanggal_kegiatan` | DATE | Tanggal kegiatan |
| `lokasi` | VARCHAR(255) | Lokasi |
| `tahun` | VARCHAR(10) | Tahun |
| `deskripsi` | TEXT | Deskripsi |
| `tags` | TEXT | Tag dipisah koma |
| `status` | VARCHAR(20) | `pending`, `approved`, `non-aktif` |
| `created_at` | DATETIME | |

### `wp9y_portofolio_video`
Item portofolio video (tautan YouTube saja — tanpa upload file).

| Kolom | Tipe | Deskripsi |
|---|---|---|
| `id` | BIGINT AUTO_INCREMENT PK | |
| `personel_id` | BIGINT | FK ke personel.id |
| `judul` | VARCHAR(255) | Judul |
| `video_url` | VARCHAR(500) | URL YouTube |
| `tanggal_kegiatan` | DATE | |
| `lokasi` | VARCHAR(255) | |
| `tahun` | VARCHAR(10) | |
| `deskripsi` | TEXT | |
| `tags` | TEXT | |
| `status` | VARCHAR(20) | |
| `created_at` | DATETIME | |

### `wp9y_kategori`
Kategori portofolio (11 baris, sudah diisi awal).

| Kolom | Tipe |
|---|---|
| `id` | INT AUTO_INCREMENT PK |
| `nama` | VARCHAR(100) UNIQUE |
| `slug` | VARCHAR(100) UNIQUE |
| `deskripsi` | TEXT |

Kategori yang sudah diisi:
1. Wedding & Personal
2. Corporate & Company
3. Event Documentation
4. Commercial & Advertising
5. Media & Entertainment
6. Film & Cinematic
7. Drone & Aerial
8. Outdoor, Travel & Nature
9. Photography Art & Style
10. Multimedia & Production Service
11. Lainnya

### `wp9y_portofolio_kategori_map`
Mapping many-to-many untuk foto.

| Kolom | Tipe |
|---|---|
| `portofolio_id` | BIGINT |
| `kategori_id` | INT |
| PK: `(portofolio_id, kategori_id)` | |

### `wp9y_portofolio_video_kategori_map`
Mapping many-to-many untuk video.

| Kolom | Tipe |
|---|---|
| `video_id` | BIGINT |
| `kategori_id` | INT |
| PK: `(video_id, kategori_id)` | |

### `wp9y_popup_ad`
Iklan popup satu baris.

| Kolom | Tipe |
|---|---|
| `id` | INT AUTO_INCREMENT PK |
| `image_url` | VARCHAR(500) |
| `link_url` | VARCHAR(500) |
| `is_active` | TINYINT(1) |
| `updated_at` | DATETIME |

---

## 4. Sistem User Kustom (Bukan WP Users)

### Konsep Inti
Situs memiliki tabel member sendiri (`wp9y_personel`) yang sepenuhnya terpisah dari pengguna WordPress. Member melakukan otentikasi melalui session PHP, bukan cookie WP.

### Kode Posisi
Disimpan di `wp9y_personel.posisi` sebagai kode yang dipisah koma:

| Kode | Label |
|---|---|
| F | Fotografer |
| V | Videografer |
| D | Drone |
| E | Editor |
| X | VFX |
| A | Animator |
| P | AI Artist - Prompt Engineer |

Fungsi mapping: `personel_posisi_label($code)` di baris 2141.

### Generate Kode Nama
Format: `{NOMOR}-{HURUF_POSISI}`

Contoh: `0001-FDE` berarti:
- `0001` = nomor urut (auto-increment, bukan dari DB — membaca kode_nama terakhir dan menambah)
- `FDE` = posisi Fotografer, Videografer, Editor

Logika generate (baris 938-960):
```php
$last_kode = $wpdb->get_var("SELECT kode_nama FROM wp9y_personel ORDER BY id DESC LIMIT 1");
// Ambil angka sebelum strip, increment
// Tambahkan kode posisi huruf kapital
```

### Alur Status
`pending` → (admin menyetujui) → `approved` → (admin toggle) → `non-aktif`

---

## 5. Session & Alur Auth

### Inisialisasi Session (baris 2148)
```php
add_action('init', 'personel_start_session', 1);
function personel_start_session() {
    if (!session_id()) session_start();
}
```
Priority 1 memastikan session dimulai sebelum handler init lainnya.

### Alur Login (baris 2183)
1. User mengirim form dengan username/email + password
2. Query `wp9y_personel` WHERE `(email = %s OR username = %s) AND status = 'approved'`
3. Jika ditemukan dan password cocok (`wp_check_password`):
   - Set `$_SESSION['personel_id']`, `$_SESSION['personel_nama']`, `$_SESSION['personel_email']`
   - **Pembuatan Shadow WP User**: Mencari atau membuat WP user dengan email yang sama, role `personel`
   - Memanggil `wp_set_auth_cookie()` untuk shadow user (mengaktifkan fitur upload media)
   - Redirect ke `/dashboard-personel`

### Alur Logout (baris 2160)
1. `$_GET['personel_action'] == 'logout'`
2. Menghancurkan session PHP
3. Memanggil `wp_logout()` untuk shadow WP user
4. Redirect ke `/login-personel`

### Fungsi Cek Auth (baris 2154)
```php
function is_personel_logged_in() {
    return isset($_SESSION['personel_id']);
}
```
Digunakan di seluruh sistem untuk mengontrol akses dashboard.

---

## 6. Sistem Registrasi

### Shortcode: `[personel_register]` (baris 275)

### Field Form
1. **Nama Lengkap** (wajib)
2. **Nama Panggilan** (wajib, satu kata, 3+ karakter, digunakan sebagai username)
3. **Email** (wajib, unik)
4. **Password** (wajib, 8+ karakter)
5. **Foto Profil** (opsional, JPG/PNG/WEBP, maks 2MB)
6. **No. HP** (opsional)
7. **Tanggal Lahir** (wajib)
8. **Domisili** (provinsi 1 wajib, provinsi 2 opsional — lihat Sistem Domisili)
9. **Posisi** (checkbox, minimal 1 wajib)
10. **Deskripsi Diri** (opsional)
11. **Link Portofolio Eksternal** (opsional, maks 5 URL)
12. **CV (PDF)** (wajib)
13. **Sertifikat** (opsional, banyak gambar)
14. **Peralatan** (opsional)
15. **Pricelist/hari** (wajib — dropdown)
16. **Pricelist Detail** (wp_editor HTML)
17. **Media Sosial** (opsional: Facebook, Instagram, TikTok, Thread, YouTube)
18. **Tags** (maks 10, input tag JS kustom)

### Handler Registrasi (baris 801)
1. Memvalidasi nonce
2. Menyertakan lib penanganan file WordPress
3. Upload CV (PDF) dan sertifikat (banyak gambar) terlebih dahulu
4. Memvalidasi semua field
5. Generate `kode_nama` dari record terakhir + kode posisi
6. Upload foto profil dengan validasi kustom (cek ekstensi + ukuran)
7. Insert ke `wp9y_personel`
8. Redirect ke halaman thank-you dengan kode_nama

### Shadow WP User
Tidak seperti login, registrasi TIDAK membuat WP user secara otomatis. WP user dibuat hanya saat login pertama.

### Validasi Client-Side
- Nama panggilan: cek AJAX on blur untuk ketersediaan
- Posisi: minimal 1 checkbox harus dicentang
- Tags: input tag JS kustom dengan batas maks 10
- Link portofolio: maks 5 URL dengan UI clone/remove

### Pola Error Registrasi
Redirect kembali ke form dengan `?pesan=...&success=0`. Menggunakan `urldecode()` untuk menampilkan pesan.

---

## 7. Referensi Shortcode

| Shortcode | Fungsi | Baris | Tujuan |
|---|---|---|---|
| `[personel_register]` | `personel_register_form()` | 275 | Form registrasi lengkap |
| `[form_login_personel]` | `personel_login_form_shortcode()` | 2183 | Form login dengan modal lupa password |
| `[dashboard_personel]` | `personel_dashboard_shortcode()` | 2477 | Dashboard member (multi-tab) |
| `[list_personel_publik]` | `render_list_personel_publik()` | 5062 | Direktori personel publik dengan filter |
| `[detail_personel_luxury]` | `render_detail_personel_shortcode()` | 5366 | Halaman profil personel tunggal |
| `[arsip_foto_publik]` | `shortcode_arsip_foto()` | 5833 | Galeri arsip foto publik |
| `[arsip_video_publik]` | `shortcode_arsip_video_fixed()` | 6163 | Galeri arsip video publik |
| `[switch_porto_button]` | `render_switch_porto_button()` | 6523 | Tombol toggle tab Foto/Video |
| `[carousel_event_terbaru]` | `render_carousel_event_terbaru()` | 6602 | Carousel posting WordPress (kategori: kebutuhan event) |
| `[carousel_home_porto]` | `render_carousel_home_porto_split()` | 6990 | Carousel portofolio (type=foto atau type=video) |
| `[personel_thankyou]` | `personel_thankyou_display()` | 1035 | Halaman sukses pasca-registrasi |
| `[personel_reset_form]` | `personel_reset_password_form_shortcode()` | 7467 | Form reset password (token + email di URL) |

---

## 8. Endpoint AJAX

Semua menggunakan hook `wp_ajax_` dan `wp_ajax_nopriv_` pada `admin-ajax.php`.

| Aksi | Auth | Handler | Baris | Tujuan |
|---|---|---|---|---|
| `fetch_wilayah` | tidak ada | `proxy_fetch_wilayah()` | 1610 | Proxy untuk API provinsi/kota Indonesia |
| `toggle_status_personel` | admin | `lx_toggle_status_personel_handler()` | 1579 | Menyetujui/menolak personel |
| `toggle_porto_status` | admin | `lx_toggle_porto_status_handler()` | 4430 | Menyetujui/menolak item portofolio (foto & video) |
| `load_more_porto` | tidak ada | `handle_ajax_load_more()` | 5870 | Infinite scroll untuk arsip foto |
| `load_more_porto_video` | tidak ada | `ajax_video_handler_fixed()` | 6196 | Infinite scroll untuk arsip video |
| `update_rekomendasi` | admin | `lx_handle_update_rekomendasi()` | 6819 | Toggle flag rekomendasi |
| `update_show_sosmed` | admin | `lx_handle_update_show_sosmed()` | 6855 | Toggle visibilitas media sosial |

### `fetch_wilayah` (Proxy API Emsifa)
- `type=provinces` → mengambil `https://emsifa.github.io/api-wilayah-indonesia/api/provinces.json`
- `type=regencies&prov_id=N` → mengambil `https://emsifa.github.io/api-wilayah-indonesia/api/regencies/{N}.json`
- Mengembalikan respons JSON mentah
- Terdaftar untuk pengguna admin dan non-admin

---

## 9. Panel Admin

### Struktur Menu
- **Personel** (level atas, `personel-admin`) - icon groups, posisi 30
  - **Portofolio Foto** (sub-menu, `personel-porto`)
  - **Portofolio Video** (sub-menu, `personel-video`)
- **Popup Iklan** (level atas, `popup-iklan`) - posisi 80

### 9.1 Halaman Admin Personel (`personel_admin_page`, baris 1224)

**Kolom Tabel**: Kode, Foto, Nama, Email, Status, Tanggal, Rekomendasi (toggle), Show Sosmed (toggle), Aksi

**Fitur**:
- DataTable dengan locale Indonesia
- Pencarian & pagination
- **Badge Status**: Approved (hijau), Pending (kuning)
- **Badge Pending Edit**: Menampilkan "Pending Edit" oranye jika draft ada untuk personel tersebut
- **AJAX toggle**: Satu klik approve/non-aktif (tombol hijau/merah)
- **Toggle Rekomendasi**: Hijau=YA, Merah=TIDAK
- **Toggle Show Sosmed**: Hijau=YA (terlihat), Merah=TIDAK
- **Aksi batch**: Approve melalui submit form
- **Hapus kaskade**: Menghapus personel, foto portofolio (dengan penghapusan file), video portofolio, dan edit draft
- **Lihat detail**: `?page=personel-admin&view=ID`

### 9.2 Tampilan Detail Admin (`personel_view_detail`, baris 1737)

Menampilkan info personel komprehensif:
- Avatar + nama + kode + status
- Info kontak (email, telepon)
- Detail profil (DOB dengan kalkulasi usia, domisili, posisi dengan label)
- Tautan portofolio eksternal (dari JSON)
- Tombol download CV
- Grid sertifikat (gambar)
- Peralatan, pricelist, media sosial, tag

**Bagian Review Draft** (jika draft ada):
- Tabel diff side-by-side membandingkan nilai saat ini vs perubahan yang diusulkan
- Perbandingan file: foto profil, CV, sertifikat ditampilkan sebagai preview visual
- Perubahan posisi dipetakan ke label
- Indikator perubahan password (tapi tidak pernah ditampilkan dalam teks biasa)
- Tombol **Setujui** (hijau) — menerapkan draft, menghapus file lama
- Tombol **Tolak** (merah) — menghapus file draft, menghapus draft

### 9.3 Halaman Admin Foto (`personel_porto_admin_page`, baris 4284)

Tabel: Foto (thumbnail), Judul, Personel, Detail (lokasi+tahun), Deskripsi & Tags, Status, Aksi

Fitur:
- DataTable dengan pencarian
- AJAX toggle approved/non-aktif
- Tombol approve untuk item pending
- Hapus dengan pembersihan file

### 9.4 Halaman Admin Video (`personel_video_admin_page`, baris 4919)

Struktur yang sama dengan admin Foto tetapi untuk video:
- Preview Video (iframe embed + tautan asli)
- AJAX toggle, approve, delete

### 9.5 Admin Popup Iklan (`popup_ad_admin_page`, baris 7599)

- Halaman pengaturan di bawah menu Popup Iklan
- Field: Image URL (dengan media uploader), Link URL, checkbox Active
- Preview langsung dari gambar yang diupload

---

## 10. Dashboard Member

### Shortcode: `[dashboard_personel]` (baris 2477)

### Pemeriksaan Akses
- Harus login (`is_personel_logged_in()`)
- Menampilkan "Silahkan login terlebih dahulu" jika tidak terotentikasi

### Struktur Tab
Navigasi melalui parameter query `?tab=`:

| Tab | Key | Fungsi | Deskripsi |
|---|---|---|---|
| Dashboard | `dashboard` | `render_personel_home()` | Kartu sambutan, statistik (jumlah foto/video/artikel) |
| Edit Profil | `edit-profil` | `render_personel_edit_profil()` | Form edit profil lengkap (disimpan sebagai draft) |
| Portofolio Foto (daftar) | `foto` | `render_list_portofolio_foto()` | Grid foto user dengan edit/hapus |
| + Unggah Foto | `foto&action=add` | `render_tab_portofolio_foto()` | Form upload foto |
| Edit Foto | `foto&action=edit&id=N` | `render_tab_edit_portofolio()` | Edit foto yang ada |
| Portofolio Video (daftar) | `video` | `render_list_portofolio_video()` | Grid video user |
| + Unggah Video | `video&action=add` | `render_tab_form_video()` | Form URL video |
| Edit Video | `video&action=edit&id=N` | `render_tab_form_video()` | Edit video yang ada |
| Artikel | `artikel` | `render_tab_artikel_personel()` | Form pembuatan artikel (posting WordPress) |

### Item Sidebar Menu
- Dashboard, Edit Profil, Portofolio Foto, Portofolio Video, Artikel, Logout
- Tab aktif disorot dengan kelas CSS

### Kuota Portofolio
- Foto: maks 20 (diperiksa melalui `get_status_kuota_personel()` di baris 6796)
- Video: maks 8
- Tombol dinonaktifkan dengan peringatan saat kuota tercapai

### Handler Upload Portofolio (baris 2651)
- Nonce diverifikasi (`add_portofolio`)
- Upload file dengan whitelist tipe MIME (jpg, png, webp)
- Validasi kustom melalui `$overrides['mimes']`
- Insert ke `wp9y_portofolio`, lalu insert mapping kategori
- Sukses/gagal ditampilkan melalui modal popup kustom

### Handler Edit Portofolio (baris 2545)
- Nonce diverifikasi (`edit_portofolio`)
- Dapat mengganti file foto
- Mereset status ke `pending` (memerlukan persetujuan ulang)
- Membangun ulang mapping kategori

### Handler Hapus Portofolio (baris 2608)
- Memverifikasi kepemilikan (WHERE id = %d AND personel_id = %d)
- Menghapus file fisik dari server
- Menghapus dari `wp9y_portofolio_kategori_map` dan `wp9y_portofolio`

### CRUD Video (baris 2488)
- Pola yang sama dengan foto tetapi menggunakan `wp9y_portofolio_video`
- Tanpa upload file — hanya menerima URL YouTube
- `get_video_embed_url()` (baris 4559) mengekstrak ID YouTube dari berbagai format URL

### Dashboard Home (baris 3543)
- Pesan sambutan dengan nama personel
- Indikator status akun (Approved: hijau, Non-aktif: merah, Pending: kuning)
- Kartu statistik: jumlah foto, jumlah video, jumlah artikel

---

## 11. Sistem Draft Profil

### Cara Kerja
Ketika member mengedit profilnya, perubahan TIDAK diterapkan langsung ke `wp9y_personel`. Sebaliknya, perubahan disimpan ke `wp9y_personel_draft_edit` sebagai blob JSON.

### Alur Lengkap

#### 1. User Mengedit Profil
Form di `render_personel_edit_profil()` (baris 3622):
- Mengisi awal semua field dari data `wp9y_personel` saat ini
- Menampilkan banner notifikasi emas jika draft yang tertunda sudah ada
- Mem-parsing domisili (format baru dan lama) untuk pengisian awal

#### 2. Simpan sebagai Draft (baris 2732)
- Nonce diverifikasi (`update_profile`)
- Membangun array `$draft_fields` dengan SEMUA field (teks + URL file)
- Jika field password diisi, itu disertakan (selalu di-hash)
- Upload file terjadi segera (file disimpan di server)
- **Pembersihan file yatim**: Jika draft lama ada, file-file-nya diperiksa — jika tidak digunakan oleh draft saat ini atau profil aktif, file tersebut dihapus
- Menggunakan `$wpdb->replace()` untuk upsert draft (satu draft per personel_id)

#### 3. Review Admin (di tampilan detail, baris 1755)
- Membandingkan saat ini vs draft field-by-field
- Menangani tipe khusus: gambar (foto_profil, sertifikat_multiple), file (cv_url), posisi (mapping label), tautan (perbandingan JSON)
- Menampilkan tabel diff yang bersih

#### 4a. Setujui (baris 1265)
- Menghapus file lama yang digantikan oleh file draft baru
- Memperbarui `wp9y_personel` dengan semua field draft
- Menghapus baris draft

#### 4b. Tolak (baris 1305)
- Menghapus file baru yang diupload di draft
- Menghapus baris draft
- Data personel utama tidak berubah

### Aturan Pembersihan File
- **Saat setujui hapus**: Foto_profil / cv_url lama (hanya jika berbeda dari draft)
- **Saat setujui hapus**: Gambar sertifikat lama (hanya yang TIDAK ada di draft baru)
- **Saat tolak hapus**: File foto_profil / cv_url / sertifikat draft baru
- **Saat timpa draft**: File draft lama yang tidak direferensikan oleh draft baru atau profil aktif

---

## 12. Sistem Portofolio (Foto & Video)

### Portofolio Foto
- File diupload ke `/wp-content/uploads/` melalui `wp_handle_upload()`
- Validasi MIME: `image/jpeg`, `image/png`, `image/webp`
- Maks 20 per personel
- Penyimpanan: `wp9y_portofolio.foto_url`

### Portofolio Video
- URL YouTube saja (tanpa upload file)
- `get_video_embed_url()` mengekstrak ID YouTube:
  - Mencocokkan pola `youtube.com/watch?v=ID` dan `youtu.be/ID`
  - Mengembalikan `https://www.youtube.com/embed/ID`
- Maks 8 per personel
- Penyimpanan: `wp9y_portofolio_video.video_url`
- Thumbnail: `https://img.youtube.com/vi/{ID}/mqdefault.jpg`

### Field Umum Item Portofolio
- `judul`, `tanggal_kegiatan`, `lokasi`, `tahun`, `deskripsi`, `tags`
- Alur status: `pending` → `approved` / `non-aktif`
- Mapping kategori (many-to-many melalui tabel mapping)
- Tags: dipisah koma, maks 10

---

## 13. Sistem Kategori (Many-to-Many)

### Tabel Database
- `wp9y_kategori` — 11 kategori sudah diisi awal
- `wp9y_portofolio_kategori_map` — mapping foto
- `wp9y_portofolio_video_kategori_map` — mapping video

### UI Seleksi Kategori (`render_portfolio_category_selection`, baris 8065)
Digunakan di form upload/edit di dashboard.
- Layout CSS `display: table` dengan 3 kolom:
  - **Kolom 1** (36px): checkbox
  - **Kolom 2** (200px): nama kategori dengan pemisah `border-right`
  - **Kolom 3**: deskripsi dengan `word-break: break-word`
- Maks 3 pilihan diterapkan client-side (JS menonaktifkan checkbox yang tersisa saat mencapai 3)
- Item yang dipilih disorot dengan border emas
- Mobile (≤640px): dikonversi ke layout block, deskripsi di-indent

### Filter Bar Kategori (`render_portfolio_category_filter_bar`, baris 8227)
Digunakan di halaman arsip publik (foto/video).
- Nav bar horizontal yang dapat di-scroll dari tombol kategori (bentuk pill)
- Kategori aktif disorot dengan emas
- Input tersembunyi menyimpan slug yang dipilih
- **Kotak Detail/Penjelasan**: Ketika kategori diklik, panel meluncur turun menampilkan:
  - Judul kategori + deskripsi
  - Grid sub-field (misalnya, untuk "Wedding & Personal": Wedding, Prewedding, Personal & Family dengan contoh item)
  - Semua data sub-field di-hardcode dalam objek JS `categoriesData` di script
- Mengklik "Semua Kategori" menyembunyikan kotak detail
- Memicu reload grid AJAX untuk hasil yang difilter

### Data Kategori di JS
Objek `categoriesData` (baris 8425-8652) adalah objek JS besar yang berisi data terstruktur untuk setiap kategori dengan sub-grup dan item contoh. Ini digunakan murni untuk UI kotak penjelasan.

---

## 14. Sistem Domisili

### Format Penyimpanan
Disimpan di `wp9y_personel.domisili` sebagai string teks:

| Format | Contoh |
|---|---|
| Provinsi tunggal | `Jawa Barat - Bandung, Bekasi` |
| Multi-provinsi | `Jawa Barat - Bandung, Bekasi \|\| Jawa Timur - Surabaya` |
| Legacy | `Bandung, Jawa Barat` |

### Registrasi
- Provinsi 1 ditampilkan secara default (wajib)
- Provinsi 2 di-toggle melalui tombol "+ Tambah Provinsi Lain"
- Setiap select provinsi menggunakan Select2 dengan maks 3 pilihan kota (via `maximumSelectionLength: 3`)
- Proxy API Emsifa mengambil provinsi dan kota
- Format yang disimpan: `{NamaProv} - {Kota1}, {Kota2}` atau `{Prov1} ... \|\| {Prov2} ...`

### Parsing Edit Profil
- Memeriksa pemisah `||` → membagi menjadi 2 blok provinsi
- Setiap blok: dipisah oleh ` - ` → provinsi + daftar kota
- Jika tidak ada ` - ` tetapi ada `, ` → format legacy
- Memilih awal provinsi/kota yang tersimpan saat memuat form edit
- Menggunakan fungsi JS `initProvKotaEdit()` yang memuat data provinsi lalu memuat kota dengan pre-selection

### Filtering Tampilan Publik
- Query filter: `LIKE '%{Provinsi} - %'` menangkap provinsi tunggal dan multi
- Dikombinasikan dengan `OR LIKE '%{kota}%'` untuk pencarian kota
- Dropdown provinsi di direktori publik menggunakan Select2 + proxy Emsifa

---

## 15. Sistem Tag

### Implementasi
Sistem input tag berbasis JS kustom (bukan library):
- User mengetik teks tag, tekan Enter/koma/spasi untuk menambah
- Tag ditampilkan sebagai pill yang dapat dihapus dengan prefiks `#`
- Maks 10 tag diterapkan
- Deduplikasi
- Sanitasi huruf kecil + alfanumerik

### Penyimpanan
- String dipisah koma di `wp9y_personel.tag` (tag profil)
- Format yang sama di `wp9y_portofolio.tags` dan `wp9y_portofolio_video.tags` (tag portofolio)

### Detail Implementasi JS
Digunakan di beberapa tempat dengan kode yang sedikit bervariasi:
- **Form registrasi** (baris 552): Pola `addTag`/`removeTag` yang dapat digunakan kembali, disimpan di global `window.removeTag`
- **Edit profil** (baris 4129): Memuat tag yang ada dari input tersembunyi
- **Upload/Edit foto** (baris 3463): Pola yang sama, dengan backspace-to-remove
- **Upload/Edit video** (baris 4639): Instance terpisah dengan ID `tagInputVideo`
- **Edit portofolio** (baris 3133): Memuat tag yang ada

---

## 16. Halaman Frontend Publik

### 16.1 Direktori Personel (`[list_personel_publik]`, baris 5062)

**URL**: Halaman dengan shortcode, filter melalui `?p_search=&p_posisi=&p_price=&p_provinsi=&p_kota=`

**Logika Query**:
- Hanya record `status = 'approved'`
- Sub-query untuk menghitung foto dan video yang disetujui per personel
- Pencarian di: `nama_panggilan`, `domisili`, `peralatan`, `deskripsi`, `tag`, `pricelist`, `kode_nama`
- Filter posisi: `LIKE '%{code}%'`
- Filter harga: pencocokan tepat pada `pricelist_perhari`
- Filter provinsi: `LIKE '%{provinsi} - %'`
- Filter kota: `LIKE '%{kota}%'`

**Pengurutan**: Direkomendasikan (`rekomendasi = 'ya'`) lebih dulu, kemudian terbaru (`ORDER BY CASE WHEN ... THEN 0 ELSE 1 END ASC, p.id DESC`)

**Tampilan Kartu**:
- Foto profil (dengan placeholder jika tidak ada)
- Nama tampilan sebagai `{firstName}-{kode_nama}`
- Tag posisi (dipetakan ke label lengkap)
- Domisili + usia
- Jumlah total karya
- Label harga (overlay pojok kanan atas)
- Tombol "LIHAT PROFIL" → `/detail-personel/?kode={kode_nama}`

**UI Filter**:
- Input teks pencarian
- Dropdown posisi (semua kode)
- Dropdown rentang harga
- Select provinsi (Select2, dimuat melalui proxy Emsifa)
- Select kota (Select2, tergantung provinsi)
- Semua terbungkus dalam kotak filter bertema emas

### 16.2 Detail Profil (`[detail_personel_luxury]`, baris 5366)

**URL**: `/detail-personel/?kode={kode_nama}`

**Layout**: Dua kolom (35% kiri / 65% kanan) di desktop, bertumpuk di mobile.

**Kolom Kiri**:
- Foto profil dalam container berbingkai
- Pricelist (HTML, dirender melalui `wpautop(wp_kses_post(...))`)
- Bagian media sosial (ditampilkan secara kondisional berdasarkan flag `show_sosmed`)
  - WhatsApp/nomor telepon
  - Tautan Facebook, Instagram, TikTok, Thread, YouTube

**Kolom Kanan**:
- Nama tampilan sebagai `{firstName}-{kode_nama}`
- Meta: Usia, Domisili, Label Posisi
- Bio/deskripsi
- Tag
- Grid sertifikat
- Daftar peralatan

**Bagian Portofolio** (di bawah container flex utama):
- **Portofolio Video**: Grid thumbnail YouTube dengan overlay tombol play, klik membuka modal
- **Portofolio Foto**: Grid foto persegi dengan judul overlay, klik membuka modal

**Modal Portofolio** (digunakan bersama untuk kedua tipe):
- Overlay fade-in dengan backdrop blur
- Media: iframe untuk video, img untuk foto
- Panel info: judul, baris meta (tautan penulis, lokasi, tahun, tanggal), deskripsi
- Tutup melalui tombol X, klik overlay, atau ESC

### 16.3 Arsip Foto (`[arsip_foto_publik]`, baris 5833)

**URL**: Halaman dengan shortcode

**Fitur**:
- Filter bar kategori (pill horizontal dengan kotak detail)
- Input pencarian (nama, lokasi, tag)
- Dropdown sort: Terbaru/Terlama posting, Terbaru/Terlama tanggal kegiatan
- Grid: 4 kolom desktop, 2 tablet, 1 mobile
- Infinite scroll melalui tombol "MUAT LEBIH BANYAK" (12 item per muatan)
- Klik membuka modal dengan gambar penuh + metadata

**AJAX Handler** (`handle_ajax_load_more`, baris 5870):
- Menerima: `type`, `offset`, `search`, `tanggal`, `tahun`, `sort`, `category`
- Filter kategori melalui JOIN pada tabel mapping dengan WHERE `k.slug = %s`
- Mengembalikan HTML yang dirender untuk setiap item

### 16.4 Arsip Video (`[arsip_video_publik]`, baris 6163)

Struktur yang sama dengan arsip foto tetapi untuk video:
- `render_video_item_html()` di baris 6123
- AJAX handler di baris 6196
- Thumbnail YouTube sebagai preview
- Overlay tombol play
- Modal terbuka dengan embed iframe

### 16.5 Tombol Switch (`[switch_porto_button]`, baris 6523)

Toggle antara halaman foto/video. Memeriksa URL saat ini untuk path `portofolio-video`, menampilkan status aktif yang sesuai. Dua tombol pill berdampingan dalam satu container.

---

## 17. Alur Reset Password

### Langkah 1: Lupa Password (tertanam di form login, baris 2414)
- Modal popup meminta email
- Mengirim POST ke `handle_personel_password_reset()` (baris 7365)

### Langkah 2: Kirim Email (baris 7369)
- Mencari email di `wp9y_personel`
- Menghasilkan token `bin2hex(random_bytes(20))`
- Menyimpan `reset_token` + `reset_expiry` (1 jam) di baris personel
- Mengirim email melalui `wp_mail()` dengan tautan: `/reset-password-personel/?token={token}&email={email}`

### Langkah 3: Form Reset (`[personel_reset_form]`, baris 7467)
- Membaca `token` dan `email` dari parameter GET
- Memvalidasi token dan kadaluwarsa terhadap database
- Menampilkan form password baru
- Saat submit, memperbarui password `wp9y_personel` DAN shadow WP user

### Detail Penting
- Menggunakan `wp_hash_password()` (bukan `md5` atau teks biasa)
- Token kadaluwarsa setelah 1 jam (`current_time('mysql') + 1 hour`)
- Memiliki debug output (baris 7484-7495) yang mengungkapkan token/expiry saat validasi gagal — hapus di production

---

## 18. Sistem Artikel (WordPress Posts)

### Handler: `lx_handle_artikel_personel()` (baris 7168)

- Dipicu oleh POST `submit_artikel`
- Membuat WordPress `post` dengan:
  - Kategori "Artikel" (dibuat otomatis jika belum ada)
  - Status `pending` (memerlukan persetujuan admin)
  - Featured image melalui `media_handle_upload()`
  - Meta `_personel_author_id` menyimpan session personel_id

### Form: `render_tab_artikel_personel()` (baris 7240)
- Input judul
- Upload gambar sampul (wajib)
- Kategori ditampilkan sebagai "Artikel" yang dinonaktifkan
- Konten melalui `wp_editor()` (TinyMCE dengan tombol Add Media diaktifkan)
- Submit dikirim untuk review admin

---

## 19. Iklan Popup

### Tabel: `wp9y_popup_ad` (dibuat otomatis, satu baris)

### Pengaturan Admin (baris 7599)
- Field Image URL dengan media uploader WordPress
- Link URL (opsional)
- Checkbox Active/inactive

### Rendering Frontend (`popup_ad_render_frontend`, baris 7737)
- Dipasang ke `wp_footer` pada priority 9999
- Guard: bukan admin, bukan AJAX, tabel ada, is_active=1, image_url tidak kosong
- **Berbasis session-storage**: Ditampilkan sekali per sesi browser (`sessionStorage.getItem('popup_iklan_closed')`)
- Format gambar portrait (max-width 380px, 85vw di mobile)
- Tombol tutup (✕ lingkaran di kanan atas)
- Animasi fade-in halus dengan scale (delay 500ms)
- Tutup pada: klik tombol, klik overlay, tombol ESC
- Tautan opsional membungkus gambar

---

## 20. Carousel

### 20.1 Carousel Event (`[carousel_event_terbaru]`, baris 6602)

- Menggunakan Swiper.js (CDN)
- Query posting WordPress di kategori "kebutuhan event", 10 terbaru
- 1 slide mobile, 2 slide tablet+
- Setiap slide: kartu thumbnail dengan badge "KEBUTUHAN EVENT", judul (20px bold putih), tanggal
- Auto-play tidak diaktifkan

### 20.2 Carousel Portofolio Home (`[carousel_home_porto]`, baris 6997)

- Atribut shortcode: `type=foto` atau `type=video`
- 10 item terbaru yang disetujui
- Ukuran kartu tetap: 258px × 154px
- Menggunakan `slidesPerView: "auto"` untuk menghormati CSS width
- Swiper dengan loop diaktifkan
- Panah navigasi (emas)
- Klik membuka modal portofolio
- Overlay: gradien bawah dengan judul emas + tautan penulis

---

## 21. Proxy API untuk Wilayah Indonesia

### Masalah
Frontend membutuhkan dropdown provinsi/kota Indonesia, tetapi API Emsifa memblokir permintaan CORS.

### Solusi
Proxy AJAX WordPress kustom di `proxy_fetch_wilayah()` (baris 1610).

**Endpoint yang diproxy**:
- `type=provinces` → `https://emsifa.github.io/api-wilayah-indonesia/api/provinces.json`
- `type=regencies&prov_id=N` → `https://emsifa.github.io/api-wilayah-indonesia/api/regencies/{N}.json`

**Penggunaan**:
```js
$.getJSON(ajaxurl, { action: 'fetch_wilayah', type: 'provinces' })
$.getJSON(ajaxurl, { action: 'fetch_wilayah', type: 'regencies', prov_id: 12 })
```

**Keamanan**: Hanya proxy pass-through, tanpa caching, tanpa validasi data respons selain memeriksa bahwa tidak kosong/error.

---

## 22. Penanganan Upload Media

### Foto Profil
- Registrasi: Validasi kustom dengan cek ekstensi file + ukuran (batas 2MB)
- Edit: `wp_handle_upload()` dengan whitelist tipe MIME (`image/jpeg`, `image/png`, `image/webp`)
- Disimpan sebagai string URL

### CV
- PDF saja
- `wp_handle_upload()` dengan pengaturan default
- Disimpan sebagai string URL

### Sertifikat
- Banyak gambar (JPG/PNG/WEBP)
- Loop melalui array `$_FILES['sertifikat_files']`
- Setiap file diupload secara individual
- Disimpan sebagai array JSON dari URL

### Foto Portofolio
- `wp_handle_upload()` dengan override MIME (batasi ke jpg/png/webp)
- Pesan error kustom untuk format tidak valid
- Pesan upload sukses melalui modal kustom

### Video Portofolio
- Tanpa upload file — input teks URL YouTube saja
- `get_video_embed_url()` menormalisasi URL YouTube ke format embed

### Batas Ukuran Upload
- Filter WordPress `upload_size_limit` mengatur 64MB untuk semua user
- Filter kustom `izinkan_personel_upload_limit()` (baris 7340) mengizinkan 10MB untuk role `personel`

---

## 23. Pola CSS / JS & Konvensi

### Modal Popup Kustom (Menggantikan `alert()`)
Semua panggilan `alert()` diganti dengan modal CSS yang dihasilkan secara dinamis menggunakan pola IIFE yang konsisten:
```js
(function(){
    var d=document.createElement('div');
    d.style.cssText='position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:999999;display:flex;align-items:center;justify-content:center;';
    d.innerHTML='<div style="background:#1a1a1a;border:1px solid #d4af37;border-radius:8px;padding:30px 40px;max-width:400px;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,0.5);">...</div>';
    document.body.appendChild(d);
})();
```
- Border emas (1px solid #d4af37) = sukses
- Border merah (1px solid #ff4444) = error

### Konfigurasi Select2
- Tema gelap sesuai estetika situs
- Select provinsi: `minimumResultsForSearch: -1` untuk menekan pencarian
- Multi-select kota: `maximumSelectionLength: 3`
- Input pencarian disembunyikan secara visual ketika tidak ada tag yang dipilih (via CSS opacity/width, bukan display:none — perlu dapat difokus)
- `.form-row`, `.form-group.full` diatur ke `overflow: visible` untuk mencegah terpotongnya dropdown Select2

### DataTables
- CDN: `cdn.datatables.net/1.13.6/`
- Paket bahasa Indonesia
- Styling tema gelap kustom
- Dimuat hanya di halaman admin personel (tidak global)

### Swiper.js
- CDN: `cdn.jsdelivr.net/npm/swiper@11/`
- Digunakan di 3 instance carousel
- Setiap instance memiliki script inisialisasi sendiri

### Sistem Tag Input
- Implementasi kustom (bukan library)
- Pola: input tersembunyi menyimpan nilai dipisah koma, tag visual dirender di wrapper, input field ditambahkan setelah tag terakhir
- Events: keydown (enter/koma/backspace), blur, click (hapus)

---

## 24. Referensi WordPress Hooks

### Actions

| Hook | Callback | Priority | Baris |
|---|---|---|---|
| `after_setup_theme` | `hello_elementor_setup()` | default | 102 |
| `after_setup_theme` | `hello_elementor_content_width()` | 0 | 191 |
| `wp_enqueue_scripts` | `hello_elementor_scripts_styles()` | default | 163 |
| `elementor/theme/register_locations` | `hello_elementor_register_elementor_locations()` | default | 179 |
| `wp_head` | `hello_elementor_add_description_meta_tag()` | default | 216 |
| `init` | `hello_elementor_customizer()` | default | 238 |
| `init` | `handle_personel_register()` | default | 800 |
| `init` | `personel_start_session()` | **1** | 2147 |
| `init` | `personel_logout_handler()` | default | 2159 |
| `init` | `lx_handle_artikel_personel()` | default | 7167 |
| `init` | `handle_personel_password_reset()` | default | 7465 |
| `init` | `portfolio_kategori_setup_db()` | default | 8060 |
| `admin_menu` | `personel_admin_menu()` | default | 1167 |
| `admin_menu` | `personel_admin_menu_porto()` | default | 1180 |
| `admin_menu` | `personel_admin_menu_video()` | default | 1191 |
| `admin_menu` | `popup_ad_admin_menu()` | default | 7583 |
| `admin_enqueue_scripts` | `personel_enqueue_datatables()` | default | 1202 |
| `admin_enqueue_scripts` | `popup_ad_admin_enqueue()` | default | 7594 |
| `admin_head` | `lx_status_personel_assets()` | default | 1637 |
| `admin_footer` | `lx_porto_status_scripts()` | default | 4460 |
| `admin_footer` | `lx_rekomendasi_custom_assets()` | default | 6881 |
| `admin_init` | `popup_ad_create_table()` | default | 7567 |
| `wp_footer` | `lx_porto_foto_assets()` | default | 5923 |
| `wp_footer` | `lx_universal_porto_assets()` | default | 6255 |
| `wp_footer` | anonim (video JS inline) | default | 6385 |
| `wp_footer` | `force_premium_menu_new_tab_specific()` | default | 6778 |
| `wp_footer` | `popup_ad_render_frontend()` | **9999** | 7937 |

### AJAX Actions

| Hook | Callback | Baris |
|---|---|---|
| `wp_ajax_toggle_status_personel` | `lx_toggle_status_personel_handler()` | 1578 |
| `wp_ajax_fetch_wilayah` | `proxy_fetch_wilayah()` | 1608 |
| `wp_ajax_nopriv_fetch_wilayah` | `proxy_fetch_wilayah()` | 1609 |
| `wp_ajax_toggle_porto_status` | `lx_toggle_porto_status_handler()` | 4429 |
| `wp_ajax_load_more_porto` | `handle_ajax_load_more()` | 5868 |
| `wp_ajax_nopriv_load_more_porto` | `handle_ajax_load_more()` | 5869 |
| `wp_ajax_load_more_porto_video` | `ajax_video_handler_fixed()` | 6193 |
| `wp_ajax_nopriv_load_more_porto_video` | `ajax_video_handler_fixed()` | 6194 |
| `wp_ajax_update_rekomendasi` | `lx_handle_update_rekomendasi()` | 6818 |
| `wp_ajax_update_show_sosmed` | `lx_handle_update_show_sosmed()` | 6854 |

### Filters

| Hook | Callback | Priority | Baris |
|---|---|---|---|
| `hello_elementor_page_title` | `hello_elementor_check_hide_title()` | default | 258 |
| `wp_get_nav_menu_items` | `custom_personel_menu_filter()` | 20 | 4247 |
| `upload_size_limit` | closure anonim (64MB) | default | — |
| `upload_size_limit` | `izinkan_personel_upload_limit()` | default | 7340 |
| `show_admin_bar` | `sembunyikan_admin_bar_untuk_personel()` | default | 7348 |

---

## 25. Peta Fungsi Lengkap (Rentang Baris)

| Baris | Bagian | Fungsi |
|---|---|---|
| 1-273 | Hello Elementor Theme Boilerplate | `hello_elementor_setup()`, `hello_maybe_update_theme_version_in_db()`, `hello_elementor_scripts_styles()`, `hello_elementor_register_elementor_locations()`, `hello_elementor_content_width()`, `hello_elementor_add_description_meta_tag()`, `hello_elementor_check_hide_title()`, `hello_elementor_body_open()` |
| 274-536 | HTML Form Registrasi | `personel_register_form()` — form HTML lengkap + CSS inline + JS (inisialisasi Select2, sistem tag, tautan porto, checkbox posisi, cek ketersediaan nama panggilan AJAX) |
| 537-644 | JS Inline Registrasi | jQuery untuk tautan porto, styling Select2 |
| 645-797 | CSS Registrasi + JS Proxy Emsifa | Select2 tema gelap, styling form, loading dropdown Provinsi/Kota |
| 798-1033 | Handler Registrasi | `handle_personel_register()` — validasi, upload file, generate kode_nama, insert DB, redirect ke halaman thank-you |
| 1034-1165 | Halaman Thank-You | `personel_thankyou_display()` — menampilkan kode_nama dan status |
| 1166-1222 | Setup Menu Admin | `personel_admin_menu()`, `personel_admin_menu_porto()`, `personel_admin_menu_video()`, `personel_enqueue_datatables()` |
| 1223-1575 | Halaman Admin Personel | `personel_admin_page()` — DataTable, accept/reject draft, aksi batch, CSS + JS inline |
| 1576-1635 | AJAX: toggle status personel + proxy Emsifa | `lx_toggle_status_personel_handler()`, `proxy_fetch_wilayah()` |
| 1636-1735 | Assets JS/CSS Toggle Admin | `lx_status_personel_assets()` — JS inline untuk toggle status + modal error kustom |
| 1736-2139 | Tampilan Detail Admin | `personel_view_detail()` — tampilan profil lengkap, tabel diff draft, tautan eksternal, grid sertifikat, tombol CV, peralatan, pricelist, sosial, tag |
| 2140-2144 | Helper Label Posisi | `personel_posisi_label($code)` |
| 2145-2178 | Session & Logout | `personel_start_session()`, `is_personel_logged_in()`, `personel_logout_handler()` |
| 2179-2473 | Form Login | `personel_login_form_shortcode()` — HTML form, CSS inline, modal lupa password + JS |
| 2474-3045 | Shortcode Dashboard | `personel_dashboard_shortcode()` — router tab, semua handler POST (CRUD video, CRUD foto, update profil), sidebar + layout utama |
| 3046-3192 | Form Edit Portofolio | `render_tab_edit_portofolio()` — form edit dengan nilai awal + seleksi kategori |
| 3193-3387 | Daftar Portofolio Foto | `render_list_portofolio_foto()` — grid foto user dengan edit/hapus |
| 3388-3541 | Upload Portofolio Foto | `render_tab_portofolio_foto()` — form upload dengan sistem tag + seleksi kategori |
| 3542-3620 | Dashboard Home | `render_personel_home()` — kartu sambutan + statistik (jumlah foto/video) |
| 3621-4245 | Form Edit Profil | `render_personel_edit_profil()` — form lengkap, parsing domisili, inisialisasi Select2 dengan nilai tersimpan, sistem tag, manajer tautan, simpan sebagai draft |
| 4246-4283 | Filter Menu | `custom_personel_menu_filter()` — menyuntikkan tautan Dashboard di nav ketika personel login |
| 4284-4423 | Admin Portofolio Foto | `personel_porto_admin_page()` — DataTable dengan approve/delete/toggle |
| 4424-4558 | AJAX Toggle Portofolio + Assets | `lx_toggle_porto_status_handler()`, `lx_porto_status_scripts()` |
| 4559-4917 | Sistem Video | `get_video_embed_url()`, `render_tab_form_video()`, `render_list_portofolio_video()` |
| 4918-5056 | Admin Portofolio Video | `personel_video_admin_page()` — DataTable dengan preview iframe |
| 5057-5359 | Direktori Personel Publik | `render_list_personel_publik()` — sistem filter lengkap, grid kartu, CSS, inisialisasi Select2 |
| 5360-5785 | Detail Profil Publik | `render_detail_personel_shortcode()` — layout dua kolom, bagian portofolio, modal |
| 5786-5919 | Arsip Foto + AJAX | `render_porto_item_html()`, `shortcode_arsip_foto()`, `handle_ajax_load_more()` |
| 5920-6115 | Assets Arsip Foto | `lx_porto_foto_assets()` — CSS, HTML modal, JS (fungsi getPorto, load more, filter) |
| 6116-6249 | Arsip Video + AJAX | `render_video_item_html()`, `shortcode_arsip_video_fixed()`, `ajax_video_handler_fixed()` |
| 6250-6383 | Assets Modal Universal | `lx_universal_porto_assets()` — modal bersama untuk semua klik portofolio |
| 6384-6518 | JS Arsip Video (terpisah) | Callback `wp_footer` anonim — fungsi `jalankanCariVideo()`, filter/sort/load-more |
| 6519-6596 | Tombol Switch Porto | `render_switch_porto_button()` — toggle foto/video |
| 6597-6773 | Carousel Event | `render_carousel_event_terbaru()` — carousel Swiper dari posting WP |
| 6774-6811 | Utilitas | `force_premium_menu_new_tab_specific()`, `get_status_kuota_personel()` |
| 6812-6989 | AJAX Rekomendasi/Sosmed + Assets | `lx_handle_update_rekomendasi()`, `lx_handle_update_show_sosmed()`, `lx_rekomendasi_custom_assets()` — JS/CSS tombol toggle admin |
| 6990-7159 | Carousel Portofolio Home | `render_carousel_home_porto_split()` — carousel Swiper dengan kartu 258×154 |
| 7160-7338 | Sistem Artikel | `lx_handle_artikel_personel()`, `render_tab_artikel_personel()` |
| 7339-7363 | Filter | `izinkan_personel_upload_limit()`, `sembunyikan_admin_bar_untuk_personel()` |
| 7364-7531 | Reset Password | `handle_personel_password_reset()`, `personel_reset_password_form_shortcode()` |
| 7532-7937 | Sistem Popup Iklan | `popup_ad_create_table()`, `popup_ad_admin_menu()`, `popup_ad_admin_enqueue()`, `popup_ad_admin_page()`, `popup_ad_render_frontend()` |
| 7938-8222 | Sistem Kategori (UI Seleksi) | `portfolio_kategori_setup_db()`, `render_portfolio_category_selection()` |
| 8223-8717 | Sistem Kategori (Filter Bar) | `render_portfolio_category_filter_bar()` — HTML, CSS, JS objek categoriesData |

---

## 26. Pertimbangan Keamanan

### Penyimpanan Password
- Menggunakan `wp_hash_password()` (bcrypt via WordPress) untuk password personel
- BUKAN `md5()` atau teks biasa

### Perlindungan CSRF
- Registrasi: `wp_create_nonce('personel_register')` / `wp_verify_nonce()`
- Login: POST langsung (tanpa nonce)
- Update profil: `personel_update_nonce` / `update_profile`
- Aksi portofolio: `porto_nonce`, `porto_edit_nonce`
- Aksi admin: `_wpnonce` dengan `personel_action`
- Aksi draft: `draft_nonce` dengan `personel_draft_action`
- Artikel: `artikel_nonce`
- Popup iklan: `popup_ad_nonce_field`

### Pencegahan SQL Injection
- Semua query menggunakan `$wpdb->prepare()` dengan placeholder `%d`, `%s`
- Tidak ada interpolasi variabel langsung di SQL

### Pencegahan XSS
- `esc_html()`, `esc_attr()`, `esc_url()`, `esc_textarea()` digunakan di seluruh sistem
- `wp_kses_post()` untuk field rich text
- `sanitize_text_field()`, `sanitize_email()`, `sanitize_user()` digunakan untuk input form

### Keamanan Upload File
- Validasi tipe MIME untuk foto profil
- Pemeriksaan ekstensi untuk foto portofolio
- `wp_handle_upload()` bawaan WordPress menangani keamanan file

### Kelemahan / Area yang Perlu Diperhatikan
1. **Nonce form registrasi**: Menggunakan `wp_create_nonce` tetapi nonce disimpan di field tersembunyi, tidak terkait dengan user yang login (karena user belum login saat registrasi) — praktik standar WordPress
2. **Mode debug login**: Form reset password memiliki debug output yang dikomentari (baris 7484) yang mengungkapkan token DB — harus dihapus di production
3. **Keamanan session**: Session PHP di lingkungan shared hosting bisa rentan terhadap session fixation/hijacking — tidak ada regenerasi session setelah login
4. **Endpoint AJAX**: `fetch_wilayah` terbuka untuk semua (nopriv) tetapi hanya memproksi API publik — tidak ada rate limiting
5. **Akses file langsung**: Path penghapusan file dibangun dengan mengganti site_url dengan ABSPATH — rapuh, bisa rusak di konfigurasi server yang berbeda

---

## 27. Bug & Keanehan yang Diketahui

### Masalah Kualitas Kode
1. **Pendaftaran shortcode duplikat**: `form_login_personel` ditambahkan dua kali (baris 2179 dan 2181) — tidak berbahaya tetapi redundan
2. **Variabel tidak terpakai di registrasi**: `$nama_depan = strtok($nama_panggilan, ' ')` di baris 909 digunakan untuk mendapatkan nama depan tetapi `$nama_panggilan` sudah berupa satu kata (divalidasi di JS)
3. **Dead code**: Baris 1028-1032 setelah `wp_redirect()` + `exit` — tidak akan pernah dieksekusi
4. **Nama kolom draft**: Query `wp9y_personel` menggunakan `SELECT id` tetapi kolom tidak ada — `$has_draft = $wpdb->get_var($wpdb->prepare("SELECT id FROM wp9y_personel_draft_edit WHERE personel_id = %d", ...))` — ini berfungsi karena hanya memeriksa keberadaan baris
5. **Styling tidak konsisten**: Halaman admin menggunakan gaya admin WordPress, dashboard member menggunakan tema gelap kustom, halaman publik menggunakan tema emas/gelap
6. **HTML tidak konsisten di tabel diff**: Bagian review draft mencampur HTML yang di-escape dan tidak di-escape (misalnya, tag gambar digabungkan dengan `.=` daripada menggunakan `wp_kses`)

### Masalah Fungsional
1. **Perbandingan kadaluwarsa token**: `reset_expiry > NOW()` membandingkan datetime MySQL — bergantung pada waktu server MySQL yang cocok dengan timezone WordPress (tidak dijamin)
2. **Tidak ada rate limiting pada lupa password**: Siapa pun dapat memicu email ke email terdaftar mana pun (potensi vektor penyalahgunaan)
3. **Konflik shadow WP user**: Jika WP user asli memiliki email yang sama dengan personel, login akan secara tidak tepat menganggapnya sebagai akun shadow
4. **Tab artikel tidak lengkap**: Tab artikel dashboard memanggil `render_tab_artikel_personel()` dengan pemeriksaan `function_exists()` — fungsi didefinisikan tetapi daftar artikel tab di-hardcode ke "0" di statistik
5. **Field password di draft**: Ketika user mengatur password baru di form edit profil, itu disimpan di JSON draft sebagai string yang di-hash. Admin dapat melihat "Password Diubah (Hashed)" tetapi tidak dapat melihat password sebenarnya — ini adalah perilaku yang benar
6. **Regenerasi session**: Tidak ada regenerasi ID session setelah login (`session_regenerate_id()`) — potensi kerentanan session fixation

### Keanehan CSS/JS
1. **Input pencarian Select2**: Disembunyikan via `width: 0; opacity: 0` alih-alih `display: none` karena Select2 membutuhkan input untuk dapat difokus untuk navigasi keyboard
2. **Banyak blok jQuery `document.ready`**: File memiliki banyak blok `<script>` terpisah, masing-masing dengan `jQuery(document).ready(...)` sendiri — ini berfungsi tetapi tidak efisien
3. **Duplikasi script inline**: JS input tag diduplikasi di beberapa tempat (registrasi, edit profil, upload foto, upload video, edit portofolio) dengan implementasi yang sedikit berbeda
4. **`option:selected` vs `prop('selected', true)`**: Beberapa inisialisasi Select2 menggunakan `.html(options).trigger('change')` yang mungkin tidak menangani nilai yang sudah dipilih sebelumnya dengan benar di semua browser

---

*Dokumentasi dibuat dari analisis codebase. Nomor baris mengacu pada `function.php` pada saat penulisan.*
