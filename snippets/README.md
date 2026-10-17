# Eigen code en snippets

Inventarisdatum: 17 oktober 2026. Deze documentatie beschrijft de aangetroffen
code en de onderlinge afhankelijkheden. De bestanden worden niet automatisch
naar WordPress uitgerold.

## Systeemoverzicht

| Onderdeel | Status | Functie | Belangrijkste afhankelijkheden |
| --- | --- | --- | --- |
| Code Snippet 100 | Actief | Publieke taxonomietermen toevoegen aan de WordPress-linkzoeker | WordPress term-API en native term-URL's |
| Code Snippet 39 | Actief | Prefixen uit Elementor-archieftitels verwijderen | Elementor |
| Code Snippet 42 | Inactief | In Pods Admin tonen aan welke posttypes een taxonomie gekoppeld is | Pods en wp-admin DOM |
| Code Snippet 49 | Actief, maar verouderde CPT-naam | Contextclasses voor het generieke Beroepengids-archief | Queryvar `tfsc_taxonomy_archive`; controleert nog `beroep` in plaats van `beroepengids` |
| Code Snippet 62 | Actief | Elementor-taxonomielussen filteren op huidige taxonomie en hiërarchieniveau | Elementor Loop Taxonomy en `tfsc_taxonomy_archive` |
| Code Snippet 9 | Actief | ALT- en title-attribuut van de uitgelichte afbeelding gelijkmaken aan de berichttitel | WordPress afbeeldingen |
| Code Snippet 99 | Actief maar inert | Rank Math-breadcrumbs voor samengestelde vacaturearchieven | Ontbrekende functies `tfsc_is_combo_archive()` en `tfsc_get_combo_archive()` plus `TFSC_COMBO_BASE` |
| `snip-tfsc` | Actief | Google Jobs/SNIP-locatieobjecten maken vanuit WordPress-locatietermen | SNIP Pro, taxonomie `locatie` en termmeta |
| `tfsc-custom-functions` | Actief | Globale frontendaanpassingen, uploads als e-mailbijlage, styles en shortcodes | Elementor Pro, Rank Math en lokale assets |
| `tfsc-schema-read-endpoint` | Actief | Alleen-lezen inventaris van posttypes, taxonomieën, termen en Pods | REST-authenticatie en Pods |
| Elementor Custom Code | Actief volgens export | Adviesknop, Clarity en EngageBay-tracking | Elementor DOM en externe trackingdiensten |

## Code Snippets

### 100 — WordPress-linkzoeker voor taxonomietermen

Filtert `wp_link_query`. Bij een zoekopdracht worden maximaal 50 termen uit
alle publieke en publiek opvraagbare taxonomieën toegevoegd. `post_format`
wordt uitgesloten. Via de globale vlag `tfsc_force_native_term_link` vraagt de
snippet om een native term-URL in plaats van een virtuele combinatie-URL.

De code die deze vlag daadwerkelijk verwerkt is niet in de repository
aangetroffen. De zoekresultaten zelf blijven bruikbaar, maar deze afhankelijkheid
moet worden teruggevonden of opnieuw ontworpen.

### 39 — Archieftitel zonder WordPress-prefix

Filtert `elementor/utils/get_the_archive_title` en retourneert alleen de naam
voor categorieën, tags, taxonomiearchieven en posttypearchieven. Daardoor toont
Elementor geen prefixen zoals “Categorie:” of “Archieven:”.

### 42 — Connecties in Pods Admin

Alleen bedoeld voor `wp-admin/admin.php?page=pods`. De snippet leest voor iedere
geregistreerde taxonomie de gekoppelde posttypes uit `object_type` en voegt via
JavaScript een visuele kolom **Connecties** toe aan het Pods-overzicht.

De snippet verandert geen Pods-configuratie. Hij was bij export inactief.

### 49 — Beroepengids-archivecontext

Voegt bodyclasses toe zodat één Elementor-archieftemplate verschillend kan
reageren op:

