# Centrale TODO

Laatst bijgewerkt: 20 oktober 2026

Dit is de centrale werklijst. Detailbesluiten en testresultaten blijven in de
bestaande documenten onder `audits/`, `content/` en `functionality/` staan.

## Actuele prioriteit — formulieren naar Supabase/Appsmith

Op 19 oktober 2026 heeft de gebruiker de werkprioriteit verlegd van travel naar
de koppeling van de WordPress-formulieren met Supabase en Appsmith. Travel
blijft open staan, maar wordt niet eerst uitgevoerd.

### Besluit 20 oktober — eerst alle formulieren afronden

- [ ] De zes actuele formulieren één voor één afronden met vaste Engelse
  technische `field_id`-waarden, ongewijzigde duidelijke Nederlandse labels,
  bijgewerkte Elementor-e-mailshortcodes en een expliciet gecontroleerde Form
  Bridge-mapping. Geen veld-ID aanpassen zonder eerst de afhankelijkheden van
  precies dat formulier te controleren.
- [ ] Per formulier een synthetische end-to-endtest uitvoeren en WordPress-
  outbox, Supabase-verwerking, Backblaze indien van toepassing en Appsmith
  controleren. Pas daarna het volgende formulier oppakken.
- [ ] Uitvoervolgorde na het vraagformulier: Registratie, Mailing Registratie,
  Werkgeverscontact, Adviesaanvraag en als laatste Solliciteren.
- [x] `Vacature - Stel je vraag` heeft canonieke Engelse veld-ID's en een
  actieve mapping in Form Bridge `0.3.8`. De mapping maakt een kandidaat maar
  geen bedrijf; alleen `vacancy_number` wordt als vacaturecontext vertrouwd.
- [ ] `Vacature - Stel je vraag` nog openbaar synthetisch testen zonder én met
  vacaturecontext.
- [x] `Registratie` heeft canonieke Engelse kandidaatveld-ID's en een actieve
  mapping als `Open registratie/inschrijving`, met kandidaat aan en bedrijf uit.
  De vaste legacywaarden `Registratie`, vacaturenummer `1` en `The Field
  Service Company` staan op `Niet versturen`.
- [x] `Registratie` openbaar synthetisch getest zonder cv. Een eerste proef met
  een al gebruikt synthetisch mobiel nummer ging terecht naar handmatige
  controle met `person_email_conflict` en maakte geen canonieke persoon aan.
  De tweede proef met volledig unieke gegevens werd als `succeeded` verwerkt:
  één persoon, kandidaatprofiel, primair adres, privacybewijs, voorkeur `Vast
  dienstverband`, activiteit en open intake-item; geen bedrijf, vacature of
  sollicitatie. Appsmith toont **Registratie / Verwerkt / Nieuw profiel** en
  opent het juiste profiel. De aparte PDF- en Word-cv-routes waren al bewezen.
- [ ] De huidige vacatureknop-/linkcode pas vereenvoudigen nadat alle
  formulieren zijn afgerond. Tot dat moment blijven `postTitle`, `cn`,
  `vacaturenummer` en `postUrl` bestaan om nog niet gestandaardiseerde
  formulieren en e-mails niet te breken.
- [ ] Daarna de frontendcode die titel, bedrijf en URL uit de pagina schraapt
  verwijderen. Op afgescheiden formulierpagina's alleen het canonieke
  vacaturenummer transporteren, bijvoorbeeld `?vacaturenummer=335`; Supabase
  leidt titel, bedrijf, dienstverband en canonieke URL vervolgens zelf af.
  Elementor-e-mails die nu nog een meegestuurde titel gebruiken moeten vóór
  die opschoning op vacaturenummer of een generiek onderwerp worden gezet.

De actuele connectorcode, veldcontracten en live testresultaten staan in de
private repository `tfsc-crm-ats`, branch `codex/wordpress-form-bridge`.

1. [x] De actuele configuratie van Registratie, Solliciteren,
   Werkgeverscontact, Mailing, Advies en Vraag op LIVE alleen-lezen uitlezen:
   formulier-ID, veld-ID, type, verplichtheid, toestemming, uploads, limieten
   en alle ingestelde acties. Afgerond op 19 oktober; zie de actuele nulmeting.
