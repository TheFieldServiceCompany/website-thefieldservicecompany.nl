<?php

/**
 * Vac Combo Archives | Base Code [v4.5]  werkt 
 */
/**
 * TFSC - Volledig dynamische vacature-archieven
 *
 * - Virtuele combo archives onder /vacatures/...
 * - Onbeperkt aantal taxonomies/terms
 * - Verschillende taxonomies = AND
 * - Meerdere terms binnen taxonomy = OR
 * - kennisgebied = AND
 * - Alleen vac_status=open
 * - Search & Filter Pro v3 sync
 * - Elementor archive condition
 * - [combo_heading]
 * - [combo_term taxonomy="..."]
 */


/**
 * ============================================================
 * CONFIG
 * ============================================================
 */

if ( ! defined( 'TFSC_COMBO_BASE' ) ) {
    define( 'TFSC_COMBO_BASE', 'vacatures' );
}

if ( ! defined( 'TFSC_VACANCY_POST_TYPE' ) ) {
    define( 'TFSC_VACANCY_POST_TYPE', 'vacature' );
}

if ( ! defined( 'TFSC_SEARCH_FILTER_QUERY_ID' ) ) {
    define( 'TFSC_SEARCH_FILTER_QUERY_ID', 1 );
}

if ( ! defined( 'TFSC_STATUS_TAXONOMY' ) ) {
    define( 'TFSC_STATUS_TAXONOMY', 'vac_status' );
}

if ( ! defined( 'TFSC_OPEN_STATUS_SLUG' ) ) {
    define( 'TFSC_OPEN_STATUS_SLUG', 'open' );
}


/**
 * ============================================================
 * HELPERS
 * ============================================================
 */

function tfsc_get_combo_taxonomy_match_method( $taxonomy ) {

    return 'kennisgebied' === $taxonomy
        ? 'AND'
        : 'OR';
}


function tfsc_combo_status_clause() {

    return [
        'taxonomy'         => TFSC_STATUS_TAXONOMY,
        'field'            => 'slug',
        'terms'            => [ TFSC_OPEN_STATUS_SLUG ],
        'operator'         => 'IN',
        'include_children' => false,
    ];
}


function tfsc_combo_taxonomy_allowed( $taxonomy ) {

    if (
        ! $taxonomy ||
        TFSC_STATUS_TAXONOMY === $taxonomy ||
        ! taxonomy_exists( $taxonomy )
    ) {
        return false;
    }

    $object = get_taxonomy( $taxonomy );

    return (
        $object &&
        $object->publicly_queryable &&
        in_array(
            TFSC_VACANCY_POST_TYPE,
            (array) $object->object_type,
            true
        )
    );
}


function tfsc_get_allowed_combo_taxonomies() {

    static $allowed = null;

    if ( null !== $allowed ) {
        return $allowed;
    }

    $allowed = [];

    foreach (
        get_object_taxonomies(
            TFSC_VACANCY_POST_TYPE,
            'objects'
        )
        as $taxonomy => $object
    ) {
        if ( tfsc_combo_taxonomy_allowed( $taxonomy ) ) {
            $allowed[] = $taxonomy;
        }
    }

    return $allowed;
}


function tfsc_is_combo_archive() {

    return (bool) get_query_var(
        'tfsc_combo_archive'
    );
}


function tfsc_is_vacancy_seo_landing() {

    if (
        tfsc_is_combo_archive() &&
        false !== tfsc_get_combo_archive()
    ) {
        return true;
    }

    $term = get_queried_object();

    return (
        is_tax() &&
        $term instanceof WP_Term &&
        tfsc_combo_taxonomy_allowed(
            $term->taxonomy
        )
    );
}


/**
 * ============================================================
 * QUERY VARS + REWRITE
 * ============================================================
 */

add_filter(
    'query_vars',
    function( $vars ) {

        $vars[] = 'tfsc_combo_archive';
        $vars[] = 'tfsc_combo_path';

        return $vars;
    }
);


