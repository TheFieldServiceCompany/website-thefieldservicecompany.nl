# Reisprofielen — WordPress, Supabase en Appsmith

> **Actuele eindstatus 19 oktober 2026:** dit document bevat ook de eerdere
> inventarisatie en de besluitvorming onderweg. Het definitieve model voegt
> naast Reisduur en Weekenden thuis ook `Hotel nachten/maand` toe. De langste
> Reisduur-band heet `meer dan 30 dagen`. Supabase en Appsmith gebruiken de
> vijf gelijke canonieke veldnamen uit
> `audits/2026-10-19-travel-uitvoering-appsmith-en-branchopschoning.md`. De 25
> oude WordPress-Reisprofiel-termen zijn inmiddels verwijderd; bestaande
> vacatures moeten inhoudelijk opnieuw worden geclassificeerd.

Datum: 19 oktober 2026  
Status: canoniek model uitgevoerd in Supabase, Appsmith en LIVE WordPress

## Bevestigde architectuur

- **Appsmith is het CRM en de beheerinterface.**
- **Supabase is de canonieke database en bevat de operationele logica.**
- **WordPress is de publieke publicatie-, filter- en SEO-laag.**
- **EngageBay is uitsluitend legacy- en migratiebron.** Het is geen toekomstig
  CRM en ook geen extra doelsysteem naast Appsmith.

Appsmith hoeft daarom niet via een tweede CRM-API vanuit Supabase te worden
gesynchroniseerd. Appsmith leest en beheert de canonieke records in Supabase.
De noodzakelijke synchronisatie is die tussen WordPress-keuzes en het
bestaande Supabase/Appsmith-model.

## Teruggevonden bespreking over Reisprofielen

De live controle in de eerdere chat vond 25 termen zonder descriptions. Op dat
moment waren zes termen gekoppeld aan de vacature `Field Service Engineer HVAC
Koeltechniek | Internationaal Allround`:

- `Hotelovernachting 50%`;
- `Internationaal reizen`;
- `Nachten thuis 50%`;
- `Reizen Internationaal`;
- `Vaak van huis`;
- `Veel vliegen`.

De inhoudelijke conclusie was dat één WordPress-taxonomie nu verschillende
soorten informatie door elkaar zet:

1. **Bestemming of gebied:** Nederland, België, Benelux, Duitsland, Polen en
   internationaal.
2. **Overnachtingen:** hotelovernachtingen, percentages en weekdagen.
3. **Thuis zijn:** 25%, 50%, 75%, 100%, bijna dagelijks en weekenden.
4. **Reispatroon:** korte trips, langdurig op locatie, vaak van huis en veel
   vliegen.

Daardoor zijn labels inhoudelijk dubbel of elkaars omgekeerde, onder meer:

- `Internationaal reizen` en `Reizen Internationaal`;
- `Nationaal reizen` en `Reizen Nationaal`;
- `Bijna elke dag thuis` en `Nachten thuis 100%`;
- `Kort op locatie` en `Korte trips`;
- `Hotelovernachting 25%` en `Nachten thuis 75%`;
- `Hotelovernachting 50%` en `Nachten thuis 50%`;
- `Hotelovernachting 75%` en `Nachten thuis 25%`.

De gebruiker heeft daarna verduidelijkt dat een deel van deze doublures bewust
als zoektermvariant is aangemaakt voor toekomstige archief- en SEO-ingangen.
Daarom worden termen niet automatisch verwijderd, hernoemd of samengevoegd.

## Bestaande logica in Supabase en Appsmith

De operationele gegevens zijn al gesplitst in concrete dimensies.

### Kandidaten

- `candidate_work_area_preferences` en `geographic_areas`: gewenste
  werkgebieden, landen en regio's;
- `candidate_travel_preferences`:
  - `willing_international_travel`;
  - `max_international_days_per_year`;
  - `max_consecutive_nights_away`;
  - `overnight_stays_acceptable`;
  - `weekend_travel_acceptable`;
- `candidate_mobility_preferences`:
  - `max_one_way_commute_km`;
  - `relocation_willing`;
  - `own_vehicle_available`.

