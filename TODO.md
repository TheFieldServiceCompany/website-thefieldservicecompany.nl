# Centrale TODO

Laatst bijgewerkt: 21 oktober 2026

Dit is de centrale werklijst. Detailbesluiten en testresultaten blijven in de
bestaande documenten onder `audits/`, `content/` en `functionality/` staan.

## Actuele prioriteit — formulieren naar Supabase/Appsmith

Op 19 oktober 2026 heeft de gebruiker de werkprioriteit verlegd van travel naar
de koppeling van de WordPress-formulieren met Supabase en Appsmith. Travel
blijft open staan, maar wordt niet eerst uitgevoerd.

### Besluiten 20–21 oktober — eerst alle formulieren afronden

- [x] Productiepoort TFSC Form Bridge afgerond. Versie `0.4.1` staat LIVE,
  waarschuwt zichtbaar wanneer een gekoppelde `field_id` verdwijnt en koppelt
  een nieuwe ID nooit automatisch. Time-outs, rate limiting, definitieve
  validatiefouten, ontbrekende mappings, mislukte documentuploads en begrensde
  retry/backoff zijn synthetisch getest. Een live, teruggedraaide Supabase-test
  met een niet-bestaand vacaturenummer gaf `vacancy_not_found` en liet nul
  testregels achter. De vijf koppelingen bleven actief en `connection_ok` is na
  installatie opnieuw bevestigd. Code en volledig log: private repo
  `tfsc-crm-ats`, branch `codex/wordpress-form-bridge`, commit `233ac99`.
- [x] De uploadregressie van Elementor Pro 4.3.2 gericht opgevangen. TFSC
  Custom Functions `0.2.0` bevat niet langer de globale legacyfilters voor
  `cv_upload`; TFSC Form Bridge `0.4.0` handelt uitsluitend actief gemapte cv-
  en vacaturebijlagevelden af en bewaart daarvoor een eigen afgeschermde
  werkkopie tot Backblaze en Supabase de registratie hebben bevestigd. De code
  is op PHP 7.4 en 8.3 getest, staat live en de beveiligde verbindingstest geeft
  `connection_ok`.
- [x] `Registratie` openbaar met een echte DOCX getest. De eerste Backblaze-
  poging kreeg een tijdelijke afwijzing; de outbox-retry leverde daarna af.
  Supabase registreerde het originele document als `cv` met PDF-preview en
  Appsmith toont **Registratie / Verwerkt / Nieuw profiel** met actieve CV-knop.

- [x] De vijf actieve Bridge-formulieren één voor één afgerond met vaste
  Engelse technische `field_id`-waarden, ongewijzigde duidelijke Nederlandse
  labels, bijgewerkte Elementor-e-mail-/Akismet-shortcodes en gecontroleerde
  Form Bridge-mappings. Mailing Registratie is bewust overgeslagen omdat de
  pagina niet meer actief wordt gebruikt en niet wordt gekoppeld.
- [x] Per actief formulier een synthetische end-to-endtest uitvoeren en WordPress-
  outbox, Supabase-verwerking, Backblaze indien van toepassing en Appsmith
  controleren. Afgerond voor Registratie, Adviesaanvraag, Solliciteren, Vraag
  en Werkgeverscontact. Mailing Registratie blijft bewust buiten scope omdat
  die pagina niet meer actief wordt gebruikt.
- [x] Veldstandaardisatie uitgevoerd in de volgorde Registratie,
  Werkgeverscontact, Adviesaanvraag en Solliciteren. Mailing Registratie blijft
  buiten scope.
- [x] `Vacature - Stel je vraag` heeft canonieke Engelse veld-ID's en een
  actieve mapping in Form Bridge `0.3.8`. De mapping maakt een kandidaat maar
  geen bedrijf; alleen `vacancy_number` wordt als vacaturecontext vertrouwd.
- [x] `Vacature - Stel je vraag` openbaar synthetisch getest zonder én met
  vacaturecontext. Beide outbox-items leverden in één poging af en Supabase
  verwerkte ze zonder reviewcode. Alleen de vacaturevraag koppelde aan
  vacaturenummer `885425`; er ontstond geen sollicitatie. Appsmith toont beide
  als type `Vraag`.
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
- [x] `verzoek_advies` gebruikt nu `employment_preferences`, `last_name`,
  `city`, `mobile`, `cv`, `linkedin_url`, `ambition` en `privacy_consent`.
  Beheermail en Akismet zijn bijgewerkt, uploads zijn beperkt tot PDF/DOC/DOCX
  en de Bridge-mapping staat gecontroleerd op versie `3`.
