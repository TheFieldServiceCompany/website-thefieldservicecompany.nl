# Audit live vacaturetaxonomiekoppelingen

Datum: 17 oktober 2026  
Status: alleen-lezen controle; geen WordPress- of Supabase-data gewijzigd

## Bron en methode

De 63 gepubliceerde vacatures in de oude live-WordPress-export van 16 oktober
2026 zijn op slug vergeleken met de 63 huidige gepubliceerde live-vacatures.
Voor iedere vacature zijn de gekoppelde termslugs uit de export vergeleken met
de actuele WordPress REST-term-ID's, nadat die ID's weer naar huidige slugs zijn
vertaald.

Er ontbreken geen vacatures en er staan geen extra vacature-slugs in de
huidige set. De oude taxonomie `opleiding` is bij de vergelijking vertaald naar
de huidige taxonomie `opleidingsniveau`.

Drie bekende termcorrecties zijn daarnaast als dezelfde betekenis behandeld:

- bedrijfssector `high-tech` → `hightech`;
- functienaam `service-monteur` → `servicemonteur`;
- locatie `heereveen` → `heerenveen`.

Deze correcties zijn geen verloren taxonomiekoppelingen.

## Resultaat per bestaande taxonomie

| Huidige taxonomie | Exact na normalisatie | Echte afwijkingen | Bevinding |
|---|---:|---:|---|
| `bedrijfssector` | 63/63 | 0 | Vier koppelingen gebruiken nu de gecorrigeerde slug `hightech`. |
| `dagen-internationaal` | 63/63 | 0 | Volledig gelijk. |
| `dienstverband` | 63/63 | 0 | Volledig gelijk. |
| `functie-inhoud` | 62/63 | 1 | Vacature 23277 mist `inbedrijfstellen`. |
| `functienaam` | 60/63 | 3 | Twee koelmonteurvacatures missen bredere functienamen; vacature 23277 gebruikt nu `service-engineer` in plaats van `field-service-engineer`. |
| `kennisgebied` | 56/63 | 7 | Vacature 23277 mist `elektrotechniek`; zes vacatures hebben nu aanvullend `rotating-equipment`. |
| `locatie` | 62/63 | 1 | De spelling `Heerenveen` is correct gemigreerd; alleen vacature 23277 mist daarnaast `Tilburg`. |
| `opleidingsniveau` | 63/63 | 0 | Volledig gelijk na het eerder uitgevoerde herstel. |
| `vac_status` | 63/63 | 0 | Volledig gelijk. |
| `werkgebied` | 63/63 | 0 | Volledig gelijk. |

## Concrete inhoudelijke verschillen

### Vacature 23277 — HVAC/koeltechniek internationaal allround

Huidige slug:
`field-service-engineer-hvac-koeltechniek-internationaal-allround`

Ten opzichte van de oude live-export:

- `functie-inhoud`: `inbedrijfstellen` ontbreekt;
- `kennisgebied`: `elektrotechniek` ontbreekt;
- `locatie`: `Tilburg` ontbreekt;
- `functienaam`: oud `field-service-engineer`, nu `service-engineer`.

Omdat de vier afwijkingen bij dezelfde vacature zitten en inhoudelijk ook een
latere redactionele keuze kunnen zijn, worden ze niet automatisch teruggezet.

### Junior koelmonteur en koelmonteur koudetechniek

Bij vacature 18605 (`junior-koelmonteur`) en vacature 18590
(`koelmonteur-koudetechniek`) is `servicemonteur` nog gekoppeld, maar zijn de
oude aanvullende functienamen `field-service-engineer` en `service-engineer`
niet meer gekoppeld. Dit kan een bewuste aanscherping zijn en vereist daarom
een inhoudelijk besluit.

### Aanvullend kennisgebied Rotating Equipment

Zes vacatures hebben nu aanvullend het kennisgebied `rotating-equipment`; deze
koppelingen stonden niet in de oude export:

- 12838 — internationaal service engineer rotating equipment;
- 12917 — internationaal commissioning engineer rotating equipment;
- 12934 — service monteur rotating equipment internationaal;
- 13186 — internationaal service engineer compressoren;
- 13595 — field service engineer compressoren Benelux/Duitsland;
- 13601 — field service supervisor compressoren Benelux/Duitsland.

De toevoegingen passen inhoudelijk bij de functietitels. Ze worden daarom als
mogelijke verrijking beschouwd en niet als migratiefout verwijderd.

## Dubbele functienaamterm

De huidige functienaamtaxonomie bevat zowel:

- term-ID 230: `Servicemonteur`, slug `servicemonteur`, gekoppeld aan 16
  vacatures;
- term-ID 420: `Service Monteur`, slug `service-monteur`, gekoppeld aan alleen
  vacature 23645.

De koppelingen zijn semantisch behouden, maar de dubbele term moet later bewust
worden geconsolideerd. Daarbij moeten archief-URL's, redirects, Pods-relaties,
snippets, filters en sitemapuitvoer worden gecontroleerd.

## Nieuwe huidige taxonomieën zonder oude tegenhanger

De oude export bevatte geen koppelingen voor deze later toegevoegde
taxonomieën:

- `reisprofiel`: 25 termen beschikbaar, 0 van 63 vacatures gekoppeld;
- `vac_studierichting`: 18 termen beschikbaar, 0 van 63 vacatures gekoppeld.

Dit is geen aantoonbare migratiefout, maar bevestigt de bestaande TODO om beide
taxonomieën inhoudelijk te beoordelen en de vacatures, Supabase, Appsmith en
Beroepengids later gecontroleerd te vullen.

## Besluitpunten vóór eventuele correctie

1. Bepalen of de vier afwijkingen van vacature 23277 moeten worden hersteld of
   bewust zijn aangebracht.
2. Bepalen of de twee koelmonteurvacatures naast `servicemonteur` ook weer
   `field-service-engineer` en `service-engineer` moeten krijgen.
3. Bevestigen dat de zes extra koppelingen aan `rotating-equipment` als
   inhoudelijke verrijking mogen blijven staan.
4. Een migratie- en redirectplan maken voor de dubbele termen
   `servicemonteur` en `service-monteur`.

