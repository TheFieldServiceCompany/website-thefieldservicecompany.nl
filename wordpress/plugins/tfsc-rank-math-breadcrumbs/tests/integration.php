<?php
/**
 * Uses real WordPress hooks, Rank Math renderer and TFSC snippets.
 * Elementor controls and the WordPress query are fixtures.
 */

namespace Elementor {
    class Controls_Manager {
        const CHOOSE = 'choose';
        const COLOR = 'color';
        const SELECT = 'select';
        const TAB_STYLE = 'style';
    }

    class Group_Control_Typography {
        public static function get_type() { return 'typography'; }
    }

    class Widget_Base {
        public $controls = [];
        public $settings = [];
        public function get_settings( $key ) { return $this->settings[$key] ?? null; }
        public function start_controls_section( $name, $settings ) {}
        public function end_controls_section() {}
        public function add_control( $name, $settings ) { $this->controls[$name] = $settings; }
        public function add_responsive_control( $name, $settings ) { $this->controls[$name] = $settings; }
        public function add_group_control( $type, $settings ) { $this->controls[$settings['name']] = $settings; }
        public function render_for_test() { ob_start(); $this->render(); return ob_get_clean(); }
        public function controls_for_test() { $this->register_controls(); return $this->controls; }
        public function dynamic_for_test() { return $this->is_dynamic_content(); }
    }
}

namespace Elementor\Core\Kits\Documents\Tabs {
    class Global_Typography {
        const TYPOGRAPHY_SECONDARY = 'secondary';
    }
}

namespace RankMath {
    class Helper {
        public static $enabled = true;
        public static function is_breadcrumbs_enabled() { return self::$enabled; }
        public static function get_settings( $key, $default = false ) {
            return [
                'general.breadcrumbs_home' => true,
                'general.breadcrumbs_separator' => '›',
                'general.breadcrumbs_home_label' => 'Home',
                'general.breadcrumbs_home_link' => 'https://example.test/',
            ][$key] ?? $default;
        }
        public static function get_post_meta( $key, $id ) { return ''; }
        public static function get_term_meta( $key, $id ) { return ''; }
    }
}

namespace {
    error_reporting( E_ALL );
    set_error_handler( function( $level, $message ) { throw new \Exception( $message ); } );
    define( 'ABSPATH', '/' );
    define( 'TFSC_COMBO_BASE', 'vacatures' );
    require '/core/plugin.php';
    require '/core/formatting.php';
    require '/core/kses.php';

    function get_option( $key ) { return $key === 'blog_charset' ? 'UTF-8' : false; }
    function wp_allowed_protocols() { return [ 'http', 'https' ]; }
    function is_utf8_charset( $charset = null ) { return true; }
    function _canonical_charset( $charset ) { return 'UTF-8'; }
    function esc_html__( $text, $domain = '' ) { return esc_html( $text ); }
    function wp_parse_args( $args, $defaults = [] ) { return array_merge( $defaults, $args ); }
    function home_url( $path = '' ) { return 'https://example.test/' . ltrim( $path, '/' ); }
    function wp_parse_url( $url, $component = -1 ) { return parse_url( $url, $component ); }
    function is_singular() { return $GLOBALS['context'] === 'article'; }
    function is_single() { return is_singular(); }
    function is_page() { return false; }
    function is_post_type_archive() { return $GLOBALS['context'] === 'archive'; }
    function is_tax() { return $GLOBALS['context'] === 'term'; }
    function is_front_page() { return false; }
    function is_paged() { return false; }
    function get_post_type( $post = null ) { return 'beroepengids'; }
    function get_post_type_object( $type ) { return (object) [ 'has_archive' => 'beroepengids/artikelen', 'labels' => (object) [ 'name' => 'Artikelen' ] ]; }
    function get_post_type_archive_link( $type ) { return home_url( '/beroepengids/artikelen/' ); }
    function get_query_var( $key, $default = '' ) { return $GLOBALS['query_vars'][$key] ?? $default; }
    function taxonomy_exists( $taxonomy ) { return $taxonomy === 'beroep_functienaam'; }
    function get_taxonomy( $taxonomy ) { return (object) [ 'rewrite' => [ 'slug' => 'beroepengids/functienamen' ], 'labels' => (object) [ 'name' => 'Functienamen' ] ]; }
    function get_queried_object() { return $GLOBALS['term']; }
    function get_the_title( $post = null ) { return 'Field Service Engineer'; }
    function get_permalink( $post = null ) { return home_url( '/beroepengids/artikel/field-service-engineer/' ); }
    function get_term_link( $term ) { return home_url( '/beroepengids/functienamen/' . $term->slug . '/' ); }
    function is_taxonomy_hierarchical( $taxonomy ) { return true; }
    function tfsc_is_combo_archive() { return $GLOBALS['context'] === 'combo'; }
    function tfsc_get_combo_archive() { return $GLOBALS['combo']; }
    class WP_Term {
        public $taxonomy = 'beroep_functienaam';
        public $slug = 'field-service-engineer';
        public $name = 'Field Service Engineer';
        public $parent = 0;
        public $term_id = 1;
    }
    $term = new WP_Term();
    $post = (object) [ 'ID' => 1, 'post_parent' => 0 ];
    $wp_query = new class {
        public function get_queried_object() { return $GLOBALS['term']; }
        public function get( $key ) { return get_query_var( $key ); }
    };
    $context = 'base';
    $query_vars = [];
    $checks = [];
    function check( $condition, $name ) {
        if ( ! $condition ) { throw new \Exception( 'FAIL: ' . $name ); }
        $GLOBALS['checks'][] = $name;
    }
    require '/rank-math/class-hooker.php';
    require '/rank-math/class-breadcrumbs.php';
    require '/rank-math/template-tags.php';
    require '/snippets/99.php';
    require '/snippets/199.php';
    require '/plugin/tfsc-rank-math-breadcrumbs.php';

