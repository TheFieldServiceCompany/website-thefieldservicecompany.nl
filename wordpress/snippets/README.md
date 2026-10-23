# WordPress-snippets

Inventarisdatum: 17 oktober 2026. Deze map is de beheerde bron voor losse
WordPress-snippets. De documentatie beschrijft de aangetroffen code en de
onderlinge afhankelijkheden. Bestanden worden niet automatisch naar WordPress
uitgerold; gedateerde exports staan apart onder `wordpress/exports/code-snippets/`.

## Systeemoverzicht

| Onderdeel | Status | Functie | Belangrijkste afhankelijkheden |
| --- | --- | --- | --- |
| Code Snippet 100 | Actief | Publieke taxonomietermen toevoegen aan de WordPress-linkzoeker | WordPress term-API en native term-URL's |
| Code Snippet 39 | Actief, versie 2.0 | Prefixen uit Elementor-archieftitels verwijderen en optioneel `pods_archive_title` gebruiken | Elementor en WordPress-termmeta |
| Code Snippet 42 | Inactief | In Pods Admin tonen aan welke posttypes een taxonomie gekoppeld is | Pods en wp-admin DOM |
| Code Snippet 49 | Actief en gecorrigeerd | Contextclasses voor het generieke Beroepengids-archief | Queryvar `tfsc_taxonomy_archive`; LIVE-CPT-controle op `beroepengids` |
| Code Snippet 62 | Actief, versie 2.0 | Elementor-taxonomielussen filteren op huidige taxonomie en hiërarchieniveau | Elementor Loop Taxonomy en `tfsc_taxonomy_archive` |
| Code Snippet 113 | Actief, versie 3.2 | Virtuele basisarchieven voor Beroepengids-taxonomieën | Rewrite- en querylogica voor `tfsc_taxonomy_archive` |
| Code Snippet 9 | Actief | ALT- en title-attribuut van de uitgelichte afbeelding gelijkmaken aan de berichttitel | WordPress afbeeldingen |
| Code Snippet 99 | Actief | Rank Math-breadcrumbs voor samengestelde vacaturearchieven | Snippet 186 levert `tfsc_is_combo_archive()`, `tfsc_get_combo_archive()` en `TFSC_COMBO_BASE` |
| Code Snippet 148 | Actief | Custom Rank Math-sitemaps voor indexeerbare vacaturecombinaties | Snippets 186 en 193, Rank Math |
| Code Snippet 186 | Actief | Virtuele vacature-comboarchieven, rewrites, queries, paginering en Search & Filter-synchronisatie | WordPress, Elementor Pro en Search & Filter Query 1 |
| Code Snippet 193 | Actief | SEO, robots, canonical, metadata en indexeringsbesluit voor combo- en filter-URL's | Snippet 186 en Rank Math |
| Code Snippet 204 | Actief, versie 3.1 | Elementor-postqueries filteren via Pods-relaties zonder bestaande Elementor-filters te overschrijven | Pods, Elementor Query ID `relationship_posts` en taxonomieën van de gekozen Source |
| Code Snippet 209 | Actief, versie 1.2 | Unieke Rank Math-title en description voor de twee hoofdarchieven en de zeven virtuele Beroepengids-basearchieven | Rank Math en queryvar `tfsc_taxonomy_archive` |
| Nieuwe snippet, ID nog niet toegewezen | Repository-only | Laat single vacatures zonder handmatige SEO-description de centrale Rank Math-description gebruiken, ook wanneer een WordPress-excerpt bestaat | Rank Math en posttype `vacature` |
| Code Snippet 215 | Actief, versie 1.2 | Beroepengids-basearchieven en de twee native opleidingstaxonomysitemaps aan de Rank Math-sitemapindex toevoegen | Rank Math Sitemap API |
| `snip-tfsc` | Actief | Google Jobs/SNIP-locatieobjecten maken vanuit WordPress-locatietermen | SNIP Pro, taxonomie `locatie` en termmeta |
| `tfsc-custom-functions` | Actief | Globale frontendaanpassingen, uploads als e-mailbijlage, styles en shortcodes | Elementor Pro, Rank Math en lokale assets |
| `tfsc-schema-read-endpoint` | Actief | Alleen-lezen inventaris van posttypes, taxonomieën, termen en Pods | REST-authenticatie en Pods |
| Elementor Custom Code | Actief volgens export | Adviesknop, Clarity en EngageBay-tracking | Elementor DOM en externe trackingdiensten |