add_action(
    'init',
    function() {

        add_rewrite_rule(
            '^'
            . preg_quote( TFSC_COMBO_BASE, '/' )
            . '/(.+?)/?$',

            'index.php'
            . '?tfsc_combo_archive=1'
            . '&tfsc_combo_path=$matches[1]',

            'top'
        );
    },
    99
);


/**
 * ============================================================
 * TERMPAD OPLOSSEN
 * ============================================================
 */

function tfsc_resolve_term_path(
    $taxonomy,
    $segments
) {

    $tax_object =
        get_taxonomy( $taxonomy );

    if (
        ! $tax_object ||
        ! is_array( $segments ) ||
        ! $segments
    ) {
        return false;
    }

    $segments = array_values(
        array_filter(
            array_map(
                'sanitize_title',
                $segments
            )
        )
    );

    if (
        ! $segments ||
        (
            ! $tax_object->hierarchical &&
            1 !== count( $segments )
        )
    ) {
        return false;
    }

    $terms     = [];
    $parent_id = 0;

    foreach ( $segments as $index => $slug ) {

        $term =
            get_term_by(
                'slug',
                $slug,
                $taxonomy
            );

        if (
            ! $term ||
            is_wp_error( $term )
        ) {
            return false;
        }

        if (
            $index > 0 &&
            (int) $term->parent !== $parent_id
        ) {
            return false;
        }

        $terms[]  = $term;
        $parent_id = (int) $term->term_id;
    }

    return [
        'path' => $terms,
        'term' => end( $terms ),
    ];
}


/**
 * ============================================================
 * COMBO URL PARSEN
 * ============================================================
 */

function tfsc_get_combo_archive() {

    static $resolved = false;
    static $cache    = false;

    if ( $resolved ) {
        return $cache;
    }

    $resolved = true;

    if ( ! tfsc_is_combo_archive() ) {
        return false;
    }

    $raw_path =
        get_query_var(
            'tfsc_combo_path'
        );

    if ( ! $raw_path ) {
        return false;
    }

    $segments = array_values(
        array_filter(
            explode(
                '/',
                trim(
                    (string) $raw_path,
                    '/'
                )
            ),
            'strlen'
        )
    );

    if ( count( $segments ) < 2 ) {
        return false;
    }

    $allowed =
        tfsc_get_allowed_combo_taxonomies();

    if ( ! $allowed ) {
        return false;
    }

    $items = [];
    $count = count( $segments );
    $i     = 0;

    while ( $i < $count ) {

        $taxonomy =
            sanitize_key(
                $segments[ $i ]
            );

        if (
            ! in_array(
                $taxonomy,
                $allowed,
                true
            )
        ) {
            return false;
        }

        $i++;
        $term_segments = [];

        while ( $i < $count ) {

            $possible_taxonomy =
                sanitize_key(
                    $segments[ $i ]
                );

            if (
                in_array(
                    $possible_taxonomy,
                    $allowed,
                    true
                )
            ) {
                break;
            }

            $term_segments[] =
                $segments[ $i ];

            $i++;
        }

        if ( ! $term_segments ) {
            return false;
        }

        $resolved_path =
            tfsc_resolve_term_path(
                $taxonomy,
                $term_segments
            );

        if ( ! $resolved_path ) {
            return false;
        }

        $items[] = [
            'taxonomy' => $taxonomy,
            'term'     => $resolved_path['term'],
            'path'     => $resolved_path['path'],
        ];
    }

    return $cache = (
        $items
            ? $items
            : false
    );
}


/**
 * ============================================================
 * COMBO TERMS GROEPEREN
 * ============================================================
 */

