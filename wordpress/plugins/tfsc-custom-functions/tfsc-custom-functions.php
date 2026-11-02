<?php
/**
 * Plugin Name: TFSC Custom Functions Plugin
 * Description: TFSC frontend styles, local fonts, search titles and compatibility shortcodes.
 * Version: 0.5.0
 * Author: The Field Service Company
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'TFSC_CUSTOM_FUNCTIONS_VERSION', '0.5.0' );

define( 'TFSC_APPLICATION_FORM_PAGE_ID', 21465 );
define( 'TFSC_LEGACY_VACANCY_TRANSFER_WIDGET_ID', '62cd538' );

// Fonts are hosted locally; Elementor must not request Google Fonts remotely.
add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );

// Elementor decides which icon libraries a widget requires.

// Native block styles are left to WordPress: articles contain tables and images.

add_filter( 'elementor/utils/get_the_archive_title', 'tfsc_custom_functions_archive_title' );
function tfsc_custom_functions_archive_title( $title ) {
    if ( is_search() ) {
        return 'Jouw zoekresultaten voor:<span class="tfsc-search-query"> ' . esc_html( get_search_query() ) . '</span>';
    }
    return $title;
}

add_action( 'wp_enqueue_scripts', 'tfsc_custom_functions_enqueue_styles', PHP_INT_MAX );
function tfsc_custom_functions_enqueue_styles() {
    $filename = 'tfsc-custom-stylesheet.css';
    $path     = plugin_dir_path( __FILE__ ) . $filename;

    if ( ! is_file( $path ) || ! is_readable( $path ) ) {
        return;
    }

    $modified = filemtime( $path );
    $version  = false !== $modified ? (string) $modified : TFSC_CUSTOM_FUNCTIONS_VERSION;

    // Preserve this handle and priority for existing theme/Elementor overrides.
    wp_enqueue_style( 'theme-override', plugin_dir_url( __FILE__ ) . $filename, array(), $version, 'all' );
}

// Font faces live in CSS; avoid preloading a second, fixed font configuration.

// Keep the public shortcode name: one saved template still references it.
add_shortcode( 'rm_get_estimated_time_to_read', 'tfsc_custom_functions_reading_time' );
function tfsc_custom_functions_reading_time() {
    $post = get_post();
    if ( ! $post instanceof WP_Post ) {
        return '';
    }

    $text = wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
    $text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
    $words = preg_match_all( '/[\p{L}\p{N}]+(?:[\x{2019}\x{0027}-][\p{L}\p{N}]+)*/u', $text );
    if ( false === $words ) {
        $words = str_word_count( $text );
    }

    if ( $words < 200 ) {
        return 'Minder dan 1 minuut.';
    }
    $minutes = max( 1, (int) round( $words / 200 ) );
    return $minutes . ( 1 === $minutes ? ' minuut' : ' minuten' );
}

// No current content reference found. Retained for external/backward compatibility.
add_shortcode( 'current_page_url', 'tfsc_custom_functions_current_page_url' );
function tfsc_custom_functions_current_page_url() {
    global $wp;
    $request = isset( $wp->request ) ? $wp->request : '';
    return esc_url( home_url( $request ) );
}

/**
 * Read the business vacancy number from the stored vacancy field.
 *
 * The number is deliberately not inferred from rendered labels, titles or
 * URLs. Pods may use custom storage, so its API is the first source. The
 * post-meta fallback preserves compatibility with the existing Elementor
 * Post Custom Field tag.
 */
function tfsc_custom_functions_vacancy_number( $post_id = 0 ) {
    $post_id = $post_id ? (int) $post_id : (int) get_queried_object_id();

    if ( ! $post_id || 'vacature' !== get_post_type( $post_id ) ) {
        return '';
    }

    $number = '';

    if ( function_exists( 'pods' ) ) {
        $pod = pods( 'vacature', $post_id );
        if ( $pod && method_exists( $pod, 'exists' ) && $pod->exists() ) {
            $number = $pod->field( 'vacaturenummer' );
        }
    }

    if ( is_array( $number ) || is_object( $number ) || '' === trim( (string) $number ) ) {
        $number = get_post_meta( $post_id, 'vacaturenummer', true );
    }

    $number = trim( (string) $number );

    return preg_match( '/^[0-9]{1,12}$/', $number ) ? $number : '';
}

/**
 * The old Elementor HTML widget scraped visible labels and concatenated five
 * unencoded query parameters. Its application and vacancy-question behavior
 * is replaced below by one server-backed context handler.
 */