## Code Snippets

### Nog niet toegewezen — centrale Rank Math-description voor vacatures

`wordpress/snippets/php/tfsc-rank-math-central-vacature-description.php`
corrigeert de description-prioriteit uitsluitend voor individuele vacatures.
Rank Math 1.0.280 gebruikt intern eerst een handmatige SEO-description, daarna
het opgeslagen WordPress-excerpt en pas daarna de centrale template uit
**Titles & Meta**. Daardoor kon een vacature zonder handmatige SEO-description
toch de centrale vacaturetemplate overslaan.

De filter respecteert een werkelijk handmatig ingevulde Rank Math-description.
Als die ontbreekt, leest hij de actuele centrale instelling
`titles.pt_vacature_description` en laat hij Rank Math de variabelen vervangen.
Er staat geen vacaturetekst hardcoded in de snippet.

Op 23 oktober 2026 is gecontroleerd dat dit gedrag nog aanwezig is in de
actuele Rank Math Free-versie 1.0.280 en op de `master`-branch. De snippet is
in de repository vastgelegd, maar heeft nog geen WordPress Code Snippets-ID en
is nog niet naar LIVE uitgerold. Na uitrol moeten minimaal de HTML
`meta[name="description"]`, Open Graph/Twitter-fallbacks en LiteSpeed-cache
worden gecontroleerd op een vacature met en zonder handmatige SEO-description.

### 100 — WordPress-linkzoeker voor taxonomietermen

Filtert `wp_link_query`. Bij een zoekopdracht worden maximaal 50 termen uit
alle publieke en publiek opvraagbare taxonomieën toegevoegd. `post_format`
wordt uitgesloten. Via de globale vlag `tfsc_force_native_term_link` vraagt de
snippet om een native term-URL in plaats van een virtuele combinatie-URL.

De actieve snippets 188, 189 en 190 respecteren deze vlag wanneer zij normale
term-URL's vervangen door vacature-combo-URL's. De linkzoeker kan daardoor
bewust de oorspronkelijke WordPress-term-URL blijven tonen.

### 39 — Archieftitel zonder WordPress-prefix en met Pods-override

Filtert `elementor/utils/get_the_archive_title` en retourneert alleen de naam
voor categorieën, tags, taxonomiearchieven en posttypearchieven. Daardoor toont
Elementor geen prefixen zoals “Categorie:” of “Archieven:”.

Sinds 18 oktober 2026 leest versie 2.0 op gewone custom-taxonomytermarchieven
eerst het termmetaveld `pods_archive_title`. Een ingevulde, opgeschoonde waarde
wordt de zichtbare Elementor-archieftitel; als het veld ontbreekt of leeg is,
blijft de normale termnaam de fallback. De termnaam, slug, URL, breadcrumb en
Rank Math SEO-titel worden niet gewijzigd.

De actieve beroepengids-taxonomie-Pods hebben hiervoor ieder een groep
`Archive` met een Plain Text-veld `pods_archive_title`. Snippet 232 is de
inactieve herstelkopie van de voorafgaande versie. De tijdelijke functionele
testsnippet 233 staat eveneens inactief.

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

De posttypecontrole gebruikte `beroep`, terwijl de huidige CPT `beroepengids`
heet. Dit is op 17 oktober 2026 op LIVE gecorrigeerd en functioneel hertest.
Het CPT-archief krijgt nu de class `tfsc-archive-beroepen`. De taxonomietak
werkt wanneer een andere component de queryvar zet en bleef na de wijziging
correct de class `tfsc-archive-taxonomy` toevoegen.

### 62 — Elementor-taxonomielussen

Ondersteunt de volgende Elementor Query ID's:

- `taxonomy_level_X`: toon termen van exact hiërarchieniveau X;
- `current_taxonomy`: toon alle termen van de taxonomie uit de huidige context;
- `current_taxonomy_level_X`: toon alleen niveau X van die huidige taxonomie;
- `current_term_children`: toon uitsluitend de directe kinderen van de huidige
  term.

