# Content-, taxonomie- en locatielogica

Besluitlog, gestart op 17 oktober 2026. Deze regels zijn richtinggevend; er is
nog niets in WordPress of Supabase aangepast.

## Eén canonieke begrippenlaag

Vacatures, Beroepengids en later Kennisbank mogen niet ieder een los woordenboek
met bijna dezelfde termen onderhouden. Supabase wordt de canonieke laag voor
goedgekeurde begrippen en relaties. WordPress gebruikt die begrippen voor
pagina's, filters, archieven, interne links en SEO.

Mappings worden per taxonomie beoordeeld en expliciet vastgelegd. Er worden
geen termen automatisch samengevoegd of verwijderd zonder goedgekeurd besluit.

## Locaties

Alle huidige WordPress-plaatsen en postcodes moeten uiteindelijk ook in
Supabase beschikbaar zijn. De bestaande koppelingen van landen en provincies
aan vacatures moeten eveneens behouden blijven.

Het doelmodel onderscheidt minimaal:

1. **Bedrijfsadres** — vestiging, straat, plaats en postcode van het bedrijf.
2. **Vacaturelocatie** — feitelijke standplaats of werklocatie van de functie.
3. **Bestuurlijke indeling** — land en provincie die aan de vacature zijn
   gekoppeld.
4. **Woon-/wervingsregio** — regio waaruit een kandidaat mag komen of wonen.
5. **Regiodekking** — de plaatsen en postcodes die onder zo'n woon-/wervingsregio
   vallen.

De huidige set bestaat bewust uit de grotere plaatsen en de afgesproken
regio's. Het aantal en de afbakening zijn voor nu bepaald. TODO: later samen
herzien of extra plaatsen of postcodes nodig zijn; bestaande records worden
niet vooruitlopend op die beoordeling uitgebreid.

Een vacature kan dus meerdere geografische relaties hebben. Het bedrijfsadres
mag niet automatisch als wervingsregio worden geïnterpreteerd, en een
wervingsregio mag niet automatisch als feitelijke werklocatie worden
gepubliceerd.

De structured data en SEO-metadata worden later uit de juiste relaties
opgebouwd. De huidige SNIP-plugin leest alles uit de WordPress-taxonomie
`locatie`; die bestaande werking is beschreven in `snippets/README.md` en moet
bij het nieuwe model bewust worden vervangen of aangepast.

## Reizen

`dagen-internationaal` is een gedeelde inhoudelijke categorie met een beperkt
aantal afgesproken termen. Zowel vacatures als kandidaten moeten onder deze
termen kunnen vallen. Dezelfde canonieke termen en relaties moeten daarom ook
in Supabase beschikbaar zijn.

Bestaande vacatures en kandidaten worden later gecontroleerd onder de juiste
term ingedeeld. Eventuele al aanwezige numerieke of ja/nee-velden in Supabase
bepalen dit model niet automatisch; we beoordelen per onderdeel of die velden
aanvullend nuttig zijn. `reisprofiel` en de precieze relatie met
`dagen-internationaal` behandelen we afzonderlijk wanneer dat tabblad aan de
beurt is.

## Beroepengids

Beroepengids wordt een inhoudelijke spiegel van de relevante canonieke
taxonomieën. Een sector, functie, capability, kennisgebied of opleidingsniveau
verwijst naar hetzelfde onderliggende begrip als bij vacatures.

Beroepengids-pagina's bieden duurzame uitleg en linken door naar actuele
vacatures die aan hetzelfde begrip zijn gekoppeld. De contentpagina en de
vacaturefilter zijn verschillende presentaties van hetzelfde concept, geen
losse duplicaten.

### Zoekintentie en functie van de archiefpagina's

De Beroepengids bedient primair een informatieve en oriënterende zoekintentie:
mensen willen begrijpen welke technische beroepen, werkzaamheden,
kennisgebieden, opleidingen, sectoren en werkgebieden bestaan en welke daarvan
bij hun achtergrond of ambitie passen. Actuele vacatures zijn een relevante
vervolgstap, maar niet het hoofdonderwerp van deze archiefpagina's. Zo blijven
de Beroepengids en de commerciële vacaturearchieven inhoudelijk onderscheiden.

De voornaamste doelgroepen zijn:

- studenten, starters en zij-instromers die technische loopbaanmogelijkheden
  verkennen;
- ervaren technici en fieldserviceprofessionals die functies, specialisaties
  of vervolgstappen vergelijken;
- kandidaten die vanuit een opleiding, kennisgebied, sector of type werk naar
  passende beroepen en vervolgens vacatures zoeken;
- werkgevers en andere lezers die eenduidige uitleg over beroepen en vereiste
  kennis zoeken.

### Vast inhoudsmodel

Ieder taxonomie-base-archief krijgt vier afzonderlijke inhoudslagen:

1. **Paginatitel/H1** — de menselijke naam van het onderwerp.
2. **Korte intro bovenaan** — twee à drie zinnen die direct uitleggen wat de
   bezoeker op de pagina kan vinden, hoe dit onderwerp binnen field service en
   techniek wordt gebruikt en welke vervolgstap mogelijk is.
