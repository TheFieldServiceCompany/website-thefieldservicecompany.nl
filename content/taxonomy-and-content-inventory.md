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
| `dagen-internationaal` | Gedeelde canonieke termen voor vacatures en kandidaten | Bestaande vacatures en kandidaten onder de juiste term indelen |
| `reisprofiel` | `vacancy_travel_requirements` | Samengestelde labels naar concrete velden ontleden |

`dagen-internationaal` blijft een gedeelde categorie met de afgesproken termen.
Zowel vacatures als kandidaten krijgen in Supabase relaties naar deze termen.
Bestaande data moet later gecontroleerd worden ingedeeld. `reisprofiel` en
eventuele aanvullende concrete reisvelden beoordelen we afzonderlijk.

Voor `vac_studierichting` is besloten de inhoudelijke inrichting uit te stellen.
Deze vacaturetaxonomie bestaat al in WordPress en heeft in het beheer het label
`Studierichtingen`; zij hoeft dus niet opnieuw voor vacatures te worden
aangemaakt. De huidige termen en vacaturekoppelingen worden later gezamenlijk
gecontroleerd en waar nodig gevuld.
De huidige uitwerking van Beroepengids blijft voorlopig beperkt tot
`opleidingsniveau`. Als afzonderlijke TODO worden opleidingsrichtingen later in
één traject ontworpen en doorgevoerd voor WordPress-vacatures, het
gestructureerde Supabase-model en de vacature-invoer en -bewerking in Appsmith.
Daarbij worden eerst de canonieke termen en slugs vastgesteld; pas daarna volgen
koppelingen, datamigratie, Beroepengids-relaties en end-to-endtests.

De eventuele latere overgang van `beroep_opleidingsniveau` naar de bredere
Beroepengids-taxonomie `Opleidingen` is onderdeel van dezelfde TODO en gebeurt
pas nadat de vacature-, Supabase- en Appsmith-keten gereed is. Bestaande URL's,
relaties, snippets, sitemap en SEO worden dan als expliciete
migratieonderdelen behandeld.

Alle WordPress-plaatsen en postcodes moeten ook in Supabase beschikbaar komen.
Daarnaast blijven de bestaande land- en provinciekoppelingen aan vacatures
behouden. Bedrijfsadres, feitelijke vacaturelocatie en toegestane woon- of
wervingsregio worden als afzonderlijke relaties gemodelleerd.
De huidige regio's en grotere plaatsen zijn voor nu bepaald. Later controleren
we als TODO of aanvullende plaatsen of postcodes nodig zijn.

## TODO: vacaturetaxonomieën en Beroepengids-spiegels nalopen

De volgende onderdelen worden later per taxonomie én per termenlijst samen
beoordeeld. Er worden nu geen termen, koppelingen of Pods-definities gewijzigd.

- `werkgebied` bestaat al bij vacatures, maar de definitieve termen moeten nog
  worden gecontroleerd, gevuld en aan de juiste vacatures gekoppeld. Daarna
  wordt de aansluiting op `beroep_werkgebied`, plaatsen, postcodes en Supabase
  gecontroleerd.
- `opleidingsniveau` bestaat bij vacatures en als `beroep_opleidingsniveau` in
  de Beroepengids. De canonieke niveaus en slugs moeten nog worden vastgesteld,
  beide termenlijsten moeten worden gevuld en bestaande vacatures moeten
  gecontroleerd worden ingedeeld.
- `vac_studierichting` bestaat al bij vacatures onder het beheerlabel
  `Studierichtingen`. Later beoordelen en vullen we de termen en relaties. Pas
  daarna ontwerpen we een bijpassende custom taxonomie voor de Beroepengids en
  de koppeling met Supabase en Appsmith.
- `reisprofiel` bestaat al bij vacatures onder het beheerlabel
  `Reisprofielen`. Later beoordelen we de termen, bestaande
  vacaturekoppelingen en de relatie met de gestructureerde reisvelden in
  Supabase. Ook bepalen we dan of en hoe een spiegelende Beroepengids-taxonomie
  wordt toegevoegd.

Bij deze controle lopen we steeds de taxonomie-instellingen, termen, slugs,
synoniemen, hiërarchie, bestaande vacaturekoppelingen, Beroepengids-relaties,
filters, snippets, sitemap, REST-uitvoer, Supabase-mapping en Appsmith-invoer
gezamenlijk na.

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

## TODO: blogartikelen gecontroleerd verplaatsen

De bestaande blogartikelen worden niet in bulk en niet zonder inhoudelijke
beoordeling verplaatst. Per artikel voeren we later deze vaste stappen uit:

1. bepalen of het artikel thuishoort in Beroepengids of Kennisbank;
2. inhoud, actualiteit, zoekintentie en interne links beoordelen en bijwerken;
3. de definitieve titel, nieuwe slug, taxonomieën en metadata vaststellen;
4. het artikel naar het juiste posttype en de juiste locatie verplaatsen;
5. in Rank Math een permanente 301-redirect van de oude URL naar de nieuwe URL
   aanmaken;
6. oude en nieuwe URL, canonical, sitemap, interne links en eventuele externe
   verwijzingen controleren;
7. pas na een geslaagde hertest het artikel als afgerond markeren.

De bestaande blogindex en de voorlopige verdeling in de Google Sheet blijven
het uitgangspunt. Geen oude URL verdwijnt zonder een gecontroleerde redirect.

## TODO: omvang en prestaties van WordPress

De WordPress-installatie en de back-ups zijn uitzonderlijk groot. Eerst wordt
een alleen-lezen omvangsanalyse gemaakt; er wordt niets verwijderd zonder een
actuele back-up, een controleerbare inventaris en expliciete goedkeuring.

- uitsplitsen hoeveel ruimte uploads, database, plugins, thema's en overige
  mappen gebruiken;
- controleren op oude migratiebestanden, lokale back-upsets, cachemappen,
  logbestanden en tijdelijke bestanden die onbedoeld in back-ups meekomen;
- dubbele, ongebruikte en door Elementor gegenereerde media inventariseren,
  inclusief de bijbehorende metadata en afgeleide afbeeldingsformaten;
- grote databasetabellen en groei door revisies, transients, logs, crondata,
  formulierinzendingen en verweesde Elementor-/Pods-data onderzoeken;
- vaststellen welke onderdelen veilig van de reguliere back-up kunnen worden
  uitgesloten en welke gegevens vanwege herstelbaarheid of bewaarplicht moeten
  blijven bestaan;
- daarna een gefaseerd optimalisatie- en opschoningsvoorstel maken, met vóór-
  en nameting en een herstelplan.

## TODO: Nederlandstalige formuliermeldingen

Alle bezoekersmeldingen van de formulieren moeten later in duidelijk Nederlands
worden gezet. Dit geldt voor validatie, verplichte velden, uploadfouten,
verzendfouten en succesmeldingen. Algemene teksten zoals "This field is
required" zijn niet duidelijk genoeg wanneer het om de verplichte
privacyverklaring gaat. Die checkbox krijgt daarom een specifieke melding die
expliciet uitlegt dat de privacyverklaring nog moet worden geaccepteerd.

Bij de uitwerking controleren we per formulier zowel de meldingen van Elementor
als eventuele browser-, plugin- en servermeldingen, zodat bezoekers nergens een
onduidelijke Engelse fouttekst zien.