Het niveau wordt bepaald door het aantal voorouders van een term. Bij een
ongeldige of lege context wordt `include = [0]` gebruikt, zodat de loop bewust
geen willekeurige termen toont. De actuele taxonomie komt uit de queryvar
`tfsc_taxonomy_archive`.

De tijdelijke directe-kinderenuitbreiding voor het eerste, gecombineerde
Opleidingenmodel is op 18 oktober 2026 weer verwijderd. De definitieve
structuur gebruikt twee zelfstandige taxonomieën en kan met de bestaande
generieke queries werken. De actieve LIVE-versie is 2.0; snippet 222 is de
inactieve herstelkopie van vóór deze controle.

### 113 — Virtuele Beroepengids-taxonomy-basearchieven

Registreert de toegestane taxonomieën voor virtuele basisroutes onder
`/beroepengids/` en zet de queryvar `tfsc_taxonomy_archive`. Op 18 oktober 2026
zijn `beroep_opleidingsniveau` en `beroep_studierichting` aan de allowlist
toegevoegd. Zij leveren de basisroutes `/beroepengids/opleidingsniveaus/` en
`/beroepengids/studierichtingen/`. De gecombineerde `beroep_opleiding`-route
wordt niet meer door deze snippet gepubliceerd. De actieve LIVE-versie is 3.2;
snippet 223 is de inactieve herstelkopie.

### 204 — Gerelateerde posts via Pods-relaties

Haakt in op Elementor Query ID `relationship_posts`. De snippet leest alle
Pods-velden met prefix `relationship_`, normaliseert de gekoppelde termen en
gebruikt alleen taxonomieën die bij de in Elementor gekozen Source horen.
Verschillende relatietaxonomieën worden met `AND` gecombineerd; meerdere
termen binnen één taxonomie gebruiken `IN`.

Versie 3.0 verving aan het einde de volledige bestaande `tax_query`. Daardoor
verdwenen filters die in Elementor zelf waren ingesteld, waaronder de
uitsluiting van vacatures met `vac_status = Ingevuld`. Versie 3.1 behoudt de
bestaande Elementor-query en plaatst die samen met de relatiequery in een
nieuwe bovenliggende `AND`-groep. Zo moeten vacatures zowel aan de Elementor-
voorwaarden als aan de gekozen relaties voldoen.

De actieve LIVE-snippet is 204. Snippet 240 is de inactieve herstelkopie van
versie 3.0. De correctie is functioneel getest op de archieven Maritieme
techniek en MBO: de ingevulde testvacature verdween en gekoppelde open
vacatures bleven staan.

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
De vereiste functies en constante worden op LIVE geleverd door actieve snippet
186. De breadcrumbfunctie is daardoor niet inert; hij moet samen met de
combo-archieflogica worden getest.

### 209 — TFSC SEO Beroepengids en Kennisbank

`wordpress/snippets/php/tfsc-seo-beroepengids-kennisbank-archives.php` bevat een strikt
afgebakende mapping voor de gewone Beroepengids-pagina, het Kennisbank-CPT-
archief en de virtuele Beroepengids-taxonomy-base-archieven. De code
overschrijft uitsluitend Rank Math-title en description, plus dezelfde waarden
voor Open Graph en Twitter. Canonicals, robots, sitemaps, vacature-combo's,
gewone termarchieven en zichtbare pagina-inhoud blijven ongemoeid.

De code staat in de repository en is na expliciete goedkeuring op LIVE als
snippet 209 geactiveerd. Alle acht HTML-titles, meta descriptions, Open Graph-
waarden en Twitter-waarden zijn daarna exact teruggelezen. Een vacaturearchief
en een normaal Beroepengids-termarchief zijn als afbakeningscontrole getest en
behielden hun bestaande metadata.

Op 18 oktober 2026 zijn afzonderlijke mappings toegevoegd voor
`beroep_opleidingsniveau` en `beroep_studierichting`. De actieve LIVE-versie is
1.2; snippet 224 is de inactieve herstelkopie.

Tijdens deze controle bleef de algemene onderhoudsblokkade actief. Daarom
tonen de pagina's nog `noindex, nofollow` en geen canonical; snippet 209
verandert die waarden niet. Canonicals en indexeerbaarheid worden opnieuw
gecontroleerd wanneer de onderhoudsmodus en WordPress-zoekmachineblokkade bij
de definitieve livegang worden opgeheven.

