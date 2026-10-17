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

Een vacature kan dus meerdere geografische relaties hebben. Het bedrijfsadres
mag niet automatisch als wervingsregio worden geïnterpreteerd, en een
wervingsregio mag niet automatisch als feitelijke werklocatie worden
gepubliceerd.

De structured data en SEO-metadata worden later uit de juiste relaties
opgebouwd. De huidige SNIP-plugin leest alles uit de WordPress-taxonomie
`locatie`; die bestaande werking is beschreven in `snippets/README.md` en moet
bij het nieuwe model bewust worden vervangen of aangepast.

## Reizen

WordPress gebruikt `dagen-internationaal` en `reisprofiel` als taxonomietermen.
Supabase heeft voor reisvereisten een gestructureerd model. Dit betekent niet
dat gegevens verdwijnen. Per bestaande WordPress-term bepalen we samen welke
concrete velden en grenzen hij vertegenwoordigt. Tot dat besluit blijven de
WordPress-termen ongewijzigd.

## Beroepengids

Beroepengids wordt een inhoudelijke spiegel van de relevante canonieke
taxonomieën. Een sector, functie, capability, kennisgebied of opleidingsniveau
verwijst naar hetzelfde onderliggende begrip als bij vacatures.

Beroepengids-pagina's bieden duurzame uitleg en linken door naar actuele
vacatures die aan hetzelfde begrip zijn gekoppeld. De contentpagina en de
vacaturefilter zijn verschillende presentaties van hetzelfde concept, geen
losse duplicaten.

Voor het vullen worden eerst de canonieke termen, hiërarchie, URL-structuur en
redirectregels goedgekeurd. De huidige seed- en testtermen worden niet eerder
verwijderd.

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