- het Beroepengids-posttypearchief;
- een generiek taxonomiearchief via `tfsc_taxonomy_archive`.

De posttypecontrole gebruikt nog `beroep`, terwijl de huidige CPT
`beroepengids` heet. Die tak werkt daardoor momenteel niet voor het actuele
posttype. De taxonomietak werkt alleen wanneer een andere component de queryvar
zet.

### 62 — Elementor-taxonomielussen

Ondersteunt de volgende Elementor Query ID's:

- `taxonomy_level_X`: toon termen van exact hiërarchieniveau X;
- `current_taxonomy`: toon alle termen van de taxonomie uit de huidige context;
- `current_taxonomy_level_X`: toon alleen niveau X van die huidige taxonomie.

Het niveau wordt bepaald door het aantal voorouders van een term. Bij een
ongeldige of lege context wordt `include = [0]` gebruikt, zodat de loop bewust
geen willekeurige termen toont. De actuele taxonomie komt uit de queryvar
`tfsc_taxonomy_archive`.

### 9 — ALT en title voor uitgelichte afbeelding

Alleen op een enkel bericht wordt gecontroleerd of de geladen afbeelding de
uitgelichte afbeelding is. In dat geval worden het ALT- en HTML-title-attribuut
vervangen door de titel van het huidige bericht.

Dit is een generieke fallback. Bij een latere toegankelijkheidscontrole moet
worden besloten of een redactionele ALT-tekst voor sommige afbeeldingen beter
is dan automatisch de berichttitel.

### 99 — Rank Math-breadcrumbs voor combinatiearchieven

Bedoeld voor paden als:

`Home > Vacatures > Field Service Engineer > Elektrotechniek`

De URL wordt opgebouwd uit de taxonomienaam en de volledige termhiërarchie.
De code stopt nu direct, omdat de vereiste combo-archieffuncties en constante
niet actief in de repository aanwezig zijn. De snippet verandert de normale
breadcrumbs daardoor momenteel niet.

## Plugin `snip-tfsc`

Deze plugin voegt twee dynamische SNIP-velden toe:

- **PODs Places** voor Schema.org `Place`/`PostalAddress`;
- **PODs Areas** voor Schema.org `AdministrativeArea`.

Beide lezen de aan een vacature gekoppelde termen uit taxonomie `locatie`.
Voor iedere term kunnen deze termmetavelden worden gebruikt:

| WordPress-termmeta | Schema.org-veld |
| --- | --- |
| `streetaddress` | `streetAddress` |
| `addresslocality` | `addressLocality` |
| `addressregion` | `addressRegion` |
| `postalcode` | `postalCode` |
| `addresscountry` | `addressCountry` |

Een term met uitsluitend `addressregion` wordt als provincie/staat gezien. Een
term met uitsluitend `addresscountry` wordt als land gezien. Andere termen
worden als plaats/adres uitgevoerd.

`PODs Areas` produceert alleen land- en provincieobjecten wanneer ook de term
`TELECOMMUTE` aan de vacature is gekoppeld. Dit is de huidige implementatie en
niet automatisch het gewenste toekomstige model.

### Gevolg voor het nieuwe locatiemodel

De plugin gebruikt nu één taxonomie voor verschillende betekenissen. Hij maakt
nog geen expliciet onderscheid tussen:

1. het adres van het bedrijf;
2. de feitelijke standplaats of werklocatie van de vacature;
3. het land en de provincie van de vacature;
4. de toegestane woon- of wervingsregio met bijbehorende plaatsen en postcodes.

Dit onderscheid moet eerst in het datamodel en de mapping worden vastgelegd.
Daarna kan de structured data uit de juiste relatie worden opgebouwd.

## Plugin `tfsc-custom-functions`

Deze plugin is de functionele code-laag die normaal in een child theme zou
staan.

### Frontend-assets en prestaties