add_filter( 'elementor/widget/render_content', 'tfsc_custom_functions_remove_legacy_vacancy_transfer', 10, 2 );
function tfsc_custom_functions_remove_legacy_vacancy_transfer( $content, $widget ) {
    if ( ! is_singular( 'vacature' ) || ! is_object( $widget ) || ! method_exists( $widget, 'get_id' ) ) {
        return $content;
    }

    if ( TFSC_LEGACY_VACANCY_TRANSFER_WIDGET_ID === (string) $widget->get_id() ) {
        return '';
    }

    return $content;
}

/**
 * Accept the vacancy context only from the dedicated application button POST.
 * Elementor's existing hidden field currently reads the `vacaturenummer`
 * request parameter as GET; promoting the validated POST value server-side
 * keeps that form configuration compatible while the browser URL stays clean.
 */
add_action( 'template_redirect', 'tfsc_custom_functions_receive_application_context', 0 );
function tfsc_custom_functions_receive_application_context() {
    if (
        'POST' !== strtoupper( isset( $_SERVER['REQUEST_METHOD'] ) ? (string) $_SERVER['REQUEST_METHOD'] : '' ) ||
        empty( $_POST['tfsc_vacancy_application'] ) ||
        ! is_page( TFSC_APPLICATION_FORM_PAGE_ID )
    ) {
        return;
    }

    $number = isset( $_POST['vacaturenummer'] ) && is_scalar( $_POST['vacaturenummer'] )
        ? trim( (string) wp_unslash( $_POST['vacaturenummer'] ) )
        : '';

    if ( ! preg_match( '/^[0-9]{1,12}$/', $number ) ) {
        status_header( 400 );
        wp_die(
            esc_html__( 'Deze vacature kon niet veilig aan het sollicitatieformulier worden gekoppeld. Ga terug naar de vacature en probeer het opnieuw.', 'tfsc' ),
            esc_html__( 'Vacaturekoppeling ontbreekt', 'tfsc' ),
            array( 'response' => 400 )
        );
    }

    $_GET['vacaturenummer']     = $number;
    $_REQUEST['vacaturenummer'] = $number;
}

/**
 * Render one hidden POST form and one click handler for every application CTA
 * on a single vacancy page. Existing data-tfsc-* attributes stay on the real
 * buttons, so GTM can keep attributing the click location.
 */
add_action( 'wp_footer', 'tfsc_custom_functions_render_vacancy_context', 5 );
function tfsc_custom_functions_render_vacancy_context() {
    if ( ! is_singular( 'vacature' ) ) {
        return;
    }

    $number       = tfsc_custom_functions_vacancy_number();
    $apply_url    = home_url( '/formulier-solliciteren/' );
    $question_url = home_url( '/formulier-vraag/' );
    ?>
    <form id="tfsc-vacancy-application-context" method="post" action="<?php echo esc_url( $apply_url ); ?>" hidden>
        <input type="hidden" name="tfsc_vacancy_application" value="1">
        <input type="hidden" name="vacaturenummer" value="<?php echo esc_attr( $number ); ?>">
    </form>
    <script id="tfsc-vacancy-context-handler">
    (() => {
        const vacancyNumber = <?php echo wp_json_encode( $number ); ?>;
        const applicationForm = document.getElementById('tfsc-vacancy-application-context');
        const questionUrl = <?php echo wp_json_encode( $question_url ); ?>;

        const applicationSelector = '[data-tfsc-cta-action="apply"], .button-solliciteren a, a.button-solliciteren';
        const questionSelector = '.button-vac-email a, a.button-vac-email';

        document.addEventListener('click', (event) => {
            if (!(event.target instanceof Element)) {
                return;
            }

            const applicationButton = event.target.closest(applicationSelector);
            const questionButton = event.target.closest(questionSelector);

            if (!applicationButton && !questionButton) {
                return;
            }

            event.preventDefault();

            if (!vacancyNumber) {
                window.alert('Deze vacature mist een geldig vacaturenummer. De verzending is gestopt om een verkeerde koppeling te voorkomen.');
                return;
            }

            // De event-loop blijft vrij zodat bestaande GTM-clicklisteners de
            // oorspronkelijke knop en data-tfsc-* attributen kunnen registreren.
            window.setTimeout(() => {
                if (applicationButton) {
                    applicationForm.submit();
                    return;
                }

                const url = new URL(questionUrl, window.location.origin);
                url.searchParams.set('vacaturenummer', vacancyNumber);
                window.location.assign(url.toString());
            }, 0);
        });
    })();
    </script>
    <?php
}
