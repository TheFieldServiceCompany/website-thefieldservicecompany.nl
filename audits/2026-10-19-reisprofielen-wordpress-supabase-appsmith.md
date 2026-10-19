# Reisprofielen — WordPress, Supabase en Appsmith

Datum: 19 oktober 2026  
Status: teruggevonden besluitvorming en alleen-lezen architectuurcontrole

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
| Overnachten en nachten van huis | Travel-velden voor overnachting en aaneengesloten nachten | Percentages zijn niet zonder afspraak exact om te rekenen |
| Weekendreizen | Travel-veld voor weekendreizen | Driewaardig houden: ja, nee of onbekend |
| Eigen vervoer, woon-werkafstand en verhuizen | Mobility-velden | Niet onder Reisprofiel opslaan |
| Korte trips, langdurig op locatie, veel vliegen en vaak van huis | Nog te besluiten: afgeleid publiek label of aparte canonieke eigenschap | Niet als vrij dubbel label in Appsmith toevoegen |

Bewuste zoekvarianten kunnen in WordPress blijven bestaan als publieke termen
of aliassen. Meerdere WordPress-termen mogen dan naar dezelfde canonieke
betekenis verwijzen. Appsmith toont één operationele keuze of één concreet veld,
niet iedere SEO-variant als aparte waarde.

## Uitvoeringsstatus

1. [x] Alle 25 actuele WordPress-termen, slugs, beschrijvingen, tellingen en
   vacaturekoppelingen alleen-lezen uitlezen.
2. [x] Per term een controlematrix maken met dimensie, betekenis, mogelijke
   alias, Supabase-doelveld en informatie die niet veilig kan worden afgeleid.
3. [ ] Conflicten en ontbrekende keuzes aan de gebruiker voorleggen. Verwijder of
   hernoem geen termen.
4. [ ] Besluiten welke termen publiek/SEO blijven en welke canonieke keuzes Appsmith
   toont.
5. [x] Dezelfde aanpak vastleggen voor de overige WordPress-taxonomieën en
   keuzelijsten.
6. [ ] Pas na goedkeuring een testbaar implementatie- en migratieplan maken voor
   WordPress, Supabase en Appsmith.
7. [ ] Reisprofiel-descriptions schrijven en vacaturekoppelingen pas vastleggen nadat
   het model is goedgekeurd; anders wordt de huidige vermenging versterkt.

Er zijn voor deze reconstructie geen gegevens of instellingen in WordPress,
Supabase of Appsmith gewijzigd.

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
| 470 | Hotelovernachting | 0 | **Houden en hernoemen** | `Hotelovernachtingen`; publiek label voor `overnight_stays_required` |
| 471 | Vaak van huis | 1 | Verwijderen als losse term | Te vaag; afleiden uit internationale dagen en nachten van huis |
| 472 | Langdurig op locatie | 0 | **Houden en hernoemen** | `Lang verblijf`; onderdeel van filterdimensie Verblijfsduur |
| 473 | Kort op locatie | 0 | Samenvoegen/verwijderen | Synoniem; als zoekvariant opnemen bij `Kort verblijf` |
| 474 | Veel vliegen | 1 | Verwijderen als losse term | Geen canoniek vervoersmiddelveld; als zoekterm in teksten behouden |
| 476 | Bijna elke dag thuis | 0 | **Houden** | Publiek profiel voor geen of zeer weinig overnachtingen; grens nog bepalen |
| 480 | Hotelovernachting 25% | 0 | Samenvoegen/verwijderen | Percentage is niet exact en overlapt met nachten thuis |
| 481 | Hotelovernachting 50% | 1 | Samenvoegen/verwijderen | Naar `Hotelovernachtingen`; percentage in description bewaren |
| 482 | Hotelovernachting 75% | 0 | Samenvoegen/verwijderen | Percentage is niet exact en overlapt met nachten thuis |
| 483 | Nachten thuis 100% | 0 | Samenvoegen/verwijderen | Naar `Bijna elke dag thuis` |
| 484 | Nachten thuis 75% | 0 | Verwijderen als losse term | Omgekeerde en onnauwkeurige variant van hotelovernachtingspercentage |
| 485 | Nachten thuis 50% | 1 | Verwijderen als losse term | Omgekeerde duplicaatvariant; verwerken in description Hotelovernachtingen |
| 486 | Nachten thuis 25% | 0 | Verwijderen als losse term | Omgekeerde en onnauwkeurige variant van hotelovernachtingspercentage |
| 487 | Weekenden thuis | 0 | **Houden** | Publiek label voor `weekend_travel_required = false` |
| 488 | Hotelovernachting weekdagen | 0 | Samenvoegen/verwijderen | Combinatie van Hotelovernachtingen en Weekenden thuis |
| 489 | Korte trips | 0 | **Houden en hernoemen** | `Kort verblijf`; onderdeel van filterdimensie Verblijfsduur |
| 490 | Reizen Nederland | 0 | Verwijderen uit Reisprofiel | Werkgebied Nederland |
| 491 | Reizen Internationaal | 1 | Verwijderen uit Reisprofiel | Duplicaat van Internationaal reizen; naar veld en Werkgebied |
| 492 | Reizen Nationaal | 0 | Verwijderen uit Reisprofiel | Duplicaat van Nationaal reizen |
| 493 | Reizen Benelux | 0 | Verwijderen uit Reisprofiel | Werkgebied Benelux |
| 494 | Reizen België | 0 | Verwijderen uit Reisprofiel | Werkgebied België |
| 495 | Reizen Duitsland | 0 | Verwijderen uit Reisprofiel | Werkgebied Duitsland |
| 496 | Reizen Polen | 0 | Verwijderen uit Reisprofiel | Eerst Polen als canoniek Werkgebied beoordelen |