### Vacatures

- `vacancy_work_areas`: werkgebieden;
- `vacancy_travel_requirements`:
  - `international_travel_required`;
  - `max_international_travel_days_per_year`;
  - `max_consecutive_nights_away`;
  - `overnight_stays_required`;
  - `weekend_travel_required`;
- `vacancy_mobility_requirements`:
  - `own_vehicle_required`;
  - `relocation_required`.

De bestaande matching vergelijkt werkgebieden, internationaal reizen,
overnachtingen, weekendreizen, het maximale aantal internationale dagen,
eigen vervoer en verhuizen al afzonderlijk. Eén platte lijst met
Reisprofielen zou informatie verliezen en dubbele Appsmith-keuzes veroorzaken.

## Voorgesteld doelmodel voor de koppeling

WordPress en Supabase hoeven niet overal dezelfde zichtbare labels te hebben.
Ze moeten wel dezelfde betekenis delen.

| WordPress-betekenis | Canonieke bestemming | Aandachtspunt |
| --- | --- | --- |
| Land, regio of internationaal werkgebied | `geographic_areas`, `vacancy_work_areas` en kandidaat-werkgebiedvoorkeuren | Hoort operationeel niet in één algemeen Reisprofiel |
| Omvang internationaal werk | `dagen-internationaal` plus de velden voor maximaal aantal internationale dagen | Eerst de grenzen van iedere term exact vastleggen |
| Reisduur per afzonderlijke reis | Meetbare duurband per aaneengesloten reis | `Elke nacht thuis` betekent geen overnachting; iedere hogere band impliceert minimaal één overnachting |
| Weekenden thuis | Percentage van de weekenden dat iemand thuis is of een vacature naar verwachting thuis biedt | Banden 0%, 25%, 50%, 75% en 100%; onbekend blijft apart |
| Eigen vervoer, woon-werkafstand en verhuizen | Mobility-velden | Niet onder Reisprofiel opslaan |
| Korte trips, langdurig op locatie, veel vliegen en vaak van huis | Zoekwoorden en contentthema's | Niet als subjectieve of dubbele operationele keuze in Appsmith toevoegen |

Bewuste zoekvarianten kunnen in WordPress blijven bestaan als publieke termen
of aliassen. Meerdere WordPress-termen mogen dan naar dezelfde canonieke
betekenis verwijzen. Appsmith toont één operationele keuze of één concreet veld,
niet iedere SEO-variant als aparte waarde.

## Uitvoeringsstatus

1. [x] Alle 25 actuele WordPress-termen, slugs, beschrijvingen, tellingen en
   vacaturekoppelingen alleen-lezen uitlezen.
2. [x] Per term een controlematrix maken met dimensie, betekenis, mogelijke
   alias, Supabase-doelveld en informatie die niet veilig kan worden afgeleid.
3. [x] Conflicten en ontbrekende keuzes aan de gebruiker voorleggen.
4. [x] Besluiten welke canonieke keuzes Appsmith toont.
5. [x] Dezelfde aanpak vastleggen voor de overige WordPress-taxonomieën en
   keuzelijsten.
6. [x] Na goedkeuring een testbaar implementatie- en migratieplan maken voor
   WordPress, Supabase en Appsmith.
7. [x] De tien nieuwe canonieke WordPress-termen met operationele descriptions
   additive toevoegen en via REST verifiëren.
8. [ ] Legacytermen, vacature `23277`, redirects, archiefteksten en definitieve
   frontendplaatsing handmatig valideren voordat iets wordt verwijderd of
   omgezet.

De inventarisatie begon alleen-lezen. Na expliciete goedkeuring zijn Supabase,
de Appsmith-bron en de additive WordPress-structuur aangepast. WordPress-
legacytermen zijn niet verwijderd of omgezet. Zie voor de exacte live-ID's en
verificatie `2026-10-19-reisduur-weekenden-thuis-live.md`.

## Actuele alleen-lezen inventarisatie