### 215 — Beroepengidsarchieven in de Rank Math-sitemap

Voegt de toegestane virtuele Beroepengids-basisroutes toe aan de eerste
page-sitemap. Versie 1.2 koppelt daarnaast de native sitemaps voor
`beroep_opleidingsniveau` en `beroep_studierichting` expliciet en zonder
duplicaten aan de sitemapindex. Snippet 225 is de inactieve herstelkopie.

De twee sitemap-URL's staan in de index. De directe XML-generatie van de
term-URL's wacht nog op de definitieve Rank Math-instelling **lege termen
opnemen** voor beide taxonomieën; dit is als open controlepunt vastgelegd en
wordt niet als afgerond gemeld.

### 148, 186 en 193 — vacature-comboarchieven en sitemap

Deze drie actieve LIVE-snippets vormen één functionele keten:

1. **186 — Base Code** registreert virtuele paden onder `/vacatures/`, leest
   taxonomie- en termsegmenten, bouwt de vacaturequery, beperkt de resultaten
   tot gepubliceerde vacatures met status `open`, verzorgt paginering en
   synchroniseert Search & Filter Query 1.
2. **193 — SEO & Canonical** bepaalt per combo de titel, beschrijving, robots,
   canonical, Open Graph-URL en pagineringslinks. De centrale functie
   `tfsc_combo_should_index()` wordt ook door de sitemap gebruikt.
3. **148 — Sitemap** maakt de Rank Math-provider `combo-vacatures`, genereert
   en cachet de toegestane combinaties en voegt de gesplitste sitemaps aan de
   sitemapindex toe.

De exacte actieve exports, inclusief alle oorspronkelijke uitleg, staan onder
`wordpress/exports/code-snippets/2026-10-17/`.

Daarnaast staat in `wordpress/exports/code-snippets/2026-10-17/all-2026-10-17/` een volledige,
leesbare export van alle 47 LIVE-snippets: 26 actief en 21 inactief. Daarmee
blijven ook oudere versies, tests en de uitleg bij eerdere beslissingen
beschikbaar. Deze export is uitsluitend een back-up en wordt niet automatisch
naar WordPress uitgerold.

#### Vastgestelde LIVE-logica

- verschillende taxonomieën worden met `AND` gecombineerd;
- meerdere termen binnen één taxonomie gebruiken normaal `OR`;
- alleen `kennisgebied` gebruikt binnen dezelfde taxonomie `AND`;
- alleen gepubliceerde vacatures met `vac_status=open` tellen mee;
- een combo is indexeerbaar wanneer die een `functienaam` bevat, maximaal twee
  verschillende taxonomieën gebruikt en minimaal één resultaat heeft;
- daarnaast is een combo met uitsluitend `locatie` indexeerbaar;
- overige combo's zonder `functienaam`, en combo's met meer dan twee
  taxonomieën, krijgen `noindex, follow`;
- Search & Filter-query-URL's horen `noindex, follow` te krijgen en volgens de
  snippetbeschrijving een canonical naar de schone `/vacatures/`-URL.

#### Fase 1 live gezet en gecontroleerd op 19 oktober 2026

Snippet 148 en snippet 193 begrenzen de eerste publicatiefase nu beide tot
maximaal twee verschillende taxonomieën. De URL-cache van snippet 148 gebruikt
een nieuwe sleutel. Daarna is de Rank Math-sitemapmodule eenmaal uit- en weer
ingeschakeld, zodat Rank Math de sitemapindex op de normale manier opnieuw
opbouwde.

De openbare sitemapindex bevat sindsdien precies twee
`combo-vacatures`-sitemaps met samen 800 unieke URL's: 501 in deel 1 en 299 in
deel 2. Alle 800 URL's bevatten exact twee taxonomieën; er zijn geen URL's met
drie of meer taxonomieën aangetroffen.

Representatieve frontendtests bevestigen:

- twee taxonomieën met functienaam: `index, follow` en self-canonical;
- drie taxonomieën met functienaam: `noindex, follow` en geen canonical;
- de homepage blijft bereikbaar met HTTP 200.