    $manager = new class {
        public $widget;
        public function register( $widget ) { $this->widget = $widget; }
    };
    do_action( 'elementor/widgets/register', $manager );
    $widget = $manager->widget;
    check( $widget->get_name() === 'breadcrumbs', 'Stored widget type remains breadcrumbs' );
    check( $widget->dynamic_for_test(), 'Shared widget output is dynamic per page' );
    $controls = $widget->controls_for_test();
    check( $controls['typography']['selector'] === '{{WRAPPER}}', 'Saved typography keeps its wrapper selector' );
    check( $controls['text_color']['selectors']['{{WRAPPER}}'] === 'color: {{VALUE}};', 'Stored text colors use the original selector' );
    check( $controls['link_color']['selectors']['{{WRAPPER}} a'] === 'color: {{VALUE}};', 'Stored link colors use the original selector' );
    check( $controls['typography']['global']['default'] === 'secondary', 'Default global typography matches the installed Pro widget' );
    check( $controls['align']['prefix_class'] === 'elementor%s-align-', 'Responsive alignment retains the original CSS classes' );

    foreach ( [ 'base', 'term', 'article', 'archive', 'combo' ] as $context ) {
        $query_vars = [ 'post_type' => 'beroepengids' ];
        if ( $context === 'base' ) { $query_vars['tfsc_taxonomy_archive'] = 'beroep_functienaam'; }
        $combo = [ [ 'taxonomy' => 'functienaam', 'term' => $term ] ];
        if ( $context === 'combo' ) {
            $sector = clone $term;
            $sector->name = 'Energie';
            $sector->slug = 'energie';
            $combo[] = [ 'taxonomy' => 'bedrijfssector', 'term' => $sector ];
        }
        ob_start(); rank_math_the_breadcrumbs(); $expected = ob_get_clean();
        $html = $widget->render_for_test();
        check( $html === $expected, $context . ' widget matches real Rank Math Free output byte for byte' );
        check( strpos( $html, '<nav aria-label="breadcrumbs" class="rank-math-breadcrumb"><p>' ) === 0, $context . ' preserves existing navigation markup and CSS class' );
        if ( $context === 'base' || $context === 'article' || $context === 'archive' ) {
            check( strpos( $html, 'href="https://example.test/beroepengids/"' ) !== false, $context . ' retains the Beroepengids parent link' );
        }
        if ( $context === 'term' ) {
            check( strpos( $html, 'href="https://example.test/beroepengids/functienamen/"' ) !== false, 'Detail archive links back to its virtual base archive through snippet 199' );
        }
        if ( $context === 'combo' ) {
            check( strpos( $html, 'href="https://example.test/vacatures/functienaam/field-service-engineer/"' ) !== false, 'Combo archive links back to its first dimension through snippet 99' );
            check( strpos( $html, '<span class="last">Energie</span>' ) !== false, 'Combo current dimension remains the final crumb' );
        }
    }

    $context = 'base';
    $query_vars = [ 'tfsc_taxonomy_archive' => 'beroep_functienaam' ];
    $widget->settings['html_tag'] = 'div';
    check( strpos( $widget->render_for_test(), '<div>' ) === 0, 'Stored alternate HTML wrapper is supported' );
    $widget->settings['html_tag'] = 'script';
    check( strpos( $widget->render_for_test(), '<nav aria-label="breadcrumbs"' ) === 0, 'Unrecognized HTML tag falls back to safe Rank Math markup' );
    $widget->settings = [];
    \RankMath\Helper::$enabled = false;
    check( $widget->render_for_test() === '', 'Disabled breadcrumb setting is respected' );
    define( 'RANK_MATH_PRO_VERSION', 'fixture' );
    $manager->widget = null;
    do_action( 'elementor/widgets/register', $manager );
    check( $manager->widget === null, 'Active Rank Math Pro retains ownership of its widget' );
    echo json_encode( [ 'php' => PHP_VERSION, 'passed' => count( $checks ), 'checks' => $checks ], JSON_PRETTY_PRINT );
}