Op 19 oktober 2026 is de LIVE-taxonomie opnieuw via de publieke WordPress REST-
API uitgelezen. De ruwe momentopname staat in
`audits/exports/reisprofiel-live-2026-10-19.json`.

- Er zijn 25 termen.
- Alle 25 descriptions zijn leeg.
- Zes termen hebben `count = 1`; de overige negentien hebben `count = 0`.
- De zes gebruikte termen staan allemaal op vacature `23277`, `Field Service
  Engineer HVAC Koeltechniek | Internationaal Allround`.
- De taxonomie is hiërarchisch geregistreerd, maar alle termen staan op het
  hoogste niveau.

## Voorstel per term

Dit is een voorstel en nog geen verwijderopdracht. Voor verwijdering worden
eerst de vacaturekoppeling, redirects, indexering en descriptions geregeld.

| ID | Huidige term | Gebruik | Voorstel | Canonieke bestemming of reden |
| ---: | --- | ---: | --- | --- |
| 468 | Internationaal reizen | 1 | Verwijderen uit Reisprofiel | `international_travel_required` en Werkgebied Internationaal |
| 469 | Nationaal reizen | 0 | Verwijderen uit Reisprofiel | Internationaal reizen = nee en Werkgebied Nederland |
| 470 | Hotelovernachting | 0 | Verwijderen als losse filterterm | Overnachten wordt exact afgeleid uit Reisduur: vanaf 2 reisdagen is er minimaal één overnachting; het type accommodatie niet |
| 471 | Vaak van huis | 1 | Verwijderen als losse term | Te vaag; afleiden uit internationale dagen en nachten van huis |
| 472 | Langdurig op locatie | 0 | Verwijderen als subjectieve filterterm | Alleen na inhoudelijke controle naar een meetbare Reisduur-band mappen; zoekvariant in descriptions bewaren |
| 473 | Kort op locatie | 0 | Verwijderen als subjectieve filterterm | Alleen na inhoudelijke controle naar een meetbare Reisduur-band mappen; zoekvariant in descriptions bewaren |
| 474 | Veel vliegen | 1 | Verwijderen als losse term | Geen canoniek vervoersmiddelveld; als zoekterm in teksten behouden |
| 476 | Bijna elke dag thuis | 0 | Verwijderen als subjectieve filterterm | Mogelijk `Reisduur = Elke nacht thuis`, maar alleen na inhoudelijke controle; zoekvariant bewaren |
| 480 | Hotelovernachting 25% | 0 | Verwijderen als filterterm | Niet gelijk aan 25% weekenden thuis; niet automatisch converteren |
| 481 | Hotelovernachting 50% | 1 | Verwijderen als filterterm | Niet gelijk aan 50% weekenden thuis; gekoppelde vacature handmatig valideren |
| 482 | Hotelovernachting 75% | 0 | Verwijderen als filterterm | Niet gelijk aan 75% weekenden thuis; niet automatisch converteren |
| 483 | Nachten thuis 100% | 0 | Verwijderen als filterterm | Niet gelijk aan 100% weekenden thuis; zoekvariant in descriptions bewaren |
| 484 | Nachten thuis 75% | 0 | Verwijderen als filterterm | Niet gelijk aan 75% weekenden thuis; niet automatisch converteren |
| 485 | Nachten thuis 50% | 1 | Verwijderen als filterterm | Niet gelijk aan 50% weekenden thuis; gekoppelde vacature handmatig valideren |
| 486 | Nachten thuis 25% | 0 | Verwijderen als filterterm | Niet gelijk aan 25% weekenden thuis; niet automatisch converteren |
| 487 | Weekenden thuis | 0 | Vervangen door meetbare waarden | Nieuwe band: 0%, 25%, 50%, 75% of 100% weekenden thuis |
| 488 | Hotelovernachting weekdagen | 0 | Verwijderen als samengestelde filterterm | Mogelijke combinatie van Reisduur en Weekenden thuis, maar niet automatisch afleiden |
| 489 | Korte trips | 0 | Verwijderen als subjectieve filterterm | Alleen na inhoudelijke controle naar een meetbare Reisduur-band mappen; zoekvariant bewaren |
| 490 | Reizen Nederland | 0 | Verwijderen uit Reisprofiel | Werkgebied Nederland |
| 491 | Reizen Internationaal | 1 | Verwijderen uit Reisprofiel | Duplicaat van Internationaal reizen; naar veld en Werkgebied |
| 492 | Reizen Nationaal | 0 | Verwijderen uit Reisprofiel | Duplicaat van Nationaal reizen |
| 493 | Reizen Benelux | 0 | Verwijderen uit Reisprofiel | Werkgebied Benelux |
| 494 | Reizen België | 0 | Verwijderen uit Reisprofiel | Werkgebied België |
| 495 | Reizen Duitsland | 0 | Verwijderen uit Reisprofiel | Werkgebied Duitsland |
| 496 | Reizen Polen | 0 | Verwijderen uit Reisprofiel | Eerst Polen als canoniek Werkgebied beoordelen |