- schakelt externe Elementor Google Fonts uit;
- deregistreert Elementor Font Awesome-stijlen;
- verwijdert Gutenberg Block Library CSS;
- laadt `tfsc-custom-stylesheet.css` als laatste stylesheet;
- preloadt twee lokale Inter-fontbestanden;
- preloadt alle op dat moment geregistreerde JavaScriptbestanden.

De Google Fonts-filter staat dubbel geregistreerd. Het globaal verwijderen van
Font Awesome en block CSS kan onderdelen breken. Het preloaden van alle scripts
kan juist extra belasting veroorzaken. Dit moet later gericht worden getest en
opgeschoond.

### Elementor-formulieruploads

- bewaart voor `cv_upload` de oorspronkelijke bestandsnaam;
- voegt geüploade bestanden toe als e-mailbijlage;
- verwijdert bestanden niet na verzending, omdat
  `DELETE_ATTACHMENT_FROM_SERVER` op `false` staat;
- de markering `[elementor_form_no_attachment]` voorkomt een bijlage voor een
  specifieke e-mailactie.

De huidige code probeert per upload altijd twintig posities te lezen en
controleert `$_FILES` onvoldoende. Dit is bestaande technische schuld. Omdat
lokale opslag en gegevensbehoud belangrijk zijn, mag dit pas na een back-up en
gerichte regressietest worden aangepast.

### Overige functionaliteit

- aangepaste Elementor-zoekresultaattitel;
- shortcode `[rm_get_estimated_time_to_read]` op basis van 200 woorden per
  minuut;
- shortcode `[current_page_url]` voor de huidige URL;
- centrale frontend-CSS voor typografie, knoppen, taxonomietermen,
  Beroepengids-/vacaturearchieven, breadcrumbs, formulieren, privacycheckboxes,
  pop-ups, paginering en responsive gedrag.

Bekende CSS-punten zijn de selector `grecaptcha-badge` zonder punt en een
kapotte commentaargrens bij het footerblok.

Het losse bestand `custom-stylesheet-wp-admin.css` verhoogt uitsluitend de
zichtbare hoogte van de locatie-taxonomiebox in wp-admin. In de PHP-plugin is
geen enqueue van dit adminbestand aangetroffen.

## Plugin `tfsc-schema-read-endpoint`

Registreert `GET /wp-json/tfsc/v1/schema` voor ingelogde gebruikers met de
capability `read`. De respons bevat:

- WordPress-versie;
- alle geregistreerde posttypes en gekoppelde taxonomieën;
- alle taxonomieën, termen, hiërarchie en gebruiksaantallen;
- genormaliseerde Pods-structuren en relevante relatie-instellingen;
- technische debugstatus van de Pods-uitlezing.

Het endpoint wijzigt niets. Na afronding van de inventaris moet de toegang
worden beperkt tot `manage_options`, moeten debugdetails verdwijnen of kan het
endpoint geheel worden verwijderd.

## Elementor Custom Code

### Adviesknop

Zoekt op een vacaturepagina het vacaturenummer, de bedrijfsnaam,
contractvorm, paginatitel en URL in de gerenderde Elementor-HTML. Bij een klik
op `.button-advies` worden deze waarden als queryparameters aan het
adviesformulier meegegeven.

De geëxporteerde code bevat een hardcoded URL naar de dev-site en encodeert de
queryparameterwaarden niet met `encodeURIComponent`. Voor gebruik op productie
moeten beide punten worden opgelost en getest.

### Tracking

- Microsoft Clarity wordt in de `<head>` geladen.
- EngageBay client-side tracking is als template zonder accountidentificatoren
  opgeslagen. Formulierintegraties lopen afzonderlijk via Advanced Form
  Integration.

## Wijzigingsregels

Bij iedere latere wijziging leggen we vast:

- welk functioneel probleem wordt opgelost;
- welke templates, taxonomieën, plugins en externe diensten afhankelijk zijn;
- welke oude en nieuwe URL- of datalogica geldt;
- hoe de wijziging op desktop, mobiel, formulieren, SEO en structured data is
  getest;
- welke rollback beschikbaar is.
