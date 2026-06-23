# Gawean — Complete Codebase Documentation

> **Theme**: WordPress Hello Elementor Child Theme  
> **File**: `function.php` (8,717 lines — single-file architecture)  
> **DB Prefix**: `wp9y_`  
> **Live Site**: https://profesional-indonesia.com  
> **PHP**: 8.1.34  
> **Concept**: No plugins, no page builder dependency for custom features — all logic in one file. Custom user system, custom tables, custom auth.

---

## Table of Contents

1. [Architecture Overview](#1-architecture-overview)
2. [Quick Start / Development](#2-quick-start--development)
3. [Database Schema](#3-database-schema)
4. [Custom User System (Not WP Users)](#4-custom-user-system-not-wp-users)
5. [Session & Auth Flow](#5-session--auth-flow)
6. [Registration System](#6-registration-system)
7. [Shortcodes Reference](#7-shortcodes-reference)
8. [AJAX Endpoints](#8-ajax-endpoints)
9. [Admin Panel](#9-admin-panel)
10. [Member Dashboard](#10-member-dashboard)
11. [Profile Draft System](#11-profile-draft-system)
12. [Portfolio System (Foto & Video)](#12-portfolio-system-foto--video)
13. [Category System (Many-to-Many)](#13-category-system-many-to-many)
14. [Domicile System](#14-domicile-system)
15. [Tags System](#15-tags-system)
16. [Public Frontend Pages](#16-public-frontend-pages)
17. [Password Reset Flow](#17-password-reset-flow)
18. [Article System (WordPress Posts)](#18-article-system-wordpress-posts)
19. [Popup Advertisement](#19-popup-advertisement)
20. [Carousels](#20-carousels)
21. [API Proxy for Wilayah Indonesia](#21-api-proxy-for-wilayah-indonesia)
22. [Media Upload Handling](#22-media-upload-handling)
23. [CSS / JS Patterns & Conventions](#23-css--js-patterns--conventions)
24. [WordPress Hooks Reference](#24-wordpress-hooks-reference)
25. [Complete Function Map (Line Ranges)](#25-complete-function-map-line-ranges)
26. [Security Considerations](#26-security-considerations)
27. [Known Bugs & Oddities](#27-known-bugs--oddities)

---

## 1. Architecture Overview

This is a **single-file WordPress child theme** where all custom logic, styling, and JavaScript live in `function.php` (8,717 lines). The theme inherits from Hello Elementor but implements:

- A **completely custom user/membership system** with its own database tables, authentication (PHP sessions), and role management
- Portfolio management (photo upload + YouTube video links) with admin approval workflow
- A many-to-many category system for portfolio items
- Public-facing directory and archive pages
- Password reset via email tokens
- Popup advertisement system
- Article submission from frontend
- Carousels using Swiper.js

**Key Decision**: The site does NOT use WordPress user accounts for members. It has a `wp9y_personel` table that stores all member data. PHP `$_SESSION` is used for auth. A "shadow" WP user is created on login solely to enable WordPress media upload features.

---

## 2. Quick Start / Development

```bash
# No build step required
# Edit function.php directly
# Upload to: wp-content/themes/gawean/

# Syntax check before deploy
php -l function.php

# No package.json, npm, or composer
# All assets loaded from CDN or inlined
```

### Development Conventions
- All CSS/JS inlined via `wp_footer`, `admin_head`, `admin_footer` action callbacks
- CDN libraries: Swiper.js, Select2, DataTables, jQuery
- Theme colors: `#d4af37` (gold), `#1a1a1a` (dark), `#333` (border)
- All AJAX uses `admin-ajax.php`
- No external CSS/JS files for custom code

---

## 3. Database Schema

All tables are auto-created via `portfolio_kategori_setup_db()` (line 7946, called on `init`).

### `wp9y_personel`
Main members table.

| Column | Type | Description |
|---|---|---|
| `id` | BIGINT AUTO_INCREMENT PK | Primary key |
| `username` | VARCHAR(100) | Unique login name (from nama_panggilan) |
| `password` | VARCHAR(255) | Hashed with `wp_hash_password()` |
| `email` | VARCHAR(100) | Unique, also used for login |
| `nama_lengkap` | VARCHAR(255) | Full name |
| `nama_panggilan` | VARCHAR(100) | Nickname (first name only stored) |
| `kode_nama` | VARCHAR(50) | Format: `{NUMBER}-{POSITION_CODES}` e.g. `0001-FDE` |
| `foto_profil` | VARCHAR(500) | Profile photo URL |
| `cv_url` | VARCHAR(500) | PDF CV URL |
| `sertifikat_multiple` | LONGTEXT | JSON array of certificate image URLs |
| `no_hp` | VARCHAR(50) | Phone number |
| `tanggal_lahir` | DATE | Date of birth |
| `domisili` | TEXT | Domicile (see Domicile System section) |
| `posisi` | VARCHAR(50) | Comma-separated position codes (F,V,D,E,X,A,P) |
| `tag` | TEXT | Comma-separated tags |
| `deskripsi` | TEXT | Self-description |
| `peralatan` | TEXT | Equipment list |
| `pricelist_perhari` | VARCHAR(50) | `dibawah_1jt`, `1jt_3jt`, `diatas_3jt` |
| `pricelist` | LONGTEXT | HTML detailed pricelist (wp_editor content) |
| `porto_links` | TEXT | JSON array of external portfolio URLs |
| `facebook` | VARCHAR(500) | Social media URLs |
| `instagram` | VARCHAR(500) | |
| `tiktok` | VARCHAR(500) | |
| `thread` | VARCHAR(500) | |
| `youtube` | VARCHAR(500) | |
| `status` | VARCHAR(20) | `pending`, `approved`, `non-aktif` |
| `rekomendasi` | VARCHAR(10) | `ya` or `tidak` (recommendation flag) |
| `show_sosmed` | TINYINT(1) | Toggle social media visibility on public profile |
| `reset_token` | VARCHAR(100) | Password reset token |
| `reset_expiry` | DATETIME | Token expiry time |
| `created_at` | DATETIME | Auto-set on insert |

### `wp9y_personel_draft_edit`
Stores pending profile edits awaiting admin approval.

| Column | Type | Description |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | |
| `personel_id` | BIGINT UNIQUE | FK to personel.id |
| `draft_data` | LONGTEXT | JSON object of all changed fields |
| `updated_at` | DATETIME | Auto-update timestamp |

### `wp9y_portofolio`
Photo portfolio items.

| Column | Type | Description |
|---|---|---|
| `id` | BIGINT AUTO_INCREMENT PK | |
| `personel_id` | BIGINT | FK to personel.id |
| `judul` | VARCHAR(255) | Title |
| `foto_url` | VARCHAR(500) | Photo file URL |
| `tanggal_kegiatan` | DATE | Event date |
| `lokasi` | VARCHAR(255) | Location |
| `tahun` | VARCHAR(10) | Year |
| `deskripsi` | TEXT | Description |
| `tags` | TEXT | Comma-separated tags |
| `status` | VARCHAR(20) | `pending`, `approved`, `non-aktif` |
| `created_at` | DATETIME | |

### `wp9y_portofolio_video`
Video portfolio items (YouTube links only — no file upload).

| Column | Type | Description |
|---|---|---|
| `id` | BIGINT AUTO_INCREMENT PK | |
| `personel_id` | BIGINT | FK to personel.id |
| `judul` | VARCHAR(255) | Title |
| `video_url` | VARCHAR(500) | YouTube URL |
| `tanggal_kegiatan` | DATE | |
| `lokasi` | VARCHAR(255) | |
| `tahun` | VARCHAR(10) | |
| `deskripsi` | TEXT | |
| `tags` | TEXT | |
| `status` | VARCHAR(20) | |
| `created_at` | DATETIME | |

### `wp9y_kategori`
Portfolio categories (11 rows, pre-seeded).

| Column | Type |
|---|---|
| `id` | INT AUTO_INCREMENT PK |
| `nama` | VARCHAR(100) UNIQUE |
| `slug` | VARCHAR(100) UNIQUE |
| `deskripsi` | TEXT |

Categories seeded:
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
Many-to-many mapping for photos.

| Column | Type |
|---|---|
| `portofolio_id` | BIGINT |
| `kategori_id` | INT |
| PK: `(portofolio_id, kategori_id)` | |

### `wp9y_portofolio_video_kategori_map`
Many-to-many mapping for videos.

| Column | Type |
|---|---|
| `video_id` | BIGINT |
| `kategori_id` | INT |
| PK: `(video_id, kategori_id)` | |

### `wp9y_popup_ad`
Single-row popup advertisement.

| Column | Type |
|---|---|
| `id` | INT AUTO_INCREMENT PK |
| `image_url` | VARCHAR(500) |
| `link_url` | VARCHAR(500) |
| `is_active` | TINYINT(1) |
| `updated_at` | DATETIME |

---

## 4. Custom User System (Not WP Users)

### Core Concept
The site has its own member table (`wp9y_personel`) completely separate from WordPress users. Members authenticate via PHP sessions, not WP cookies.

### Position Codes
Stored in `wp9y_personel.posisi` as comma-separated codes:

| Code | Label |
|---|---|
| F | Fotografer |
| V | Videografer |
| D | Drone |
| E | Editor |
| X | VFX |
| A | Animator |
| P | AI Artist - Prompt Engineer |

Mapping function: `personel_posisi_label($code)` at line 2141.

### Kode Nama Generation
Format: `{NUMBER}-{POSITION_LETTERS}`

Example: `0001-FDE` means:
- `0001` = sequential number (auto-increment, not from DB — reads last kode_nama and increments)
- `FDE` = positions Fotografer, Videografer, Editor

Generation logic (line 938-960):
```php
$last_kode = $wpdb->get_var("SELECT kode_nama FROM wp9y_personel ORDER BY id DESC LIMIT 1");
// Extract number before dash, increment
// Append uppercase position codes
```

### Status Workflow
`pending` → (admin approves) → `approved` → (admin toggles) → `non-aktif`

---

## 5. Session & Auth Flow

### Session Initialization (line 2148)
```php
add_action('init', 'personel_start_session', 1);
function personel_start_session() {
    if (!session_id()) session_start();
}
```
Priority 1 ensures session starts before any other init handler.

### Login Flow (line 2183)
1. User submits form with username/email + password
2. Query `wp9y_personel` WHERE `(email = %s OR username = %s) AND status = 'approved'`
3. If found and password matches (`wp_check_password`):
   - Sets `$_SESSION['personel_id']`, `$_SESSION['personel_nama']`, `$_SESSION['personel_email']`
   - **Shadow WP User creation**: Looks up or creates a WP user with same email, role `personel`
   - Calls `wp_set_auth_cookie()` for the shadow user (enables media upload features)
   - Redirects to `/dashboard-personel`

### Logout Flow (line 2160)
1. `$_GET['personel_action'] == 'logout'`
2. Destroys PHP session
3. Calls `wp_logout()` for shadow WP user
4. Redirects to `/login-personel`

### Auth Check Function (line 2154)
```php
function is_personel_logged_in() {
    return isset($_SESSION['personel_id']);
}
```
Used throughout to gate dashboard access.

---

## 6. Registration System

### Shortcode: `[personel_register]` (line 275)

### Form Fields
1. **Nama Lengkap** (required)
2. **Nama Panggilan** (required, single word, 3+ chars, used as username)
3. **Email** (required, unique)
4. **Password** (required, 8+ chars)
5. **Foto Profil** (optional, JPG/PNG/WEBP, max 2MB)
6. **No. HP** (optional)
7. **Tanggal Lahir** (required)
8. **Domisili** (province 1 required, province 2 optional — see Domicile System)
9. **Posisi** (checkboxes, at least 1 required)
10. **Deskripsi Diri** (optional)
11. **Link Portofolio Eksternal** (optional, max 5 URLs)
12. **CV (PDF)** (required)
13. **Sertifikat** (optional, multiple images)
14. **Peralatan** (optional)
15. **Pricelist/hari** (required — dropdown)
16. **Pricelist Detail** (wp_editor HTML)
17. **Social Media** (optional: Facebook, Instagram, TikTok, Thread, YouTube)
18. **Tags** (max 10, custom JS tag input)

### Registration Handler (line 801)
1. Validates nonce
2. Includes WordPress file handling libs
3. Uploads CV (PDF) and certificates (multiple images) first
4. Validates all fields
5. Generates `kode_nama` from last record + position codes
6. Uploads profile photo with custom validation (extension + size checks)
7. Inserts into `wp9y_personel`
8. Redirects to thank-you page with kode_nama

### Shadow WP User
Unlike login, registration does NOT auto-create a WP user. The WP user is created only on first login.

### Client-Side Validation
- Nama panggilan: AJAX check on blur for availability
- Posisi: at least 1 checkbox must be checked
- Tags: custom JS tag input with max 10 limit
- Portofolio links: max 5 URLs with clone/remove UI

### Registration Error Pattern
Redirects back to form with `?pesan=...&success=0` appended. Uses `urldecode()` to display messages.

---

## 7. Shortcodes Reference

| Shortcode | Function | Line | Purpose |
|---|---|---|---|
| `[personel_register]` | `personel_register_form()` | 275 | Full registration form |
| `[form_login_personel]` | `personel_login_form_shortcode()` | 2183 | Login form with forgot password modal |
| `[dashboard_personel]` | `personel_dashboard_shortcode()` | 2477 | Member dashboard (multi-tab) |
| `[list_personel_publik]` | `render_list_personel_publik()` | 5062 | Public personnel directory with filters |
| `[detail_personel_luxury]` | `render_detail_personel_shortcode()` | 5366 | Single personnel profile page |
| `[arsip_foto_publik]` | `shortcode_arsip_foto()` | 5833 | Public photo gallery archive |
| `[arsip_video_publik]` | `shortcode_arsip_video_fixed()` | 6163 | Public video gallery archive |
| `[switch_porto_button]` | `render_switch_porto_button()` | 6523 | Foto/Video tab toggle button |
| `[carousel_event_terbaru]` | `render_carousel_event_terbaru()` | 6602 | WordPress posts carousel (category: needs event) |
| `[carousel_home_porto]` | `render_carousel_home_porto_split()` | 6990 | Portfolio carousel (type=foto or type=video) |
| `[personel_thankyou]` | `personel_thankyou_display()` | 1035 | Post-registration success page |
| `[personel_reset_form]` | `personel_reset_password_form_shortcode()` | 7467 | Password reset form (token + email in URL) |

---

## 8. AJAX Endpoints

All use `wp_ajax_` and `wp_ajax_nopriv_` hooks on `admin-ajax.php`.

| Action | Auth | Handler | Line | Purpose |
|---|---|---|---|---|
| `fetch_wilayah` | none | `proxy_fetch_wilayah()` | 1610 | Proxy for Indonesia provinces/regencies API |
| `toggle_status_personel` | admin | `lx_toggle_status_personel_handler()` | 1579 | Approve/reject personel |
| `toggle_porto_status` | admin | `lx_toggle_porto_status_handler()` | 4430 | Approve/reject portfolio items (foto & video) |
| `load_more_porto` | none | `handle_ajax_load_more()` | 5870 | Infinite scroll for photo archive |
| `load_more_porto_video` | none | `ajax_video_handler_fixed()` | 6196 | Infinite scroll for video archive |
| `update_rekomendasi` | admin | `lx_handle_update_rekomendasi()` | 6819 | Toggle recommendation flag |
| `update_show_sosmed` | admin | `lx_handle_update_show_sosmed()` | 6855 | Toggle social media visibility |

### `fetch_wilayah` (Emsifa API Proxy)
- `type=provinces` → fetches `https://emsifa.github.io/api-wilayah-indonesia/api/provinces.json`
- `type=regencies&prov_id=N` → fetches `https://emsifa.github.io/api-wilayah-indonesia/api/regencies/{N}.json`
- Returns raw JSON response
- Registered for both admin and non-admin users

---

## 9. Admin Panel

### Menu Structure
- **Personel** (top-level, `personel-admin`) - icon groups, position 30
  - **Portofolio Foto** (sub-menu, `personel-porto`)
  - **Portofolio Video** (sub-menu, `personel-video`)
- **Popup Iklan** (top-level, `popup-iklan`) - position 80

### 9.1 Personel Admin Page (`personel_admin_page`, line 1224)

**Table Columns**: Kode, Foto, Nama, Email, Status, Tanggal, Rekomendasi (toggle), Show Sosmed (toggle), Aksi

**Features**:
- DataTable with Indonesian locale
- Search & pagination
- **Status badges**: Approved (green), Pending (yellow)
- **Pending Edit badge**: Shows orange "Pending Edit" if draft exists for that personel
- **AJAX toggle**: One-click approve/non-aktif (green/red button)
- **Rekomendasi toggle**: Green=YA, Red=TIDAK
- **Show Sosmed toggle**: Green=YA (visible), Red=TIDAK
- **Batch actions**: Approve via form submit
- **Delete cascade**: Deletes personel, their portfolio photos (with file deletion), portfolio videos, and draft edits
- **View detail**: `?page=personel-admin&view=ID`

### 9.2 Admin Detail View (`personel_view_detail`, line 1737)

Shows comprehensive personel info:
- Avatar + name + kode + status
- Contact info (email, phone)
- Profile details (DOB with age calculation, domicile, position with labels)
- External portfolio links (rendered from JSON)
- CV download button
- Certificates grid (images)
- Equipment, pricelist, social media, tags

**Draft Review Section** (if draft exists):
- Side-by-side diff table comparing current values vs proposed changes
- File comparisons: profile photo, CV, certificates shown as visual preview
- Position changes mapped to labels
- Password change indicator (but never shown in plaintext)
- **Approve** button (green) — applies draft, deletes old files
- **Reject** button (red) — deletes draft files, removes draft

### 9.3 Foto Admin Page (`personel_porto_admin_page`, line 4284)

Table: Foto (thumbnail), Judul, Personel, Detail (lokasi+tahun), Deskripsi & Tags, Status, Aksi

Features:
- DataTable with search
- AJAX toggle approved/non-aktif
- Approve button for pending items
- Delete with file cleanup

### 9.4 Video Admin Page (`personel_video_admin_page`, line 4919)

Same structure as Foto admin but for videos:
- Preview Video (embedded iframe + original link)
- AJAX toggle, approve, delete

### 9.5 Popup Ad Admin (`popup_ad_admin_page`, line 7599)

- Settings page under Popup Iklan menu
- Fields: Image URL (with media uploader), Link URL, Active checkbox
- Live preview of uploaded image

---

## 10. Member Dashboard

### Shortcode: `[dashboard_personel]` (line 2477)

### Gate Check
- Must be logged in (`is_personel_logged_in()`)
- Shows "Silahkan login terlebih dahulu" if not authenticated

### Tab Structure
Navigation via `?tab=` query parameter:

| Tab | Key | Function | Description |
|---|---|---|---|
| Dashboard | `dashboard` | `render_personel_home()` | Welcome card, stats (foto/video/artikel counts) |
| Edit Profil | `edit-profil` | `render_personel_edit_profil()` | Full profile edit form (saves as draft) |
| Portofolio Foto (list) | `foto` | `render_list_portofolio_foto()` | Grid of user's photos with edit/delete |
| + Unggah Foto | `foto&action=add` | `render_tab_portofolio_foto()` | Photo upload form |
| Edit Foto | `foto&action=edit&id=N` | `render_tab_edit_portofolio()` | Edit existing photo |
| Portofolio Video (list) | `video` | `render_list_portofolio_video()` | Grid of user's videos |
| + Unggah Video | `video&action=add` | `render_tab_form_video()` | Video URL form |
| Edit Video | `video&action=edit&id=N` | `render_tab_form_video()` | Edit existing video |
| Artikel | `artikel` | `render_tab_artikel_personel()` | Article creation form (WordPress posts) |

### Sidebar Menu Items
- Dashboard, Edit Profil, Portofolio Foto, Portofolio Video, Artikel, Logout
- Active tab highlighted with CSS class

### Portfolio Quota
- Photos: max 20 (checked via `get_status_kuota_personel()` at line 6796)
- Videos: max 8
- Button disabled with warning when quota reached

### Portfolio Upload Handler (line 2651)
- Nonce verified (`add_portofolio`)
- File upload with MIME type whitelist (jpg, png, webp)
- Custom validation via `$overrides['mimes']`
- Inserts into `wp9y_portofolio`, then inserts category mappings
- Success/failure shown via custom modal popup

### Portfolio Edit Handler (line 2545)
- Nonce verified (`edit_portofolio`)
- Can replace photo file
- Resets status to `pending` (requires re-approval)
- Rebuilds category mappings

### Portfolio Delete Handler (line 2608)
- Verifies ownership (WHERE id = %d AND personel_id = %d)
- Deletes physical file from server
- Deletes from `wp9y_portofolio_kategori_map` and `wp9y_portofolio`

### Video CRUD (line 2488)
- Same pattern as photo but with `wp9y_portofolio_video`
- No file upload — accepts YouTube URL only
- `get_video_embed_url()` (line 4559) extracts YouTube ID from various URL formats

### Dashboard Home (line 3543)
- Welcome message with personel name
- Account status indicator (Approved: green, Non-aktif: red, Pending: yellow)
- Stats cards: photo count, video count, article count

---

## 11. Profile Draft System

### How It Works
When a member edits their profile, changes are NOT applied directly to `wp9y_personel`. Instead, they are saved to `wp9y_personel_draft_edit` as a JSON blob.

### The Complete Flow

#### 1. User Edits Profile
Form at `render_personel_edit_profil()` (line 3622):
- Pre-fills all fields from current `wp9y_personel` data
- Shows gold notification banner if a pending draft already exists
- Parses domicile (both new and legacy formats) for pre-fill

#### 2. Save as Draft (line 2732)
- Nonce verified (`update_profile`)
- Builds `$draft_fields` array with ALL fields (text + file URLs)
- If password field is filled, it's included (always hashed)
- File uploads happen immediately (files stored on server)
- **Orphan file cleanup**: If an old draft exists, its files are checked — if they're not used by the current draft or the active profile, they're deleted
- Uses `$wpdb->replace()` to upsert the draft (one draft per personel_id)

#### 3. Admin Review (in detail view, line 1755)
- Compares current vs draft field-by-field
- Handles special types: images (foto_profil, sertifikat_multiple), files (cv_url), positions (label mapping), links (JSON comparison)
- Shows clean diff table

#### 4a. Approve (line 1265)
- Deletes old files that were replaced by new draft files
- Updates `wp9y_personel` with all draft fields
- Deletes draft row

#### 4b. Reject (line 1305)
- Deletes new files that were uploaded in the draft
- Deletes draft row
- Main personel data unchanged

### File Cleanup Rules
- **On approve delete**: Old foto_profil / cv_url (only if different from draft)
- **On approve delete**: Old certificate images (only those NOT in new draft)
- **On reject delete**: New draft foto_profil / cv_url / certificate files
- **On draft overwrite**: Old draft files not referenced by new draft or active profile

---

## 12. Portfolio System (Foto & Video)

### Photo Portfolio
- Files uploaded to `/wp-content/uploads/` via `wp_handle_upload()`
- MIME validation: `image/jpeg`, `image/png`, `image/webp`
- Max 20 per personel
- Storage: `wp9y_portofolio.foto_url`

### Video Portfolio
- YouTube URL only (no file upload)
- `get_video_embed_url()` extracts YouTube ID:
  - Matches `youtube.com/watch?v=ID` and `youtu.be/ID` patterns
  - Returns `https://www.youtube.com/embed/ID`
- Max 8 per personel
- Storage: `wp9y_portofolio_video.video_url`
- Thumbnails: `https://img.youtube.com/vi/{ID}/mqdefault.jpg`

### Portfolio Items Common Fields
- `judul` (title), `tanggal_kegiatan`, `lokasi`, `tahun`, `deskripsi`, `tags`
- Status workflow: `pending` → `approved` / `non-aktif`
- Category mapping (many-to-many via mapping tables)
- Tags: comma-separated, max 10

---

## 13. Category System (Many-to-Many)

### Database Tables
- `wp9y_kategori` — 11 pre-seeded categories
- `wp9y_portofolio_kategori_map` — foto mapping
- `wp9y_portofolio_video_kategori_map` — video mapping

### Category Selection UI (`render_portfolio_category_selection`, line 8065)
Used in upload/edit forms in dashboard.
- CSS `display: table` layout with 3 columns:
  - **Column 1** (36px): checkbox
  - **Column 2** (200px): category name with `border-right` separator
  - **Column 3**: description with `word-break: break-word`
- Max 3 selections enforced client-side (JS disables remaining checkboxes on reaching 3)
- Selected items highlighted with gold border
- Mobile (≤640px): converts to block layout, description indented

### Category Filter Bar (`render_portfolio_category_filter_bar`, line 8227)
Used in public archive pages (foto/video).
- Horizontal scrollable nav bar of category buttons (pill style)
- Active category highlighted in gold
- Hidden input stores selected slug
- **Detail/Explanation Box**: When a category is clicked, a panel slides down showing:
  - Category title + description
  - Sub-fields grid (e.g., for "Wedding & Personal": Wedding, Prewedding, Personal & Family categories with example items)
  - All sub-field data is hardcoded in a JS `categoriesData` object in the script
- Clicking "Semua Kategori" hides the detail box
- Triggers AJAX grid reload for filtered results

### Category Data in JS
The `categoriesData` object (line 8425-8652) is a large JS object containing structured data for each category with sub-groups and example items. This is used purely for the explanation box UI.

---

## 14. Domicile System

### Storage Format
Stored in `wp9y_personel.domisili` as a text string:

| Format | Example |
|---|---|
| Single province | `Jawa Barat - Bandung, Bekasi` |
| Multi-province | `Jawa Barat - Bandung, Bekasi \|\| Jawa Timur - Surabaya` |
| Legacy | `Bandung, Jawa Barat` |

### Registration
- Province 1 shown by default (required)
- Province 2 toggled via "+ Tambah Provinsi Lain" button
- Each province select uses Select2 with max 3 city selections (via `maximumSelectionLength: 3`)
- Emsifa API proxy fetches provinces and regencies
- Saved format: `{ProvName} - {City1}, {City2}` or `{Prov1} ... \|\| {Prov2} ...`

### Edit Profile Parsing
- Checks for `||` separator → splits into 2 province blocks
- Each block: splits by ` - ` → province + city list
- If no ` - ` but has `, ` → legacy format
- Pre-selects saved provinces/cities when loading edit form
- Uses `initProvKotaEdit()` JS function that loads province data and then loads cities with pre-selection

### Public Display Filtering
- Filter query: `LIKE '%{Provinsi} - %'` catches both single and multi-province
- Combined with `OR LIKE '%{kota}%'` for city search
- Province dropdown in public directory uses Select2 + Emsifa proxy

---

## 15. Tags System

### Implementation
Custom JS-based tag input system (not a library):
- User types tag text, presses Enter/comma/space to add
- Tags displayed as removable pills with `#` prefix
- Max 10 tags enforced
- Deduplication
- Lowercase + alphanumeric sanitization

### Storage
- Comma-separated string in `wp9y_personel.tag` (profile tags)
- Same format in `wp9y_portofolio.tags` and `wp9y_portofolio_video.tags` (portfolio tags)

### JS Implementation Details
Used in multiple places with slightly varied code:
- **Registration form** (line 552): Reusable `addTag`/`removeTag` pattern, stored in global `window.removeTag`
- **Edit profile** (line 4129): Pre-loads existing tags from hidden input
- **Upload/Edit foto** (line 3463): Same pattern, with backspace-to-remove
- **Upload/Edit video** (line 4639): Separate instance with `tagInputVideo` ID
- **Edit portofolio** (line 3133): Pre-loads existing tags

---

## 16. Public Frontend Pages

### 16.1 Personnel Directory (`[list_personel_publik]`, line 5062)

**URL**: Page with shortcode, filters via `?p_search=&p_posisi=&p_price=&p_provinsi=&p_kota=`

**Query Logic**:
- Only `status = 'approved'` records
- Sub-queries to count approved photos and videos per personel
- Search across: `nama_panggilan`, `domisili`, `peralatan`, `deskripsi`, `tag`, `pricelist`, `kode_nama`
- Position filter: `LIKE '%{code}%'`
- Price filter: exact match on `pricelist_perhari`
- Province filter: `LIKE '%{provinsi} - %'`
- City filter: `LIKE '%{kota}%'`

**Sorting**: Recommended (`rekomendasi = 'ya'`) first, then newest (`ORDER BY CASE WHEN ... THEN 0 ELSE 1 END ASC, p.id DESC`)

**Card Display**:
- Profile photo (with placeholder if none)
- Display name as `{firstName}-{kode_nama}`
- Position tags (mapped to full labels)
- Domicile + age
- Total karya count
- Price tag (top-right corner overlay)
- "LIHAT PROFIL" button → `/detail-personel/?kode={kode_nama}`

**Filters UI**:
- Search text input
- Position dropdown (all codes)
- Price range dropdown
- Province select (Select2, loaded via Emsifa proxy)
- City select (Select2, depends on province)
- All wrapped in gold-themed filter box

### 16.2 Detail Profile (`[detail_personel_luxury]`, line 5366)

**URL**: `/detail-personel/?kode={kode_nama}`

**Layout**: Two-column (35% left / 65% right) on desktop, stacked on mobile.

**Left Column**:
- Profile photo in framed container
- Pricelist (HTML, rendered via `wpautop(wp_kses_post(...))`)
- Social media section (conditionally shown based on `show_sosmed` flag)
  - WhatsApp/phone number
  - Facebook, Instagram, TikTok, Thread, YouTube links

**Right Column**:
- Display name as `{firstName}-{kode_nama}`
- Meta: Age, Domicile, Position labels
- Bio/description
- Tags
- Certificates grid
- Equipment list

**Portfolio Sections** (below the main flex container):
- **Video Portfolio**: Grid of YouTube thumbnails with play button overlay, click opens modal
- **Photo Portfolio**: Square grid of photos with overlay title, click opens modal

**Portfolio Modal** (shared for both types):
- Fade-in overlay with backdrop blur
- Media: iframe for video, img for photo
- Info panel: title, meta row (author link, location, year, date), description
- Close via X button, overlay click, or ESC

### 16.3 Photo Archive (`[arsip_foto_publik]`, line 5833)

**URL**: Page with shortcode

**Features**:
- Category filter bar (horizontal pills with detail box)
- Search input (name, location, tags)
- Sort dropdown: Newest/Oldest post, Newest/Oldest event date
- Grid: 4 columns desktop, 2 tablet, 1 mobile
- Infinite scroll via "MUAT LEBIH BANYAK" button (12 items per load)
- Click opens modal with full image + metadata

**AJAX Handler** (`handle_ajax_load_more`, line 5870):
- Accepts: `type`, `offset`, `search`, `tanggal`, `tahun`, `sort`, `category`
- Category filter via JOIN on mapping tables with WHERE `k.slug = %s`
- Returns rendered HTML for each item

### 16.4 Video Archive (`[arsip_video_publik]`, line 6163)

Same structure as photo archive but for videos:
- `render_video_item_html()` at line 6123
- AJAX handler at line 6196
- YouTube thumbnails as preview
- Play button overlay
- Modal opens with iframe embed

### 16.5 Switch Button (`[switch_porto_button]`, line 6523)

Toggle between foto/video pages. Checks current URL for `portofolio-video` path, shows appropriate active state. Two pill buttons side by side in a container.

---

## 17. Password Reset Flow

### Step 1: Forgot Password (embedded in login form, line 2414)
- Modal popup asks for email
- Sends POST to `handle_personel_password_reset()` (line 7365)

### Step 2: Send Email (line 7369)
- Looks up email in `wp9y_personel`
- Generates `bin2hex(random_bytes(20))` token
- Stores `reset_token` + `reset_expiry` (1 hour) in personel row
- Sends email via `wp_mail()` with link: `/reset-password-personel/?token={token}&email={email}`

### Step 3: Reset Form (`[personel_reset_form]`, line 7467)
- Reads `token` and `email` from GET params
- Validates token and expiry against database
- Shows new password form
- On submit, updates both `wp9y_personel.password` AND shadow WP user password

### Important Details
- Uses `wp_hash_password()` (not `md5` or plaintext)
- Tokens expire after 1 hour (`current_time('mysql') + 1 hour`)
- Has debug output (line 7484-7495) that reveals token/expiry when validation fails — remove in production

---

## 18. Article System (WordPress Posts)

### Handler: `lx_handle_artikel_personel()` (line 7168)

- Triggered by `submit_artikel` POST
- Creates WordPress `post` with:
  - Category "Artikel" (auto-created if not exists)
  - Status `pending` (requires admin approval)
  - Featured image via `media_handle_upload()`
  - Meta `_personel_author_id` stores session personel_id

### Form: `render_tab_artikel_personel()` (line 7240)
- Title input
- Cover image upload (required)
- Category shown as disabled "Artikel"
- Content via `wp_editor()` (TinyMCE with Add Media button enabled)
- Submit sends for admin review

---

## 19. Popup Advertisement

### Table: `wp9y_popup_ad` (auto-created, single row)

### Admin Settings (line 7599)
- Image URL field with WordPress media uploader
- Link URL (optional)
- Active/inactive checkbox

### Frontend Rendering (`popup_ad_render_frontend`, line 7737)
- Hooked to `wp_footer` at priority 9999
- Guards: not admin, not AJAX, table exists, is_active=1, image_url not empty
- **Session-storage based**: Shown once per browser session (`sessionStorage.getItem('popup_iklan_closed')`)
- Portrait image format (max-width 380px, 85vw on mobile)
- Close button (✕ circle in top-right)
- Smooth fade-in with scale animation (500ms delay)
- Close on: button click, overlay click, ESC key
- Optional link wrapping the image

---

## 20. Carousels

### 20.1 Event Carousel (`[carousel_event_terbaru]`, line 6602)

- Uses Swiper.js (CDN)
- Queries WordPress posts in category "kebutuhan event", latest 10
- 1 slide mobile, 2 slides tablet+
- Each slide: thumbnail card with "KEBUTUHAN EVENT" badge, title (20px bold white), date
- Auto-play not enabled

### 20.2 Home Portfolio Carousel (`[carousel_home_porto]`, line 6997)

- Shortcode attributes: `type=foto` or `type=video`
- Latest 10 approved items
- Fixed card size: 258px × 154px
- Uses `slidesPerView: "auto"` to respect CSS width
- Swiper with loop enabled
- Navigation arrows (gold)
- Click opens portfolio modal
- Overlay: gradient bottom with gold title + author link

---

## 21. API Proxy for Wilayah Indonesia

### Problem
The frontend needs Indonesian province/city dropdowns, but the Emsifa API blocks CORS requests.

### Solution
Custom WordPress AJAX proxy at `proxy_fetch_wilayah()` (line 1610).

**Endpoints proxied**:
- `type=provinces` → `https://emsifa.github.io/api-wilayah-indonesia/api/provinces.json`
- `type=regencies&prov_id=N` → `https://emsifa.github.io/api-wilayah-indonesia/api/regencies/{N}.json`

**Usage**:
```js
$.getJSON(ajaxurl, { action: 'fetch_wilayah', type: 'provinces' })
$.getJSON(ajaxurl, { action: 'fetch_wilayah', type: 'regencies', prov_id: 12 })
```

**Security**: Just a pass-through proxy, no caching, no validation of response data beyond checking it's not empty/error.

---

## 22. Media Upload Handling

### Profile Photo
- Registration: Custom validation with file extension + size check (2MB limit)
- Edit: `wp_handle_upload()` with MIME type whitelist (`image/jpeg`, `image/png`, `image/webp`)
- Stored as URL string

### CV
- PDF only
- `wp_handle_upload()` with default settings
- Stored as URL string

### Certificates
- Multiple images (JPG/PNG/WEBP)
- Loop through `$_FILES['sertifikat_files']` array
- Each file uploaded individually
- Stored as JSON array of URLs

### Portfolio Photos
- `wp_handle_upload()` with MIME overrides (restrict to jpg/png/webp)
- Custom error message on invalid format
- Successful upload message via custom modal

### Portfolio Videos
- No file upload — YouTube URL text input only
- `get_video_embed_url()` normalizes YouTube URLs to embed format

### Upload Size Limits
- WordPress filter `upload_size_limit` sets 64MB for all users
- Custom filter `izinkan_personel_upload_limit()` (line 7340) allows 10MB for `personel` role

---

## 23. CSS / JS Patterns & Conventions

### Custom Modal Popups (Replacing `alert()`)
All `alert()` calls replaced with dynamically-generated CSS modals using a consistent IIFE pattern:
```js
(function(){
    var d=document.createElement('div');
    d.style.cssText='position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:999999;display:flex;align-items:center;justify-content:center;';
    d.innerHTML='<div style="background:#1a1a1a;border:1px solid #d4af37;border-radius:8px;padding:30px 40px;max-width:400px;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,0.5);">...</div>';
    document.body.appendChild(d);
})();
```
- Gold border (1px solid #d4af37) = success
- Red border (1px solid #ff4444) = error

### Select2 Configuration
- Dark theme matching site aesthetics
- Province selects: `minimumResultsForSearch: -1` to suppress search
- City multi-select: `maximumSelectionLength: 3`
- Search input visually hidden when no tags selected (via CSS opacity/width, not display:none — needs to be focusable)
- `.form-row`, `.form-group.full` set to `overflow: visible` to prevent Select2 dropdown clipping

### DataTables
- CDN: `cdn.datatables.net/1.13.6/`
- Indonesian language pack
- Custom dark theme styling
- Loaded only on admin personel pages (not globally)

### Swiper.js
- CDN: `cdn.jsdelivr.net/npm/swiper@11/`
- Used in 3 carousel instances
- Each instance has its own initialization script

### Tag Input System
- Custom implementation (not a library)
- Pattern: hidden input stores comma-separated values, visual tags rendered in wrapper, input field appended after last tag
- Events: keydown (enter/comma/backspace), blur, click (remove)

---

## 24. WordPress Hooks Reference

### Actions

| Hook | Callback | Priority | Line |
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
| `wp_footer` | anonymous (video JS inline) | default | 6385 |
| `wp_footer` | `force_premium_menu_new_tab_specific()` | default | 6778 |
| `wp_footer` | `popup_ad_render_frontend()` | **9999** | 7937 |

### AJAX Actions

| Hook | Callback | Line |
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

| Hook | Callback | Priority | Line |
|---|---|---|---|
| `hello_elementor_page_title` | `hello_elementor_check_hide_title()` | default | 258 |
| `wp_get_nav_menu_items` | `custom_personel_menu_filter()` | 20 | 4247 |
| `upload_size_limit` | anonymous closure (64MB) | default | — |
| `upload_size_limit` | `izinkan_personel_upload_limit()` | default | 7340 |
| `show_admin_bar` | `sembunyikan_admin_bar_untuk_personel()` | default | 7348 |

---

## 25. Complete Function Map (Line Ranges)

| Lines | Section | Functions |
|---|---|---|
| 1-273 | Hello Elementor Theme Boilerplate | `hello_elementor_setup()`, `hello_maybe_update_theme_version_in_db()`, `hello_elementor_scripts_styles()`, `hello_elementor_register_elementor_locations()`, `hello_elementor_content_width()`, `hello_elementor_add_description_meta_tag()`, `hello_elementor_check_hide_title()`, `hello_elementor_body_open()` |
| 274-536 | Registration Form HTML | `personel_register_form()` — full HTML form + inline CSS + JS (Select2 init, tag system, porto links, position checkboxes, nama panggilan AJAX availability check) |
| 537-644 | Registration Inline JS | jQuery for porto links, Select2 styling |
| 645-797 | Registration CSS + Emsifa Proxy JS | Select2 dark theme, form styling, Province/City dropdown loading |
| 798-1033 | Registration Handler | `handle_personel_register()` — validation, file uploads, kode_nama generation, DB insert, redirect to thank-you page |
| 1034-1165 | Thank-You Page | `personel_thankyou_display()` — shows kode_nama and status |
| 1166-1222 | Admin Menu Setup | `personel_admin_menu()`, `personel_admin_menu_porto()`, `personel_admin_menu_video()`, `personel_enqueue_datatables()` |
| 1223-1575 | Admin Personel Page | `personel_admin_page()` — DataTable, draft accept/reject, batch actions, inline CSS + JS |
| 1576-1635 | AJAX: toggle personel status + Emsifa proxy | `lx_toggle_status_personel_handler()`, `proxy_fetch_wilayah()` |
| 1636-1735 | Admin Toggle JS/CSS Assets | `lx_status_personel_assets()` — inline JS for status toggle + custom error modal |
| 1736-2139 | Admin Detail View | `personel_view_detail()` — full profile display, draft diff table, external links, sertifikat grid, CV button, equipment, pricelist, social, tags |
| 2140-2144 | Position Label Helper | `personel_posisi_label($code)` |
| 2145-2178 | Session & Logout | `personel_start_session()`, `is_personel_logged_in()`, `personel_logout_handler()` |
| 2179-2473 | Login Form | `personel_login_form_shortcode()` — form HTML, inline CSS, forgot password modal + JS |
| 2474-3045 | Dashboard Shortcode | `personel_dashboard_shortcode()` — tab router, all POST handlers (video CRUD, foto CRUD, profile update), sidebar + main layout |
| 3046-3192 | Edit Portfolio Form | `render_tab_edit_portofolio()` — pre-filled edit form with category selection |
| 3193-3387 | List Portfolio Foto | `render_list_portofolio_foto()` — grid of user's photos with edit/delete |
| 3388-3541 | Upload Portfolio Foto | `render_tab_portofolio_foto()` — upload form with tag system + category selection |
| 3542-3620 | Dashboard Home | `render_personel_home()` — welcome card + stats (foto/video counts) |
| 3621-4245 | Edit Profile Form | `render_personel_edit_profil()` — full form, domicile parsing, Select2 init with saved values, tag system, link manager, save as draft |
| 4246-4283 | Menu Filter | `custom_personel_menu_filter()` — inject Dashboard link in nav when personel logged in |
| 4284-4423 | Admin Portfolio Foto | `personel_porto_admin_page()` — DataTable with approve/delete/toggle |
| 4424-4558 | AJAX Toggle Portfolio + Assets | `lx_toggle_porto_status_handler()`, `lx_porto_status_scripts()` |
| 4559-4917 | Video System | `get_video_embed_url()`, `render_tab_form_video()`, `render_list_portofolio_video()` |
| 4918-5056 | Admin Portfolio Video | `personel_video_admin_page()` — DataTable with iframe preview |
| 5057-5359 | Public Personnel Directory | `render_list_personel_publik()` — full filter system, card grid, CSS, Select2 init |
| 5360-5785 | Public Detail Profile | `render_detail_personel_shortcode()` — two-column layout, portfolio sections, modal |
| 5786-5919 | Photo Archive + AJAX | `render_porto_item_html()`, `shortcode_arsip_foto()`, `handle_ajax_load_more()` |
| 5920-6115 | Photo Archive Assets | `lx_porto_foto_assets()` — CSS, modal HTML, JS (getPorto function, load more, filter) |
| 6116-6249 | Video Archive + AJAX | `render_video_item_html()`, `shortcode_arsip_video_fixed()`, `ajax_video_handler_fixed()` |
| 6250-6383 | Universal Modal Assets | `lx_universal_porto_assets()` — shared modal for all portfolio clicks |
| 6384-6518 | Video Archive JS (separate) | Anonymous `wp_footer` callback — `jalankanCariVideo()` function, filter/sort/load-more |
| 6519-6596 | Switch Porto Button | `render_switch_porto_button()` — foto/video toggle |
| 6597-6773 | Event Carousel | `render_carousel_event_terbaru()` — Swiper carousel of WP posts |
| 6774-6811 | Utilities | `force_premium_menu_new_tab_specific()`, `get_status_kuota_personel()` |
| 6812-6989 | AJAX Rekomendasi/Sosmed + Assets | `lx_handle_update_rekomendasi()`, `lx_handle_update_show_sosmed()`, `lx_rekomendasi_custom_assets()` — admin toggle buttons JS/CSS |
| 6990-7159 | Home Portfolio Carousel | `render_carousel_home_porto_split()` — Swiper carousel with 258×154 cards |
| 7160-7338 | Article System | `lx_handle_artikel_personel()`, `render_tab_artikel_personel()` |
| 7339-7363 | Filters | `izinkan_personel_upload_limit()`, `sembunyikan_admin_bar_untuk_personel()` |
| 7364-7531 | Password Reset | `handle_personel_password_reset()`, `personel_reset_password_form_shortcode()` |
| 7532-7937 | Popup Ad System | `popup_ad_create_table()`, `popup_ad_admin_menu()`, `popup_ad_admin_enqueue()`, `popup_ad_admin_page()`, `popup_ad_render_frontend()` |
| 7938-8222 | Category System (Selection UI) | `portfolio_kategori_setup_db()`, `render_portfolio_category_selection()` |
| 8223-8717 | Category System (Filter Bar) | `render_portfolio_category_filter_bar()` — HTML, CSS, JS categoriesData object |

---

## 26. Security Considerations

### Password Storage
- Uses `wp_hash_password()` (bcrypt via WordPress) for personel passwords
- NOT `md5()` or plaintext

### CSRF Protection
- Registration: `wp_create_nonce('personel_register')` / `wp_verify_nonce()`
- Login: direct POST (no nonce)
- Profile update: `personel_update_nonce` / `update_profile`
- Portfolio actions: `porto_nonce`, `porto_edit_nonce`
- Admin actions: `_wpnonce` with `personel_action`
- Draft actions: `draft_nonce` with `personel_draft_action`
- Article: `artikel_nonce`
- Popup ad: `popup_ad_nonce_field`

### SQL Injection Prevention
- All queries use `$wpdb->prepare()` with `%d`, `%s` placeholders
- No direct variable interpolation in SQL

### XSS Prevention
- `esc_html()`, `esc_attr()`, `esc_url()`, `esc_textarea()` used throughout
- `wp_kses_post()` for rich text fields
- `sanitize_text_field()`, `sanitize_email()`, `sanitize_user()` used for form inputs

### File Upload Security
- MIME type validation for profile photos
- Extension checks for portfolio photos
- WordPress's built-in `wp_handle_upload()` handles file security

### Weaknesses / Areas of Concern
1. **Register form nonce**: Uses `wp_create_nonce` but nonce is stored in a hidden field, not associated with logged-in user (since user isn't logged in during register) — standard WordPress practice
2. **Login debug mode**: Password reset form has commented-out debug output (line 7484) that reveals DB tokens — should be removed in production
3. **Session security**: PHP sessions on a shared hosting environment could be vulnerable to session fixation/hijacking — no session regeneration after login
4. **AJAX endpoints**: `fetch_wilayah` is open to all (nopriv) but only proxies a public API — no rate limiting
5. **Direct file access**: File deletion paths are constructed by replacing site_url with ABSPATH — fragile, could break on different server configurations

---

## 27. Known Bugs & Oddities

### Code Quality Issues
1. **Duplicate shortcode registration**: `form_login_personel` is added twice (lines 2179 and 2181) — harmless but redundant
2. **Unused variable in registration**: `$nama_depan = strtok($nama_panggilan, ' ')` at line 909 is used to get first name but `$nama_panggilan` is already a single word (validated in JS)
3. **Dead code**: Lines 1028-1032 after `wp_redirect()` + `exit` — will never execute
4. **Draft column name**: `wp9y_personel` query uses `SELECT id` but column doesn't exist — `$has_draft = $wpdb->get_var($wpdb->prepare("SELECT id FROM wp9y_personel_draft_edit WHERE personel_id = %d", ...))` — this works because it just checks row existence
5. **Inconsistent styling**: Admin pages use WordPress admin styles, member dashboard uses custom dark theme, public pages use gold/dark theme
6. **Inconsistent HTML in diff table**: The draft review section mixes escaped and unescaped HTML (e.g., image tags are concatenated with `.=` rather than using `wp_kses`)

### Functional Issues
1. **Token expiry comparison**: `reset_expiry > NOW()` compares MySQL datetime — relies on MySQL server time matching WordPress timezone (not guaranteed)
2. **No rate limiting on forgot password**: Anyone can trigger emails to any registered email (potential abuse vector)
3. **Shadow WP user conflict**: If a real WP user has same email as a personel, login will incorrectly assume it's a shadow account
4. **Incomplete article tab**: The dashboard articles tab calls `render_tab_artikel_personel()` with `function_exists()` check — function is defined but the tab's article list is hardcoded to "0" in stats
5. **Password field in draft**: When a user sets a new password in the edit profile form, it's saved in the draft JSON as a hashed string. The admin can see "Password Diubah (Hashed)" but can't see the actual password — this is correct behavior
6. **Session regeneration**: No session ID regeneration after login (`session_regenerate_id()`) — potential session fixation vulnerability

### CSS/JS Oddities
1. **Select2 search input**: Hidden via `width: 0; opacity: 0` instead of `display: none` because Select2 needs the input to be focusable for keyboard navigation
2. **Multiple jQuery `document.ready` blocks**: The file has many separate `<script>` blocks, each with its own `jQuery(document).ready(...)` — this works but is inefficient
3. **Inline script duplication**: The tag input JS is duplicated in multiple places (registration, edit profile, upload foto, upload video, edit portofolio) with slightly different implementations
4. **`option:selected` vs `prop('selected', true)`**: Some Select2 initialization uses `.html(options).trigger('change')` which may not properly handle pre-selected values on all browsers

---

*Documentation generated from codebase analysis. Lines numbers reference `function.php` at commit time of writing.*