Het eerdere voorstel om vijf publieke Reisprofielen te behouden is na
inhoudelijke bespreking ingetrokken. `Kort verblijf`, `Lang verblijf` en
`Bijna elke dag thuis` zijn zonder vaste grens niet meetbaar.

Het goedgekeurde eindresultaat vervangt de gemengde taxonomie `Reisprofiel`
door drie meetbare filterdimensies: Reisduur, Weekenden thuis en Hotel
nachten/maand. De WordPress-structuur is LIVE uitgevoerd; de 25 legacytermen
zijn later dezelfde dag na akkoord verwijderd. Bestaande vacatures worden niet
gegokt maar moeten inhoudelijk opnieuw worden geclassificeerd.

### Filterdimensie 1: Reisduur

`Reisduur` betekent de duur van één aaneengesloten reis, niet de looptijd van
het project en niet het totale aantal reisdagen per jaar. Voor de publieke
filter worden deze banden voorgesteld:

1. `Elke nacht thuis`;
2. `2–7 dagen`;
3. `8–14 dagen`;
4. `15–30 dagen`;
5. `meer dan 30 dagen`.

`0 dagen` hoort niet in Reisduur: dat betekent dat geen reis plaatsvindt en
wordt door het aparte ja/nee-veld voor reizen afgedekt. `Elke nacht thuis`
betekent dat iemand na iedere werkdag thuis overnacht; het is nadrukkelijk geen
reisduur van één dag. Iedere hogere band betekent automatisch minimaal één
overnachting. Reisduur bewijst echter niet of die overnachting in een hotel,
appartement, schip of andere accommodatie is. Daarom is Hotel nachten/maand
later als apart meetbaar veld toegevoegd, met WordPress-banden 0, 1–7, 8–14 en
15–31 en een numeriek Supabase/Appsmith-veld van 0 tot en met 31.

Voor matching moet de betekenis aan beide kanten expliciet zijn:

- kandidaat: **maximaal aanvaardbare Reisduur per reis**;
- vacature: **verwachte maximale Reisduur per reis**.

Het huidige Supabase-veld `max_consecutive_nights_away` telt nachten en kan dus
niet zonder conversieregel simpelweg als dagen worden hernoemd. Voor uitvoering
is een beoordeelde migratie of een nieuw dagenveld nodig. De bestaande waarden
blijven tot die tijd ongewijzigd.

### Filterdimensie 2: Weekenden thuis

`Weekenden thuis` wordt geen ja/nee-keuze, maar een meetbaar percentage:

1. `0%`;
2. `25%`;
3. `50%`;
4. `75%`;
5. `100%`.

De percentages worden gedefinieerd als het verwachte aandeel weekenden thuis
in een normale werkcyclus. In een eenvoudige vierweekse uitleg staat 25% voor
gemiddeld één weekend thuis, 50% voor twee, 75% voor drie en 100% voor alle
weekenden. `0%` is nodig voor volledigheid; `onbekend` blijft een aparte lege
datastatus en wordt niet als 0% geïnterpreteerd.

Ook hier verschilt de semantiek per kant:

