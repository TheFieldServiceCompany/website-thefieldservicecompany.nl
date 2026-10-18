# Beroepengids Opleidingen: Pods- en snippetimpact

> **Historisch ontwerp, later vervangen.** De hieronder onderzochte
> parent-kindstructuur met `beroep_opleiding` is niet de uiteindelijke
> architectuur geworden. De actuele LIVE-opzet gebruikt de zelfstandige
> taxonomieën `beroep_opleidingsniveau` en `beroep_studierichting`. Zie
> `2026-10-18-beroepengids-opleidingen-split-live.md`.

Datum: 18 oktober 2026  
Status: analyse uitgevoerd; LIVE-implementatie op 18 oktober 2026 afgerond

## LIVE-resultaat

- Pods-taxonomie 27600 `beroep_opleiding` is aangemaakt met velden 27601–27604.
- De boom bevat 2 parenttermen en 27 kindtermen: 4 opleidingsniveaus en 23
  studierichtingen.
- Alle 27 kinderen hebben de juiste relatie naar `opleidingsniveau` of
  `vac_studierichting`, een korte intro en een unieke lange description.
- Snippets 62, 113 en 209 zijn na inactieve herstelkopieën 210, 211 en 212
  aangepast en actief getest.
- Snippet 215 versie 1.1 voegt de basis-URL en de native
  `beroep_opleiding`-sitemap correct aan de Rank Math-sitemapindex toe. De
  herstelkopie is snippet 221.
- Pagina 24266 verwijst nu met heading, kaartlink, taxonomielus en tekstlink
  naar Opleidingen. De oorspronkelijke Elementor-data staat in
  `audits/exports/page-24266-beroepengids-before-opleidingen-2026-10-18.json`.
- De basis, parents, kindtermen, relaties, vacatureknop en beide sitemaproutes
  zijn functioneel gecontroleerd. De onderhoudsmodus bleef aan.

Nog open: de 301 van `/beroepengids/opleidingsniveau/` naar
`/beroepengids/opleidingen/`, de oude testterm en taxonomy gecontroleerd
opruimen, en een aparte beslissing over de positie van de lange description
en de generieke vacaturesecties op de twee parentarchieven.

## Besluit en begripsafbakening

De Beroepengids krijgt **één hiërarchische Pods-taxonomie** met het publieke
label `Opleidingen` en bij voorkeur de interne naam `beroep_opleiding`.
`Opleidingsniveau` en `Studierichting` worden daarin geen aparte taxonomieën,
maar twee bovenliggende termen. De 4 niveaus en 23 studierichtingen worden
kindtermen onder de juiste tak.

De publieke boom wordt:

```text
Opleidingen
├── Opleidingsniveau
│   ├── VMBO / LBO
│   ├── MBO
│   ├── HBO
│   └── WO / Universiteit
└── Studierichting
    ├── Autotechniek
    ├── Bedrijfsautotechniek
    ├── ...
    └── Werktuigbouwkunde
```

Er komt geen gewone WordPress-pagina of Beroepengidsartikel `Opleidingen`.
`/beroepengids/opleidingen/` wordt, net als de bestaande basisarchieven voor
functienamen, functie-inhoud en bedrijfssectoren, een virtueel
taxonomy-basearchief.

De twee gegevensdimensies blijven aan de vacature-, Supabase- en Appsmithkant
wel technisch gescheiden:

- vacaturetaxonomie `opleidingsniveau`;
- vacaturetaxonomie `vac_studierichting`;
- Supabase `education_levels` en `education_fields`;
- afzonderlijke Appsmith-keuzes voor niveau en studierichting.

De ene Beroepengids-taxonomie is dus een publieke inhouds- en navigatiespiegel,
niet de vervanging van het gescheiden vacaturedatamodel.

## Aanbevolen Pods-configuratie

| Instelling | Waarde |
| --- | --- |
| Interne naam | `beroep_opleiding` |
| Meervoudig label | Opleidingen |
| Enkelvoudig label | Opleiding |
| Objecttype | `beroepengids` |
| Publiek / publiek bevraagbaar | Ja |
| Hiërarchisch | Ja |
| REST | Ja |
| Rewrite-basis | `beroepengids/opleidingen` |
| Hiërarchische rewrite | Ja |

De hiërarchische rewrite is nodig om de betekenis in de URL zichtbaar te
houden, bijvoorbeeld:

- `/beroepengids/opleidingen/opleidingsniveau/hbo/`;
- `/beroepengids/opleidingen/studierichting/elektrotechniek/`.

