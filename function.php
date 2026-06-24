<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_VERSION', '3.4.7' );
define( 'EHP_THEME_SLUG', 'hello-elementor' );

define( 'HELLO_THEME_PATH', get_template_directory() );
define( 'HELLO_THEME_URL', get_template_directory_uri() );
define( 'HELLO_THEME_ASSETS_PATH', HELLO_THEME_PATH . '/assets/' );
define( 'HELLO_THEME_ASSETS_URL', HELLO_THEME_URL . '/assets/' );
define( 'HELLO_THEME_SCRIPTS_PATH', HELLO_THEME_ASSETS_PATH . 'js/' );
define( 'HELLO_THEME_SCRIPTS_URL', HELLO_THEME_ASSETS_URL . 'js/' );
define( 'HELLO_THEME_STYLE_PATH', HELLO_THEME_ASSETS_PATH . 'css/' );
define( 'HELLO_THEME_STYLE_URL', HELLO_THEME_ASSETS_URL . 'css/' );
define( 'HELLO_THEME_IMAGES_PATH', HELLO_THEME_ASSETS_PATH . 'images/' );
define( 'HELLO_THEME_IMAGES_URL', HELLO_THEME_ASSETS_URL . 'images/' );

if ( ! isset( $content_width ) ) {
	$content_width = 800; // Pixels.
}

if ( ! function_exists( 'hello_elementor_setup' ) ) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function hello_elementor_setup() {
		if ( is_admin() ) {
			hello_maybe_update_theme_version_in_db();
		}

		if ( apply_filters( 'hello_elementor_register_menus', true ) ) {
			register_nav_menus( [ 'menu-1' => esc_html__( 'Header', 'hello-elementor' ) ] );
			register_nav_menus( [ 'menu-2' => esc_html__( 'Footer', 'hello-elementor' ) ] );
		}

		if ( apply_filters( 'hello_elementor_post_type_support', true ) ) {
			add_post_type_support( 'page', 'excerpt' );
		}

		if ( apply_filters( 'hello_elementor_add_theme_support', true ) ) {
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_theme_support(
				'html5',
				[
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
					'script',
					'style',
					'navigation-widgets',
				]
			);
			add_theme_support(
				'custom-logo',
				[
					'height'      => 100,
					'width'       => 350,
					'flex-height' => true,
					'flex-width'  => true,
				]
			);
			add_theme_support( 'align-wide' );
			add_theme_support( 'responsive-embeds' );

			/*
			 * Editor Styles
			 */
			add_theme_support( 'editor-styles' );
			add_editor_style( 'assets/css/editor-styles.css' );

			/*
			 * WooCommerce.
			 */
			if ( apply_filters( 'hello_elementor_add_woocommerce_support', true ) ) {
				// WooCommerce in general.
				add_theme_support( 'woocommerce' );
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support( 'wc-product-gallery-zoom' );
				// lightbox.
				add_theme_support( 'wc-product-gallery-lightbox' );
				// swipe.
				add_theme_support( 'wc-product-gallery-slider' );
			}
		}
	}
}
add_action( 'after_setup_theme', 'hello_elementor_setup' );

function hello_maybe_update_theme_version_in_db() {
	$theme_version_option_name = 'hello_theme_version';
	// The theme version saved in the database.
	$hello_theme_db_version = get_option( $theme_version_option_name );

	// If the 'hello_theme_version' option does not exist in the DB, or the version needs to be updated, do the update.
	if ( ! $hello_theme_db_version || version_compare( $hello_theme_db_version, HELLO_ELEMENTOR_VERSION, '<' ) ) {
		update_option( $theme_version_option_name, HELLO_ELEMENTOR_VERSION );
	}
}

if ( ! function_exists( 'hello_elementor_display_header_footer' ) ) {
	/**
	 * Check whether to display header footer.
	 *
	 * @return bool
	 */
	function hello_elementor_display_header_footer() {
		$hello_elementor_header_footer = true;

		return apply_filters( 'hello_elementor_header_footer', $hello_elementor_header_footer );
	}
}

if ( ! function_exists( 'hello_elementor_scripts_styles' ) ) {
	/**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
	function hello_elementor_scripts_styles() {
		if ( apply_filters( 'hello_elementor_enqueue_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor',
				HELLO_THEME_STYLE_URL . 'reset.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( apply_filters( 'hello_elementor_enqueue_theme_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor-theme-style',
				HELLO_THEME_STYLE_URL . 'theme.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( hello_elementor_display_header_footer() ) {
			wp_enqueue_style(
				'hello-elementor-header-footer',
				HELLO_THEME_STYLE_URL . 'header-footer.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_scripts_styles' );

if ( ! function_exists( 'hello_elementor_register_elementor_locations' ) ) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */
	function hello_elementor_register_elementor_locations( $elementor_theme_manager ) {
		if ( apply_filters( 'hello_elementor_register_elementor_locations', true ) ) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}
add_action( 'elementor/theme/register_locations', 'hello_elementor_register_elementor_locations' );

if ( ! function_exists( 'hello_elementor_content_width' ) ) {
	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	function hello_elementor_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'hello_elementor_content_width', 800 );
	}
}
add_action( 'after_setup_theme', 'hello_elementor_content_width', 0 );

if ( ! function_exists( 'hello_elementor_add_description_meta_tag' ) ) {
	/**
	 * Add description meta tag with excerpt text.
	 *
	 * @return void
	 */
	function hello_elementor_add_description_meta_tag() {
		if ( ! apply_filters( 'hello_elementor_description_meta_tag', true ) ) {
			return;
		}

		if ( ! is_singular() ) {
			return;
		}

		$post = get_queried_object();
		if ( empty( $post->post_excerpt ) ) {
			return;
		}

		echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $post->post_excerpt ) ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'hello_elementor_add_description_meta_tag' );

// Settings page
require get_template_directory() . '/includes/settings-functions.php';

// Header & footer styling option, inside Elementor
require get_template_directory() . '/includes/elementor-functions.php';

if ( ! function_exists( 'hello_elementor_customizer' ) ) {
	// Customizer controls
	function hello_elementor_customizer() {
		if ( ! is_customize_preview() ) {
			return;
		}

		if ( ! hello_elementor_display_header_footer() ) {
			return;
		}

		require get_template_directory() . '/includes/customizer-functions.php';
	}
}
add_action( 'init', 'hello_elementor_customizer' );

if ( ! function_exists( 'hello_elementor_check_hide_title' ) ) {
	/**
	 * Check whether to display the page title.
	 *
	 * @param bool $val default value.
	 *
	 * @return bool
	 */
	function hello_elementor_check_hide_title( $val ) {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$current_doc = Elementor\Plugin::instance()->documents->get( get_the_ID() );
			if ( $current_doc && 'yes' === $current_doc->get_settings( 'hide_title' ) ) {
				$val = false;
			}
		}
		return $val;
	}
}
add_filter( 'hello_elementor_page_title', 'hello_elementor_check_hide_title' );

/**
 * BC:
 * In v2.7.0 the theme removed the `hello_elementor_body_open()` from `header.php` replacing it with `wp_body_open()`.
 * The following code prevents fatal errors in child themes that still use this function.
 */
if ( ! function_exists( 'hello_elementor_body_open' ) ) {
	function hello_elementor_body_open() {
		wp_body_open();
	}
}

require HELLO_THEME_PATH . '/theme.php';

HelloTheme\Theme::instance();

function personel_register_form() {
    if (is_user_logged_in()) {
        return '<div class="personel-info">Sudah login. <a href="' . wp_logout_url() . '">Logout</a></div>';
    }

    ob_start();
    ?>
    <div class="personel-register-container">
        <div class="personel-register-form">
            <h2>Registrasi Personel</h2>
            <p class="form-subtitle">Isi data dengan lengkap!</p>
            
            <?php if (isset($_GET['message'])): ?>
                <div class="alert alert-<?php echo $_GET['success'] == '1' ? 'success' : 'error'; ?>">
                    <?php echo urldecode($_GET['message']); ?>
                </div>
            <?php endif; ?>
            
            <form method="post" enctype="multipart/form-data" id="personelRegister">
                <input type="hidden" name="personel_register" value="1">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="nama_lengkap" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Panggilan (untuk kode nama, 1 kata saja) <span class="required">*</span> <span id="username-status"></span></label>
                        <input type="text" name="nama_panggilan" id="namaPanggilan" required maxlength="30">
                        
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Email <span class="required">*</span></label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Password <span class="required">*</span></label>
                        <input type="password" name="password" required minlength="8">
                        <small>Minimal 8 karakter</small>
                    </div>
                </div>

                <div class="form-group full">
                    <label>Foto Profil</label>
                    <input type="file" name="foto_profil" accept="image/jpeg,image/png,image/webp">
                    <small>Max 2MB, JPG/PNG/WEBP</small>
                </div>
				<h2>
					Biodata
				</h2>
                <div class="form-row">
                    <div class="form-group">
                        <label>No. HP</label>
                        <input type="tel" name="no_hp">
                    </div>
                    <div class="form-group">
						<label>Tanggal Lahir <span class="required">*</span></label>
						<input type="date" name="tanggal_lahir" required>
					</div>
                </div>

                <div class="form-row">
                    <div class="form-group full" style="margin-bottom: 20px;">
                        <label>Domisili Provinsi 1 <span class="required">*</span></label>
                        <select name="provinsi_1" id="domisili_provinsi_1" class="lx-select2" required style="width:100%;">
                            <option value="">Memuat data provinsi...</option>
                        </select>
                        <input type="hidden" name="nama_provinsi_1" id="nama_provinsi_1">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group full">
                        <label>Kota/Kabupaten 1 <span class="required">*</span> (Maksimal 3)</label>
                        <select name="kota_kabupaten_1[]" id="domisili_kota_1" class="lx-select2-multi" multiple="multiple" required style="width:100%;">
                            <option value="">Pilih provinsi terlebih dahulu</option>
                        </select>
                    </div>
                </div>

                <div id="domisili_provinsi_2_wrap" style="display:none;">
                    <div class="form-row">
                        <div class="form-group full" style="margin-bottom: 20px;">
                            <label>Domisili Provinsi 2 (Opsional)</label>
                            <select name="provinsi_2" id="domisili_provinsi_2" class="lx-select2" style="width:100%;">
                                <option value="">Pilih Provinsi</option>
                            </select>
                            <input type="hidden" name="nama_provinsi_2" id="nama_provinsi_2">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group full">
                            <label>Kota/Kabupaten 2 (Maksimal 3)</label>
                            <select name="kota_kabupaten_2[]" id="domisili_kota_2" class="lx-select2-multi" multiple="multiple" style="width:100%;">
                                <option value="">Pilih provinsi terlebih dahulu</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="button" id="btn-tambah-provinsi" class="btn-tambah-provinsi" style="margin-bottom:15px;">+ Tambah Provinsi Lain</button>
				<h2>
					Data Pekerjaan
				</h2>
                    <div class="form-group full">
						<label>Posisi <span class="required">*</span> (Pilih satu atau lebih)</label>
						<div class="checkbox-group">
							<label><input type="checkbox" name="posisi[]" value="F"> 📸 Fotografer</label>
							<label><input type="checkbox" name="posisi[]" value="V"> 🎥 Videografer</label>
							<label><input type="checkbox" name="posisi[]" value="D"> 🚁 Drone</label>
							<label><input type="checkbox" name="posisi[]" value="E"> ✂️ Editor</label>
							<label><input type="checkbox" name="posisi[]" value="X"> 🔮 VFX</label>
							<label><input type="checkbox" name="posisi[]" value="A"> 🎭 Animator</label>
							<label><input type="checkbox" name="posisi[]" value="P"> 🤖 AI Artist - Prompt Engineer</label>
						</div>
						<small id="selected-posisi">Belum ada posisi dipilih</small>
					</div>
                <div class="form-row">
                   
                    <div class="form-group">
                        <label>Deskripsi Diri</label>
                        <textarea name="deskripsi" rows="4" placeholder="Pengalaman 5 tahun..."></textarea>
                    </div>
                </div>
				<div class="form-group full">
					<label>Link Portofolio Eksternal (Max 5)</label>
					<div id="porto-link-container">
						<div class="porto-link-item" style="display: flex; gap: 10px; margin-bottom: 10px;">
							<input type="url" name="porto_links[]" placeholder="https://linkweb.com/" style="flex: 1;">
							<button type="button" class="btn-remove-link" style="display:none; background:#d63638; color:#fff; border:none; padding:0 10px; border-radius:4px; cursor:pointer;">&times;</button>
						</div>
					</div>
					<button type="button" id="btn-add-link" class="button button-small" style="margin-top: 5px;">+ Tambah Link</button>
					<small style="display:block; margin-top:5px;">Contoh: GDrive, Behance, Adobe Portfolio, dsb.</small>
				</div>
				<div class="form-row">
					<div class="form-group">
						<label>Upload CV (PDF) <span class="required">*</span></label>
						<input type="file" name="cv_file" accept="application/pdf">
						<small>Format: PDF. Max 2MB.</small>
					</div>

					<div class="form-group">
						<label>Upload Sertifikat (Bisa pilih banyak sekaligus)</label>
						<input type="file" name="sertifikat_files[]" accept="image/jpeg,image/png,image/webp" multiple>
						<small>Pilih satu atau lebih gambar. JPG/PNG/WEBP.</small>
						<div id="file-list-preview" style="margin-top: 5px; font-size: 11px; color: #d4af37;"></div>
					</div>
				</div>

				<script>
				// Opsional: Script untuk memberi tahu user berapa file yang dipilih
				document.querySelector('input[name="sertifikat_files[]"]').addEventListener('change', function(e) {
					let list = document.getElementById('file-list-preview');
					list.innerHTML = e.target.files.length + " file sertifikat terpilih.";
				});
				</script>

                <div class="form-row">
                    <div class="form-group">
                        <label>Peralatan</label>
                        <textarea name="peralatan" rows="4" placeholder="Canon R5, DJI Mavic 3, dll"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Pricelist/hari <span class="required">*</span></label>
                        <select name="pricelist_perhari" required>
                            <option value="">Pilih Range</option>
                            <option value="dibawah_1jt">💰 Dibawah 1 jt</option>
                            <option value="1jt_3jt">💎 1 jt - 3 jt</option>
                            <option value="diatas_3jt">⭐ Diatas 3 jt</option>
                        </select>
                    </div>
                </div>

                <div class="form-group full">
                    <label>Pricelist Detail</label>
                    <?php wp_editor('', 'pricelist', [
                        'textarea_name' => 'pricelist',
                        'textarea_rows' => 6,
                        'media_buttons' => false,
                        'teeny' => true
                    ]); ?>
                </div>

                <div class="form-group full">
                    <label>Social Media</label>
                    <div class="social-row">
                        <input type="url" name="facebook" placeholder="facebook.com/username">
                        <input type="url" name="instagram" placeholder="instagram.com/username">
                    </div>
                    <div class="social-row">
                        <input type="url" name="tiktok" placeholder="tiktok.com/@username">
                        <input type="url" name="thread" placeholder="threads.net/@username">
                    </div>
                    <input type="url" name="youtube" placeholder="youtube.com/@username">
                </div>

                <!-- GANTI field tag di form -->
<div class="form-group full">
    <label>Tag</label>
    <div class="tag-input-container">
        <div class="tag-input-wrapper" id="tagInputWrapper">
            <input type="text" id="tagInput" placeholder="Ketik tag, tekan spasi/koma/enter...">
        </div>
        <input type="hidden" name="tag" id="tagHiddenInput" value="">
    </div>
    <small>Pisahkan dengan Enter. Max 10 tags.</small>
</div>
<input type="hidden" name="personel_nonce" value="<?php echo wp_create_nonce('personel_register'); ?>">
                <button type="submit" class="btn-submit-gold">
                    📤 Kirim Pendaftaran
                </button>
            </form>
        </div>
    </div>

    <script>
  document.getElementById('namaPanggilan').addEventListener('blur', function() {
    const rawValue = this.value.trim(); // Ambil nilai asli untuk cek spasi
    const username = rawValue.toLowerCase().replace(/[^a-z0-9]/g, '');
    const statusEl = document.getElementById('username-status');
    const submitBtn = document.querySelector('button[type="submit"]');
    
    // 1. Validasi: Tidak boleh kosong
    if (rawValue === "") {
        statusEl.innerHTML = "";
        return;
    }

    // 2. Validasi: Harus 1 kata (tidak boleh ada spasi di tengah)
    if (rawValue.includes(' ')) {
        statusEl.innerHTML = '<span style="color:#e74c3c;">❌ Hanya boleh 1 kata (tanpa spasi)</span>';
        submitBtn.disabled = true;
        return;
    }

    // 3. Validasi: Minimal 3 karakter
    if (username.length < 3) {
        statusEl.innerHTML = '<span style="color:orange;">Min 3 karakter</span>';
        submitBtn.disabled = true;
        return;
    }
    
    // Jika lolos validasi lokal, baru jalankan Fetch AJAX
    statusEl.innerHTML = '<span>Mengecek...</span>';
    
    fetch('', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'nama_panggilan_ajax=' + encodeURIComponent(username)
    }).then(r=>r.json()).then(data => {
        statusEl.innerHTML = data.available ? 
            '<span style="color:#27ae60;">✅ Username tersedia</span>' : 
            '<span style="color:#e74c3c;">❌ Username sudah dipakai</span>';
        submitBtn.disabled = !data.available;
    }).catch(() => {
        statusEl.innerHTML = '<span style="color:orange;">Cek koneksi</span>';
    });
});
		
		// Tambah di akhir script form
document.querySelectorAll('input[name="posisi[]"]').forEach(cb => {
    cb.addEventListener('change', function() {
        const checked = document.querySelectorAll('input[name="posisi[]"]:checked');
        const count = checked.length;
        const labels = Array.from(checked).map(cb => cb.nextElementSibling.textContent.trim());
        
        document.getElementById('selected-posisi').textContent = 
            count ? `✅ Dipilih: ${labels.join(', ')} (${count} posisi)` : 'Belum ada posisi dipilih';
        
        // Minimal 1 posisi
        document.querySelector('button[type="submit"]').disabled = count === 0;
    });
});
		
		// TAG SYSTEM - WordPress Style
(function() {
    const tagInput = document.getElementById('tagInput');
    const wrapper = document.getElementById('tagInputWrapper');
    const hiddenInput = document.getElementById('tagHiddenInput');
    const maxTags = 10;
    let tags = [];

    function addTag(text) {
        if (tags.length >= maxTags || text.trim().length < 2) return;
        
        const tag = text.trim().toLowerCase().replace(/[^a-z0-9\s]/g, '');
        if (tags.includes(tag) || tag.length < 2) return;
        
        tags.push(tag);
        renderTags();
        tagInput.value = '';
        updateHiddenInput();
    }

    function removeTag(index) {
        tags.splice(index, 1);
        renderTags();
        updateHiddenInput();
    }

    function renderTags() {
        wrapper.innerHTML = '';
        tags.forEach((tag, index) => {
            const tagEl = document.createElement('div');
            tagEl.className = 'tag-item';
            tagEl.innerHTML = `
                #${tag}
                <button type="button" class="tag-remove" onclick="removeTag(${index})">&times;</button>
            `;
            wrapper.appendChild(tagEl);
        });
        wrapper.appendChild(tagInput);
    }

    function updateHiddenInput() {
        hiddenInput.value = tags.join(',');
    }

    // Events
    tagInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ',' || e.key === ' ') {
            e.preventDefault();
            addTag(this.value);
        }
    });

    tagInput.addEventListener('blur', function() {
        if (this.value.trim()) {
            addTag(this.value);
        }
    });

    // Global removeTag function
    window.removeTag = function(index) {
        removeTag(index);
    };
})();
    </script>
<script>
jQuery(document).ready(function($) {
    var maxLinks = 5;
    var container = $('#porto-link-container');
    var addButton = $('#btn-add-link');

    // Tambah Link
    addButton.on('click', function() {
        var linkCount = container.find('.porto-link-item').length;
        
        if (linkCount < maxLinks) {
            var newField = container.find('.porto-link-item').first().clone();
            newField.find('input').val(''); // Kosongkan value
            newField.find('.btn-remove-link').show(); // Munculkan tombol hapus
            container.append(newField);
        }

        if (container.find('.porto-link-item').length === maxLinks) {
            addButton.hide(); // Sembunyikan tombol tambah jika sudah 5
        }
    });

    // Hapus Link
    container.on('click', '.btn-remove-link', function() {
        $(this).parent('.porto-link-item').remove();
        addButton.show(); // Munculkan kembali tombol tambah
    });
});
</script>
<style>
[type="button"], [type="submit"], button {
  background-color: #c97925;
  border: 1px solid #cd6407;
  border-radius: 3px;
  color: #fff;
  display: inline-block;
  font-size: 1rem;
  font-weight: 400;
  padding: .5rem 1rem;
  text-align: center;
  transition: all .3s;
  -webkit-user-select: none;
  -moz-user-select: none;
  user-select: none;
  white-space: nowrap;
}</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
.select2-container--default .select2-selection--single,
.select2-container--default .select2-selection--multiple {
    background-color: #1a1a1a !important;
    border: 1px solid #333 !important;
    border-radius: 4px;
    padding: 5px;
    min-height: 42px;
}
.select2-container--default .select2-selection--single .select2-selection__rendered,
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    color: #fff !important;
}
.select2-container--default .select2-results__option {
    background-color: #1a1a1a;
    color: #fff;
}
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #d4af37 !important;
    color: #000 !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #d4af37 !important;
    border: none;
    color: #000 !important;
    font-weight: bold;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #000 !important;
    margin-right: 5px;
}
.select2-container--default .select2-selection--multiple .select2-selection__placeholder {
    margin-top: 0;
    line-height: 32px;
}
.select2-container--default .select2-selection--multiple .select2-search--inline {
    width: 0;
    opacity: 0;
    overflow: hidden;
}
.select2-container--default.select2-container--open .select2-selection--multiple .select2-search--inline,
.select2-container--default .select2-selection--multiple:has(.select2-selection__choice) .select2-search--inline {
    width: auto;
    opacity: 1;
}
.form-row {
    overflow: visible !important;
}
.form-group.full {
    overflow: visible !important;
}
.btn-tambah-provinsi {
    background: transparent;
    color: #d4af37;
    border: 1px dashed #d4af37;
    cursor: pointer;
    padding: 6px 14px;
    border-radius: 4px;
    font-size: 0.9rem;
}
.btn-tambah-provinsi:hover {
    background: rgba(212, 175, 55, 0.1);
}
</style>

<script>
jQuery(document).ready(function($) {
    function initProvKota(provSelector, kotaSelector, hiddenSelector, ajaxUrl, isRequired) {
        var $prov = $(provSelector);
        var $kota = $(kotaSelector);
        var $hidden = $(hiddenSelector);

        var opts = { placeholder: "Pilih Provinsi", width: '100%' };
        if (!isRequired) opts.allowClear = true;
        $prov.select2(opts);

        $kota.select2({
            placeholder: "Pilih maksimal 3 Kota/Kabupaten",
            maximumSelectionLength: 3,
            width: '100%'
        });

        $prov.on('change', function() {
            var provId = $(this).val();
            var provName = $(this).find(':selected').data('name');
            $hidden.val(provName || '');

            $kota.html('<option value="">Memuat data kota...</option>').prop('disabled', true);

            if (provId) {
                $.getJSON(ajaxUrl, { action: 'fetch_wilayah', type: 'regencies', prov_id: provId })
                    .done(function(regencies) {
                        var options = '';
                        regencies.forEach(function(reg) {
                            options += '<option value="' + reg.name + '">' + reg.name + '</option>';
                        });
                        $kota.html(options).prop('disabled', false).trigger('change');
                    })
                    .fail(function() {
                        $kota.html('<option value="">Gagal memuat data</option>').prop('disabled', false);
                    });
            } else {
                $kota.html('<option value="">Pilih provinsi terlebih dahulu</option>').prop('disabled', true);
            }
        });
    }

    var ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';
    var $prov1 = $('#domisili_provinsi_1');

    initProvKota('#domisili_provinsi_1', '#domisili_kota_1', '#nama_provinsi_1', ajaxUrl, true);
    initProvKota('#domisili_provinsi_2', '#domisili_kota_2', '#nama_provinsi_2', ajaxUrl, false);

    $.getJSON(ajaxUrl, { action: 'fetch_wilayah', type: 'provinces' })
        .done(function(provinces) {
            var options = '<option value="">Pilih Provinsi</option>';
            provinces.forEach(function(prov) {
                options += '<option value="' + prov.id + '" data-name="' + prov.name + '">' + prov.name + '</option>';
            });
            $('#domisili_provinsi_1, #domisili_provinsi_2').html(options).trigger('change');
        })
        .fail(function() {
            $('#domisili_provinsi_1, #domisili_provinsi_2').html('<option value="">Gagal memuat data</option>');
        });

    $('#btn-tambah-provinsi').on('click', function() {
        $('#domisili_provinsi_2_wrap').slideDown();
        $(this).hide();
    });
});
</script>
    <?php
    return ob_get_clean();
}
add_shortcode('personel_register', 'personel_register_form');

