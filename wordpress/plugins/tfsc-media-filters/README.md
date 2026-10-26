# TFSC Mediafilters 1.0.0

WordPress-plugin om bestanden in de mediabibliotheek te vinden op extensie,
pixelbreedte, pixelhoogte, bestandsgrootte, oriëntatie en alt-tekst.

## Installatie

1. Upload `tfsc-media-filters-1.0.0.zip` via **Plugins → Nieuwe plugin → Plugin uploaden**.
2. Activeer **TFSC Mediafilters**.
3. Open **Media → Mediafilters** en klik op **Index starten / hervatten**.
4. Laat dat scherm open tot de index klaar is. Je kunt een onderbroken ronde hervatten.
5. Open **Media → Bibliotheek**. Selecteer eventueel een HappyFiles-map en klik op **Extra filters**.

Bestanden in deze repository worden niet automatisch op de website geïnstalleerd.
WordPress 6.4+ en PHP 7.4+ zijn vereist. Gebruik een actuele browser.

## Filters

| Filter | Gedrag |
| --- | --- |
| Extensies | Eén of meer daadwerkelijke bestandsextensies, gescheiden door komma's. Bijvoorbeeld `jpg,jpeg,webp`. Hoofdletters en een voorlooppunt zijn toegestaan. `jpg` en `jpeg` zijn afzonderlijke extensies. |
| Breedte / hoogte | Inclusieve minimum- en maximumgrenzen in pixels. Zelfde minimum en maximum geeft een exacte maat. |
| Bestandsgrootte | Inclusieve grenzen in KB, met 1024 bytes per KB; decimalen toegestaan. |
| Oriëntatie | Liggend, staand of exact vierkant, op basis van beschikbare breedte en hoogte. |
| Alt-tekst | Alleen afbeeldingen; ontbrekend, leeg of alleen spaties tegenover ingevulde alt-tekst. Lege alt-tekst kan bewust gekozen zijn voor decoratieve afbeeldingen. |

De filters worden gecombineerd met **EN**. Binnen de extensielijst geldt **OF**.
Leeggelaten velden leggen geen grens op. Reset wist alleen de TFSC-filters.

Afmetingen gelden voor het volledige bestand zoals WordPress dat beheert. Bij
automatisch verkleinde uploads is dat dus de `-scaled`-versie. Gegenereerde
thumbnails worden niet als losse mediabestanden doorzocht. Breedte en hoogte
werken ook voor video's wanneer WordPress deze metadata heeft opgeslagen.
SVG's hebben doorgaans geen pixelmetadata en vallen dan buiten maatfilters.

Een onbekende afmeting of bestandsgrootte wordt niet behandeld als nul. Voor
externe opslag is bestandsgrootte beschikbaar wanneer die in de WordPress-metadata
staat. De plugin downloadt geen externe bestanden om ontbrekende waarden te vinden.
Na optimalisatie of vervanging buiten de WordPress-metadata om kun je opnieuw indexeren.

## HappyFiles

De plugin voegt alleen eigen voorwaarden toe aan de bestaande WordPress-query.
Hij vervangt geen `tax_query`, `meta_query`, `post__in`, zoekterm, datum,
bestandssoort of paginering. Daardoor worden de filters toegepast binnen de door
HappyFiles geselecteerde map. HappyFiles bepaalt of submappen meetellen en wie
welke bestanden kan zien. Reset verandert de folderselectie niet.

De interface gebruikt de standaard WordPress-mediabibliotheek in raster- en
lijstweergave en de standaard mediakiezer in editors. Een mediakiezer van een
derde partij die de standaard WordPress-weergaven vervangt kan extra integratie
vereisen. HappyFiles is optioneel; de plugin functioneert ook zonder HappyFiles.

De combinatie met HappyFiles' taxonomiemodel is met integratietests gecontroleerd.
De commerciële HappyFiles-plugin zelf en de doelwebsite waren geen onderdeel van
de lokale testomgeving. Controleer de geïnstalleerde HappyFiles-versie op de
dev-site voordat je de plugin op productie gebruikt.

## Index en onderhoud

- Een eigen tabel `{prefix}tfsc_media_index` bewaart één regel per mediabestand,
  met afzonderlijke indexen voor extensie, breedte, hoogte, bestandsgrootte en oriëntatie.
- De eerste index en herindexering lopen in batches van maximaal 100 bestanden,
  met een opgeslagen voortgang en een slot tegen gelijktijdige indexrondes.
- Nieuwe uploads, gewijzigde bestandsverwijzingen en gewijzigde metadata worden
  automatisch bijgewerkt. Alt-tekst wordt rechtstreeks uit WordPress gelezen.
- Tijdens indexeren zijn extra filters niet beschikbaar; de gewone bibliotheek
  blijft bruikbaar. Zo worden onvolledige resultaten niet als volledig gepresenteerd.
- Alleen beheerders met `manage_options` kunnen indexeren; filteren vereist `upload_files`.
- De plugin verandert geen afbeeldingen, folders, URLs of originele metadata.
- Deactiveren stopt de plugin en behoudt de index. Verwijderen via WordPress ruimt
  alleen de eigen tabel en opties op, ook op multisite. HappyFiles-termen blijven bestaan.
- Bij multisite maakt elke site de eigen index aan zodra een beheerder het dashboard opent.

## Validatie

Lokale integratietests op WordPress 6.8.3 / PHP 8.3.30 / MySQL 8.0.35:
40 controles voor gecombineerde filters, foldervoorwaarden, submappen, bestaande
OR-queries, paginering/tellingen, foutieve invoer, rechten, indexhervatting,
uploads tijdens indexeren, beeldbewerkingen, bestandsvervanging en verwijderen.

Een lokale browsertest controleerde daarnaast rasterweergave (2 passende
afbeeldingen), de standaard mediakiezer (1 vierkante afbeelding), lijstweergave
(1 passende afbeelding), reset, indexeren via AJAX en afwijzing van een ongeldige
beveiligingscode (HTTP 403). Er waren geen JavaScript-fouten.

De test in `tests/integration.php` vereist een **wegwerpinstallatie** met
`define('TFSC_MEDIA_FILTERS_TESTS', true)` in `wp-config.php`:

```sh
TFSC_TEST_WP_LOAD=/pad/naar/test-wordpress/wp-load.php php tests/integration.php
```

Uitvoerlocatie: uitsluitend WordPress-beheer; er is geen frontendfunctionaliteit.
Afhankelijkheden: WordPress; HappyFiles optioneel. WordPress-snippet-ID: niet van
toepassing, dit is een zelfstandige plugin.

## Wijzigingslog

- 2026-10-26 — 1.0.0: eerste versie op verzoek van Rudi, inclusief filters binnen
  HappyFiles-mappen, media-index en installatiepakket.

## Technische bronnen

- [WordPress AJAX attachment-query](https://developer.wordpress.org/reference/hooks/ajax_query_attachments_args/)
- [WordPress attachment-metadata](https://developer.wordpress.org/reference/functions/wp_get_attachment_metadata/)
- [HappyFiles: folders als taxonomy terms](https://happyfiles.io/#faq)