function tfsc_group_combo_terms(
    $property = 'term_id'
) {

    $combo =
        tfsc_get_combo_archive();

    if ( ! is_array( $combo ) ) {
        return [];
    }

    $grouped = [];

    foreach ( $combo as $item ) {

        if (
            empty( $item['taxonomy'] ) ||
            empty( $item['term'] ) ||
            ! $item['term'] instanceof WP_Term
        ) {
            continue;
        }

        $taxonomy =
            $item['taxonomy'];

        if ( 'name' === $property ) {

            $value =
                trim(
                    $item['term']->name
                );

        } elseif ( 'slug' === $property ) {

            $value =
                $item['term']->slug;

        } else {

            $value =
                (int) $item['term']->term_id;
        }

        if (
            '' === $value ||
            0 === $value
        ) {
            continue;
        }

        if (
            ! isset(
                $grouped[ $taxonomy ]
            )
        ) {
            $grouped[ $taxonomy ] = [];
        }

        if (
            ! in_array(
                $value,
                $grouped[ $taxonomy ],
                true
            )
        ) {
            $grouped[ $taxonomy ][] =
                $value;
        }
    }

    return $grouped;
}


/**
 * ============================================================
 * TAX QUERY
 * ============================================================
 */

function tfsc_build_combo_tax_query() {

    $grouped =
        tfsc_group_combo_terms(
            'term_id'
        );

    if ( ! $grouped ) {
        return false;
    }

    $tax_query = [
        'relation' => 'AND',
        tfsc_combo_status_clause(),
    ];

    foreach (
        $grouped
        as $taxonomy => $term_ids
    ) {

        if ( ! $term_ids ) {
            continue;
        }

        $object =
            get_taxonomy(
                $taxonomy
            );

        $method =
            tfsc_get_combo_taxonomy_match_method(
                $taxonomy
            );

        $tax_query[] = [
            'taxonomy' =>
                $taxonomy,

            'field' =>
                'term_id',

            'terms' =>
                $term_ids,

            'operator' =>
                (
                    'AND' === $method &&
                    count( $term_ids ) > 1
                )
                    ? 'AND'
                    : 'IN',

            'include_children' =>
                (
                    'AND' !== $method &&
                    $object &&
                    $object->hierarchical
                ),
        ];
    }

    return $tax_query;
}


/**
 * ============================================================
 * MAIN QUERY
 * ============================================================
 */

add_action(
    'pre_get_posts',
    function( $query ) {

        if (
            is_admin() ||
            ! $query->is_main_query()
        ) {
            return;
        }

        /**
         * Virtuele combo archive.
         */
        if ( tfsc_is_combo_archive() ) {

            $tax_query =
                tfsc_build_combo_tax_query();

            if ( ! $tax_query ) {
                return;
            }

            $query->set(
                'post_type',
                TFSC_VACANCY_POST_TYPE
            );

            $query->set(
                'post_status',
                'publish'
            );

            $query->set(
                'tax_query',
                $tax_query
            );

            $query->set(
                'no_found_rows',
                false
            );

            return;
        }

        /**
         * Native vacature taxonomy archive.
         */
        if ( ! $query->is_tax() ) {
            return;
        }

        $term =
            $query->get_queried_object();

        if (
            ! $term instanceof WP_Term ||
            ! tfsc_combo_taxonomy_allowed(
                $term->taxonomy
            )
        ) {
            return;
        }

        $tax_query =
            $query->get(
                'tax_query'
            );

        if ( ! is_array( $tax_query ) ) {
            $tax_query = [];
        }

        $tax_query[] =
            tfsc_combo_status_clause();

        $tax_query['relation'] =
            'AND';

        $query->set(
            'post_type',
            TFSC_VACANCY_POST_TYPE
        );

        $query->set(
            'post_status',
            'publish'
        );

        $query->set(
            'tax_query',
            $tax_query
        );
    },
    1
);


/**
 * ============================================================
 * COMBO ARCHIVE STATUS + 404
 * ============================================================
 */

