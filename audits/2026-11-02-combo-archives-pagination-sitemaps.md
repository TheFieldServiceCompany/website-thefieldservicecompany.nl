# Combo-archieven: adviesblok, filters, paginering en sitemap

Datum: 2 november 2026. Uitgevoerd na expliciet akkoord van Rudi op het plan. Alle functionele controles zijn via REST, de publieke HTML en een gecontroleerde browser uitgevoerd.

## Uitkomst

- Alle **21 open Field Service Engineer-vacatures** komen precies één keer terug over drie pagina’s: 10 + 10 + 1.
- Ook het algemene overzicht is gecontroleerd: **alle 47 open vacatures**, zonder ontbrekende of dubbele vacaturekaarten.
- Het adviesblok is extra, na de vijfde vacature. Het vervangt geen vacature en verandert geen query-offset, result count of paginagrootte.
- Nummerlinks, volgende en vorige blijven op het combo-archief. Iedere vervolgpagina heeft een eigen canonical.
- Een echte filterwijziging opent het algemene `/vacatures/` met de gekozen filters. Getest met Field Service Engineer + Commissioning Engineer vanaf pagina 2.
- Reset opent het algemene vacatureoverzicht zonder de oude combo-selectie.
- De combositemap bevat **1.144 unieke URL’s**: 70 archieven met één taxonomie en 1.074 combinaties met twee taxonomieën. Geen combinaties dieper dan twee.
- De native vacaturetaxonomieën staan niet langer als dubbele URL’s in de XML-sitemap. Beroepengids-taxonomieën blijven aanwezig.

## Waarom niet simpelweg Static item position aanzetten?

De oude Alternate Template verving de zesde vacature van een pagina. Bij FSE verdwenen daardoor vacature 13190 (Field Service Engineer Verpakkingsmachines) en 15757 (Field Service Engineer High Tech) uit de zichtbare kaarten.

We hebben eerst de ingebouwde statische positie afgeschermd getest. Daarbij verschenen 9 vacatures op de eerste pagina, maar begon pagina 2 nog bij de elfde vacature. Vacature 20806 viel daardoor tussen de pagina’s. Deze instelling is **niet uitgerold**. Dit is het waargenomen gedrag in deze combinatie van huidige query, Elementor en Search & Filter; geen algemene bewering dat Elementor overal dezelfde fout heeft.

De werkende oplossing houdt de oorspronkelijke query met tien vacatures intact. De vervangende Alternate Template staat uit in Elementor-document 25495, widget 44137129. Snippet 376 voegt het bestaande adviesontwerp 20568 tijdens het serverrenderen toe. Bij minder dan zes vacatures wordt geen extra blok geplaatst. Het ontwerp zelf blijft in Elementor te bewerken.

## Wijzigingen en samenhang

| Onderdeel | Verantwoordelijkheid | Uitgevoerd |
|---|---|---|
| Snippet 186 | Router, vacaturequery en Search & Filter-synchronisatie | Canonieke paginalinks; onderscheid tussen pagineren en filter wijzigen; reset naar algemeen overzicht |
| Snippet 193 | Canonical, indexbeleid en centrale instellingen | Native vacaturetaxonomieën uit XML uitsluiten; bestaande canonicalrelatie naar combo blijft |
| Snippet 148 | Rank Math-combositemap | Minimum één dimensie; beleidsmaximum twee; nieuwe eigen cacheversie |
| Snippet 376 | Extra adviesblok | Bestaand adviesontwerp toevoegen zonder resultaten te vervangen |
| Elementor-document 25495 | Gedeeld vacaturearchief | Alternate Template uit; tien vacatures per pagina behouden |
| Live indexbeleid | Centrale optie van 193 | Maximum van drie naar twee gezet; er waren nul expliciete diepe vrijgaven |

De sitemapgenerator gebruikt nog steeds het centrale indexbeleid. Alleen geldige functienaam- en locatiehoofden en toegestane combinaties met open vacatures worden opgenomen. Het eerdere minimum van twee verklaarde waarom het FSE-hoofdarchief ontbrak in XML: de pagina zelf bestond wel degelijk.

De sitemapuitsluiting betreft de via de bestaande centrale vacature-allowlist herkende taxonomieën. Het is geen uitschakeling van Pods, geen redirect en geen algemene noindex-wijziging. De gecontroleerde native FSE-pagina blijft naar de combo-URL canonicaliseren. Blog, Kennisbank en Beroepengids houden hun eigen canonicals.

## De gevonden managerplugin

