# Beheer en livegang 0.4.0 — 1 november 2026

## Resultaat

Na de opdracht de PHP-problemen op te lossen en de bevestiging “top gaan” is 0.4.0 op de website geplaatst. Pluginbestanden en Elementor-kit 9367 zijn samen bijgewerkt. De verwachte bestandshashes, kitinstellingen en actieve pluginversie zijn teruggelezen.

De eerste installatiepoging liep via de gewone netwerkroute vast vóór de helper werd aangemaakt. De bestaande TFSC-originroute via `45.82.188.97`, met HTTPS-hostnaam en certificaatcontrole behouden, gaf wel antwoord. Via die bestaande route is de installatie uitgevoerd. Er is niets aan DNS, firewall of browserbeleid gewijzigd.

## Eén beheerplek per rol

1. **Global Colors:** CSS gebruikt Elementor-variabelen voor kleuren die exact in het bestaande palet voorkomen. Afwijkende oude componentkleuren zijn niet naar een ongeveer gelijkende kleur omgezet.
2. **Typography:** de effectieve waarden zijn in Site Settings vastgelegd: basistekst 18/30 px, mobiel 17/28 px; H1 40 px en op tablet/mobiel 30 px; H2 24 px, H3 20 px en de bestaande kleine kopmaten. H2/H3 blijven gewicht 400, zoals de bestaande effectieve weergave.
3. **Global Fonts:** de werkelijke families zijn Inter Tight en Barlow Semi Condensed. Oude fontaliassen, Inter en een niet-actieve gewicht-800-definitie zijn gecorrigeerd. De gegenereerde kit-CSS bevat nu de fontfamilievariabelen.
4. **Plugin-CSS:** componenten en bewuste afwijkingen blijven hier: knoppen, formulieren, lijsten, tabellen, focus en artikel-H2/H3 van 26/22 px. Dubbele algemene kopgroottes zijn verwijderd.
5. **PHP:** geen eigen kleurwaarden of vaste fontpreloads meer. De zoekresultatentitel gebruikt `.tfsc-search-query`; de accentkleur komt uit het Elementor-palet via CSS.

Dit herschrijft niet alle individuele widgetinstellingen. Een bewuste lokale waarde van een widget blijft bestaan. Nieuwe algemene aanpassingen horen op de hierboven genoemde plek, zodat nieuwe dubbele regels worden voorkomen.

## Plaatsing en herstel

Vooraf zijn alle drie de live pluginbestanden en complete kitinstellingen geëxporteerd. Bij vervangen is gecontroleerd dat bestanden en kit sinds die back-up niet gewijzigd waren. Alleen de twee vaste pluginbestanden en toegestane typografiesleutels konden veranderen. De server controleerde de PHP-syntaxis vóór schrijven; bestanden zijn via tijdelijke bestanden in dezelfde pluginmap vervangen. Het hulpmiddel bevatte herstel bij een mislukte stap.

Elementor heeft de instellingen via de document-save verwerkt. De kit-CSS is opnieuw gegenereerd, de Elementor-bestandscache vernieuwd en LiteSpeed gepurged. Het tijdelijke hulpmiddel is verwijderd; de route gaf daarna HTTP 404.

De oude niet-geladen `custom-stylesheet-wp-admin.css` is zonder activering op de server gebleven. Het bestand staat ook in de back-up.

## Controles

- Vooraf: 18 PHP-controles en 13 controles met echte WordPress-hulpfuncties geslaagd.
- Vooraf: 12 voor/na-paren op 1280 × 720 en 390 × 844, samen 24 ladingen. Geen onverwachte wijzigingen in gemeten kleuren, groottes, gewichten of regelhoogtes; bedoelde lijstwijzigingen uitgezonderd. Geen overloop na reparatie. Eerste fontfamilie gelijk; fallbacklijsten kunnen verschillen.
- Productie: WordPress 7.1.4 bevestigt actieve versie 0.4.0; de CSS-hash komt overeen met het geplaatste bestand.
- Zes openbare pagina’s geven HTTP 200 zonder zichtbare PHP-fout en laden de nieuwe LiteSpeed-CSS.
- Echte browser: formulier behoudt Inter Tight/Barlow; een checkbox krijgt toetsenbordfocus met zichtbare rand. Geen formulier verzonden.
- Echte browser: Kennisbank op 390 px, documentbreedte 375 px inclusief ruimte voor de scrollbar; vier tabellen met eigen scrollkader van 355 px.
- Echte browser: WordPress-blokbibliotheek laadt weer, mobiel Elementor-menu opent en sluit, Meer toont extra loop-items (52 → 54).

Niet ieder formulier, filter, popup of iedere oude pagina is doorlopen. Er zijn geen artikelen, vacatures, formulieren of inzendingen inhoudelijk gewijzigd.

## Bewijs en herstelbestanden

`0.4.0/` bevat de kitpatch, plaatsingshashes, gezondheidscontrole, browserresultaten en voorafcontrole. Originele bestanden en kitinstellingen staan onder `wordpress/exports/plugins/2026-11-01-before-custom-functions-0.4.0/`.

Bij exact terugzetten moeten pluginbestanden en bijbehorende Elementor-instellingen worden hersteld. Controleer eerst op latere wijzigingen en herstel geen andere kitinstellingen blind. Genereer daarna de kit-CSS opnieuw en vernieuw Elementor-/LiteSpeed-/CDN-cache.