add_action(
    'wp',
    function() {

        if (
            ! tfsc_is_combo_archive() ||
            ! tfsc_get_combo_archive()
        ) {
            return;
        }

        global $wp_query;

        $wp_query->is_home       = false;
        $wp_query->is_front_page = false;
        $wp_query->is_page       = false;
        $wp_query->is_single     = false;
        $wp_query->is_singular   = false;
        $wp_query->is_search     = false;
        $wp_query->is_404        = false;
        $wp_query->is_archive    = true;
    }
);


add_action(
    'template_redirect',
    function() {

        if (
            ! tfsc_is_combo_archive() ||
            tfsc_get_combo_archive()
        ) {
            return;
        }

        global $wp_query;

        $wp_query->set_404();
        status_header( 404 );
        nocache_headers();
    }
);


/**
 * ============================================================
 * SEARCH & FILTER PRO
 * ============================================================
 *
 * Op SEO-landings:
 * eerste wijziging → hard load naar /vacatures/?_...
 *
 * Op combo archives:
 * huidige combo-values eerst synchroniseren naar S&F.
 *
 * Daarna werkt /vacatures/ normaal verder via AJAX.
 */

add_action(
    'wp_footer',
    function() {

        if (
            ! tfsc_is_vacancy_seo_landing()
        ) {
            return;
        }

        $is_combo =
            tfsc_is_combo_archive();

        $active_values =
            $is_combo
                ? tfsc_group_combo_terms( 'slug' )
                : [];

        ?>
        <script>
        (function () {

            const QUERY_ID =
                <?php echo (int) TFSC_SEARCH_FILTER_QUERY_ID; ?>;

            const IS_COMBO =
                <?php echo $is_combo ? 'true' : 'false'; ?>;

            const COMBO_VALUES =
                <?php echo wp_json_encode( $active_values ); ?>;

            let comboReady = ! IS_COMBO;
            let comboDone = false;
            let redirectAttached = false;
            let redirectStarted = false;


            function normalize(items) {

                if (!items) {
                    return [];
                }

                if (items instanceof Map) {
                    return Array.from(items.values());
                }

                if (Array.isArray(items)) {
                    return items;
                }

                if (typeof items === 'object') {
                    return Object.values(items);
                }

                return [];
            }


            function sameValues(a, b) {

                a = Array.isArray(a)
                    ? a.map(String).sort()
                    : [];

                b = Array.isArray(b)
                    ? b.map(String).sort()
                    : [];

                return (
                    a.length === b.length &&
                    a.every(
                        (value, index) =>
                            value === b[index]
                    )
                );
            }


            function syncCombo() {

    if (!IS_COMBO || comboDone) {
        return;
    }

    const api = window.searchAndFilter?.frontend?.fields;

    if (!api || typeof api.getAll !== 'function') {
        return;
    }

    const fields = normalize(api.getAll());

    let expected = 0;
    let correct  = 0;

    fields.forEach(field => {

        if (
            !field ||
            typeof field.getAttributes !== 'function' ||
            typeof field.getValues !== 'function' ||
            typeof field.setValues !== 'function'
        ) {
            return;
        }

        const attributes = field.getAttributes();

        if (
            !attributes ||
            String(attributes.queryId) !== String(QUERY_ID) ||
            attributes.dataType !== 'taxonomy'
        ) {
            return;
        }

        const taxonomy = attributes.dataTaxonomy;

        if (
            !taxonomy ||
            !Object.prototype.hasOwnProperty.call(
                COMBO_VALUES,
                taxonomy
            )
        ) {
            return;
        }

        expected++;

        const wanted = (Array.isArray(COMBO_VALUES[taxonomy])
            ? COMBO_VALUES[taxonomy]
            : [COMBO_VALUES[taxonomy]]
        ).map(String);

        const current = (Array.isArray(field.getValues())
            ? field.getValues()
            : []
        ).map(String);

        if (sameValues(current, wanted)) {
            correct++;
            return;
        }

        field.setValues(wanted);
    });

    if (expected && correct === expected) {

        comboDone  = true;
        comboReady = true;

        window.TFSC_SF_COMBO_READY = true;
    }
}


            function buildResultsUrl(query) {

                if (
                    !query ||
                    typeof query.getUrl !== 'function' ||
                    typeof query.getFields !== 'function'
                ) {
                    return null;
                }

                const baseUrl =
                    query.getUrl();

                if (!baseUrl) {
                    return null;
                }

                const prefix =
                    window.searchAndFilterData?.urlPrefix
                    || '_';

                const parts = [];

                normalize(
                    query.getFields()
                ).forEach(
                    function (field) {

                        if (
                            !field ||
                            typeof field.getUrlName !== 'function' ||
                            typeof field.getUrlValues !== 'function'
                        ) {
                            return;
                        }

                        const name =
                            field.getUrlName();

                        let values =
                            field.getUrlValues();

                        if (!name) {
                            return;
                        }

                        if (
                            values !== null &&
                            values !== undefined &&
                            !Array.isArray(values)
                        ) {
                            values = [values];
                        }

                        values =
                            Array.isArray(values)
                                ? values.filter(
                                    value =>
                                        value !== null &&
                                        value !== undefined &&
                                        String(value) !== ''
                                )
                                : [];

                        if (!values.length) {
                            return;
                        }

                        parts.push(
                            encodeURIComponent(
                                prefix + name
                            )
                            +
                            '='
                            +
                            values
                                .map(
                                    value =>
                                        encodeURIComponent(
                                            String(value)
                                        )
                                )
                                .join(',')
                        );
                    }
                );

                if (
                    typeof query.getActiveUrlArg
                    === 'function'
                ) {

                    const active =
                        query.getActiveUrlArg();

                    if (active) {
                        parts.push(
                            encodeURIComponent(active)
                        );
                    }
                }

                const cleanBase =
                    String(baseUrl)
                        .split('#')[0]
                        .split('?')[0];

                return parts.length
                    ? cleanBase + '?' + parts.join('&')
                    : cleanBase;
            }


            function attachRedirect() {

                if (redirectAttached) {
                    return true;
                }

                const queries =
                    window
                        .searchAndFilter
                        ?.frontend
                        ?.queries;

                if (
                    !queries ||
                    typeof queries.get !== 'function'
                ) {
                    return false;
                }

                const query =
                    queries.get(QUERY_ID);

                if (
                    !query ||
                    typeof query.on !== 'function'
                ) {
                    return false;
                }

                redirectAttached = true;

                query.on(
                    'get-results/start',
                    function () {

                        if (
                            redirectStarted ||
                            (
                                IS_COMBO &&
                                !comboReady
                            )
                        ) {
                            return;
                        }

                        const url =
                            buildResultsUrl(query);

                        if (!url) {
                            return;
                        }

                        redirectStarted = true;
                        window.location.href = url;
                    }
                );

                return true;
            }


            function start() {

                [50, 250, 750, 1500]
                    .forEach(
                        delay =>
                            setTimeout(
                                syncCombo,
                                delay
                            )
                    );

                [250, 750, 1500]
                    .forEach(
                        delay =>
                            setTimeout(
                                attachRedirect,
                                delay
                            )
                    );
            }


            document.addEventListener(
                'search-filter/mount',
                function () {

                    setTimeout(
                        syncCombo,
                        0
                    );

                    setTimeout(
                        syncCombo,
                        100
                    );

                    setTimeout(
                        attachRedirect,
                        150
                    );

                    setTimeout(
                        attachRedirect,
                        400
                    );
                }
            );


            if (
                document.readyState === 'loading'
            ) {
                document.addEventListener(
                    'DOMContentLoaded',
                    start
                );
            } else {
                start();
            }


            /**
             * Browser Back / BFCache.
             */
            window.addEventListener(
                'pageshow',
                function (event) {

                    if (event.persisted) {
                        window.location.reload();
                    }
                }
            );

        })();
        </script>
        <?php
    },
    90
);


