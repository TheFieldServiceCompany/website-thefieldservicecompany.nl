<?php

/**
 * TFSC - Virtuele Beroepengids-basearchieven in Rank Math sitemap
 *
 * WordPress Code Snippets ID: 215
 *
 * DOEL
 * ============================================================
 * Snippet 113 maakt virtuele taxonomy-basearchieven zoals:
 *
 * /beroepengids/functienamen/
 * /beroepengids/functie-inhoud/
 * /beroepengids/opleidingen/
 *
 * Deze URL's zijn geen pagina en geen echte taxonomyterm. Rank Math kan ze
 * daarom niet automatisch uit de gewone WordPress-objecten afleiden. Deze
 * snippet voegt de toegestane, publieke Beroepengids-bases als extra URL toe
 * aan de eerste pagina van page-sitemap.xml. Daarnaast koppelt hij de native
 * beroep_opleiding-taxonomysitemap aan de sitemapindex wanneer Rank Math die
 * door nog lege postrelaties niet zelf vermeldt.
 *
 * AFBAKENING
 * ============================================================
 * - De allowlist van snippet 113 blijft leidend.
 * - Alleen taxonomies met rewrite-prefix `beroepengids/` worden toegevoegd.
 * - Gewone taxonomytermarchieven blijven aan Rank Math zelf.
 * - Vacature-comboarchieven blijven aan snippet 148.
 * - Er worden geen duizenden combinaties aan page-sitemap.xml toegevoegd.
 * - Het nieuwe beroep_opleiding-sitemapbestand wordt expliciet aan de index
 *   gekoppeld zolang Rank Math het door nul gekoppelde artikelen weglaat.
 */


/**
 * Geeft alle geldige virtuele Beroepengids-base-URL's terug.
 *
 * @return string[]
 */
function tfsc_beroepengids_base_sitemap_urls() {

    if ( ! function_exists( 'tfsc_taxonomy_base_archive_allowed' ) ) {
        return [];
    }

    $urls = [];

    foreach (
        get_taxonomies(
            [
                'public' => true,
            ],
            'objects'
        )
        as $taxonomy
    ) {

        if (
            ! $taxonomy instanceof WP_Taxonomy ||
            ! tfsc_taxonomy_base_archive_allowed( $taxonomy->name )
        ) {
            continue;
        }

        $slug = trim(
            (string) (
                $taxonomy->rewrite['slug']
                ?? ''
            ),
            '/'
        );

        if (
            ! $slug ||
            0 !== strpos( $slug, 'beroepengids/' )
        ) {
            continue;
        }

        $urls[] = home_url( '/' . $slug . '/' );
    }

    $urls = array_values(
        array_unique(
            array_filter( $urls )
        )
    );

    sort( $urls, SORT_NATURAL | SORT_FLAG_CASE );

    return $urls;
}


/**
 * Voeg de virtuele bases toe aan page-sitemap.xml.
 * Rank Math past deze filter uitsluitend op de eerste sitemap-pagina toe.
 */
add_filter(
    'rank_math/sitemap/page_content',
    function( $content ) {

        foreach ( tfsc_beroepengids_base_sitemap_urls() as $url ) {

            $loc = esc_url( $url );

            if (
                false !== strpos(
                    (string) $content,
                    '<loc>' . $loc . '</loc>'
                )
            ) {
                continue;
            }

            $content .=
                "\n\t<url>\n"
                . "\t\t<loc>"
                . $loc
                . "</loc>\n"
                . "\t</url>";
        }

        return $content;
    },
    20
);


/**
 * Rank Math kan een taxonomy-sitemap met `lege termen opnemen` correct
 * genereren, maar laat die sitemap uit de index zolang alle terms nog nul
 * gekoppelde Beroepengids-artikelen hebben. De sitemap zelf bevat al wel alle
 * 29 inhoudelijk gevulde termen. Daarom koppelen we hem expliciet aan de index.
 */
add_filter(
    'rank_math/sitemap/index',
    function( $xml ) {

        if ( ! taxonomy_exists( 'beroep_opleiding' ) ) {
            return $xml;
        }

        $url = home_url( '/beroep_opleiding-sitemap.xml' );
        $loc = esc_url( $url );

        if (
            false !== strpos(
                (string) $xml,
                '<loc>' . $loc . '</loc>'
            )
        ) {
            return $xml;
        }

        $xml .=
            "\n\t<sitemap>\n"
            . "\t\t<loc>"
            . $loc
            . "</loc>\n"
            . "\t</sitemap>";

        return $xml;
    },
    20
);


/**
 * Eenmalig de page-sitemapcache vernieuwen na invoering van deze versie.
 */
add_action(
    'init',
    function() {

        $version = '2026-10-18-v2';

        if (
            get_option( 'tfsc_beroep_base_sitemap_version' ) === $version
        ) {
            return;
        }

        if ( class_exists( '\\RankMath\\Sitemap\\Cache' ) ) {
            \RankMath\Sitemap\Cache::invalidate_storage();
        }

        update_option(
            'tfsc_beroep_base_sitemap_version',
            $version,
            false
        );
    },
    100
);
