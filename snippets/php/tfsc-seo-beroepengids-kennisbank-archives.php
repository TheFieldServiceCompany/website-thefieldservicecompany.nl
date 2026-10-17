<?php

/**
 * TFSC - SEO metadata Beroepengids en Kennisbank
 *
 * WordPress Code Snippets ID: 209
 * LIVE-status: actief en hertest op 17 oktober 2026
 *
 * DOEL
 * ============================================================
 * Geeft acht centrale landingspagina's een unieke SEO-title en meta
 * description:
 *
 * - de gewone WordPress-pagina /beroepengids/;
 * - het CPT-archief /kennisbank/;
 * - de zes virtuele taxonomy-base-archieven onder /beroepengids/.
 *
 * WAAROM VIA EEN SNIPPET?
 * ============================================================
 * De zes taxonomy-base-archieven zijn virtuele archieven. Ze bestaan dankzij
 * de queryvar `tfsc_taxonomy_archive` en hebben geen eigen WordPress-term of
 * pagina waarop Rank Math losse metadata kan opslaan. Zonder deze gerichte
 * mapping gebruikt Rank Math daar een generieke archieftitel.
 *
 * AFBAKENING
 * ============================================================
 * Deze code verandert uitsluitend title en description. De code wijzigt niet:
 *
 * - canonical-URL's;
 * - robots-instructies;
 * - vacature-comboarchieven;
 * - gewone taxonomy-termarchieven;
 * - sitemapregels;
 * - zichtbare H1-, intro- of description-teksten.
 *
 * Open Graph en Twitter krijgen dezelfde titel en beschrijving als de gewone
 * SEO-metadata, zodat gedeelde links niet terugvallen op generieke tekst.
 */

/**
 * Geeft de SEO-data voor de huidige request terug.
 *
 * @return array{title:string,description:string}|false
 */
function tfsc_knowledge_archive_seo_data() {

    /**
     * De Beroepengids-hoofdpagina is een gewone Elementor-pagina.
     */
    if ( is_page( 24266 ) ) {
        return [
            'title'       => 'Beroepengids voor field service | The Field Service Company',
            'description' => 'Ontdek welke fieldservicefunctie bij jou past. Vergelijk functienamen, werkzaamheden, kennisgebieden, sectoren, werkgebieden en actuele vacatures.',
        ];
    }

    /**
     * De Kennisbank is het archief van het CPT kennisbankartikel.
     */
    if ( is_post_type_archive( 'kennisbankartikel' ) ) {
        return [
            'title'       => 'Kennisbank over field service | The Field Service Company',
            'description' => 'Vind praktische kennis over field service, van service en commissioning tot loopbaan, internationale inzet, recruitment en werken bij klanten.',
        ];
    }

    /**
     * Alleen de exact bekende virtuele Beroepengids-basearchieven.
     */
    $taxonomy_name = sanitize_key(
        (string) get_query_var( 'tfsc_taxonomy_archive' )
    );

    $archives = [
        'beroep_functienaam' => [
            'title'       => 'Functienamen in field service | Beroepengids',
            'description' => 'Ontdek functienamen in field service en vind welke fieldservicefunctie bij jou past. Vergelijk werkzaamheden, branches, werkgebieden en vacatures.',
        ],
        'beroep_functie-inhoud' => [
            'title'       => 'Werkzaamheden in field service | Beroepengids',
            'description' => 'Ontdek werkzaamheden binnen field service en vind welke fieldservicefunctie bij jou past. Vergelijk service, onderhoud, inbedrijfstellen en meer.',
        ],
        'beroep_kennisgebied' => [
            'title'       => 'Kennisgebieden binnen field service | Beroepengids',
            'description' => 'Ontdek kennisgebieden binnen field service en vind functies en vacatures die passen bij jouw kennis, interesses en ontwikkelrichting.',
        ],
        'beroep_bedrijfssector' => [
            'title'       => 'Bedrijfssectoren binnen field service | Beroepengids',
            'description' => 'Ontdek bedrijfssectoren binnen field service en bekijk welke functies, machines, werkomgevingen en actuele vacatures bij jou passen.',
        ],
        'beroep_opleidingsniveau' => [
            'title'       => 'Opleidingsniveaus voor fieldservicefuncties | Beroepengids',
            'description' => 'Vergelijk opleidingsniveaus binnen field service en ontdek welke functies, ontwikkelroutes en vacatures bij jouw achtergrond passen.',
        ],
        'beroep_werkgebied' => [
            'title'       => 'Werkgebieden voor fieldservicefuncties | Beroepengids',
            'description' => 'Vergelijk werkgebieden binnen field service en ontdek functies en vacatures die passen bij de regio’s waarin jij wilt werken en reizen.',
        ],
    ];

    return $archives[ $taxonomy_name ] ?? false;
}

/**
 * Houd alle tekstuitvoer schoon en voorspelbaar.
 */
function tfsc_filter_knowledge_archive_seo_value( $current, $key ) {
    $seo = tfsc_knowledge_archive_seo_data();

    if ( ! $seo || empty( $seo[ $key ] ) ) {
        return $current;
    }

    return wp_strip_all_tags( $seo[ $key ] );
}

add_filter(
    'rank_math/frontend/title',
    function( $title ) {
        return tfsc_filter_knowledge_archive_seo_value( $title, 'title' );
    },
    80
);

add_filter(
    'rank_math/frontend/description',
    function( $description ) {
        return tfsc_filter_knowledge_archive_seo_value(
            $description,
            'description'
        );
    },
    80
);

$social_filters = [
    'rank_math/opengraph/facebook/og_title'            => 'title',
    'rank_math/opengraph/facebook/og_description'      => 'description',
    'rank_math/opengraph/twitter/twitter_title'        => 'title',
    'rank_math/opengraph/twitter/twitter_description'  => 'description',
];

foreach ( $social_filters as $hook => $key ) {
    add_filter(
        $hook,
        function( $current ) use ( $key ) {
            return tfsc_filter_knowledge_archive_seo_value( $current, $key );
        },
        80
    );
}

unset( $social_filters );
