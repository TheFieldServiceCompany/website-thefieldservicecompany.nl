# Rank Math Free/Pro — tweede functionaliteitsaudit

Gecontroleerd op 7 november 2026 voor thefieldservicecompany.nl. Geïnstalleerd zijn Rank Math Free 1.0.281, Rank Math Pro 3.0.124, Elementor 4.3.6 en Elementor Pro 4.3.3.

## Besluit voor de overstap

Alleen het breadcrumb-widget vervangen was onvoldoende. De vervolgaudit bevestigde ook Pro-afhankelijk schema uit linkmarkeringen, een queryparameterregel en het inschakelen van de lokale sitemap. **Versie 0.2.0 van de eigen uitbreiding vangt deze vier bestaande aansluitingen op. De gecontroleerde live proef met Pro uit is geslaagd.** Het [vervolgverslag](2026-11-07-rank-math-free-compatibility-test.md) bevat de meetresultaten en overstapstappen.

**Actuele eindstand: Free en TFSC Rank Math Breadcrumbs 0.3.1 zijn actief; Pro is door de eigenaar gedeactiveerd.** De [eindcontrole na deactivering](2026-11-07-rank-math-final-free-pro-double-check.md) behandelt alle 204 regels uit de officiële vergelijking. Het [functionele vervolg](2026-11-07-rank-math-redirect-cleanup-and-job-locations.md) voegt automatische redirectopruiming toe en bevestigt de onafhankelijke vacaturelocaties. De [beveiligingsreview van 0.3.1](2026-11-07-rank-math-security-review.md) vervangt het onveilige vertrouwen op legacy-metadata door serverbewijzen. Zoekintentieanalyse is uitgezet; SEO Analyzer blijft op verzoek aan. De eerste audit was te stellig over een overstap naar Free. Met name het Elementor-widget en de omzetting van linkmarkeringen naar schema waren daarin onvoldoende gecontroleerd.

## Wat is onderzocht

- De officiële Free/Pro-vergelijking en de afzonderlijke documentatie over breadcrumbs, schema, FAQ, redirects, afbeeldingen, Local SEO, Analytics, rapporten en AI.
- De daadwerkelijke instellingen, effectieve modulestatus en alle 212 PHP-bestanden buiten de vendor-map van de geïnstalleerde Pro-plugin. De broncode is lokaal gebruikt voor deze audit en wordt niet als onderdeel van dit rapport verspreid.
- 654 gepubliceerde records in de inhoudscontrole, waaronder configuratie-objecten. De Elementor-controle omvatte 99 gepubliceerde documenten. Gepubliceerde Rank Math-metadata en schema-metadata van alle niet-revisiestatussen zijn gecontroleerd.
- De actieve TFSC-snippets met Rank Math-aansluitingen, waaronder 99, 148, 193, 199, 209, 215 en 327. Hun gebruikte kernfilters bestaan in Free. Er zijn geen rechtstreekse Pro-klassen nodig in deze snippets.
- Alle 36 openbare pagina’s met relevante linkmarkeringen zijn opgehaald en hun JSON-LD is onderzocht. Op 14 pagina’s is de extra schema-uitvoer daadwerkelijk aanwezig. Er waren geen ophaalfouten.
- Een afzonderlijke inhoudscontrole omvatte 348 gepubliceerde blogs, Beroepengids-artikelen, Kennisbankartikelen, pagina’s, vacatures en ingevulde vacatures. Daarin zijn geen videomarkeringen, Rank Math HowTo-blokken of de Pro-templatevariabelen voor random woorden, imagealt, imagetitle en podcast_image gevonden.

## Werkelijke afhankelijkheden en ingestelde Pro-functies