- [x] `verzoek_advies` via de echte openbare browserroute getest. De outbox
  leverde direct af en Supabase verwerkte mappingversie `3` zonder reviewcode.
  De route maakte een persoon, kandidaatprofiel en adviesactiviteit, maar
  terecht geen sollicitatie, bedrijf of vacature. Appsmith toont het contact
  als type `Advies` in **Nieuwe contacten**.
- [x] `form_solliciteren_20240226` gebruikt nu `last_name`, `mobile`, `cv`,
  `linkedin_url`, `vacancy_number` en `privacy_consent`. Beheermail en Akismet
  zijn bijgewerkt, uploads zijn beperkt tot PDF/DOC/DOCX en de Bridge-mapping
  staat gecontroleerd op versie `3`.
- [x] `form_solliciteren_20240226` via de echte openbare route getest met een
  synthetische DOCX en bestaand canoniek vacaturenummer `885425`. Supabase
  verwerkte mappingversie `3` zonder reviewcode, maakte precies één
  sollicitatie bij de juiste vacature en registreerde de DOCX als `cv` met
  PDF-preview. Appsmith toont het contact als type `Sollicitatie`.
- [x] `Vacature - Contactverzoek (Werkgever)` openbaar getest met een echte
  synthetische DOCX. De eerste proef van vóór de fix maakte de CRM-records maar
  ging voor het document naar review met `document_path_missing`. Versie
  `0.4.0` bewaart daarom een afzonderlijke afgeschermde werkkopie voor de
  asynchrone aflevering. De herhaalproef leverde in één poging af: Supabase
  maakte zonder reviewcode persoon, bedrijf, bedrijfscontact, activiteit en
  Draft-vacature `884980`; de DOCX staat privé in Backblaze en is aan de
  vacature gekoppeld. Appsmith toont **Werkgever / Verwerkt**, het juiste
  bedrijf, vacaturenummer en één bijlage.
- [x] Terugkerende tijdelijke eerste Backblaze-uploadfout onderzocht en
  herstelroute live bewezen. TFSC Form Bridge `0.4.2` haalt bij een tijdelijke
  transport-, 408-, 429-, 5xx- of uploadtokenfout direct een verse upload-URL
  op. Veilige productielogging identificeerde tijdens een openbare
  Registratie-proef exact `503 service_unavailable`; ook de directe tweede pod
  was kort niet beschikbaar. De duurzame outbox hield de DOCX vast en leverde
  na backoff op poging twee volledig af. Supabase bevat het canonieke document,
  de versie, Backblaze-file-ID, kandidaatrelatie en PDF-preview; Appsmith toont
  de registratie met actieve CV-knop. De tijdelijke opslagpodfout is daarmee
  zichtbaar en zonder documentverlies afgevangen. Code en log: private repo
  `tfsc-crm-ats`, branch `codex/wordpress-form-bridge`, commits `3b7ce8a` en
  `e25913d`.
- [x] De adviesknop-/linkcode is op 21 oktober 2026 vereenvoudigd. De knop
  transporteert alleen nog het canonieke vacaturenummer als
  `?vacaturenummer=<nummer>`; op algemene archiefpagina's zonder één vacature
  opent het adviesformulier zonder vacatureparameter.
- [x] `verzoek_advies` gebruikt alleen nog het verborgen veld
  `vacancy_number`. `postTitle`, `postUrl`, `vacaturenaam` en `bedrijfsnaam`
  zijn uit het formulier verwijderd. Het e-mailonderwerp en de Akismet-mapping
  verwijzen niet meer naar die legacyvelden. De Bridge-mapping staat op versie
  `4` en koppelt `vacancy_number` aan `Vacature - vacaturenummer`.

De actuele connectorcode, veldcontracten en live testresultaten staan in de
private repository `tfsc-crm-ats`, branch `codex/wordpress-form-bridge`.

1. [x] De actuele configuratie van Registratie, Solliciteren,
   Werkgeverscontact, Mailing, Advies en Vraag op LIVE alleen-lezen uitlezen:
   formulier-ID, veld-ID, type, verplichtheid, toestemming, uploads, limieten
   en alle ingestelde acties. Afgerond op 19 oktober; zie de actuele nulmeting.