2. [ ] Per formulier vastleggen welk bestaand Supabase-record en welke relaties
   moeten ontstaan. Geen nieuwe tabellen ontwerpen voordat deze mapping is
   goedgekeurd.
3. [ ] Ieder Elementor-veld naast de bestaande Supabase-kolom en de relevante
   Appsmith-weergave of -invoer zetten; ontbrekende doelen en twijfelgevallen
   expliciet markeren.
4. [ ] Deduplicatie, bron-ID, toestemming, eigenaarschap en bewaartermijn per
   formulier laten goedkeuren.
5. [ ] Daarna pas het technische ontwerp voor lokale Elementor-opslag,
   WordPress-outbox, beveiligde server-side aflevering, idempotentie, retries
   en handmatige review definitief maken.
6. [ ] De koppeling eerst synthetisch en in schaduwmodus naast AFI/EngageBay
   testen. EngageBay pas per formulier uitschakelen nadat verwerking in
   Supabase en beheer in Appsmith aantoonbaar correct zijn.

Eerste actuele Supabase-controle: het bestaande personen-, kandidaat-,
sollicitatie-, vacature-, werkgevers- en documentenmodel is aanwezig en de
gecontroleerde tabellen hebben RLS. Er is nog geen intake-, submission- of
outboxtabel en het project heeft nog geen Edge Function. Zie
`audits/2026-10-19-formulier-supabase-appsmith-nulmeting.md`.

## Uitvoervolgorde — één punt tegelijk

Deze lijst is de gezamenlijke werkvolgorde. We pakken steeds één genummerd
punt op, leggen het besluit, de wijziging en de verificatie vast en vinken het
pas daarna af. De uitgebreidere acceptatiecriteria en deelstappen staan in de
secties verderop in dit document. De volgorde kan alleen bewust worden
gewijzigd; open afhankelijkheden worden niet stilzwijgend overgeslagen.

### A. WordPress-taxonomieën, Supabase en Appsmith

1. [x] **Doelarchitectuur bevestigd.** Appsmith is het CRM en de
   beheerinterface, Supabase is de canonieke database en EngageBay is alleen
   legacy- en migratiebron.
2. [x] **Reisprofielen alleen-lezen inventariseren en classificeren.** Alle 25
   actuele termen, slugs, descriptions, tellingen en vacaturekoppelingen
   vastleggen en per term bepalen of het om gebied, reisomvang,
   overnachtingen, thuisnachten, weekendreizen of reispatroon gaat.
3. [x] **Reisprofiel-mapping en aliasbeleid goedkeuren.** Bewuste
   zoektermvarianten in WordPress behouden waar ze publiek nut hebben, maar
   koppelen aan één canonieke betekenis of concreet Supabase-veld; geen
   dubbele operationele keuzes in Appsmith maken. Het definitieve model gebruikt
   `Reisduur` (`Elke nacht thuis`, `2–7`, `8–14`, `15–30`, `meer dan 30
   dagen`), `Weekenden thuis` (0%, 25%, 50%, 75%, 100%) en apart `Hotel
   nachten/maand` (0, 1–7, 8–14, 15–31). Reisfrequentie vervalt. De 25 oude
   Reisprofiel-termen zijn LIVE verwijderd; vacature `23277` bestaat nog en
   moet inhoudelijk opnieuw worden geclassificeerd. Search & Filter-velden 14,
   15 en 16 bestaan, maar zijn niet in de zichtbare frontend-GUI geplaatst.
4. [ ] **Volledige WordPress–Supabase–Appsmith-keuzematrix maken.** Naast
   Reisprofielen ook Werkgebieden, Dagen internationaal en de resterende
   taxonomieën en keuzelijsten vergelijken op naam, betekenis, ID en status.
   Opleidingsniveau heeft al 4 en Studierichting 23 actieve mappings; deze
   worden in de end-to-endcontrole opnieuw gevalideerd. Leg voor `Dagen
   internationaal` ook de exacte bandgrenzen naar het numerieke Supabaseveld
   vast; ID 358 heet LIVE `Meer dan 200 dagen`.
