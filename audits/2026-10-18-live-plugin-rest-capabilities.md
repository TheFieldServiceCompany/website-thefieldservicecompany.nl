# REST-mogelijkheden van de LIVE-plugins

Datum: 18 oktober 2026  
Omgeving: `thefieldservicecompany.nl`  
Werkwijze: alleen-lezen inventaris via een WordPress Application Password

## Conclusie

Voor het dagelijkse technische beheer kan REST voortaan de standaard zijn
voor Code Snippets, Pods, Search & Filter, Elementor, Rank Math en gewone
WordPress-content. De beheerinterface blijft nodig voor visuele Elementor-
controle, licentieflows en plugins die geen beheerroute registreren. Back-up en
restore worden niet via een zelfgebouwde REST-brug geopend: daarvoor blijven
UpdraftPlus en WPvivid bewust GUI-taken.

## Beschikbare eigen REST-routes

| Plugin of onderdeel | LIVE-route | Bruikbaarheid |
| --- | --- | --- |
| Code Snippets 4.0.0 | `code-snippets/v1` | Volledig lezen, maken, wijzigen, verwijderen, activeren, deactiveren en exporteren |
| Pods 3.3.9.2 | `pods/v1` | Pods, groepen en velden lezen en beheren; inhoud loopt daarnaast via `wp/v2` |
| Search & Filter 3.2.3 | `search-filter/v1`, `search-filter-pro/v1` | Queries, velden, stijlen, instellingen, import/export en indexer beheren |
| Elementor 4.3.5 / Pro 4.3.2 | `elementor/v1`, `elementor-pro/v1` | Templates, voorwaarden, globals, settings, systeeminfo en formulierinzendingen; visuele QA blijft GUI |
| Rank Math / Pro | `rankmath/v1` en subnamespaces | Instellingen, metadata, redirects, schema, analyse, IndexNow en diverse Pro-functies |
| Wordfence 9.0.2 | `wordfence/v1`, `wordfence-login-security/v1` | Configuratie en scans; wegens veiligheidsimpact alleen na een expliciete opdracht wijzigen |
| Site Kit 1.189.0 | `google-site-kit/v1` | Module-, gebruikers- en site-instellingen en gekoppelde data; OAuth-acties voorzichtig behandelen |
| Akismet 5.7.2 | `akismet/v1` | Sleutel, instellingen en statistieken |
| SNIP Pro 2.31.8 | `wpbuddy/rich_snippets/v1` en `v2` | Schemaformulieren, properties, import/export en cachebeheer |
| WP Rollback 3.2.0 | `wp-rollback/v1` | Informatie, migraties en rollbackprocessen; alleen gebruiken na expliciet akkoord |
| TFSC Schema Read Endpoint 2.0.0 | `tfsc/v1/schema` | Bewust alleen-lezen inventaris van schema en Pods |

## Beperkte routes

| Plugin | Wat er wel beschikbaar is | Beperking |
| --- | --- | --- |
| LiteSpeed Cache 7.9.1 | `litespeed/v1` en `litespeed/v3` | Vooral crawler-, CDN- en verbindingsacties; geen volledige beheer-API voor alle cache-instellingen |
| Advanced Form Integration 2.10.3 | `afi` | Alleen inkomende webhookroutes; flows en koppelingen zijn niet via een beheerroute ontsloten |

## Geen eigen REST-namespace aangetroffen

De volgende actieve plugins registreren op LIVE momenteel geen eigen
beheer-namespace:

- Admin Columns;
- Advanced Cache Warmer;
- Duplicate Page;
- HappyFiles Pro;
- Members;
- UpdraftPlus;
- WordPress Importer;
- WPvivid Backup;
- TFSC AI Crawler Log;
- TFSC Custom Functions;
- de maatwerkplugin SNIP Job Places for TFSC.

Dat betekent niet altijd dat hun gegevens onbereikbaar zijn. HappyFiles
gebruikt bijvoorbeeld de via `wp/v2` zichtbare taxonomie
`happyfiles_category`, terwijl mediabestanden via `wp/v2/media` lopen. De
plugins hebben alleen geen eigen REST-beheerlaag voor hun volledige
functionaliteit.

## WordPress Core als beheerlaag

De beveiligde route `/wp-json/wp/v2/plugins` is beschikbaar. Daarmee kan een
beheerder plugininformatie lezen en ondersteunde pluginstatusacties uitvoeren.
Deze route vervangt geen pluginspecifieke instellingen-API. Activeren,
deactiveren, installeren of verwijderen blijft een impactvolle handeling en
gebeurt alleen binnen een expliciete opdracht.

Ook de volgende door plugins geregistreerde contenttypes zijn via `wp/v2`
bereikbaar:

- Elementor Templates (`elementor_library`);
- Elementor Custom Code (`elementor_snippet`);
- SNIP Schema Templates (`wpb-rs-global`);
- Rank Math Schema's (`rank_math_schema`);
- alle TFSC custom posttypes en taxonomieën.

## Pods: huidige REST-status

Pods gebruikt twee verschillende lagen:

1. `pods/v1` beheert de definitie van Pods, groepen en velden;
2. `wp/v2` beheert de daadwerkelijke posts en taxonomietermen.

Op LIVE hebben alle vijf Pods-posttypes en twintig Pods-taxonomieën
`rest_enable=1`. De twee Settings Pods (`pods_archief_instellingen` en
`tfsc_company_information`) hebben geen gewone publieke `wp/v2`-collectie.

Voor de Pod `vacature` staat bovendien expliciet:

- `rest_enable=1`;
- `rest_api_field_mode=value`;
- `read_all=1`;
- `write_all=1`.

Een controle op vacature 23645 bevestigde dat customvelden werkelijk in de
REST-respons staan, onder andere `vacaturenummer`, `vacature_open_tot`,
`werkuren`, `woonplaats`, `bedrijfsnaam`, salarisvelden en de gekoppelde
taxonomieën.

Bij een aantal andere Pods zijn `read_all` en `write_all` niet expliciet
opgeslagen. Hun posttype of taxonomie is wel REST-enabled, maar vóór een
schrijfoperatie controleren we per veld of lezen en schrijven zijn toegestaan.
Dit voorkomt dat een globale Pods-wijziging onbedoeld velden openbaar of
schrijfbaar maakt.

## Werkafspraak

- **REST eerst:** Code Snippets, Pods-structuur, WordPress-content en
  taxonomieën, Search & Filter, ondersteunde Rank Math-instellingen en
  niet-visuele Elementor-data.
- **GUI voor controle:** Elementor-layout, licentieautorisaties en processen
  waarbij een visuele bevestiging nodig is.
- **GUI voor back-ups:** UpdraftPlus en WPvivid; geen maatwerk-REST-endpoint
  toevoegen voor backup of restore.
- **Beveiligingsplugins:** Wordfence alleen wijzigen na expliciet akkoord.
- **Vooraf verifiëren:** bij iedere nog niet gebruikte REST-route eerst GET of
  schema inspecteren, daarna een kleine wijziging met herstelmogelijkheid.

Er zijn tijdens deze inventarisatie geen instellingen gewijzigd.