add_action('init', 'handle_personel_register');
function handle_personel_register() {
    if (!isset($_POST['personel_register']) || !wp_verify_nonce($_POST['personel_nonce'] ?? '', 'personel_register')) {
        return;
    }
    
    global $wpdb;
    $table_name = 'wp9y_personel';

    // 1. VALIDASI
    $errors = [];
	
	// 1. WAJIB: Panggil file yang dibutuhkan WordPress
require_once( ABSPATH . 'wp-admin/includes/file.php' );
require_once( ABSPATH . 'wp-admin/includes/media.php' );
require_once( ABSPATH . 'wp-admin/includes/image.php' );

// 2. Proses CV (Single PDF)
$cv_url = '';
if (!empty($_FILES['cv_file']['name'])) {
    $cv_upload = wp_handle_upload($_FILES['cv_file'], array('test_form' => false));
    if (isset($cv_upload['url'])) {
        $cv_url = $cv_upload['url'];
    }
}

// 3. Proses Sertifikat (Multiple Images)
$sertifikat_urls = array();
if (!empty($_FILES['sertifikat_files']['name'][0])) {
    $files = $_FILES['sertifikat_files'];
    
    foreach ($files['name'] as $key => $value) {
        if ($files['name'][$key]) {
            $file = array(
                'name'     => $files['name'][$key],
                'type'     => $files['type'][$key],
                'tmp_name' => $files['tmp_name'][$key],
                'error'    => $files['error'][$key],
                'size'     => $files['size'][$key]
            );

            // Gunakan 'test_form' => false karena kita upload dari frontend
            $upload = wp_handle_upload($file, array('test_form' => false));
            if (isset($upload['url'])) {
                $sertifikat_urls[] = $upload['url'];
            }
        }
    }
}

// Gabungkan array menjadi string JSON untuk database
$sertifikat_json = json_encode($sertifikat_urls);
    
    // Username dari nama_panggilan
    $nama_panggilan = sanitize_user($_POST['nama_panggilan']);
    if (strlen($nama_panggilan) < 3) $errors[] = 'Nama panggilan minimal 3 karakter';
    
    // Email & Password
    $email = sanitize_email($_POST['email']);
    if (!is_email($email)) $errors[] = 'Email tidak valid';
    if (strlen($_POST['password']) < 8) $errors[] = 'Password minimal 8 karakter';
    
    // Check duplicate
    if ($wpdb->get_var($wpdb->prepare("SELECT id FROM $table_name WHERE username = %s", $nama_panggilan))) {
        $errors[] = 'Nama panggilan sudah terdaftar';
    }
    if ($wpdb->get_var($wpdb->prepare("SELECT id FROM $table_name WHERE email = %s", $email))) {
        $errors[] = 'Email sudah terdaftar';
    }
    
    // Posisi minimal 1
    $posisi_array = isset($_POST['posisi']) ? array_map('sanitize_text_field', (array)$_POST['posisi']) : [];
    if (empty($posisi_array)) $errors[] = 'Pilih minimal 1 posisi';
    
    // Tag
    $tags = sanitize_text_field($_POST['tag'] ?? '');
    
    if ($errors) {
        wp_redirect(add_query_arg(['message' => urlencode(implode(' | ', $errors)), 'success' => '0'], wp_get_referer()));
        exit;
    }
	
	// Ambil array porto_links
	$porto_links = isset($_POST['porto_links']) ? $_POST['porto_links'] : [];

	// Bersihkan link yang kosong dan sanitize
	$clean_links = array_filter(array_map('esc_url_raw', $porto_links));

	// Batasi paksa maksimal 5 (untuk keamanan backend)
	$final_links = array_slice($clean_links, 0, 5);

	// Simpan sebagai JSON
	$porto_links_json = json_encode($final_links);
	
	// Tangkap data dari form
	$tanggal_lahir = isset($_POST['tanggal_lahir']) ? sanitize_text_field($_POST['tanggal_lahir']) : '';

	$domisili_parts = [];
	for ($i = 1; $i <= 2; $i++) {
	    $prov = $_POST["nama_provinsi_$i"] ?? '';
	    $kota = isset($_POST["kota_kabupaten_$i"]) ? array_filter(array_map('sanitize_text_field', (array)$_POST["kota_kabupaten_$i"])) : [];
	    if (!empty($prov) && !empty($kota)) {
	        $domisili_parts[] = sanitize_text_field($prov) . ' - ' . implode(', ', $kota);
	    }
	}
	$domisili_value = implode(' || ', $domisili_parts);

	// Masukkan ke dalam $wpdb->insert atau $wpdb->update
	// 'porto_links' => $porto_links_json,
$nama_depan = strtok($nama_panggilan, ' '); 
    // 2. PROCESS DATA
    $data = [
        'username' => $nama_panggilan,
        'email' => $email,
        'password' => wp_hash_password($_POST['password']),
        'nama_lengkap' => sanitize_text_field($_POST['nama_lengkap']),
        'nama_panggilan' => $nama_depan,
        'no_hp' => sanitize_text_field($_POST['no_hp']),
        'tanggal_lahir' => $tanggal_lahir,
        'domisili' => $domisili_value,
        'posisi' => implode(',', array_unique($posisi_array)), // F,D,E
        'cv_url' => $cv_url,
        'sertifikat_multiple' => $sertifikat_json,
        'deskripsi' => sanitize_textarea_field($_POST['deskripsi']),
        'peralatan' => sanitize_textarea_field($_POST['peralatan']),
        'pricelist_perhari' => sanitize_text_field($_POST['pricelist_perhari']),
        'pricelist' => wp_kses_post($_POST['pricelist']),
        'facebook' => esc_url_raw($_POST['facebook'] ?? ''),
        'instagram' => esc_url_raw($_POST['instagram'] ?? ''),
        'tiktok' => esc_url_raw($_POST['tiktok'] ?? ''),
        'thread' => esc_url_raw($_POST['thread'] ?? ''),
        'youtube' => esc_url_raw($_POST['youtube'] ?? ''),
        'tag' => $tags, // wedding,bandung,drone
        'status' => 'pending',
		'porto_links' => $porto_links_json
    ];

    // 3. KODE NAMA: 0001-FDE
    $last_kode = $wpdb->get_var("
    SELECT kode_nama 
    FROM $table_name 
    ORDER BY id DESC 
    LIMIT 1
");

$last_number = 0;

if ($last_kode) {
    // Ambil angka sebelum tanda -
    preg_match('/^(\d+)/', $last_kode, $matches);

    if (!empty($matches[1])) {
        $last_number = (int)$matches[1];
    }
}

$new_number = $last_number + 1;

$kode_letters = strtoupper(implode('', array_unique($posisi_array)));

$data['kode_nama'] = sprintf('%04d-%s', $new_number, $kode_letters);

    // 4. UPLOAD FOTO PROFIL
   $foto_profil = '';

if (!empty($_FILES['foto_profil']['name'])) {
    require_once(ABSPATH . 'wp-admin/includes/file.php');

    $file = $_FILES['foto_profil'];
    $file_name = $file['name'];
    $file_size = $file['size'];
    
    // 1. Ambil Ekstensi File
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    // 2. Tentukan Aturan Validasi
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
    $max_size = 2 * 1024 * 1024; // 2MB dalam bytes

    // 3. Proses Cek Validasi
    if (!in_array($file_ext, $allowed_extensions)) {
        // Jika format salah, berikan pesan error atau hentikan proses
        wp_die("Error: Format file .$file_ext tidak diizinkan. Gunakan JPG, PNG, atau WEBP.");
    }

    if ($file_size > $max_size) {
        // Jika ukuran lebih dari 2MB
        wp_die("Error: Ukuran file terlalu besar. Maksimal adalah 2MB.");
    }

    // 4. Jika Lolos Validasi, Baru Upload
    $upload = wp_handle_upload($file, ['test_form' => false]);
    
    if (!isset($upload['error'])) {
        $foto_profil = $upload['url'];
    } else {
        // Opsional: Tampilkan error jika upload gagal karena alasan lain
        wp_die("Upload Error: " . $upload['error']);
    }
}

// Gunakan foto baru jika ada, jika tidak ada tetap gunakan nilai sebelumnya (atau kosong)
if (!empty($foto_profil)) {
    $data['foto_profil'] = $foto_profil;
}

    // 5. SAVE DATABASE
    $result = $wpdb->insert($table_name, $data);
    
    // GANTI bagian akhir fungsi handle_personel_register()
if ($result) {
    error_log('Personel registered: ' . $nama_panggilan . ' ID: ' . $wpdb->insert_id);
    
    // REDIRECT KE THANK YOU PAGE + kirim kode_nama
    $thank_you_url = 'https://profesional-indonesia.com/terima-kasih/?kode=' . urlencode($data['kode_nama']);
    wp_redirect($thank_you_url);
    exit;
} else {
    // Error balik ke form
    $db_error = $wpdb->last_error ?: 'unknown error';
    error_log('Personel registration FAILED: ' . $db_error . ' | DATA: ' . print_r($data, true));
    wp_redirect(add_query_arg([
        'message' => urlencode('❌ Gagal: ' . $db_error),
        'success' => '0'
    ], wp_get_referer()));
    exit;
}
    
    wp_redirect(add_query_arg([
        'message' => urlencode($message),
        'success' => $result ? '1' : '0'
    ], wp_get_referer()));
    exit;
}
// Shortcode untuk thank you page
function personel_thankyou_display() {
    if (!isset($_GET['kode'])) {
        return '<p>Terima kasih telah mendaftar!</p>';
    }
    
    global $wpdb;
    $kode = sanitize_text_field($_GET['kode']);
    $personel = $wpdb->get_row($wpdb->prepare(
        "SELECT nama_panggilan, kode_nama, status FROM wp9y_personel WHERE kode_nama = %s", 
        $kode
    ));
    
    if (!$personel) {
        return '<p>Data tidak ditemukan.</p>';
    }
    
    ob_start();
    ?>
    <div class="thankyou-message">
        <h2>🎉 Pendaftaran Berhasil!</h2>
        <div class="personel-info-card">
            <p><strong>Halo, <?php echo esc_html($personel->nama_panggilan); ?>!</strong></p>
            <div class="kode-display">
                <label>Kode Personel:</label>
                <div class="kode-value"><?php echo esc_html($personel->kode_nama); ?></div>
            </div>
            <p><strong>Status:</strong> 
                <span class="status-badge status-<?php echo $personel->status; ?>">
                    <?php echo ucfirst($personel->status); ?>
                </span>
            </p>
            <p>✅ Data sedang diproses admin.</p>
        </div>
    </div>

 <style>
    /* Container Utama */
    .thankyou-message { 
        text-align: center; 
        max-width: 500px; 
        margin: 50px auto; 
        font-family: 'Inter', sans-serif;
    }

    /* Kartu Informasi Personel */
    .personel-info-card { 
        background: linear-gradient(145deg, #1a1a1a 0%, #0a0a0a 100%); 
        padding: 40px; 
        border-radius: 20px; 
        color: white; 
        border: 1px solid rgba(212, 175, 55, 0.3); /* Border Emas Tipis */
        box-shadow: 0 20px 50px rgba(0,0,0,0.5), 0 0 20px rgba(212, 175, 55, 0.1);
        position: relative;
        overflow: hidden;
    }

    /* Efek kilau emas di kartu */
    .personel-info-card::before {
        content: "";
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(212,175,55,0.05) 0%, transparent 70%);
        pointer-events: none;
    }

    .kode-display { 
        margin: 30px 0; 
    }

    .kode-display label { 
        font-size: 12px; 
        color: #d4af37; /* Warna Emas */
        text-transform: uppercase; 
        display: block; 
        margin-bottom: 10px; 
        letter-spacing: 2px;
        font-weight: 600;
    }

    /* Tampilan Kode/ID */
    .kode-value { 
        background: rgba(255, 255, 255, 0.03); 
        padding: 20px 25px; 
        border-radius: 12px; 
        font-size: 28px; 
        font-weight: 800; 
        letter-spacing: 4px; 
        color: #ffffff;
        border: 1px solid rgba(212, 175, 55, 0.2);
        backdrop-filter: blur(10px);
        box-shadow: inset 0 0 15px rgba(212, 175, 55, 0.05);
        display: inline-block;
        min-width: 200px;
    }

    /* Badge Status */
    .status-badge { 
        display: inline-block;
        padding: 8px 20px; 
        border-radius: 50px; 
        font-size: 12px; 
        font-weight: 700; 
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Status Pending khusus tema Emas Gelap */
    .status-pending { 
        background: linear-gradient(135deg, #8a6d3b 0%, #d4af37 100%); 
        color: #000; /* Teks hitam agar kontras dengan emas */
        box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
    }
    
    .thankyou-message h2 {
        color: #d4af37;
        margin-bottom: 10px;
    }
    
    .thankyou-message p {
        color: #a0a0a0;
        font-size: 14px;
        line-height: 1.6;
    }
</style>
    <?php
    return ob_get_clean();
}
add_shortcode('personel_thankyou', 'personel_thankyou_display');

add_action('admin_menu', 'personel_admin_menu');
function personel_admin_menu() {
    add_menu_page(
        'Personel',
        'Personel', 
        'manage_options',
        'personel-admin',
        'personel_admin_page',
        'dashicons-groups',
        30
    );
}

add_action('admin_menu', 'personel_admin_menu_porto');
function personel_admin_menu_porto() {
    add_submenu_page(
        'personel-admin',      // Parent slug (slug menu utama Personel)
        'Manage Portofolio',   // Page Title
        'Portofolio Foto',     // Menu Title
        'manage_options',      // Capability
        'personel-porto',      // Menu Slug
        'personel_porto_admin_page' // Function
    );
}
add_action('admin_menu', 'personel_admin_menu_video');
function personel_admin_menu_video() {
    add_submenu_page(
        'personel-admin',
        'Manage Portofolio Video',
        'Portofolio Video',
        'manage_options',
        'personel-video',
        'personel_video_admin_page'
    );
}
add_action('admin_enqueue_scripts', 'personel_enqueue_datatables');
function personel_enqueue_datatables($hook) {
    // Debug: echo $hook; // Aktifkan ini jika ingin melihat nama hook yang tepat
    
    // Daftar halaman di mana DataTables boleh dimuat
    $allowed_pages = [
        'toplevel_page_personel-admin',      // Menu Utama Personel
        'personel_page_personel-porto',       // Sub-Menu Portofolio
		'personel_page_personel-video',
    ];

    if (!in_array($hook, $allowed_pages)) {
        return;
    }

    // CSS DataTables
    wp_enqueue_style('datatables-css', 'https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css');
    
    // JS DataTables (Wajib load jQuery dulu)
    wp_enqueue_script('datatables-js', 'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', array('jquery'), null, true);
}

function personel_admin_page() {
    global $wpdb;
    $table_name = 'wp9y_personel';
    
    // Handle actions (Keep your existing logic here)
    if (isset($_POST['action']) && wp_verify_nonce($_POST['_wpnonce'], 'personel_action')) {
        if ($_POST['action'] == 'approve' && isset($_POST['personel_id'])) {
            $wpdb->update($table_name, ['status' => 'approved'], ['id' => intval($_POST['personel_id'])]);
            echo '<div class="notice notice-success"><p>✅ Approved!</p></div>';
        }
       if ($_POST['action'] == 'delete' && isset($_POST['personel_id'])) {
    $personel_id = intval($_POST['personel_id']);

    // 1. Ambil data personel untuk menghapus file fisik foto profil
    $personel = $wpdb->get_row($wpdb->prepare("SELECT foto_profil FROM $table_name WHERE id = %d", $personel_id));
    if ($personel && $personel->foto_profil) {
        wp_delete_file($personel->foto_profil);
    }

    // 2. Hapus semua data portofolio FOTO terkait
    $wpdb->delete('wp9y_portofolio', ['personel_id' => $personel_id], ['%d']);

    // 3. Hapus semua data portofolio VIDEO terkait
    $wpdb->delete('wp9y_portofolio_video', ['personel_id' => $personel_id], ['%d']);

    // 4. Hapus data PERSONEL utama
    $wpdb->delete($table_name, ['id' => $personel_id], ['%d']);

    // Tampilkan notifikasi sukses
    echo '<div class="notice notice-success"><p>✅ Personel beserta seluruh portofolio foto dan videonya berhasil dihapus permanen!</p></div>';
}
    }

    // Handle draft actions (approve/reject profile modifications)
    if (isset($_POST['draft_action']) && wp_verify_nonce($_POST['draft_nonce'] ?? '', 'personel_draft_action')) {
        $personel_id = intval($_POST['personel_id']);
        $draft_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp9y_personel_draft_edit WHERE personel_id = %d", $personel_id));
        
        if ($draft_row) {
            $draft_fields = json_decode($draft_row->draft_data, true);
            
            if ($_POST['draft_action'] == 'approve') {
                // 1. Get current personnel record to find old files to delete
                $personel = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp9y_personel WHERE id = %d", $personel_id));
                
                if ($personel) {
                    // 2. Delete old files replaced by the draft
                    $file_keys = ['foto_profil', 'cv_url'];
                    foreach ($file_keys as $key) {
                        if (!empty($draft_fields[$key]) && $draft_fields[$key] !== $personel->$key && !empty($personel->$key)) {
                            $file_path = str_replace(site_url('/'), ABSPATH, $personel->$key);
                            if (file_exists($file_path)) {
                                unlink($file_path);
                            }
                        }
                    }
                    
                    // Handle old certificate images deletion
                    if (!empty($draft_fields['sertifikat_multiple']) && $draft_fields['sertifikat_multiple'] !== $personel->sertifikat_multiple && !empty($personel->sertifikat_multiple)) {
                        $old_certs = json_decode($personel->sertifikat_multiple, true) ?: [];
                        $new_certs = json_decode($draft_fields['sertifikat_multiple'], true) ?: [];
                        
                        foreach ($old_certs as $cert_url) {
                            if (!in_array($cert_url, $new_certs)) {
                                $file_path = str_replace(site_url('/'), ABSPATH, $cert_url);
                                if (file_exists($file_path)) {
                                    unlink($file_path);
                                }
                            }
                        }
                    }
                }
                
                // 3. Hapus field yang tidak ada di tabel DB sebelum update
                unset($draft_fields['sertifikat']);

                // 4. Update main personnel record with draft fields
                $wpdb->update('wp9y_personel', $draft_fields, ['id' => $personel_id]);
                
                // 5. Delete the draft row
                $wpdb->delete('wp9y_personel_draft_edit', ['personel_id' => $personel_id]);
                
                echo '<div class="notice notice-success"><p>✅ Perubahan profil berhasil disetujui dan diperbarui!</p></div>';
                
            } elseif ($_POST['draft_action'] == 'reject') {
                // Reject: delete new files uploaded in draft
                $personel = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp9y_personel WHERE id = %d", $personel_id));
                
                if ($personel) {
                    $file_keys = ['foto_profil', 'cv_url'];
                    foreach ($file_keys as $key) {
                        if (!empty($draft_fields[$key]) && $draft_fields[$key] !== $personel->$key) {
                            $file_path = str_replace(site_url('/'), ABSPATH, $draft_fields[$key]);
                            if (file_exists($file_path)) {
                                unlink($file_path);
                            }
                        }
                    }
                    
                    // Handle certificate images deletion
                    if (!empty($draft_fields['sertifikat_multiple']) && $draft_fields['sertifikat_multiple'] !== $personel->sertifikat_multiple) {
                        $new_certs = json_decode($draft_fields['sertifikat_multiple'], true) ?: [];
                        $old_certs = json_decode($personel->sertifikat_multiple, true) ?: [];
                        
                        foreach ($new_certs as $cert_url) {
                            if (!in_array($cert_url, $old_certs)) {
                                $file_path = str_replace(site_url('/'), ABSPATH, $cert_url);
                                if (file_exists($file_path)) {
                                    unlink($file_path);
                                }
                            }
                        }
                    }
                }
                
                // Delete the draft row
                $wpdb->delete('wp9y_personel_draft_edit', ['personel_id' => $personel_id]);
                
                echo '<div class="notice notice-warning"><p>❌ Perubahan profil ditolak dan berkas draft dibersihkan.</p></div>';
            }
        }
    }
    
    if (isset($_GET['view'])) {
        personel_view_detail($_GET['view']);
        return;
    }
    
    $personels = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
    $pending_draft_ids = $wpdb->get_col("SELECT personel_id FROM wp9y_personel_draft_edit") ?: [];
    ?>
    <div class="wrap">
        <h1>👥 Data Personel</h1>
        <br>
        
        <table id="personelTable" class="wp-list-table widefat fixed striped">
    <thead>
        <tr>
            <th width="10%">Kode</th>
            <th width="8%">Foto</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Status</th>
            <th>Tanggal</th>
            <th>Rekomendasi</th>
            <th>Show Sosmed</th>
            <th width="20%">Aksi</th> </tr>
    </thead>
    <tbody>
        <?php foreach ($personels as $p): 
$nama_depan = strtok($p->nama_panggilan, ' '); 
?>
        <tr>
            <td><strong><?php echo esc_html($p->kode_nama); ?></strong></td>
            <td>
                <?php if ($p->foto_profil): ?>
                    <img src="<?php echo esc_url($p->foto_profil); ?>" width="40" height="40" style="border-radius:50%; object-fit: cover;">
                <?php endif; ?>
            </td>
            <td><strong><?php echo esc_html($nama_depan); ?></strong></td>
            <td><?php echo esc_html($p->email); ?></td>
            <td>
                <span class="status-badge status-<?php echo $p->status; ?>">
                    <?php echo ucfirst($p->status); ?>
                </span>
                <?php if (in_array($p->id, $pending_draft_ids)): ?>
                    <span style="display:block; margin-top:5px; background:#fff3cd; color:#856404; padding:2px 8px; border-radius:4px; font-size:10px; font-weight:bold; border:1px solid #ffeeba; text-align:center;">Pending Edit</span>
                <?php endif; ?>
            </td>
            <td><?php echo date('d M Y', strtotime($p->created_at)); ?></td>
            <td style="text-align:center;">
                <?php $is_active_rec = ($p->rekomendasi === 'ya'); ?>
                <button type="button" 
                        class="lx-toggle-btn <?php echo ($is_active_rec ? 'active' : ''); ?>" 
                        data-id="<?php echo $p->id; ?>" 
                        data-status="<?php echo ($is_active_rec ? 'ya' : 'tidak'); ?>">
                        <?php echo ($is_active_rec ? 'YA' : 'TIDAK'); ?>
                </button>
            </td>
            <td style="text-align:center;">
                <?php $is_show_sosmed = (intval($p->show_sosmed) !== 0); ?>
                <button type="button" 
                        class="lx-toggle-sosmed-btn <?php echo ($is_show_sosmed ? 'active' : ''); ?>" 
                        data-id="<?php echo $p->id; ?>" 
                        data-status="<?php echo ($is_show_sosmed ? 1 : 0); ?>">
                        <?php echo ($is_show_sosmed ? 'YA' : 'TIDAK'); ?>
                </button>
            </td>

            <td>
                <a href="?page=personel-admin&view=<?php echo $p->id; ?>" class="button button-small" title="Lihat">👁️</a>

                <?php if ($p->status === 'approved'): ?>
                    <button type="button" class="btn-status-toggle status-active" 
                            data-id="<?php echo $p->id; ?>" 
                            data-status="approved">NON-AKTIFKAN</button>
                <?php elseif ($p->status === 'non-aktif'): ?>
                    <button type="button" class="btn-status-toggle status-inactive" 
                            data-id="<?php echo $p->id; ?>" 
                            data-status="non-aktif">AKTIFKAN</button>
                <?php endif; ?>

                <?php if ($p->status == 'pending'): ?>
                    <form method="post" style="display:inline;">
                        <?php wp_nonce_field('personel_action'); ?>
                        <input type="hidden" name="personel_id" value="<?php echo $p->id; ?>">
                        <button type="submit" name="action" value="approve" class="button button-small button-primary">Approve</button>
                    </form>
                <?php endif; ?>

                <form method="post" style="display:inline;">
                    <?php wp_nonce_field('personel_action'); ?>
                    <input type="hidden" name="personel_id" value="<?php echo $p->id; ?>">
                    <button type="submit" name="action" value="delete" class="button button-small" 
                            onclick="return confirm('Hapus?')">🗑️</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
    </div>

    <script type="text/javascript">
    jQuery(document).ready(function($) {
       $('#personelTable').DataTable({
    "pageLength": 10,
    "responsive": true,
    "dom": '<"top"lf>rt<"bottom"ip><"clear">', // Mengatur posisi Search (f) dan Length (l)
    "language": {
        "search": "_INPUT_",
        "searchPlaceholder": "Cari data personel...",
        "lengthMenu": "Tampilkan _MENU_ data",
        "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ personel",
        "paginate": {
            "next": "Lanjut",
            "previous": "Kembali"
        }
    },
    "columnDefs": [
        { "orderable": false, "targets": [1, 6] }
    ]
});
    });
    </script>

    <style>
    /* Container Styling */
    .wrap {
        margin: 20px 20px 0 0;
    }

    /* DataTables Custom Styling */
    #personelTable_wrapper {
        background: #fff;
        padding: 20px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    #personelTable {
        border-collapse: collapse !important;
        margin: 15px 0 !important;
        width: 100% !important;
        border: none !important;
    }

    #personelTable thead th {
        background-color: #f8f9fa;
        color: #1d2327;
        font-weight: 600;
        padding: 12px 15px;
        border-bottom: 2px solid #e2e8f0 !important;
    }

    #personelTable tbody td {
        padding: 12px 15px !important;
        vertical-align: middle !important;
        border-bottom: 1px solid #f0f0f0 !important;
    }

    /* Hover effect */
    #personelTable tbody tr:hover {
        background-color: #f0f7ff !important;
        transition: 0.2s ease-in-out;
    }

    /* Search Box & Length Menu */
    .dataTables_filter input {
        border: 1px solid #ccd0d4 !important;
        border-radius: 4px !important;
        padding: 5px 10px !important;
        margin-left: 10px !important;
        width: 250px !important;
    }

    .dataTables_length select {
        border: 1px solid #ccd0d4 !important;
        border-radius: 4px !important;
        padding: 2px 5px !important;
    }

    /* Status Badges */
    .status-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .status-approved {
        background-color: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    .status-pending {
        background-color: #fef9c3;
        color: #854d0e;
        border: 1px solid #fef08a;
    }

    /* Action Buttons */
    .btn-action {
        padding: 6px 10px !important;
        border-radius: 6px !important;
        text-decoration: none !important;
        font-size: 12px !important;
    }
    
    .button-link-delete {
        color: #d63638 !important;
    }
    .button-link-delete:hover {
        color: #b32d2e !important;
        background: #fbe9e9 !important;
    }

    /* Pagination */
    .dataTables_paginate .paginate_button.current {
        background: #2271b1 !important;
        color: white !important;
        border: 1px solid #2271b1 !important;
        border-radius: 4px !important;
    }
    
    .dataTables_paginate .paginate_button:hover {
        background: #f0f0f1 !important;
        border: 1px solid #ccc !important;
        color: #2271b1 !important;
    }
</style>
    <?php
}
// Handler AJAX untuk mengubah status personel
add_action('wp_ajax_toggle_status_personel', 'lx_toggle_status_personel_handler');
function lx_toggle_status_personel_handler() {
    global $wpdb;

    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $current_status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';

    if ($id <= 0) {
        wp_send_json_error('ID tidak valid.');
    }

    // Logika pembalikan status
    $new_status = ($current_status === 'approved') ? 'non-aktif' : 'approved';

    $updated = $wpdb->update(
        'wp9y_personel',
        array('status' => $new_status),
        array('id' => $id),
        array('%s'), 
        array('%d')
    );

    if ($updated !== false) {
        wp_send_json_success(array('new_status' => $new_status));
    } else {
        wp_send_json_error('Gagal memperbarui database.');
    }
    wp_die();
}

add_action('wp_ajax_fetch_wilayah', 'proxy_fetch_wilayah');
add_action('wp_ajax_nopriv_fetch_wilayah', 'proxy_fetch_wilayah');
function proxy_fetch_wilayah() {
    $type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : '';
    $prov_id = isset($_GET['prov_id']) ? intval($_GET['prov_id']) : 0;

    if ($type === 'provinces') {
        $url = 'https://emsifa.github.io/api-wilayah-indonesia/api/provinces.json';
    } elseif ($type === 'regencies' && $prov_id) {
        $url = 'https://emsifa.github.io/api-wilayah-indonesia/api/regencies/' . $prov_id . '.json';
    } else {
        wp_send_json_error('Invalid request');
    }

    $response = wp_remote_get($url);
    if (is_wp_error($response)) {
        wp_send_json_error('Gagal mengambil data wilayah');
    }

    $body = wp_remote_retrieve_body($response);
    if (empty($body)) {
        wp_send_json_error('Data kosong');
    }

    header('Content-Type: application/json');
    echo $body;
    wp_die();
}