5. [ ] **Travelgegevens van de vacatures classificeren en koppelen.** De
   WordPress-taxonomieën en de Supabase/Appsmith-velden bestaan al. Dit punt
   betekent dus niet dat er nog nieuwe travelvelden moeten worden bedacht.
   Controleer per bestaande vacature de inhoud en toon eerst een voorstel voor
   `Reisduur`, `Weekenden thuis`, `Hotel nachten/maand` en `Dagen
   internationaal`. Leg pas na goedkeuring de WordPress-relaties en de
   corresponderende numerieke waarden in Supabase vast. Begin met vacature
   `23277`. De frontendplaatsing van Search & Filter-velden 14, 15 en 16 doet
   de gebruiker handmatig.
   De vijf Beroepengids-spiegels voor Bedrijfssectoren, Functie-inhoud,
   Functienamen, Kennisgebieden en Werkgebieden zijn op 19 oktober LIVE
   gelijkgetrokken met de vacaturetermen: 127 namen/slugs en 127 bestaande
   relaties zijn gecontroleerd. De 32 Functienamen hebben sinds 19 oktober
   afzonderlijke descriptions, uitnodigende intro's en archieftitels als
   artikelarchief; alle 96 waarden zijn LIVE geverifieerd. Ook de 33
   Bedrijfssectoren hebben unieke descriptions, intro's en archieftitels; alle
   99 waarden zijn LIVE geverifieerd. Ook de 29 Functie-inhoud-termen hebben
   unieke descriptions, intro's en archieftitels; alle 87 waarden zijn LIVE
   geverifieerd. Ook de 25 Kennisgebieden hebben unieke descriptions, intro's
   en archieftitels; alle 75 waarden zijn LIVE geverifieerd. Ook de 8
   Werkgebieden hebben unieke descriptions, intro's en archieftitels; alle 24
   waarden zijn LIVE geverifieerd. Daarmee zijn alle 127 spiegeltermen
   inhoudelijk als artikelarchief ingericht.
   De 23 Beroepengids-studierichtingen hebben sinds 19 oktober descriptions,
   uitnodigende intro's en archieftitels als artikelarchief. Alle 69 waarden
   zijn LIVE geverifieerd.
6. [ ] **Synchronisatieplan ontwerpen en testen.** Het travel-deel is in
   Supabase en de Appsmith-bron uitgevoerd en met rollbacktests gecontroleerd.
   De WordPress-taxonomiestructuur en Search & Filter-objecten zijn additive
   LIVE aangemaakt en via REST geverifieerd. De actuele Appsmith-werkbranch is
   `crm-layout-redesign`; commit `47972ac` bevat ook het definitieve label
   `More than 30 days`. De structuur en invoermogelijkheden zijn daarmee
   grotendeels gelijkgetrokken; de bestaande vacaturedata is nog niet
   gesynchroniseerd. Frontendplaatsing, vacaturekoppeling en een volledige
   live end-to-endtest blijven open. Daarna volgen de overige keuzes.
   Als afzonderlijke latere migratiestap moeten bovendien alle bestaande LIVE
   WordPress-vacatures in Supabase worden opgenomen, op aantallen en relaties
   worden gereconcilieerd en daarna in Appsmith zichtbaar en beheerbaar zijn.
   Appsmith blijft daarbij de beheerinterface op Supabase en krijgt geen
   tweede kopie van de vacaturedata. Dit is vastgelegd, maar heeft nu geen
   voorrang op de formulierkoppeling.

### B. Formulieren, intake en inhaalslag

7. [ ] **Alleen-lezen nulmeting voor formulieren afronden.** Live
   WordPress-formulieren, Supabase-schema/constraints/RLS/functions,
   EngageBay-bron, Appsmith-weergave en bestaande importlogica controleren.
8. [ ] **Exacte grens van de eerdere kandidaatimport vastleggen.** Het laatste
   betrouwbare bron-ID of eindmoment bewijzen; niet op een datum gokken.