2. [x] Per formulier vastleggen welk bestaand Supabase-record en welke relaties
   moeten ontstaan. Geen nieuwe tabellen ontwerpen voordat deze mapping is
   goedgekeurd.
3. [x] Ieder Elementor-veld naast de bestaande Supabase-kolom en de relevante
   Appsmith-weergave of -invoer zetten; ontbrekende doelen en twijfelgevallen
   expliciet markeren.
4. [ ] Deduplicatie, bron-ID, toestemming, eigenaarschap en bewaartermijn per
   formulier laten goedkeuren.
5. [x] Daarna pas het technische ontwerp voor lokale Elementor-opslag,
   WordPress-outbox, beveiligde server-side aflevering, idempotentie, retries
   en handmatige review definitief maken.
6. [x] De koppeling eerst synthetisch en in schaduwmodus naast AFI/EngageBay
   testen. EngageBay pas per formulier uitschakelen nadat verwerking in
   Supabase en beheer in Appsmith aantoonbaar correct zijn.

Actuele Supabase-toestand: het bestaande personen-, kandidaat-, sollicitatie-,
vacature-, werkgevers- en documentenmodel wordt hergebruikt. De intake- en
werkvoorraadtabellen, idempotente verwerkings-RPC's en beveiligde Edge Function
`tfsc-form-ingest` staan LIVE; RLS en beperkte rollen zijn actief. Zie de
historische nulmeting in
`audits/2026-10-19-formulier-supabase-appsmith-nulmeting.md` en de actuele
implementatie en testlog in de private repository `tfsc-crm-ats`.

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

7. [x] **Alleen-lezen nulmeting voor formulieren afronden.** Live
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
11. [x] **Deduplicatie- en koppelregels goedkeuren.** E-mail, telefoon,
   LinkedIn en externe ID's normaliseren; ambigue gevallen altijd naar
   handmatige controle sturen.
12. [x] **Volledige veldmapping goedkeuren.** Ieder Elementor-veld koppelen aan
   het bestaande Supabase-model dat Appsmith beheert.
13. [x] **Technisch ontwerp definitief goedkeuren.** Lokale Elementor-opslag,
   WordPress-outbox, beveiligde Edge Function, idempotentie, retries,
   `dead_letter`/`review` en herstelprocedure vastleggen. De eigen TFSC Form
   Bridge is de actieve connector en vervangt AFI voor de vijf gekoppelde
   formulieren. Elementor Atomic Forms vallen buiten scope. De ontvangstkant
   is formulierbouwer-onafhankelijk opgezet, zodat de latere overstap naar
   Bricks dezelfde Edge Function, mapping, idempotentie en Supabase-tabellen
   kan behouden.
14. [x] **WordPress-connector en lokale outbox bouwen.** Werken na succesvolle
   Elementor-opslag, met unieke submission-UUID en zonder geheimen of
   persoonsgegevens in browsercode of gewone logs.
15. [x] **Supabase-intake en idempotente verwerking bouwen.** Bestaande
    tabellen en relaties hergebruiken; eventuele schemawijzigingen via een
    beoordeelde migratie, minimale rechten en passende RLS uitvoeren.
16. [x] **Cv- en documentroute bouwen.** Private Backblaze B2 is gekozen voor
    de bestanden; Supabase bewaart documentmetadata en relaties. MIME- en
    groottecontrole, documentversies, private toegang en PDF-preview zijn in
    de actieve route opgenomen.
17. [x] **Appsmith-weergave en beheer aansluiten.** De nieuwe Supabase-records
    en foutstatussen in het bestaande CRM tonen en beheren zonder een tweede
    synchronisatielaag of dubbel datamodel.
18. [x] **Synthetische end-to-endtests uitvoeren.** Nieuwe, bestaande en
    ambigue records, duplicaten, verkeerde/te grote bestanden en uitval/herstel
    van Supabase en fouten in Appsmith testen.
19. [x] **Schaduwmodus naast AFI/EngageBay draaien.** Uitkomsten vergelijken
    zonder de bestaande productieroute of lokale Elementor-inzendingen uit te
    schakelen.
