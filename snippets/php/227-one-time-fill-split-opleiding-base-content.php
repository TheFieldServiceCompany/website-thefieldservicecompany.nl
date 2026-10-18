<?php

/**
 * TFSC - Eenmalig vullen Opleidingsniveaus- en Studierichtingen-archieven
 *
 * LIVE-uitvoering: 18 oktober 2026
 * Bedoeling: één keer actief uitvoeren en daarna direct deactiveren.
 */

add_action(
    'init',
    function() {

        $version = '2026-10-18-v1';

        if (
            get_option( 'tfsc_split_opleiding_base_content_version' )
            === $version
        ) {
            return;
        }

        if ( ! function_exists( 'pods' ) ) {
            return;
        }

        $content = [
            'archive_beroep_opleidingsniveau_intro' => <<<'HTML'
Welk opleidingsniveau sluit aan op de fieldservicefunctie die jij zoekt? Vergelijk niveaus, praktijkervaring en verantwoordelijkheden en ontdek welke functies en actuele vacatures bij jouw achtergrond passen.
HTML,
            'archive_beroep_opleidingsniveau_description' => <<<'HTML'
<h2>Welk opleidingsniveau heb je nodig voor field service?</h2>

<p>Binnen field service werken mensen met verschillende opleidingsniveaus. Het passende niveau hangt niet alleen af van de functietitel, maar vooral van het technische systeem, de werkzaamheden en de verantwoordelijkheid die je bij een klant krijgt. Een servicemonteur die onderhoud uitvoert aan industriële machines heeft een ander profiel dan een commissioning engineer die een complete productielijn in bedrijf stelt. Ook voor site supervisors, technical support engineers en service managers wegen opleiding en ervaring op een andere manier mee.</p>

<p>Daarom helpt een opleidingsniveau je om functies te selecteren, maar geeft het nooit het volledige antwoord. Werkgevers kijken ook naar jouw studierichting, productkennis, certificaten, zelfstandigheid en ervaring met storingen, onderhoud of commissioning.</p>

<h2>Wat zeggen vmbo, mbo, hbo en wo over fieldservicewerk?</h2>

<p>Vmbo bereidt je voor op een vervolgopleiding. Lbo is een oudere benaming die nog voorkomt op cv’s en in loopbaanhistorie. Voor zelfstandig technisch servicewerk wordt meestal aanvullende vakkennis gevraagd, bijvoorbeeld via een mbo-opleiding, leerwerktraject of aantoonbare praktijkervaring.</p>

<p>Mbo sluit sterk aan op praktisch fieldservicewerk. Je leert veilig werken, technische tekeningen en schema’s gebruiken, meten, monteren, onderhouden en storingen systematisch onderzoeken. Het mbo-niveau en de gekozen richting bepalen hoeveel technische diepgang en zelfstandigheid je tijdens de opleiding opbouwt.</p>

<p>Hbo verbindt praktijk met analyse, ontwerp en verbetering. Dat past bij complexere diagnose, inbedrijfstelling, technisch advies, projectverantwoordelijkheid en het vertalen van problemen uit het veld naar engineering. Een associate degree valt in deze Beroepengids onder het hbo-filter, maar blijft een zelfstandige tweejarige opleiding en is niet hetzelfde als een hbo-bachelor.</p>

<p>Wo kan relevant zijn voor zeer complexe systemen, onderzoek, modellering of specialistische engineering. Veel fieldservicefuncties vragen echter vooral om de juiste combinatie van technische kennis en praktische inzetbaarheid. Een hoger diploma maakt praktijkervaring op locatie dus niet overbodig.</p>

<h2>Hoe belangrijk is werk- en denkniveau?</h2>

<p>In vacatures staat soms een diploma-eis en soms een gevraagd werk- en denkniveau. Bij werk- en denkniveau kan ruime ervaring meewegen, maar dat betekent niet automatisch dat iedere formele eis vervalt. Voor gereguleerde werkzaamheden, veiligheid of specifieke bevoegdheden kunnen diploma’s en certificaten noodzakelijk blijven.</p>

<p>Lees daarom wat je werkelijk gaat doen. Moet je vooral onderhoud uitvoeren en onderdelen vervangen, of ook root-cause-analyses maken, software testen, klanten adviseren en collega’s aansturen? Hoe groter de technische complexiteit en beslissingsruimte, hoe zwaarder analyse, communicatie en verantwoordelijkheid meestal meetellen.</p>

<h2>Hoe vind je vacatures die bij jouw niveau passen?</h2>

<p>Vergelijk eerst jouw opleidingsniveau met de gevraagde studierichting en werkzaamheden. Kijk daarna naar het soort machines, de bedrijfssector, het reisprofiel en de ervaring die een werkgever verwacht. Zo ontdek je of jouw achtergrond direct aansluit of welke training, certificering of praktijkstap je nog nodig hebt.</p>

<p>The Field Service Company koppelt actuele fieldservicevacatures aan opleidingsniveaus. Daardoor kun je gericht bekijken welke functies bij jouw achtergrond passen en vervolgens beoordelen waar jouw technische kennis, ervaring en ontwikkelambitie het beste tot hun recht komen.</p>
HTML,
            'archive_beroep_studierichting_intro' => <<<'HTML'
Welke studierichting sluit aan op jouw toekomst in field service? Ontdek hoe technische opleidingen passen bij machines, systemen, bedrijfssectoren, functies en actuele vacatures op locatie bij klanten.
HTML,
            'archive_beroep_studierichting_description' => <<<'HTML'
<h2>Welke studierichtingen sluiten aan op field service?</h2>

<p>Field service brengt verschillende technische vakgebieden samen. Werktuigbouwkunde, elektrotechniek en mechatronica vormen een brede basis voor machines en installaties. Industriële automatisering, elektronica, embedded systems, robotica en ICT &amp; technische informatica sluiten aan op besturingen, sensoren, software en diagnose. Procestechniek, chemische technologie en operationele techniek zijn relevant wanneer apparatuur onderdeel is van een productieproces. Ook autotechniek, bedrijfsautotechniek, mobiele werktuigentechniek, energietechniek, installatietechniek, maritieme techniek en luchtvaarttechniek kunnen leiden naar gespecialiseerde fieldservicefuncties.</p>

<p>De naam van een opleiding is daarbij een startpunt. Onderwijsinstellingen kunnen vergelijkbare opleidingen anders noemen of andere accenten leggen. Kijk daarom altijd naar wat je daadwerkelijk hebt geleerd en naar de techniek waarmee een werkgever werkt.</p>

<h2>Welke kennis uit jouw opleiding gebruik je in field service?</h2>

<p>In field service pas je theorie toe op een systeem dat bij een klant moet blijven presteren. Mechanische kennis helpt bij slijtage, aandrijvingen, lageringen en nauwkeurige montage. Elektrotechniek is nodig voor voedingen, motoren, schakelingen en veilig meten. Besturingskennis helpt bij PLC’s, sensoren, netwerken en softwaregestuurde machines. Proceskennis is belangrijk wanneer temperatuur, druk, stroming, materiaal of productkwaliteit invloed heeft op een storing.</p>

<p>Je gebruikt niet alleen technische vakken. Ook rapporteren, plannen, risico’s beoordelen en uitleg geven aan operators of technische diensten horen bij het werk. Een goede fieldserviceprofessional kan een klacht vertalen naar een controleplan, metingen beoordelen en duidelijk onderbouwen welke ingreep nodig is.</p>

<h2>Waarom overlappen studierichtingen vaak?</h2>

<p>Moderne machines zijn zelden uitsluitend mechanisch of elektrisch. Een storing kan ontstaan door een versleten onderdeel, een verkeerde sensorwaarde, software, communicatie tussen componenten of omstandigheden in het proces. Daardoor werken verschillende opleidingen geregeld aan dezelfde installaties, maar vanuit een ander vertrekpunt.</p>

<p>Met mechatronica ben je bijvoorbeeld breed inzetbaar op het snijvlak van mechanica en elektronica. Werktuigbouwkunde kan sterker aansluiten op constructie en aandrijving, terwijl industriële automatisering zich meer richt op besturing en procesgedrag. Technische bedrijfskunde kan relevant zijn wanneer techniek wordt gecombineerd met onderhoudsprocessen, planning en verbetering. Welke achtergrond het beste past, wordt dus bepaald door de inhoud van de functie en niet alleen door de functietitel.</p>

<h2>Welke functies passen bij een technische studierichting?</h2>

<p>Een studierichting kan leiden naar werk als servicemonteur, field service engineer, commissioning engineer, application engineer, technical support engineer, site supervisor of specialist voor een bepaald product. Met ervaring kun je doorgroeien naar projectleiding, servicecoördinatie, training, engineering of service management. Sommige functies zijn vooral praktisch; andere vragen veel analyse, klantadvies of internationale verantwoordelijkheid.</p>

<p>Vergelijk daarom de opleiding met de dagelijkse werkzaamheden. Onderhoud en reparatie vragen andere accenten dan complexe commissioning, validatie of het onderzoeken van terugkerende storingen. Bekijk ook de bedrijfssector: dezelfde technische basis wordt anders toegepast in machinebouw, energie, maritieme techniek, hightech, procesindustrie of medische technologie.</p>

<h2>Hoe kies je jouw volgende stap in field service?</h2>

<p>Begin bij de kennis die jij al hebt en bepaal met welke systemen je wilt werken. Vergelijk daarna functienamen, werkzaamheden, opleidingsniveau, sector en reisprofiel. Zo zie je waar jouw opleiding direct aansluit en waar producttraining, certificering of praktijkervaring nodig is.</p>

<p>The Field Service Company koppelt actuele fieldservicevacatures aan technische studierichtingen. Daarmee kun je vanuit jouw opleiding gericht ontdekken welke functies, machines en werkgevers passen bij jouw kennis en welke richting je verder kunt ontwikkelen.</p>
HTML,
        ];

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
                array_map( 'trim', $content )
            );

            $result['saved'] = false !== $saved;

            if ( false === $saved ) {
                $result['errors'][] = 'Gesplitste archiefcontent niet opgeslagen.';
            }
        }

        update_option(
            'tfsc_split_opleiding_base_content_result',
            $result,
            false
        );

        if ( $result['saved'] ) {
            update_option(
                'tfsc_split_opleiding_base_content_version',
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
            '/split-opleiding-base-content',
            [
                'methods'             => 'GET',
                'permission_callback' => function() {
                    return current_user_can( 'manage_options' );
                },
                'callback'            => function() {
                    return rest_ensure_response(
                        get_option(
                            'tfsc_split_opleiding_base_content_result',
                            []
                        )
                    );
                },
            ]
        );
    }
);