9. [x] **Actuele formulieren en uploads inventariseren.** Registratie,
   sollicitatie, werkgeverscontact, mailing, advies en vraag inclusief
   Elementor-veld-ID's, bestandstypen, limieten en bestaande acties zijn op
   19 oktober alleen-lezen vastgelegd. Alle zes actieve AFI-koppelingen naar
   EngageBay staan in het LIVE-overzicht als falend in de afgelopen zeven
   dagen. Zie `audits/2026-10-19-formulier-supabase-appsmith-nulmeting.md`.
10. [ ] **Eigenaarschap en privacybesluiten nemen.** Per gegeven het leidende
   systeem, toestemming, bewaartermijn, verwijderroute en logbeleid bepalen.
11. [ ] **Deduplicatie- en koppelregels goedkeuren.** E-mail, telefoon,
   LinkedIn en externe ID's normaliseren; ambigue gevallen altijd naar
   handmatige controle sturen.
12. [ ] **Volledige veldmapping goedkeuren.** Ieder Elementor-veld koppelen aan
   het bestaande Supabase-model dat Appsmith beheert.
13. [ ] **Technisch ontwerp definitief goedkeuren.** Lokale Elementor-opslag,
   WordPress-outbox, beveiligde Edge Function, idempotentie, retries,
   `dead_letter`/`review` en herstelprocedure vastleggen. De bestaande
   pluginopties en het voorlopige voorkeursontwerp staan in
   `audits/2026-10-19-wordpress-elementor-supabase-koppelopties.md`. AFI heeft
   al een inactieve algemene Webhook, actieve jobqueue, logging en resend en is
   daarom de voorlopige voorkeursconnector; een eigen outboxplugin is alleen
   nodig als de AFI-proef niet aan de acceptatiecriteria voldoet. Elementor
   Atomic Forms vallen buiten scope. De ontvangstkant wordt
   formulierbouwer-onafhankelijk ontworpen, zodat de latere overstap naar
   Bricks dezelfde Edge Function, mapping, idempotentie en Supabase-tabellen
   behoudt.
14. [ ] **WordPress-connector en lokale outbox bouwen.** Werken na succesvolle
   Elementor-opslag, met unieke submission-UUID en zonder geheimen of
   persoonsgegevens in browsercode of gewone logs.
15. [ ] **Supabase-intake en idempotente verwerking bouwen.** Bestaande
    tabellen en relaties hergebruiken; eventuele schemawijzigingen via een
    beoordeelde migratie, minimale rechten en passende RLS uitvoeren.
16. [ ] **Cv- en documentroute bouwen.** Besluit over private Supabase Storage,
    MIME-/groottecontrole, documentversies, relaties en herstelbaarheid eerst
    goedkeuren.
17. [ ] **Appsmith-weergave en beheer aansluiten.** De nieuwe Supabase-records
    en foutstatussen in het bestaande CRM tonen en beheren zonder een tweede
    synchronisatielaag of dubbel datamodel.
18. [ ] **Synthetische end-to-endtests uitvoeren.** Nieuwe, bestaande en
    ambigue records, duplicaten, verkeerde/te grote bestanden en uitval/herstel
    van Supabase en fouten in Appsmith testen.
19. [ ] **Schaduwmodus naast AFI/EngageBay draaien.** Uitkomsten vergelijken
    zonder de bestaande productieroute of lokale Elementor-inzendingen uit te
    schakelen.
20. [ ] **Backfill-droogloop maken.** Elementor en EngageBay vanaf de bewezen
    grens onderling en tegen Supabase ontdubbelen; alleen aantallen voor nieuw,
    bestaand, betrouwbaar, ambigu en fout vastleggen.
21. [ ] **Goedgekeurde backfill uitvoeren en reconciliëren.** Dezelfde
    idempotente route gebruiken, relaties en aantallen controleren en geen
    persoonsgegevens of exports in GitHub opnemen.
22. [ ] **Gecontroleerd omschakelen.** AFI per formulier uitschakelen, niet
    alles tegelijk; monitoring, foutoverzicht en herstelhandleiding opleveren.

### C. SEO, content en websitekwaliteit