Een zwaardere, tijdelijke poging om alle Rank Math-sitemapcaches vanuit de
snippet te verwijderen bleek onnodig belastend en is volledig teruggedraaid.
De actieve snippet bevat geen permanente cache-bypass of globale
cacheverwijdering. Inactieve herstelkopie 245 bevat de terugzetbare versie van
snippet 148; ook de eerdere veiligheidskopieën 243, 244 en 246 zijn bewaard.
De definitieve controle staat in
`audits/2026-10-19-combo-sitemap-max-two-taxonomies.md`.

#### Eerder vastgelegde keuze voor native vacature-taxonomiearchieven

De oudere, bewust uitgeschakelde SEO-snippets 146, 150 en 187 beschrijven
dezelfde strategie als de actieve opvolger 193. Dat geeft een herleidbare
beslislijn binnen WordPress zelf:

- de oorspronkelijke vacature-taxonomiearchieven blijven bereikbaar;
- ze blijven `index, follow`;
- een native `functienaam` canonicaliseert naar de overeenkomstige combo onder
  `/vacatures/functienaam/.../`;
- een native `locatie` canonicaliseert naar de overeenkomstige locatie-only
  combo onder `/vacatures/locatie/.../`;
- andere native vacaturetaxonomieën canonicaliseren in de huidige v2.2-code
  naar een combo met `field-service-engineer` als functienaamcontext.

Op 17 oktober 2026 is dit op LIVE gecontroleerd. De native archieven voor
`commissioning-engineer`, `nederland` en bedrijfssector `energie` geven alle
`index, follow` en renderen de hierboven beschreven canonical. Er is dus geen
reden om de native archieven of snippet 193 nu uit te schakelen. De inhoudelijke
keuze voor de vaste `field-service-engineer`-context bij andere taxonomieën
blijft wel een expliciet herbevestigingspunt voordat onderhoudsmodus en de
zoekmachineblokkade worden opgeheven.

### Audit van actief/inactief-selectie

De huidige status blijkt geen willekeurige verzameling. Vrijwel iedere oude
versie of kloon staat uit en de nieuwste opvolger staat aan:

| Inactieve voorganger(s) | Actieve opvolger |
| --- | --- |
| 42 en 207 | 208 |
| 102, 195, 196 en 198 | 199 |
| 110 | 206 |
| 111 | 204 |
| 122 | 189 |
| 126 | 188 |
| 130 | 203 |
| 132 | 191 |
| 134 en 201 | 202 |
| 146, 150 en 187 | 193 |
| 149 en 184 | 186 |

Snippets 185 (`debug 6`) en 197 (`test hermes vacatures create`) zijn eveneens
terecht inactief. Ze mogen pas na een aparte inhoudelijke vergelijking worden
verwijderd; inactief laten is nu de veilige keuze.

**Voorlopig statusbesluit:** alle 26 momenteel actieve snippets blijven aan.
De oudere versies blijven uit. Snippet 49 is inmiddels gecorrigeerd en
hertest. Snippet 99 heeft nog een onvolledige breadcrumb in de Rank Math-
schema-uitvoer en snippet 193 heeft de genoemde canonical-afwijking op
`noindex`-URL's. Deze twee punten worden eerst gericht beoordeeld en hertest;
ze zijn geen reden om de bijbehorende functionaliteit nu geheel uit te
schakelen.

### Functionele controle Beroepengids-keten

Op 17 oktober 2026 zijn drie representatieve LIVE-paden gecontroleerd:

- `/beroepengids/functienamen/` wordt door snippet 113 correct als virtueel
  taxonomie-base-archief afgehandeld, krijgt HTTP-content, het juiste
  Elementor-template en de classes `tfsc-taxonomy-base-archive` en
  `tfsc-archive-taxonomy`;
- `/beroepengids/functienamen/field-service-engineer-b/` laadt als normaal
  termarchief met de bedoelde termcontent en gerelateerde secties;
- `/beroepengids/artikelen/` laadt als het CPT-archief van `beroepengids` en
  toont de bedoelde template en breadcrumb.

