<?php
/**
 * Plugin Name: TFSC Mediafilters
 * Description: Filter de mediabibliotheek op extensie, pixelafmetingen, bestandsgrootte, oriëntatie en alt-tekst, ook binnen HappyFiles-mappen.
 * Version: 1.0.0
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * Author: The Field Service Company
 * License: GPL-2.0-or-later
 * Text Domain: tfsc-media-filters
 */

defined( 'ABSPATH' ) || exit;

final class TFSC_Media_Filters {
	const VERSION = '1.0.0';
	const STATE = 'tfsc_mf_index_state';
	const SCHEMA = 'tfsc_mf_schema';

	public function __construct() {
		add_action( 'admin_init', array( $this, 'maybe_install' ) );
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'admin_notices', array( $this, 'notice' ) );
		add_action( 'restrict_manage_posts', array( $this, 'list_controls' ), 20, 2 );
		add_action( 'pre_get_posts', array( $this, 'list_query' ), 99 );
		add_filter( 'ajax_query_attachments_args', array( $this, 'ajax_query' ), 99 );
		add_filter( 'posts_where', array( $this, 'where' ), 99, 2 );
		add_action( 'add_attachment', array( $this, 'sync' ) );
		add_action( 'edit_attachment', array( $this, 'sync' ) );
		foreach ( array( 'added_post_meta', 'updated_post_meta', 'deleted_post_meta' ) as $hook ) {
			add_action( $hook, array( $this, 'meta_changed' ), 10, 4 );
		}
		// Run after metadata deletion, whose hooks could otherwise reinsert the row.
		add_action( 'deleted_post', array( $this, 'remove' ) );
		add_action( 'wp_ajax_tfsc_mf_index', array( $this, 'index_ajax' ) );
	}

	public static function table() {
		global $wpdb;
		return $wpdb->prefix . 'tfsc_media_index';
	}

	public static function activate( $network_wide = false ) {
		// Schema is per site; other sites initialize lazily in their own admin.
		self::install();
	}

	public function maybe_install() {
		if ( current_user_can( 'manage_options' ) && self::VERSION !== get_option( self::SCHEMA ) ) {
			self::install();
		}
	}

	public static function install() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$table = self::table();
		dbDelta( "CREATE TABLE $table (
			attachment_id bigint(20) unsigned NOT NULL,
			extension varchar(20) NOT NULL DEFAULT '',
			width int(10) unsigned DEFAULT NULL,
			height int(10) unsigned DEFAULT NULL,
			filesize bigint(20) unsigned DEFAULT NULL,
			orientation varchar(10) NOT NULL DEFAULT '',
			PRIMARY KEY  (attachment_id),
			KEY extension (extension),
			KEY width (width),
			KEY height (height),
			KEY filesize (filesize),
			KEY orientation (orientation)
		) " . $wpdb->get_charset_collate() . ';' );
		// Do not mark a failed schema installation as successful.
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) ) !== $table ) {
			return;
		}
		update_option( self::SCHEMA, self::VERSION, false );
		self::reset_index();
	}

	public static function reset_index() {
		global $wpdb;
		$max = (int) $wpdb->get_var( "SELECT MAX(ID) FROM {$wpdb->posts} WHERE post_type = 'attachment'" );
		$total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'attachment' AND ID <= $max" );
		$state = array( 'cursor' => 0, 'max' => $max, 'total' => $total, 'processed' => 0, 'ready' => 0 === $total );
		update_option( self::STATE, $state, false );
		return $state;
	}

	public static function ready() {
		$state = get_option( self::STATE, array() );
		return ! empty( $state['ready'] );
	}

	public function meta_changed( $meta_id, $post_id, $key, $value ) {
		if ( in_array( $key, array( '_wp_attached_file', '_wp_attachment_metadata' ), true ) ) {
			$this->sync( $post_id );
		}
	}

	public function sync( $id ) {
		global $wpdb;
		if ( 'attachment' !== get_post_type( $id ) || self::VERSION !== get_option( self::SCHEMA ) ) {
			return true;
		}
		$meta = wp_get_attachment_metadata( $id, true );
		$meta = is_array( $meta ) ? $meta : array();
		$relative = (string) get_post_meta( $id, '_wp_attached_file', true );
		$extension = strtolower( pathinfo( $relative, PATHINFO_EXTENSION ) );
		$extension = preg_match( '/^[a-z0-9]{1,20}$/D', $extension ) ? $extension : '';
		$width = ! empty( $meta['width'] ) ? absint( $meta['width'] ) : null;
		$height = ! empty( $meta['height'] ) ? absint( $meta['height'] ) : null;
		$size = isset( $meta['filesize'] ) && is_numeric( $meta['filesize'] ) ? max( 0, (int) $meta['filesize'] ) : null;
		// Never fetch remote files. Offload plugins can provide filesize in metadata.
		if ( null === $size ) {
			$file = get_attached_file( $id, true );
			if ( $file && ! wp_is_stream( $file ) && is_file( $file ) && is_readable( $file ) ) {
				$bytes = wp_filesize( $file );
				$size = false === $bytes ? null : $bytes;
			}
		}
		$orientation = '';
		if ( $width && $height ) {
			$orientation = $width === $height ? 'square' : ( $width > $height ? 'landscape' : 'portrait' );
		}
		$result = $wpdb->replace( self::table(), array(
			'attachment_id' => (int) $id,
			'extension' => $extension,
			'width' => $width,
			'height' => $height,
			'filesize' => $size,
			'orientation' => $orientation,
		), array( '%d', '%s', '%d', '%d', '%d', '%s' ) );
		// Invalidate cached attachment queries after indexed values change.
		if ( false !== $result ) {
			clean_post_cache( $id );
		}
		return false !== $result;
	}

	public function remove( $id ) {
		global $wpdb;
		if ( self::VERSION === get_option( self::SCHEMA ) ) {
			$wpdb->delete( self::table(), array( 'attachment_id' => $id ), array( '%d' ) );
		}
	}

	public function index_batch( $state ) {
		global $wpdb;
		$ids = $wpdb->get_col( $wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'attachment' AND ID > %d AND ID <= %d ORDER BY ID ASC LIMIT 100",
			$state['cursor'], $state['max']
		) );
		if ( $wpdb->last_error ) {
			return new WP_Error( 'index_read', 'De mediabibliotheek kon niet worden gelezen.' );
		}
		foreach ( $ids as $id ) {
			if ( ! $this->sync( $id ) ) {
				return new WP_Error( 'index_write', 'De media-index kon niet worden opgeslagen. Controleer de databaserechten.' );
			}
			$state['cursor'] = (int) $id;
			++$state['processed'];
		}
		$state['ready'] = count( $ids ) < 100 || $state['cursor'] >= $state['max'];
		update_option( self::STATE, $state, false );
		return $state;
	}

	public function index_ajax() {
		check_ajax_referer( 'tfsc_mf_index', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'Geen toegang.' ), 403 );
		}
		// Atomic option lock prevents two browser tabs from racing the cursor.
		if ( ! add_option( 'tfsc_mf_index_lock', time(), '', false ) ) {
			if ( (int) get_option( 'tfsc_mf_index_lock' ) < time() - 120 ) {
				delete_option( 'tfsc_mf_index_lock' );
			}
			wp_send_json_error( array( 'message' => 'Er draait al een indexronde. Probeer het over enkele seconden opnieuw.' ), 409 );
		}
		try {
			if ( self::VERSION !== get_option( self::SCHEMA ) ) {
				throw new RuntimeException( 'De indextabel ontbreekt. Deactiveer en activeer de plugin opnieuw.' );
			}
			$state = ! empty( $_POST['restart'] ) ? self::reset_index() : get_option( self::STATE, array() );
			if ( ! isset( $state['max'] ) ) {
				$state = self::reset_index();
			}
			if ( empty( $state['ready'] ) ) {
				$state = $this->index_batch( $state );
			}
		} catch ( Throwable $e ) {
			$state = new WP_Error( 'index_exception', 'Indexeren is onderbroken. Hervat de index of controleer het WordPress-foutenlog.' );
		} finally {
			delete_option( 'tfsc_mf_index_lock' );
		}
		if ( is_wp_error( $state ) ) {
			wp_send_json_error( array( 'message' => $state->get_error_message() ), 500 );
		}
		wp_send_json_success( $state );
	}

	public static function sanitize( $input ) {
		if ( ! is_array( $input ) ) {
			return array();
		}
		$out = array();
		if ( isset( $input['extension'] ) && is_string( $input['extension'] ) ) {
			$parts = preg_split( '/[\s,;]+/', strtolower( substr( $input['extension'], 0, 500 ) ) );
			$parts = array_map( function ( $part ) { return ltrim( $part, '.' ); }, $parts );
			$parts = array_filter( $parts, function ( $part ) { return (bool) preg_match( '/^[a-z0-9]{1,20}$/D', $part ); } );
			if ( $parts ) {
				$out['extension'] = implode( ',', array_slice( array_unique( $parts ), 0, 20 ) );
			}
		}
		foreach ( array( 'width_min', 'width_max', 'height_min', 'height_max', 'size_min', 'size_max' ) as $key ) {
			if ( ! isset( $input[ $key ] ) || ! is_scalar( $input[ $key ] ) ) {
				continue;
			}
			$value = str_replace( ',', '.', trim( (string) $input[ $key ] ) );
			if ( '' !== $value && is_numeric( $value ) && is_finite( (float) $value ) && (float) $value >= 0 ) {
				$is_size = 0 === strpos( $key, 'size_' );
				$out[ $key ] = $is_size ? min( 1000000000, (float) $value ) : (int) min( 1000000, (float) $value );
			}
		}
		foreach ( array( 'orientation' => array( 'landscape', 'portrait', 'square' ), 'alt' => array( 'missing', 'present' ) ) as $key => $allowed ) {
			if ( isset( $input[ $key ] ) && is_string( $input[ $key ] ) && in_array( $input[ $key ], $allowed, true ) ) {
				$out[ $key ] = $input[ $key ];
			}
		}
		return $out;
	}

	public function ajax_query( $args ) {
		if ( is_admin() && current_user_can( 'upload_files' ) && isset( $_REQUEST['query']['tfsc_mf'] ) ) {
			$args['tfsc_mf_filters'] = self::sanitize( wp_unslash( $_REQUEST['query']['tfsc_mf'] ) );
		}
		return $args;
	}

	public function list_query( $query ) {
		global $pagenow;
		if ( is_admin() && 'upload.php' === $pagenow && $query->is_main_query() && 'attachment' === $query->get( 'post_type' ) && current_user_can( 'upload_files' ) && isset( $_GET['tfsc_mf'] ) ) {
			$query->set( 'tfsc_mf_filters', self::sanitize( wp_unslash( $_GET['tfsc_mf'] ) ) );
		}
	}

	public function where( $where, $query ) {
		global $wpdb;
		$filters = $query->get( 'tfsc_mf_filters' );
		if ( ! is_admin() || ! current_user_can( 'upload_files' ) || 'attachment' !== $query->get( 'post_type' ) || ! is_array( $filters ) || ! $filters ) {
			return $where;
		}
		if ( ! self::ready() ) {
			// Never present a partially indexed library as complete filtered results.
			return $where . ' AND 1=0';
		}
		$filters = self::sanitize( $filters );
		$clauses = array();
		if ( ! empty( $filters['extension'] ) ) {
			$extensions = explode( ',', $filters['extension'] );
			$placeholders = implode( ',', array_fill( 0, count( $extensions ), '%s' ) );
			$clauses[] = $wpdb->prepare( "mf.extension IN ($placeholders)", $extensions );
		}
		foreach ( array( 'width' => 'width', 'height' => 'height', 'size' => 'filesize' ) as $key => $column ) {
			foreach ( array( 'min' => '>=', 'max' => '<=' ) as $bound => $operator ) {
				if ( isset( $filters[ $key . '_' . $bound ] ) ) {
					$value = $filters[ $key . '_' . $bound ];
					if ( 'size' === $key ) {
						$value *= 1024;
					}
					$value = 'min' === $bound ? ceil( $value ) : floor( $value );
					$clauses[] = $wpdb->prepare( "mf.$column $operator %d", $value );
				}
			}
		}
		if ( ! empty( $filters['orientation'] ) ) {
			$clauses[] = $wpdb->prepare( 'mf.orientation = %s', $filters['orientation'] );
		}
		if ( $clauses ) {
			$table = self::table();
			$where .= " AND EXISTS (SELECT 1 FROM $table mf WHERE mf.attachment_id = {$wpdb->posts}.ID AND " . implode( ' AND ', $clauses ) . ')';
		}
		if ( ! empty( $filters['alt'] ) ) {
			$exists = 'present' === $filters['alt'] ? 'EXISTS' : 'NOT EXISTS';
			$where .= " AND {$wpdb->posts}.post_mime_type LIKE 'image/%' AND $exists (SELECT 1 FROM {$wpdb->postmeta} mf_alt WHERE mf_alt.post_id = {$wpdb->posts}.ID AND mf_alt.meta_key = '_wp_attachment_image_alt' AND TRIM(mf_alt.meta_value) <> '')";
		}
		return $where;
	}

	public function menu() {
		add_media_page( 'Mediafilters', 'Mediafilters', 'manage_options', 'tfsc-media-filters', array( $this, 'settings_page' ) );
	}

	public function notice() {
		global $pagenow;
		if ( 'upload.php' !== $pagenow || self::ready() ) {
			return;
		}
		echo '<div class="notice notice-warning"><p>TFSC Mediafilters: de media-index is nog niet klaar. ';
		if ( current_user_can( 'manage_options' ) ) {
			echo '<a href="' . esc_url( admin_url( 'upload.php?page=tfsc-media-filters' ) ) . '">Maak de index aan om de extra filters te gebruiken.</a>';
		} else {
			echo 'Vraag een beheerder om de media-index aan te maken.';
		}
		echo '</p></div>';
	}

	public function settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$state = get_option( self::STATE, array() );
		echo '<div class="wrap"><h1>TFSC Mediafilters</h1><p>Eenmalig indexeren maakt filteren op bestaande media mogelijk. Nieuwe uploads en wijzigingen worden daarna automatisch bijgewerkt.</p>';
		echo '<p>De index leest bestandseigenschappen en wijzigt geen mediabestanden, mappen of bestandsadressen. Laat dit scherm open tijdens het indexeren; je kunt een onderbroken ronde later hervatten.</p>';
		echo '<p id="tfsc-mf-index-status" role="status" aria-live="polite">' . esc_html( ! empty( $state['ready'] ) ? 'De index is klaar.' : 'De index is nog niet klaar.' ) . '</p>';
		echo '<p><button type="button" class="button button-primary" id="tfsc-mf-index-start">' . esc_html( ! empty( $state['ready'] ) ? 'Index opnieuw opbouwen' : 'Index starten / hervatten' ) . '</button></p>';
		echo '<p><a href="' . esc_url( admin_url( 'upload.php' ) ) . '">Naar de mediabibliotheek</a></p>';
		echo '<h2>Beschikbare filters</h2><ul><li>Extensies, zoals jpg, jpeg, png, webp, avif, svg en pdf (meerdere met komma’s).</li><li>Minimum en maximum breedte en hoogte in pixels. Gebruik hetzelfde minimum en maximum voor een exacte maat.</li><li>Minimum en maximum bestandsgrootte in KB (1 KB = 1024 bytes).</li><li>Liggend, staand of exact vierkant.</li><li>Afbeeldingen met of zonder alt-tekst.</li></ul>';
		echo '<p>Afmetingen gelden voor het volledige, door WordPress beheerde bestand, niet voor gegenereerde thumbnails. Onbekende afmetingen en bestandsgroottes doen niet mee aan dat filter. Bij extern opgeslagen bestanden is bestandsgrootte alleen beschikbaar als deze in WordPress-metadata staat.</p>';
		echo '<h2>HappyFiles</h2><p>Kies eerst een HappyFiles-map en klik daarna op Extra filters. Alle voorwaarden worden gecombineerd met de bestaande mapselectie, zoekterm, datum en bestandstype. Reset wist alleen de TFSC-filters. Of submappen meetellen, bepaalt HappyFiles.</p></div>';
	}

	public static function controls( $values = array(), $named = true ) {
		ob_start();
		$name = function ( $key ) use ( $named ) { return $named ? ' name="tfsc_mf[' . esc_attr( $key ) . ']"' : ''; };
		$value = function ( $key ) use ( $values ) { return isset( $values[ $key ] ) ? esc_attr( $values[ $key ] ) : ''; };
		?>
		<div class="tfsc-mf-fields">
			<label>Extensies <input type="text" data-mf="extension"<?php echo $name( 'extension' ); ?> value="<?php echo $value( 'extension' ); ?>" placeholder="jpg, webp, pdf" maxlength="500"></label>
			<?php foreach ( array( 'width' => 'Breedte (px)', 'height' => 'Hoogte (px)', 'size' => 'Grootte (KB)' ) as $key => $label ) : ?>
				<fieldset><legend><?php echo esc_html( $label ); ?></legend>
				<?php foreach ( array( 'min' => 'Van', 'max' => 'Tot en met' ) as $bound => $text ) : $field = $key . '_' . $bound; ?>
					<label><span class="screen-reader-text"><?php echo esc_html( $label . ' — ' . $text ); ?></span><input type="number" data-mf="<?php echo esc_attr( $field ); ?>"<?php echo $name( $field ); ?> min="0" max="<?php echo 'size' === $key ? '1000000000' : '1000000'; ?>" step="<?php echo 'size' === $key ? 'any' : '1'; ?>" value="<?php echo $value( $field ); ?>" placeholder="<?php echo esc_attr( $text ); ?>"></label>
				<?php endforeach; ?></fieldset>
			<?php endforeach; ?>
			<?php foreach ( array( 'orientation' => array( 'Oriëntatie', array( '' => 'Alle vormen', 'landscape' => 'Liggend', 'portrait' => 'Staand', 'square' => 'Vierkant' ) ), 'alt' => array( 'Alt-tekst', array( '' => 'Alle afbeeldingen', 'missing' => 'Ontbreekt / leeg', 'present' => 'Aanwezig' ) ) ) as $key => $field ) : ?>
				<label><?php echo esc_html( $field[0] ); ?><select data-mf="<?php echo esc_attr( $key ); ?>"<?php echo $name( $key ); ?>>
					<?php foreach ( $field[1] as $option => $label ) : ?><option value="<?php echo esc_attr( $option ); ?>" <?php selected( $value( $key ), $option ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?>
				</select></label>
			<?php endforeach; ?>
		</div>
		<p class="tfsc-mf-hint">Filters gelden samen binnen de gekozen map. Leeg = geen grens. Voor exact: vul dezelfde waarde bij Van en Tot in.</p>
		<p class="tfsc-mf-error" role="alert"></p>
		<div class="tfsc-mf-actions"><button type="<?php echo $named ? 'submit' : 'button'; ?>" class="button button-primary tfsc-mf-apply">Toepassen</button> <button type="button" class="button tfsc-mf-reset">Reset filters</button></div>
		<?php
		return ob_get_clean();
	}

	public function list_controls( $post_type, $which ) {
		if ( 'attachment' !== $post_type || ! in_array( $which, array( 'top', 'bar' ), true ) || ! self::ready() || ! current_user_can( 'upload_files' ) ) {
			return;
		}
		$values = isset( $_GET['tfsc_mf'] ) ? self::sanitize( wp_unslash( $_GET['tfsc_mf'] ) ) : array();
		echo '<details class="tfsc-mf-list"><summary>Extra filters' . ( $values ? ' (actief)' : '' ) . '</summary><div class="tfsc-mf-list-panel">' . self::controls( $values ) . '</div></details>';
	}

	public function assets( $hook ) {
		if ( ! current_user_can( 'upload_files' ) ) {
			return;
		}
		$settings = 'media_page_tfsc-media-filters' === $hook;
		// Hooking media views also supports the native media picker in editors.
		if ( ! $settings && 'upload.php' !== $hook && ! wp_script_is( 'media-views', 'enqueued' ) ) {
			return;
		}
		wp_enqueue_style( 'tfsc-media-filters', plugins_url( 'assets/media-filters.css', __FILE__ ), array(), self::VERSION );
		wp_enqueue_script( 'tfsc-media-filters', plugins_url( 'assets/media-filters.js', __FILE__ ), $settings ? array( 'jquery' ) : array( 'jquery', 'media-views' ), self::VERSION, true );
		wp_localize_script( 'tfsc-media-filters', 'tfscMF', array(
			'ready' => self::ready(),
			'controls' => self::controls( array(), false ),
			'nonce' => current_user_can( 'manage_options' ) ? wp_create_nonce( 'tfsc_mf_index' ) : '',
			'state' => current_user_can( 'manage_options' ) ? get_option( self::STATE, array() ) : array(),
		) );
	}
}

register_activation_hook( __FILE__, array( 'TFSC_Media_Filters', 'activate' ) );
$GLOBALS['tfsc_media_filters'] = new TFSC_Media_Filters();