23. [ ] **Canonicalstrategie voor losse vacaturetaxonomiearchieven kiezen.**
    Beslissen tussen de huidige combo-canonical en self-canonicals.
24. [ ] **Snippet 193 veilig aanpassen en testen.** Eerst een inactieve
    herstelkopie maken; alleen uitvoeren na punt 23.
25. [ ] **Combo-sitemap monitoren.** De 800 URL's met exact twee taxonomieën
    controleren op indexeerbaarheid, inhoud en duplicaten.
26. [ ] **Search & Filter- en overige `noindex`-combo's controleren.** Robots,
    canonical, sitemap en interne links nalopen.
27. [ ] **Travelarchiefteksten controleren.** Loop uitsluitend de bestaande
    descriptions van Reisduur, Weekenden thuis, Hotel nachten/maand en Dagen
    internationaal inhoudelijk na. Er worden hierbij geen nieuwe velden of
    termen gemaakt. De verwijderde oude Reisprofiel-URL's hoeven niet te worden
    omgeleid of als 410 te worden behandeld: Google had er geen toegang toe en
    ze waren niet geïndexeerd.
28. [x] **Robotsbeleid voor lege Beroepengids-termarchieven bevestigd.** De
    robotsinstellingen en terminstellingen zijn door de gebruiker ingericht.
    Lege taxonomiearchieven mogen `noindex` blijven zolang er geen werkelijk
    Beroepengidsartikel aan is gekoppeld. De globale Rank Math-instelling
    `noindex_empty_taxonomies` blijft daarom gewenst; hiervoor hoeft geen
    hardcoded uitzondering of aanvullende wijziging te worden gemaakt.
29. [ ] **Centrale overzichtspagina voor publieke archieven ontwerpen.** Alleen
    goedgekeurde, indexeerbare archieven opnemen.
    Beschrijvende reisthema's zo nodig als sterke Beroepengids- of
    landingspagina met een dynamisch vacatureblok uitwerken, niet als extra
    filterterm.
30. [ ] **LiteSpeed JavaScript veilig instellen en Clarity testen.** Combine en
    `Combine External and Inline` uitschakelen, deferred behouden en Clarity
    anoniem verifiëren.
31. [ ] **`font-display: swap` instellen en visueel testen.** Elementor-layout
    en fonts op representatieve pagina's controleren.
32. [ ] **Resterende taxonomieën en content gefaseerd afwerken.** Werkgebied,
    vacaturekoppelingen, blogmigraties, Arbeidsvoorwaarden en formulierteksten
    één gecontroleerd onderdeel tegelijk behandelen.
33. [ ] **WordPress-omvang en prestaties meten en optimaliseren.** Ongebruikte
    media en grote tabellen eerst inventariseren; alleen na back-up en expliciet
    akkoord opschonen.

## Geparkeerd: travelpunten uit de vorige werksessie

Deze punten blijven open, maar zijn op 19 oktober achter de formulierkoppeling
geplaatst. Het robotsbeleid voor lege Beroepengids-archieven staat al vast.

1. [ ] **Alleen-lezen travelnulmeting per vacature maken.** Controleer alle
   huidige WordPress-koppelingen en vacatureteksten voor Reisduur, Weekenden
   thuis, Hotel nachten/maand en Dagen internationaal. Begin met `23277` en
   wijzig nog niets.
2. [ ] **Exacte omzettingstabel vastleggen.** Leg de betekenis en grenzen vast
   tussen de publieke WordPress-banden en de numerieke Supabasevelden
   `maximum_travel_duration`, `weekends_home_percentage`,
   `hotel_nights_per_month` en `max_international_days_per_year`.
3. [ ] **Voorstel per vacature laten goedkeuren.** Toon de vier voorgestelde
   travelwaarden per vacature, inclusief twijfelgevallen en lege waarden.
4. [ ] **Goedgekeurde waarden koppelen en synchroniseren.** Werk WordPress en
   Supabase gecontroleerd bij en controleer daarna dezelfde waarden in
   Appsmith. Geen automatische invulling bij twijfel.