3. **Zichtbare verdiepende tekst onderaan** — unieke, duurzame uitleg met
   duidelijke tussenkoppen. Deze beschrijft het begrip, relevante verschillen,
   de relatie met beroepen, kennis, opleiding en sectoren, hoe iemand keuzes kan
   vergelijken en hoe de gekoppelde artikelen en vacatures moeten worden
   gebruikt. Dit wordt geen lange lijst zoekwoorden en geen generieke tekst die
   alleen het taxonomielabel vervangt.
4. **Unieke SEO-metadata** — een beschrijvende title en meta description die de
   concrete pagina samenvatten. De meta description is niet hetzelfde veld als
   de lange zichtbare description onderaan.

De gekozen werktitels voor de zes base-archieven zijn:

- `Functienamen in field service | Beroepengids`;
- `Werkzaamheden in field service | Beroepengids`;
- `Kennisgebieden binnen field service | Beroepengids`;
- `Opleidingsniveaus voor fieldservicefuncties | Beroepengids`;
- `Bedrijfssectoren in field service | Beroepengids`;
- `Werkgebieden in field service | Beroepengids`.

### Vindbaarheid in zoekmachines en AI-systemen

De inhoud blijft in normale, crawlbare HTML zichtbaar en krijgt een logische
koppenstructuur, beschrijvende interne links en consequente begrippen. De
relaties tussen beroep, functienaam, werkzaamheden, kennisgebied,
opleidingsniveau, sector, werkgebied en actuele vacature worden expliciet in
tekst en links uitgedrukt. Structured data beschrijft uitsluitend inhoud en
relaties die ook zichtbaar op de pagina staan.

Voor vindbaarheid in generatieve zoekfuncties gelden dezelfde basisprincipes
als voor gewone zoekresultaten: unieke en aantoonbaar nuttige inhoud, duidelijke
technische structuur, indexeerbaarheid, feitelijke juistheid en eigen expertise.
Er worden geen aparte AI-teksten, massaal gegenereerde varianten of
`llms.txt`-achtige omwegen als vervanging voor goede inhoud gebruikt.

Bij iedere pagina leggen we vast wie de inhoud inhoudelijk heeft beoordeeld en
wanneer dat gebeurde. Feiten die kunnen veranderen worden periodiek herzien.
Waar mogelijk voegen we concrete praktijkkennis uit technische recruitment en
field service toe; dat onderscheidt de pagina's van algemene woordenboektekst.

Voor het vullen worden eerst de canonieke termen, hiërarchie, URL-structuur en
redirectregels goedgekeurd. De huidige seed- en testtermen worden niet eerder
verwijderd.

### Pods-relaties van Beroepengidsartikelen

Een Beroepengidsartikel gebruikt het Pods-veld `introduction` voor de zichtbare
introductie. De standaard WordPress-samenvatting is niet de bron van die tekst.

De actieve snippets lezen alle ingevulde Pods-velden met het voorvoegsel
`relationship_`, behalve `relationship_vacature_button_url`. In de normale
situatie selecteren we in zo'n veld de overeenkomstige term uit de
tegenliggende vacaturetaxonomie, bijvoorbeeld vacatureterm
`field service engineer` in `relationship_functienaam`. Verschillende
taxonomieën worden samen als AND-filter gebruikt. Meerdere termen binnen
`kennisgebied` worden eveneens met AND gecombineerd; binnen andere taxonomieën
worden meerdere termen als IN/OR behandeld.

De vacatureknop en de bijbehorende combo-archief-URL worden automatisch uit
deze actieve relaties opgebouwd. `relationship_vacature_button_url` wordt
alleen ingevuld wanneer bewust een afwijkende, expliciete taxonomieterm als
buttonbestemming nodig is. Het veld is geen standaardverplichting en wordt niet
gebruikt om de vacaturequery te filteren.

De lijsten met gerelateerde kennisgebieden, bedrijfssectoren,
functie-inhouden, opleidingsniveaus en werkgebieden worden afgeleid uit de open
vacatures die aan alle actieve relaties voldoen. Onjuiste vacaturetaxonomieën
werken daardoor rechtstreeks door in het Beroepengidsartikel. Dit werd zichtbaar
toen bij `Wat is een Field Service Engineer?` aanvankelijk alleen `LBO`
verscheen. Op 17 oktober 2026 zijn de opleidingsniveaus van alle 63
gepubliceerde vacatures hersteld uit de oude live-WordPress-export. De
functionele hertest toont nu HBO, LBO, MBO en Universiteit. Daarmee is bevestigd
dat het Beroepengidsartikel geen handmatige opleidingsrelatie nodig heeft: de
actieve snippets leiden deze lijst correct af uit de gekoppelde vacatures.

## TODO: opleidingsrichtingen in vacatures, Supabase en Appsmith

