# Rank Math Free/Pro — eindcontrole na deactivering door de eigenaar

Controle van 7 november 2026 voor thefieldservicecompany.nl, na de definitieve deactivering van Pro door de eigenaar.

**Later vervolg:** de automatische redirectopruiming is op verzoek toegevoegd en vervolgens beveiligd in de actuele versie 0.3.1. Zie de [beveiligingsreview](2026-11-07-rank-math-security-review.md) en [opruiming en vacaturelocaties](2026-11-07-rank-math-redirect-cleanup-and-job-locations.md). De onderstaande controlegegevens beschrijven de eerdere versie 0.2.0.

**Free 1.0.281 en TFSC Rank Math Breadcrumbs 0.2.0 zijn actief; Pro 3.0.124 is inactief. Er is bij deze hercontrole geen nieuwe uitval gevonden in de onderzochte, gebruikte websitefuncties. Er is geen aanvullende uitbreiding op productie geplaatst.** De bestaande vier aansluitingen blijven nodig: Elementor-breadcrumbs, gemarkeerde About/Mentions-entiteiten, queryparametervergelijking en de lokale sitemap.

## Volledigheid van de vergelijking

De twee officiële vergelijkingstabellen bevatten samen **204 regels**. Dit zijn ook overlappende productclaims en limieten, geen 204 onafhankelijke technische functies. Free en Pro hebben op 80 regels een verschillende presentatie: 77 vinkjes/beschikbaarheidsverschillen en drie verschillen in frequentie of bewaartermijn. Alle 204 regels zijn ondergebracht in de 22 groepen hieronder. Geen regel is overgeslagen bij de groepsindeling. [Officiële vergelijking](https://rankmath.com/free-vs-pro/).

Die lijst is vergeleken met de geïnstalleerde code en de relevante, afzonderlijke handleidingen. De 212 eerder onderzochte Pro-PHP-bestanden zijn opnieuw op hashes gecontroleerd: alle bestanden zijn ongewijzigd. De publicatiegeschiedenis vermeldt eveneens Pro 3.0.124 als huidige versie. [Pro-changelog](https://rankmath.com/changelog/pro/).

De [bewijsgegevens](evidence/rank-math-final-double-check-2026-11-07.json) bevatten de aantallen en een indeling per tabel-/regelnummer. De volledige reclameteksten en ruwe bedrijfsinstellingen zijn niet overgenomen.

| Groep | Regels in vergelijking | Verschil en oordeel voor TFSC |
| --- | ---: | --- |
| Analytics en rapportage | 27 | Pro breidt trackers, historie, prestaties per pagina, landfilters, AdSense, indexstatus, trackingkeuzes en rapportage uit. Analytics staat uit. Deze functies leveren nu geen actieve site-uitvoer. Een opgeslagen 15-daagse e-mailkeuze moet bij eventuele herinschakeling naar een ondersteunde Free-keuze worden aangepast. |
| AI-abonnement | 1 | Betreft proef-/abonnementsrechten. Content AI, Link Genius en AI Visibility vallen onder het afzonderlijke AI-product. Deze modules zijn niet actief. Er zijn geen accountrechten of abonnementen veranderd. |
| Linkanalyse en AI-linkbeheer | 5 | Geavanceerde interne koppelingen, gebroken links en verweesde pagina’s worden niet nagebouwd. De betreffende Rank Math-modules zijn niet actief. Bestaande, opgeslagen gewone links verdwijnen niet door Pro-deactivering. |
| MCP | 1 | De basisverbinding is niet hetzelfde als recht op iedere betaalde taak. Voor dit werk is de bestaande WordPress REST-toegang voldoende. Geen nieuwe MCP-koppeling nodig. |
| Schema | 35 | Free levert basistypen en blokken, waaronder JobPosting en FAQ. Pro breidt onder meer templates, import, custom/multiple schema, bijzondere types, voorwaarden, preview/validatie en HowTo-velden uit. Er is geen opgeslagen Rank Math-schema in post-, term- of gebruikersmetadata, en geen templatecollectie. Alle 16 gepubliceerde FAQ’s zijn afzonderlijk gecontroleerd. Het eigen TFSC-schema blijft werken. |
| Redactie en optimalisatie | 15 | Free behoudt basisoptimalisatie. Extra Pro-filters, trends, bulk- en quick-editfuncties vervallen. De onderzochte 314 records hebben maximaal vijf focuskeywords. Geen huidige inhoud hoeft voor een overschreden editorlimiet te worden aangepast. |
| Nieuws | 1 | Pro-nieuwssitemap staat uit. Geen vervanging nodig. |
| Video | 4 | Free bevat handmatig Video-schema; Pro doet automatische detectie/invulling en videositemaps. Er is nu geen aangetroffen videoinhoud of opgeslagen Video-schema. Bij toekomstige video’s is handmatige schema-invoer of een aparte oplossing nodig. |
| Afbeeldingen en sociale weergave | 18 | Free behoudt basis-ALT/TITLE, OpenGraph en sociale afbeeldingen. Extra Pro-captions, tekstvervanging en watermerken zijn niet ingesteld. Geen niet-lege overlaymetadata gevonden bij posts, termen of gebruikers. Geen nieuwe opvang nodig. |
| Local SEO | 7 | Bedrijfsidentiteit en één locatie blijven beschikbaar in Free. Meerdere locaties, geavanceerde locatieblokken en routezoeker worden niet gebruikt. De bestaande lokale sitemap blijft via onze uitbreiding beschikbaar. |
| Webwinkels en downloads | 3 | De extra WooCommerce-/EDD-functies hebben hier geen actieve onderliggende winkelplugin of bijbehorende content. Geen vervanging nodig. |
| Podcast | 2 | Podcastmodule en extra schema/feed zijn niet actief. Geen vervanging nodig. |
| Beheer, inrichting en commerciële rechten | 18 | Omvat onder meer inrichting, beheerrollen, ondersteuning, klantbeheer en overige producteigenschappen. Er is geen aangetroffen openbare TFSC-afhankelijkheid waarvoor extra code nodig is. Betaalde beheer- en dienstverleningsrechten worden niet door onze uitbreiding overgenomen. |
| Gemarkeerde entiteiten | 1 | Pro’s omzetting van About/Mentions-markeringen is opgevangen. De bestaande uitvoer op 14 pagina’s blijft gelijk. De Pro-bediening om nieuwe markeringen toe te voegen wordt niet nagebouwd. |
| Redirects en 404 | 16 | Gewone codes, matching, monitor en automatische redirects bij slugwijzigingen bestaan in Free. Querymatching is opgevangen. Pro-categorieën, planning, exports en synchronisatie worden niet nagebouwd. Automatisch opruimen bij definitief verwijderen verandert; zie hieronder. |
| Import en export | 6 | Basisoverdracht bestaat in Free; extra CSV-/bulkprocessen uit Pro vervallen. Dit zijn beheerhandelingen. Het verdwijnen van de CSV-bediening verandert geen reeds opgeslagen pagina-inhoud. |
| Pagebuilders | 2 | De gewone Elementor-koppeling blijft. Het afzonderlijke Pro-breadcrumbwidget is opgevangen. In de bestaande Elementor-documenten zijn geen gebruikte Rank Math Pro-FAQ-vlaggen of Pro-locatie-/related-posts-/rich-snippet-shortcodes aangetroffen. Divi is niet actief. |
| Breadcrumbs | 2 | De Free-engine en de eigen widgetaansluiting blijven werken met bestaande templates, CSS en archieffilters. Geen shortcodevervanging nodig. |
| Crawlers en indexering | 13 | Robots, verificatie, llms.txt en gewone indexeringsregels zijn beoordeeld. Het huidige robots.txt is een fysiek bestand en bereikbaar. De llms-module staat uit; een HTTP 404 voor llms.txt is daarom geen gevonden Pro-regressie. De verkooplijst over wachtwoord-noindex is verouderd voor deze geïnstalleerde code; zie de correctie hieronder. |
| Affiliate-links | 1 | De Pro-prefixlijst is leeg. Geen actieve verhulde-linkbewerking te vervangen. |
| Gewone metadata en feeds | 18 | Basis-metadata, canonicals, archieven, XML-sitemaps en RSS blijven door Free geleverd. De huidige pagina- en sitemapvergelijking is gelijk en de RSS-feed is geldig XML. |
| SEO-analyse | 8 | De basis-SEO Analyzer is beschikbaar in Free en staat nog aan. Pro-concurrentievergelijking en aanvullende analyses vervallen. Zoekintentieanalyse is eerder op verzoek uitgezet en blijft uit. |

Dit is een beoordeling van huidige TFSC-afhankelijkheden, geen verklaring dat iedere gedeelde Free-functie ook op TFSC is ingesteld. Een in een verkooplijst genoemde mogelijkheid is evenmin bewijs dat iemand die functie op de website heeft gebruikt.

## Extra onderzochte aansluitingen buiten de verkooplijst

- **Schema bij taxonomieën en auteurs:** de eerdere controle op berichtmetadata is uitgebreid naar `termmeta` en `usermeta`. Geen opgeslagen `rank_math_schema*` gevonden in alle drie tabellen. Ook geen extra schema-templates in andere statussen.
- **Concepten en nog te publiceren inhoud:** geen Pro-schema-/locatie-/related-posts-shortcodes gevonden. Er is één concept met een gewoon Free-FAQ-blok. De 36 About/Mentions-records zijn gepubliceerd.
- **Elementor:** 105 documenten uit publish, draft, pending, future en private onderzocht op de genoemde Pro-shortcodes; nul treffers. De eerder onderzochte 99 gepubliceerde documenten blijven de basis voor de widgetinventarisatie.
- **Eigen aansluitingen:** alle 35 actieve functionele Code Snippets en 47 PHP-bestanden van andere actieve TFSC-plugins gescand op rechtstreekse Pro-klassen, Pro-functies en Pro-constanten; nul treffers. Onze compatibiliteitsplugin is bewust uitgezonderd, omdat zijn Pro-controle de dubbele uitvoering juist voorkomt. De tijdelijke auditsnippet is ook uitgezonderd.
- **Extra titelvariabelen:** geen Pro-randomwoord-/afbeeldings-/podcastvariabelen gevonden in Rank Math-post-, term- of gebruikersmetadata. De eerdere controle van algemene instellingen was eveneens leeg.
- **ACF-afbeeldingen in sitemaps:** Pro kan extra ACF-velden uitlezen. De betreffende instelling is niet gezet en ACF zelf is niet actief. Het bestaan van een compatibiliteitsfunctie met een ACF-naam is geen bewijs van actieve ACF-inhoud. De volledige sitemapvergelijking bleef gelijk. [ACF-handleiding](https://rankmath.com/kb/advanced-custom-fields/).
- **AMP en meertalige nieuwsuitbreiding:** geen actieve AMP-functies aangetroffen en de nieuwsmodule staat uit. Er is geen gevonden actieve Pro-aansluiting voor deze routes.

## Correctie: wachtwoordpagina’s automatisch op noindex

De officiële vergelijking zet dit bij Pro. In **Free 1.0.281** staan echter zowel het instellingsveld in `includes/settings/titles/misc.php` als de uitvoering in `includes/frontend/paper/class-singular.php`. Een aanvullende test met de echte Free-renderer bevestigt: bij een wachtwoordbericht werkt `noindex` als de optie aanstaat; met de optie uit wordt die regel niet toegevoegd. De actuele TFSC-optie staat uit en is niet veranderd.

Daarom is dit voor deze installatie **geen ontbrekende Pro-functie**. De eerdere audit is op dit punt gecorrigeerd. De afzonderlijke handleiding beschrijft de optie ook zonder die Pro-beperking. [Titles & Meta](https://rankmath.com/kb/titles-and-meta/#8-11-noindex-password-protected-pages).

## Verschillen die bij toekomstige contentbewerkingen tellen

**URL wijzigen blijft automatisch werken.** In een afzonderlijke tijdelijke WordPress-installatie met alleen Free zijn een echte CPT-slugwijziging en een taxonomie-slugwijziging uitgevoerd. In beide gevallen maakte Free de juiste 301-regel naar de nieuwe URL. Dit is dus niet alleen uit de documentatie afgeleid. [Automatische redirects](https://rankmath.com/kb/how-to-redirect-posts-after-changing-urls/).

**Definitief verwijderen in Free alleen vraagt een redirectcontrole.** In dezelfde baselineproef bleven de gemaakte regels na definitieve verwijdering van het bericht of de term bestaan. Pro voegt extra koppeling en automatische opruiming toe. Op het latere verzoek van de eigenaar is die aansluiting nu in onze versie 0.3.0 opgenomen en getest; zie het vervolgverslag hierboven.

**Nieuwe video’s, meerdere bedrijfslocaties en geavanceerde schema’s zijn een nieuwe keuze.** Het huidige ontbreken van die content maakt ze geen migratieblokkade, maar Free neemt de Pro-automatisering niet vanzelf over zodra zulke inhoud wordt toegevoegd. Onze uitbreiding ondersteunt de huidige enkele locatie en leest bestaande About/Mentions-markeringen; ze biedt geen Pro-schema-editor. Voor nieuwe gemarkeerde links kunnen dezelfde attributen via de bestaande REST-publicatie worden aangeleverd.

**FAQ blijft via de bestaande werkwijze bruikbaar.** Alle 39 vragen uit de 16 gepubliceerde Rank Math-FAQ-blokken zijn aanwezig in zowel de zichtbare HTML als de schema-uitvoer. Voor toekomstige Elementor-accordions is de eigen Elementor-FAQ-optie een andere functie dan de extra Rank Math Pro-schakelaar. [Elementor en Rank Math FAQ](https://rankmath.com/kb/faq-schema-elementor/).

## Bewijs na de definitieve deactivering

- REST bevestigt Free actief, Pro inactief en onze uitbreiding 0.2.0 actief.
- Opnieuw 48 verse openbare pagina-aanvragen en 30 sitemap-/locatiebestanden vergeleken met de oorspronkelijke Pro-situatie: geen verschillen in de onderzochte uitvoer. Sitemap-`lastmod` is bij inhoudsvergelijking uitgesloten. Alle 48 pagina’s waren cache-misses in beide rondes.
- Aanvullend alle 16 FAQ-pagina’s gecontroleerd: 39 verwachte vragen, geen ontbrekende vraag in HTML of JSON-LD.
- robots.txt: HTTP 200. RSS: HTTP 200, geldig RSS-XML. De tweede pagina van vacatures en Beroepengids-artikelen: beide HTTP 200, indexeerbare robotsregels en een canonical naar de betreffende tweede pagina.
- De eerdere gecontroleerde omschakelproef leverde al 14 identieke desktop-/mobiele breadcrumb-screenshots en berekende stijlen op. Die visuele proef is niet opnieuw volledig uitgevoerd na de laatste handmatige deactivering.
- Lokaal opnieuw 56 volledige compatibiliteitscontroles geslaagd, plus acht nieuwe controles op de Free-bewerkingswerkwijze. De 27 gerichte widgetcontroles waren eerder geslaagd. [Tests](../wordpress/plugins/tfsc-rank-math-breadcrumbs/tests/free-workflows.php).

Tijdens deze hercontrole is Pro niet opnieuw aangezet. Er zijn geen content-, template-, schema- of SEO-instellingen gewijzigd. Alleen een tijdelijke, geauthenticeerde leesroute is gebruikt; die is weer gedeactiveerd en geeft HTTP 404. De acht bewerkingstests draaiden uitsluitend in een tijdelijke lokale database. De dev-site is niet aangeraakt.

Het advies na de beveiligingsreview is **Free met uitbreiding 0.3.1 actief houden**. De extra automatische opruiming is beschikbaar met ondertekende serverbewijzen. Meerdere werklocaties in vacatures worden al door TFSC Structured Data geleverd en zijn geen Pro-afhankelijkheid. Bij nieuwe soorten content of toekomstige updates hoort een gerichte test op dev; de huidige proef bewijst geen gedrag van nog onbekende versies of verwerking door Google.

## Aanvullende primaire bronnen

- [Analytics en indexstatus](https://rankmath.com/kb/analytics/)
- [Geavanceerde redirects en CSV](https://rankmath.com/kb/setting-up-redirections/)
- [Rank Math AI en de afzonderlijke abonnementsvorm](https://rankmath.com/kb/rank-math-ai-rebranding/)
- [llms.txt en module-instellingen](https://rankmath.com/kb/llms-txt/)
- [Algemene instellingen, robots.txt en basisredirects](https://rankmath.com/kb/general-settings/)