Repository: [TFSC Combo Archive Manager](https://github.com/TheFieldServiceCompany/tfsc-combo-archive-manager).

Live gecontroleerd: **actief, versie 0.2.0**. De veronderstelling dat de plugin nog inactief stond klopte dus niet meer.

Beheer: **Tools → TFSC Combo-archieven** (`tools.php?page=tfsc-combo-archive-manager`).

Versie 0.2.0 is een teller en **read-only automatiseringssimulatie**. Hij rekent dagelijks en na vacaturewijzigingen voorstellen uit, maar schrijft niet naar de live indexpolicy, canonicals, robots of sitemaps. De preview gebruikt eigen opties. De standaardregels zijn voorstellen vanaf zes open vacatures, terug onder drie na twee lage scandagen, maximaal vier taxonomieën en 25 voorstellen per run. Dat previewmaximum is geen live sitemapvrijgave.

De GitHub-historie van 26 oktober beschrijft de overgang van teller naar veilige beleidssimulatie. De openbare keten blijft **186 → 193 → 148**. Voor een latere actieve manager moeten we diezelfde centrale policy hergebruiken; geen tweede, concurrerende sitemap- of indexeringslogica toevoegen. Deze update heeft geen actieve automatische uitbreiding ingeschakeld.

## Overige archieven

83 Elementor-librarydocumenten zijn op Alternate Templates gecontroleerd. Eén gedeeld document gebruikte een vervangend adviesblok: het vacaturearchief. Verder zijn de werkelijk gerenderde grids van Blog, Beroepengids, Kennisbank en een Beroepengids-functiearchief bekeken. Daar stond geen overeenkomstige Alternate Template aan. We hebben daar geen onnodige instellingen veranderd. Dit is een controle op de gedeelde templates en representatieve pagina’s, geen claim dat iedere afzonderlijke archive-URL handmatig is doorgeklikt.

## Acceptatietests

- FSE pagina 1 → 2 → 3 en terug: 21 unieke vacature-ID’s, geen gaten of dubbelen.
- Eigen canonical per pagina; native FSE-canonical wijst naar combo.
- Filter wijzigen vanaf pagina 2: `/vacatures/?_functienaam=field-service-engineer,commissioning-engineer&~1`.
- Reset naar `/vacatures/`: tien vacaturekaarten plus extra adviesblok.
- Mobiel 390 px: tien vacatures plus advies; documentbreedte 390 px, geen horizontale overflow.
- Geen JavaScript-pageerrors in de definitieve browserruns. Een tijdens testen gevonden variabelereferentie is vóór afronding hersteld en opnieuw getest.
- Algemeen vacatureoverzicht: alle 47 open vacatures komen precies eenmaal terug.
- Alle drie combositemapdelen verwerkt: 1.144 unieke URL’s, diepte maximaal twee, FSE-hoofdarchief aanwezig.
- Sitemapindex bevat geen native functienaam-, locatie-, werkgebied- of bedrijfssector-sitemap meer; Beroepengids-sitemaps blijven aanwezig.
- Live snippetcode na afloop vergeleken met de geteste bronbestanden.
- Tijdelijke proeflogica vervangen door de definitieve snippet; tijdelijke, beheerder-beveiligde onderhoudsroutes verwijderd en 404 gecontroleerd.

De tests begonnen met `tfsc_test=codex`; browserrequests naar analyticscollectie werden geblokkeerd. Er zijn geen formulieren verzonden. Back-ups van de oorspronkelijke snippets en het gewijzigde Elementor-document zijn vóór de updates lokaal opgeslagen.

## Cache en terugdraaien

Alleen de Rank Math-sitemapcache en de betrokken vacaturecache zijn vernieuwd, via de bestaande Rank Math-helper en LiteSpeeds posttype-/URL-purgehooks. Geen globale objectcache-, asset- of sitepurge.

Snippet 376 kan afzonderlijk worden gedeactiveerd: dan verdwijnt alleen het extra adviesblok en blijven de tien vacatures behouden. Laat daarvoor de vervangende Alternate Template uit. De overige wijzigingen zijn afzonderlijk te herstellen vanuit de vooraf opgeslagen backups. Voor een latere sitemapuitbreiding moet zowel de centrale grens als de expliciete tijdelijke begrenzing in 148 bewust worden aangepast en getest.

Een bijgewerkte XML-sitemap betekent niet dat Google onmiddellijk alle URL’s opnieuw heeft gecrawld of opgenomen. Oude los ingediende native taxonomiesitemaps kunnen in Search Console nog als historische indiening voorkomen; het huidige sitemapindexbestand bevat de canonieke structuur.

## Bronnen

- [Elementor: Alternate Template](https://elementor.com/help/how-do-i-add-an-alternate-template-in-a-loop-grid/)
- [Search & Filter: Elementor-integratie](https://searchandfilter.com/documentation/integrations/elementor/)
- [Rank Math: sitemapfilters](https://rankmath.com/docs/filters-and-hooks/admin/sitemap/)
- [LiteSpeed: purge-API](https://docs.litespeedtech.com/lscache/lscwp/api/)
- [Google: paginering](https://developers.google.com/search/docs/specialty/ecommerce/pagination-and-incremental-page-loading)
- [Google: canonicals](https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls)
