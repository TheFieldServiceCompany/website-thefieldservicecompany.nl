# Functienaam als anker en Rank Math als beheerder van losse archieven — 0.4.0

## Opdracht en afbakening

De manager beheert uitsluitend combinaties vanaf twee kenmerken met functienaam voorop. Losse functie-, sector-, kennisgebied- en locatiearchieven worden door Rank Math beheerd. De aangevinkte kenmerken vormen een pool: maximaal twee levert functie + ieder extra kenmerk afzonderlijk, maximaal drie voegt alle drietallen toe, maximaal vier de viertallen.

De bestaande twee-dimensionale indexeringsregeling blijft intact. Diepere URL’s vereisen nog steeds een expliciete exacte vrijgave. De poolscan is één vacaturequery met hergebruik van de termgegevens. Ontbrekende optionele velden sluiten de kortere combinaties niet uit.

## Motor en canonieke structuur

Snippet 193 bevatte een extra filter dat alle native vacaturetaxonomieën uit hun Rank Math-sitemap hield. Dit filter is verwijderd. De native robotsfunctie forceert niet langer index/follow. Standalone combinatie-routes lezen Rank Math-termindexering voordat managerregels worden toegepast; de manager kan losse archieven niet uitsluiten.

De bestaande canonicalstructuur blijft behouden: native vacature-taxonomiepagina’s verwijzen naar hun canonieke TFSC-route. Rank Math-term-sitemapentries gebruiken dezelfde canonieke URL. Expliciete custom canonicals in Rank Math blijven behouden. De eigen combositemap (148) bevat nu uitsluitend combinaties vanaf twee en geen losse archieven, zodat de canonieke hoofd-URL niet dubbel wordt vermeld.

Niet-functiecombinaties worden vóór de productgeneratie overgeslagen. De vroege functienaamcheck vindt ook vóór dure term-/Rank Math-controles plaats. Dit voorkomt rekenwerk voor onbruikbare combinaties.

## Rank Math-bron

De live installatie gebruikt Rank Math 1.0.280. De openbare vacaturetaxonomieën voor sector, kennisgebied, locatie en functie staan voor sitemap en indexering aan. Status is niet publiek opvraagbaar en niet indexeerbaar en blijft uitgesloten. Er zijn geen Rank Math-opties gewijzigd.

Beschikbare kenmerken volgen de officiële helpers get_accessible_taxonomies en is_taxonomy_indexable plus de sitemap-exclusiefilter. Individuele term-noindex en Exclude Terms worden voor combinatie-indexering gerespecteerd. Wijzigingen aan Rank Math Titles & Meta of Sitemap Settings maken snapshots ongeldig, legen betrokken sitemapcache en plannen een nieuwe controle.

Bronnen: [Rank Math-taxonomiehelper](https://github.com/rankmath/seo-by-rank-math/blob/master/includes/helpers/class-taxonomy.php), [term-sitemapprovider](https://github.com/rankmath/seo-by-rank-math/blob/master/includes/modules/sitemap/providers/class-taxonomy.php), [officiële sitemapfilters](https://rankmath.com/docs/filters-and-hooks/admin/sitemap/).

## Controle

PHP 8.5.10: acht pluginbestanden geparseerd; 63 beleids-/poolchecks, 24 selectiechecks, vier deactivatiechecks en bestaande tellertests. Getest: meer vinkjes dan URL-diepte, combinaties van twee en drie, geen combinatie zonder functie, ontbrekende optionele velden, Rank Math-uitgeschakelde taxonomie, individuele noindex-term, behoud van custom canonical, echte sitemapuitvoer, expliciete vrijgave en rollback, selectiebeveiliging, REST-rechten en onvolledige scans. Vijf adminschermen en effectvoorvertoning zijn ook gerenderd en in Chromium gecontroleerd.

Live controles en tellingen staan bij het bewijsmateriaal. De upgrade geeft geen diepe URL’s vrij en verandert geen vacaturegegevens of Rank Math-instellingen.

## Tijdens de live controle opgelost

De eerste uitlezing deed dezelfde Rank Math-controles herhaaldelijk en duurde circa 32 seconden. Beschikbaarheid en termindexering worden nu per aanvraag hergebruikt; na de correctie is 0,53 seconde gemeten. Onbruikbare taxonomiesets zonder functie worden al vóór de productgeneratie overgeslagen. Bij de structurele sitemapwijziging is naast de combo-cache ook de Rank Math-sitemapcache vernieuwd; dat is geen globale WordPress-cachepurge.

## Definitieve live uitkomst

- Maximum blijft twee; nul diepe releases. De keuzepool van functie, sector, kennisgebied en locatie levert 85 paren op, alle reeds indexeerbaar volgens het bestaande beleid.
- 1.074 unieke combinatie-URL’s in drie combo-sitemapdelen, allemaal met functienaam en minstens twee kenmerken.
- Rank Math-sitemapdelen voor losse vacaturetaxonomieën zijn aanwezig; Status ontbreekt. De FSE-hoofd-URL staat precies eenmaal in functienaam-sitemap.xml, niet ook in de combositemap.
- FSE-hoofdarchief, pagina 2 en native FSE-pagina behouden de verwachte canonical en index/follow.
- Huidige uitlezing 0,53 seconde; de vijf echte beheerschermen zijn ook met 500 regels gecontroleerd.

[Pluginbroncode en bewijsbestanden](https://github.com/TheFieldServiceCompany/tfsc-combo-archive-manager/tree/545e1d73246bd1e00991362cbd1bdfc34dca709f)