5. [ ] **Travelketen end-to-end testen.** Test opslaan, opnieuw laden,
   directionele matching en de zichtbare vacature-uitvoer met een synthetische
   vacature. De gebruiker plaatst de bestaande Search & Filter-velden 14, 15
   en 16 zelf in de frontend-GUI; daarna wordt de publieke filtering getest.
6. [ ] **Travelarchiefteksten nalopen.** Controleer alleen de bestaande
   descriptions. De verwijderde oude Reisprofiel-URL's behoeven geen redirect
   of 410, omdat Google er geen toegang toe had en ze niet waren geïndexeerd.
- [ ] Beslissen hoe de canonical van losse vacaturetaxonomiearchieven moet
  werken. Functie-inhoud, bedrijfssector, dienstverband, dagen internationaal
  en vergelijkbare archieven verwijzen nu naar een combo met de vaste context
  `field-service-engineer`. Beoordelen of dit inhoudelijk correct is of dat
  self-canonicals beter zijn.
- [ ] Snippet 193 pas na dat besluit aanpassen. Eerst in Code Snippets een
  inactieve herstelkopie maken en daarna de wijziging op representatieve
  archieven testen.
- [ ] De nieuwe eerste sitemapfase controleren en monitoren. De combo-sitemap
  bevat nu 800 unieke URL's met exact twee taxonomieën; combo's met drie of
  meer taxonomieën zijn bereikbaar maar `noindex`.
- [ ] Search & Filter-URL's en andere `noindex`-combo's controleren op robots,
  canonical, sitemapvermelding en interne links.
- [ ] Later één centrale overzichtspagina voor alle goedgekeurde publieke
  archieven ontwerpen.

## Eerstvolgende technische controles

- [ ] LiteSpeed JavaScript veiliger instellen: minify aanhouden, combine en
  `Combine External and Inline` uitschakelen, deferred behouden en Microsoft
  Clarity uitsluiten van optimalisatie. Daarna alles opschonen en anoniem
  controleren of Clarity daadwerkelijk wordt geladen.
- [ ] `font-display: swap` inschakelen en visueel controleren of de Elementor-
  vormgeving gelijk blijft.
- [ ] Uploadlimieten en toegestane bestandstypen van registratie en
  sollicitatie documenteren en testen.

## Project: WordPress, Supabase en Appsmith verbinden

### Uitgangspunt

Een inzending wordt altijd eerst duurzaam in Elementor opgeslagen. Een storing
bij Supabase, Appsmith of e-mail mag nooit formulierwaarden of een cv laten
verdwijnen. Persoonsgegevens, cv's, sleutels en echte formulierwaarden komen
niet in GitHub of gewone foutlogs.

### Fase 1 — besluiten en veldmapping

- [x] De doelarchitectuur staat vast: Appsmith is het CRM, Supabase de
  canonieke database en EngageBay alleen de huidige legacy-bestemming van
  Advanced Form Integration en een latere migratiebron.
- [ ] Per formulier vastleggen wat er moet ontstaan:
  - Registratie: `person` en `candidate_profile`, met toestemming en eventueel
    document.
  - Sollicitatie: kandidaat koppelen of aanmaken en precies één `application`
    aan de juiste vacature koppelen.
  - Werkgeverscontact: `person`, `company_contact` en `company` koppelen of
    gecontroleerd aanmaken.
  - Mailing, advies en vraag: bepalen welke gegevens in Supabase thuishoren en
    welke alleen als communicatie-inzending bewaard blijven.
- [ ] Per gegeven bepalen wat leidend is: de oorspronkelijke WordPress-
  inzending of het canonieke record in Supabase. Dit geldt minimaal voor
  kandidaten, werkgevers, contacten, sollicitaties, vacatures, documenten,
  toestemming en communicatievoorkeuren.
- [ ] Een volledige veldmapping maken van ieder Elementor-veld naar de
  bestaande Supabase-kolom en de bijbehorende Appsmith-invoer of -weergave.
- [ ] Regels voor herkennen en samenvoegen vastleggen: genormaliseerd
  e-mailadres, telefoonnummer, LinkedIn-profiel en bestaande externe ID's.
  Twijfelgevallen gaan naar handmatige controle en overschrijven nooit
  stilzwijgend bestaande gegevens.