Aanbevolen velden op de nieuwe taxonomy-Pod:

| Veld | Type | Doel |
| --- | --- | --- |
| `archive_featured_image` | afbeelding | bestaande generieke archiefkaart/template |
| `intro_taxonomy` | WYSIWYG | korte intro per term |
| `relationship_opleidingsniveau` | relatie naar `opleidingsniveau` | alleen invullen op niveau-kindtermen |
| `relationship_vac_studierichting` | relatie naar `vac_studierichting` | alleen invullen op studierichting-kindtermen |

`relationship_vacature_button_url` hoeft niet standaard te worden toegevoegd.
Snippet 127 bouwt de vacatureknop automatisch uit de actieve
`relationship_*`-velden. Eén expliciet buttonveld kan bovendien maar aan één
doeltaxonomie worden gekoppeld en past daarom minder goed bij deze tweedelige
boom.

Op `HBO` wordt alleen `relationship_opleidingsniveau = HBO` ingevuld. Op
`Elektrotechniek` wordt alleen
`relationship_vac_studierichting = Elektrotechniek` ingevuld. De twee
bovenliggende navigatietermen blijven zonder vacature-relatie. Zo voorkomen we
dat de generieke snippets verschillende relaties onbedoeld met AND combineren.

## Waarom `beroep_opleiding` de juiste interne naam is

De bestaande Pods-instellingen bevatten al:

- `archive_beroep_opleiding_intro`;
- `archive_beroep_opleiding_description`.

Snippet 202 verwijdert voor het bepalen van de veldnaam het voorvoegsel
`beroep_`. `beroep_opleiding` normaliseert daardoor exact naar `opleiding` en
vindt de bestaande velden zonder maatwerk. De huidige taxonomy
`beroep_opleidingsniveau` normaliseert naar `opleidingsniveau` en past juist
niet op die bestaande instellingen.

## Impact per snippet of onderdeel

| Onderdeel | Inschatting | Benodigde wijziging |
| --- | --- | --- |
| Snippet 113 — virtuele taxonomy-basearchieven | klein, verplicht | `beroep_opleidingsniveau` in de allowlist vervangen door `beroep_opleiding`; daarna permalinks verversen |
| Snippet 209 — Rank Math SEO voor basisarchieven | klein, verplicht | mapping vervangen door `beroep_opleiding` met titel en description voor Opleidingen |
| Snippet 62 — taxonomyloops op niveau | klein, waarschijnlijk nodig | bestaand `current_taxonomy_level_0` toont de twee hoofdtakken; een nieuwe queryvariant `current_term_children` is wenselijk om op een hoofdtak uitsluitend de directe kinderen te tonen |
| Snippet 202 — Pods Archiefinstellingen | geen codewijziging | werkt automatisch met de gekozen interne naam en de bestaande intro-/descriptionvelden |
| Snippet 116 — generieke termvelden en afbeeldingen | geen codewijziging | werkt automatisch zodra de nieuwe Pod dezelfde velden bevat |
| Snippet 188 — relaties vanaf een taxonomyterm | geen codewijziging | leest alle ingevulde `relationship_*`-velden en gebruikt de echte doeltaxonomie uit de data |
| Snippet 204 — gerelateerde posts | geen codewijziging | kan niveau- en studierichtingrelaties generiek verwerken; per kindterm slechts de passende relatie invullen |
| Snippet 127 — vacatureknop | geen codewijziging | bouwt automatisch de juiste vacature-combo-URL uit de niveau- óf studierichtingrelatie |
| Snippet 131 — vacaturetermen op een basisarchief | niet gebruiken voor Opleidingen | gaat uit van precies één doeltaxonomie per basisarchief en past daarom niet op deze tweedelige boom; de basis toont de twee Beroepengids-hoofdtakken via snippet 62 |
| Snippet 203 — dynamische archieftitel | geen codewijziging | hiërarchische termpaden worden generiek herkend |
| Snippet 199 — breadcrumbs | waarschijnlijk geen codewijziging | basisarchief werkt generiek; na aanleg controleren of Rank Math beide termniveaus toont, anders alleen het termarchiefdeel expliciet op ancestors laten bouwen |
| Snippet 101 — description-accordion | niet als opslagroute gebruiken | deze is op vacaturetaxonomieën/comboarchieven gericht; de 450-woordenteksten horen bij de Beroepengids-kindtermen en moeten onderaan door het generieke Elementor-termarchief worden weergegeven |
| Snippet 148 — vacature-combo-sitemap | geen wijziging | `beroep_opleiding` hoort niet in de vacature-combo-sitemap |
| Rank Math sitemap | kleine aparte aanvulling | native termarchieven opnemen via de taxonomy-sitemap; bij nog lege termen `Include Empty Terms` beoordelen. Het virtuele basisarchief apart als extra URL toevoegen |