- kandidaat: **minimaal gewenst percentage weekenden thuis**;
- vacature: **verwacht percentage weekenden thuis**.

De huidige booleans `weekend_travel_acceptable` en
`weekend_travel_required` kunnen deze vijf waarden niet exact opslaan. De
nieuwe percentagevelden zijn daarom nullable toegevoegd; bestaande waarden
zijn niet automatisch geconverteerd.

### Wat daarnaast al wordt afgedekt

Met de twee nieuwe dimensies is het reisprofiel nog niet het hele reismodel,
maar de overige hoofdvragen bestaan al als aparte velden:

| Vraag | Bestaande of voorgestelde dimensie |
| --- | --- |
| Waar wordt gereisd? | Werkgebieden |
| Hoeveel dagen per jaar internationaal? | Dagen internationaal |
| Hoe lang duurt één reis? | Nieuwe Reisduur-banden |
| Hoe vaak is iemand in het weekend thuis? | Nieuw percentage Weekenden thuis |
| Is internationaal reizen aanvaardbaar/verplicht? | Bestaand ja/nee-veld |

Daarmee zijn de belangrijkste filter- en matchingvragen gedekt.
**Reisfrequentie vervalt als aparte dimensie.** Als de verdeling van reizen
later inhoudelijk relevant blijkt, wordt dat eerst als nieuwe, aantoonbare
behoefte beoordeeld en niet alsnog in Reisduur verstopt.

Vervoersmiddel, type accommodatie en planbaarheid/vaste rotatie worden vooralsnog
als descriptions of contentthema behandeld. Ze worden alleen nieuwe canonieke
velden als er een aantoonbare CRM- of matchbehoefte is.

Alle 25 huidige termen moeten na goedkeuring naar de juiste meetbare dimensie,
Werkgebied of contentbestemming worden gemapt. De zes nu gebruikte termen
vereisen handmatige controle bij vacature `23277`; percentages nachten thuis
of hotelovernachtingen worden niet gegokt als percentages weekenden thuis.

### Zoekwoorden en bestaande URL's bewaren

Verwijderen uit de taxonomie betekent niet dat de woorden uit de website
verdwijnen:

- percentages, `nachten thuis`, `weekdagen weg`, `hotelovernachtingen` en
  `hotelovernachting weekdagen` komen natuurlijk terug in de descriptions van
  Reisduur en Weekenden thuis;
- `korte trips` en `kort op locatie` komen terug bij de passende korte
  Reisduur-banden;
- `langdurig op locatie` en `vaak van huis` komen terug bij de langere
  Reisduur-banden en de relevante Dagen-internationaal-archieven;
- `veel vliegen`, `internationaal reizen` en `reizen internationaal` komen
  terug in descriptions van Werkgebied Internationaal/Wereldwijd en de
  Dagen-internationaal-archieven;
- landnamen en regiovarianten komen terug op de bijbehorende
  Werkgebied-archieven.

Voor iedere verwijderde publieke term wordt vóór verwijdering bepaald of een
301-redirect nodig is. Een term wordt niet simpelweg verwijderd met een 404
als de URL geïndexeerd is, verkeer heeft of externe links ontvangt.

### Uitgebreidere zoekvragen buiten het filter

Niet iedere beschrijvende zoekterm hoeft een filterterm of taxonomiearchief te
worden. Voor onderwerpen zoals `veel vliegen voor werk`, `internationaal werken
als field service engineer`, `korte internationale projecten`,
`hotelovernachtingen voor werk` en `weekenden thuis in field service` kan later
een inhoudelijke landingspagina of Beroepengidsartikel worden gemaakt.

Zo'n pagina kan:

- het onderwerp uitgebreid en genuanceerd uitleggen;
- de langere zoekvarianten verwerken zonder de filters te vervuilen;
- linken naar Werkgebieden, Dagen internationaal en Reisduur;
- onderaan een dynamisch vacatureblok tonen dat op de canonieke Supabase-
  velden en WordPress-mapping is gebaseerd.