Voorgesteld eindresultaat: vijf duidelijke publieke Reisprofielen behouden,
waarvan drie onder een kortere en preciezere naam:

1. `Hotelovernachtingen`;
2. `Lang verblijf`;
3. `Bijna elke dag thuis`;
4. `Weekenden thuis`;
5. `Kort verblijf`.

### Naam en filterdimensie: Verblijfsduur

`Korte trips` kan ook afstand, frequentie of het aantal reizen suggereren.
`Projectduur` is onjuist, omdat een internationaal project maanden kan duren
terwijl een medewerker telkens kort op locatie is. Het voorstel is daarom:

- **filteronderwerp:** `Verblijfsduur`;
- **korte opties:** `Kort verblijf` en `Lang verblijf`;
- **onderliggende waarde:** maximale aaneengesloten nachten weg.

Supabase bevat nu `max_consecutive_nights_away`: een maximum, geen gemiddelde.
Daarom gebruiken filter en automatische koppeling niet het woord `gemiddeld`.
De descriptions mogen wel uitleggen wat bij werkgevers doorgaans of gemiddeld
voorkomt, zolang duidelijk blijft dat de vacaturewaarde een maximum of
verwachting is.

### Voorgestelde afleidingsregels

Deze grenzen zijn concreet genoeg om te testen, maar worden pas na goedkeuring
gebruikt:

| Publiek Reisprofiel | Vacatureveldregel in Supabase |
| --- | --- |
| Bijna elke dag thuis | `overnight_stays_required = false`; een onbekende waarde levert geen profiel op |
| Hotelovernachtingen | `overnight_stays_required = true` |
| Kort verblijf | `max_consecutive_nights_away` tussen 1 en 7 |
| Lang verblijf | `max_consecutive_nights_away` vanaf 8 |
| Weekenden thuis | `overnight_stays_required = true` én `weekend_travel_required = false` |

Een vacature kan dus bijvoorbeeld zowel `Hotelovernachtingen` als `Kort verblijf`
hebben. Dat zijn geen doublures: het ene zegt dat overnachten voorkomt en het
andere hoe lang een aaneengesloten reis maximaal duurt. Bij een onbekende bron-
waarde wordt geen label gegokt.

`Weekdagen weg` wordt niet als tweede filterterm toegevoegd. Het overlapt sterk
met `Weekenden thuis` en de bestaande velden bewijzen niet dat iemand iedere
werkdag weg is. De woorden kunnen wel in de description van
`Hotelovernachtingen` en `Weekenden thuis` worden gebruikt, met een nuance als
“doorgaans op weekdagen van huis”.