| Onderdeel | Verschil tussen Free en Pro | Aangetroffen bij TFSC | Gevolg bij Pro uit |
| --- | --- | --- | --- |
| Elementor-breadcrumbwidget | De breadcrumb-engine en shortcode zijn Free. Het bestaande Elementor-widget wordt door Pro geregistreerd. | 15 widgets in gepubliceerde Elementor-documenten, waarvan eerder 13 op openbare pagina’s bevestigd. | De uitbreiding neemt hetzelfde widgettype over. Live gecontroleerd op zeven paginatypen, desktop en mobiel; markup, berekende stijlen en screenshots zijn gelijk. |
| About/Mentions-schema uit links | Pro zet linkmarkeringen om naar extra gestructureerde gegevens. | 36 gepubliceerde records met 5 about- en 70 mentions-markeringen. Op 14 pagina’s is de uitvoer zichtbaar in JSON-LD. | Versie 0.2.0 bewaart de bestaande gemarkeerde entiteiten. Volledige schema-uitvoer op alle 36 gemarkeerde pagina’s bleef gelijk met Pro uit. |
| Queryparameters bij redirectbronnen | Pro vergelijkt ook de volledige aanvraag met queryparameters. | Regel 133 is actief, heeft statuscode 410 en een bron met de parameters `wordfence_lh` en `hid`. De parameterwaarden zijn bewust weggelaten. | Versie 0.2.0 gebruikt de Free-vergelijking voor de volledige URI. De echte opgeslagen regel matcht met én zonder Pro; een afwijkende query matcht niet. Dit is binnen een geauthenticeerde controle getest, zonder de beveiligings-URL aan te roepen. |
| Lokale XML-sitemap en KML | Free bevat de generator, maar Pro schakelt hem via een filter in. | `local_sitemap` staat aan, één bedrijfslocatie; meerdere locaties staan uit. | Versie 0.2.0 schakelt de Free-generator in op basis van de bestaande keuze. `local-sitemap.xml`, `locations.kml` en de index zijn live gelijk gebleven. |
| Automatische opruiming van redirects | Free kan redirects bij URL-wijzigingen maken. Pro koppelt die ook aan posts/terms voor opruiming bij verwijderen. | Over alle statussen bestaan 37 bericht- en 15 termkoppelingen. De eerdere telling van 32 betrof gepubliceerde berichten. | Versie 0.3.1 gebruikt ondertekende serverbewijzen voor de gecontroleerd overgenomen en nieuwe koppelingen. Gewone metadata kan geen verwijdering autoriseren. Prullenbak/herstel en handmatige ongekoppelde regels blijven behouden. |
| Zoekintentie in de editor | De Pro-code levert extra zoekintentieanalyse. | Op verzoek uitgezet: `determine_search_intent` is nu `off`. Alleen deze sleutel is gewijzigd; alle andere algemene instellingen zijn gelijk gebleven. | Deze redactiefunctie hoeft niet te worden vervangen. TFSC doet dit onderzoek elders. |
| Rank Tracker en uitgebreide Analytics | Geselecteerde keywords volgen en uitgebreide prestaties horen bij Pro. | Opgeslagen trackerdata was aanwezig in de eerste audit. De Analytics-module staat nu effectief uit. | Toegang tot de Pro-rapportages verdwijnt. Er is momenteel geen actieve Analytics-module die de site-uitvoer verzorgt. |
| E-mailrapporten | Free biedt een maandelijks basisrapport. Pro biedt onder meer 15 dagen en uitgebreidere inhoud. | Rapporten staan ingesteld op 15 dagen. Er staat een taak klaar en één mislukte taak is bewaard. De rapportcallback is momenteel niet geregistreerd doordat Analytics uitstaat. | De huidige configuratie bevat Pro-keuzes, maar actieve verzending is niet aangetoond. Bij herinschakeling moet dit naar een Free-interval en inhoud worden aangepast. |
| Automatisch importeren van focuskeywords | Een extra Pro-optie binnen Analytics. | De opgeslagen optie staat aan voor meerdere posttypes. Analytics staat effectief uit. | Deze automatisering is nu slapend en wordt in Free niet overgenomen. |
| Marketplace | De geïnstalleerde Pro-bron registreert deze extra module. | De module staat in de actieve lijst. | De betreffende beheerfunctie vervalt. Dit verzorgt geen openbare metadata of breadcrumbs. |