De eerste twee controles bevestigen dat snippets 62, 101, 113 en de dynamische
archiefketen daadwerkelijk worden gebruikt en actief moeten blijven. De LIVE-
versie van snippet 49 controleerde nog op de oude CPT-naam `beroep`. Na de
correctie naar `beroepengids` is bevestigd dat `/beroepengids/artikelen/` de
class `tfsc-archive-beroepen` krijgt en dat de bestaande taxonomieclass op
`/beroepengids/functienamen/` behouden blijft.

Op het taxonomie-base-archief staat bovendien zichtbaar de letterlijke tekst
`archive_beroep_functienaam_description` in Elementor-widget `bb20f10`. Dit is
geen snippetfout. De widget gebruikt correct de dynamische tag **TFSC Archive
Settings Field** met instelling `description`. Het centrale Pods-veld bevat
zelf letterlijk zijn eigen veldnaam als tijdelijke inhoud.

Dezelfde placeholder is aangetroffen in de description-velden voor
bedrijfssector, functie-inhoud, functienaam, kennisgebied, opleiding en
werkgebied. Daarnaast bevatten twee introvelden nog de tijdelijke teksten
`test bedrijfssector intro` en `intro functienaam`. Op 17 oktober 2026 zijn
uitsluitend deze acht tijdelijke LIVE-waarden leeggemaakt. De bestaande echte
Beroepengids-CPT-teksten en de kennisbankvelden zijn daarbij ongewijzigd
gebleven. Na opslag zijn de taxonomie-base-archieven voor functienaam,
functie-inhoud, opleidingsniveau en werkgebied opnieuw getest; op geen van deze
pagina's wordt nog een ruwe veldnaam of de twee testteksten getoond.

Ook is een naamgevingsverschil vastgesteld: de actieve WordPress-taxonomie heet
`beroep_opleidingsniveau`, terwijl de centrale Pods-velden `opleiding` in hun
naam gebruiken. De generieke veldopbouw zal daardoor voor dit archief niet de
bestaande Pods-velden vinden. Snippet 113 noemt daarnaast `beroep_locatie` in de
allowlist, maar deze taxonomie verschijnt niet in het huidige Beroepengids-
beheermenu en heeft geen centrale archiefvelden. Beide punten worden eerst als
datamodelbesluit beoordeeld en niet stilzwijgend hernoemd.

De paginatitel is nu generiek `Archive • The Field Service Company`; voor de
Beroepengids base-archieven is dus nog gerichte Rank Math-metadata nodig.

Alle drie de paden geven tijdens deze test `noindex, nofollow`, overeenkomstig
de nog actieve algemene zoekmachineblokkade. Canonicals kunnen pas betrouwbaar
anoniem worden hertest nadat die blokkade bij de uiteindelijke livegang bewust
is opgeheven.

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

In de repository is voorbereid dat alleen werkelijk aanwezige, leesbare
bestanden worden toegevoegd en dat de oorspronkelijke bestandsnaam eerst veilig
wordt gecontroleerd. Deze correctie moet vóór uitrol met een enkel en een
meervoudig testbestand worden getest. Lokale opslag blijft behouden.

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

Zoekt uitsluitend naar het zichtbare `Vacaturenummer:` in de gerenderde
Elementor-HTML. Bij een klik op `.button-advies` opent de code het
adviesformulier op het huidige domein en geeft zij, wanneer aanwezig, alleen
`?vacaturenummer=<nummer>` mee. Op algemene archiefpagina's zonder één concrete
vacature opent hetzelfde formulier bewust zonder vacatureparameter.

Bedrijfsnaam, contractvorm, paginatitel en pagina-URL worden niet meer uit de
pagina geschraapt of als queryparameter meegestuurd. Supabase leidt de
vacaturecontext af uit het canonieke vacaturenummer. De vereenvoudigde code is
op 21 oktober 2026 LIVE gezet. De algemene adviesroute is gecontroleerd. Een
openbare proef met WordPress-vacaturenummer `359` bewees dat het nummer correct
in mappingversie `4` aankomt; Supabase zette de inzending terecht op
`optional_vacancy_not_found` omdat deze vacature nog niet is gemigreerd. De
herhaalproef met bestaande Supabase-vacature `885425` slaagde zonder
reviewcode en koppelde de adviesactiviteit aan de juiste vacature zonder een
sollicitatie aan te maken.

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