Voorlopig blijft de bestaande Beroepengids-taxonomie
`beroep_opleidingsniveau` ongewijzigd en gekoppeld aan het bestaande
vacature-`opleidingsniveau`. Er wordt nu geen overkoepelende taxonomie
`Opleidingen` aangemaakt en er worden geen opleidingsrichtingen vooruitlopend
ingericht of gekoppeld.

De vacaturetaxonomie voor opleidingsrichtingen bestaat al: de technische naam
is `vac_studierichting` en het zichtbare beheerlabel is `Studierichtingen`.
Het latere werk betreft dus het controleren en vullen van de termen en
vacaturekoppelingen, plus het ontwerpen van de bijpassende
Beroepengids-taxonomie; niet het opnieuw aanmaken van de vacaturetaxonomie.

Later pakken we dit als één samenhangend traject op:

1. een gecontroleerde, canonieke lijst met opleidingsrichtingen, slugs en
   eventuele synoniemen vaststellen;
2. bepalen hoe de bestaande WordPress-taxonomie `vac_studierichting` wordt
   gevuld en hoe bestaande vacatures gecontroleerd aan één of meerdere
   richtingen worden gekoppeld;
3. het bijpassende gestructureerde Supabase-model beoordelen en inrichten,
   inclusief een meervoudige relatie tussen vacatures en
   opleidingsrichtingen in plaats van vrije tekst;
4. Appsmith aanpassen zodat opleidingsrichtingen bij vacatures kunnen worden
   bekeken, toegevoegd en gewijzigd met dezelfde canonieke waarden;
5. de Beroepengids-taxonomie `Opleidingen`, de Pods-relaties en de betrokken
   snippets uitbreiden, zodat opleidingsrichtingen naar de juiste vacatures
   kunnen doorlinken;
6. synchronisatie, validatie, bestaande data, filters, REST-uitvoer en
   end-to-endwerking in WordPress, Supabase en Appsmith gezamenlijk testen.

Deze TODO is alleen een ontwerp- en implementatieopdracht voor later. Er worden
nu geen opleidingsrichtingen, mappings of databasevelden aangemaakt of
gewijzigd.

Pas wanneer opleidingsrichtingen in WordPress-vacatures, Supabase en Appsmith
volledig zijn ontworpen, gevuld en getest, beoordelen we de overgang naar één
overkoepelende Beroepengids-taxonomie `Opleidingen`. Die kan dan
`Opleidingsniveau`, `Opleidingsrichting` en eventuele aanvullende onderwerpen
onderbrengen. Bij die overgang horen expliciet: migratie van bestaande
Beroepengids-termen en Pods-relaties, aanpassing van snippets en sitemap,
controle van templates, filters en SEO, behoud of 301-redirect van bestaande
URL's en een functionele hertest voordat `beroep_opleidingsniveau` wordt
uitgefaseerd.

## TODO: werkgebieden, opleidingsniveaus en reisprofielen

- De bestaande vacaturetaxonomie `werkgebied` nog inhoudelijk nalopen, vullen
  en aan bestaande vacatures koppelen; daarna vergelijken met
  `beroep_werkgebied`, plaatsen, postcodes en Supabase.
- De herstelde vacaturetaxonomie `opleidingsniveau` en
  `beroep_opleidingsniveau` later verder gelijktrekken met één goedgekeurde
  canonieke termenlijst. De 63 bestaande gepubliceerde vacaturekoppelingen zijn
  op 17 oktober 2026 uit de oude live-export hersteld en exact gecontroleerd;
  toekomstig werk betreft nieuwe vacatures, eventuele ontbrekende termen en de
  WordPress-, Supabase- en Appsmith-keten.
- De bestaande vacaturetaxonomie `reisprofiel` (beheerlabel `Reisprofielen`)
  inclusief termen en vacaturekoppelingen nalopen. Daarbij expliciet bepalen
  hoe de samengestelde WordPress-termen zich verhouden tot de gestructureerde
  reisvelden in Supabase.
- Na het bovenstaande besluiten of `Studierichtingen` en `Reisprofielen` als
  eigen spiegelende taxonomieën aan de Beroepengids worden toegevoegd en welke
  Pods-relaties, snippets, filters, sitemapregels en URL's daarvoor nodig zijn.

## Blog en Kennisbank

Het posttype Blog verdwijnt uiteindelijk. Blogartikelen worden alleen binnen
WordPress herplaatst naar Beroepengids of Kennisbank en hoeven niet als
artikelen naar Supabase.

Per artikel beoordelen we eerst:

- inhoudelijke bestemming;
- actualiteit en noodzakelijke updates;
- overlap met andere artikelen;
- titel en zoekintentie;
- behoud of wijziging van de slug;
- metadata en structured data;
- interne links;
- 301-redirect van de oude URL wanneer de URL verandert.

## Besluitprocedure

We behandelen de Google Sheet tabblad voor tabblad. Ieder besluit krijgt een
status, motivatie en gevolgen voor WordPress, Supabase, SEO en bestaande
functionaliteit. Pas na goedkeuring worden mappings of systemen aangepast.