/**
 * ============================================================
 * ELEMENTOR ARCHIVE CONDITION
 * ============================================================
 */

add_action(
    'elementor/theme/register_conditions',
    function( $conditions_manager ) {

        if (
            ! class_exists(
                '\ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base'
            )
        ) {
            return;
        }

        if (
            ! class_exists(
                'TFSC_Combined_Vacancy_Archive_Condition'
            )
        ) {

            class TFSC_Combined_Vacancy_Archive_Condition
                extends \ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base {

                public static function get_type() {
                    return 'archive';
                }

                public static function get_priority() {
                    return 40;
                }

                public function get_name() {
                    return 'tfsc_combined_vacancy_archive';
                }

                public function get_label() {
                    return 'TFSC Combined Vacancy Archives';
                }

                public function check( $args ) {

                    return (
                        tfsc_is_combo_archive() &&
                        false !== tfsc_get_combo_archive()
                    );
                }
            }
        }

        $archive =
            $conditions_manager
                ->get_condition(
                    'archive'
                );

        if ( $archive ) {
            $archive->register_sub_condition(
                new TFSC_Combined_Vacancy_Archive_Condition()
            );
        }
    }
);


/**
 * ============================================================
 * ELEMENTOR - ALLE NATIVE VACATURE TAXONOMY TERM ARCHIVES
 * ============================================================
 */