- [ ] Bewaartermijnen, verwijderverzoeken en toestemming uitwerken voor
  WordPress, Supabase/Appsmith en cv-bestanden.

### Fase 2 — bestaande systemen alleen-lezen inventariseren

- [ ] De relevante Supabase-tabellen, constraints, RLS-regels, functies en
  bestaande importlogica volledig documenteren voordat er iets wordt
  gewijzigd.
- [ ] Bestaande EngageBay-ID's als historische externe ID's en de tabel
  `external_identifiers` gebruiken om dubbele personen, kandidaten, contacten
  en bedrijven te voorkomen; EngageBay is daarmee geen doelarchitectuur.
- [ ] Vaststellen hoe Appsmith de nieuwe en bestaande records toont en welke
  queries of formulieren later moeten worden aangepast.
- [ ] De actuele WordPress-formulierconfiguratie exporteren als herstelpunt.

De eerste alleen-lezen controle bevestigt dat het Supabase-model al de juiste
basisrelaties bevat: `persons` → `candidate_profiles` → `applications`,
`companies` ↔ `company_contacts`, plus `documents`, `document_links` en
`external_identifiers`. RLS staat aan op de gecontroleerde tabellen. We bouwen
daarom voort op het bestaande model en maken geen tweede kandidaat- of
werkgevermodel.

### Fase 3 — veilige ontvangst en foutopvang bouwen

- [ ] Een WordPress-connector maken die na lokale Elementor-opslag een
  minimaal outboxrecord met unieke submission-UUID aanmaakt.
- [ ] Geen formuliergegevens of bestanden vanuit browser-JavaScript
  rechtstreeks naar Supabase sturen.
- [ ] De WordPress-server laten afleveren bij een beveiligde Supabase Edge
  Function. Authenticatie, geheimen en een handtekening blijven uitsluitend
  server-side.
- [ ] In Supabase een duurzame intake/outbox en auditstatus inrichten met
  idempotente verwerking op submission-UUID.
- [ ] Statussen gebruiken: `received`, `pending`, `sending`, `succeeded`,
  `retry`, `dead_letter` en `review`.
- [ ] Tijdelijke fouten automatisch opnieuw proberen met oplopende wachttijd;
  permanente en ambigue gevallen zichtbaar maken voor handmatige controle.
- [ ] Nieuwe tabellen alleen expliciet aan de Data API blootstellen als dat
  nodig is, met minimale rechten en RLS. Secret/service-role-sleutels nooit in
  WordPress-frontendcode of Appsmith-clientcode plaatsen.

### Fase 4 — cv's en andere uploads

- [ ] Beslissen of Supabase Storage de definitieve documentopslag wordt. Er is
  nu nog geen Storage-bucket ingericht.
- [ ] Bij gebruik van Storage een private bucket maken, met MIME-/groottecheck,
  niet-voorspelbare paden, RLS en tijdelijk ondertekende downloadlinks.
- [ ] `documents`, `document_versions` en `document_links` gebruiken in plaats
  van een tweede los bestandsmodel.
- [ ] Een WordPress-upload pas opruimen nadat bestand, metadata en relaties
  aantoonbaar correct in Supabase staan én de bewaartermijn dit toestaat.

### Fase 5 — Appsmith aansluiten op de Supabase-intake

- [ ] Appsmith de duurzaam verwerkte Supabase-records en hun intake- of
  foutstatus laten tonen. Er komt geen tweede CRM-kopie naast Supabase.
- [ ] Externe bron-ID's centraal bewaren in `external_identifiers`.
- [ ] Kandidaat, werkgever, contactpersoon en sollicitatie met hun onderlinge
  relaties synchroniseren.
- [ ] Supabase- of Appsmith-fouten zichtbaar maken en een handmatige
  herstelactie bieden zonder de oorspronkelijke inzending opnieuw te laten
  invullen.

### Fase 6 — testen, kandidaten inhalen en gecontroleerd omschakelen