De voorkeursrichting is een herkenbare sectie binnen de Beroepengids, zoals
`Werken in field service` of `Reizen en verblijf`. Eerst wordt gecontroleerd of
dit binnen de bestaande Beroepengidsstructuur logisch past; anders wordt een
gerichte landingspagina gebruikt. Er worden geen dunne archiefpagina's gemaakt
alleen om een extra zoekwoord te bezitten.

## Uitvoering op 19 oktober 2026

Supabase staging (`bpfocgapqsrokmargsjr`) bevat nu aan beide kanten dezelfde
canonieke kolomnamen:

- `international_travel`;
- `max_international_days_per_year`;
- `maximum_travel_duration`;
- `weekends_home_percentage`;
- `hotel_nights_per_month`.

De database bevat check constraints voor de goedgekeurde keuzewaarden en
getalbereiken, bijgewerkte Appsmith-read/savefuncties en directionele matching.
Legacykolommen en compatibiliteitsaliassen blijven waar nodig tijdelijk
aanwezig.

De 1.773 bestaande kandidaat-reisrecords en het bestaande vacature-reisrecord
hebben voor de nieuwe velden `NULL` gehouden. Rollbacktests voor lezen,
opslaan, wissen, revisiebeveiliging en rechten zijn geslaagd. De Appsmith-
widgets zijn aangepast naar de meetbare selecties; oude overnachtingswidgets
zijn verborgen, niet verwijderd.

Eerdere live migraties:

- `20261019111559_measurable_travel_profile_fields`;
- `20261019111640_measurable_candidate_travel_workspot`;
- `20261019111730_measurable_vacancy_travel`;
- `20261019111924_measurable_vacancy_matching`.

De latere canonieke naamgeving, Hotel nights/month en Appsmith-functies zijn
samengebracht in migratie
`20261019151611_canonical_travel_fields_and_appsmith`.

Voor WordPress blijft de bestaande interne taxonomy-slug `reisprofiel`
behouden met beheerlabel Reisduur. De vijf canonieke Reisduur-termen,
`Weekenden thuis` en `Hotel nachten/maand` zijn LIVE uitgevoerd. De 25
legacytermen zijn later dezelfde dag na akkoord verwijderd. Vacature `23277`
bestaat nog, maar heeft geen Reisduur-relatie; descriptions, indexering,
redirects en nieuwe vacaturekoppelingen blijven handmatig te valideren.

## Actuele Supabase-controle

Project: `tfsc-crm-ats-staging` (`bpfocgapqsrokmargsjr`). Eerst is alleen-lezen
geïnventariseerd; daarna zijn de goedgekeurde additive migraties toegepast.

### Schema

De bestaande gestructureerde velden blijven aanwezig:

- kandidaten: internationaal reizen, maximaal internationale dagen per jaar,
  maximaal aaneengesloten nachten weg, overnachtingen en weekendreizen;
- vacatures: dezelfde vijf huidige reiskenmerken als requirements;
- werkgebieden: aparte relaties naar `geographic_areas`;
- mobiliteit: woon-werkafstand, verhuizen en eigen vervoer staan apart.

Daarnaast zijn de vier meetbare duur- en weekendvelden toegevoegd die in de
uitvoeringssectie hierboven staan. De legacyvelden zijn niet verwijderd.

Er is geen veld voor vervoersmiddel of `veel vliegen`. Als dat operationeel
filterbaar moet blijven, vereist dat later een afzonderlijk modelbesluit. Het
mag niet stilzwijgend als ja/nee-reisveld worden geïnterpreteerd.

### Geen gekopieerde WordPress-doublures

- `source_term_mappings` bevat geen mapping voor `reisprofiel`.
- `observed_terms` bevat geen van de 25 Reisprofiel-labels.
- De ruwe bronvelden van de kandidaat-reisvoorkeuren bevatten de besproken
  WordPress-labels niet.
- Landen en regio's uit de Reisprofiel-lijst bestaan al canoniek in
  `geographic_areas`: België, Duitsland, Nederland, Polen, Benelux, Europa en
  World Wide.

Er hoeft dus geen lijst met 25 dubbele termen uit Supabase te worden verwijderd.

