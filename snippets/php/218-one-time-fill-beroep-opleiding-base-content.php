<?php

/**
 * TFSC - Eenmalig vullen van het Opleidingen-basearchief
 *
 * LIVE-uitvoering: 18 oktober 2026
 * Bedoeling: één keer actief uitvoeren en daarna direct deactiveren.
 */

add_action(
    'init',
    function() {

        $version = '2026-10-18-v1';

        if (
            get_option( 'tfsc_beroep_opleiding_base_content_version' )
            === $version
        ) {
            return;
        }

        if ( ! function_exists( 'pods' ) ) {
            return;
        }

        $intro = <<<'HTML'
Welke opleiding sluit aan op het fieldservicewerk dat jij wilt doen? Verken opleidingsniveaus en technische studierichtingen en ontdek hoe jouw kennis aansluit op functies, machines, bedrijfssectoren en actuele vacatures.
HTML;

        $description = <<<'HTML'
<h2>Welke opleidingen sluiten aan op field service?</h2>

<p>Field service vraagt om mensen die techniek begrijpen én die kennis bij klanten in de praktijk kunnen toepassen. Op deze pagina kun je opleidingen vanuit twee kanten verkennen: op opleidingsniveau en op studierichting. Zo ontdek je welke achtergrond past bij werk aan machines, installaties, productielijnen en technische systemen op locatie.</p>

<p>Een technische opleiding kan rechtstreeks voorbereiden op field service, maar is niet de enige route. Ook ervaring in onderhoud, productie, installatie, engineering of een werkplaats kan een sterke basis vormen. Daarom helpt de Beroepengids je niet alleen naar een diploma te kijken. Je vergelijkt wat je tijdens een opleiding leert met de werkzaamheden, verantwoordelijkheid en technische omgeving van verschillende fieldservicefuncties.</p>

<h2>Wat zegt een opleidingsniveau over jouw mogelijkheden?</h2>

<p>Werkgevers noemen in vacatures vaak vmbo/lbo, mbo, hbo of wo. Het gevraagde niveau geeft een indicatie van de technische diepgang, zelfstandigheid en verantwoordelijkheid binnen een functie. Voor praktisch service- en onderhoudswerk wegen aantoonbare ervaring en probleemoplossend vermogen vaak zwaar mee. Bij specialistische engineering, complexe commissioning, projectleiding of site management kan een hoger werk- en denkniveau belangrijk zijn.</p>

<p>Bekijk per opleidingsniveau welke functies en vacatures aansluiten. Let daarbij ook op de gevraagde ervaring, certificaten en het type techniek. Een niveau is een startpunt voor jouw zoektocht, geen harde grens voor wat jij kunt bereiken.</p>

<h2>Welke studierichting past bij field service?</h2>

<p>Binnen field service komen veel technische studierichtingen samen. Werktuigbouwkunde, elektrotechniek en mechatronica sluiten aan op veel machines en installaties. Procestechniek, chemische technologie en operationele techniek zijn relevant in industriële omgevingen. ICT en technische informatica, embedded systems, robotica en industriële automatisering spelen een grote rol bij slimme machines, besturingen en diagnose op afstand. Ook richtingen zoals autotechniek, energietechniek, maritieme techniek en luchtvaarttechniek kunnen leiden naar gespecialiseerde fieldservicefuncties.</p>

<p>Per studierichting lees je welke kennis je in het werk gebruikt, welke systemen en bedrijfssectoren erbij passen en welke functies je kunt onderzoeken. Daarmee vertaal je jouw opleiding naar concrete mogelijkheden buiten het klaslokaal.</p>

<h2>Hoe vind je een passende fieldservicefunctie?</h2>

<p>Begin bij jouw opleidingsniveau of studierichting en bekijk daarna de gekoppelde functienamen, kennisgebieden, bedrijfssectoren en beroepsartikelen. Vergelijk die informatie met jouw praktijkervaring en met het soort werk dat je zoekt: storingen oplossen, onderhoud uitvoeren, installaties in bedrijf stellen, klanten adviseren, projecten begeleiden of teams op locatie aansturen.</p>

<p>The Field Service Company biedt actuele fieldservicevacatures voor verschillende opleidingsniveaus en technische studierichtingen. Via de koppelingen in de Beroepengids ga je gericht van jouw achtergrond naar functies en vacatures waarin je jouw technische kennis kunt inzetten en verder ontwikkelen.</p>
HTML;

        $pod = pods( 'pods_archief_instellingen' );

        $result = [
            'version' => $version,
            'saved'   => false,
            'errors'  => [],
        ];

        if ( ! $pod ) {
            $result['errors'][] = 'Instellingen-Pod niet gevonden.';
        } else {
            $saved = $pod->save(
                [
                    'archive_beroep_opleiding_intro'       => trim( $intro ),
                    'archive_beroep_opleiding_description' => trim( $description ),
                ]
            );

            $result['saved'] = false !== $saved;

            if ( false === $saved ) {
                $result['errors'][] = 'Opleidingen-content niet opgeslagen.';
            }
        }

        update_option(
            'tfsc_beroep_opleiding_base_content_result',
            $result,
            false
        );

        if ( $result['saved'] ) {
            update_option(
                'tfsc_beroep_opleiding_base_content_version',
                $version,
                false
            );
        }
    },
    100
);


add_action(
    'rest_api_init',
    function() {
        register_rest_route(
            'tfsc-debug/v1',
            '/opleidingen-base-content',
            [
                'methods'             => 'GET',
                'permission_callback' => function() {
                    return current_user_can( 'manage_options' );
                },
                'callback'            => function() {
                    return rest_ensure_response(
                        get_option(
                            'tfsc_beroep_opleiding_base_content_result',
                            []
                        )
                    );
                },
            ]
        );
    }
);
