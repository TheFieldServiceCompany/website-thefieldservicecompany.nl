# Content- en taxonomie-inventaris

Datum: 17 oktober 2026
Status: onderzoek en voorstel; nog geen wijzigingen in WordPress of Supabase

## Werkdocument

De volledige vergelijking staat in de Google Sheet
[TFSC content- en taxonomie-inventaris](https://docs.google.com/spreadsheets/d/1yQGZp-Qo9GVIgLmLbuUYTS-UbwGQoqFSEmSBT7fBl3o/edit).

De Sheet bevat:

- een index van de 18 bestaande blogartikelen;
- een voorlopig voorstel per blog voor Beroepengids of Kennisbank;
- alle relevante WordPress-custom-taxonomieën en 256 termen;
- alle 250 onderzochte canonieke Supabase-vocabulaire-items;
- een vergelijking op termniveau met exacte, waarschijnlijke en ontbrekende
  matches;
- een eerste taxonomieplan voor Beroepengids en Kennisbank.

## Huidige situatie

- Vacatures gebruiken 12 custom taxonomieën met samen 243 termen.
- Supabase bevat nog geen expliciete WordPress-termkoppelingen in
  `source_term_mappings`.
- Beroepengids heeft 0 artikelen en 6 taxonomieën. Vier taxonomieën bevatten
  elk één ongebruikte test- of seedterm.
- Kennisbank heeft 0 artikelen en momenteel geen gekoppelde custom
  taxonomieën.
- De 18 blogs zijn voorlopig verdeeld in 9 Beroepengids-kandidaten en 9
  Kennisbank-kandidaten. Dit is nog geen migratiebesluit.

## Voorgesteld doelmodel

| WordPress-taxonomie | Supabase-model | Besluit dat nog nodig is |
| --- | --- | --- |
| `bedrijfssector` | `industry_sectors` | Namen, synoniemen en hiërarchie vaststellen |
| `dienstverband` | `employment_types` | Rechtstreeks koppelen |
| `functie-inhoud` | `capabilities` | Synoniemen consolideren naar kernvaardigheden |
| `functienaam` | `role_families` | Functievarianten aan rol-families koppelen |
| `kennisgebied` | `knowledge_domains` | Kern- en detailniveau scheiden |
| `opleidingsniveau` | `education_levels` | Onder meer LBO/VMBO en Universiteit/WO vastleggen |
| `vac_studierichting` | `education_fields` | Drie resterende modelverschillen beoordelen |
| `vac_status` | `vacancy_statuses` | WordPress-publicatiestatus aan CRM-workflow koppelen |
| `locatie` | `geographic_areas` en vacature-relaties | Alle plaatsen en postcodes opnemen; bedrijfsadres, werklocatie en woon-/wervingsregio scheiden |
| `werkgebied` | relatie naar `geographic_areas` | Niet als tweede losse geografie-vocabulaire onderhouden |
| `dagen-internationaal` | `vacancy_travel_requirements` | Bandtermen naar numerieke grenzen omzetten |
| `reisprofiel` | `vacancy_travel_requirements` | Samengestelde labels naar concrete velden ontleden |

`dagen-internationaal` en `reisprofiel` worden niet zonder beoordeling als
losse Supabase-termenlijst gekopieerd. De bestaande betekenis blijft behouden;
per term bepalen we samen hoe die naar de concrete reisvelden wordt vertaald.

Alle WordPress-plaatsen en postcodes moeten ook in Supabase beschikbaar komen.
Daarnaast blijven de bestaande land- en provinciekoppelingen aan vacatures
behouden. Bedrijfsadres, feitelijke vacaturelocatie en toegestane woon- of
wervingsregio worden als afzonderlijke relaties gemodelleerd.

## Beroepengids en Kennisbank

De zes bestaande Beroepengids-taxonomieën worden na goedkeuring een inhoudelijke
spiegel van dezelfde canonieke concepten als vacatures. Beroepengids-pagina's
linken door naar actuele vacatures met hetzelfde begrip. Testtermen worden pas
daarna verwijderd of vervangen.

Voor Kennisbank moet eerst de inhoudsstructuur worden ontworpen: onderwerpen,
contenttypes, doelgroepen en eventueel niveau. Pas daarna worden taxonomieën en
URL-structuur aangemaakt.

## Veilige volgorde

1. Per vacaturetaxonomie het doelmodel goedkeuren.
2. De voorgestelde termmatches en consolidaties in de Sheet beoordelen.
3. Goedgekeurde WordPress–Supabase-mappings expliciet vastleggen.
4. Kennisbank-taxonomieën en URL-structuur ontwerpen.
5. Beroepengids-taxonomieën vanuit de goedgekeurde canonieke concepten vullen.
6. Blogartikelen één voor één inhoudelijk beoordelen en bijwerken; pas daarna
   verplaatsen, inclusief slugbesluit, 301-redirect, metadata, interne links en
   functionele/SEO-controle. Blogcontent wordt niet naar Supabase verplaatst.

## Openstaande opschoning

- ongebruikte media inventariseren en later gecontroleerd verwijderen;
- database-opschoning voorbereiden en pas na back-up uitvoeren;
- overbodige of dubbele taxonomietermen na de mappingbesluiten consolideren.