add_action('admin_head', 'lx_status_personel_assets');
function lx_status_personel_assets() {
    // Hanya tampilkan di halaman admin personel
    if (isset($_GET['page']) && $_GET['page'] === 'personel-admin') {
        ?>
        <style>
            /* Style umum tombol toggle status */
            .btn-status-toggle {
                padding: 5px 10px;
                border-radius: 4px;
                font-size: 10px;
                font-weight: 700;
                cursor: pointer;
                border: none;
                color: #fff !important;
                display: inline-block;
                min-width: 100px;
                text-align: center;
                transition: all 0.3s ease;
                text-transform: uppercase;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            }
            
            /* Merah untuk tombol 'Non-Aktifkan' (karena status saat ini aktif) */
            .status-active { 
                background: #d63638 !important; 
            }
            
            /* Hijau untuk tombol 'Aktifkan' (karena status saat ini non-aktif) */
            .status-inactive { 
                background: #00a32a !important; 
            }

            .btn-status-toggle:hover { 
                opacity: 0.85; 
                transform: translateY(-1px);
            }
            
            .btn-status-toggle:disabled { 
                background: #ccc !important; 
                cursor: wait; 
            }
        </style>

        <script type="text/javascript">
        jQuery(document).ready(function($) {
            $(document).on('click', '.btn-status-toggle', function(e) {
                e.preventDefault();
                var btn = $(this);
                var person_id = btn.attr('data-id');
                var current_status = btn.attr('data-status');

                // Cegah klik ganda saat proses
                btn.text('...').prop('disabled', true);

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'toggle_status_personel',
                        id: person_id,
                        status: current_status
                    },
                    success: function(response) {
                        if (response.success) {
                            var ns = response.data.new_status;
                            
                            // Update atribut data dan tampilan tombol
                            btn.attr('data-status', ns);
                            
                            if (ns === 'approved') {
                                btn.text('NON-AKTIFKAN');
                                btn.addClass('status-active').removeClass('status-inactive');
                                // Opsional: Update badge status di kolom sebelah jika ada
                                btn.closest('tr').find('.status-badge')
                                   .text('Approved').attr('class', 'status-badge status-approved');
                            } else {
                                btn.text('AKTIFKAN');
                                btn.addClass('status-inactive').removeClass('status-active');
                                btn.closest('tr').find('.status-badge')
                                   .text('Non-aktif').attr('class', 'status-badge status-non-aktif');
                            }
                        } else {
                            (function(){var d=document.createElement('div');d.style.cssText='position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:999999;display:flex;align-items:center;justify-content:center;';d.innerHTML='<div style="background:#1a1a1a;border:1px solid #ff4444;border-radius:8px;padding:30px 40px;max-width:400px;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,0.5);"><div style="font-size:48px;margin-bottom:10px;">❌</div><p style="color:#fff;font-size:16px;margin:10px 0 20px;">Gagal mengubah status.</p><button onclick="this.parentNode.parentNode.remove()" style="background:#ff4444;color:#fff;border:none;padding:10px 24px;border-radius:4px;font-weight:bold;cursor:pointer;">OK</button></div>';document.body.appendChild(d);})();
                        }
                    },
                    error: function() {
                        (function(){var d=document.createElement('div');d.style.cssText='position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:999999;display:flex;align-items:center;justify-content:center;';d.innerHTML='<div style="background:#1a1a1a;border:1px solid #ff4444;border-radius:8px;padding:30px 40px;max-width:400px;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,0.5);"><div style="font-size:48px;margin-bottom:10px;">❌</div><p style="color:#fff;font-size:16px;margin:10px 0 20px;">Koneksi server bermasalah.</p><button onclick="this.parentNode.parentNode.remove()" style="background:#ff4444;color:#fff;border:none;padding:10px 24px;border-radius:4px;font-weight:bold;cursor:pointer;">OK</button></div>';document.body.appendChild(d);})();
                    },
                    complete: function() {
                        btn.prop('disabled', false);
                    }
                });
            });
        });
        </script>
        <?php
    }
}
// VIEW DETAIL
function personel_view_detail($id) {
    global $wpdb;
    $table_name = 'wp9y_personel';
    $personel = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
    
    if (!$personel) {
        echo '<div class="notice notice-error"><p>Personel tidak ditemukan!</p></div>';
        return;
    }
	// Ambil data link porto dari kolom porto_links (format JSON)
	$external_links = json_decode($personel->porto_links, true);
	$nama_depan = strtok($personel->nama_panggilan, ' '); 
    ?>
    <div class="wrap">
        <h1>👤 Detail <?php echo esc_html($nama_depan); ?></h1>
        <a href="?page=personel-admin" class="button button-secondary" style="margin-bottom:15px;">&larr; Kembali</a>
        
        <?php
        $draft_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp9y_personel_draft_edit WHERE personel_id = %d", $id));
        if ($draft_row) {
            $draft_fields = json_decode($draft_row->draft_data, true);
            $diff = [];
            $fields_to_compare = [
                'nama_lengkap'      => 'Nama Lengkap',
                'nama_panggilan'    => 'Nama Panggilan',
                'no_hp'             => 'No HP',
                'tanggal_lahir'     => 'Tanggal Lahir',
                'domisili'          => 'Domisili',
                'sertifikat'        => 'Sertifikat (Teks)',
                'deskripsi'         => 'Deskripsi',
                'peralatan'         => 'Peralatan',
                'pricelist_perhari' => 'Pricelist Perhari',
                'pricelist'         => 'Pricelist (HTML)',
                'facebook'          => 'Facebook',
                'instagram'         => 'Instagram',
                'tiktok'            => 'Tiktok',
                'thread'            => 'Thread',
                'youtube'           => 'Youtube',
                'tag'               => 'Tags',
                'kode_nama'         => 'Kode Nama',
                'posisi'            => 'Posisi',
                'porto_links'       => 'Link Portofolio Eksternal',
                'foto_profil'       => 'Foto Profil',
                'cv_url'            => 'CV PDF',
                'sertifikat_multiple'=> 'Sertifikat (Gambar)',
            ];

            foreach ($fields_to_compare as $key => $label) {
                $old_val = isset($personel->$key) ? $personel->$key : '';
                $new_val = isset($draft_fields[$key]) ? $draft_fields[$key] : '';
                
                if ($key === 'porto_links') {
                    $old_arr = json_decode($old_val, true) ?: [];
                    $new_arr = json_decode($new_val, true) ?: [];
                    sort($old_arr);
                    sort($new_arr);
                    if ($old_arr !== $new_arr) {
                        $diff[$key] = [
                            'label' => $label,
                            'old'   => implode('<br>', array_map('esc_url', $old_arr)),
                            'new'   => implode('<br>', array_map('esc_url', $new_arr)),
                        ];
                    }
                } elseif ($key === 'sertifikat_multiple') {
                    $old_arr = json_decode($old_val, true) ?: [];
                    $new_arr = json_decode($new_val, true) ?: [];
                    sort($old_arr);
                    sort($new_arr);
                    if ($old_arr !== $new_arr) {
                        $old_imgs = '';
                        foreach ($old_arr as $img) {
                            $old_imgs .= '<img src="'.esc_url($img).'" style="max-height:60px; margin-right:5px; border-radius:4px; border:1px solid #ddd;">';
                        }
                        $new_imgs = '';
                        foreach ($new_arr as $img) {
                            $new_imgs .= '<img src="'.esc_url($img).'" style="max-height:60px; margin-right:5px; border-radius:4px; border:1px solid #d4af37;">';
                        }
                        $diff[$key] = [
                            'label' => $label,
                            'old'   => $old_imgs ?: '-',
                            'new'   => $new_imgs ?: '-',
                        ];
                    }
                } elseif ($key === 'foto_profil') {
                    if ($old_val !== $new_val) {
                        $diff[$key] = [
                            'label' => $label,
                            'old'   => $old_val ? '<img src="'.esc_url($old_val).'" style="width:60px; height:60px; border-radius:50%; object-fit:cover; border:1px solid #ddd;">' : '-',
                            'new'   => $new_val ? '<img src="'.esc_url($new_val).'" style="width:60px; height:60px; border-radius:50%; object-fit:cover; border:2px solid #00a32a;">' : '-',
                        ];
                    }
                } elseif ($key === 'cv_url') {
                    if ($old_val !== $new_val) {
                        $diff[$key] = [
                            'label' => $label,
                            'old'   => $old_val ? '<a href="'.esc_url($old_val).'" target="_blank">Lihat CV Lama</a>' : '-',
                            'new'   => $new_val ? '<a href="'.esc_url($new_val).'" target="_blank" style="color:#2271b1; font-weight:bold;">Lihat CV Baru</a>' : '-',
                        ];
                    }
                } elseif ($key === 'posisi') {
                    if ($old_val !== $new_val) {
                        $old_pos = explode(',', $old_val);
                        $new_pos = explode(',', $new_val);
                        $old_pos_labels = array_map('personel_posisi_label', $old_pos);
                        $new_pos_labels = array_map('personel_posisi_label', $new_pos);
                        $diff[$key] = [
                            'label' => $label,
                            'old'   => implode(', ', $old_pos_labels),
                            'new'   => implode(', ', $new_pos_labels),
                        ];
                    }
                } else {
                    if ($old_val !== $new_val) {
                        $diff[$key] = [
                            'label' => $label,
                            'old'   => nl2br(esc_html($old_val)),
                            'new'   => nl2br(esc_html($new_val)),
                        ];
                    }
                }
            }
            
            if (!empty($draft_fields['password'])) {
                $diff['password'] = [
                    'label' => 'Password',
                    'old'   => '********',
                    'new'   => '<span style="color:#d63638; font-weight:bold;">Password Diubah (Hashed)</span>',
                ];
            }

            if (!empty($diff)): ?>
                <div class="draft-review-box" style="background:#fff; border:1px solid #ccd0d4; border-left:4px solid #ffb900; padding:20px; border-radius:8px; margin:20px 0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <h2 style="margin-top:0; color:#1d2327;">⚠️ Peninjauan Usulan Perubahan Profil</h2>
                    <p style="color:#646970;">Personel ini mengajukan perubahan profil berikut. Selama peninjauan ini belum disetujui, profil aktif mereka di website tetap berjalan menggunakan data lama.</p>
                    
                    <table class="wp-list-table widefat fixed striped" style="margin:20px 0; border:1px solid #ccd0d4;">
                        <thead>
                            <tr>
                                <th width="20%"><strong>Nama Kolom</strong></th>
                                <th width="40%"><strong>Nilai Aktif Saat Ini</strong></th>
                                <th width="40%"><strong>Nilai Usulan Baru</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($diff as $item): ?>
                                <tr>
                                    <td><strong><?php echo esc_html($item['label']); ?></strong></td>
                                    <td style="color:#646970;"><?php echo $item['old']; ?></td>
                                    <td style="background:#f0f9ff; font-weight:500; color:#1d2327;"><?php echo $item['new']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div class="action-buttons" style="display:flex; gap:15px; align-items:center;">
                        <form method="post" style="display:inline;">
                            <?php wp_nonce_field('personel_draft_action', 'draft_nonce'); ?>
                            <input type="hidden" name="personel_id" value="<?php echo $id; ?>">
                            <button type="submit" name="draft_action" value="approve" class="button button-large button-primary" style="background:#00a32a; border-color:#00a32a; color:#fff;">Setujui Perubahan</button>
                        </form>
                        
                        <form method="post" style="display:inline;">
                            <?php wp_nonce_field('personel_draft_action', 'draft_nonce'); ?>
                            <input type="hidden" name="personel_id" value="<?php echo $id; ?>">
                            <button type="submit" name="draft_action" value="reject" class="button button-large button-secondary" style="color:#d63638; border-color:#d63638; display:inline-flex; align-items:center;" onclick="return confirm('Tolak dan hapus usulan perubahan ini?')">Tolak Perubahan</button>
                        </form>
                    </div>
                </div>
            <?php endif;
        }
        ?>
        
        <div class="personel-detail-card">
            <div class="detail-header">
                <?php if ($personel->foto_profil): ?>
                    <img src="<?php echo esc_url($personel->foto_profil); ?>" 
                         class="detail-avatar">
                <?php endif; ?>
                <div>
                    <h2><?php echo esc_html($personel->nama_lengkap); ?></h2>
                    <p class="kode-big"><?php echo esc_html($nama_depan.'-'.$personel->kode_nama); ?></p>
                    <span class="status-badge status-<?php echo $personel->status; ?>">
                        <?php echo ucfirst($personel->status); ?>
                    </span>
                </div>
            </div>
            
            <div class="detail-grid">
                <div class="detail-section">
                    <h3>📧 Kontak</h3>
                    <p><strong>Email:</strong> <?php echo esc_html($personel->email); ?></p>
                    <p><strong>HP:</strong> <?php echo esc_html($personel->no_hp ?: '-'); ?></p>
                </div>
                
                <div class="detail-section">
                    <h3>📍 Profil</h3>
                    <p>
						<strong>Tanggal Lahir:</strong> 
						<?php 
						if (!empty($personel->tanggal_lahir) && $personel->tanggal_lahir !== '0000-00-00') {
							// Format tanggal lahir (Contoh: 15 Agustus 1995)
							$tanggal_lahir_formatted = date_i18n('d F Y', strtotime($personel->tanggal_lahir));

							// Hitung umur secara otomatis
							$bday = new DateTime($personel->tanggal_lahir);
							$today = new DateTime();
							$umur = $today->diff($bday)->y;

							echo esc_html($tanggal_lahir_formatted . ' (' . $umur . ' Tahun)');
						} else {
							echo '-';
						}
						?>
					</p>
                    <p><strong>Domisili:</strong> <?php echo esc_html($personel->domisili ?: '-'); ?></p>
                    <?php if ($personel->posisi): ?>
                        <p><strong>Posisi:</strong> 
                            <?php 
                            $pos = explode(',', $personel->posisi);
                            foreach ($pos as $p) echo '<span class="pos-tag">' . personel_posisi_label($p) . '</span>';
                            ?>
                        </p>
                    <?php endif; ?>
                </div>
                
                
                
                <div class="detail-section full">
                    <h3>📝 Deskripsi</h3>
                    <div class="detail-text"><?php echo nl2br(esc_html($personel->deskripsi)); ?></div>
                </div>
				<div class="detail-section full" style="margin-top: 20px;">
					<h3>🔗 Link Portofolio Eksternal</h3>
					<div class="external-links-wrapper" style="background: #1a1a1a; padding: 15px; border-radius: 8px; border: 1px solid #333;">
						<?php if (!empty($external_links) && is_array($external_links)) : ?>
							<ul style="list-style: none; padding: 0; margin: 0;">
								<?php foreach ($external_links as $link) : ?>
									<li style="margin-bottom: 10px; display: flex; align-items: center; gap: 10px;">
										<span class="dashicons dashicons-external" style="color: #d4af37;"></span>
										<a href="<?php echo esc_url($link); ?>" target="_blank" style="color: #fff; text-decoration: none; font-size: 14px; border-bottom: 1px solid #444; padding-bottom: 2px;">
											<?php echo esc_html($link); ?>
										</a>
										
									</li>
								<?php endforeach; ?>
							</ul>
						<?php else : ?>
							<p style="color: #666; font-style: italic; margin: 0;">Tidak ada link portofolio eksternal.</p>
						<?php endif; ?>
					</div>
				</div>
                
                
				<?php if (!empty($personel->cv_url)) : ?>
					<div class="detail-section full">
						<h3>📄 Curriculum Vitae (CV)</h3>
						<a href="<?php echo esc_url($personel->cv_url); ?>" target="_blank" class="btn-download-cv">
							<span class="dashicons dashicons-pdf"></span> LIHAT / DOWNLOAD CV (PDF)
						</a>
					</div>
				<?php endif; ?>
					
					<style>
					/* CV Button */
					.btn-download-cv {
						display: inline-flex;
						align-items: center;
						background: #d4af37;
						color: #000 !important;
						padding: 10px 20px;
						border-radius: 5px;
						text-decoration: none !important;
						font-weight: bold;
						gap: 10px;
						transition: 0.3s;
					}
					.btn-download-cv:hover { background: #fff; }

					/* Sertifikat Grid */
					.sertifikat-grid {
						display: grid;
						grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
						gap: 15px;
						margin-top: 15px;
					}

					.sertifikat-item {
						aspect-ratio: 1 / 1;
						overflow: hidden;
						border-radius: 8px;
						border: 1px solid #333;
						background: #1a1a1a;
						cursor: pointer;
						transition: 0.3s;
					}

					.sertifikat-item img {
						width: 100%;
						height: 100%;
						object-fit: cover;
						transition: 0.5s;
					}

					.sertifikat-item:hover { border-color: #d4af37; }
					.sertifikat-item:hover img { transform: scale(1.1); }

					/* Mobile view */
					@media (max-width: 600px) {
						.sertifikat-grid { grid-template-columns: repeat(3, 1fr); gap: 10px; }
					}
				</style>

				<div class="detail-section full">
					<h3>🏆 Sertifikat</h3>
					<div class="sertifikat-grid">
						<?php 
						$sertifikat_data = json_decode($personel->sertifikat_multiple, true);

						if (!empty($sertifikat_data) && is_array($sertifikat_data)) :
							foreach ($sertifikat_data as $img_url) : ?>
								<div class="sertifikat-item">
									<a href="<?php echo esc_url($img_url); ?>" target="_blank" class="porto-clickable-img">
										<img src="<?php echo esc_url($img_url); ?>" alt="Sertifikat" loading="lazy">
									</a>
								</div>
							<?php endforeach; 
						else : ?>
							<p class="no-data">Belum ada sertifikat yang diunggah.</p>
						<?php endif; ?>
					</div>
				</div>
			
                
                <div class="detail-section full">
                    <h3>⚙️ Peralatan</h3>
                    <div class="detail-text"><?php echo nl2br(esc_html($personel->peralatan)); ?></div>
                </div>
                
                <?php if ($personel->pricelist): ?>
                <div class="detail-section full">
                    <h3>💰 Pricelist</h3>
                    <div class="pricelist-preview"><?php echo $personel->pricelist; ?></div>
                </div>
                <?php endif; ?>
				<div class="detail-section">
                    <h3>🌐 Sosial</h3>
                    <?php 
                    $social = ['facebook', 'instagram', 'tiktok', 'thread', 'youtube'];
                    foreach ($social as $s) {
                        if ($personel->$s) {
                            echo '<p><strong>' . ucfirst($s) . ':</strong> ';
                            echo '<a href="' . esc_url($personel->$s) . '" target="_blank">' . esc_html($personel->$s) . '</a></p>';
                        }
                    }
                    ?>
                </div>
                
                <?php if ($personel->tag): ?>
                <div class="detail-section">
                    <h3># Tags</h3>
                    <?php 
                    $tags = explode(',', $personel->tag);
                    foreach ($tags as $tag) {
                        echo '<span class="tag-badge"># ' . esc_html(trim($tag)) . '</span>';
                    }
                    ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <style>
        .personel-detail-card { max-width: 900px; margin: 30px 0; }
        .detail-header { 
            display: flex; gap: 25px; align-items: center; 
            background: #f8f9fa; padding: 30px; border-radius: 20px; margin-bottom: 30px;
        }
        .detail-avatar { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; }
        .detail-header h2 { margin: 0 0 10px 0; color: #2c3e50; }
        .kode-big { font-size: 36px; font-weight: 800; color: #e67e22; margin: 10px 0; }
        
        .detail-grid { display: grid; gap: 25px; }
        .detail-section { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
        .detail-section.full { grid-column: 1 / -1; }
        .detail-section h3 { margin-top: 0; color: #2c3e50; border-bottom: 2px solid #ecf0f1; padding-bottom: 10px; }
        .detail-text { line-height: 1.7; color: #5a6c7d; }
        .pricelist-preview { border: 1px solid #ecf0f1; padding: 20px; border-radius: 10px; max-height: 300px; overflow: auto; }
        
        .pos-tag, .tag-badge { 
            display: inline-block; padding: 6px 12px; margin: 4px 4px 4px 0; 
            border-radius: 20px; font-size: 13px; font-weight: 500; background: #3498db; color: white;
        }
        .status-badge { padding: 8px 16px; border-radius: 25px; font-weight: 600; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-approved { background: #d4edda; color: #155724; }
        
        @media (min-width: 768px) {
            .detail-grid { grid-template-columns: 1fr 1fr; }
        }
        </style>
    </div>
    <?php
}

function personel_posisi_label($code) {
  $labels = ['F'=>'Fotografer', 'V'=>'Videografer', 'D'=>'Drone', 'E'=>'Editor', 'X'=>'VFX', 'A'=>'Animator', 'P'=>'AI Artist - Prompt Engineer'];
    return $labels[$code] ?? $code;
}

//login page
add_action('init', 'personel_start_session', 1);
function personel_start_session() {
    if (!session_id()) {
        session_start();
    }
}
// Fungsi untuk cek apakah personel sudah login
function is_personel_logged_in() {
    return isset($_SESSION['personel_id']);
}

// Handler Logout
add_action('init', 'personel_logout_handler');
function personel_logout_handler() {
    if (isset($_GET['personel_action']) && $_GET['personel_action'] == 'logout') {
        
        // 1. Pastikan session menyala sebelum dihancurkan
        if (!session_id()) {
            session_start();
        }
        
        // 2. Hancurkan session kustom PHP Anda
        session_destroy();

        // 3. Logout dari sistem inti WordPress (Hapus cookie)
        wp_logout();

        // 4. Alihkan kembali ke halaman login
        wp_redirect(home_url('/login-personel')); // Pastikan slug ini sudah benar
        exit;
    }
}
add_shortcode('form_login_personel', 'personel_login_form_shortcode');

add_shortcode('form_login_personel', 'personel_login_form_shortcode');

function personel_login_form_shortcode() {
   if (is_personel_logged_in()) {
    return '<div class="personel-box login-personel-container" style="text-align:center;">
                <p style="color:white;">Anda sudah login sebagai <strong>'.$_SESSION['personel_nama'].'</strong></p>
                <a href="?personel_action=logout" class="btn-login" style="display:inline-block; text-decoration:none;">Logout</a>
            </div>';
}

global $wpdb;
$error = '';

if (isset($_POST['personel_login_submit'])) {
    $login_input = sanitize_text_field($_POST['login_user']);
    $password    = $_POST['login_password'];

    $user = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM wp9y_personel WHERE (email = %s OR username = %s) AND status = 'approved'",
        $login_input, $login_input
    ));

    if ($user && wp_check_password($password, $user->password, $user->id)) {
        $_SESSION['personel_id']    = $user->id;
        $_SESSION['personel_nama']  = $user->nama_panggilan;
        $_SESSION['personel_email'] = $user->email;
        
        // =========================================================
        // AUTO-SYNC WP LOGIN (Agar tombol Add Media muncul)
        // =========================================================
        $wp_user = get_user_by('email', $user->email); 
        
        if (!$wp_user) {
            // Jika belum punya akun bayangan di WP, buatkan!
            $new_user_id = wp_insert_user(array(
                'user_login'   => $user->username, 
                'user_email'   => $user->email,
                'user_pass'    => wp_generate_password(), 
                'display_name' => $user->nama_panggilan, 
                'role'         => 'personel'
            ));
            if (!is_wp_error($new_user_id)) {
                $wp_user = get_userdata($new_user_id);
            }
        }

        // Eksekusi Login WordPress
        if ($wp_user) {
            wp_clear_auth_cookie();
            wp_set_current_user($wp_user->ID);
            wp_set_auth_cookie($wp_user->ID);
        }
        // =========================================================

        // Alihkan ke dashboard (pakai PHP agar session tidak hilang)
        wp_safe_redirect(home_url('/dashboard-personel'));
        exit;
    } else {
        $error = 'Akses ditolak. Periksa kembali akun Anda.';
    }
}

ob_start();
?>
    <style>
        .login-personel-container { 
            max-width: 400px; 
            margin: 40px auto; 
            padding: 30px; 
            border-radius: 15px; 
            background: #1a1a1a; /* Hitam Gelap */
            border: 1px solid #c5a059; /* Border Emas Tipis */
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            font-family: 'Segoe UI', Roboto, sans-serif;
            color: #ffffff;
        }

        .login-personel-container h3 {
            text-align: center;
            color: #d4af37; /* Warna Emas */
            font-size: 24px;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .personel-form label {
            color: #f0f0f0;
            font-size: 14px;
            font-weight: 500;
        }

        .personel-form input { 
            width: 100%; 
            padding: 12px 15px; 
            margin: 8px 0 20px 0; 
            border: 1px solid #333; 
            border-radius: 8px; 
            background: #262626; 
            color: #fff;
            box-sizing: border-box;
            transition: 0.3s;
        }

        .personel-form input:focus {
            outline: none;
            border-color: #d4af37;
            box-shadow: 0 0 5px rgba(212, 175, 55, 0.3);
        }

        /* Tombol Gradasi Emas Gelap */
        .btn-login { 
            width: 100%; 
            padding: 14px; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: bold; 
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1a1a1a; /* Teks Hitam agar kontras */
            background: linear-gradient(135deg, #8a6d3b 0%, #d4af37 50%, #8a6d3b 100%);
            background-size: 200% auto;
            transition: 0.5s;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }

        .btn-login:hover { 
            background-position: right center;
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
            transform: translateY(-2px);
        }

        .error-msg {
            font-size: 13px;
            text-align: center;
            border-left: 4px solid #d4af37;
            background: #2d2311;
            color: #e5c07b;
            padding: 10px;
            margin-bottom: 20px;
        }
		/* Sembunyikan Modal saat awal */
.modal-overlay-custom {
    display: none; 
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.7); /* Background Gelap */
}

/* Box Putih di Tengah */
.modal-box-custom {
    background-color: #fff;
    margin: 10% auto;
    padding: 20px;
    border-radius: 8px;
    width: 90%;
    max-width: 400px;
    color: #333;
    position: relative;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

.modal-header-custom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #ddd;
    padding-bottom: 10px;
    margin-bottom: 15px;
}

.close-custom {
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    color: #999;
}

.form-control-custom {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

.btn-submit-custom {
    width: 100%;
    background: #6366F1; /* Warna Ungu sesuai gambar */
    color: white;
    border: none;
    padding: 12px;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 15px;
    font-weight: bold;
}

.btn-submit-custom:hover { background: #4F46E5; }
    </style>

    <div class="login-personel-container">
        <form method="post" class="personel-form">
            <h3>Personel Login</h3>
            
            <?php if ($error): ?>
                <div class="error-msg">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <p>
                <label>Username / Email</label>
                <input type="text" name="login_user" required placeholder="Email atau Username">
            </p>
            <p>
                <label>Password</label>
                <input type="password" name="login_password" required placeholder="••••••••">
            </p>
            <p style="margin-top: 10px;">
                <button type="submit" name="personel_login_submit" class="btn-login">Sign In</button>
            </p>
        </form>
		<a href="javascript:void(0);" id="forgotLink" style="font-size: 13px; color: #EAB308; cursor: pointer;">Lupa Password?</a>
		
    </div>


<div id="forgotModalCustom" class="modal-overlay-custom">
    <div class="modal-box-custom">
        <form method="post" action="">
            <div class="modal-header-custom">
                <h5 style="margin:0;">Lupa Password Personel</h5>
                <span class="close-custom" onclick="closeForgotModal()">&times;</span>
            </div>
            <div class="modal-body-custom">
                <p style="font-size: 14px; margin-bottom: 15px;">Masukkan email Anda yang terdaftar, kami akan mengirimkan link reset password.</p>
                <input type="email" name="forgot_email" class="form-control-custom" placeholder="Email Personel" required>
            </div>
            <div class="modal-footer-custom">
                <button type="submit" name="personel_forgot_submit" class="btn-submit-custom">KIRIM LINK RESET</button>
            </div>
        </form>
    </div>
</div>
<script>
(function() {
    // Fungsi ini akan berjalan otomatis
    var startModal = function() {
        var modal = document.getElementById("forgotModalCustom");
        var btn = document.getElementById("forgotLink");
        var span = document.querySelector(".close-custom");

        // Cek apakah elemen-elemennya ada
        if (btn && modal) {
            btn.onclick = function(e) {
                e.preventDefault();
                modal.style.display = "block";
            }

            if (span) {
                span.onclick = function() {
                    modal.style.display = "none";
                }
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
            console.log("Modal Password Reset Siap.");
        } else {
            console.error("Error: Tombol 'forgotLink' atau 'forgotModalCustom' tidak ditemukan di halaman.");
        }
    };

    // Jalankan saat dokumen selesai dimuat
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", startModal);
    } else {
        startModal();
    }
})();
</script>
    <?php
    return ob_get_clean();
}

add_shortcode('dashboard_personel', 'personel_dashboard_shortcode');

function personel_dashboard_shortcode() {
    if (!is_personel_logged_in()) {
        return '<div class="db-notice">Silahkan login terlebih dahulu.</div>';
    }
	
	
global $wpdb;
$table_name = 'wp9y_personel';
$personel_id = $_SESSION['personel_id'];
$message = '';
// Handler Portofolio Video
if (isset($_POST['submit_video']) || isset($_POST['update_video'])) {
    
    $is_edit = isset($_POST['update_video']);
    $judul = isset($_POST['judul']) ? sanitize_text_field($_POST['judul']) : '';
    $data = [
        'personel_id'      => $personel_id,
		'judul'       => $judul,
        'video_url'        => esc_url_raw($_POST['video_url']), // Simpan URL YouTube/Vimeo
        'tanggal_kegiatan' => $_POST['tanggal'],
        'lokasi'           => sanitize_text_field($_POST['lokasi']),
        'tahun'            => sanitize_text_field($_POST['tahun']),
        'deskripsi'        => sanitize_textarea_field($_POST['deskripsi']),
        'tags'             => sanitize_text_field($_POST['tags']),
        'status'           => 'pending'
    ];

    if ($is_edit) {
        $video_id = intval($_POST['video_id']);
        $wpdb->update('wp9y_portofolio_video', $data, ['id' => $video_id, 'personel_id' => $personel_id]);
        
        $wpdb->delete($wpdb->prefix . 'portofolio_video_kategori_map', ['video_id' => $video_id]);
        $selected_kategori = isset($_POST['portfolio_kategori']) ? array_slice(array_map('intval', $_POST['portfolio_kategori']), 0, 3) : [];
        foreach ($selected_kategori as $cat_id) {
            $wpdb->insert($wpdb->prefix . 'portofolio_video_kategori_map', [
                'video_id' => $video_id,
                'kategori_id' => $cat_id,
            ]);
        }
        $msg = "Video berhasil diperbarui!";
    } else {
        $wpdb->insert('wp9y_portofolio_video', $data);
        $new_video_id = $wpdb->insert_id;
        if ($new_video_id) {
            $selected_kategori = isset($_POST['portfolio_kategori']) ? array_slice(array_map('intval', $_POST['portfolio_kategori']), 0, 3) : [];
            foreach ($selected_kategori as $cat_id) {
                $wpdb->insert($wpdb->prefix . 'portofolio_video_kategori_map', [
                    'video_id' => $new_video_id,
                    'kategori_id' => $cat_id,
                ]);
            }
        }
        $msg = "Video berhasil diunggah!";
    }
    echo "<script>
    (function(){
        var d=document.createElement('div');
        d.style.cssText='position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:999999;display:flex;align-items:center;justify-content:center;';
        d.innerHTML='<div style=\"background:#1a1a1a;border:1px solid #d4af37;border-radius:8px;padding:30px 40px;max-width:400px;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,0.5);\">'+
            '<div style=\"font-size:48px;margin-bottom:10px;\">✅</div>'+
            '<p style=\"color:#fff;font-size:16px;margin:10px 0 20px;\">$msg</p>'+
            '<button onclick=\"window.location.href=\\'?tab=video\\'\" style=\"background:#d4af37;color:#000;border:none;padding:10px 24px;border-radius:4px;font-weight:bold;cursor:pointer;\">OK</button>'+
        '</div>';
        document.body.appendChild(d);
    })();
    </script>";
}	
// Handler Edit Portofolio
if (isset($_POST['update_portofolio'])) {
    if (wp_verify_nonce($_POST['porto_edit_nonce'], 'edit_portofolio')) {
        global $wpdb;
        $porto_id = intval($_POST['porto_id']);
        $personel_id = $_SESSION['personel_id'];

        $data_update = [
			'judul' => $_POST['judul'],
            'tanggal_kegiatan' => $_POST['tanggal'],
            'lokasi'           => sanitize_text_field($_POST['lokasi']),
            'tahun'            => sanitize_text_field($_POST['tahun']),
            'deskripsi'        => sanitize_textarea_field($_POST['deskripsi']),
            'tags'             => sanitize_text_field($_POST['tags']),
            'status'           => 'pending' // Setiap diedit, wajib di-approve ulang
        ];

        // Handle jika ganti foto
        if (!empty($_FILES['file_foto']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            $movefile = wp_handle_upload($_FILES['file_foto'], array('test_form' => false));
            if ($movefile && !isset($movefile['error'])) {
                $data_update['foto_url'] = $movefile['url'];
            }
        }

        $wpdb->update('wp9y_portofolio', $data_update, ['id' => $porto_id, 'personel_id' => $personel_id]);
        
        // Update kategori (many-to-many)
        $wpdb->delete('wp9y_portofolio_kategori_map', ['portofolio_id' => $porto_id]);
        $selected_kategori = isset($_POST['portfolio_kategori']) ? array_slice(array_map('intval', $_POST['portfolio_kategori']), 0, 3) : [];
        foreach ($selected_kategori as $cat_id) {
            $wpdb->insert('wp9y_portofolio_kategori_map', [
                'portofolio_id' => $porto_id,
                'kategori_id'   => $cat_id,
            ]);
        }

        echo "<script>
(function(){
    var d=document.createElement('div');
    d.style.cssText='position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:999999;display:flex;align-items:center;justify-content:center;';
    var m=document.createElement('div');
    m.style.cssText='background:#1a1a1a;border:1px solid #d4af37;border-radius:8px;padding:30px 40px;max-width:400px;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,0.5);';
    var e=document.createElement('div');
    e.style.cssText='font-size:48px;margin-bottom:10px;';
    e.textContent='✅';
    m.appendChild(e);
    var p=document.createElement('p');
    p.style.cssText='color:#fff;font-size:16px;margin:10px 0 20px;';
    p.textContent='Perubahan disimpan! Mohon tunggu persetujuan admin kembali.';
    m.appendChild(p);
    var b=document.createElement('button');
    b.style.cssText='background:#d4af37;color:#000;border:none;padding:10px 24px;border-radius:4px;font-weight:bold;cursor:pointer;';
    b.textContent='OK';
    b.onclick=function(){window.location.href='?tab=foto';};
    m.appendChild(b);
    d.appendChild(m);
    document.body.appendChild(d);
})();
</script>";
    }
}	
// Handler Hapus Portofolio
if (isset($_GET['tab']) && $_GET['tab'] == 'foto' && isset($_GET['action']) && $_GET['action'] == 'delete') {
    
    $porto_id = intval($_GET['id']);
    $personel_id = $_SESSION['personel_id'];

    // Pastikan foto yang dihapus adalah milik personel yang login
    $foto = $wpdb->get_row($wpdb->prepare("SELECT foto_url FROM wp9y_portofolio WHERE id = %d AND personel_id = %d", $porto_id, $personel_id));

    if ($foto) {
        // Hapus file dari storage server
        $file_path = str_replace(site_url('/'), ABSPATH, $foto->foto_url);
        if (file_exists($file_path)) unlink($file_path);

        // Hapus data dari database
        $wpdb->delete('wp9y_portofolio_kategori_map', ['portofolio_id' => $porto_id]);
        $wpdb->delete('wp9y_portofolio', ['id' => $porto_id]);
        
        echo "<script>
(function(){
    var d=document.createElement('div');
    d.style.cssText='position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:999999;display:flex;align-items:center;justify-content:center;';
    var m=document.createElement('div');
    m.style.cssText='background:#1a1a1a;border:1px solid #d4af37;border-radius:8px;padding:30px 40px;max-width:400px;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,0.5);';
    var e=document.createElement('div');
    e.style.cssText='font-size:48px;margin-bottom:10px;';
    e.textContent='✅';
    m.appendChild(e);
    var p=document.createElement('p');
    p.style.cssText='color:#fff;font-size:16px;margin:10px 0 20px;';
    p.textContent='Portofolio telah dihapus.';
    m.appendChild(p);
    var b=document.createElement('button');
    b.style.cssText='background:#d4af37;color:#000;border:none;padding:10px 24px;border-radius:4px;font-weight:bold;cursor:pointer;';
    b.textContent='OK';
    b.onclick=function(){window.location.href='?tab=foto';};
    m.appendChild(b);
    d.appendChild(m);
    document.body.appendChild(d);
})();
</script>";
    }
}	
// Handler Upload Portofolio
if (isset($_POST['submit_portofolio'])) {
    if (wp_verify_nonce($_POST['porto_nonce'], 'add_portofolio')) {
        global $wpdb;
        
        // Handle Upload Foto
        if (!empty($_FILES['file_foto']['name'])) {
    require_once(ABSPATH . 'wp-admin/includes/file.php');

    // 1. Buat daftar format dan MIME type yang diizinkan
    $allowed_mimes = array(
        'jpg|jpeg|jpe' => 'image/jpeg',
        'png'          => 'image/png',
        'webp'         => 'image/webp'
    );

    // 2. Masukkan aturan tersebut ke parameter overrides
    $overrides = array(
        'test_form' => false,
        'mimes'     => $allowed_mimes
    );

    // 3. Jalankan wp_handle_upload dengan menyertakan $overrides
    $movefile = wp_handle_upload($_FILES['file_foto'], $overrides);
    
    if ($movefile && !isset($movefile['error'])) {
        $wpdb->insert('wp9y_portofolio', [
            'personel_id'      => $_SESSION['personel_id'],
            'judul'            => $_POST['judul'],
            'foto_url'         => $movefile['url'],
            'tanggal_kegiatan' => $_POST['tanggal'],
            'lokasi'           => sanitize_text_field($_POST['lokasi']),
            'tahun'            => sanitize_text_field($_POST['tahun']),
            'deskripsi'        => sanitize_textarea_field($_POST['deskripsi']),
            'tags'             => sanitize_text_field($_POST['tags']),
            'status'           => 'pending'
        ]);
        
        $new_porto_id = $wpdb->insert_id;
        if ($new_porto_id) {
            $selected_kategori = isset($_POST['portfolio_kategori']) ? array_slice(array_map('intval', $_POST['portfolio_kategori']), 0, 3) : [];
            foreach ($selected_kategori as $cat_id) {
                $wpdb->insert('wp9y_portofolio_kategori_map', [
                    'portofolio_id' => $new_porto_id,
                    'kategori_id'   => $cat_id,
                ]);
            }
        }

        echo "<script>
        (function(){
            var d=document.createElement('div');
            d.style.cssText='position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:999999;display:flex;align-items:center;justify-content:center;';
            d.innerHTML='<div style=\"background:#1a1a1a;border:1px solid #d4af37;border-radius:8px;padding:30px 40px;max-width:400px;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,0.5);\">'+
                '<div style=\"font-size:48px;margin-bottom:10px;\">✅</div>'+
                '<p style=\"color:#fff;font-size:16px;margin:10px 0 20px;\">Portofolio berhasil diunggah! Menunggu moderasi admin.</p>'+
                '<button onclick=\"window.location.href=\\'?tab=foto\\'\" style=\"background:#d4af37;color:#000;border:none;padding:10px 24px;border-radius:4px;font-weight:bold;cursor:pointer;\">OK</button>'+
            '</div>';
            document.body.appendChild(d);
        })();
        </script>";
    } else {
        // Jika user mengunggah file selain format di atas, $movefile['error'] akan berisi pesan penolakan
        $err_msg = addslashes($movefile['error']);
        echo "<script>
        (function(){
            var d=document.createElement('div');
            d.style.cssText='position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:999999;display:flex;align-items:center;justify-content:center;';
            d.innerHTML='<div style=\"background:#1a1a1a;border:1px solid #ff4444;border-radius:8px;padding:30px 40px;max-width:400px;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,0.5);\">'+
                '<div style=\"font-size:48px;margin-bottom:10px;\">❌</div>'+
                '<p style=\"color:#fff;font-size:16px;margin:10px 0 20px;\">$err_msg</p>'+
                '<button onclick=\"this.closest(\\'div\\').parentElement.remove()\" style=\"background:#ff4444;color:#fff;border:none;padding:10px 24px;border-radius:4px;font-weight:bold;cursor:pointer;\">OK</button>'+
            '</div>';
            document.body.appendChild(d);
        })();
        </script>";
    }
}
    }
}	

// Cek apakah form disubmit
if (isset($_POST['update_profile_personel'])) {
    // Verifikasi Nonce untuk Keamanan (mencegah CSRF)
    if (!isset($_POST['personel_update_nonce']) || !wp_verify_nonce($_POST['personel_update_nonce'], 'update_profile')) {
        $message = '<div class="notice-error">⚠️ Sesi keamanan kadaluarsa. Silahkan refresh dan coba lagi.</div>';
    } else {
        $personel = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp9y_personel WHERE id = %d", $personel_id));
        
        $old_kode = $personel->kode_nama;

        // 2. Pecah kode (Contoh: 0001-FDE)
        if (!empty($old_kode) && strpos($old_kode, '-') !== false) {
            $parts = explode('-', $old_kode);
            $number_part = $parts[0]; // Ini akan mengambil '0001'
        } else {
            $number_part = substr($old_kode, 0, 4) ?: '0000'; 
        }

        // 3. Ambil posisi baru dari form
        $posisi_array = isset($_POST['posisi']) ? $_POST['posisi'] : [];
        $new_suffix = implode('', $posisi_array); // Gabungkan jadi "FDEA"

        // 4. Gabungkan kembali
        $new_full_kode = $number_part . '-' . $new_suffix;
		
		$porto_links = isset($_POST['porto_links']) ? $_POST['porto_links'] : [];
        
        // Bersihkan: hapus yang kosong, sanitize URL
        $clean_links = array_filter(array_map('esc_url_raw', $porto_links));
        
        // Ambil maksimal 5 saja
        $final_links = array_slice($clean_links, 0, 5);
		$domisili_lama = $personel->domisili;
		$domisili_parts = [];
		for ($i = 1; $i <= 2; $i++) {
		    $prov = $_POST["nama_provinsi_$i"] ?? '';
		    $kota = isset($_POST["kota_kabupaten_$i"]) ? array_filter(array_map('sanitize_text_field', (array)$_POST["kota_kabupaten_$i"])) : [];
		    if (!empty($prov) && !empty($kota)) {
		        $domisili_parts[] = sanitize_text_field($prov) . ' - ' . implode(', ', $kota);
		    }
		}
		$domisili_baru = !empty($domisili_parts) ? implode(' || ', $domisili_parts) : $domisili_lama;
		$tanggal_lahir = isset($_POST['tanggal_lahir']) ? sanitize_text_field($_POST['tanggal_lahir']) : '';	
        
        // 1. Siapkan Data Dasar untuk Draft
        $draft_fields = [
            'nama_lengkap'      => sanitize_text_field($_POST['nama_lengkap']),
            'nama_panggilan'    => sanitize_text_field($_POST['nama_panggilan']),
            'no_hp'             => sanitize_text_field($_POST['no_hp']),
            'tanggal_lahir'     => $tanggal_lahir,
            'domisili'          => $domisili_baru,
            'deskripsi'         => sanitize_textarea_field($_POST['deskripsi']),
            'peralatan'         => sanitize_textarea_field($_POST['peralatan']),
            'pricelist_perhari' => sanitize_text_field($_POST['pricelist_perhari']),
            'pricelist'         => wp_kses_post($_POST['pricelist']), // Mengizinkan HTML aman dari editor
            'facebook'          => esc_url_raw($_POST['facebook']),
            'instagram'         => esc_url_raw($_POST['instagram']),
            'tiktok'            => esc_url_raw($_POST['tiktok']),
            'thread'            => esc_url_raw($_POST['thread']),
            'youtube'           => esc_url_raw($_POST['youtube']),
            'tag'               => sanitize_text_field($_POST['tag']),
			'kode_nama'         => $new_full_kode,
    		'posisi'            => implode(',', $posisi_array),
			'porto_links'       => json_encode($final_links),
            // Default file paths to current active values
            'foto_profil'       => $personel->foto_profil,
            'cv_url'            => $personel->cv_url,
            'sertifikat_multiple'=> $personel->sertifikat_multiple,
        ];

        // 2. Handle Ganti Password (Hanya jika diisi)
        if (!empty($_POST['new_password'])) {
            if (strlen($_POST['new_password']) >= 8) {
                $draft_fields['password'] = wp_hash_password($_POST['new_password']);
            } else {
                $message .= '<div class="notice-error">⚠️ Password minimal 8 karakter. Password tidak diubah.</div>';
            }
        }

        // 3. Handle Update Foto Profil
        if (!empty($_FILES['foto_profil']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            
            $uploadedfile = $_FILES['foto_profil'];
            $upload_overrides = array('test_form' => false);
            
            $file_type = wp_check_filetype($uploadedfile['name']);
            $allowed_types = array('image/jpeg', 'image/png', 'image/webp');

            if (in_array($file_type['type'], $allowed_types)) {
                $movefile = wp_handle_upload($uploadedfile, $upload_overrides);

                if ($movefile && !isset($movefile['error'])) {
                    $draft_fields['foto_profil'] = $movefile['url'];
                } else {
                    $message .= '<div class="notice-error">❌ Gagal upload foto: ' . $movefile['error'] . '</div>';
                }
            } else {
                $message .= '<div class="notice-error">❌ Format foto tidak didukung (Gunakan JPG/PNG/WebP).</div>';
            }
        }
		
		// Pastikan library file WordPress dimuat
        require_once(ABSPATH . 'wp-admin/includes/file.php');

        // 4. Update CV (Jika ada file baru)
        if (!empty($_FILES['cv_file']['name'])) {
            $cv_upload = wp_handle_upload($_FILES['cv_file'], array('test_form' => false));
            if (isset($cv_upload['url'])) {
                $draft_fields['cv_url'] = $cv_upload['url'];
            } else {
                $message .= '<div class="notice-error">❌ Gagal upload CV: ' . $cv_upload['error'] . '</div>';
            }
        }

        // 5. Update Sertifikat (Jika ada file baru)
        if (!empty($_FILES['sertifikat_files']['name'][0])) {
            $files = $_FILES['sertifikat_files'];
            $new_sertifikat_urls = array();
            
            foreach ($files['name'] as $key => $value) {
                if ($files['name'][$key]) {
                    $file = array(
                        'name'     => $files['name'][$key],
                        'type'     => $files['type'][$key],
                        'tmp_name' => $files['tmp_name'][$key],
                        'error'    => $files['error'][$key],
                        'size'     => $files['size'][$key]
                    );

                    $upload = wp_handle_upload($file, array('test_form' => false));
                    if (isset($upload['url'])) {
                        $new_sertifikat_urls[] = $upload['url'];
                    }
                }
            }

            if (!empty($new_sertifikat_urls)) {
                $draft_fields['sertifikat_multiple'] = json_encode($new_sertifikat_urls);
            }
        }

        // 6. PENCEGAHAN BERKAS SAMPAH (Jika draft lama ditimpa draft baru)
        $existing_draft_json = $wpdb->get_var($wpdb->prepare("SELECT draft_data FROM wp9y_personel_draft_edit WHERE personel_id = %d", $personel_id));
        if ($existing_draft_json) {
            $old_draft = json_decode($existing_draft_json, true);
            
            // Periksa Foto Profil & CV
            $file_keys = ['foto_profil', 'cv_url'];
            foreach ($file_keys as $key) {
                if (!empty($old_draft[$key]) && $old_draft[$key] !== $draft_fields[$key] && $old_draft[$key] !== $personel->$key) {
                    $file_path = str_replace(site_url('/'), ABSPATH, $old_draft[$key]);
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
            }
            
            // Periksa Sertifikat (Multiple)
            if (!empty($old_draft['sertifikat_multiple']) && $old_draft['sertifikat_multiple'] !== $draft_fields['sertifikat_multiple'] && $old_draft['sertifikat_multiple'] !== $personel->sertifikat_multiple) {
                $old_certs = json_decode($old_draft['sertifikat_multiple'], true) ?: [];
                $new_certs = json_decode($draft_fields['sertifikat_multiple'], true) ?: [];
                $active_certs = json_decode($personel->sertifikat_multiple, true) ?: [];
                
                if (is_array($old_certs)) {
                    foreach ($old_certs as $cert_url) {
                        if (!in_array($cert_url, $new_certs) && !in_array($cert_url, $active_certs)) {
                            $file_path = str_replace(site_url('/'), ABSPATH, $cert_url);
                            if (file_exists($file_path)) {
                                unlink($file_path);
                            }
                        }
                    }
                }
            }
        }

        // 7. Simpan Draft Ke Database (Menimpa draft lama jika ada)
        $draft_saved = $wpdb->replace('wp9y_personel_draft_edit', [
            'personel_id' => $personel_id,
            'draft_data'  => json_encode($draft_fields),
        ]);

        if ($draft_saved !== false) {
            $message = '<div class="notice-success">✅ Perubahan profil Anda telah dikirim untuk ditinjau oleh admin. Sementara itu, profil aktif Anda tetap berjalan.</div>';
        } else {
            $message = '<div class="notice-error">❌ Terjadi kesalahan saat mengirim perubahan ke admin.</div>';
        }
    }
}
    $personel = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp9y_personel WHERE id = %d", $personel_id));
    
    // Tentukan Tab Aktif
    $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'dashboard';

    ob_start();
    ?>
    <div class="dashboard-container">
        <aside class="db-sidebar">
            <div class="db-profile-section">
                <img src="<?php echo esc_url($personel->foto_profil); ?>" class="db-avatar">
                <p class="db-name"><?php echo esc_html($personel->nama_panggilan); ?></p>
                <span class="db-status">ID: <?php echo $personel->kode_nama; ?></span>
            </div>
            <nav class="db-menu">
                <?php 
                $menus = [
                    'dashboard'   => ['icon' => '📊', 'label' => 'Dashboard'],
                    'edit-profil' => ['icon' => '👤', 'label' => 'Edit Profil'],
                    'foto'        => ['icon' => '📸', 'label' => 'Portofolio Foto'],
                    'video'       => ['icon' => '🎥', 'label' => 'Portofolio Video'],
                    'artikel'     => ['icon' => '✍️', 'label' => 'Artikel'],
                ];
                foreach ($menus as $key => $val) {
                    $active = ($current_tab == $key) ? 'active' : '';
                    echo '<a href="?tab='.$key.'" class="db-menu-item '.$active.'">'.$val['icon'].' '.$val['label'].'</a>';
                }
                ?>
                <a href="?personel_action=logout" class="db-menu-item logout">🚪 Logout</a>
            </nav>
        </aside>

        <main class="db-main">
            <?php 
            switch ($current_tab) {
                case 'edit-profil':
                    render_personel_edit_profil($personel, $message);
                    break;
                case 'foto':
					$action = isset($_GET['action']) ? $_GET['action'] : '';
					$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

					if ($action == 'add') {
						render_tab_portofolio_foto($personel);
					} elseif ($action == 'edit' && $id > 0) {
						render_tab_edit_portofolio($personel, $id);
					} else {
						render_list_portofolio_foto($personel);
					}
					break;
                case 'video':
					$action = isset($_GET['action']) ? $_GET['action'] : '';
					$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

					// Logika Hapus Video
					if ($action == 'delete' && $id > 0) {
						global $wpdb;
						$wpdb->delete('wp9y_portofolio_video_kategori_map', ['video_id' => $id]);
						$wpdb->delete('wp9y_portofolio_video', [
							'id' => $id, 
							'personel_id' => $_SESSION['personel_id']
						]);
						echo "<script>
(function(){
    var d=document.createElement('div');
    d.style.cssText='position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:999999;display:flex;align-items:center;justify-content:center;';
    var m=document.createElement('div');
    m.style.cssText='background:#1a1a1a;border:1px solid #d4af37;border-radius:8px;padding:30px 40px;max-width:400px;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,0.5);';
    var e=document.createElement('div');
    e.style.cssText='font-size:48px;margin-bottom:10px;';
    e.textContent='✅';
    m.appendChild(e);
    var p=document.createElement('p');
    p.style.cssText='color:#fff;font-size:16px;margin:10px 0 20px;';
    p.textContent='Video berhasil dihapus';
    m.appendChild(p);
    var b=document.createElement('button');
    b.style.cssText='background:#d4af37;color:#000;border:none;padding:10px 24px;border-radius:4px;font-weight:bold;cursor:pointer;';
    b.textContent='OK';
    b.onclick=function(){window.location.href='?tab=video';};
    m.appendChild(b);
    d.appendChild(m);
    document.body.appendChild(d);
})();
</script>";
						break;
					}

					// Tampilan Form Edit
					if ($action == 'edit' && $id > 0) {
						render_tab_form_video($personel, $id);
					} 
					// Tampilan Form Tambah
					elseif ($action == 'add') {
						render_tab_form_video($personel);
					} 
					// Tampilan List Portofolio Video (Default)
					else {
						render_list_portofolio_video($personel);
					}
					break;
                case 'artikel':
    echo '<div class="tab-header" style="margin-bottom:20px;">';
    echo '    <h2 style="color:var(--gold); margin:0;">✍️ Artikel Saya</h2>';
    echo '    <p style="color:#888;">Bagikan pengalaman dan tips Anda ke publik. Setiap artikel akan melalui review admin.</p>';
    echo '</div>';
    
    // Memanggil fungsi render form yang kita buat sebelumnya
    if (function_exists('render_tab_artikel_personel')) {
        render_tab_artikel_personel();
    } else {
        echo '<p style="color:red;">Fungsi render_tab_artikel_personel tidak ditemukan. Pastikan kode handler sudah di-copy ke functions.php</p>';
    }
    break;
                default:
                    render_personel_home($personel);
                    break;
            }
            ?>
        </main>
    </div>
    <?php
    return ob_get_clean();
}
function render_tab_edit_portofolio($personel, $porto_id) {
    global $wpdb;
    $porto = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM wp9y_portofolio WHERE id = %d AND personel_id = %d", 
        $porto_id, $personel->id
    ));

    if (!$porto) {
        echo "<div class='notice-error'>Data tidak ditemukan atau Anda tidak memiliki akses.</div>";
        return;
    }
    ?>
    <div class="form-edit-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h2 style="color:var(--gold); margin:0;">✏️ Edit Portofolio</h2>
            <a href="?tab=foto" style="color:#aaa; text-decoration:none; font-size:13px;">✕ Batalkan</a>
        </div>

        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('edit_portofolio', 'porto_edit_nonce'); ?>
            <input type="hidden" name="porto_id" value="<?php echo $porto->id; ?>">
            <div class="form-group full" style="margin-bottom: 15px;">
			<label style="display:block; margin-bottom:5px; color:#d4af37; font-weight:bold;">
				Judul Portofolio <span style="color:red;">*</span>
			</label>
			<input type="text" name="judul" value="<?php echo $porto->judul; ?>" required 
				   placeholder="Judul Portofolio" 
				   style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: #fff; border-radius: 4px;">
		</div>
            <div class="form-group full">
                <label>Foto Saat Ini</label>
                <div style="margin: 10px 0;">
                    <img src="<?php echo esc_url($porto->foto_url); ?>" style="width:200px; border-radius:8px; border:1px solid var(--border-gold);">
                </div>
                <label>Ganti Foto (Kosongkan jika tidak ganti)</label>
                <input type="file" name="file_foto" accept="image/*">
            </div>
			<div class="form-group full">
                <label>Deskripsi Singkat</label>
                <textarea name="deskripsi" rows="4"><?php echo esc_textarea($porto->deskripsi); ?></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Tanggal Kegiatan</label>
                    <input type="date" name="tanggal" value="<?php echo $porto->tanggal_kegiatan; ?>" required>
                </div>
                <div class="form-group">
                    <label>Lokasi</label>
                    <input type="text" name="lokasi" value="<?php echo esc_attr($porto->lokasi); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tahun</label>
                    <select name="tahun" required>
                        <?php 
                        $year = date('Y');
                        for($i=0; $i<=15; $i++) {
                            $y = $year - $i;
                            $selected = ($porto->tahun == $y) ? 'selected' : '';
                            echo "<option value='$y' $selected>$y</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group full">
                <label>Tags Portofolio</label>
                <div class="tag-system-container">
                    <div class="tag-input-wrapper" id="tagInputWrapper">
                        <input type="text" id="tagInput" placeholder="Ketik tag lalu tekan Enter/Koma...">
                    </div>
                    <input type="hidden" name="tags" id="tagHiddenInput" value="<?php echo esc_attr($porto->tags); ?>">
                </div>
                <small style="color:var(--text-muted);">Maksimal 10 tags. Contoh: cinematic, wedding, colorist</small>
            </div>

            <?php render_portfolio_category_selection($porto->id, 'foto'); ?>

            <button type="submit" name="update_portofolio" class="btn-update">
                💾 Simpan Perubahan
            </button>
        </form>
    </div>

    <script>
    (function() {
        const tagInput = document.getElementById('tagInput');
        const wrapper = document.getElementById('tagInputWrapper');
        const hiddenInput = document.getElementById('tagHiddenInput');
        const maxTags = 10;
        
        let tags = hiddenInput.value ? hiddenInput.value.split(',').filter(t => t !== "") : [];

        function renderTags() {
            const inputField = tagInput;
            wrapper.querySelectorAll('.tag-item').forEach(el => el.remove());
            
            tags.forEach((tag, index) => {
                const tagEl = document.createElement('div');
                tagEl.className = 'tag-item';
                tagEl.innerHTML = `#${tag} <button type="button" class="tag-remove" data-index="${index}">&times;</button>`;
                wrapper.insertBefore(tagEl, inputField);
            });
        }

        function addTag(text) {
            if (tags.length >= maxTags) return;
            const tag = text.trim().toLowerCase().replace(/[^a-z0-9\s-]/g, '');
            if (tag.length >= 2 && !tags.includes(tag)) {
                tags.push(tag);
                updateHiddenInput();
                renderTags();
            }
            tagInput.value = '';
        }

        function removeTag(index) {
            tags.splice(index, 1);
            updateHiddenInput();
            renderTags();
        }

        function updateHiddenInput() {
            hiddenInput.value = tags.join(',');
        }

        tagInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                addTag(this.value);
            }
        });

        wrapper.addEventListener('click', function(e) {
            if (e.target.classList.contains('tag-remove')) {
                removeTag(e.target.getAttribute('data-index'));
            }
        });

        renderTags();
    })();
    </script>
    <?php
}
function render_list_portofolio_foto($personel) {
    global $wpdb;
    $fotos = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM wp9y_portofolio WHERE personel_id = %d ORDER BY created_at DESC", 
        $personel->id
    ));
    ?>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="color:var(--gold); margin:0;">📸 Portofolio Saya</h2>
        <?php 
// 1. Ambil status kuota (pastikan variabel $personel_id sesuai dengan variabel di halaman Anda)
$kuota_foto = get_status_kuota_personel($personel->id, 'foto');

// 2. Cek kondisi kuota
if (!$kuota_foto['is_full']) : ?>
    <a href="?tab=foto&action=add" class="btn-update" style="width: auto; padding: 10px 20px; text-decoration:none; font-size:14px; color:#000;">
        + Unggah Foto Baru
    </a>
<?php else : ?>
    <span style="display: inline-block; padding: 10px 20px; background: #331a1a; border: 1px solid #ff4d4d; color: #ff4d4d; border-radius: 5px; font-size: 13px; font-weight: bold;">
        ⚠️ Kuota Foto Penuh (Maks. 20)
    </span>
<?php endif; ?>
    </div>

    <div class="porto-grid">
        <?php if($fotos): foreach($fotos as $f): ?>
            <div class="porto-item">
                <div class="porto-image">
                    <img src="<?php echo esc_url($f->foto_url); ?>" alt="Portofolio">
                    <div class="status-overlay">
                        <span class="status-badge-new <?php echo ($f->status == 'approved') ? 'st-approved' : 'st-pending'; ?>">
                            <?php echo strtoupper($f->status); ?>
                        </span>
                    </div>
                </div>
                
                <div class="porto-content">
                    <h3 class="porto-title" style="margin: 0 0 10px 0; color: #d4af37; font-size: 16px; font-weight: bold;">
                        <?php echo esc_html($f->judul); ?>
                    </h3>
                    
                    <div class="porto-meta-row">
                        <span class="p-year">🗓️ <?php echo esc_html($f->tahun); ?></span>
                        <span class="p-loc">📍 <?php echo esc_html($f->lokasi); ?></span>
                    </div>
                    
                    <?php if(!empty($f->tags)): ?>
                        <h4 class="porto-tags">
                            <?php 
                                $tags_array = explode(',', $f->tags);
                                foreach($tags_array as $t) {
                                    echo '<span class="tag-pill">#' . trim(esc_html($t)) . '</span> ';
                                }
                            ?>
                        </h4>
                    <?php endif; ?>
                    
                    <p class="porto-desc">
                        <?php echo !empty($f->deskripsi) ? wp_trim_words(esc_html($f->deskripsi), 15, '...') : '<i style="opacity:0.5;">Tidak ada deskripsi.</i>'; ?>
                    </p>

                    <div class="porto-actions">
                        <a href="?tab=foto&action=edit&id=<?php echo $f->id; ?>" class="btn-edit-porto" style="color:#000">✏️ Edit</a>
                        <a href="?tab=foto&action=delete&id=<?php echo $f->id; ?>" class="btn-delete-porto" onclick="return confirm('Hapus foto ini?')">🗑️</a>
                    </div>
                </div>
            </div>
        <?php endforeach; else: ?>
            <div class="empty-state">Belum ada portofolio yang diunggah.</div>
        <?php endif; ?>
    </div>

    <style>
    .porto-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .porto-item {
        background: #161616;
        border-radius: 12px;
        border: 1px solid #333;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .porto-image {
        position: relative;
        height: 200px;
        background: #000;
    }

    .porto-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.9;
    }

    /* Status Overlay agar tidak tabrakan dengan teks di bawah */
    .status-overlay {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 10;
    }

    .status-badge-new {
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .st-approved { background: #dcfce7; color: #166534; }
    .st-pending { background: #fef9c3; color: #854d0e; }

    .porto-content { padding: 15px; flex-grow: 1; display: flex; flex-direction: column; }

    /* Meta Row (Tahun & Lokasi) */
    .porto-meta-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid #222;
    }

    .p-year, .p-loc { font-size: 11px; font-weight: 600; color: #d4af37; }

    /* Tag Styling */
    .porto-tags { margin: 0 0 10px 0; display: flex; flex-wrap: wrap; gap: 5px; }
    .tag-pill {
        font-size: 10px;
        color: #aaa;
        background: #222;
        padding: 2px 6px;
        border-radius: 3px;
    }

    .porto-desc {
        font-size: 13px;
        color: #ccc;
        line-height: 1.5;
        margin-bottom: 15px;
        flex-grow: 1;
    }

    /* Actions */
    .porto-actions {
        display: flex;
        gap: 8px;
        margin-top: auto;
    }

    .btn-edit-porto {
        flex-grow: 1;
        text-align: center;
        background: #d4af37;
        color: #000;
        padding: 8px;
        border-radius: 6px;
        font-weight: 700;
        text-decoration: none;
        font-size: 12px;
    }

    .btn-delete-porto {
        background: #333;
        color: #ff4d4d;
        padding: 8px 12px;
        border-radius: 6px;
        text-decoration: none;
        border: 1px solid #444;
    }

    .btn-delete-porto:hover { background: #ff4d4d; color: #fff; }

    .empty-state {
        grid-column: 1/-1;
        padding: 60px;
        text-align: center;
        border: 1px dashed #333;
        border-radius: 12px;
        color: #666;
    }

    @media (max-width: 768px) { .porto-grid { grid-template-columns: 1fr; } }
    </style>
    <?php
}
function render_tab_portofolio_foto($personel) {
    ?>
    <div class="form-edit-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="color:var(--gold); margin:0;">📸 Upload Portofolio Baru</h2>
            <a href="?tab=foto" class="btn-action" style="background:#333; color:white; padding:5px 15px; border-radius:5px; text-decoration:none; font-size:12px;">← Kembali ke List</a>
        </div>

        <form method="post" enctype="multipart/form-data" class="personel-form">
            <?php wp_nonce_field('add_portofolio', 'porto_nonce'); ?>
			
			<div class="form-group full" style="margin-bottom: 15px;">
			<label style="display:block; margin-bottom:5px; color:#d4af37; font-weight:bold;">
				Judul Portofolio <span style="color:red;">*</span>
			</label>
			<input type="text" name="judul" required 
				   placeholder="Judul Portofolio" 
				   style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: #fff; border-radius: 4px;">
		</div>
            
            <div class="form-group full" style="border: 2px dashed var(--border-gold); padding: 30px; text-align: center; border-radius: 10px; margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 15px; font-size: 16px;">Pilih File Foto (JPG/PNG/WEBP)</label>
                <input type="file" name="file_foto" accept="image/*" required style="background: transparent; border: none;">
                <p style="font-size: 11px; opacity: 0.6; margin-top: 10px;">Rekomendasi ukuran: 1920x1080px, Max 3MB.</p>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tanggal Kegiatan</label>
                    <input type="date" name="tanggal" required>
                </div>
                <div class="form-group">
                    <label>Lokasi (Kota/Venue)</label>
                    <input type="text" name="lokasi" placeholder="Misal: Jakarta / Hotel Mulia" required>
                </div>
            </div>
			
			<div class="form-group full">
                <label>Deskripsi Singkat</label>
                <textarea name="deskripsi" rows="3" placeholder="Ceritakan sedikit tentang karya ini..."></textarea>
				<small style="color:var(--text-muted);">*Tidak mencantumkan no WA dan link sosmed</small><br>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tahun</label>
                    <select name="tahun" required>
                        <?php 
                        $year = date('Y');
                        for($i=0; $i<=15; $i++) {
                            echo "<option value='".($year-$i)."'>".($year-$i)."</option>";
                        }
                        ?>
                    </select>
                </div>
				</div>
                <div class="form-group full">
    <label>
				</label>
    <div class="tag-system-container">
        <div class="tag-input-wrapper" id="tagInputWrapper">
            <input type="text" id="tagInput" placeholder="Ketik tag lalu tekan Enter/Koma...">
        </div>
        <input type="hidden" name="tags" id="tagHiddenInput" >
    </div>
    <small style="color:var(--text-muted);">Maksimal 10 tags. Contoh: cinematic, wedding, colorist</small>
</div>
            
            <?php render_portfolio_category_selection(0, 'foto'); ?>

            <button type="submit" name="submit_portofolio" class="btn-update">
                🚀 Unggah Portofolio
            </button>
        </form>
    </div>
<script>
(function() {
    const tagInput = document.getElementById('tagInput');
    const wrapper = document.getElementById('tagInputWrapper');
    const hiddenInput = document.getElementById('tagHiddenInput');
    const maxTags = 10;
    
    // Ambil data awal dari hidden input (data dari DB)
    let tags = hiddenInput.value ? hiddenInput.value.split(',').filter(t => t !== "") : [];

    function renderTags() {
        // Simpan input field agar tidak hilang saat wrapper di-clear
        const inputField = tagInput;
        wrapper.innerHTML = '';
        
        tags.forEach((tag, index) => {
            const tagEl = document.createElement('div');
            tagEl.className = 'tag-item';
            tagEl.innerHTML = `
                #${tag}
                <button type="button" class="tag-remove" data-index="${index}">&times;</button>
            `;
            wrapper.appendChild(tagEl);
        });
        
        wrapper.appendChild(inputField);
        inputField.focus();
    }

    function addTag(text) {
        if (tags.length >= maxTags) return;
        
        // Bersihkan input: lowercase, hilangkan karakter aneh
        const tag = text.trim().toLowerCase().replace(/[^a-z0-9\s-]/g, '');
        
        if (tag.length >= 2 && !tags.includes(tag)) {
            tags.push(tag);
            updateHiddenInput();
            renderTags();
        }
        tagInput.value = '';
    }

    function removeTag(index) {
        tags.splice(index, 1);
        updateHiddenInput();
        renderTags();
    }

    function updateHiddenInput() {
        hiddenInput.value = tags.join(',');
    }

    // Event Listeners
    tagInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            addTag(this.value);
        }
        if (e.key === 'Backspace' && this.value === '' && tags.length > 0) {
            removeTag(tags.length - 1);
        }
    });

    // Menangani klik pada tombol remove menggunakan event delegation
    wrapper.addEventListener('click', function(e) {
        if (e.target.classList.contains('tag-remove')) {
            const index = e.target.getAttribute('data-index');
            removeTag(index);
        }
    });

    // Inisialisasi awal
    renderTags();
    
})();
</script>
    <?php
}

function render_personel_home($personel) {
    global $wpdb;

    // 1. Hitung jumlah Foto (Approved)
    $count_foto = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM wp9y_portofolio WHERE personel_id = %d AND status = 'approved'", 
        $personel->id
    ));

    // 2. Hitung jumlah Video (Approved)
    $count_video = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM wp9y_portofolio_video WHERE personel_id = %d AND status = 'approved'", 
        $personel->id
    ));

    ?>
    <div class="db-welcome-card">
        <h2 style="color:var(--gold);">Welcome back, <?php echo esc_html($personel->nama_panggilan); ?>!</h2>
        <p>Status Akun: 
            <?php if($personel->status == 'approved'): ?>
                <span style="color:#00ff00;">● Approved</span>
            <?php elseif($personel->status == 'non-aktif'): ?>
                <span style="color:#ff4d4d;">● Non-Aktif</span>
            <?php else: ?>
                <span style="color:#ffcc00;">● Pending</span>
            <?php endif; ?>
        </p>
    </div>

    <div class="db-grid">
        <div class="stat-card">
            <h4>Foto</h4>
            <p><?php echo number_format($count_foto); ?></p>
        </div>
        <div class="stat-card">
            <h4>Video</h4>
            <p><?php echo number_format($count_video); ?></p>
        </div>
        <div class="stat-card">
            <h4>Artikel</h4>
            <p>0</p> </div>
    </div>

    <style>
        /* Tambahkan style jika belum ada agar tampilan stat-card lebih menarik */
        .db-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .stat-card {
            background: #1a1a1a;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid #333;
            transition: 0.3s;
        }
        .stat-card:hover {
            border-color: var(--gold);
            transform: translateY(-5px);
        }
        .stat-card h4 {
            margin: 0;
            color: #888;
            font-size: 14px;
            text-transform: uppercase;
        }
        .stat-card p {
            margin: 10px 0 0;
            font-size: 32px;
            font-weight: bold;
            color: var(--gold);
        }
    </style>
    <?php
}

function render_personel_edit_profil($personel, $message = '') {
    global $wpdb;
    
    // Ambil draft usulan jika ada
    $draft_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp9y_personel_draft_edit WHERE personel_id = %d", $personel->id));
    
    // Buat objek tampilan gabungan (Default ke data personel aktif)
    $display_data = clone $personel;
    $has_draft = false;
    
    if ($draft_row) {
        $has_draft = true;
        $draft_fields = json_decode($draft_row->draft_data, true);
        if (is_array($draft_fields)) {
            foreach ($draft_fields as $key => $value) {
                // Jangan timpa password dengan hash di form edit
                if ($key !== 'password') {
                    $display_data->$key = $value;
                }
            }
        }
    }

    // Ambil data posisi yang tersimpan (asumsi disimpan sebagai string koma: F,V,D)
    $posisi_saved = !empty($display_data->posisi) ? explode(',', $display_data->posisi) : [];

    // Parse domisili: handle semua format
    $dom_prov_1 = '';
    $dom_kota_1 = [];
    $dom_prov_2 = '';
    $dom_kota_2 = [];
    if (!empty($display_data->domisili)) {
        $blok_prov = explode(' || ', $display_data->domisili);
        foreach ($blok_prov as $idx => $blok) {
            $parts = explode(' - ', $blok);
            if (count($parts) >= 2) {
                $prov = trim($parts[0]);
                $kota_str = trim($parts[1]);
                $kota = !empty($kota_str) ? explode(', ', $kota_str) : [];
                if ($idx === 0) {
                    $dom_prov_1 = $prov;
                    $dom_kota_1 = $kota;
                } elseif ($idx === 1) {
                    $dom_prov_2 = $prov;
                    $dom_kota_2 = $kota;
                }
            } else {
                $old_parts = explode(', ', $blok, 2);
                if (count($old_parts) === 2) {
                    if ($idx === 0) {
                        $dom_kota_1 = [trim($old_parts[0])];
                        $dom_prov_1 = trim($old_parts[1]);
                    } elseif ($idx === 1) {
                        $dom_kota_2 = [trim($old_parts[0])];
                        $dom_prov_2 = trim($old_parts[1]);
                    }
                }
            }
        }
    }
    ?>

    <div class="form-edit-container">
		<?php 
        if (!empty($message)) echo $message; 
        
        if ($has_draft) {
            echo '<div class="notice-info" style="background:#fff3cd; color:#856404; border-left:4px solid #ffc107; padding:15px; border-radius:4px; margin-bottom:20px;">
                ⚠️ <strong>Informasi:</strong> Anda memiliki usulan perubahan profil yang sedang ditinjau oleh admin. Anda tetap dapat mengedit kembali jika ingin memperbarui usulan Anda.
            </div>';
        }
        ?>
        <h2 style="color:var(--gold); margin-top:0; border-bottom:1px solid var(--border-gold); padding-bottom:10px;">
            👤 Edit Profil Lengkap
        </h2>
        
        <form method="post" enctype="multipart/form-data" id="personelUpdateForm">
            <?php wp_nonce_field('update_profile', 'personel_update_nonce'); ?>
            <input type="hidden" name="update_profile_personel" value="1">

            <div class="form-row">
                <div class="form-group">
                    <label>Nama Lengkap <span class="required">*</span></label>
                    <input type="text" name="nama_lengkap" value="<?php echo esc_attr($display_data->nama_lengkap); ?>" required>
                </div>
                <div class="form-group">
                    <label>Nama Panggilan <span class="required">*</span></label>
                    <input type="text" name="nama_panggilan" value="<?php echo esc_attr($display_data->nama_panggilan); ?>" required maxlength="30">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Email <span class="required">*</span></label>
                    <input type="email" value="<?php echo esc_attr($display_data->email); ?>" disabled style="background:#333; cursor:not-allowed;">
                    <small>Email tidak dapat diubah demi keamanan akun.</small>
                </div>
                <div class="form-group">
                    <label>Ganti Password</label>
                    <input type="password" name="new_password" minlength="8" placeholder="Kosongkan jika tidak ganti">
                    <small>Minimal 8 karakter</small>
                </div>
            </div>

            <div class="form-group full">
                <label>Foto Profil Saat Ini</label>
                <div style="display:flex; align-items:center; gap:15px; margin-bottom:10px;">
                    <img src="<?php echo esc_url($display_data->foto_profil); ?>" width="60" height="60" style="border-radius:50%; border:1px solid var(--gold);">
                    <input type="file" name="foto_profil" accept="image/jpeg,image/png,image/webp">
                </div>
                <small>Max 2MB, JPG/PNG/WEBP. Biarkan kosong jika tidak ingin ganti.</small>
            </div>

            <h3 style="color:var(--gold); margin-top:30px;">📋 Biodata</h3>
            <div class="form-row">
                <div class="form-group">
                    <label>No. HP</label>
                    <input type="tel" name="no_hp" value="<?php echo esc_attr($display_data->no_hp); ?>">
                </div>
                <div class="form-group">
				<label>Tanggal Lahir</label>
				<input type="date" name="tanggal_lahir" value="<?php echo esc_attr($display_data->tanggal_lahir); ?>" required>
			</div>
            </div>

            <div class="form-row">
                <div class="form-group full" style="margin-bottom: 20px;">
                    <label>Domisili Provinsi 1 <span class="required">*</span></label>
                    <select name="provinsi_1" id="edit_domisili_provinsi_1" class="lx-select2" style="width:100%;">
                        <option value="">Pilih Provinsi</option>
                    </select>
                    <input type="hidden" name="nama_provinsi_1" id="edit_nama_provinsi_1" value="<?php echo esc_attr($dom_prov_1); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group full">
                    <label>Kota/Kabupaten 1 <span class="required">*</span> (Maksimal 3)</label>
                    <select name="kota_kabupaten_1[]" id="edit_domisili_kota_1" class="lx-select2-multi" multiple="multiple" style="width:100%;">
                        <?php foreach ($dom_kota_1 as $kt): ?>
                            <option value="<?php echo esc_attr(trim($kt)); ?>" selected><?php echo esc_html(trim($kt)); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div id="edit_domisili_provinsi_2_wrap" style="display:<?php echo $dom_prov_2 ? 'block' : 'none'; ?>;">
                <div class="form-row">
                    <div class="form-group full" style="margin-bottom: 20px;">
                        <label>Domisili Provinsi 2 (Opsional)</label>
                        <select name="provinsi_2" id="edit_domisili_provinsi_2" class="lx-select2" style="width:100%;">
                            <option value="">Pilih Provinsi</option>
                        </select>
                        <input type="hidden" name="nama_provinsi_2" id="edit_nama_provinsi_2" value="<?php echo esc_attr($dom_prov_2); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group full">
                        <label>Kota/Kabupaten 2 (Maksimal 3)</label>
                        <select name="kota_kabupaten_2[]" id="edit_domisili_kota_2" class="lx-select2-multi" multiple="multiple" style="width:100%;">
                            <?php foreach ($dom_kota_2 as $kt): ?>
                                <option value="<?php echo esc_attr(trim($kt)); ?>" selected><?php echo esc_html(trim($kt)); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <?php if (empty($dom_prov_2)): ?>
                <button type="button" id="btn-tambah-provinsi-edit" class="btn-tambah-provinsi" style="margin-bottom:15px;">+ Tambah Provinsi Lain</button>
            <?php endif; ?>

            <h3 style="color:var(--gold); margin-top:30px;">💼 Data Pekerjaan</h3>
            <div class="form-group full">
                <label>Posisi <span class="required">*</span></label>
                <div class="checkbox-group">
                    <?php 
                    $list_posisi = [
						'F' => '📸 Fotografer',
						'V' => '🎥 Videografer',
						'D' => '🚁 Drone',
						'E' => '✂️ Editor',
						'X' => '🔮 VFX',
						'A' => '🎭 Animator',
						'P' => '🤖 AI Artist - Prompt Engineer'
					];
                    foreach($list_posisi as $key => $label): ?>
                        <label style="color:white; cursor:pointer;">
                            <input type="checkbox" name="posisi[]" value="<?php echo $key; ?>" <?php checked(in_array($key, $posisi_saved)); ?>> 
                            <?php echo $label; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
			
			<div class="form-row">
    <div class="form-group">
        <label>Update CV (PDF)</label>
        <?php if (!empty($display_data->cv_url)): ?>
            <div style="margin-bottom: 10px;">
                <a href="<?php echo esc_url($display_data->cv_url); ?>" target="_blank" style="color: #d4af37; text-decoration: none; font-size: 12px;">
                    <span class="dashicons dashicons-pdf"></span> Lihat CV Saat Ini
                </a>
            </div>
        <?php endif; ?>
        <input type="file" name="cv_file" accept="application/pdf">
        <small>Kosongkan jika tidak ingin mengubah. Max 2MB (PDF).</small>
    </div>

    <div class="form-group">
        <label>Update Sertifikat (Gambar)</label>
        <?php 
        $sertifikat_data = json_decode($display_data->sertifikat_multiple, true);
        if (!empty($sertifikat_data) && is_array($sertifikat_data)): ?>
            <div style="display: flex; gap: 5px; margin-bottom: 10px; overflow-x: auto; padding-bottom: 5px;">
                <?php foreach ($sertifikat_data as $img_url): ?>
                    <img src="<?php echo esc_url($img_url); ?>" width="50" height="50" style="object-fit: cover; border-radius: 4px; border: 1px solid #444;">
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <input type="file" name="sertifikat_files[]" accept="image/jpeg,image/png,image/webp" multiple>
        <small>Pilih file baru untuk mengganti semua sertifikat lama.</small>
    </div>
</div>

            <div class="form-row">
                
                <div class="form-group">
                    <label>Deskripsi Diri</label>
                    <textarea name="deskripsi" rows="3"><?php echo esc_textarea($display_data->deskripsi); ?></textarea>
					<small style="color:var(--text-muted);">*Tidak mencantumkan no WA dan link sosmed</small><br>
                </div>
            </div>
			<div class="form-group full">
    <label>Link Portofolio Eksternal (Max 5)</label>
    <div id="porto-link-container">
        <?php 
        // Ambil data link dari database
        $porto_links = json_decode($display_data->porto_links, true);
        
        // Jika kosong, sediakan minimal 1 input kosong
        if (empty($porto_links)) {
            $porto_links = array(''); 
        }

        foreach ($porto_links as $index => $link) : ?>
            <div class="porto-link-item" style="display: flex; gap: 10px; margin-bottom: 10px;">
                <input type="url" name="porto_links[]" value="<?php echo esc_url($link); ?>" placeholder="https://linkweb.net/" style="flex: 1;">
                <button type="button" class="btn-remove-link" style="<?php echo ($index === 0 && count($porto_links) === 1) ? 'display:none;' : ''; ?> background:#d63638; color:#fff; border:none; padding:0 10px; border-radius:4px; cursor:pointer;">&times;</button>
            </div>
        <?php endforeach; ?>
    </div>
    
    <button type="button" id="btn-add-link" 
        class="button" 
        style="<?php echo (count($porto_links) >= 5) ? 'display:none;' : ''; ?> 
               margin-top: 10px; 
               background-color: #d4af37; 
               color: #ffffff; 
               border: none; 
               padding: 5px 15px; 
               font-weight: bold; 
               border-radius: 4px; 
               cursor: pointer;
               transition: 0.3s;">
    + Tambah Link
</button>
    <small style="display:block; margin-top:5px;">Link tambahan seperti GDrive, Behance, atau Dropbox.</small>
</div>

            <div class="form-row">
                <div class="form-group">
                    <label>Peralatan</label>
                    <textarea name="peralatan" rows="4"><?php echo esc_textarea($display_data->peralatan); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Pricelist/hari <span class="required">*</span></label>
                    <select name="pricelist_perhari" required>
                        <option value="">Pilih Range</option>
                        <option value="dibawah_1jt" <?php selected($display_data->pricelist_perhari, 'dibawah_1jt'); ?>>💰 Dibawah 1 jt</option>
                        <option value="1jt_3jt" <?php selected($display_data->pricelist_perhari, '1jt_3jt'); ?>>💎 1 jt - 3 jt</option>
                        <option value="diatas_3jt" <?php selected($display_data->pricelist_perhari, 'diatas_3jt'); ?>>⭐ Diatas 3 jt</option>
                    </select>
                </div>
            </div>

            <div class="form-group full" style="background: #eee; border-radius: 8px; padding: 10px; color: #000;">
                <label style="color:#000; font-weight:bold;">Pricelist Detail</label>
                <?php wp_editor($display_data->pricelist, 'pricelist', [
                    'textarea_name' => 'pricelist',
                    'textarea_rows' => 6,
                    'media_buttons' => false,
                    'teeny' => true
                ]); ?>
            </div>

           <h3 style="color:var(--gold); margin-top:30px; border-bottom:1px solid var(--border-gold); padding-bottom:10px;">
    🌐 Social Media Profiles
</h3>

<div class="form-row">
    <div class="form-group">
        <label>Facebook URL</label>
        <input type="url" name="facebook" value="<?php echo esc_url($display_data->facebook); ?>" placeholder="https://facebook.com/username">
    </div>
    <div class="form-group">
        <label>Instagram URL</label>
        <input type="url" name="instagram" value="<?php echo esc_url($display_data->instagram); ?>" placeholder="https://instagram.com/username">
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label>TikTok URL</label>
        <input type="url" name="tiktok" value="<?php echo esc_url($display_data->tiktok); ?>" placeholder="https://tiktok.com/@username">
    </div>
    <div class="form-group">
        <label>Threads URL</label>
        <input type="url" name="thread" value="<?php echo esc_url($display_data->thread); ?>" placeholder="https://threads.net/@username">
    </div>
</div>

<div class="form-group full">
    <label>YouTube Channel URL</label>
    <input type="url" name="youtube" value="<?php echo esc_url($display_data->youtube); ?>" placeholder="https://youtube.com/@username">
</div>

            <div class="form-group full">
    <label>
				</label>
    <div class="tag-system-container">
        <div class="tag-input-wrapper" id="tagInputWrapper">
            <input type="text" id="tagInput" placeholder="Ketik tag lalu tekan Enter/Koma...">
        </div>
        <input type="hidden" name="tag" id="tagHiddenInput" value="<?php echo esc_attr($display_data->tag); ?>">
    </div>
    <small style="color:var(--text-muted);">Maksimal 10 tags. Contoh: cinematic, wedding, colorist</small>
</div>

            <button type="submit" class="btn-update">
                🚀 Simpan Perubahan Profil
            </button>
        </form>
    </div>v>
    <small style="color:var(--text-muted);">Maksimal 10 tags. Contoh: cinematic, wedding, colorist</small>
</div>

            <button type="submit" class="btn-update">
                🚀 Simpan Perubahan Profil
            </button>
        </form>
    </div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>
.select2-container--default .select2-selection--single,
.select2-container--default .select2-selection--multiple {
    background-color: #1a1a1a !important;
    border: 1px solid #333 !important;
    border-radius: 4px;
    padding: 5px;
    min-height: 42px;
}
.select2-container--default .select2-selection--single .select2-selection__rendered,
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    color: #fff !important;
}
.select2-container--default .select2-results__option {
    background-color: #1a1a1a;
    color: #fff;
}
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #d4af37 !important;
    color: #000 !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #d4af37 !important;
    border: none;
    color: #000 !important;
    font-weight: bold;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #000 !important;
    margin-right: 5px;
}
.select2-container--default .select2-selection--multiple .select2-selection__placeholder {
    margin-top: 0;
    line-height: 32px;
}
.select2-container--default .select2-selection--multiple .select2-search--inline {
    width: 0;
    opacity: 0;
    overflow: hidden;
}
.select2-container--default.select2-container--open .select2-selection--multiple .select2-search--inline,
.select2-container--default .select2-selection--multiple:has(.select2-selection__choice) .select2-search--inline {
    width: auto;
    opacity: 1;
}
.form-row {
    overflow: visible !important;
}
.form-group.full {
    overflow: visible !important;
}
.btn-tambah-provinsi {
    background: transparent;
    color: #d4af37;
    border: 1px dashed #d4af37;
    cursor: pointer;
    padding: 6px 14px;
    border-radius: 4px;
    font-size: 0.9rem;
}
.btn-tambah-provinsi:hover {
    background: rgba(212, 175, 55, 0.1);
}
</style>
<script>
jQuery(document).ready(function($) {
    function initProvKotaEdit(provSelector, kotaSelector, hiddenSelector, ajaxUrl, savedProv, savedKota) {
        var $prov = $(provSelector);
        var $kota = $(kotaSelector);
        var $hidden = $(hiddenSelector);
        var isInitializing = true;

        $prov.select2({ placeholder: "Pilih Provinsi", width: '100%' });
        $kota.select2({
            placeholder: "Pilih maksimal 3 Kota/Kabupaten",
            maximumSelectionLength: 3,
            width: '100%'
        });

        $prov.on('change', function() {
            if (isInitializing) return;
            var provId = $(this).val();
            var provName = $(this).find(':selected').data('name');
            $hidden.val(provName || '');
            $kota.html('<option value="">Memuat data kota...</option>').prop('disabled', true);
            if (provId) {
                $.getJSON(ajaxUrl, { action: 'fetch_wilayah', type: 'regencies', prov_id: provId })
                    .done(function(regencies) {
                        var options = '';
                        regencies.forEach(function(reg) {
                            options += '<option value="' + reg.name + '">' + reg.name + '</option>';
                        });
                        $kota.html(options).prop('disabled', false).trigger('change');
                    })
                    .fail(function() {
                        $kota.html('<option value="">Gagal memuat data</option>').prop('disabled', false);
                    });
            } else {
                $kota.html('<option value="">Pilih provinsi terlebih dahulu</option>').prop('disabled', true);
            }
        });

        $.getJSON(ajaxUrl, { action: 'fetch_wilayah', type: 'provinces' })
            .done(function(provinces) {
                var options = '<option value="">Pilih Provinsi</option>';
                var matchedId = null;
                var initialKota = savedKota.slice();
                provinces.forEach(function(prov) {
                    var selected = (prov.name === savedProv) ? 'selected' : '';
                    if (selected) matchedId = prov.id;
                    options += '<option value="' + prov.id + '" data-name="' + prov.name + '" ' + selected + '>' + prov.name + '</option>';
                });
                $prov.html(options).trigger('change');
                if (matchedId) {
                    $.getJSON(ajaxUrl, { action: 'fetch_wilayah', type: 'regencies', prov_id: matchedId })
                        .done(function(regencies) {
                            var options = '';
                            regencies.forEach(function(reg) {
                                var selected = (initialKota.indexOf(reg.name) !== -1) ? 'selected' : '';
                                options += '<option value="' + reg.name + '" ' + selected + '>' + reg.name + '</option>';
                            });
                            $kota.html(options).prop('disabled', false).trigger('change');
                            isInitializing = false;
                        })
                        .fail(function() {
                            $kota.html('<option value="">Gagal memuat data</option>').prop('disabled', false);
                            isInitializing = false;
                        });
                } else {
                    isInitializing = false;
                }
            })
            .fail(function() {
                $prov.html('<option value="">Gagal memuat data</option>');
                isInitializing = false;
            });
    }

    var ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';
    var savedProv1 = $('#edit_nama_provinsi_1').val();
    var savedKota1 = [];
    <?php foreach ($dom_kota_1 as $kt): ?>
    savedKota1.push('<?php echo esc_js(trim($kt)); ?>');
    <?php endforeach; ?>
    initProvKotaEdit('#edit_domisili_provinsi_1', '#edit_domisili_kota_1', '#edit_nama_provinsi_1', ajaxUrl, savedProv1, savedKota1);

    var savedProv2 = $('#edit_nama_provinsi_2').val();
    var savedKota2 = [];
    <?php foreach ($dom_kota_2 as $kt): ?>
    savedKota2.push('<?php echo esc_js(trim($kt)); ?>');
    <?php endforeach; ?>
    if (savedProv2) {
        initProvKotaEdit('#edit_domisili_provinsi_2', '#edit_domisili_kota_2', '#edit_nama_provinsi_2', ajaxUrl, savedProv2, savedKota2);
    } else {
        $('#edit_domisili_provinsi_2').select2({ placeholder: "Pilih Provinsi", width: '100%' });
        $('#edit_domisili_kota_2').select2({
            placeholder: "Pilih maksimal 3 Kota/Kabupaten",
            maximumSelectionLength: 3,
            width: '100%'
        });
        $.getJSON(ajaxUrl, { action: 'fetch_wilayah', type: 'provinces' })
            .done(function(provinces) {
                var options = '<option value="">Pilih Provinsi</option>';
                provinces.forEach(function(prov) {
                    options += '<option value="' + prov.id + '" data-name="' + prov.name + '">' + prov.name + '</option>';
                });
                $('#edit_domisili_provinsi_2').html(options).trigger('change');
            });
        $('#edit_domisili_provinsi_2').on('change', function() {
            var provId = $(this).val();
            var provName = $(this).find(':selected').data('name');
            $('#edit_nama_provinsi_2').val(provName || '');
            $('#edit_domisili_kota_2').html('<option value="">Memuat data kota...</option>').prop('disabled', true);
            if (provId) {
                $.getJSON(ajaxUrl, { action: 'fetch_wilayah', type: 'regencies', prov_id: provId })
                    .done(function(regencies) {
                        var options = '';
                        regencies.forEach(function(reg) {
                            options += '<option value="' + reg.name + '">' + reg.name + '</option>';
                        });
                        $('#edit_domisili_kota_2').html(options).prop('disabled', false).trigger('change');
                    })
                    .fail(function() {
                        $('#edit_domisili_kota_2').html('<option value="">Gagal memuat data</option>').prop('disabled', false);
                    });
            }
        });
    }

    $('#btn-tambah-provinsi-edit').on('click', function() {
        $('#edit_domisili_provinsi_2_wrap').slideDown();
        $(this).hide();
        $('#edit_domisili_provinsi_2').trigger('change');
    });
});
</script>
<script>
(function() {
    const tagInput = document.getElementById('tagInput');
    const wrapper = document.getElementById('tagInputWrapper');
    const hiddenInput = document.getElementById('tagHiddenInput');
    const maxTags = 10;
    
    // Ambil data awal dari hidden input (data dari DB)
    let tags = hiddenInput.value ? hiddenInput.value.split(',').filter(t => t !== "") : [];

    function renderTags() {
        // Simpan input field agar tidak hilang saat wrapper di-clear
        const inputField = tagInput;
        wrapper.innerHTML = '';
        
        tags.forEach((tag, index) => {
            const tagEl = document.createElement('div');
            tagEl.className = 'tag-item';
            tagEl.innerHTML = `
                #${tag}
                <button type="button" class="tag-remove" data-index="${index}">&times;</button>
            `;
            wrapper.appendChild(tagEl);
        });
        
        wrapper.appendChild(inputField);
        inputField.focus();
    }

    function addTag(text) {
        if (tags.length >= maxTags) return;
        
        // Bersihkan input: lowercase, hilangkan karakter aneh
        const tag = text.trim().toLowerCase().replace(/[^a-z0-9\s-]/g, '');
        
        if (tag.length >= 2 && !tags.includes(tag)) {
            tags.push(tag);
            updateHiddenInput();
            renderTags();
        }
        tagInput.value = '';
    }

    function removeTag(index) {
        tags.splice(index, 1);
        updateHiddenInput();
        renderTags();
    }

    function updateHiddenInput() {
        hiddenInput.value = tags.join(',');
    }

    // Event Listeners
    tagInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            addTag(this.value);
        }
        if (e.key === 'Backspace' && this.value === '' && tags.length > 0) {
            removeTag(tags.length - 1);
        }
    });

    // Menangani klik pada tombol remove menggunakan event delegation
    wrapper.addEventListener('click', function(e) {
        if (e.target.classList.contains('tag-remove')) {
            const index = e.target.getAttribute('data-index');
            removeTag(index);
        }
    });

    // Inisialisasi awal
    renderTags();
    
})();
</script>
<script>
jQuery(document).ready(function($) {
    var maxLinks = 5;
    var container = $('#porto-link-container');
    var addButton = $('#btn-add-link');

    // Fungsi Update Tombol Tambah
    function updateAddButton() {
        if (container.find('.porto-link-item').length >= maxLinks) {
            addButton.hide();
        } else {
            addButton.show();
        }
    }

    // Tambah Link Baru
    addButton.on('click', function() {
        if (container.find('.porto-link-item').length < maxLinks) {
            var newField = container.find('.porto-link-item').first().clone();
            newField.find('input').val(''); // Kosongkan input baru
            newField.find('.btn-remove-link').show(); // Pastikan tombol hapus muncul
            container.append(newField);
        }
        updateAddButton();
    });

    // Hapus Link
    container.on('click', '.btn-remove-link', function() {
        if (container.find('.porto-link-item').length > 1) {
            $(this).parent('.porto-link-item').remove();
        } else {
            // Jika tinggal satu, kosongkan saja isinya tapi jangan hapus barisnya
            $(this).siblings('input').val('');
            $(this).hide();
        }
        updateAddButton();
    });
});
</script>
    <?php
}

