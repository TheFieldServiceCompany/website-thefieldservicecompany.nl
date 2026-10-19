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

## Eerstvolgende werkvolgorde

1. Lees alleen-lezen alle 25 actuele WordPress-termen, slugs, beschrijvingen,
   tellingen en vacaturekoppelingen uit.
2. Maak per term een controlematrix met dimensie, betekenis, mogelijke alias,
   Supabase-doelveld en eventuele informatie die niet veilig kan worden
   afgeleid.
3. Leg conflicten en ontbrekende keuzes aan de gebruiker voor. Verwijder of
   hernoem geen termen.
4. Besluit welke termen publiek/SEO blijven en welke canonieke keuzes Appsmith
   toont.
5. Leg dezelfde aanpak vast voor de overige WordPress-taxonomieën en
   keuzelijsten.
6. Maak pas na goedkeuring een testbaar implementatie- en migratieplan voor
   WordPress, Supabase en Appsmith.
7. Schrijf Reisprofiel-descriptions en leg vacaturekoppelingen pas vast nadat
   het model is goedgekeurd; anders wordt de huidige vermenging versterkt.

Er zijn voor deze reconstructie geen gegevens of instellingen in WordPress,
Supabase of Appsmith gewijzigd.