De markeringen zijn verspreid over 34 vacatures, één ingevulde vacature en één Beroepengids-artikel. De actuele schema-uitvoer staat op één Beroepengids-artikel en 13 vacatures. Een concreet voorbeeld is [Wat is een Field Service Engineer?](https://thefieldservicecompany.nl/beroepengids/artikel/wat-is-een-field-service-engineer/), waar vijf mentions worden uitgegeven. De overige 22 records bevatten wel markeringen maar leveren in de huidige openbare JSON-LD geen overeenkomstige Thing-eigenschappen op. Een opgeslagen optie of markering is dus niet automatisch bewijs van actieve uitvoer.

De Pro-bron leest deze markeringen uit de bestaande berichtinhoud en voegt ze toe aan het WebPage-schema. Daarvoor zijn geen afzonderlijk opgeslagen `rank_math_schema_*`-velden nodig. Dat verklaart waarom de eerdere controle op schema-metadata deze afhankelijkheid miste.

## Functies die Free levert of waarvoor geen actief Pro-gebruik is gevonden

| Onderdeel | Free/Pro-specificatie en aangetroffen gebruik | Beoordeling |
| --- | --- | --- |
| Titels, descriptions, robots, canonicals en sociale metadata | De basis wordt door Free geleverd. Er zijn onder meer 287 titels, 314 descriptions en vier canonicals opgeslagen. De TFSC-filters gebruiken Free-aansluitingen. | Beschikbaar in Free. De uitvoer op de 48 gecontroleerde pagina’s bleef gelijk tijdens de live proef. |
| XML- en HTML-sitemaps | Beide bestaan in Free. XML-instellingen en de extra TFSC-URL’s worden via kernfilters geleverd. HTML-sitemap staat ingesteld. | Geen aangetroffen Pro-afhankelijkheid in deze functies. |
| Gewone redirects en 404-monitor | Free ondersteunt onder meer 301 en 410, matchtypes en de monitor. | 234 actieve regels en 31 regels in de prullenbak. De actieve codes zijn 301 en 410. De queryparameterregel is hierboven apart behandeld. |
| Geplande redirects | In- en uitschakeldatums en redirectcategoriebeheer horen bij Pro. | Geen geplande redirectacties gevonden. Categoriebeheer blijft een Pro-interfaceverschil. |
| CSV-import/export en synchronisatie naar .htaccess | Uitgebreidere Pro-beheermiddelen. Een export naar .htaccess is een expliciete beheerhandeling. | De mogelijkheid om deze acties uit te voeren vervalt. Eerder geschreven serverregels zijn niet geïnventariseerd en verdwijnen niet automatisch door deactivering. |
| Standaardschema en eigen TFSC-schema | Free bevat onder meer Article, BlogPosting, Breadcrumb en Local SEO-schema. De site gebruikt daarnaast een eigen plugin voor gestructureerde gegevens. | De basis is beschikbaar in Free. De volledige schema-uitvoer op de 48 gecontroleerde pagina’s bleef gelijk tijdens de live proef. |
| Custom Schema Builder, meerdere schema’s, import en schema-templates | Deze uitgebreide beheerfuncties horen bij Pro. | Geen `rank_math_schema_*`-metadata in niet-revisierecords gevonden. De templatecollectie bevat nul records in publish, draft, pending, private en future. |
| FAQ | Het Rank Math Gutenberg-FAQ-blok werkt in Free. De eigen Rank Math-FAQ-optie in het Elementor-accordionwidget is Pro. Elementor heeft ook een eigen FAQ-optie. | 16 gepubliceerde records bevatten het Gutenberg-FAQ-blok. In de 99 Elementor-documenten is geen Pro-FAQ-vlag gevonden. |
| HowTo en extra schematypen | Free heeft basisblokken en basistypen. Pro heeft extra schema’s en geavanceerde opties. | Geen opgeslagen custom-schema’s gevonden. De openbare-contentcontrole en de bestaande eigen schema-uitvoer blijven de referentie voor de proef. |
| Afbeeldings-SEO | Free kan ontbrekende ALT- en TITLE-attributen aanvullen. Pro voegt onder meer captions, descriptions, hoofdletteraanpassingen, avatar-ALT en vervangingen toe. | Zowel de basisaanvulling als alle genoemde Pro-bewerkingen staan uit. De vervangingslijst is leeg. |
| Automatische videodetectie en videositemap | Handmatig Video-schema bestaat in Free. Automatisch detecteren/invullen en videositemaps zijn Pro-uitbreidingen. | Detectie staat voor meerdere posttypes opgeslagen als aan. De videositemapmodule staat uit. In 348 inhoudsrecords zijn geen videomarkeringen gevonden en de 99 Elementor-documenten bevatten geen Video- of Audio-widgets. |
| Nieuws- en podcastsitemaps/feed | Deze uitbreidingen horen bij Pro. | Beide modules staan uit. Aanwezige oude nieuwsrobotsmetadata maakt de nieuwsmodule niet actief. |
| Media RSS | Pro kan videogegevens aan de feed toevoegen. | `disable_media_rss` staat aan. De Pro-code voegt de gegevens nu al niet toe. |
| Local SEO | Een bedrijfsidentiteit en enkele locatie werken in Free. Meerdere locaties, routezoeker en aanvullende kaartopties zijn Pro. | Meerdere locaties, locatiedetectie en uitgebreide zoekfunctie staan uit. Bedrijfsschema bleef live gelijk; de apart ingeschakelde lokale sitemap wordt hierboven behandeld. |
| WooCommerce, EDD, bbPress en Divi | De bijbehorende Pro-integraties breiden die andere producten uit. | Geen actief gebruik van die producten aangetroffen. Een aangevinkte WooCommerce-module zonder WooCommerce-plugin is geen werkende winkelintegratie. |
| Watermerken en aanvullende social-image overlays | Pro breidt de gewone sociale afbeeldingen uit. | Geen standaard watermerk of custom-overlaylijst ingesteld en geen overeenkomstige overlay-metadata gevonden. De 72 `rank_math_og_content_image`-velden zijn een cache die ook in Free bestaat. |
| Automatische noindex voor wachtwoordpagina’s | De verkooplijst noemt Pro, maar instelling en uitvoering bestaan al in de geïnstalleerde Free 1.0.281. De aanvullende lokale test bevestigt dit. | Optie staat uit. Voor deze installatie geen ontbrekende Pro-functie. |
| Affiliate-prefixen | Pro kan verhulde affiliate-links extra behandelen. | De prefixlijst is leeg. Geen actieve bewerking te vervangen. |
| Focuskeywords | In de geïnstalleerde editorcode is de limiet vijf voor Free en honderd met de Pro-filter. | Alle 314 onderzochte records hebben maximaal vijf focuskeywords. De verdeling is 213×1, 27×2, 57×3, 12×4 en 5×5. |
| Bulkbewerking, quick edit, filteren, Google Trends en PageSpeed/performance-overzichten | Pro voegt beheer- en analysefuncties toe. | Deze extra mogelijkheden verdwijnen. Historisch klikgebruik kan de databasecontrole niet vaststellen. Er is geen zelfstandige openbare uitvoer van aangetoond. |
| SEO Analyzer | De basisanalyse bestaat in Free. Pro voegt onder meer aanvullende concurrentieanalyse toe. | De actuele effectieve module staat aan. Dat wijkt af van de eerdere aanname dat hij uit stond. Tijdens deze audit is deze instelling niet gewijzigd. |
| Index Status | Een basisoverzicht bestaat in Free. Pro heeft uitgebreidere widgets/details en een hoger aantal URL’s per planning. | Analytics staat uit. Dit onderdeel is dus momenteel geen actieve bron van website-uitvoer. |
| Link Genius en AI | Pro breidt linkbeheer en AI-koppelingen uit. Rank Math AI heeft een eigen abonnement en gebruikslimieten. | Link Counter/Link Genius, Content AI en AI Visibility ontbreken in de actieve modulelijst. Opgeslagen Content AI-scores zijn historisch materiaal. |
| MCP | De basisverbinding werkt ook met Free. Betaalde taken behouden hun planvereisten. | Voor deze audit zijn de bestaande WordPress-REST-toegang en tijdelijke beheerroutes gebruikt. |

## Installatie en live proef

De uitbreiding is via 0.1.0 en 0.1.1 bijgewerkt naar 0.2.0. De bronhashes van de geïnstalleerde bestanden komen overeen met de geteste repositorybestanden. Er slagen 27 gerichte controles en 56 controles in de tijdelijke volledige WordPress-installatie. De live proef omvatte 48 pagina’s, 30 sitemap-/locatiebestanden en 14 desktop-/mobiele vergelijkingen. Er zijn geen verschillen gevonden in de gecontroleerde breadcrumbs, metadata, volledige schema-uitvoer, sitemapinhoud, widgetstijlen en screenshots.

De tijdelijke installatie- en auditroutes zijn gedeactiveerd via snippet 410. De auditroute retourneert daarna HTTP 404. Na de eerste proef is Pro weer aangezet; daarna heeft de eigenaar Pro definitief gedeactiveerd. De actuele eindstand is Free actief, Pro inactief en TFSC Rank Math Breadcrumbs 0.2.0 actief. De laatste hercontrole veranderde geen inhoud, templates of SEO-instellingen.

De [samengevatte controlegegevens](evidence/rank-math-free-2026-11-07.json) leggen de aantallen, aangetroffen schema-uitvoer, bronhashes en installatiestatus vast. Ruwe instellingen, ontvangers, aanmeldgegevens en de waarden van de queryparameters zijn daarin weggelaten.

De aangetroffen openbare afhankelijkheden zijn nu opgevangen en gecontroleerd. De Pro-beheerfuncties uit de tabel worden niet nagebouwd. Automatische opruiming van redirects bij toekomstige verwijdering verandert in Free. Account- en AI-abonnementsrechten, historisch gebruik van beheerknoppen en iedere toekomstige pluginversie blijven buiten deze proef. Er is geen Search Console-indexeringstest uitgevoerd en geen SEO-rankingwinst geclaimd.

## Officiële specificaties

- [Volledige actuele vergelijking Free, Pro, Business en Agency](https://rankmath.com/free-vs-pro/)
- [Breadcrumbs en het Pro-widget voor Elementor](https://rankmath.com/kb/breadcrumbs/)
- [About en Mentions-schema](https://rankmath.com/kb/about-and-mentions-schema/)
- [Redirects met queryparameters](https://rankmath.com/kb/setting-up-redirections/)
- [Redirectcategorieën en planning](https://rankmath.com/kb/how-to-create-a-redirection/)
- [Schema’s per editie](https://rankmath.com/kb/rich-snippets/) en [meerdere schema’s](https://rankmath.com/kb/multiple-schema-types/)
- [FAQ met Elementor](https://rankmath.com/kb/faq-schema-elementor/)
- [Afbeeldings-SEO](https://rankmath.com/kb/image-seo/)
- [Local SEO](https://rankmath.com/kb/local-seo/)
- [HTML-sitemap in Free](https://rankmath.com/kb/html-sitemap/)
- [Analytics en Index Status](https://rankmath.com/kb/analytics/)
- [E-mailrapporten en frequenties](https://rankmath.com/kb/seo-email-reporting/)
- [Zoekintentieanalyse](https://rankmath.com/kb/search-intent-analysis/)
- [Rank Math AI en de abonnementsstructuur](https://rankmath.com/kb/rank-math-ai-rebranding/)
- [MCP en planvereisten](https://rankmath.com/kb/setup-rank-math-mcp/)