add_filter('wp_get_nav_menu_items', 'custom_personel_menu_filter', 20, 2);

function custom_personel_menu_filter($items, $menu) {
    // Hindari muncul di dashboard admin
    if (is_admin()) return $items;

    // Pastikan session aktif
    if (!session_id()) { session_start(); }

    // Cek apakah personel sedang login
    if (isset($_SESSION['personel_id'])) {
        
        // Tambahkan item Dashboard
        $new_item = new stdClass();
        $new_item->ID = 999991; // ID unik fiktif
        $new_item->db_id = 999991;
        $new_item->title = 'Dashboard';
        $new_item->url = home_url('/dashboard-personel');
        $new_item->menu_order = count($items) + 1;
        $new_item->menu_item_parent = 0;
        $new_item->type = 'custom';
        $new_item->object = 'custom';
        $new_item->object_id = 999991;
        $new_item->classes = array('menu-item', 'menu-dashboard-gold'); // Class CSS
        $new_item->target = '';
        $new_item->attr_title = '';
        $new_item->description = '';
        $new_item->xfn = '';
        $new_item->status = 'publish';

        $items[] = $new_item;

        
    }

    return $items;
}
function personel_porto_admin_page() {
    global $wpdb;
    $table_porto = 'wp9y_portofolio';
    $table_personel = 'wp9y_personel';

    // --- LOGIKA APPROVAL & DELETE ---
    if (isset($_POST['porto_action']) && wp_verify_nonce($_POST['_wpnonce'], 'porto_admin_nonce')) {
        $id = intval($_POST['porto_id']);
        
        if ($_POST['porto_action'] == 'approve') {
            $wpdb->update($table_porto, ['status' => 'approved'], ['id' => $id]);
            echo '<div class="notice notice-success is-dismissible"><p>✅ Portofolio Approved!</p></div>';
        }
        
        if ($_POST['porto_action'] == 'delete') {
            $foto = $wpdb->get_var($wpdb->prepare("SELECT foto_url FROM $table_porto WHERE id = %d", $id));
            if ($foto) {
                $file_path = str_replace(site_url('/'), ABSPATH, $foto);
                if (file_exists($file_path)) unlink($file_path);
            }
            $wpdb->delete($table_porto, ['id' => $id]);
            echo '<div class="notice notice-success is-dismissible"><p>🗑️ Portofolio Berhasil Dihapus!</p></div>';
        }
    }

    // Ambil data dengan Join untuk mendapatkan nama personel
    $results = $wpdb->get_results("
        SELECT p.*, per.nama_panggilan, per.kode_nama 
        FROM $table_porto p 
        JOIN $table_personel per ON p.personel_id = per.id 
        ORDER BY p.created_at DESC
    ");
    ?>

    <div class="wrap">
        <h1 class="wp-heading-inline">📸 Moderasi Portofolio Foto</h1>
        <hr class="wp-header-end">

        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-top: 20px;">
            <table id="adminPortoTable" class="wp-list-table widefat fixed striped">
    <thead>
        <tr>
            <th width="100">Foto</th>
			<th>Judul</th>
            <th>Personel</th>
            <th>Detail</th>
            <th>Deskripsi & Tags</th>
            <th width="100">Status</th>
            <th width="180">Aksi</th> </tr>
    </thead>
    <tbody>
        <?php foreach ($results as $row): ?>
        <tr>
            <td>
                <a href="<?php echo esc_url($row->foto_url); ?>" target="_blank">
                    <img src="<?php echo esc_url($row->foto_url); ?>" width="80" height="80" style="object-fit:cover; border-radius:4px; border:1px solid #ddd;">
                </a>
            </td>
			<td><strong><?php echo esc_html($row->judul); ?></strong></td>
            <td>
                <strong><?php echo esc_html($row->nama_panggilan); ?></strong><br>
                <small style="color:#666;"><?php echo esc_html($row->kode_nama); ?></small>
            </td>
            <td>
                <span class="dashicons dashicons-location" style="font-size:16px; color:#d4af37;"></span> <?php echo esc_html($row->lokasi); ?><br>
                <span class="dashicons dashicons-calendar-alt" style="font-size:16px; color:#d4af37;"></span> <?php echo esc_html($row->tahun); ?>
            </td>
            <td>
                <p style="margin:0; font-size:12px; line-height:1.4;">
                    <?php echo esc_html($row->deskripsi); ?>
                </p>
                <div style="margin-top:5px;">
                    <?php 
                    if($row->tags) {
                        $tags = explode(',', $row->tags);
                        foreach($tags as $t) echo '<span class="tag-badge">#' . trim($t) . '</span> ';
                    }
                    ?>
                </div>
            </td>
            <td>
                <span class="status-pill status-badge <?php echo $row->status; ?>">
                    <?php echo ucfirst($row->status); ?>
                </span>
            </td>
            <td>
                <div style="margin-bottom: 5px;">
                    <?php if ($row->status === 'approved'): ?>
                        <button type="button" class="btn-porto-status status-active" 
                                data-id="<?php echo $row->id; ?>" 
                                data-type="foto" 
                                data-status="approved">NON-AKTIFKAN</button>
                    <?php elseif ($row->status === 'non-aktif'): ?>
                        <button type="button" class="btn-porto-status status-inactive" 
                                data-id="<?php echo $row->id; ?>" 
                                data-type="foto" 
                                data-status="non-aktif">AKTIFKAN</button>
                    <?php endif; ?>
                </div>

                <form method="post" style="display:inline;">
                    <?php wp_nonce_field('porto_admin_nonce'); ?>
                    <input type="hidden" name="porto_id" value="<?php echo $row->id; ?>">
                    
                    <?php if ($row->status == 'pending'): ?>
                        <button type="submit" name="porto_action" value="approve" class="button button-primary button-small">Approve</button>
                    <?php endif; ?>
                    
                    <button type="submit" name="porto_action" value="delete" class="button button-small" onclick="return confirm('Hapus portofolio ini?')">🗑️</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
        </div>
    </div>

    <style>
        .tag-badge { background: #f0f0f0; color: #444; padding: 2px 6px; border-radius: 4px; font-size: 10px; margin-right: 3px; border: 1px solid #ddd; }
        .status-pill { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .status-pill.approved { background: #dcfce7; color: #166534; }
        .status-pill.pending { background: #fef9c3; color: #854d0e; }
        
        #adminPortoTable td { vertical-align: middle; }
        #adminPortoTable_wrapper { margin-top: 20px; }
    </style>

    <script>
    jQuery(document).ready(function($) {
        $('#adminPortoTable').DataTable({
            "pageLength": 10,
            "order": [[4, "desc"]], // Urutkan status pending di atas (secara alfabet)
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
            }
        });
    });
    </script>
    <?php
}

/**
 * Handler AJAX untuk Aktif/Non-Aktif Portofolio (Foto & Video)
 */
add_action('wp_ajax_toggle_porto_status', 'lx_toggle_porto_status_handler');
function lx_toggle_porto_status_handler() {
    global $wpdb;

    $id     = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $type   = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : 'foto';
    $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';

    if ($id <= 0) wp_send_json_error('ID tidak valid.');

    // Pilih tabel berdasarkan type
    $table = ($type === 'video') ? 'wp9y_portofolio_video' : 'wp9y_portofolio';
    
    // Tentukan status baru
    $new_status = ($status === 'approved') ? 'non-aktif' : 'approved';

    $updated = $wpdb->update(
        $table,
        array('status' => $new_status),
        array('id' => $id),
        array('%s'),
        array('%d')
    );

    if ($updated !== false) {
        wp_send_json_success(array('new_status' => $new_status));
    } else {
        wp_send_json_error('Database error.');
    }
    wp_die();
}
add_action('admin_footer', 'lx_porto_status_scripts');
function lx_porto_status_scripts() {
    // Pastikan script hanya jalan di halaman admin yang relevan
    if (isset($_GET['page']) && (strpos($_GET['page'], 'porto') !== false || strpos($_GET['page'], 'video') !== false)) {
        ?>
        <style>
            /* Style Tombol Toggle */
            .btn-porto-status {
                display: inline-block;
                padding: 5px 10px;
                border-radius: 4px;
                font-size: 10px;
                font-weight: bold;
                color: #fff !important;
                border: none;
                cursor: pointer;
                min-width: 100px;
                text-align: center;
                transition: 0.3s;
                text-transform: uppercase;
                margin-bottom: 5px;
            }
            
            /* Status Approved -> Tombol Non-Aktifkan (Merah) */
            .btn-porto-status.status-active {
                background: #d63638 !important;
            }

            /* Status Non-Aktif -> Tombol Aktifkan (Hijau) */
            .btn-porto-status.status-inactive {
                background: #00a32a !important;
            }

            .btn-porto-status:hover {
                opacity: 0.8;
                box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            }

            .btn-porto-status:disabled {
                background: #bbb !important;
                cursor: wait;
            }

            /* Perbaikan warna teks pill status */
            .status-pill.non-aktif { background: #eee; color: #666; }
            .status-pill.approved { background: #dff0d8; color: #3c763d; }
        </style>

        <script>
        jQuery(document).ready(function($) {
            $(document).on('click', '.btn-porto-status', function(e) {
                e.preventDefault();
                var btn = $(this);
                var id = btn.data('id');
                var type = btn.data('type');
                var status = btn.data('status');

                btn.prop('disabled', true).text('...');

                $.post(ajaxurl, {
                    action: 'toggle_porto_status',
                    id: id,
                    type: type,
                    status: status
                }, function(res) {
                    if (res.success) {
                        var ns = res.data.new_status;
                        btn.data('status', ns);
                        
                        if (ns === 'approved') {
                            btn.text('NON-AKTIFKAN')
                               .addClass('status-active')
                               .removeClass('status-inactive');
                            
                            // Update teks badge status di baris yang sama
                            btn.closest('tr').find('.status-badge')
                               .text('Approved').attr('class', 'status-badge status-pill approved');
                        } else {
                            btn.text('AKTIFKAN')
                               .addClass('status-inactive')
                               .removeClass('status-active');
                            
                            btn.closest('tr').find('.status-badge')
                               .text('Non-aktif').attr('class', 'status-badge status-pill non-aktif');
                        }
                    } else {
                        (function(){var d=document.createElement('div');d.style.cssText='position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:999999;display:flex;align-items:center;justify-content:center;';d.innerHTML='<div style="background:#1a1a1a;border:1px solid #ff4444;border-radius:8px;padding:30px 40px;max-width:400px;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,0.5);"><div style="font-size:48px;margin-bottom:10px;">❌</div><p style="color:#fff;font-size:16px;margin:10px 0 20px;">Gagal mengubah status.</p><button onclick="this.parentNode.parentNode.remove()" style="background:#ff4444;color:#fff;border:none;padding:10px 24px;border-radius:4px;font-weight:bold;cursor:pointer;">OK</button></div>';document.body.appendChild(d);})();
                    }
                }).fail(function() {
                    (function(){var d=document.createElement('div');d.style.cssText='position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:999999;display:flex;align-items:center;justify-content:center;';d.innerHTML='<div style="background:#1a1a1a;border:1px solid #ff4444;border-radius:8px;padding:30px 40px;max-width:400px;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,0.5);"><div style="font-size:48px;margin-bottom:10px;">❌</div><p style="color:#fff;font-size:16px;margin:10px 0 20px;">Server Error.</p><button onclick="this.parentNode.parentNode.remove()" style="background:#ff4444;color:#fff;border:none;padding:10px 24px;border-radius:4px;font-weight:bold;cursor:pointer;">OK</button></div>';document.body.appendChild(d);})();
                }).always(function() {
                    btn.prop('disabled', false);
                });
            });
        });
        </script>
        <?php
    }
}
function get_video_embed_url($url) {
    // YouTube
    if (preg_match('/(youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $match)) {
        return 'https://www.youtube.com/embed/' . $match[2];
    }
    
    return $url;
}

function render_tab_form_video($personel, $video_id = 0) {
    global $wpdb;
    $edit_data = $video_id ? $wpdb->get_row($wpdb->prepare("SELECT * FROM wp9y_portofolio_video WHERE id = %d", $video_id)) : null;
    ?>
    <div class="form-edit-container">
        <h2 style="color:var(--gold);"><?php echo $video_id ? '✏️ Edit Video' : '🎥 Tambah Portofolio Video'; ?></h2>
        <form method="post">
            <input type="hidden" name="video_id" value="<?php echo $video_id; ?>">
            <div class="form-group full" style="margin-bottom: 15px;">
			<label style="display:block; margin-bottom:5px; color:#d4af37; font-weight:bold;">
				Judul Portofolio <span style="color:red;">*</span>
			</label>
			<input type="text" name="judul" value="<?php echo $edit_data ? $edit_data->judul : ''; ?>" required 
				   placeholder="Judul Portofolio" 
				   style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: #fff; border-radius: 4px;">
		</div>
            <div class="form-group full">
                <label>URL Video (YouTube) <span class="required">*</span></label>
                <input type="url" name="video_url" value="<?php echo $edit_data ? esc_url($edit_data->video_url) : ''; ?>" placeholder="https://www.youtube.com/watch?v=xxxx" required>
                <small>Pastikan video diset "Public" atau "Unlisted".</small>
            </div>
			 <div class="form-group full">
                <label>Deskripsi</label>
                <textarea name="deskripsi" rows="4"><?php echo $edit_data ? esc_textarea($edit_data->deskripsi) : ''; ?></textarea>
				 <small style="color:var(--text-muted);">*Tidak mencantumkan no WA dan link sosmed</small><br>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" value="<?php echo $edit_data ? $edit_data->tanggal_kegiatan : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Lokasi</label>
                    <input type="text" name="lokasi" value="<?php echo $edit_data ? esc_attr($edit_data->lokasi) : ''; ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tahun</label>
                    <select name="tahun" required>
                        <?php 
                        $year = date('Y');
                        for($i=0; $i<=15; $i++) {
                            $y = $year - $i;
                            $sel = ($edit_data && $edit_data->tahun == $y) ? 'selected' : '';
                            echo "<option value='$y' $sel>$y</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group full">
                <label>Tags</label>
                <div class="tag-system-container">
                    <div class="tag-input-wrapper" id="tagInputWrapperVideo">
                        <input type="text" id="tagInputVideo" placeholder="Ketik tag...">
                    </div>
                    <input type="hidden" name="tags" id="tagHiddenInputVideo" value="<?php echo $edit_data ? esc_attr($edit_data->tags) : ''; ?>">
                </div>
            </div>

            <?php render_portfolio_category_selection($video_id, 'video'); ?>

            <button type="submit" name="<?php echo $video_id ? 'update_video' : 'submit_video'; ?>" class="btn-update">
                🚀 <?php echo $video_id ? 'Simpan Perubahan' : 'Unggah Video'; ?>
            </button>
        </form>
    </div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tagInput = document.getElementById('tagInputVideo');
    const wrapper = document.getElementById('tagInputWrapperVideo');
    const hiddenInput = document.getElementById('tagHiddenInputVideo');
    
    if(!tagInput) return; // Guard clause

    let tags = hiddenInput.value ? hiddenInput.value.split(',').filter(t => t !== "") : [];

    function renderTags() {
        wrapper.querySelectorAll('.tag-item').forEach(el => el.remove());
        tags.forEach((tag, index) => {
            const tagEl = document.createElement('div');
            tagEl.className = 'tag-item';
            tagEl.innerHTML = `#${tag} <button type="button" class="tag-remove" data-index="${index}">&times;</button>`;
            wrapper.insertBefore(tagEl, tagInput);
        });
    }

    tagInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            const val = this.value.trim().toLowerCase().replace(/[^a-z0-9\s-]/g, '');
            if (val && !tags.includes(val) && tags.length < 10) {
                tags.push(val);
                hiddenInput.value = tags.join(',');
                renderTags();
            }
            this.value = '';
        }
    });

    wrapper.addEventListener('click', function(e) {
        if (e.target.classList.contains('tag-remove')) {
            tags.splice(e.target.dataset.index, 1);
            hiddenInput.value = tags.join(',');
            renderTags();
        }
    });

    renderTags();
});
</script>
    <?php
}
function render_list_portofolio_video($personel) {
    global $wpdb;
    $videos = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM wp9y_portofolio_video WHERE personel_id = %d ORDER BY created_at DESC", 
        $personel->id
    ));
    ?>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="color:var(--gold); margin:0;">🎥 Video Portofolio Saya</h2>
        <?php 
$kuota_video = get_status_kuota_personel($personel->id, 'video');

if (!$kuota_video['is_full']) : ?>
    <a href="?tab=video&action=add" class="btn-update" style="width: auto; padding: 10px 20px; text-decoration:none; font-size:14px; color:#000;">
        + Unggah Video Baru
    </a>
<?php else : ?>
    <span style="display: inline-block; padding: 10px 20px; background: #331a1a; border: 1px solid #ff4d4d; color: #ff4d4d; border-radius: 5px; font-size: 13px; font-weight: bold;">
        ⚠️ Kuota Video Penuh (Maks. 8)
    </span>
<?php endif; ?>
    </div>

    <div class="porto-grid">
        <?php if($videos): foreach($videos as $v): ?>
            <div class="porto-item video-card">
                <div class="porto-video-wrapper">
                    <iframe src="<?php echo get_video_embed_url($v->video_url); ?>" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                    </iframe>
                    <div class="status-overlay">
                        <span class="status-badge-new <?php echo ($v->status == 'approved') ? 'st-approved' : 'st-pending'; ?>">
                            <?php echo strtoupper($v->status); ?>
                        </span>
                    </div>
                </div>
                
                <div class="porto-content">
					<h3 class="porto-title" style="margin: 0 0 10px 0; color: #d4af37; font-size: 16px; font-weight: bold;">
                        <?php echo esc_html($v->judul); ?>
                    </h3>
                    <div class="porto-meta-row">
						
                        <span class="p-year">🗓️ <?php echo esc_html($v->tahun); ?></span>
                        <span class="p-loc">📍 <?php echo esc_html($v->lokasi); ?></span>
                    </div>
                    
                    <?php if(!empty($v->tags)): ?>
                        <div class="porto-tags">
                            <?php 
                                $tags_array = explode(',', $v->tags);
                                foreach($tags_array as $t) {
                                    echo '<span class="tag-pill">#' . trim(esc_html($t)) . '</span> ';
                                }
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <p class="porto-desc">
                        <?php echo !empty($v->deskripsi) ? wp_trim_words(esc_html($v->deskripsi), 15, '...') : '<i style="opacity:0.5;">Tidak ada deskripsi.</i>'; ?>
                    </p>

                    <div class="porto-actions">
                        <a href="?tab=video&action=edit&id=<?php echo $v->id; ?>" class="btn-edit-porto" style="color: #000">✏️ Edit</a>
                        <a href="?tab=video&action=delete&id=<?php echo $v->id; ?>" class="btn-delete-porto" onclick="return confirm('Hapus video ini?')">🗑️</a>
                    </div>
                </div>
            </div>
        <?php endforeach; else: ?>
            <div class="empty-state">Belum ada video yang diunggah.</div>
        <?php endif; ?>
    </div>

    <style>
    /* Grid 2 Kolom khusus Video */
    .porto-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
    }

    .video-card {
        background: #161616;
        border: 1px solid #333;
        border-radius: 12px;
        overflow: hidden;
        transition: border-color 0.3s ease;
    }

    .video-card:hover {
        border-color: #d4af37;
    }

    /* Ratio 16:9 agar video responsif */
    .porto-video-wrapper {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 aspect ratio */
        height: 0;
        overflow: hidden;
        background: #000;
    }

    .porto-video-wrapper iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .status-overlay {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 5;
    }

    .status-badge-new {
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        box-shadow: 0 2px 5px rgba(0,0,0,0.5);
    }

    .st-approved { background: #dcfce7; color: #166534; }
    .st-pending { background: #fef9c3; color: #854d0e; }

    .porto-content {
        padding: 18px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .porto-meta-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        border-bottom: 1px solid #222;
        padding-bottom: 8px;
    }

    .p-year, .p-loc {
        font-size: 11px;
        font-weight: 600;
        color: #d4af37;
    }

    .porto-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-bottom: 10px;
    }

    .tag-pill {
        font-size: 10px;
        background: #222;
        color: #aaa;
        padding: 2px 8px;
        border-radius: 3px;
        border: 1px solid #333;
    }

    .porto-desc {
        font-size: 13px;
        color: #ccc;
        line-height: 1.6;
        margin-bottom: 15px;
        min-height: 40px;
    }

    .porto-actions {
        display: flex;
        gap: 10px;
        margin-top: auto;
    }

    .btn-edit-porto {
        flex: 1;
        background: #d4af37;
        color: #000;
        text-align: center;
        padding: 10px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 700;
        font-size: 12px;
        transition: 0.3s;
    }

    .btn-edit-porto:hover {
        background: #fff;
        color: #000;
    }

    .btn-delete-porto {
        background: #222;
        color: #ff4d4d;
        padding: 10px 15px;
        border-radius: 6px;
        text-decoration: none;
        border: 1px solid #333;
        transition: 0.3s;
    }

    .btn-delete-porto:hover {
        background: #ff4d4d;
        color: #fff;
        border-color: #ff4d4d;
    }

    .empty-state {
        grid-column: 1/-1;
        text-align: center;
        padding: 60px;
        border: 1px dashed #444;
        border-radius: 12px;
        color: #666;
    }

    /* Responsif HP */
    @media (max-width: 768px) {
        .porto-grid {
            grid-template-columns: 1fr;
        }
    }
    </style>
    <?php
}

function personel_video_admin_page() {
    global $wpdb;
    $table_video = 'wp9y_portofolio_video';
    $table_personel = 'wp9y_personel';

    // --- LOGIKA APPROVAL & DELETE ---
    if (isset($_POST['video_admin_action']) && wp_verify_nonce($_POST['_wpnonce'], 'video_admin_nonce')) {
        $id = intval($_POST['video_id']);
        
        if ($_POST['video_admin_action'] == 'approve') {
            $wpdb->update($table_video, ['status' => 'approved'], ['id' => $id]);
            echo '<div class="notice notice-success is-dismissible"><p>✅ Video Berhasil Di-approve!</p></div>';
        }
        
        if ($_POST['video_admin_action'] == 'delete') {
            $wpdb->delete($table_video, ['id' => $id]);
            echo '<div class="notice notice-success is-dismissible"><p>🗑️ Video Telah Dihapus!</p></div>';
        }
    }

    // Ambil data Join
    $results = $wpdb->get_results("
        SELECT v.*, per.nama_panggilan, per.kode_nama 
        FROM $table_video v 
        JOIN $table_personel per ON v.personel_id = per.id 
        ORDER BY v.created_at DESC
    ");
    ?>

    <div class="wrap">
        <h1 class="wp-heading-inline">🎥 Moderasi Portofolio Video</h1>
        <hr class="wp-header-end">

        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-top: 20px;">
            <table id="adminVideoTable" class="wp-list-table widefat fixed striped">
    <thead>
        <tr>
            <th width="180">Preview Video</th>
			<th>Judul</th>
            <th>Personel</th>
            <th>Info Kegiatan</th>
            <th>Deskripsi & Tags</th>
            <th width="100">Status</th>
            <th width="130">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($results as $row): ?>
        <tr>
            <td>
                <div style="position:relative; padding-bottom:56.25%; height:0; overflow:hidden; border-radius:4px; background:#000;">
                    <iframe src="<?php echo get_video_embed_url($row->video_url); ?>" 
                            style="position:absolute; top:0; left:0; width:100%; height:100%;" 
                            frameborder="0" allowfullscreen></iframe>
                </div>
                <small><a href="<?php echo esc_url($row->video_url); ?>" target="_blank">Buka Original Link ↗️</a></small>
            </td>
			<td><strong><?php echo esc_html($row->judul); ?></strong></td>
            <td>
                <strong><?php echo esc_html($row->nama_panggilan); ?></strong><br>
                <code><?php echo esc_html($row->kode_nama); ?></code>
            </td>
            <td>
                <strong>📍 <?php echo esc_html($row->lokasi); ?></strong><br>
                <span class="dashicons dashicons-calendar-alt" style="font-size:14px;"></span> <?php echo esc_html($row->tahun); ?>
            </td>
            <td>
                <p style="margin:0; font-size:12px; line-height:1.4; color:#555;">
                    <?php echo esc_html($row->deskripsi); ?>
                </p>
                <div style="margin-top:5px;">
                    <?php 
                    if($row->tags) {
                        $tags = explode(',', $row->tags);
                        foreach($tags as $t) echo '<span class="v-tag">#' . trim($t) . '</span> ';
                    }
                    ?>
                </div>
            </td>
            <td>
                <span class="v-status status-badge <?php echo $row->status; ?>">
                    <?php echo ucfirst($row->status); ?>
                </span>
            </td>
            <td>
                <div style="margin-bottom: 8px;">
                    <?php if ($row->status === 'approved'): ?>
                        <button type="button" class="btn-porto-status status-active" 
                                data-id="<?php echo $row->id; ?>" 
                                data-type="video" 
                                data-status="approved">NON-AKTIFKAN</button>
                    <?php elseif ($row->status === 'non-aktif'): ?>
                        <button type="button" class="btn-porto-status status-inactive" 
                                data-id="<?php echo $row->id; ?>" 
                                data-type="video" 
                                data-status="non-aktif">AKTIFKAN</button>
                    <?php endif; ?>
                </div>

                <form method="post">
                    <?php wp_nonce_field('video_admin_nonce'); ?>
                    <input type="hidden" name="video_id" value="<?php echo $row->id; ?>">
                    
                    <?php if ($row->status == 'pending'): ?>
                        <button type="submit" name="video_admin_action" value="approve" class="button button-primary" style="background:#2271b1; width:100%; margin-bottom:5px;">Approve</button>
                    <?php endif; ?>
                    
                    <button type="submit" name="video_admin_action" value="delete" class="button button-link-delete" style="color:#d63638; width:100%; text-align:center;" onclick="return confirm('Hapus video ini secara permanen?')">Hapus</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
        </div>
    </div>

    <style>
        .v-tag { background: #f0f0f1; border: 1px solid #c3c4c7; padding: 1px 5px; border-radius: 3px; font-size: 10px; color: #50575e; }
        .v-status { padding: 3px 8px; border-radius: 3px; font-size: 11px; font-weight: bold; }
        .v-status.approved { background: #dcfce7; color: #166534; }
        .v-status.pending { background: #fef9c3; color: #854d0e; }
        #adminVideoTable td { vertical-align: middle; }
    </style>

    <script type="text/javascript">
    jQuery(document).ready(function($) {
        if ($.fn.DataTable) {
            $('#adminVideoTable').DataTable({
                "pageLength": 10,
                "order": [[4, "desc"]], // Status pending di atas
                "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json" }
            });
        }
    });
    </script>
    <?php
}



add_shortcode('list_personel_publik', 'render_list_personel_publik');

function render_list_personel_publik() {
    global $wpdb;
    
    // 1. Ambil Parameter Filter & Search
    $search = isset($_GET['p_search']) ? sanitize_text_field($_GET['p_search']) : '';
    $filter_posisi = isset($_GET['p_posisi']) ? sanitize_text_field($_GET['p_posisi']) : '';
    $filter_price = isset($_GET['p_price']) ? sanitize_text_field($_GET['p_price']) : '';
    $filter_provinsi = isset($_GET['p_provinsi']) ? sanitize_text_field($_GET['p_provinsi']) : '';
    $filter_kota = isset($_GET['p_kota']) ? sanitize_text_field($_GET['p_kota']) : '';

    // 2. Build Query
   $query = "SELECT p.*, 
          (SELECT COUNT(*) FROM wp9y_portofolio WHERE personel_id = p.id AND status = 'approved') as total_foto,
          (SELECT COUNT(*) FROM wp9y_portofolio_video WHERE personel_id = p.id AND status = 'approved') as total_video
          FROM wp9y_personel p 
          WHERE p.status = 'approved'";

if ( $search ) {
    // 1. Amankan input pencarian khusus untuk query LIKE
    $like_search = '%' . $wpdb->esc_like( $search ) . '%';
    
    // 2. Masukkan variabel $like_search sebanyak 5 kali, sesuai jumlah %s
    $query .= $wpdb->prepare(
        " AND (p.nama_panggilan LIKE %s OR p.domisili LIKE %s OR p.peralatan LIKE %s OR p.deskripsi LIKE %s OR p.tag LIKE %s OR p.pricelist LIKE %s OR p.kode_nama LIKE %s)", 
        $like_search, 
        $like_search, 
        $like_search, 
        $like_search, 
        $like_search,
		$like_search,
		$like_search
    );
}
if ($filter_posisi) {
    $query .= $wpdb->prepare(" AND p.posisi LIKE %s", '%'.$filter_posisi.'%');
}
if ($filter_price) {
    $query .= $wpdb->prepare(" AND p.pricelist_perhari = %s", $filter_price);
}
if ($filter_provinsi) {
    $like_prov = '%' . $wpdb->esc_like($filter_provinsi) . '%';
    $query .= $wpdb->prepare(" AND p.domisili LIKE %s", $like_prov);
}
if ($filter_kota) {
    $like_kota = '%' . $wpdb->esc_like($filter_kota) . '%';
    $query .= $wpdb->prepare(" AND p.domisili LIKE %s", $like_kota);
}

// TAMBAHKAN BARIS INI UNTUK SORTING PRIORITAS
$query .= " ORDER BY CASE WHEN p.rekomendasi = 'ya' THEN 0 ELSE 1 END ASC, p.id DESC";

    $results = $wpdb->get_results($query);

    ob_start();
    ?>
    <div class="public-personel-container">
        <form method="get" class="personel-filter-form">
            <div class="filter-grid">
                <input type="text" name="p_search" value="<?php echo esc_attr($search); ?>" placeholder="Cari...">
                
                <select name="p_posisi">
					<option value="">Semua Posisi</option>
					<option value="F" <?php selected($filter_posisi, 'F'); ?>>Fotografer</option>
					<option value="V" <?php selected($filter_posisi, 'V'); ?>>Videografer</option>
					<option value="D" <?php selected($filter_posisi, 'D'); ?>>Drone</option>
					<option value="E" <?php selected($filter_posisi, 'E'); ?>>Editor</option>
					<option value="X" <?php selected($filter_posisi, 'X'); ?>>VFX</option>
					<option value="A" <?php selected($filter_posisi, 'A'); ?>>Animator</option>
					<option value="P" <?php selected($filter_posisi, 'P'); ?>>AI Artist - Prompt Engineer</option>
				</select>

                <select name="p_price">
                    <option value="">Semua Range Harga</option>
                    <option value="dibawah_1jt" <?php selected($filter_price, 'dibawah_1jt'); ?>>💰 Dibawah 1 jt</option>
                    <option value="1jt_3jt" <?php selected($filter_price, '1jt_3jt'); ?>>💎 1 jt - 3 jt</option>
                    <option value="diatas_3jt" <?php selected($filter_price, 'diatas_3jt'); ?>>⭐ Diatas 3 jt</option>
                </select>

                <select name="p_provinsi" id="filter_provinsi">
                    <option value="">Semua Provinsi</option>
                </select>

                <select name="p_kota" id="filter_kota">
                    <option value="">Semua Kota/Kabupaten</option>
                </select>

                <button type="submit" class="btn-filter-gold">CARI PERSONEL</button>
                <a href="<?php echo esc_url(home_url($GLOBALS['wp']->request)); ?>" class="btn-clear-filter">✖ RESET</a>
            </div>
        </form>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
jQuery(document).ready(function($) {
    var filterProvinsi = '<?php echo $filter_provinsi; ?>';
    var filterKota = '<?php echo $filter_kota; ?>';

    $('#filter_provinsi').select2({
        placeholder: "Semua Provinsi",
        width: '100%'
    });
    $('#filter_kota').select2({
        placeholder: "Semua Kota/Kabupaten",
        width: '100%'
    });

    var ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';

    $.getJSON(ajaxUrl, { action: 'fetch_wilayah', type: 'provinces' })
        .done(function(provinces) {
            let options = '<option value="">Semua Provinsi</option>';
            provinces.forEach(prov => {
                var selected = (prov.name === filterProvinsi) ? 'selected' : '';
                options += `<option value="${prov.name}" data-id="${prov.id}" ${selected}>${prov.name}</option>`;
            });
            $('#filter_provinsi').html(options).trigger('change');
            if (filterProvinsi) {
                var provId = $('#filter_provinsi').find(':selected').data('id');
                if (provId) loadKotaFilter(provId);
            }
        });

    function loadKotaFilter(provId) {
        $.getJSON(ajaxUrl, { action: 'fetch_wilayah', type: 'regencies', prov_id: provId })
            .done(function(regencies) {
                let options = '<option value="">Semua Kota/Kabupaten</option>';
                regencies.forEach(reg => {
                    var selected = (reg.name === filterKota) ? 'selected' : '';
                    options += `<option value="${reg.name}" ${selected}>${reg.name}</option>`;
                });
                $('#filter_kota').html(options).trigger('change');
            });
    }

    $('#filter_provinsi').on('change', function() {
        var provId = $(this).find(':selected').data('id');
        if (provId) {
            loadKotaFilter(provId);
        } else {
            $('#filter_kota').html('<option value="">Semua Kota/Kabupaten</option>').trigger('change');
        }
    });
});
</script>

        <div class="personel-public-grid">
            <?php if ($results): foreach ($results as $p): 
                $total_karya = $p->total_foto + $p->total_video;
                $foto_profil = !empty($p->foto_profil) ? $p->foto_profil : 'https://placehold.co/300x300?text=No+Photo';
                
                // Mapping kode ke kata lengkap
                $posisi_map = [
					'F' => 'Fotografer',
					'V' => 'Videografer',
					'D' => 'Drone',
					'E' => 'Editor',
					'X' => 'VFX',
					'A' => 'Animator',
					'P' => 'AI Artist - Prompt Engineer'
				];
            ?>
                <?php $detail_url = home_url('/detail-personel/?kode=' . $p->kode_nama); ?>

<div class="personel-card-public">
    <a href="<?php echo $detail_url; ?>" class="card-main-link">
        
        <div class="card-image">
            <img src="<?php echo esc_url($foto_profil); ?>" alt="<?php echo esc_attr($p->nama_panggilan); ?>">
            <div class="card-price-tag">
                <?php 
                    if($p->pricelist_perhari == 'dibawah_1jt') echo '< 1Jt';
                    elseif($p->pricelist_perhari == '1jt_3jt') echo '1-3Jt';
                    else echo '> 3Jt';
                ?>
            </div>
        </div>

        <div class="card-body">
			<?php 
				// Mengambil kata pertama saja
				$nama_depan = strtok($p->nama_panggilan, ' '); 
			?>
            <h3 class="p-name"><?php echo esc_html($nama_depan); ?>-<?php echo esc_html($p->kode_nama); ?></h3>
            
            <div class="p-tags">
                <?php 
                if (!empty($p->posisi)) {
                    $posisi_user = explode(',', $p->posisi);
                    foreach($posisi_user as $code) {
                        $code = trim($code);
                        if(isset($posisi_map[$code])) {
                            echo '<span class="mini-tag">'. $posisi_map[$code] .'</span>';
                        }
                    }
                }
                ?>
            </div>

            <div class="p-info">
                <span>📍 <?php echo esc_html($p->domisili); ?></span>
                <span>🎂 <?php 
				if (!empty($p->tanggal_lahir) && $p->tanggal_lahir !== '0000-00-00') {
					$bday = new DateTime($p->tanggal_lahir);
					$today = new DateTime();
					echo esc_html($today->diff($bday)->y);
				} else {
					echo '-';
				}
			?> Thn</span>
            </div>
            
            <div class="p-stats">
                <span><b><?php echo $total_karya; ?></b> Karya Portofolio</span>
            </div>

            <span class="btn-view-profile">LIHAT PROFIL</span>
        </div>
    </a>
</div>
            <?php endforeach; else: ?>
                <p class="no-result">Tidak ditemukan personel yang sesuai kriteria.</p>
            <?php endif; ?>
        </div>
    </div>
<style>
        :root { --gold: #d4af37; --dark: #111; --light: #fff; --gray: #222; }
        
        .public-personel-container { max-width: 1200px; margin: 0 auto; padding: 20px; font-family: 'Inter', sans-serif; }
        
        /* Filter Styles */
        .personel-filter-form { background: var(--gray); padding: 20px; border-radius: 15px; margin-bottom: 40px; border: 1px solid #333; }
        .filter-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr 1fr; gap: 10px; }
        .filter-grid input, .filter-grid select { background: #000; border: 1px solid #444; color: #fff; padding: 12px; border-radius: 8px; outline: none; }
        .filter-grid input:focus { border-color: var(--gold); }
        .select2-container--default .select2-selection--single { background-color: #000 !important; border: 1px solid #444 !important; border-radius: 8px !important; height: 46px !important; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { color: #fff !important; line-height: 46px !important; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 46px !important; }
        .select2-container--default .select2-results__option { background-color: #111; color: #fff; }
        .select2-container--default .select2-results__option--highlighted[aria-selected] { background-color: #d4af37 !important; color: #000 !important; }
        .select2-dropdown { background-color: #111 !important; border: 1px solid #444 !important; }
        .btn-filter-gold { background: var(--gold); color: #000; font-weight: bold; border: none; border-radius: 8px; cursor: pointer; transition: 0.3s; }
        .btn-filter-gold:hover { background: #fff; box-shadow: 0 0 15px rgba(212, 175, 55, 0.4); }
        .btn-clear-filter { background: transparent; color: #888; border: 1px solid #444; border-radius: 8px; padding: 12px; text-decoration: none; font-weight: bold; font-size: 13px; text-align: center; cursor: pointer; transition: 0.3s; display: flex; align-items: center; justify-content: center; }
        .btn-clear-filter:hover { color: #fff; border-color: #888; background: rgba(255,255,255,0.05); }

        /* Grid Styles */
        .personel-public-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
        
        .personel-card-public { background: var(--gray); border-radius: 20px; overflow: hidden; border: 1px solid #333; transition: 0.4s; position: relative; }
        .personel-card-public:hover { transform: translateY(-10px); border-color: var(--gold); box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        
        .card-image { position: relative; height: 280px; overflow: hidden; }
        .card-image img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .personel-card-public:hover .card-image img { transform: scale(1.1); }
        
        .card-price-tag { position: absolute; top: 15px; right: 15px; background: var(--gold); color: #000; padding: 5px 12px; border-radius: 50px; font-weight: 800; font-size: 11px; z-index: 2; }
        
        .card-body { padding: 25px; text-align: center; }
        .p-name { color: var(--light); font-size: 22px; margin: 0 0 10px; font-weight: 700; letter-spacing: 1px; }
        
        .p-tags { margin-bottom: 15px; display: flex; flex-wrap: wrap; justify-content: center; gap: 5px; }
        .mini-tag { font-size: 10px; background: rgba(212, 175, 55, 0.1); color: var(--gold); padding: 3px 8px; border-radius: 4px; border: 1px solid rgba(212, 175, 55, 0.3); font-weight: 600; }
        
        .p-info { color: #888; font-size: 13px; display: flex; justify-content: center; gap: 15px; margin-bottom: 15px; }
        .p-stats { color: #bbb; font-size: 14px; margin-bottom: 20px; padding-top: 10px; border-top: 1px solid #333; }
        .p-stats b { color: var(--gold); }
        
        .btn-view-profile { display: block; background: transparent; color: var(--gold); border: 1px solid var(--gold); padding: 12px; border-radius: 10px; text-decoration: none; font-weight: bold; font-size: 13px; transition: 0.3s; }
        .btn-view-profile:hover { background: var(--gold); color: #000; }

        .no-result { grid-column: 1/-1; text-align: center; color: #666; padding: 50px; }

        @media (max-width: 992px) { .personel-public-grid { grid-template-columns: repeat(2, 1fr); } .filter-grid { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 768px) { .filter-grid { grid-template-columns: 1fr; } }
        @media (max-width: 600px) { .personel-public-grid { grid-template-columns: 1fr; } .filter-grid { grid-template-columns: 1fr; } }
    </style>
    <style>
        /* Perbaikan CSS agar Tag Posisi yang Panjang tetap rapi */
        .p-tags { 
            margin-bottom: 15px; 
            display: flex; 
            flex-wrap: wrap; 
            justify-content: center; 
            gap: 6px; 
            min-height: 55px; /* Menjaga agar tinggi card tetap sama */
            align-items: center;
        }
        .mini-tag { 
            font-size: 10px; 
            background: rgba(212, 175, 55, 0.1); 
            color: #d4af37; 
            padding: 4px 10px; 
            border-radius: 4px; 
            border: 1px solid rgba(212, 175, 55, 0.3); 
            font-weight: 600;
            white-space: nowrap;
        }
        /* Sisanya sama dengan CSS sebelumnya */
		
    </style>
    <?php
    return ob_get_clean();
}

//detail personel

add_shortcode('detail_personel_luxury', 'render_detail_personel_shortcode');

function render_detail_personel_shortcode() {
    global $wpdb;
    
    $kode = isset($_GET['kode']) ? sanitize_text_field($_GET['kode']) : '';
    if (!$kode) return "<p style='color:white; text-align:center;'>Pilih personel untuk melihat profil.</p>";

    $p = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp9y_personel WHERE kode_nama = %s AND status = 'approved'", $kode));
    if (!$p) return "<p style='color:white; text-align:center;'>Personel tidak ditemukan.</p>";

    $fotos = $wpdb->get_results($wpdb->prepare("SELECT * FROM wp9y_portofolio WHERE personel_id = %d AND status = 'approved' ORDER BY id DESC", $p->id));
    $videos = $wpdb->get_results($wpdb->prepare("SELECT * FROM wp9y_portofolio_video WHERE personel_id = %d AND status = 'approved' ORDER BY id DESC", $p->id));

    ob_start(); 
    ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <div class="luxury-wrapper">
        <div class="luxury-container">
            <div class="luxury-main-flex">
                <div class="lx-left">
                    <div class="lx-frame">
                        <img src="<?php echo esc_url($p->foto_profil); ?>" class="lx-profile-img">
                    </div>
                    <div class="lx-price-box">
    <div class="lx-divider-text"><span>Pricelist</span></div>
    <div class="lx-price-list">
        <?php echo wpautop(wp_kses_post($p->pricelist)); ?>
    </div>
</div>

<?php if (intval($p->show_sosmed) !== 0): ?>
    <div class="lx-social-box" style="background:#0a0a0a; border:1px solid #333; border-radius:2px; margin-top:20px; padding:20px;">
      
        <div class="lx-divider-text" style="margin-top:0; margin-bottom:25px;"><span>KONTAK & SOSMED</span></div>

        
        <?php if (!empty($p->no_hp)): ?>
            <p style="font-size:13px; color:#ccc; margin-bottom:10px;">
                📞 WhatsApp: <span style="font-weight:bold; color:#fff; display:block; margin-top:2px;"><?php echo esc_html($p->no_hp); ?></span>
            </p>
        <?php endif; ?>

        <?php 
        $socials = ['facebook', 'instagram', 'tiktok', 'thread', 'youtube'];
        foreach ($socials as $s) {
            if (!empty($p->$s)) {
                echo '<p style="font-size:13px; margin-bottom:8px;">';
                echo '🔗 ' . ucfirst($s) . ': ';
                echo '<a href="' . esc_url($p->$s) . '" target="_blank" style="color:#d4af37; text-decoration:none; font-weight:bold; display:block; margin-top:2px;">Kunjungi Link</a>';
                echo '</p>';
            }
        }
        ?>
    </div>
<?php endif; ?>

                </div>

                <div class="lx-right">
					<?php 
				// Mengambil kata pertama saja
				$nama_depan = strtok($p->nama_panggilan, ' '); 
			?>
                    <h1 class="lx-name"><?php echo esc_html($nama_depan . '-' . $p->kode_nama); ?></h1>
                    <div class="lx-meta">
                       <span>📱 Usia: 
							<?php 
							if (!empty($p->tanggal_lahir) && $p->tanggal_lahir !== '0000-00-00') {
								$bday = new DateTime($p->tanggal_lahir);
								$today = new DateTime();
								echo esc_html($today->diff($bday)->y);
							} else {
								echo '-';
							}
							?> Tahun
						</span> &nbsp; | &nbsp;
                        <span>📍 Domisili: <?php echo esc_html($p->domisili); ?></span>
                        <div class="lx-pos">📷 Posisi: 
                            <?php 
                                $map = ['F'=>'Fotografer', 'V'=>'Videografer', 'D'=>'Drone', 'E'=>'Editor', 'X'=>'VFX', 'A'=>'Animator', 'P'=>'AI Artist - Prompt Engineer'];
                                foreach(explode(',', $p->posisi) as $c) { if(isset($map[trim($c)])) echo '<span class="lx-tag-item">'.$map[trim($c)].'</span> '; }
                            ?>
                        </div>
                    </div>
                    <div class="lx-bio"><?php echo nl2br(esc_html($p->deskripsi)); ?></div>
                    <div class="lx-tags">
                        <?php if(!empty($p->tag)) { foreach(explode(',', $p->tag) as $t) echo '<span class="lx-tag-item">#'.trim($t).'</span>'; } ?>
                    </div>
					<h5>Sertifikat</h5>
					
					<div class="sertifikat-grid">

<?php 
$sertifikat_data = json_decode($p->sertifikat_multiple, true);

if (!empty($sertifikat_data) && is_array($sertifikat_data)) :

    foreach ($sertifikat_data as $img) :

        $img_url = is_array($img) ? $img['url'] : $img;

        if (empty($img_url)) continue;
?>

        <div class="sertifikat-item">
            <a href="javascript:void(0);" 
               onclick="window.open('<?php echo esc_url($img_url); ?>','_blank')">

                <img src="<?php echo esc_url($img_url); ?>" alt="Sertifikat">

            </a>
        </div>

<?php 
    endforeach;

else : ?>
    <p class="no-data">Belum ada sertifikat yang diunggah.</p>
<?php endif; ?>

</div>
				
					<h5>Peralatan</h5>
					<div class="lx-bio"><?php echo nl2br(esc_html($p->peralatan)); ?></div>
                </div>
            </div>

				<?php if($videos): ?>
<div class="lx-porto-section">
    <div class="lx-divider-text"><span>🎥 Portofolio Video</span></div>
    
    <div class="lx-video-grid">
        <?php foreach($videos as $v): ?>
		<?php 
				// Mengambil kata pertama saja
				$nama_depan = strtok($p->nama_panggilan, ' '); 
			?>
            <div class="lx-video-item">
                <div class="lx-video-card porto-clickable" 
                     data-type="video" 
                     data-url="<?php echo get_video_embed_url($v->video_url); ?>"
                     data-title="<?php echo esc_attr($v->judul); ?>"
                     data-desc="<?php echo esc_attr($v->deskripsi); ?>"
                     data-author="<?php echo esc_attr($p->nama_panggilan . '-' . $p->kode_nama); ?>"
                     data-tahun="<?php echo $v->tahun; ?>"
                     data-lokasi="<?php echo $v->lokasi; ?>"
                     data-tanggal="<?php echo date('d M Y', strtotime($v->tanggal_kegiatan)); ?>">
                    
                    <div class="lx-video-thumb">
                        <?php preg_match('/(v=|be\/)([a-zA-Z0-9_-]+)/', $v->video_url, $m); ?>
                        <img src="https://img.youtube.com/vi/<?php echo $m[2] ?? ''; ?>/mqdefault.jpg" alt="<?php echo esc_attr($v->judul); ?>">
                        <div class="lx-play-icon">▶</div>
                    </div>
                    
                    <div class="lx-video-info">
                        <strong><?php echo esc_html($v->judul); ?></strong><br>
                        <small><?php echo $v->tahun; ?></small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="lx-more-btn-wrap">
        <a href="<?php echo home_url('/portofolio-video/'); ?>" class="lx-btn-outline">Lihat Semua Video Portofolio ❯</a>
    </div>
</div>
<?php endif; ?>

            <?php if($fotos): ?>
<div class="lx-porto-section">
    <div class="lx-divider-text"><span>📷 Portofolio Foto</span></div>
    
    <div class="lx-foto-grid">
        <?php foreach($fotos as $f): ?>
		<?php 
				// Mengambil kata pertama saja
				$nama_depan = strtok($p->nama_panggilan, ' '); 
			?>
            <div class="lx-foto-item">
                <div class="lx-foto-card porto-clickable"
                     data-type="image"
                     data-url="<?php echo esc_url($f->foto_url); ?>"
                     data-title="<?php echo esc_attr($f->judul); ?>"
                     data-desc="<?php echo esc_attr($f->deskripsi); ?>"
                     data-tahun="<?php echo $f->tahun; ?>"
                     data-lokasi="<?php echo $f->lokasi; ?>" 
                     data-author="<?php echo esc_attr($nama_depan . '-' . $p->kode_nama); ?>"
                     data-tanggal="<?php echo date('d M Y', strtotime($f->tanggal_kegiatan)); ?>">
                    
                    <div class="lx-foto-wrapper">
                        <img src="<?php echo esc_url($f->foto_url); ?>" alt="<?php echo esc_attr($f->judul); ?>">
                    </div>
                    <div class="lx-foto-meta-mini"><?php echo esc_html($f->judul); ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="lx-more-btn-wrap">
        <a href="<?php echo home_url('/portofolio-foto/'); ?>" class="lx-btn-outline">Lihat Semua Foto Portofolio ❯</a>
    </div>
</div>
<?php endif; ?>
        </div>
    </div>

    <div id="portoModal" class="lx-modal">
        <div class="lx-modal-content">
            <span class="lx-close">&times;</span>
            <div class="lx-modal-body">
                <div id="portoMedia" class="lx-modal-media"></div>
                <div class="lx-modal-info">
                    <h2 id="modalTitle" style="color:#d4af37; margin-bottom:5px;"></h2>
                    <div id="modalMeta" class="lx-modal-meta-row"></div>
                    <hr style="border:0; border-top:1px solid #333; margin:15px 0;">
                    <p id="modalDesc" style="color:#ccc; line-height:1.6;"></p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .luxury-wrapper { background: #000; color: #fff; padding: 40px 0; font-family: 'Inter', sans-serif; }
        .luxury-container { max-width: 1000px; margin: 0 auto; padding: 0 20px; }
        .luxury-main-flex { display: flex; gap: 40px; margin-bottom: 50px; text-align: left; }
        .lx-left { width: 35%; }
        .lx-right { width: 65%; }
        .lx-frame { background: #0a0a0a; padding: 10px; border: 1px solid #333; }
        .lx-profile-img { width: 100%; border-radius: 2px; }
        .lx-name { font-size: 42px; color: #d4af37; font-family: 'Playfair Display', serif; margin-bottom: 10px; }
        .lx-meta { color: #d4af37; font-size: 14px; margin-bottom: 20px; }
        .lx-bio { background: rgba(255,255,255,0.03); border: 1px solid #222; padding: 20px; border-radius: 4px; font-size: 14px; margin-bottom: 20px; }
        .lx-tag-item { border: 1px solid #d4af37; color: #d4af37; padding: 2px 10px; font-size: 11px; margin-right: 5px; display: inline-block; margin-bottom: 5px; }
        .lx-divider-text { border-top: 1px solid rgba(212,175,55,0.3); margin: 30px 0; position: relative; text-align: center; }
        .lx-divider-text span { position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: #000; padding: 0 15px; color: #d4af37; font-size: 13px; text-transform: uppercase; }
        
        .lx-video-card, .lx-foto-card { background: #0a0a0a; border: 1px solid #222; overflow: hidden; cursor: pointer; transition: 0.3s; position: relative;}
        .lx-video-card:hover, .lx-foto-card:hover { border-color: #d4af37; }
        .lx-video-thumb { position: relative; height: 140px; }
        .lx-video-thumb img, .lx-foto-card img { width: 100%; height: 100%; object-fit: cover; }
        .lx-foto-card { height: 180px; }
        .lx-foto-meta-mini { position: absolute; bottom: 0; background: rgba(0,0,0,0.7); width: 100%; font-size: 10px; padding: 5px; text-align: center; color: #d4af37; }
        .lx-play-icon { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; color: #d4af37; font-size: 30px; background: rgba(0,0,0,0.2); }
        .lx-more-btn-wrap { text-align: center; margin-top: 30px; }
        .lx-btn-outline { display: inline-block; border: 1px solid #d4af37; color: #d4af37; padding: 10px 25px; border-radius: 5px; text-decoration: none; font-size: 12px; font-weight: bold; transition: 0.3s; }
        .lx-btn-outline:hover { background: #d4af37; color: #000; }

        /* MODAL CSS */
        .lx-modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.9); backdrop-filter: blur(5px); }
        .lx-modal-content { position: relative; margin: 5% auto; width: 80%; max-width: 900px; background: #111; border-radius: 10px; border: 1px solid #333; overflow: hidden; animation: lxFadeIn 0.3s; }
        .lx-close { position: absolute; right: 20px; top: 15px; color: #fff; font-size: 30px; cursor: pointer; z-index: 10; }
        .lx-modal-body { display: flex; flex-direction: column; }
        .lx-modal-media { width: 100%; background: #000; min-height: 300px; display: flex; align-items: center; justify-content: center; }
        .lx-modal-media iframe, .lx-modal-media img { width: 100%; max-height: 500px; object-fit: contain; }
        .lx-modal-info { padding: 30px; }
        .lx-modal-meta-row { font-size: 13px; color: #888; }
        @keyframes lxFadeIn { from {opacity: 0; transform: scale(0.95);} to {opacity: 1; transform: scale(1);} }
        @media (max-width: 768px) { .luxury-main-flex { flex-direction: column; } .lx-left, .lx-right { width: 100%; } .lx-modal-content { width: 95%; margin: 10% auto; } }
			/* Sertifikat Grid */
					.sertifikat-grid {
						display: grid;
						grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
						gap: 15px;
						margin-top: 15px;
					}

					.sertifikat-item {
						aspect-ratio: 1 / 1;
						overflow: hidden;
						border-radius: 8px;
						border: 1px solid #333;
						background: #1a1a1a;
						cursor: pointer;
						transition: 0.3s;
					}

					.sertifikat-item img {
						width: 100%;
						height: 100%;
						object-fit: cover;
						transition: 0.5s;
					}

					.sertifikat-item:hover { border-color: #d4af37; }
					.sertifikat-item:hover img { transform: scale(1.1); }

					/* Mobile view */
					@media (max-width: 600px) {
						.sertifikat-grid { grid-template-columns: repeat(3, 1fr); gap: 10px; }
					}
		/* Container Grid */
.lx-video-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr); /* 4 kolom sama rata */
    gap: 20px; /* Jarak antar item */
    margin-bottom: 30px;
}

/* Responsif untuk layar lebih kecil */
@media (max-width: 1024px) {
    .lx-video-grid {
        grid-template-columns: repeat(3, 1fr); /* 3 kolom di tablet */
    }
}

@media (max-width: 768px) {
    .lx-video-grid {
        grid-template-columns: repeat(2, 1fr); /* 2 kolom di HP */
        gap: 15px;
    }
}

@media (max-width: 480px) {
    .lx-video-grid {
        grid-template-columns: repeat(1, 1fr); /* 1 kolom di HP kecil */
    }
}

/* Pastikan gambar thumb responsif */
.lx-video-thumb img {
    width: 100%;
    height: auto;
    display: block;
    border-radius: 8px;
}
		/* Container Grid untuk Foto */
.lx-foto-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr); /* 4 kolom */
    gap: 15px; /* Jarak antar foto */
    margin-bottom: 30px;
}

/* Styling Card Foto agar seragam */
.lx-foto-card {
    position: relative;
    cursor: pointer;
    overflow: hidden;
    border-radius: 8px;
    background: #f0f0f0;
}

.lx-foto-wrapper {
    aspect-ratio: 1 / 1; /* Membuat foto jadi kotak (square), opsional */
    overflow: hidden;
}

.lx-foto-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Agar gambar tidak gepeng */
    transition: transform 0.3s ease;
}

.lx-foto-card:hover img {
    transform: scale(1.05); /* Efek zoom saat hover */
}

/* Responsif */
@media (max-width: 1024px) {
    .lx-foto-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .lx-foto-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
    </style>

    <script>
    jQuery(document).ready(function($) {
        // Init Swiper
        const pConf = { slidesPerView: 2, spaceBetween: 15, navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" }, breakpoints: { 1024: { slidesPerView: 4 } } };
        new Swiper(".videoSwiper", pConf);
        new Swiper(".fotoSwiper", pConf);

        // Modal Logic
        const modal = $('#portoModal');
        const close = $('.lx-close');

        $('.porto-clickable').on('click', function() {
            const data = $(this).data();
            $('#modalTitle').text(data.title);
            $('#modalDesc').text(data.desc || "Tidak ada deskripsi.");
            $('#modalMeta').html(`
				👤 <a href="https://profesional-indonesia.com/detail-personel/?kode=${data.kode_nama}" target="_blank" style="text-decoration:none; color:#007bff;"><b>${data.author}</b></a> &nbsp; | &nbsp;
				📍 ${data.lokasi} &nbsp; | &nbsp; 
				🗓️ ${data.tahun} &nbsp; | &nbsp; 
				📅 ${data.tanggal}
			`);

            if(data.type === 'video') {
                $('#portoMedia').html(`<iframe src="${data.url}" frameborder="0" allowfullscreen style="width:100%; aspect-ratio:16/9;"></iframe>`);
            } else {
                $('#portoMedia').html(`<img src="${data.url}" style="width:100%;">`);
            }
            modal.show();
        });

        close.on('click', () => {
            modal.hide();
            $('#portoMedia').empty();
        });

        $(window).on('click', (e) => {
            if (e.target == modal[0]) {
                modal.hide();
                $('#portoMedia').empty();
            }
        });
    });
    </script>
    <?php
    return ob_get_clean();
}

/**
 * ============================================================
 * SISTEM GALERI FOTO PUBLIK PRO (SEARCH, FILTER, SORT, AJAX)
 * ============================================================
 */

// 1. FUNGSI RENDER ITEM FOTO
if ( ! function_exists( 'render_porto_item_html' ) ) {
    function render_porto_item_html($data, $type) {
        $thumb = $data->foto_url;
        $media_url = $data->foto_url;
		
				// Mengambil kata pertama saja
				$nama_depan = strtok($data->nama_panggilan, ' '); 
			
        ob_start(); ?>
        <div class="lx-item porto-clickable" 
             data-type="image" 
             data-url="<?php echo esc_url($media_url); ?>"
             data-title="<?php echo esc_attr($data->judul); ?>"
             data-desc="<?php echo esc_attr($data->deskripsi); ?>"
             data-tags="<?php echo esc_attr($data->tags); ?>"
             data-tahun="<?php echo $data->tahun; ?>"
			 data-lokasi="<?php echo $data->lokasi; ?>"
			 data-kodenama="<?php echo $data->kode_nama; ?>"
             data-author="<?php echo esc_attr($nama_depan . '-' . $data->kode_nama); ?>"
             data-tanggal="<?php echo date('d M Y', strtotime($data->tanggal_kegiatan)); ?>">
            <div class="lx-thumb">
                <img src="<?php echo esc_url($thumb); ?>" loading="lazy">
            </div>
            <div class="lx-info">
                <strong><?php echo esc_html($data->judul); ?></strong>
                <div class="lx-meta-bottom">
                    <small>by <a href="<?php echo esc_url('https://profesional-indonesia.com/detail-personel/?kode=' . urlencode($data->kode_nama)); ?>">
    <?php echo esc_html($nama_depan . '-' . $data->kode_nama); ?>
</a></small>
                    <small><?php echo date('d/m/Y', strtotime($data->tanggal_kegiatan)); ?></small>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

// 2. SHORTCODE GALERI FOTO [arsip_foto_publik]
add_shortcode('arsip_foto_publik', 'shortcode_arsip_foto');
function shortcode_arsip_foto() {
    global $wpdb;
    ob_start(); ?>
    <div class="lx-archive">
        <?php render_portfolio_category_filter_bar('foto'); ?>
        <div class="lx-filter-header">
            <input type="text" id="search-foto" class="lx-input-flex" placeholder="Cari nama, lokasi, atau tags...">
            <button id="btn-filter-foto" class="lx-btn-gold">CARI</button>
        </div>

        <div class="lx-sort-wrapper">
            <select id="sort-foto" class="lx-input-sort">
                <option value="newest_post">Postingan Terbaru</option>
                <option value="oldest_post">Postingan Terlama</option>
                <option value="newest_event">Tanggal Kegiatan (Baru-Lama)</option>
                <option value="oldest_event">Tanggal Kegiatan (Lama-Baru)</option>
            </select>
        </div>

        <div class="lx-grid" id="foto-container">
            <?php 
            $res = $wpdb->get_results("SELECT f.*, p.nama_panggilan, p.kode_nama FROM wp9y_portofolio f JOIN wp9y_personel p ON f.personel_id = p.id WHERE f.status = 'approved' AND p.status = 'approved' ORDER BY f.id DESC LIMIT 12");
            if($res) foreach($res as $f) echo render_porto_item_html($f, 'image'); 
            ?>
        </div>
        
        <div class="lx-load-wrap">
            <button id="load-more-foto" data-offset="12" class="lx-btn-outline">MUAT LEBIH BANYAK</button>
        </div>
    </div>
    <?php return ob_get_clean();
}

// 3. AJAX HANDLER (FILTER, SEARCH & SORT)
add_action('wp_ajax_load_more_porto', 'handle_ajax_load_more');
add_action('wp_ajax_nopriv_load_more_porto', 'handle_ajax_load_more');
function handle_ajax_load_more() {
    global $wpdb;
    $type   = ($_POST['type'] == 'video') ? 'video' : 'image';
    $table  = ($type == 'video') ? 'wp9y_portofolio_video' : 'wp9y_portofolio';
    $offset = intval($_POST['offset']);
    $search = sanitize_text_field($_POST['search']);
    $tgl    = sanitize_text_field($_POST['tanggal']);
    $tahun  = sanitize_text_field($_POST['tahun']);
    $sort   = sanitize_text_field($_POST['sort']);

    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';

    $join = "";
    $where_cat = "";
    if (!empty($category) && $category !== 'semua') {
        if ($type === 'video') {
            $join = " JOIN wp9y_portofolio_video_kategori_map m ON t.id = m.video_id 
                      JOIN wp9y_kategori k ON m.kategori_id = k.id ";
        } else {
            $join = " JOIN wp9y_portofolio_kategori_map m ON t.id = m.portofolio_id 
                      JOIN wp9y_kategori k ON m.kategori_id = k.id ";
        }
        $where_cat = $wpdb->prepare(" AND k.slug = %s ", $category);
    }

    $query = "SELECT t.*, p.nama_panggilan, p.kode_nama FROM $table t 
              JOIN wp9y_personel p ON t.personel_id = p.id 
              $join
              WHERE t.status = 'approved' AND p.status = 'approved' $where_cat";

    if(!empty($search)) {
        $query .= $wpdb->prepare(" AND (t.judul LIKE %s OR t.lokasi LIKE %s OR p.nama_panggilan LIKE %s OR t.tags LIKE %s)", '%'.$search.'%', '%'.$search.'%', '%'.$search.'%', '%'.$search.'%');
    }
    // Sorting Logic
    switch ($sort) {
        case 'oldest_post': $query .= " ORDER BY t.id ASC"; break;
        case 'newest_event': $query .= " ORDER BY t.tanggal_kegiatan DESC"; break;
        case 'oldest_event': $query .= " ORDER BY t.tanggal_kegiatan ASC"; break;
        default: $query .= " ORDER BY t.id DESC"; break;
    }

    $query .= " LIMIT $offset, 12";
    $res = $wpdb->get_results($query);
    
    if($res) {
        foreach($res as $r) echo render_porto_item_html($r, $type);
    } else if($offset == 0) {
        echo "<p class='lx-no-data'>Tidak ada data pada kategori ini.</p>";
    }
    wp_die();
}

// 4. ASSETS (CSS & JS)
add_action('wp_footer', 'lx_porto_foto_assets');
function lx_porto_foto_assets() { ?>
    <div id="lx-modal" class="lx-m">
        <div class="lx-m-content">
            <span class="lx-close">&times;</span>
            <div id="lx-m-media"></div>
            <div class="lx-m-info">
                <h3 id="lx-m-title" style="color:#d4af37;margin:0"></h3>
                <div id="lx-m-meta" style="font-size:12px;color:#888;margin:10px 0"></div>
                <div id="lx-m-tags" style="margin-bottom:15px"></div>
                <p id="lx-m-desc" style="font-size:14px;color:#ccc;line-height:1.6;border-top:1px solid #222;padding-top:15px"></p>
            </div>
        </div>
    </div>

    <style>
        .lx-archive { max-width: 1200px; margin: 0 auto; padding: 20px; background: #000; color: #fff; }
        
        /* Filter Header Inline */
        .lx-filter-header { display: flex; gap: 10px; margin-bottom: 15px; flex-wrap: wrap; align-items: center; }
        .lx-input-flex { background: #111; border: 1px solid #333; color: #fff; padding: 12px; border-radius: 5px; flex: 1; min-width: 150px; outline: none; transition: 0.3s; }
        .lx-input-flex:focus { border-color: #d4af37; }
        .lx-btn-gold { background: #d4af37; color: #000; border: none; padding: 12px 30px; border-radius: 5px; font-weight: bold; cursor: pointer; }
        
        /* Sort Styling */
        .lx-sort-wrapper { display: flex; justify-content: flex-end; margin-bottom: 20px; }
        .lx-input-sort { background: #1a1a1a; border: 1px solid #444; color: #d4af37; padding: 8px; border-radius: 4px; font-size: 12px; outline: none; width:150px }

        /* Grid */
        .lx-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; clear: both; }
        .lx-item { background: #111; border: 1px solid #222; border-radius: 8px; overflow: hidden; cursor: pointer; transition: 0.3s; }
        .lx-item:hover { border-color: #d4af37; transform: translateY(-5px); }
        .lx-thumb { height: 180px; overflow: hidden; }
        .lx-thumb img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .lx-item:hover .lx-thumb img { transform: scale(1.1); }
        
        .lx-info { padding: 12px; }
        .lx-info strong { display: block; color: #d4af37; font-size: 14px; margin-bottom: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .lx-meta-bottom { display: flex; justify-content: space-between; align-items: center; }
        .lx-meta-bottom small { font-size: 10px; color: #777; }

        .lx-load-wrap { text-align: center; margin-top: 40px; }
        .lx-btn-outline { background: transparent; border: 1px solid #d4af37; color: #d4af37; padding: 12px 35px; border-radius: 5px; cursor: pointer; font-weight: bold; }
        
        .lx-no-data { grid-column: 1/-1; text-align: center; padding: 50px; color: #555; }

        /* Tag Styling di Modal */
        .m-tag { display: inline-block; background: rgba(212,175,55,0.1); color: #d4af37; padding: 2px 8px; border-radius: 3px; font-size: 10px; margin-right: 5px; border: 1px solid rgba(212,175,55,0.3); }

        /* Modal */
        .lx-m { display: none; position: fixed; z-index: 99999; inset: 0; background: rgba(0,0,0,0.9); backdrop-filter: blur(5px); padding: 20px; }
        .lx-m-content { max-width: 700px; margin: 20px auto; background: #111; border-radius: 10px; position: relative; overflow: hidden; border: 1px solid #333; }
        .lx-close { position: absolute; right: 15px; top: 10px; font-size: 35px; color: #fff; cursor: pointer; z-index: 100; }
        #lx-m-media img { width: 100%; max-height: 450px; object-fit: contain; display: block; background: #000; }
        .lx-m-info { padding: 25px; }

        @media (max-width: 900px) { .lx-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 600px) { .lx-grid { grid-template-columns: 1fr; } .lx-filter-header { flex-direction: column; } .lx-input-flex { width: 100%; } }
    </style>

   <script>
jQuery(document).ready(function($) {

    function getPorto(isNew = false) {

        let container = $('#foto-container');
        let btn = $('#load-more-foto');

        // AMBIL OFFSET TERBARU
        let offset = isNew
    ? 0
    : parseInt(btn.attr('data-offset') || 12);

        if(isNew) {
            container.css('opacity', '0.5');
        }

        btn.text('LOADING...').prop('disabled', true);

        $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
            action: 'load_more_porto',
            type: 'image',
            offset: offset,
            search: $('#search-foto').val(),
            sort: $('#sort-foto').val(),
            category: $('#active-category-slug').val()

        }, function(res) {

            res = res.trim();

            if(isNew) {

                container.html(res).css('opacity', '1');

                // RESET OFFSET
                btn.attr('data-offset', 12);

                if(res !== '') {
                    btn
                        .show()
                        .text('MUAT LEBIH BANYAK')
                        .prop('disabled', false);
                } else {
                    btn.hide();
                }

            } else {

                if(res !== '') {

                    container.append(res);

                    // UPDATE OFFSET
                    btn.attr('data-offset', offset + 12);

                    btn
                        .text('MUAT LEBIH BANYAK')
                        .prop('disabled', false);

                } else {

                    btn
                        .text('SEMUA TELAH DIMUAT')
                        .prop('disabled', true);

                }
			}
        });
    }
    window.getPorto = getPorto;

    $('#btn-filter-foto, #sort-foto').on('change click', function() {
        getPorto(true);
    });

    $(document).off('keypress', '#search-foto').on('keypress', '#search-foto', function(e) {
        if(e.which == 13) {
            e.preventDefault();
            getPorto(true);
        }
    });

   $(document)
.off('click', '#load-more-foto')
.on('click', '#load-more-foto', function(e) {

    e.preventDefault();

    getPorto(false);
});

    // Modal
    $(document).on('click', '.porto-clickable', function() {

        let d = $(this).data();

        $('#lx-m-title').text(d.title);

        $('#lx-m-meta').html(
            '👤 <b>' + d.author + '</b> &nbsp;|&nbsp; 🗓️ ' + d.tahun + ' &nbsp;|&nbsp; 📅 ' + d.tanggal
        );

        let tagsHtml = '';

        if(d.tags) {
            d.tags.split(',').forEach(t => {
                if(t.trim()) {
                    tagsHtml += '<span class="m-tag">#' + t.trim() + '</span>';
                }
            });
        }

        $('#lx-m-tags').html(tagsHtml);

        $('#lx-m-desc').text(d.desc || "Tidak ada deskripsi.");

        $('#lx-m-media').html('<img src="' + d.url + '">');

        $('#lx-modal').fadeIn(200);
    });

    $('.lx-close, #lx-modal').on('click', function(e) {

        if(e.target == this || $(e.target).hasClass('lx-close')) {
            $('#lx-modal').fadeOut(200);
        }

    });

});
</script>
<?php }
/**
 * ============================================================
 * SISTEM GALERI VIDEO PUBLIK - FIXED CRITICAL ERROR
 * ============================================================
 */

// 1. FUNGSI RENDER ITEM VIDEO (ARSIP)
if ( ! function_exists( 'render_video_item_html' ) ) {
    function render_video_item_html($data) {
        preg_match('/(v=|be\/)([a-zA-Z0-9_-]+)/', $data->video_url, $m);
        $yt_id = $m[2] ?? '';
        $thumb = "https://img.youtube.com/vi/{$yt_id}/mqdefault.jpg";
        $media_url = "https://www.youtube.com/embed/{$yt_id}?modestbranding=1&rel=0";
		$nama_depan = strtok($data->nama_panggilan, ' '); 
        ob_start(); ?>
        <div class="lx-item porto-clickable" 
             data-type="video" 
             data-url="<?php echo esc_url($media_url); ?>"
             data-title="<?php echo esc_attr($data->judul); ?>"
             data-desc="<?php echo esc_attr($data->deskripsi); ?>"
             data-tags="<?php echo esc_attr($data->tags); ?>"
             data-tahun="<?php echo $data->tahun; ?>"
			 data-lokasi="<?php echo $data->lokasi; ?>"
			 data-kodenama="<?php echo $data->kode_nama; ?>"
             data-author="<?php echo esc_attr($nama_depan . '-' . $data->kode_nama); ?>"
             data-tanggal="<?php echo date('d M Y', strtotime($data->tanggal_kegiatan)); ?>">
            <div class="lx-thumb video-thumb">
                <img src="<?php echo esc_url($thumb); ?>" loading="lazy">
                <div class="lx-play-btn">▶</div>
            </div>
            <div class="lx-info">
                <strong><?php echo esc_html($data->judul); ?></strong>
                <div class="lx-meta-bottom">
                    <small>by <a href="<?php echo esc_url('https://profesional-indonesia.com/detail-personel/?kode=' . urlencode($data->kode_nama)); ?>">
    <?php echo esc_html($data->nama_panggilan . '-' . $data->kode_nama); ?>
</a></small>
                    <small><?php echo date('d/m/Y', strtotime($data->tanggal_kegiatan)); ?></small>
					<small><?php echo $data->lokasi; ?></small>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

// 2. SHORTCODE VIDEO [arsip_video_publik]
add_shortcode('arsip_video_publik', 'shortcode_arsip_video_fixed');
function shortcode_arsip_video_fixed() {
    global $wpdb;
    ob_start(); ?>
    <div class="lx-archive">
        <?php render_portfolio_category_filter_bar('video'); ?>
        <div class="lx-filter-header">
            <input type="text" id="search-video" class="lx-input-flex" placeholder="Cari nama, lokasi, atau tags...">
            <button id="btn-filter-video" class="lx-btn-gold">CARI VIDEO</button>
        </div>
        <div class="lx-sort-wrapper">
            <select id="sort-video" class="lx-input-sort">
                <option value="newest_post">Postingan Terbaru</option>
                <option value="oldest_post">Postingan Terlama</option>
                <option value="newest_event">Tanggal (Baru-Lama)</option>
                <option value="oldest_event">Tanggal (Lama-Baru)</option>
            </select>
        </div>
        <div class="lx-grid" id="video-container">
            <?php 
            $res = $wpdb->get_results("SELECT v.*, p.nama_panggilan, p.kode_nama FROM wp9y_portofolio_video v JOIN wp9y_personel p ON v.personel_id = p.id WHERE v.status = 'approved' AND p.status = 'approved' ORDER BY v.id DESC LIMIT 12");
            if($res) foreach($res as $v) echo render_video_item_html($v); 
            ?>
        </div>
        <div class="lx-load-wrap"><button id="load-more-video" data-offset="12" class="lx-btn-outline">MUAT LEBIH BANYAK</button></div>
    </div>
    <?php return ob_get_clean();
}

// 3. AJAX HANDLER VIDEO (FIXED COLUMN & QUERY)
add_action('wp_ajax_load_more_porto_video', 'ajax_video_handler_fixed');
add_action('wp_ajax_nopriv_load_more_porto_video', 'ajax_video_handler_fixed');

function ajax_video_handler_fixed() {
    global $wpdb;
    
    // Ambil data dari POST
    $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    $tgl    = isset($_POST['tanggal']) ? sanitize_text_field($_POST['tanggal']) : '';
    $tahun  = isset($_POST['tahun']) ? sanitize_text_field($_POST['tahun']) : '';
    $sort   = isset($_POST['sort']) ? sanitize_text_field($_POST['sort']) : '';

    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';

    $join = "";
    $where_cat = "";
    if (!empty($category) && $category !== 'semua') {
        $join = " JOIN wp9y_portofolio_video_kategori_map m ON v.id = m.video_id 
                  JOIN wp9y_kategori k ON m.kategori_id = k.id ";
        $where_cat = $wpdb->prepare(" AND k.slug = %s ", $category);
    }

    // PERBAIKAN: Tambahkan p.kode_nama di SELECT agar render_video_item_html tidak error
    $query = "SELECT v.*, p.nama_panggilan, p.kode_nama FROM wp9y_portofolio_video v 
              JOIN wp9y_personel p ON v.personel_id = p.id 
              $join
              WHERE v.status = 'approved' AND p.status = 'approved' $where_cat";

    // Filter Pencarian
    if(!empty($search)) {
        // PERBAIKAN: Search juga mencakup judul agar pencarian lebih akurat
        $query .= $wpdb->prepare(" AND (v.judul LIKE %s OR v.lokasi LIKE %s OR p.nama_panggilan LIKE %s OR v.tags LIKE %s OR p.kode_nama LIKE %s)", '%'.$search.'%', '%'.$search.'%', '%'.$search.'%', '%'.$search.'%', '%'.$search.'%');
    }
    
    // Sorting
    switch ($sort) {
        case 'oldest_post': $query .= " ORDER BY v.id ASC"; break;
        case 'newest_event': $query .= " ORDER BY v.tanggal_kegiatan DESC"; break;
        case 'oldest_event': $query .= " ORDER BY v.tanggal_kegiatan ASC"; break;
        default: $query .= " ORDER BY v.id DESC"; break;
    }

    $query .= " LIMIT $offset, 12";
    
    $res = $wpdb->get_results($query);

    if($res) {
        foreach($res as $r) {
            echo render_video_item_html($r);
        }
    } else if($offset == 0) {
        echo '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #888;">Tidak ada data pada kategori ini.</div>';
    }
    
    wp_die();
}

/**
 * UNIVERSAL MODAL ASSETS (FOTO & VIDEO)
 * Menangani Klik di Halaman Profil Personel & Halaman Arsip
 */
add_action('wp_footer', 'lx_universal_porto_assets');
function lx_universal_porto_assets() { ?>
    <div id="lx-modal" class="lx-m">
        <div class="lx-m-content">
            <span class="lx-close">&times;</span>
            <div id="lx-m-media"></div>
            <div class="lx-m-info">
                <h3 id="lx-m-title" style="color:#d4af37;margin:0;font-family:'Playfair Display',serif;"></h3>
                <div id="lx-m-meta" style="font-size:12px;color:#888;margin:10px 0"></div>
                <div id="lx-m-tags" style="margin-bottom:15px"></div>
                <p id="lx-m-desc" style="font-size:14px;color:#ccc;line-height:1.6;border-top:1px solid #222;padding-top:15px"></p>
            </div>
        </div>
    </div>

    <style>
        .lx-m { display: none; position: fixed; z-index: 99999; inset: 0; background: rgba(0,0,0,0.95); backdrop-filter: blur(8px); padding: 20px; }
        .lx-m-content { max-width: 850px; margin: 40px auto; background: #111; border-radius: 10px; position: relative; overflow: hidden; border: 1px solid #333; box-shadow: 0 0 50px rgba(0,0,0,0.8); }
        .lx-close { position: absolute; right: 20px; top: 15px; font-size: 35px; color: #fff; cursor: pointer; z-index: 100; transition: 0.3s; }
        .lx-close:hover { color: #d4af37; transform: rotate(90deg); }
        #lx-m-media { background: #000; min-height: 200px; }
        #lx-m-media img { width: 100%; max-height: 80vh; object-fit: contain; display: block; }
        #lx-m-media iframe { width: 100%; aspect-ratio: 16/9; display: block; border: none; }
        .lx-m-info { padding: 30px; text-align: left; }
        .m-tag { display: inline-block; background: rgba(212,175,55,0.1); color: #d4af37; padding: 2px 8px; border-radius: 3px; font-size: 10px; margin-right: 5px; border: 1px solid rgba(212,175,55,0.3); }
        @media (max-width: 768px) { .lx-m-content { margin: 10% auto; width: 95%; } }
    </style>

    <script type="text/javascript">
    jQuery(document).ready(function($) {
        
        // FUNGSI UNTUK MEMBUKA MODAL (FOTO ATAU VIDEO)
        // Kita gunakan $(document).on agar elemen yang di-load via AJAX tetap bisa diklik
        $(document).on('click', '.porto-clickable, .lx-item', function(e) {
            e.preventDefault();
            let d = $(this).data();
            
            // 1. Reset & Isi Konten Teks
            $('#lx-m-title').text(d.title || "Untitled");
            $('#lx-m-desc').text(d.desc || "Tidak ada deskripsi.");
            
            let metaHtml = '';

if(d.author && d.kodenama) {
    metaHtml += '👤 <a href="https://profesional-indonesia.com/detail-personel/?kode=' 
        + encodeURIComponent(d.kodenama) + 
        '" target="_blank" onclick="event.stopPropagation();"><b>' 
        + d.author + 
        '</b></a> &nbsp;|&nbsp; ';
} else if(d.author) {
    metaHtml += '👤 <b>' + d.author + '</b> &nbsp;|&nbsp; ';
}

metaHtml += '📍 ' + (d.lokasi || '-') + ' &nbsp;|&nbsp; 🗓️ ' + (d.tahun || '-');

if(d.tanggal) {
    metaHtml += ' &nbsp;|&nbsp; 📅 ' + d.tanggal;
}

$('#lx-m-meta').html(metaHtml);

            // 2. Render Tags
            let tagsHtml = '';
            if(d.tags) {
                let tagsArr = String(d.tags).split(',');
                tagsArr.forEach(function(t) {
                    if(t.trim()) tagsHtml += '<span class="m-tag">#' + t.trim() + '</span>';
                });
            }
            $('#lx-m-tags').html(tagsHtml);

            // 3. LOGIKA MEDIA (CEK TYPE VIDEO ATAU IMAGE)
            if(d.type === 'video') {
                // RENDER IFRAME UNTUK VIDEO
                $('#lx-m-media').html('<iframe src="' + d.url + '" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>');
            } else {
                // RENDER IMG UNTUK FOTO
                $('#lx-m-media').html('<img src="' + d.url + '" loading="lazy">');
            }

            // 4. Tampilkan Modal
            $('#lx-modal').fadeIn(300);
        });

        // FUNGSI TUTUP MODAL
        $(document).on('click', '.lx-close, #lx-modal', function(e) {
            if(e.target == this || $(e.target).hasClass('lx-close')) {
                $('#lx-modal').fadeOut(200, function() {
                    $('#lx-m-media').empty(); // STOP VIDEO SAAT TUTUP
                });
            }
        });

        // LOGIKA AJAX LOAD MORE (Tetap di sini agar rapi)
        $('#load-more-foto, #load-more-video').on('click', function() {
            let b = $(this), 
                t = b.attr('id').includes('video') ? 'video' : 'image', 
                c = (t === 'video') ? $('#video-container') : $('#foto-container'), 
                o = b.data('offset'),
                s = (t === 'video') ? $('#search-video').val() : $('#search-foto').val(),
                th = (t === 'video') ? $('#filter-tahun-video').val() : $('#filter-tahun-foto').val(),
                so = (t === 'video') ? $('#sort-video').val() : $('#sort-foto').val();

            b.text('LOADING...').prop('disabled', true);
            
            $.post('<?php echo admin_url('admin-ajax.php'); ?>', { 
                action: (t === 'video' ? 'load_more_porto_video' : 'load_more_porto'), 
                offset: o, 
                type: t,
                search: s,
                tahun: th,
                sort: so
            }, function(res) {
                if(res.trim() !== "") { 
                    c.append(res); 
                    b.data('offset', o + 12).text('MUAT LEBIH BANYAK').prop('disabled', false); 
                } else { 
                    b.text('SEMUA TELAH DIMUAT'); 
                }
            });
        });

        // Filter Trigger
        $('#btn-filter-foto, #btn-filter-video, #sort-foto, #sort-video').on('click change', function() {
            // Logika refresh grid bisa dipanggil di sini jika diperlukan
        });
    });
    </script>
<?php }

add_action('wp_footer', function() {
?>
<script type="text/javascript">
jQuery(document).ready(function($) {

    // Fungsi Load Video
    function jalankanCariVideo(isLoadMore = false) {

        var container = $('#video-container');
        var btnCari   = $('#btn-filter-video');
        var btnLoad   = $('#load-more-video');

        // OFFSET
        var offset = isLoadMore
            ? parseInt(btnLoad.attr('data-offset') || 12)
            : 0;

        if(!isLoadMore) {
            container.css('opacity', '0.5');
            btnCari.text('⏳...');
        }

        btnLoad.prop('disabled', true);

        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',

            data: {
                action: 'load_more_porto_video',
                search: $('#search-video').val(),
                sort: $('#sort-video').val(),
                offset: offset,
                category: $('#active-category-slug-video').val()
            },

            success: function(res) {

                res = res.trim();

                if(!isLoadMore) {

                    container.html(res);

                    // RESET OFFSET
                    btnLoad.attr('data-offset', 12);

                    if(res !== '') {

                        btnLoad
                            .show()
                            .text('MUAT LEBIH BANYAK')
                            .prop('disabled', false);

                    } else {

                        btnLoad.hide();

                    }

                } else {

                    if(res !== '') {

                        container.append(res);

                        // UPDATE OFFSET
                        btnLoad.attr('data-offset', offset + 12);

                        btnLoad
                            .text('MUAT LEBIH BANYAK')
                            .prop('disabled', false);

                    } else {

                        btnLoad
                            .text('SEMUA TELAH DIMUAT')
                            .prop('disabled', true);

                    }
                }
            },

            complete: function() {

                container.css('opacity', '1');

                btnCari.text('CARI VIDEO');

            }
        });
    }
    window.jalankanCariVideo = jalankanCariVideo;

    // FILTER & SORT
    $(document)
    .off('click change', '#btn-filter-video, #sort-video')
    .on('click change', '#btn-filter-video, #sort-video', function(e) {

        if($(this).is('button')) {
            e.preventDefault();
        }

        jalankanCariVideo(false);
    });

    // LOAD MORE
    $(document)
    .off('click', '#load-more-video')
    .on('click', '#load-more-video', function(e) {

        e.preventDefault();

        jalankanCariVideo(true);
    });

    // ENTER SEARCH
    $(document)
    .off('keypress', '#search-video')
    .on('keypress', '#search-video', function(e) {

        if(e.which == 13) {

            e.preventDefault();

            jalankanCariVideo(false);

        }
    });

});
</script>
<?php
});
/**
 * Shortcode untuk Switch Button Portofolio (Foto / Video)
 * Penggunaan: [switch_porto_button]
 */
add_shortcode('switch_porto_button', 'render_switch_porto_button');

function render_switch_porto_button() {
    // Ambil path URL saat ini
    $current_url = $_SERVER['REQUEST_URI'];
    
    // Tentukan halaman mana yang aktif
    $is_video = (strpos($current_url, 'portofolio-video') !== false);
    
    // Link tujuan
    $link_foto = home_url('/portofolio-foto/');
    $link_video = home_url('/portofolio-video/');

    ob_start(); ?>
    <div class="lx-switch-wrapper">
        <div class="lx-switch-container">
            <a href="<?php echo $link_foto; ?>" class="lx-switch-btn <?php echo !$is_video ? 'active' : ''; ?>">
                Portofolio Foto
            </a>
            
            <a href="<?php echo $link_video; ?>" class="lx-switch-btn <?php echo $is_video ? 'active' : ''; ?>">
                Portofolio Video
            </a>
        </div>
    </div>

    <style>
        .lx-switch-wrapper {
            display: flex;
            justify-content: center;
            margin: 20px 0 40px 0;
        }
        .lx-switch-container {
            background: #222; /* Warna Hitam Background */
            border: 2px solid #333;
            border-radius: 50px;
            display: inline-flex;
            padding: 5px;
            position: relative;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
        }
        .lx-switch-btn {
            text-decoration: none !important;
            padding: 12px 30px;
            border-radius: 40px;
            font-family: 'Playfair Display', serif; /* Font mewah sesuai gambar */
            font-size: 16px;
            font-weight: bold;
            color: #d4af37; /* Warna Gold untuk teks tidak aktif */
            transition: all 0.4s ease;
            white-space: nowrap;
        }
        /* State Saat Tombol Aktif (Warna Putih Seperti Gambar) */
        .lx-switch-btn.active {
            background: #fff;
            color: #000 !important;
            box-shadow: 0 2px 10px rgba(255,255,255,0.2);
        }
        /* Hover Effect untuk yang tidak aktif */
        .lx-switch-btn:not(.active):hover {
            color: #fff;
            background: rgba(255,255,255,0.05);
        }

        @media (max-width: 600px) {
            .lx-switch-btn {
                padding: 10px 20px;
                font-size: 14px;
            }
        }
    </style>
    <?php
    return ob_get_clean();
}

/**
 * Shortcode Carousel Artikel Kategori Event
 * Badge tetap, Judul Putih Bold 20px, Tanggal di bawah
 */
add_shortcode('carousel_event_terbaru', 'render_carousel_event_terbaru');

function render_carousel_event_terbaru() {
    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => 10,
        'category_name'  => 'kebutuhan event', 
        'orderby'        => 'date',
        'order'          => 'DESC'
    );

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        return '<p style="color:#666; text-align:center;">Belum ada kebutuhan event terbaru.</p>';
    }

    ob_start(); ?>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <div class="lx-event-carousel-wrapper">
        <div class="swiper eventSwiper">
            <div class="swiper-wrapper">
                <?php while ($query->have_posts()) : $query->the_post(); 
                    $thumb_url = get_the_post_thumbnail_url(get_the_ID(), 'medium_large');
                    if (!$thumb_url) $thumb_url = 'https://placehold.co/300x300?text=No+Photo';
                ?>
                <div class="swiper-slide">
                    <a href="<?php the_permalink(); ?>" class="lx-event-card">
                        <div class="lx-event-thumb">
                            <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php the_title(); ?>">
                            <span class="lx-event-badge">KEBUTUHAN EVENT</span>
                        </div>
                        <div class="lx-event-info">
                            <h3 class="lx-event-title"><?php the_title(); ?></h3>
                            <div class="lx-event-date"><?php echo get_the_date('d M Y'); ?></div>
                        </div>
                    </a>
                </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
            <div class="swiper-button-next lx-nav"></div>
            <div class="swiper-button-prev lx-nav"></div>
        </div>
    </div>

    <style>
        .lx-event-carousel-wrapper { width: 100%; padding: 20px 0; position: relative; }
        .lx-event-card { 
            display: block; 
            text-decoration: none !important; 
            background: #111; 
            border-radius: 12px; 
            overflow: hidden; 
            transition: 0.3s ease;
        }
        .lx-event-card:hover { transform: translateY(-5px); }
        
        .lx-event-thumb { position: relative; height: 200px; width: 100%; overflow: hidden; }
        .lx-event-thumb img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .lx-event-card:hover .lx-event-thumb img { transform: scale(1.1); }
        
        /* Badge Event (Gold) */
        .lx-event-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: #d4af37;
            color: #000;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 1px;
            z-index: 2;
        }
        
        .lx-event-info { padding: 20px; text-align: left; }
        
        /* Judul Artikel: Sesuai Request (20px, White, Bold 600) */
        .lx-event-title {
            color: #ffffff !important;
            font-size: 20px !important;
            font-weight: 600 !important;
            font-family: 'Playfair Display', serif; /* Menggunakan font mewah */
            margin: 0 0 10px 0;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            transition: 0.3s;
        }

        /* Tanggal Posting */
        .lx-event-date {
            color: #aaaaaa;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .lx-event-card:hover .lx-event-title { color: #d4af37 !important; }

        .lx-nav { color: #d4af37 !important; transform: scale(0.6); }
        
        @media (max-width: 768px) {
            .lx-event-thumb { height: 170px; }
            .lx-event-title { font-size: 18px !important; }
        }
		@media (max-width: 767px){

    .eventSwiper .swiper-slide {
        width: 100% !important;
    }

}

@media (max-width: 767px){

    .eventSwiper {
        overflow: hidden !important;
    }

    .eventSwiper .swiper-wrapper {
        transform: translate3d(0,0,0);
    }

}
    </style>

  <script>
jQuery(document).ready(function($) {

    new Swiper(".eventSwiper", {

        // Mobile
        slidesPerView: 1,
        slidesPerGroup: 1,
        spaceBetween: 15,

        centeredSlides: false,
        loop: false,

        allowTouchMove: true,
        grabCursor: true,

        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },

        breakpoints: {

            // Tablet & Laptop
            768: {
                slidesPerView: 2,
                slidesPerGroup: 1,
                spaceBetween: 20
            }

        }

    });

});
</script>
    <?php
    return ob_get_clean();
}

/**
 * Memaksa Link Premium Nav Elementor Terbuka di new tab
 */
add_action('wp_footer', 'force_premium_menu_new_tab_specific');
function force_premium_menu_new_tab_specific() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // Target spesifik berdasarkan class yang Anda berikan
        // Ini akan mengenai link induk maupun sub-menu
        $('.premium-menu-link').attr('target', '_blank');
        
        // Opsional: Jika ada link yang dimuat dinamis, kita jalankan ulang setiap ada klik
        $(document).on('click', '.premium-menu-link', function() {
            $(this).attr('target', '_blank');
        });
    });
    </script>
    <?php
}

function get_status_kuota_personel($personel_id, $type = 'foto') {
    global $wpdb;
    $table = ($type == 'video') ? 'wp9y_portofolio_video' : 'wp9y_portofolio';
    $limit = ($type == 'video') ? 8 : 20;
    
    $count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table WHERE personel_id = %d", 
        $personel_id
    ));

    return [
        'is_full' => ($count >= $limit),
        'count'   => $count,
        'limit'   => $limit
    ];
}

/**
 * AJAX & ASSETS UNTUK TOGGLE REKOMENDASI DI HALAMAN CUSTOM ADMIN
 */

// Handle Update via AJAX
add_action('wp_ajax_update_rekomendasi', 'lx_handle_update_rekomendasi');
function lx_handle_update_rekomendasi() {
    global $wpdb;

    // 1. Ambil data dengan proteksi
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $current_status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';

    if (!$id) {
        wp_send_json_error('ID tidak valid.');
    }

    // 2. Tentukan status baru
    $new_status = ($current_status === 'ya') ? 'tidak' : 'ya';

    // 3. Eksekusi Update langsung ke tabel
    // Menggunakan query mentah (query) seringkali lebih ampuh jika update() mengalami kendala cache
    $table_name = 'wp9y_personel';
    $result = $wpdb->query($wpdb->prepare(
        "UPDATE $table_name SET rekomendasi = %s WHERE id = %d",
        $new_status,
        $id
    ));

    // 4. Beri respons berdasarkan hasil eksekusi
    // Kita cek apakah result tidak false (karena 0 baris berubah juga dianggap sukses oleh WP)
    if ($result !== false) {
        wp_send_json_success(['new_status' => $new_status]);
    } else {
        // Jika gagal, kirim pesan error database
        wp_send_json_error($wpdb->last_error);
    }
    
    wp_die(); // Wajib ada untuk AJAX WordPress
}

add_action('wp_ajax_update_show_sosmed', 'lx_handle_update_show_sosmed');
function lx_handle_update_show_sosmed() {
    global $wpdb;
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $current_status = isset($_POST['status']) ? intval($_POST['status']) : 1;

    if (!$id) {
        wp_send_json_error('ID tidak valid.');
    }

    $new_status = ($current_status === 1) ? 0 : 1;
    $table_name = 'wp9y_personel';
    $result = $wpdb->query($wpdb->prepare(
        "UPDATE $table_name SET show_sosmed = %d WHERE id = %d",
        $new_status,
        $id
    ));

    if ($result !== false) {
        wp_send_json_success(['new_status' => $new_status]);
    } else {
        wp_send_json_error($wpdb->last_error);
    }
    wp_die();
}

// Load CSS & JS di Admin
add_action('admin_footer', 'lx_rekomendasi_custom_assets');
function lx_rekomendasi_custom_assets() {
    // Hanya jalankan jika di halaman personel-admin
    if (isset($_GET['page']) && $_GET['page'] === 'personel-admin') {
        ?>
        <style>
            .lx-toggle-btn {
                border: none;
                padding: 6px 10px;
                border-radius: 4px;
                color: #fff !important;
                font-weight: 800;
                font-size: 10px;
                cursor: pointer;
                width: 70px;
                background: #d63638; /* Merah */
                transition: 0.3s;
                outline: none !important;
            }
            .lx-toggle-btn.active {
                background: #00a32a; /* Hijau */
            }
            .lx-toggle-btn:hover { opacity: 0.8; }
            .lx-toggle-btn:disabled { opacity: 0.5; cursor: wait; }

            .lx-toggle-sosmed-btn {
                border: none;
                padding: 6px 10px;
                border-radius: 4px;
                color: #fff !important;
                font-weight: 800;
                font-size: 10px;
                cursor: pointer;
                width: 70px;
                background: #d63638;
                transition: 0.3s;
                outline: none !important;
            }
            .lx-toggle-sosmed-btn.active {
                background: #00a32a;
            }
            .lx-toggle-sosmed-btn:hover { opacity: 0.8; }
            .lx-toggle-sosmed-btn:disabled { opacity: 0.5; cursor: wait; }
        </style>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            $(document).on('click', '.lx-toggle-btn', function(e) {
                e.preventDefault();
                var btn = $(this);
                var id = btn.data('id');
                var status = btn.data('status');

                btn.text('...').prop('disabled', true);

                $.post(ajaxurl, {
                    action: 'update_rekomendasi',
                    id: id,
                    status: status
                }, function(response) {
                    if (response.success) {
                        var ns = response.data.new_status;
                        btn.data('status', ns);
                        btn.text(ns.toUpperCase());
                        if (ns === 'ya') {
                            btn.addClass('active');
                        } else {
                            btn.removeClass('active');
                        }
                    }
                    btn.prop('disabled', false);
                });
            });

            $(document).on('click', '.lx-toggle-sosmed-btn', function(e) {
                e.preventDefault();
                var btn = $(this);
                var id = btn.data('id');
                var status = parseInt(btn.data('status'));

                btn.text('...').prop('disabled', true);

                $.post(ajaxurl, {
                    action: 'update_show_sosmed',
                    id: id,
                    status: status
                }, function(response) {
                    if (response.success) {
                        var ns = parseInt(response.data.new_status);
                        btn.data('status', ns);
                        btn.text(ns === 1 ? 'YA' : 'TIDAK');
                        if (ns === 1) {
                            btn.addClass('active');
                        } else {
                            btn.removeClass('active');
                        }
                    } else {
                        (function(){var d=document.createElement('div');d.style.cssText='position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:999999;display:flex;align-items:center;justify-content:center;';d.innerHTML='<div style="background:#1a1a1a;border:1px solid #ff4444;border-radius:8px;padding:30px 40px;max-width:400px;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,0.5);"><div style="font-size:48px;margin-bottom:10px;">❌</div><p style="color:#fff;font-size:16px;margin:10px 0 20px;">Gagal memperbarui visibilitas sosial media.</p><button onclick="this.parentNode.parentNode.remove()" style="background:#ff4444;color:#fff;border:none;padding:10px 24px;border-radius:4px;font-weight:bold;cursor:pointer;">OK</button></div>';document.body.appendChild(d);})();
                    }
                }).fail(function() {
                    (function(){var d=document.createElement('div');d.style.cssText='position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:999999;display:flex;align-items:center;justify-content:center;';d.innerHTML='<div style="background:#1a1a1a;border:1px solid #ff4444;border-radius:8px;padding:30px 40px;max-width:400px;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,0.5);"><div style="font-size:48px;margin-bottom:10px;">❌</div><p style="color:#fff;font-size:16px;margin:10px 0 20px;">Koneksi server bermasalah.</p><button onclick="this.parentNode.parentNode.remove()" style="background:#ff4444;color:#fff;border:none;padding:10px 24px;border-radius:4px;font-weight:bold;cursor:pointer;">OK</button></div>';document.body.appendChild(d);})();
                }).always(function() {
                    btn.prop('disabled', false);
                });
            });
        });
        </script>
        <?php
    }
}

/**
 * Shortcode Carousel Home Terpisah (Foto / Video)
 * Ukuran Spesifik: 258px x 154px
 */
add_shortcode('carousel_home_porto', 'render_carousel_home_porto_split');

function render_carousel_home_porto_split($atts) {
    global $wpdb;
    
    // Ambil atribut tipe (default: foto)
    $a = shortcode_atts( array(
        'type' => 'foto',
    ), $atts );

    $type = $a['type'];

    // Query berdasarkan tipe
    if ($type == 'video') {
        $results = $wpdb->get_results("SELECT v.*, p.nama_panggilan, p.kode_nama FROM wp9y_portofolio_video v JOIN wp9y_personel p ON v.personel_id = p.id WHERE v.status = 'approved' ORDER BY v.id DESC LIMIT 10");
        $swiper_class = "videoSwiper";
    } else {
        $results = $wpdb->get_results("SELECT f.*, p.nama_panggilan, p.kode_nama FROM wp9y_portofolio f JOIN wp9y_personel p ON f.personel_id = p.id WHERE f.status = 'approved' ORDER BY f.id DESC LIMIT 10");
        $swiper_class = "fotoSwiper";
    }

    if (!$results) return '';

    ob_start(); ?>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <div class="lx-home-split-container">
        <div class="swiper <?php echo $swiper_class; ?>">
            <div class="swiper-wrapper">
                <?php foreach($results as $item) : 
                    if($type == 'video') {
                        preg_match('/(v=|be\/)([a-zA-Z0-9_-]+)/', $item->video_url, $m);
                        $yt_id = $m[2] ?? '';
                        $thumb = "https://img.youtube.com/vi/{$yt_id}/mqdefault.jpg";
                        $data_url = "https://www.youtube.com/embed/{$yt_id}?modestbranding=1&rel=0";
                        $data_type = 'video';
                    } else {
                        $thumb = $item->foto_url;
                        $data_url = $item->foto_url;
                        $data_type = 'image';
                    }
	$nama_depan = strtok($item->nama_panggilan, ' '); 
                ?>
                <div class="swiper-slide">
    <div class="lx-split-item porto-clickable" 
         data-type="<?php echo $data_type; ?>"
         data-url="<?php echo esc_url($data_url); ?>"
         data-title="<?php echo esc_attr($item->judul); ?>"
         data-desc="<?php echo esc_attr($item->deskripsi); ?>"
         data-author="<?php echo esc_attr($nama_depan . '-' . $item->kode_nama); ?>"
         data-tahun="<?php echo $item->tahun; ?>"
         data-lokasi="<?php echo $item->lokasi; ?>"
		 data-kodenama="<?php echo $item->kode_nama; ?>"
         data-tanggal="<?php echo date('d M Y', strtotime($item->tanggal_kegiatan ?? date('Y-m-d'))); ?>">
        
        <div class="lx-split-thumb">
            <img src="<?php echo esc_url($thumb); ?>" loading="lazy">
            <?php if($type == 'video'): ?>
                <div class="lx-play-mini">▶</div>
            <?php endif; ?>
            
            <div class="lx-split-overlay">
                <div class="lx-split-title"><?php echo esc_html($item->judul); ?></div>
                <div class="lx-split-author">
                    by <a href="<?php echo esc_url('https://profesional-indonesia.com/detail-personel/?kode=' . urlencode($item->kode_nama)); ?>">
    <?php echo esc_html($nama_depan . '-' . $item->kode_nama); ?>
</a>
                </div>
            </div>
            
        </div>
    </div>
</div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-button-next lx-nav-split"></div>
            <div class="swiper-button-prev lx-nav-split"></div>
        </div>
    </div>

    <style>
        .lx-home-split-container { padding: 10px 0; width: 100%; position: relative; }
        
        /* Ukuran Spesifik sesuai permintaan user */
        .lx-split-item { 
            position: relative; 
            width: 258px; 
            height: 154px; 
            border-radius: 8px; 
            overflow: hidden; 
            cursor: pointer; 
            border: 1px solid #333;
            background: #000;
            margin: 0 auto;
        }

        .lx-split-thumb, .lx-split-thumb img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .lx-split-item:hover .lx-split-thumb img { transform: scale(1.1); }
        
        .lx-play-mini {
            position: absolute; inset: 0;
            display: flex; align-items: center; justify-content: center;
            background: rgba(0,0,0,0.2); color: #fff; font-size: 30px;
        }

        .lx-nav-split { color: #d4af37 !important; }
        .lx-nav-split:after { font-size: 18px !important; font-weight: bold; }

        /* Memastikan Swiper mengambil ukuran item kita */
        .swiper-slide { width: auto !important; }
		
		/* Styling Overlay Judul di Carousel */
.lx-split-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 30px 10px 10px 10px; /* Padding atas lebih besar untuk gradasi halus */
    background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0) 100%);
    text-align: left;
    pointer-events: none; /* Memastikan overlay tidak mengganggu klik slider */
    z-index: 2; /* Memastikan ada di atas gambar */
}

.lx-split-title {
    color: #d4af37; /* Warna Gold */
    font-size: 14px;
    font-weight: bold;
    line-height: 1.3;
    margin-bottom: 2px;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.8);
    /* Potong teks jika terlalu panjang (opsional) */
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.lx-split-author {
    color: #cccccc;
    font-size: 11px;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.8);
}
    </style>

    <script>
    jQuery(document).ready(function($) {
        new Swiper(".<?php echo $swiper_class; ?>", {
            slidesPerView: "auto", // Mengikuti lebar item CSS (258px)
            spaceBetween: 20,
            slidesPerGroup: 1,
            loop: true,
            navigation: { 
                nextEl: ".swiper-button-next", 
                prevEl: ".swiper-button-prev" 
            },
        });
    });
    </script>


    <?php
    return ob_get_clean();
}


/**
 * SISTEM ARTIKEL PERSONEL - INTEGRASI WORDPRESS POSTS
 */

// --- 1. HANDLER PHP (Simpan Artikel) ---
add_action('init', 'lx_handle_artikel_personel');
function lx_handle_artikel_personel() {
    // Cek jika form disubmit
    if (isset($_POST['submit_artikel']) && isset($_POST['artikel_nonce'])) {
        if (!wp_verify_nonce($_POST['artikel_nonce'], 'tambah_artikel_action')) {
            wp_die('Keamanan tidak valid.');
        }

        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        // Pastikan Kategori "Artikel" ada, jika tidak ada maka buat otomatis
        $cat_id = get_cat_ID('Artikel');
        if($cat_id == 0) {
            $cat_id = wp_create_category('Artikel');
        }

        // Data Post
        $post_data = array(
            'post_title'    => sanitize_text_field($_POST['post_title']),
            'post_content'  => wp_kses_post($_POST['post_content']),
            'post_status'   => 'pending', // Menunggu Approval Admin
            'post_type'     => 'post',
            'post_category' => array($cat_id)
        );

        // Insert Post
        $post_id = wp_insert_post($post_data);

        if ($post_id && !is_wp_error($post_id)) {
            // Upload Gambar Cover (Featured Image)
            if (!empty($_FILES['artikel_cover']['name'])) {
                $attachment_id = media_handle_upload('artikel_cover', $post_id);
                if (!is_wp_error($attachment_id)) {
                    set_post_thumbnail($post_id, $attachment_id);
                }
            }
            
            // Simpan Meta Data (Opsional: simpan ID Personel untuk tracking)
            if(isset($_SESSION['personel_id'])) {
                update_post_meta($post_id, '_personel_author_id', $_SESSION['personel_id']);
            }

            echo "<script>
(function(){
    var d=document.createElement('div');
    d.style.cssText='position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:999999;display:flex;align-items:center;justify-content:center;';
    var m=document.createElement('div');
    m.style.cssText='background:#1a1a1a;border:1px solid #d4af37;border-radius:8px;padding:30px 40px;max-width:400px;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,0.5);';
    var e=document.createElement('div');
    e.style.cssText='font-size:48px;margin-bottom:10px;';
    e.textContent='✅';
    m.appendChild(e);
    var p=document.createElement('p');
    p.style.cssText='color:#fff;font-size:16px;margin:10px 0 20px;';
    p.textContent='Artikel berhasil dikirim! Status: Menunggu Approval Admin.';
    m.appendChild(p);
    var b=document.createElement('button');
    b.style.cssText='background:#d4af37;color:#000;border:none;padding:10px 24px;border-radius:4px;font-weight:bold;cursor:pointer;';
    b.textContent='OK';
    b.onclick=function(){window.location.href='';};
    m.appendChild(b);
    d.appendChild(m);
    document.body.appendChild(d);
})();
</script>";
            exit;
        }
    }
}

// --- 2. FUNGSI TAMPILAN FORM (Render di Tab Artikel) ---
function render_tab_artikel_personel() {
	if ( ! did_action( 'wp_enqueue_media' ) ) {
        wp_enqueue_media();
    }
    ?>

    <style>
        .artikel-form-container {
            background: #111;
            padding: 25px;
            border-radius: 12px;
            border: 1px solid #333;
            max-width: 900px;
            margin: 0 auto;
        }
        .form-group.full { margin-bottom: 20px; width: 100%; }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #d4af37;
            font-weight: bold;
            font-size: 14px;
        }
        .form-group input[type="text"], 
        .form-group input[type="file"] {
            width: 100%;
            padding: 12px;
            background: #1a1a1a;
            border: 1px solid #444;
            color: #fff;
            border-radius: 6px;
        }
        .btn-submit-gold {
            background: linear-gradient(135deg, #d4af37 0%, #b8952d 100%);
            color: #000;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            transition: 0.3s;
        }
        .btn-submit-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
        }
        /* Penyesuaian Editor agar tidak hancur di dark mode */
        .wp-editor-container { background: #fff !important; border-radius: 6px; overflow: hidden; }
        .wp-editor-area { color: #333 !important; }
    </style>

    <div class="artikel-form-container">
        <h2 style="color:#d4af37; margin-top:0;">📝 Buat Artikel Baru</h2>
        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('tambah_artikel_action', 'artikel_nonce'); ?>
            
            <div class="form-group full">
                <label>Judul Artikel <span style="color:red;">*</span></label>
                <input type="text" name="post_title" required placeholder="Contoh: Tips Teknik Cinematic Lighting 2024">
            </div>

            <div class="form-row" style="display:flex; gap:20px; margin-bottom:20px;">
                <div class="form-group" style="flex:1;">
                    <label>Gambar Cover (Thumbnail) <span style="color:red;">*</span></label>
                    <input type="file" name="artikel_cover" accept="image/*" required>
                </div>
                <div class="form-group" style="flex:1;">
                    <label>Kategori</label>
                    <input type="text" value="Artikel" disabled style="opacity:0.6;">
                </div>
            </div>

            <div class="form-group full">
			<label>Isi Konten Artikel <span style="color:red;">*</span></label>
				
			<?php 

			wp_editor('', 'post_content', [
				'textarea_name' => 'post_content',
				'media_buttons' => true, // Sekarang tombol Add Media akan muncul dan berfungsi
				'textarea_rows' => 12,
				'editor_class'  => 'artikel-editor-custom',
				'teeny'         => false,
				'tinymce'       => [
					'toolbar1' => 'bold,italic,underline,separator,bullist,numlist,separator,link,unlink,separator,blockquote,alignleft,aligncenter,alignright,separator,undo,redo',
				]
			]); 
			?>
		</div>

            <button type="submit" name="submit_artikel" class="btn-submit-gold">
                📤 Buat Artikel 
            </button>
        </form>
    </div>
    <?php
}

add_filter('upload_size_limit', 'izinkan_personel_upload_limit');
function izinkan_personel_upload_limit($size) {
    if (current_user_can('personel')) {
        return 1024 * 1024 * 10; // Izinkan 10MB
    }
    return $size;
}

add_filter('show_admin_bar', 'sembunyikan_admin_bar_untuk_personel');

function sembunyikan_admin_bar_untuk_personel($show) {
    // Cek apakah ada user yang sedang login
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        
        // Jika user tersebut memiliki role 'personel', matikan admin bar-nya
        if (in_array('personel', (array) $user->roles)) {
            return false;
        }
    }
    
    // Untuk role lain (seperti Administrator), biarkan tetap muncul
    return $show;
}

function handle_personel_password_reset() {
    global $wpdb;

    // A. PROSES KIRIM EMAIL (Lupa Password)
    if (isset($_POST['personel_forgot_submit'])) {
        $email = sanitize_email($_POST['forgot_email']);
        
        $user = $wpdb->get_row($wpdb->prepare(
            "SELECT id, nama_panggilan FROM wp9y_personel WHERE email = %s", 
            $email
        ));

        if ($user) {
            $token = bin2hex(random_bytes(20));
            // Gunakan current_time('mysql') agar sinkron dengan zona waktu WordPress Anda
$expiry = date("Y-m-d H:i:s", strtotime(current_time('mysql') . ' +1 hour'));

            $wpdb->update('wp9y_personel', 
                ['reset_token' => $token, 'reset_expiry' => $expiry], 
                ['id' => $user->id]
            );

            $reset_link = home_url('/reset-password-personel/?token=' . $token . '&email=' . urlencode($email));
            
            $subject = 'Reset Password Personel - ' . get_bloginfo('name');
            $message = "Halo " . $user->nama_panggilan . ",\n\n";
            $message .= "Kami menerima permintaan untuk meriset password Anda. Klik link di bawah ini:\n\n";
            $message .= $reset_link . "\n\n";
            $message .= "Link ini hanya berlaku selama 1 jam.";
            
            $sent = wp_mail($email, $subject, $message);

            if ($sent) {
                wp_die("Link reset password telah dikirim ke email Anda. Silakan cek Inbox/Spam.", "Email Terkirim");
            }
        } else {
            wp_die("Maaf, email tidak ditemukan di sistem kami.", "Error");
        }
    }

    // B. PROSES UPDATE PASSWORD BARU
    if (isset($_POST['personel_reset_now'])) {
        $token   = sanitize_text_field($_POST['token']);
        $email   = sanitize_email($_POST['email']);
        $pass1   = $_POST['new_pass'];
        $pass2   = $_POST['confirm_pass'];

        if ($pass1 !== $pass2) {
            wp_die("Password tidak cocok. Silakan ulangi.");
        }

        $user = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM wp9y_personel WHERE email = %s AND reset_token = %s AND reset_expiry > NOW()",
            $email, $token
        ));

        if ($user) {
            $hashed_password = wp_hash_password($pass1);
            
            // Update Tabel Custom
            $wpdb->update('wp9y_personel', 
                ['password' => $hashed_password, 'reset_token' => null, 'reset_expiry' => null], 
                ['id' => $user->id]
            );

            // SYNC ke WP User (Jika ada akun bayangan)
            $wp_user = get_user_by('email', $email);
            if ($wp_user) {
                wp_set_password($pass1, $wp_user->ID);
            }

            echo "<script>
(function(){
    var d=document.createElement('div');
    d.style.cssText='position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:999999;display:flex;align-items:center;justify-content:center;';
    var m=document.createElement('div');
    m.style.cssText='background:#1a1a1a;border:1px solid #d4af37;border-radius:8px;padding:30px 40px;max-width:400px;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,0.5);';
    var e=document.createElement('div');
    e.style.cssText='font-size:48px;margin-bottom:10px;';
    e.textContent='✅';
    m.appendChild(e);
    var p=document.createElement('p');
    p.style.cssText='color:#fff;font-size:16px;margin:10px 0 20px;';
    p.textContent='Password berhasil diperbarui! Silakan login kembali.';
    m.appendChild(p);
    var b=document.createElement('button');
    b.style.cssText='background:#d4af37;color:#000;border:none;padding:10px 24px;border-radius:4px;font-weight:bold;cursor:pointer;';
    b.textContent='OK';
    b.onclick=function(){window.location.href='".home_url('/login-personel')."';};
    m.appendChild(b);
    d.appendChild(m);
    document.body.appendChild(d);
})();
</script>";
            exit;
        } else {
            wp_die("Token tidak valid atau sudah kedaluwarsa.");
        }
    }
}
add_action('init', 'handle_personel_password_reset');

function personel_reset_password_form_shortcode() {
    global $wpdb;

    // 1. Ambil data dari URL
    $token = isset($_GET['token']) ? sanitize_text_field($_GET['token']) : '';
    $email = isset($_GET['email']) ? sanitize_email($_GET['email']) : '';

    if (empty($token) || empty($email)) {
        return '<div style="color:red; padding:20px; background:#ffeeee; border-radius:5px;">Link tidak valid atau data tidak lengkap.</div>';
    }

    // 2. Validasi Token dan Expiry ke Database
    $user = $wpdb->get_row($wpdb->prepare(
        "SELECT id FROM wp9y_personel WHERE email = %s AND reset_token = %s AND reset_expiry > NOW()",
        $email, $token
    ));
	// Hapus ini jika sudah normal
if (!$user) {
    global $wpdb;
    $db_check = $wpdb->get_row($wpdb->prepare("SELECT reset_token, reset_expiry FROM wp9y_personel WHERE email = %s", $email));
    echo "<div style='color:black; background:yellow; padding:10px; font-size:11px;'>";
    echo "DEBUG INFO:<br>";
    echo "Email URL: $email <br>";
    echo "Token URL: $token <br>";
    echo "Token di DB: " . ($db_check ? $db_check->reset_token : 'Email Tidak Ada') . "<br>";
    echo "Expiry di DB: " . ($db_check ? $db_check->reset_expiry : '-') . "<br>";
    echo "Waktu Server Sekarang: " . current_time('mysql');
    echo "</div>";
}
    if (!$user) {
        return '<div style="color:red; padding:20px; background:#ffeeee; border-radius:5px;">Link reset password sudah kedaluwarsa atau tidak valid. Silakan ajukan lupa password kembali.</div>';
    }

    // 3. Tampilkan Form jika Valid
    ob_start(); // Mulai output buffering
    ?>
    <div class="reset-password-container" style="max-width: 400px; margin: 20px auto; padding: 25px; background: #1a1a1a; border-radius: 10px; color: #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
        <h3 style="color: #EAB308; margin-bottom: 20px; text-align: center;">Buat Password Baru</h3>
        
        <form method="post" action="">
            <input type="hidden" name="token" value="<?php echo esc_attr($token); ?>">
            <input type="hidden" name="email" value="<?php echo esc_attr($email); ?>">
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-size: 14px;">Password Baru</label>
                <input type="password" name="new_pass" required minlength="6" 
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #333; background: #222; color: #fff;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-size: 14px;">Konfirmasi Password Baru</label>
                <input type="password" name="confirm_pass" required minlength="6" 
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #333; background: #222; color: #fff;">
            </div>

            <button type="submit" name="personel_reset_now" 
                    style="width: 100%; padding: 12px; background: #EAB308; border: none; border-radius: 5px; color: #000; font-weight: bold; cursor: pointer;">
                PERBARUI PASSWORD
            </button>
        </form>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('personel_reset_form', 'personel_reset_password_form_shortcode');

// ============================================================
// POPUP IKLAN - Global Portrait Popup Ad System
// ============================================================

/**
 * Step 1: Create/verify the custom database table for popup ad
 */
function popup_ad_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'popup_ad';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        image_url VARCHAR(500) NOT NULL DEFAULT '',
        link_url VARCHAR(500) DEFAULT '',
        is_active TINYINT(1) DEFAULT 0,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Insert default row if table is empty
    $row = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    if ($row == 0) {
        $wpdb->insert($table_name, [
            'image_url' => '',
            'link_url'  => '',
            'is_active'  => 0,
        ]);
    }
}
add_action('admin_init', 'popup_ad_create_table');

/**
 * Step 2: Register WP Admin Menu
 */
function popup_ad_admin_menu() {
    add_menu_page(
        'Popup Iklan',           // Page title
        'Popup Iklan',           // Menu title
        'manage_options',        // Capability
        'popup-iklan',           // Menu slug
        'popup_ad_admin_page',   // Callback
        'dashicons-format-image', // Icon
        80                       // Position
    );
}
add_action('admin_menu', 'popup_ad_admin_menu');

/**
 * Step 2b: Enqueue media uploader on our admin page
 */
function popup_ad_admin_enqueue($hook) {
    if ($hook !== 'toplevel_page_popup-iklan') {
        return;
    }
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'popup_ad_admin_enqueue');

/**
 * Step 2c: Admin page render & save handler
 */
function popup_ad_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'popup_ad';

    // Handle save
    if (isset($_POST['popup_ad_save']) && check_admin_referer('popup_ad_nonce_action', 'popup_ad_nonce_field')) {
        $image_url = esc_url_raw(sanitize_text_field($_POST['popup_ad_image_url'] ?? ''));
        $link_url  = esc_url_raw(sanitize_text_field($_POST['popup_ad_link_url'] ?? ''));
        $is_active = isset($_POST['popup_ad_is_active']) ? 1 : 0;

        $exists = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE id = 1");
        if ($exists) {
            $wpdb->update($table_name, [
                'image_url' => $image_url,
                'link_url'  => $link_url,
                'is_active' => $is_active,
            ], ['id' => 1]);
        } else {
            $wpdb->insert($table_name, [
                'image_url' => $image_url,
                'link_url'  => $link_url,
                'is_active' => $is_active,
            ]);
        }

        echo '<div class="notice notice-success is-dismissible"><p>Pengaturan Popup Iklan berhasil disimpan!</p></div>';
    }

    // Fetch current data
    $data = $wpdb->get_row("SELECT * FROM $table_name WHERE id = 1");
    $image_url = $data->image_url ?? '';
    $link_url  = $data->link_url ?? '';
    $is_active = $data->is_active ?? 0;
    ?>
    <div class="wrap">
        <h1 style="display:flex;align-items:center;gap:10px;">
            <span class="dashicons dashicons-format-image" style="font-size:30px;width:30px;height:30px;color:#e67e22;"></span>
            Pengaturan Popup Iklan
        </h1>
        <p style="color:#666;font-size:14px;">Kelola iklan popup portrait yang tampil di seluruh halaman depan website.</p>
        <hr>

        <form method="post" style="max-width:700px;">
            <?php wp_nonce_field('popup_ad_nonce_action', 'popup_ad_nonce_field'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row"><label for="popup_ad_image_url">Gambar Iklan (Portrait)</label></th>
                    <td>
                        <input type="text" id="popup_ad_image_url" name="popup_ad_image_url"
                               value="<?php echo esc_attr($image_url); ?>"
                               class="regular-text" style="width:100%;" placeholder="https://example.com/gambar-iklan.jpg" />
                        <br><br>
                        <button type="button" class="button button-secondary" id="popup_ad_upload_btn">
                            <span class="dashicons dashicons-upload" style="vertical-align:middle;margin-right:4px;"></span>
                            Upload / Pilih Gambar
                        </button>
                        <p class="description">Gunakan gambar portrait (rasio 2:3 atau 9:16) untuk hasil terbaik.</p>

                        <!-- Preview -->
                        <div id="popup_ad_preview" style="margin-top:15px;<?php echo empty($image_url) ? 'display:none;' : ''; ?>">
                            <p style="font-weight:600;margin-bottom:8px;">Preview:</p>
                            <div style="width:200px;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.15);border:2px solid #e0e0e0;">
                                <img id="popup_ad_preview_img" src="<?php echo esc_url($image_url); ?>"
                                     style="width:100%;height:auto;display:block;" />
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="popup_ad_link_url">Link Tujuan (Opsional)</label></th>
                    <td>
                        <input type="url" id="popup_ad_link_url" name="popup_ad_link_url"
                               value="<?php echo esc_attr($link_url); ?>"
                               class="regular-text" style="width:100%;" placeholder="https://example.com/promo" />
                        <p class="description">Ketika gambar diklik, pengunjung akan diarahkan ke URL ini. Kosongkan jika tidak perlu.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Status</th>
                    <td>
                        <label style="display:inline-flex;align-items:center;gap:8px;cursor:pointer;">
                            <input type="checkbox" name="popup_ad_is_active" value="1" <?php checked($is_active, 1); ?> />
                            <span style="font-weight:600;color:<?php echo $is_active ? '#27ae60' : '#e74c3c'; ?>;">
                                <?php echo $is_active ? '● Aktif — Popup sedang ditampilkan' : '○ Nonaktif — Popup disembunyikan'; ?>
                            </span>
                        </label>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <button type="submit" name="popup_ad_save" class="button button-primary button-large">
                    <span class="dashicons dashicons-saved" style="vertical-align:middle;margin-right:4px;"></span>
                    Simpan Pengaturan
                </button>
            </p>
        </form>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // Media uploader
        $('#popup_ad_upload_btn').on('click', function(e) {
            e.preventDefault();
            var frame = wp.media({
                title: 'Pilih Gambar Iklan',
                button: { text: 'Gunakan Gambar Ini' },
                multiple: false,
                library: { type: 'image' }
            });
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#popup_ad_image_url').val(attachment.url);
                $('#popup_ad_preview_img').attr('src', attachment.url);
                $('#popup_ad_preview').show();
            });
            frame.open();
        });

        // Live preview on manual URL change
        $('#popup_ad_image_url').on('input change', function() {
            var val = $(this).val();
            if (val) {
                $('#popup_ad_preview_img').attr('src', val);
                $('#popup_ad_preview').show();
            } else {
                $('#popup_ad_preview').hide();
            }
        });
    });
    </script>
    <?php
}

/**
 * Step 3: Render popup on the frontend (all pages)
 */
function popup_ad_render_frontend() {
    // Don't show in admin or login pages
    if (is_admin() || wp_doing_ajax()) {
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'popup_ad';

    // Check if the table exists first
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
    if (!$table_exists) {
        return;
    }

    $data = $wpdb->get_row("SELECT * FROM $table_name WHERE id = 1");

    // Only render if active and image exists
    if (!$data || !$data->is_active || empty($data->image_url)) {
        return;
    }

    $image_url = esc_url($data->image_url);
    $link_url  = esc_url($data->link_url);
    $has_link  = !empty($link_url);
    ?>

    <!-- Popup Iklan - Global Portrait Ad -->
    <style>
        /* Overlay */
        .popup-iklan-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 999999;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.4s cubic-bezier(0.16, 1, 0.3, 1),
                        visibility 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .popup-iklan-overlay.popup-iklan-show {
            opacity: 1;
            visibility: visible;
        }

        /* Container */
        .popup-iklan-container {
            position: relative;
            max-width: 380px;
            width: 88vw;
            transform: scale(0.85) translateY(30px);
            transition: transform 0.45s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .popup-iklan-overlay.popup-iklan-show .popup-iklan-container {
            transform: scale(1) translateY(0);
        }

        /* Image wrapper */
        .popup-iklan-img-wrap {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5),
                        0 0 0 1px rgba(255, 255, 255, 0.08);
            line-height: 0;
        }
        .popup-iklan-img-wrap img {
            width: 100%;
            height: auto;
            display: block;
            object-fit: cover;
        }
        .popup-iklan-img-wrap a {
            display: block;
            line-height: 0;
        }

        /* Close button */
        .popup-iklan-close {
            position: absolute;
            top: -16px;
            right: -16px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #ffffff;
            color: #1a1a1a;
            border: 3px solid rgba(0, 0, 0, 0.1);
            font-size: 20px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
            transition: all 0.25s ease;
            z-index: 10;
            font-family: Arial, Helvetica, sans-serif;
        }
        .popup-iklan-close:hover {
            background: #ef4444;
            color: #ffffff;
            border-color: #ef4444;
            transform: scale(1.15) rotate(90deg);
            box-shadow: 0 4px 20px rgba(239, 68, 68, 0.5);
        }

        /* Mobile adjustments */
        @media (max-width: 480px) {
            .popup-iklan-container {
                max-width: 320px;
                width: 85vw;
            }
            .popup-iklan-close {
                top: -12px;
                right: -12px;
                width: 36px;
                height: 36px;
                font-size: 18px;
            }
            .popup-iklan-img-wrap {
                border-radius: 12px;
            }
        }
    </style>

    <div class="popup-iklan-overlay" id="popupIklanOverlay">
        <div class="popup-iklan-container">
            <button class="popup-iklan-close" id="popupIklanClose" aria-label="Tutup Popup" title="Tutup">&#10005;</button>
            <div class="popup-iklan-img-wrap">
                <?php if ($has_link): ?>
                    <a href="<?php echo $link_url; ?>" target="_blank" rel="noopener noreferrer">
                        <img src="<?php echo $image_url; ?>" alt="Iklan Popup" />
                    </a>
                <?php else: ?>
                    <img src="<?php echo $image_url; ?>" alt="Iklan Popup" />
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    (function() {
        var overlay = document.getElementById('popupIklanOverlay');
        var closeBtn = document.getElementById('popupIklanClose');
        if (!overlay || !closeBtn) return;

        // Session-based: show only once per browser session
        var storageKey = 'popup_iklan_closed';
        if (sessionStorage.getItem(storageKey) === '1') {
            overlay.remove();
            return;
        }

        // Show the popup after a brief delay for smoother entry
        setTimeout(function() {
            overlay.classList.add('popup-iklan-show');
        }, 500);

        function closePopup() {
            overlay.classList.remove('popup-iklan-show');
            sessionStorage.setItem(storageKey, '1');
            setTimeout(function() {
                overlay.remove();
            }, 450);
        }

        // Close button
        closeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            closePopup();
        });

        // Click outside image to close
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                closePopup();
            }
        });

        // ESC key to close
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && overlay.classList.contains('popup-iklan-show')) {
                closePopup();
            }
        });
    })();
    </script>
    <!-- /Popup Iklan -->

    <?php
}
add_action('wp_footer', 'popup_ad_render_frontend', 9999);

// ============================================================
// SISTEM KATEGORI PORTOFOLIO (MANY-TO-MANY)
// ============================================================

/**
 * Step 1: Create custom database tables and populate categories
 */
function portfolio_kategori_setup_db() {
    global $wpdb;
    $kategori_table = 'wp9y_kategori';
    $map_foto_table = 'wp9y_portofolio_kategori_map';
    $map_video_table = 'wp9y_portofolio_video_kategori_map';
    $charset_collate = $wpdb->get_charset_collate();

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    // Create kategori table
    $sql_kategori = "CREATE TABLE $kategori_table (
        id INT NOT NULL AUTO_INCREMENT,
        nama VARCHAR(100) NOT NULL UNIQUE,
        slug VARCHAR(100) NOT NULL UNIQUE,
        deskripsi TEXT,
        PRIMARY KEY (id)
    ) $charset_collate;";
    dbDelta($sql_kategori);

    // Create mapping table for photos
    $sql_map_foto = "CREATE TABLE $map_foto_table (
        portofolio_id BIGINT NOT NULL,
        kategori_id INT NOT NULL,
        PRIMARY KEY (portofolio_id, kategori_id)
    ) $charset_collate;";
    dbDelta($sql_map_foto);

    // Create mapping table for videos
    $sql_map_video = "CREATE TABLE $map_video_table (
        video_id BIGINT NOT NULL,
        kategori_id INT NOT NULL,
        PRIMARY KEY (video_id, kategori_id)
    ) $charset_collate;";
    dbDelta($sql_map_video);

    // Create draft edit table for personnel profiles
    $draft_edit_table = 'wp9y_personel_draft_edit';
    $wpdb->query("CREATE TABLE IF NOT EXISTS $draft_edit_table (
        id INT NOT NULL AUTO_INCREMENT,
        personel_id BIGINT NOT NULL UNIQUE,
        draft_data LONGTEXT NOT NULL,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;");

    // Populate categories if empty
    $count = $wpdb->get_var("SELECT COUNT(*) FROM $kategori_table");
    if ($count == 0) {
        $categories = [
            [
                'nama' => 'Wedding & Personal',
                'slug' => 'wedding-personal',
                'deskripsi' => 'Dokumentasi pernikahan dan sesi pribadi seperti prewedding, foto keluarga, maternity, wisuda, hingga personal branding.'
            ],
            [
                'nama' => 'Corporate & Company',
                'slug' => 'corporate-company',
                'deskripsi' => 'Dokumentasi komersial perusahaan, profil bisnis, event korporasi, kegiatan industri, serta dokumentasi instansi pemerintah.'
            ],
            [
                'nama' => 'Event Documentation',
                'slug' => 'event-documentation',
                'deskripsi' => 'Dokumentasi acara skala besar/kecil seperti festival musik, konser, acara komunitas, serta liputan kompetisi olahraga.'
            ],
            [
                'nama' => 'Commercial & Advertising',
                'slug' => 'commercial-advertising',
                'deskripsi' => 'Pembuatan iklan TV, iklan digital, foto & video produk komersial, makanan & minuman (F&B), serta konten kreatif media sosial.'
            ],
            [
                'nama' => 'Media & Entertainment',
                'slug' => 'media-entertainment',
                'deskripsi' => 'Produksi video klip musik, program penyiaran (talkshow/podcast), dokumentasi talenta/artis, serta karya jurnalistik.'
            ],
            [
                'nama' => 'Film & Cinematic',
                'slug' => 'film-cinematic',
                'deskripsi' => 'Produksi film pendek/indie, web series, video cinematic perjalanan (travel), film dokumenter, hingga motion graphics.'
            ],
            [
                'nama' => 'Drone & Aerial',
                'slug' => 'drone-aerial',
                'deskripsi' => 'Pengambilan gambar dari udara menggunakan drone untuk kebutuhan sinematik, dokumentasi, pemetaan (mapping), hingga inspeksi.'
            ],
            [
                'nama' => 'Outdoor, Travel & Nature',
                'slug' => 'outdoor-travel-nature',
                'deskripsi' => 'Dokumentasi petualangan outdoor, promosi pariwisata & perhotelan, keindahan alam (landscape), serta kehidupan alam liar.'
            ],
            [
                'nama' => 'Photography Art & Style',
                'slug' => 'photography-art-style',
                'deskripsi' => 'Karya seni fotografi dalam berbagai aliran seperti street photography, fine art, portrait, fashion photoshoot, hingga arsitektur.'
            ],
            [
                'nama' => 'Multimedia & Production Service',
                'slug' => 'multimedia-production-service',
                'deskripsi' => 'Layanan produksi live streaming event, jasa editing & color grading, penulisan naskah, serta manajemen konten digital.'
            ],
            [
                'nama' => 'Lainnya',
                'slug' => 'lainnya',
                'deskripsi' => 'Kategori alternatif untuk bidang kreatif khusus yang tidak tercantum dalam 10 kategori utama.'
            ]
        ];
        foreach ($categories as $cat) {
            $wpdb->insert($kategori_table, [
                'nama' => $cat['nama'],
                'slug' => $cat['slug'],
                'deskripsi' => $cat['deskripsi']
            ]);
        }
    }
}
add_action('init', 'portfolio_kategori_setup_db');

/**
 * Step 2: Render category selection checkboxes in personnel dashboard forms (up to 3)
 */
function render_portfolio_category_selection($porto_id = 0, $type = 'foto') {
    global $wpdb;
    
    // Check table existence to prevent errors
    $table_kategori = 'wp9y_kategori';
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_kategori'");
    if (!$table_exists) {
        return;
    }

    $categories = $wpdb->get_results("SELECT * FROM $table_kategori ORDER BY id ASC");
    if (empty($categories)) {
        return;
    }
    
    $selected_cats = [];
    if ($porto_id > 0) {
        $table_map = ($type === 'video') ? 'wp9y_portofolio_video_kategori_map' : 'wp9y_portofolio_kategori_map';
        $id_column = ($type === 'video') ? 'video_id' : 'portofolio_id';
        $selected_cats = $wpdb->get_col($wpdb->prepare("SELECT kategori_id FROM $table_map WHERE $id_column = %d", $porto_id));
    }
    ?>
    <style>
        .porto-cat-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 10px;
        }
        .porto-cat-item {
            display: table;
            width: 100%;
            table-layout: fixed;
            background: #1a1a1a;
            border-radius: 6px;
            border: 1px solid #333;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
        }
        .porto-cat-item:hover {
            border-color: #d4af37;
            background: #222;
        }
        .porto-cat-chk {
            display: table-cell;
            width: 36px;
            vertical-align: top;
            padding: 12px 0 0 14px;
        }
        .porto-cat-chk input[type="checkbox"] {
            accent-color: #d4af37;
            cursor: pointer;
        }
        .porto-cat-name {
            display: table-cell;
            width: 200px;
            vertical-align: top;
            padding: 10px 14px;
            font-size: 14px;
            color: #eee;
            font-weight: 600;
            line-height: 1.4;
            border-right: 1px solid #333;
        }
        .porto-cat-desc {
            display: table-cell;
            vertical-align: top;
            padding: 10px 14px;
            font-size: 12px;
            color: #999;
            line-height: 1.5;
            word-break: break-word;
            overflow-wrap: break-word;
        }
        .porto-cat-item.selected {
            border-color: #d4af37;
        }
        .porto-cat-item.selected .porto-cat-name {
            color: #d4af37;
            border-right-color: #d4af37;
        }
        .porto-cat-item.disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }
        .porto-cat-item.disabled input {
            cursor: not-allowed;
        }
        @media (max-width: 640px) {
            .porto-cat-item {
                display: block;
            }
            .porto-cat-chk {
                display: inline-block;
                width: auto;
                padding: 10px 0 0 14px;
                vertical-align: top;
            }
            .porto-cat-name {
                display: inline-block;
                width: auto;
                border-right: none;
                padding: 8px 14px 4px 6px;
            }
            .porto-cat-desc {
                display: block;
                padding: 0 14px 8px 50px;
            }
        }
    </style>

    <div class="form-group full" style="margin-bottom: 25px;">
        <label style="display:block; margin-bottom:5px; color:#d4af37; font-weight:bold;">
            Kategori Portofolio (Maksimal Pilih 3) <span style="font-weight:normal; font-size:12px; color:#aaa;">- Opsional</span>
        </label>
        <div class="porto-cat-list">
            <?php foreach ($categories as $cat) : 
                $checked = in_array($cat->id, $selected_cats) ? 'checked' : '';
                $item_class = $checked ? 'selected' : '';
            ?>
                <label class="porto-cat-item <?php echo $item_class; ?>">
                    <span class="porto-cat-chk"><input type="checkbox" name="portfolio_kategori[]" value="<?php echo $cat->id; ?>" <?php echo $checked; ?> class="porto-cat-cb"></span>
                    <span class="porto-cat-name"><?php echo esc_html($cat->nama); ?></span>
                    <span class="porto-cat-desc"><?php echo esc_html($cat->deskripsi ?? ''); ?></span>
                </label>
            <?php endforeach; ?>
        </div>
        <small style="color:var(--text-muted); display:block; margin-top:8px;">Pilih maksimal 3 kategori yang paling relevan dengan karya portofolio ini.</small>
    </div>

    <script>
    jQuery(document).ready(function($) {
        function updateCheckboxLimit() {
            var checkedCbs = $('.porto-cat-cb:checked');
            var uncheckedCbs = $('.porto-cat-cb:not(:checked)');
            
            $('.porto-cat-item').removeClass('selected');
            checkedCbs.closest('.porto-cat-item').addClass('selected');

            if (checkedCbs.length >= 3) {
                uncheckedCbs.prop('disabled', true);
                uncheckedCbs.closest('.porto-cat-item').addClass('disabled');
            } else {
                $('.porto-cat-cb').prop('disabled', false);
                $('.porto-cat-item').removeClass('disabled');
            }
        }

        $(document).on('change', '.porto-cat-cb', function() {
            updateCheckboxLimit();
        });

        updateCheckboxLimit();
    });
    </script>
    <?php
}

/**
 * Step 3: Render category sub-navbar filter bar and explanation box in frontend
 */
function render_portfolio_category_filter_bar($type = 'foto') {
    $categories = [
        ['nama' => 'Wedding & Personal', 'slug' => 'wedding-personal'],
        ['nama' => 'Corporate & Company', 'slug' => 'corporate-company'],
        ['nama' => 'Event Documentation', 'slug' => 'event-documentation'],
        ['nama' => 'Commercial & Advertising', 'slug' => 'commercial-advertising'],
        ['nama' => 'Media & Entertainment', 'slug' => 'media-entertainment'],
        ['nama' => 'Film & Cinematic', 'slug' => 'film-cinematic'],
        ['nama' => 'Drone & Aerial', 'slug' => 'drone-aerial'],
        ['nama' => 'Outdoor, Travel & Nature', 'slug' => 'outdoor-travel-nature'],
        ['nama' => 'Photography Art & Style', 'slug' => 'photography-art-style'],
        ['nama' => 'Multimedia & Production Service', 'slug' => 'multimedia-production-service'],
        ['nama' => 'Lainnya', 'slug' => 'lainnya']
    ];
    
    $input_id = ($type === 'video') ? 'active-category-slug-video' : 'active-category-slug';
    ?>
    <style>
        .lx-cat-nav-wrapper {
            margin: 20px 0 15px 0;
            background: rgba(17, 17, 17, 0.9);
            border: 1px solid #222;
            border-radius: 12px;
            padding: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .lx-cat-nav {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            padding: 5px;
        }
        .lx-cat-btn {
            background: #151515;
            color: #888;
            border: 1px solid #2a2a2a;
            padding: 10px 20px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            white-space: nowrap;
        }
        .lx-cat-btn:hover {
            border-color: #d4af37;
            color: #d4af37;
            transform: translateY(-1px);
        }
        .lx-cat-btn.active {
            background: #d4af37;
            color: #000;
            border-color: #d4af37;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.35);
        }
        
        @media (max-width: 768px) {
            .lx-cat-nav {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
                width: 100%;
            }
            .lx-cat-btn {
                width: 100%;
                min-height: 48px;
                padding: 6px 8px;
                font-size: 11px;
                white-space: normal;
                line-height: 1.3;
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                box-sizing: border-box;
            }
        }

        /* Detail Box Styles */
        .lx-cat-detail-box {
            background: linear-gradient(135deg, #121212 0%, #0d0d0d 100%);
            border: 1px solid #222;
            border-left: 4px solid #d4af37;
            border-radius: 8px;
            padding: 24px;
            margin: 0 0 30px 0;
            display: none; /* Controlled via JS */
            box-shadow: 0 10px 25px rgba(0,0,0,0.4);
        }
        
        .lx-cat-detail-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 25px;
        }
        @media (min-width: 769px) {
            .lx-cat-detail-grid {
                grid-template-columns: 1fr 2.5fr;
            }
        }
        .lx-cat-detail-left h3 {
            color: #d4af37;
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            margin: 0 0 12px 0;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .lx-cat-detail-left p {
            color: #aaa;
            font-size: 13.5px;
            line-height: 1.6;
            margin: 0;
        }
        .lx-cat-detail-right {
            border-top: 1px solid #222;
            padding-top: 20px;
        }
        @media (min-width: 769px) {
            .lx-cat-detail-right {
                border-top: none;
                border-left: 1px solid #222;
                padding-top: 0;
                padding-left: 25px;
            }
        }
        .lx-cat-detail-right-title {
            color: #888;
            font-size: 11px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 15px;
        }
        .lx-cat-columns {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        .lx-cat-group {
            margin-bottom: 10px;
        }
        .lx-cat-group-name {
            color: #d4af37;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .lx-cat-group-name::before {
            content: '▫';
            color: #d4af37;
        }
        .lx-cat-group-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .lx-cat-group-list li {
            color: #bbb;
            font-size: 12.5px;
            line-height: 1.5;
            margin-bottom: 6px;
            padding-left: 12px;
            position: relative;
        }
        .lx-cat-group-list li::before {
            content: '•';
            color: #666;
            position: absolute;
            left: 0;
            top: 0;
        }
    </style>

    <input type="hidden" id="<?php echo $input_id; ?>" value="semua">
    
    <div class="lx-cat-nav-wrapper">
        <div class="lx-cat-nav">
            <button class="lx-cat-btn active" data-slug="semua">Semua Kategori</button>
            <?php foreach ($categories as $cat): ?>
                <button class="lx-cat-btn" data-slug="<?php echo esc_attr($cat['slug']); ?>">
                    <?php echo esc_html($cat['nama']); ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="lx-cat-detail-box" id="lx-cat-detail-box-<?php echo $type; ?>">
        <div class="lx-cat-detail-grid">
            <div class="lx-cat-detail-left">
                <h3 id="lx-cat-title-<?php echo $type; ?>"></h3>
                <p id="lx-cat-desc-<?php echo $type; ?>"></p>
            </div>
            <div class="lx-cat-detail-right">
                <div class="lx-cat-detail-right-title">Bidang / Layanan Yang Dicakup</div>
                <div class="lx-cat-columns" id="lx-cat-columns-<?php echo $type; ?>"></div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
    jQuery(document).ready(function($) {
        const categoriesData = {
          "semua": {
            "title": "Semua Kategori",
            "desc": "Menampilkan seluruh karya portofolio personel kreatif kami dalam berbagai bidang keahlian.",
            "columns": []
          },
          "wedding-personal": {
            "title": "Wedding & Personal",
            "desc": "Dokumentasi pernikahan dan sesi pribadi seperti prewedding, foto keluarga, maternity, wisuda, hingga personal branding.",
            "columns": [
              {
                "group": "Wedding",
                "items": ["Wedding Day", "Akad Nikah", "Resepsi", "Intimate Wedding", "Destination Wedding", "Traditional Wedding"]
              },
              {
                "group": "Prewedding",
                "items": ["Outdoor Prewedding", "Studio Prewedding", "Cinematic Prewedding", "Casual Prewedding"]
              },
              {
                "group": "Personal & Family",
                "items": ["Family Session", "Couple Session", "Maternity", "Baby Born", "Kids Photography", "Birthday", "Sweet 17", "Graduation / Wisuda", "Personal Branding"]
              }
            ]
          },
          "corporate-company": {
            "title": "Corporate & Company",
            "desc": "Dokumentasi komersial perusahaan, profil bisnis, event korporasi, kegiatan industri, serta dokumentasi instansi pemerintah.",
            "columns": [
              {
                "group": "Company Profile",
                "items": ["Company Profile Video", "Company Profile Photo", "Office Documentation", "Factory Documentation", "Industrial Documentation"]
              },
              {
                "group": "Corporate Event",
                "items": ["Gathering", "Meeting", "Seminar", "Workshop", "Conference", "Training / Pelatihan", "Launching Event", "Awarding", "Expo / Exhibition"]
              },
              {
                "group": "Industrial & Safety",
                "items": ["Safety Induction", "SOP Video", "Construction Documentation", "Manufacturing Documentation", "Warehouse Activity"]
              },
              {
                "group": "Government & Institution",
                "items": ["CSR", "Government Event", "BUMN", "School / Campus Documentation", "Pelantikan", "Ceremony"]
              }
            ]
          },
          "event-documentation": {
            "title": "Event Documentation",
            "desc": "Dokumentasi acara skala besar/kecil seperti festival musik, konser, acara komunitas, serta liputan kompetisi olahraga.",
            "columns": [
              {
                "group": "General Event",
                "items": ["Festival", "Concert", "Community Event", "Religious Event", "Cultural Event", "School Event", "Campus Event"]
              },
              {
                "group": "Event Coverage",
                "items": ["Event Photography", "Event Videography", "Multi Camera Event", "Highlight Video", "Aftermovie", "Live Event Coverage"]
              },
              {
                "group": "Competition & Sport",
                "items": ["Competition", "Sports Event", "E-Sport Event", "Tournament"]
              }
            ]
          },
          "commercial-advertising": {
            "title": "Commercial & Advertising",
            "desc": "Pembuatan iklan TV, iklan digital, foto & video produk komersial, makanan & minuman (F&B), serta konten kreatif media sosial.",
            "columns": [
              {
                "group": "Advertising",
                "items": ["TV Commercial / TVC", "Digital Ads", "Campaign Video", "Promotional Video", "Branding Video"]
              },
              {
                "group": "Product",
                "items": ["Product Photography", "Product Videography", "Marketplace Product", "E-Commerce Product", "Beauty Product", "Fashion Product", "Gadget Product", "Automotive Product"]
              },
              {
                "group": "Food & Beverage",
                "items": ["Food Photography", "Beverage Photography", "Restaurant Content", "Cafe Content", "Cooking Video"]
              },
              {
                "group": "Social Media Content",
                "items": ["Instagram Content", "TikTok Content", "YouTube Content", "Reels & Shorts", "Testimonial Video"]
              }
            ]
          },
          "media-entertainment": {
            "title": "Media & Entertainment",
            "desc": "Produksi video klip musik, program penyiaran (talkshow/podcast), dokumentasi talenta/artis, serta karya jurnalistik.",
            "columns": [
              {
                "group": "Music",
                "items": ["Music Video", "Live Music", "Band Session", "Acoustic Session", "Studio Session"]
              },
              {
                "group": "Broadcasting",
                "items": ["TV Program", "Talkshow", "Podcast", "Interview", "Live Podcast"]
              },
              {
                "group": "Entertainment",
                "items": ["Behind The Scene", "B-Roll", "Talent Documentation", "Artist Documentation"]
              },
              {
                "group": "Journalism",
                "items": ["News Coverage", "Journalism", "Human Interest", "Documentary News"]
              }
            ]
          },
          "film-cinematic": {
            "title": "Film & Cinematic",
            "desc": "Produksi film pendek/indie, web series, video cinematic perjalanan (travel), film dokumenter, hingga motion graphics.",
            "columns": [
              {
                "group": "Film Production",
                "items": ["Short Film", "Indie Film", "Web Series", "Mini Series"]
              },
              {
                "group": "Cinematic Content",
                "items": ["Cinematic Event", "Cinematic Travel", "Cinematic Commercial", "Storytelling Video"]
              },
              {
                "group": "Documentary",
                "items": ["Documentary Film", "Nature Documentary", "Social Documentary", "Company Documentary"]
              },
              {
                "group": "Animation & Education",
                "items": ["Motion Graphic", "Animation", "Explainer Video", "Educational Video", "Infographic Video"]
              }
            ]
          },
          "drone-aerial": {
            "title": "Drone & Aerial",
            "desc": "Pengambilan gambar dari udara menggunakan drone untuk kebutuhan sinematik, dokumentasi, pemetaan (mapping), hingga inspeksi.",
            "columns": [
              {
                "group": "Drone Cinematic",
                "items": ["Drone Aerial", "Drone FPV", "Cinematic FPV", "Indoor FPV", "Real Estate Drone"]
              },
              {
                "group": "Drone Documentation",
                "items": ["Event Drone", "Tourism Drone", "Construction Drone", "Industrial Drone"]
              },
              {
                "group": "Mapping & Survey",
                "items": ["Drone Mapping", "Orthophoto", "Topography", "Geomatics", "GIS Mapping", "Land Survey"]
              },
              {
                "group": "Inspection & Monitoring",
                "items": ["Progress Monitoring", "Infrastructure Inspection", "Tower Inspection", "Roof Inspection"]
              }
            ]
          },
          "outdoor-travel-nature": {
            "title": "Outdoor, Travel & Nature",
            "desc": "Dokumentasi petualangan outdoor, promosi pariwisata & perhotelan, keindahan alam (landscape), serta kehidupan alam liar.",
            "columns": [
              {
                "group": "Outdoor Activity",
                "items": ["Adventure", "Hiking", "Camping", "Offroad", "Touring"]
              },
              {
                "group": "Travel & Tourism",
                "items": ["Travel Documentation", "Tourism Promotion", "Destination Video", "Hotel & Resort", "Travel Photography"]
              },
              {
                "group": "Nature",
                "items": ["Landscape", "Mountain", "Beach", "Forest", "Waterfall", "Sunrise & Sunset"]
              },
              {
                "group": "Wildlife",
                "items": ["Animals", "Wildlife Photography", "Bird Photography"]
              }
            ]
          },
          "photography-art-style": {
            "title": "Photography Art & Style",
            "desc": "Karya seni fotografi dalam berbagai aliran seperti street photography, fine art, portrait, fashion photoshoot, hingga arsitektur.",
            "columns": [
              {
                "group": "Photography Style",
                "items": ["Street Photography", "Fine Art Photography", "Portrait Photography", "Editorial Photography", "Black & White Photography"]
              },
              {
                "group": "Creative Photography",
                "items": ["Long Exposure", "Light Painting", "Macro Photography", "Astrophotography", "Experimental Photography"]
              },
              {
                "group": "Fashion & Model",
                "items": ["Fashion Photography", "Model Photoshoot", "Beauty Shoot", "Studio Photography", "Lookbook"]
              },
              {
                "group": "Architecture & Interior",
                "items": ["Architecture Photography", "Interior Photography", "Real Estate Photography", "Property Photography"]
              }
            ]
          },
          "multimedia-production-service": {
            "title": "Multimedia & Production Service",
            "desc": "Layanan produksi live streaming event, jasa editing & color grading, penulisan naskah, serta manajemen konten digital.",
            "columns": [
              {
                "group": "Livestreaming",
                "items": ["Live Streaming", "Webinar Streaming", "Hybrid Event", "Virtual Event", "Multicam Production"]
              },
              {
                "group": "Production Service",
                "items": ["Video Editing", "Color Grading", "Audio Production", "Camera Operator", "Event Production", "LED Videotron"]
              },
              {
                "group": "Creative Service",
                "items": ["Scriptwriting", "Creative Concept", "Storyboard", "Production Team", "Content Planning"]
              },
              {
                "group": "Digital Production",
                "items": ["YouTube Production", "Social Media Management", "Content Management", "Branding Content"]
              }
            ]
          },
          "lainnya": {
            "title": "Lainnya",
            "desc": "Kategori alternatif untuk bidang kreatif khusus yang tidak tercantum dalam 10 kategori utama.",
            "columns": [
              {
                "group": "Bidang Niche",
                "items": ["Konsultasi Kreatif", "Penerjemahan Bahasa", "Asisten Virtual Kreatif", "Pengeditan Naskah", "Layanan Khusus Lainnya"]
              }
            ]
          }
        };

        const type = '<?php echo esc_js($type); ?>';
        const inputId = '<?php echo esc_js($input_id); ?>';
        const detailBox = $('#lx-cat-detail-box-' + type);
        const titleEl = $('#lx-cat-title-' + type);
        const descEl = $('#lx-cat-desc-' + type);
        const colsEl = $('#lx-cat-columns-' + type);

        // Click handler for category sub-navbar
        $('.lx-cat-nav button').on('click', function() {
            const btn = $(this);
            const parent = btn.closest('.lx-cat-nav');
            
            // Toggle active button
            parent.find('.lx-cat-btn').removeClass('active');
            btn.addClass('active');

            const slug = btn.data('slug');
            $('#' + inputId).val(slug);

            // Update detail box content dynamically
            const data = categoriesData[slug];
            if (data && slug !== 'semua') {
                titleEl.text(data.title);
                descEl.text(data.desc);
                
                // Build columns HTML
                let colsHtml = '';
                data.columns.forEach(col => {
                    colsHtml += `<div class="lx-cat-group">`;
                    colsHtml += `<div class="lx-cat-group-name">${col.group}</div>`;
                    colsHtml += `<ul class="lx-cat-group-list">`;
                    col.items.forEach(item => {
                        colsHtml += `<li>${item}</li>`;
                    });
                    colsHtml += `</ul>`;
                    colsHtml += `</div>`;
                });
                colsEl.html(colsHtml);
                
                // Fade in detail box
                detailBox.slideDown(350);
            } else {
                detailBox.slideUp(300);
            }

            // Trigger grid update
            if (type === 'video') {
                if (typeof window.jalankanCariVideo === 'function') {
                    window.jalankanCariVideo(false);
                } else if (typeof jalankanCariVideo === 'function') {
                    jalankanCariVideo(false);
                }
            } else {
                if (typeof window.getPorto === 'function') {
                    window.getPorto(true);
                } else if (typeof getPorto === 'function') {
                    getPorto(true);
                }
            }
        });
    });
    </script>
    <?php
}