20. [ ] **Backfill-droogloop maken.** Elementor en EngageBay vanaf de bewezen
    grens onderling en tegen Supabase ontdubbelen; alleen aantallen voor nieuw,
    bestaand, betrouwbaar, ambigu en fout vastleggen.
21. [ ] **Goedgekeurde backfill uitvoeren en reconciliëren.** Dezelfde
    idempotente route gebruiken, relaties en aantallen controleren en geen
    persoonsgegevens of exports in GitHub opnemen.
22. [x] **Gecontroleerd omschakelen.** AFI per formulier uitschakelen, niet
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

## Projectstatus: WordPress, Supabase en Appsmith verbinden

### Afgerond

- [x] De vijf actieve Elementor-formulieren hebben gecontroleerde mappings en
  vaste Engelse technische veld-ID's. Een gewijzigd veld-ID veroorzaakt een
  zichtbare waarschuwing en wordt nooit stilzwijgend opnieuw gekoppeld.
- [x] TFSC Form Bridge verzorgt lokale opslag, een duurzame WordPress-outbox,
  ondertekende server-side aflevering, idempotentie, retries, begrensde
  backoff en handmatige review.
- [x] De Supabase-intake, verwerkingsfuncties, RLS en beperkte rollen staan
  LIVE en hergebruiken het bestaande CRM-model.
- [x] Documenten gaan privé naar Backblaze B2; Supabase bewaart metadata en
  relaties en Appsmith kan het origineel en de PDF-preview openen.
- [x] Appsmith toont de nieuwe contacten, kandidaten, bedrijven, vacatures,
  sollicitaties, documenten en verwerkingsstatussen zonder een tweede kopie
  van het CRM-model.
- [x] Registratie, advies, vraag, sollicitatie en werkgeverscontact zijn
  end-to-end getest. De AFI-routes voor deze formulieren zijn uitgeschakeld.
- [x] TFSC Form Bridge `0.4.2` is LIVE en de productiepoort is groen. De
  gedetailleerde acceptatiecriteria, herstelroute en testmatrix staan in de
  private repository `tfsc-crm-ats`.

### Nog open

- [ ] Het abonnement/de licentie van Advanced Form Integration (AFI) opzeggen;
  de vijf vervangen formulierkoppelingen zijn technisch al uitgeschakeld.
- [ ] Eigenaarschap, bewaartermijnen, verwijderroute en logbeleid definitief
  vastleggen zonder deze besluiten alvast technisch af te dwingen.
- [ ] De exacte grens van de eerdere kandidaatimport bewijzen.
- [ ] Een privacyveilige backfill-droogloop voor Elementor en EngageBay maken,
  daarna alleen de expliciet goedgekeurde ontbrekende records importeren en
  reconciliëren.
- [x] De tijdelijke eerste Backblaze-afwijzing is als
  `503 service_unavailable` van de opslagpod geïdentificeerd. Versie `0.4.2`
  probeert direct een verse upload-URL en gebruikt daarna de begrensde
  outboxbackoff; de live DOCX-proef leverde op outboxpoging twee af.
- [x] De legacy frontendparameters en knopcode zijn op 21 oktober 2026
  vereenvoudigd tot het canonieke vacaturenummer. De algemene archiefroute
  zonder vacaturecontext is gecontroleerd. Een openbare inzending met
  WordPress-vacaturenummer `359` bewees de correcte overdracht en ging verwacht
  naar `optional_vacancy_not_found`, omdat deze vacature nog niet in Supabase
  staat; er ontstonden geen CRM-records. De herhaalproef met bestaande
  Supabase-vacature `885425` verwerkte mappingversie `4` zonder reviewcode,
  koppelde de adviesactiviteit aan de vacature en maakte geen sollicitatie.
  Appsmith **Nieuwe contacten** toont deze inzending als `Advies`, `Verwerkt`
  en `Nieuw profiel`, inclusief vacaturenummer en vacaturetitel.

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

- Geen historische kandidaat-, werkgever- of sollicitatiedata wijzigen buiten
  een vooraf goedgekeurde, idempotente backfill.
- Geen bewaartermijnen, verwijderregels of privacykeuzes aannemen voordat die
  expliciet zijn besloten.
- Geen historische review- of outboxregels verwijderen zonder controle en
  expliciet akkoord.
- Geen Elementor-inzendingen of cv's verwijderen.
- Geen persoonsgegevens, exports, databaseback-ups of geheimen naar GitHub
  pushen.