De andere twintig termen worden na goedkeuring gemapt, samengevoegd of
verwijderd. De zes nu gebruikte termen vallen allemaal in die groep. Vacature
`23277` moet daarom vóór verwijdering expliciet worden omgezet naar de juiste
Werkgebieden en gestructureerde reisvelden. Alleen `Hotelovernachtingen` kan uit
de huidige twee percentage-termen veilig als breder label worden afgeleid; de
overige waarden worden niet gegokt.

### Zoekwoorden en bestaande URL's bewaren

Verwijderen uit de taxonomie betekent niet dat de woorden uit de website
verdwijnen:

- percentages, `nachten thuis`, `weekdagen weg` en
  `hotelovernachting weekdagen` komen natuurlijk terug in de description van
  `Hotelovernachtingen` en waar passend
  bij `Weekenden thuis`;
- `korte trips` en `kort op locatie` komen terug bij `Kort verblijf`;
- `langdurig op locatie` en `vaak van huis` komen terug bij `Lang verblijf` en
  de relevante Dagen-internationaal-archieven;
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
- linken naar Werkgebieden, Dagen internationaal en Verblijfsduur;
- onderaan een dynamisch vacatureblok tonen dat op de canonieke Supabase-
  velden en WordPress-mapping is gebaseerd.

De voorkeursrichting is een herkenbare sectie binnen de Beroepengids, zoals
`Werken in field service` of `Reizen en verblijf`. Eerst wordt gecontroleerd of
dit binnen de bestaande Beroepengidsstructuur logisch past; anders wordt een
gerichte landingspagina gebruikt. Er worden geen dunne archiefpagina's gemaakt
alleen om een extra zoekwoord te bezitten.

## Actuele Supabase-controle

Project: `tfsc-crm-ats-staging` (`bpfocgapqsrokmargsjr`). Alle controles waren
alleen-lezen.

### Schema

De benodigde gestructureerde velden bestaan al:

- kandidaten: internationaal reizen, maximaal internationale dagen per jaar,
  maximaal aaneengesloten nachten weg, overnachtingen en weekendreizen;
- vacatures: dezelfde vijf reiskenmerken als requirements;
- werkgebieden: aparte relaties naar `geographic_areas`;
- mobiliteit: woon-werkafstand, verhuizen en eigen vervoer staan apart.

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

Repository `tfsc-appsmith-crm`, branch `crm-layout-redesign`, gecontroleerde
commit `e709b22`.

Appsmith kan de volgende reiswaarden voor kandidaten en vacatures al tonen en
opslaan:

- werkgebieden;
- internationaal reizen;
- maximum internationale dagen per jaar;
- maximum aaneengesloten nachten weg;
- overnachtingen;
- weekendreizen;
- eigen vervoer en verhuizen;
- bij kandidaten ook maximale enkele woon-werkafstand.

De matchlogica vergelijkt werkgebieden, internationaal reizen,
overnachtingen, weekendreizen, internationale dagen, eigen vervoer en
verhuizen afzonderlijk. Het voorgestelde opschonen van WordPress sluit daarom
aan op de bestaande Appsmith/Supabase-opbouw.

## Besluiten vóór uitvoering

1. Goedkeuren of de vijf voorgestelde Reisprofielen en de namen
   `Hotelovernachtingen`, `Kort verblijf`, `Lang verblijf`,
   `Bijna elke dag thuis` en `Weekenden thuis` de definitieve publieke set
   worden.
2. De voorgestelde afleidingsregels voor `Kort verblijf`, `Lang verblijf`,
   `Bijna elke dag thuis`, `Hotelovernachtingen` en `Weekenden thuis` goedkeuren
   of aanpassen.
3. Besluiten of `Veel vliegen` alleen een zoekterm in descriptions wordt of
   een nieuw, afzonderlijk vervoersmiddelveld nodig heeft.
4. De omzetting van vacature `23277` vooraf als controlelijst tonen.
5. Per te verwijderen term de redirectbestemming en SEO-status controleren.
6. Besluiten of uitgebreidere reisthema's als Beroepengidsartikelen of aparte
   landingspagina's worden uitgewerkt.
7. Pas daarna WordPress wijzigen en vervolgens de goedgekeurde mapping naar
   Supabase/Appsmith implementeren en testen.