add_action(
    'elementor/theme/register_conditions',
    function( $conditions_manager ) {

        if (
            ! class_exists(
                '\ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base'
            )
        ) {
            return;
        }

        if (
            ! class_exists(
                'TFSC_Vacancy_Taxonomy_Term_Archive_Condition'
            )
        ) {

            class TFSC_Vacancy_Taxonomy_Term_Archive_Condition
                extends \ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base {

                public static function get_type() {
                    return 'archive';
                }

                public static function get_priority() {
                    return 41;
                }

                public function get_name() {
                    return 'tfsc_vacancy_taxonomy_term_archives';
                }

                public function get_label() {
                    return 'TFSC Vacancy Taxonomy Term Archives';
                }

                public function check( $args ) {

                    if ( ! is_tax() ) {
                        return false;
                    }

                    $term =
                        get_queried_object();

                    if (
                        ! $term instanceof WP_Term
                    ) {
                        return false;
                    }

                    /**
                     * Status-taxonomy niet als normaal
                     * vacature-archive behandelen.
                     */
                    if (
                        defined( 'TFSC_STATUS_TAXONOMY' )
                        &&
                        TFSC_STATUS_TAXONOMY === $term->taxonomy
                    ) {
                        return false;
                    }

                    /**
                     * Gebruik de bestaande centrale helper
                     * wanneer beschikbaar.
                     */
                    if (
                        function_exists(
                            'tfsc_combo_taxonomy_allowed'
                        )
                    ) {
                        return tfsc_combo_taxonomy_allowed(
                            $term->taxonomy
                        );
                    }

                    /**
                     * Fallback:
                     * taxonomy moet aan vacature hangen.
                     */
                    $taxonomy =
                        get_taxonomy(
                            $term->taxonomy
                        );

                    if ( ! $taxonomy ) {
                        return false;
                    }

                    $post_type =
                        defined( 'TFSC_VACANCY_POST_TYPE' )
                            ? TFSC_VACANCY_POST_TYPE
                            : 'vacature';

                    return in_array(
                        $post_type,
                        (array) $taxonomy->object_type,
                        true
                    );
                }
            }
        }

        $archive =
            $conditions_manager
                ->get_condition(
                    'archive'
                );

        if ( $archive ) {

            $archive->register_sub_condition(
                new TFSC_Vacancy_Taxonomy_Term_Archive_Condition()
            );
        }
    }
);


/**
 * ============================================================
 * HEADING HTML
 * ============================================================
 */