### Wel aanwezige kandidaatdata die later moet worden beoordeeld

`candidate_travel_preferences` bevat 1.773 op 10 september 2026 geïmporteerde
records:

- alle 1.773 zijn `unreviewed` en hebben `origin_method = imported`;
- alle 1.773 staan op internationaal reizen = ja;
- maximaal internationale dagen: 35 × 120, 1.377 × 200 en 361 × onbekend;
- maximaal aaneengesloten nachten: 17 × 7, 85 × 28, 528 × 56 en 1.143 ×
  onbekend;
- overnachtingen en weekendreizen zijn bij alle 1.773 nog onbekend.

De ruwe EngageBay-keuzes zijn behouden. Open categorieën zijn niet foutief als
maximum ingevuld: alle 358 records met `vanaf 200 dagen` hebben een onbekend
maximum en alle 167 records met `vanaf 9 weken` hebben een onbekend maximum.
Deze records zijn dus geen kopieën van de WordPress-doublures, maar de kwaliteit
en betekenis moeten vóór gebruik voor automatische matching wel worden
gevalideerd. Er wordt nu niets verwijderd of overschreven.

### Vacaturedata

Er staat één ingevuld record in `vacancy_travel_requirements` en één in
`vacancy_mobility_requirements`. Beide horen bij de bestaande Codex-
verificatievacature, niet bij de live WordPress-vacature `23277`. De echte
WordPress-vacatures zijn op deze gestructureerde velden dus nog niet
gesynchroniseerd.

## Actuele Appsmith-controle

Repository `tfsc-appsmith-crm`, branch `crm-layout-redesign`. De eerste
travelwijziging stond in commit `6f182a4`; het definitieve canonieke model en
de latere fixes staan in commits `04e434e`, `ef0e64c` en `47972ac` boven op de
actuele branchhistorie.

Appsmith kan de volgende canonieke reiswaarden voor kandidaten en vacatures
tonen en opslaan:

- werkgebieden;
- internationaal reizen;
- maximum internationale dagen per jaar;
- Maximum travel duration;
- Weekends home;
- Hotel nights/month;
- eigen vervoer en verhuizen;
- bij kandidaten ook maximale enkele woon-werkafstand.

De Candidate- en Vacancy-pagina's gebruiken hiervoor dezelfde veldnamen en
dezelfde Reisduur- en Weekenden-thuiswaarden als Supabase. Alleen
International travel heeft bewust andere keuzewaarden voor kandidaat en
vacature.

De matchlogica vergelijkt directioneel de internationale dagen, Reisduur,
Weekenden thuis en Hotel nights/month. Lege waarden blijven onbekend.

GitHub laat zien dat `document-preview-backfill` op `8dbceef` eindigt en met
mergecommit `204e1ec` is opgenomen in `crm-layout-redesign`. Commit `445b20d`
verwijderde daarna bewust de oude pagina's `Candidates` en `Vacancies_Old`.
Na die opschoning volgen op dezelfde branch nog de documentpreview-,
kandidaatvoorkeur- en opleidingscommits. De laatste commit vóór de
travelwijziging was `e709b22` van 18 oktober 2026. `crm-layout-redesign` is dus
de juiste werkbranch. De branch is via GitHub/Appsmith bijgewerkt tot commit
`47972ac`; een afzonderlijke deployment is niet als onderdeel van dit log
uitgevoerd.

## Resterende besluiten na legacy-opruiming

1. Besluiten of `Veel vliegen` alleen een zoekterm in descriptions wordt of
   een nieuw, afzonderlijk vervoersmiddelveld nodig heeft.
2. De nieuwe classificatie van vacature `23277` vooraf als controlelijst tonen.
3. De redirectbestemming en SEO-status van verwijderde term-URL's controleren.
4. Besluiten of uitgebreidere reisthema's als Beroepengidsartikelen of aparte
   landingspagina's worden uitgewerkt.
5. Legacydatabasevelden pas na bronvalidatie en een afzonderlijke migratie
   eventueel verwijderen.
