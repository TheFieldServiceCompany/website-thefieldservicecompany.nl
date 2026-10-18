# Beroepengids Opleidingen — LIVE-implementatie

> **Historisch implementatieverslag, later vervangen.** Dit document beschrijft
> de eerste gecombineerde opzet met `beroep_opleiding`. Op dezelfde datum is
> besloten deze hiërarchie te vervangen door twee zelfstandige taxonomieën:
> `beroep_opleidingsniveau` en `beroep_studierichting`. Zie
> `2026-10-18-beroepengids-opleidingen-split-live.md` voor de actuele opzet.

Datum: 18 oktober 2026

Site: `https://thefieldservicecompany.nl`

Status: uitgevoerd en functioneel gecontroleerd; onderhoudsmodus bleef actief

## Pods-taxonomie en termen

Op LIVE is de hiërarchische taxonomie `beroep_opleiding` aangemaakt en aan het
posttype `beroepengids` gekoppeld.

| Onderdeel | LIVE-ID | Instelling of doel |
| --- | ---: | --- |
| Pod `beroep_opleiding` | 27600 | publiek, REST, hiërarchisch, rewrite `beroepengids/opleidingen` |
| `archive_featured_image` | 27601 | afbeelding voor archiefkaart/template |
| `intro_taxonomy` | 27602 | korte zichtbare intro |
| `relationship_opleidingsniveau` | 27603 | relatie naar vacaturetaxonomie `opleidingsniveau` |
| `relationship_vac_studierichting` | 27604 | relatie naar vacaturetaxonomie `vac_studierichting` |

De taxonomie bevat 29 termen:

- parentterm 569: Opleidingsniveau;
- parentterm 570: Studierichting;
- kindtermen 571–574: de vier opleidingsniveaus;
- kindtermen 575–597: de 23 studierichtingen.

Alle 27 kindtermen zijn aan precies één passende vacatureterm gekoppeld. Ze
hebben ieder een korte intro en een unieke, inhoudelijke SEO-description. Het
virtuele basisarchief heeft een eigen intro en een description van 459 woorden.
De twee parenttermen bevatten daarnaast een navigatielijst naar hun directe
kinderen.

## Snippets en herstelkopieën

Voor iedere bestaande snippet die is gewijzigd, is eerst in Code Snippets een
inactieve kopie gemaakt.

| Actief | Versie | Wijziging | Inactieve herstelkopie |
| ---: | --- | --- | ---: |
| 62 | 2.1 | Query ID `current_term_children` toegevoegd | 210 |
| 113 | 3.1 | `beroep_opleiding` aan de toegestane virtuele basisarchieven toegevoegd | 211 |
| 209 | 1.1 | SEO-mapping van oud Opleidingsniveau naar Opleidingen gewijzigd | 212 |
| 215 | 1.1 | native Opleidingen-taxonomysitemap expliciet aan de Rank Math-index toegevoegd | 221 |

De eenmalige snippets 216–220 hebben achtereenvolgens relaties, intro's,
basiscontent, Rank Math-termtitels en de sitemapcache verwerkt. Ze staan nu
inactief. De actieve en eenmalige snippets rapporteerden na uitvoering geen
codefouten.

## Beroepengids-pagina 24266

Op de centrale Beroepengids-pagina zijn de volgende Elementor-onderdelen
gewijzigd:

- kaartcontainer `428cb72` linkt naar `/beroepengids/opleidingen/`;
- heading `c56acea` heet nu `Opleidingen`;
- taxonomielus `2ea14df` gebruikt `beroep_opleiding` en toont de twee
  parenttermen;
- intro `29cd21e` verwijst tekstueel naar opleidingen;
- tekstwidget `45ff326` linkt eveneens naar het nieuwe Opleidingen-archief.

De oorspronkelijke Elementor-data is als herstelbestand opgeslagen in:

`audits/exports/page-24266-beroepengids-before-opleidingen-2026-10-18.json`

De kaart, heading, link, twee parenttermen en de tekstlink zijn op de publieke
pagina visueel gecontroleerd.

## SEO en sitemap

- De basisroute `/beroepengids/opleidingen/` staat in `page-sitemap.xml`.
- `beroep_opleiding-sitemap.xml` bevat alle 29 term-URL's.
- Beide sitemaproutes staan in de Rank Math-sitemapindex.
- De Rank Math-titelformule voor termen is:
  `%term% voor field service | Beroepengids`.
- De LiteSpeed-cache is na de wijzigingen volledig opgeschoond.
- Onderhoudsmodus en de daardoor zichtbare algemene `noindex, nofollow` zijn
  bewust niet gewijzigd. Canonicals en indexeerbaarheid moeten bij de
  definitieve livegang opnieuw worden gecontroleerd.

## Functionele controles

Gecontroleerd zijn:

- het virtuele Opleidingen-basisarchief met H1, intro, parentnavigatie en
  basisdescription;
- het parentarchief Opleidingsniveau met de vier juiste kinderen;
- een kindtermarchief, inclusief intro, lange description, vacatureknop en
  gerelateerde vacatures;
- de vacaturetaxonomierelaties voor niveau en studierichting;
- de centrale Beroepengids-kaart en interne link;
- de page-sitemap, taxonomysitemap en sitemapindex.

## Bewust nog niet gewijzigd

1. Maak een 301 van `/beroepengids/opleidingsniveau/` naar
   `/beroepengids/opleidingen/`.
2. Redirect en verwijder daarna pas de oude testterm en, na controle, de oude
   testtaxonomy.
3. Beslis of de lange termdescription in het gedeelde Elementor-template echt
   onderaan moet worden geplaatst. Nu wordt de native termdescription door het
   gedeelde template bovenaan weergegeven.
4. Verberg of vul de generieke vacaturesecties op de twee parentarchieven;
   deze parents hebben bewust geen eigen vacaturetaxonomierelatie.
5. Synchroniseer de definitieve opleidingstructuur later ook met Supabase en
   Appsmith. In deze WordPress-stap is daar niets gewijzigd.