function tfsc_combo_heading_html( $rows ) {

    $rows = array_values(
        array_filter(
            (array) $rows,
            'strlen'
        )
    );

    if ( ! $rows ) {
        return '';
    }

    $html =
        '<span class="tfsc-heading">';

    foreach (
        $rows
        as $index => $row
    ) {

        $html .=
            '<span class="'
            .
            (
                0 === $index
                    ? 'tfsc-heading__main'
                    : 'tfsc-heading__row'
            )
            .
            '">'
            .
            esc_html( $row )
            .
            '</span>';
    }

    $html .=
        '</span>';

    return $html;
}


/**
 * ============================================================
 * [combo_heading]
 * ============================================================
 */

add_shortcode(
    'combo_heading',
    function() {

        /**
         * Combo archive.
         */
        if ( tfsc_is_combo_archive() ) {

            $grouped =
                tfsc_group_combo_terms(
                    'name'
                );

            if ( ! $grouped ) {
                return '';
            }

            $rows = [];

            if (
                ! empty(
                    $grouped['functienaam']
                )
            ) {

                $functions =
                    $grouped['functienaam'];

                if ( 1 === count( $functions ) ) {

                    $rows[] =
                        $functions[0]
                        .
                        ' vacatures';

                } else {

                    $rows[] =
                        'Vacatures';

                    $rows[] =
                        implode(
                            ', ',
                            $functions
                        );
                }

                unset(
                    $grouped['functienaam']
                );

            } else {

                $rows[] =
                    'Vacatures';
            }

            foreach (
                $grouped
                as $names
            ) {
                if ( $names ) {
                    $rows[] =
                        implode(
                            ', ',
                            $names
                        );
                }
            }

            return tfsc_combo_heading_html(
                $rows
            );
        }

        /**
         * Native taxonomy archive.
         */
        $term =
            get_queried_object();

        if (
            $term instanceof WP_Term &&
            tfsc_combo_taxonomy_allowed(
                $term->taxonomy
            )
        ) {

            $title =
                get_term_meta(
                    $term->term_id,
                    'pods_archive_title',
                    true
                );

            $title =
                is_string( $title )
                    ? trim(
                        wp_strip_all_tags(
                            $title
                        )
                    )
                    : '';

            if ( '' === $title ) {

                $name =
                    trim(
                        $term->name
                    );

                if ( '' === $name ) {
                    return '';
                }

                $title =
                    $name
                    .
                    ' vacatures';
            }

            return tfsc_combo_heading_html(
                [ $title ]
            );
        }

        /**
         * Vacature post type archive.
         */
        if (
            is_post_type_archive(
                TFSC_VACANCY_POST_TYPE
            )
        ) {
            return tfsc_combo_heading_html(
                [ 'Vacatures' ]
            );
        }

        return '';
    }
);


/**
 * ============================================================
 * [combo_term taxonomy="..."]
 * ============================================================
 */

add_shortcode(
    'combo_term',
    function( $atts ) {

        $atts =
            shortcode_atts(
                [
                    'taxonomy' => '',
                    'separator' => ' • ',
                ],
                $atts,
                'combo_term'
            );

        $taxonomy =
            sanitize_key(
                $atts['taxonomy']
            );

        if ( ! $taxonomy ) {
            return '';
        }

        /**
         * Combo archive.
         */
        if ( tfsc_is_combo_archive() ) {

            $grouped =
                tfsc_group_combo_terms(
                    'name'
                );

            if (
                empty(
                    $grouped[ $taxonomy ]
                )
            ) {
                return '';
            }

            return esc_html(
                implode(
                    (string) $atts['separator'],
                    $grouped[ $taxonomy ]
                )
            );
        }

        /**
         * Native taxonomy archive.
         */
        $term =
            get_queried_object();

        if (
            $term instanceof WP_Term &&
            $term->taxonomy === $taxonomy &&
            tfsc_combo_taxonomy_allowed(
                $taxonomy
            )
        ) {
            return esc_html(
                $term->name
            );
        }

        return '';
    }
);