## Elementor- en pagina-impact

Het generieke Beroepengids-archieftemplate blijft de basis. Er hoeft geen nieuwe
gewone pagina en vermoedelijk ook geen nieuw template te worden gemaakt.

Wel nodig:

1. op `/beroepengids/opleidingen/` met query
   `current_taxonomy_level_0` alleen `Opleidingsniveau` en `Studierichting`
   tonen;
2. op de twee hoofdtakken alleen de directe kinderen tonen, bij voorkeur met
   de nieuwe query `current_term_children`;
3. op de 27 kindtermarchieven de korte intro bovenaan en de lange description
   onderaan tonen;
4. de huidige link `Artikelen op opleidingsniveau` op `/beroepengids/`
   vervangen door een ingang `Opleidingen` naar het virtuele basisarchief;
5. de templateweergave zo conditioneren dat een lege kindlijst op een bladterm
   geen lege ruimte oplevert.

## SEO, sitemap en redirects

- De basis krijgt een eigen SEO-title en meta description via snippet 209.
- De parent- en kindtermarchieven krijgen unieke zichtbare tekst, self-canonical
  URL's en termmetadata.
- De 27 kinddescriptions zijn al als concept voorbereid; de twee
  bovenliggende termen en de basisintro/-description vragen afzonderlijke,
  kortere navigatieteksten.
- Rank Math neemt native taxonomytermen alleen volgens de taxonomy-
  sitemapinstellingen op. Omdat nog niet iedere term aan een Beroepengidsartikel
  gekoppeld zal zijn, moet `Include Empty Terms` expliciet worden beoordeeld.
- Het virtuele basisarchief is geen normale term of pagina. Daarvoor is een
  kleine, aparte sitemaptoevoeging nodig. Dit hoort niet in de vacature-combo-
  sitemap van snippet 148.
- Oude basis-URL:
  `/beroepengids/opleidingsniveau/` krijgt een 301 naar
  `/beroepengids/opleidingen/`.
- De oude testterm `/beroepengids/opleidingsniveau/opleiding-a/` moet na de
  migratie worden verwijderd en naar een inhoudelijk passende nieuwe URL worden
  omgeleid.

## Veilige uitvoeringsvolgorde

1. Maak een export/back-up van de huidige Pod en snippets 62, 113, 199, 202 en
   209.
2. Maak `beroep_opleiding` en de twee parenttermen aan, nog zonder oude Pod te
   verwijderen.
3. Maak de 4 + 23 kindtermen aan met stabiele slugs en leg de juiste
   vacature-relatie per kindterm vast.
4. Pas snippets 113 en 209 aan en voeg zo nodig `current_term_children` toe aan
   snippet 62.
5. Configureer het generieke Elementor-archieftemplate en publiceer intro's en
   descriptions.
6. Ververs permalinks en test basis, parents en een niveau- en
   studierichtingkind inclusief H1, breadcrumbs, relaties, vacatureknop,
   canonical, robots en mobiele weergave.
7. Controleer Rank Math taxonomy-sitemap en voeg het virtuele basisarchief aan
   de sitemap toe.
8. Werk de Beroepengidsingang en interne links bij en activeer pas daarna de
   301-redirects.
9. Verwijder de oude testtaxonomy pas wanneer de nieuwe boom en redirects
   aantoonbaar werken.

## Risico-inschatting

De wijziging is technisch **laag tot middelgroot risico**. De generieke
snippetarchitectuur ondersteunt de gekozen opzet grotendeels al. De grootste
risico's zijn niet de relaties zelf, maar:

- een verkeerde rewrite- of parentinstelling waardoor URL's plat worden;
- het onbedoeld gebruiken van snippet 131, die maar één vacaturetaxonomie kan
  spiegelen;
- alle 27 kinderen tegelijk tonen in plaats van alleen de kinderen van de
  huidige hoofdtak;
- termdescriptions die niet onderaan door het Elementor-template worden
  weergegeven;
- oude URL's die zonder redirect verdwijnen;
- lege termarchieven die buiten de sitemap blijven.

Tijdens de oorspronkelijke analyse zijn geen live onderdelen gewijzigd. De
daaropvolgende LIVE-uitvoering is hierboven vastgelegd; redirects en het
opruimen van de oude testtaxonomy zijn nog niet uitgevoerd.