- [ ] Testen met synthetische gegevens: nieuwe, bestaande en ambigue kandidaat;
  nieuwe en bestaande werkgever/contactpersoon; dubbele verzending; verkeerd
  bestand; te groot bestand; uitval van Supabase; fout in Appsmith; herstel na
  uitval.
- [ ] Controleren dat elke inzending lokaal bewaard blijft en dat herhalen
  nooit dubbele personen, sollicitaties, contacten of documenten maakt.
- [ ] Eerst in schaduwmodus naast de bestaande AFI/EngageBay-legacyroute
  draaien en uitkomsten vergelijken.
- [ ] Het exacte eindmoment of laatste submission-ID van de eerdere
  kandidaatimport naar Supabase terugvinden en als controleerbare grens
  vastleggen; niet gokken op een datum.
- [ ] Alleen de kandidaatregistraties en sollicitaties ná die grens uit
  Elementor inventariseren, inclusief de aanwezigheid van een cv, maar zonder
  persoonsgegevens of bestanden in GitHub te zetten.
- [ ] Ook de kandidaten en sollicitatiegegevens die ná die grens in EngageBay
  zijn binnengekomen inventariseren. Elementor en EngageBay zijn beide
  migratiebronnen; de gecombineerde set wordt vóór import ontdubbeld tegen
  Supabase en onderling.
- [ ] Eerst een droge inhaalcontrole maken met aantallen: nieuw, al aanwezig,
  betrouwbaar te koppelen, ambigu en fout. Matchen gebeurt via genormaliseerd
  e-mailadres, telefoonnummer, LinkedIn-profiel en bestaande externe ID's.
- [ ] De goedgekeurde ontbrekende kandidaten, sollicitaties en cv-relaties via
  exact dezelfde idempotente verwerkingsroute als nieuwe inzendingen naar
  Supabase sturen en daarna in Appsmith controleren. Iedere broninzending krijgt
  een vaste submission-UUID, zodat opnieuw uitvoeren geen duplicaten maakt.
- [ ] Na de inhaalslag aantallen en relaties tussen Elementor, Supabase en de
  Appsmith-weergave afstemmen en alle ambigue gevallen handmatig beoordelen.
- [ ] Pas na een geslaagde end-to-endcontrole de oude AFI-route per formulier
  uitschakelen. Niet alle formulieren tegelijk omschakelen.
- [ ] Monitoring, foutoverzicht en een korte herstelhandleiding opleveren.

## Overige bestaande backlog

- [ ] Appsmith-branches gecontroleerd opschonen. `crm-layout-redesign` is de
  actuele werkbranch en `main` blijft behouden. Negen branches zijn volledig
  in de actuele lijn opgenomen; vier afgesplitste branches hebben elk één
  unieke commit en moeten eerst inhoudelijk worden beoordeeld. Geen remote
  branch verwijderen zonder expliciet akkoord. Zie
  `audits/2026-10-19-appsmith-branch-inventory.md`.
- [ ] Na Reisprofielen de resterende WordPress-, Supabase- en Appsmith-
  taxonomieën en keuzelijsten volgens dezelfde canonieke mapping afwerken.
- [ ] Blogartikelen één voor één actualiseren, naar Beroepengids of Kennisbank
  verplaatsen en met een geteste 301-redirect migreren.
- [ ] Taxonomie Arbeidsvoorwaarden later als gedeeld model ontwerpen.
- [ ] Nederlandstalige, specifieke formuliermeldingen instellen.
- [ ] Ongebruikte media en grote databasetabellen eerst inventariseren en pas
  na back-up en expliciet akkoord opschonen.
- [ ] WordPress-omvang en prestaties meten en gefaseerd optimaliseren.

## Nu bewust niet doen

- Geen bestaande kandidaat-, werkgever- of sollicitatiedata wijzigen.
- Geen Supabase-schema of Storage-bucket aanmaken voordat de veldmapping en
  eigenaarschapbesluiten zijn goedgekeurd.
- Advanced Form Integration nog niet uitschakelen.
- Geen Elementor-inzendingen of cv's verwijderen.
- Geen persoonsgegevens, exports, databaseback-ups of geheimen naar GitHub
  pushen.
