# Centrale TODO

Laatst bijgewerkt: 19 oktober 2026

Dit is de centrale werklijst. Detailbesluiten en testresultaten blijven in de
bestaande documenten onder `audits/`, `content/` en `functionality/` staan.

## Uitvoervolgorde — één punt tegelijk

Deze lijst is de gezamenlijke werkvolgorde. We pakken steeds één genummerd
punt op, leggen het besluit, de wijziging en de verificatie vast en vinken het
pas daarna af. De uitgebreidere acceptatiecriteria en deelstappen staan in de
secties verderop in dit document. De volgorde kan alleen bewust worden
gewijzigd; open afhankelijkheden worden niet stilzwijgend overgeslagen.

### A. WordPress–Supabase–CRM-koppeling

1. [ ] **Nieuw doel-CRM kiezen.** EngageBay blijft uitsluitend legacy- en
   migratiebron. Zonder deze keuze wordt geen CRM-specifieke mapping of code
   gemaakt.
2. [ ] **Alleen-lezen nulmeting afronden en documenteren.** Git, live
   WordPress-formulieren, Supabase-schema/constraints/RLS/functions,
   EngageBay-bron, Appsmith-weergave en bestaande importlogica controleren.
3. [ ] **Exacte grens van de eerdere kandidaatimport vastleggen.** Het laatste
   betrouwbare bron-ID of eindmoment bewijzen; niet op een datum gokken.
4. [ ] **Actuele formulieren en uploads inventariseren.** Registratie,
   sollicitatie, werkgeverscontact, mailing, advies en vraag inclusief
   Elementor-veld-ID's, bestandstypen, limieten en bestaande acties vastleggen.
5. [ ] **Eigenaarschap en privacybesluiten nemen.** Per gegeven het leidende
   systeem, toestemming, bewaartermijn, verwijderroute en logbeleid bepalen.
6. [ ] **Deduplicatie- en koppelregels goedkeuren.** E-mail, telefoon,
   LinkedIn en externe ID's normaliseren; ambigue gevallen altijd naar
   handmatige controle sturen.
7. [ ] **Volledige veldmapping goedkeuren.** Ieder Elementor-veld koppelen aan
   het bestaande Supabase-model en, na punt 1, aan het nieuwe CRM-model.
8. [ ] **Technisch ontwerp definitief goedkeuren.** Lokale Elementor-opslag,
   WordPress-outbox, beveiligde Edge Function, idempotentie, retries,
   `dead_letter`/`review` en herstelprocedure vastleggen.
9. [ ] **WordPress-connector en lokale outbox bouwen.** Werken na succesvolle
   Elementor-opslag, met unieke submission-UUID en zonder geheimen of
   persoonsgegevens in browsercode of gewone logs.
10. [ ] **Supabase-intake en idempotente verwerking bouwen.** Bestaande
    tabellen en relaties hergebruiken; eventuele schemawijzigingen via een
    beoordeelde migratie, minimale rechten en passende RLS uitvoeren.
11. [ ] **Cv- en documentroute bouwen.** Besluit over private Supabase Storage,
    MIME-/groottecontrole, documentversies, relaties en herstelbaarheid eerst
    goedkeuren.
12. [ ] **Synchronisatie naar het nieuwe CRM bouwen.** Alleen vanuit de
    duurzame intake, met externe ID's, rate-limitafhandeling, retries en
    zichtbare foutstatus.
13. [ ] **Synthetische end-to-endtests uitvoeren.** Nieuwe, bestaande en
    ambigue records, duplicaten, verkeerde/te grote bestanden en uitval/herstel
    van Supabase en CRM testen.
14. [ ] **Schaduwmodus naast AFI/EngageBay draaien.** Uitkomsten vergelijken
    zonder de bestaande productieroute of lokale Elementor-inzendingen uit te
    schakelen.
15. [ ] **Backfill-droogloop maken.** Elementor en EngageBay vanaf de bewezen
    grens onderling en tegen Supabase ontdubbelen; alleen aantallen voor nieuw,
    bestaand, betrouwbaar, ambigu en fout vastleggen.
16. [ ] **Goedgekeurde backfill uitvoeren en reconciliëren.** Dezelfde
    idempotente route gebruiken, relaties en aantallen controleren en geen
    persoonsgegevens of exports in GitHub opnemen.
17. [ ] **Gecontroleerd omschakelen.** AFI per formulier uitschakelen, niet
    alles tegelijk; monitoring, foutoverzicht en herstelhandleiding opleveren.

### B. SEO, content en websitekwaliteit

18. [ ] **Canonicalstrategie voor losse vacaturetaxonomiearchieven kiezen.**
    Beslissen tussen de huidige combo-canonical en self-canonicals.
19. [ ] **Snippet 193 veilig aanpassen en testen.** Eerst een inactieve
    herstelkopie maken; alleen uitvoeren na punt 18.
20. [ ] **Combo-sitemap monitoren.** De 800 URL's met exact twee taxonomieën
    controleren op indexeerbaarheid, inhoud en duplicaten.
21. [ ] **Search & Filter- en overige `noindex`-combo's controleren.** Robots,
    canonical, sitemap en interne links nalopen.
22. [ ] **Reisprofielen uitwerken.** De 25 beschrijvingen schrijven en dubbele
    zoekvarianten alleen na inhoudelijk besluit samenvoegen of verwijderen.
23. [ ] **Centrale overzichtspagina voor publieke archieven ontwerpen.** Alleen
    goedgekeurde, indexeerbare archieven opnemen.
24. [ ] **LiteSpeed JavaScript veilig instellen en Clarity testen.** Combine en
    `Combine External and Inline` uitschakelen, deferred behouden en Clarity
    anoniem verifiëren.
25. [ ] **`font-display: swap` instellen en visueel testen.** Elementor-layout
    en fonts op representatieve pagina's controleren.
26. [ ] **Resterende taxonomieën en content gefaseerd afwerken.** Werkgebied,
    vacaturekoppelingen, blogmigraties, Arbeidsvoorwaarden en formulierteksten
    één gecontroleerd onderdeel tegelijk behandelen.
27. [ ] **WordPress-omvang en prestaties meten en optimaliseren.** Ongebruikte
    media en grote tabellen eerst inventariseren; alleen na back-up en expliciet
    akkoord opschonen.

## Eerst oppakken: open punten uit de vorige werksessie

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
- [ ] Reisprofielen als volgend inhoudelijk onderwerp uitwerken. De 25 termen
  hebben nog geen descriptions. Dubbele termen kunnen bewuste zoekvarianten
  zijn en worden niet zonder gezamenlijk besluit verwijderd of samengevoegd.
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

## Project: WordPress, Supabase en CRM verbinden

### Uitgangspunt

Een inzending wordt altijd eerst duurzaam in Elementor opgeslagen. Een storing
bij Supabase, CRM of e-mail mag nooit formulierwaarden of een cv laten
verdwijnen. Persoonsgegevens, cv's, sleutels en echte formulierwaarden komen
niet in GitHub of gewone foutlogs.

### Fase 1 — besluiten en veldmapping

- [ ] Het nieuwe doel-CRM vaststellen. EngageBay blijft niet het CRM; het is
  alleen de huidige legacy-bestemming van Advanced Form Integration totdat de
  nieuwe koppeling bewezen werkt.
- [ ] Per formulier vastleggen wat er moet ontstaan:
  - Registratie: `person` en `candidate_profile`, met toestemming en eventueel
    document.
  - Sollicitatie: kandidaat koppelen of aanmaken en precies één `application`
    aan de juiste vacature koppelen.
  - Werkgeverscontact: `person`, `company_contact` en `company` koppelen of
    gecontroleerd aanmaken.
  - Mailing, advies en vraag: bepalen welke gegevens naar Supabase en welke
    uitsluitend naar het CRM gaan.
- [ ] Per gegeven bepalen welk systeem leidend is: WordPress-inzending,
  Supabase of CRM. Dit geldt minimaal voor kandidaten, werkgevers, contacten,
  sollicitaties, vacatures, documenten, toestemming en communicatievoorkeuren.
- [ ] Een volledige veldmapping maken van ieder Elementor-veld naar de
  bestaande Supabase-kolom en het bijbehorende CRM-veld.
- [ ] Regels voor herkennen en samenvoegen vastleggen: genormaliseerd
  e-mailadres, telefoonnummer, LinkedIn-profiel en bestaande externe ID's.
  Twijfelgevallen gaan naar handmatige controle en overschrijven nooit
  stilzwijgend bestaande gegevens.
- [ ] Bewaartermijnen, verwijderverzoeken en toestemming uitwerken voor
  WordPress, Supabase, CRM en cv-bestanden.

### Fase 2 — bestaande systemen alleen-lezen inventariseren

- [ ] De relevante Supabase-tabellen, constraints, RLS-regels, functies en
  bestaande importlogica volledig documenteren voordat er iets wordt
  gewijzigd.
- [ ] Bestaande EngageBay-ID's als historische externe ID's en de tabel
  `external_identifiers` gebruiken om dubbele personen, kandidaten, contacten
  en bedrijven te voorkomen; EngageBay is daarmee geen doelarchitectuur.
- [ ] De officiële CRM-API controleren op authenticatie, rate limits,
  idempotentie, contact-/bedrijfsrelaties en foutmeldingen.
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
- [ ] Geen formuliergegevens of bestanden vanuit browser-JavaScript rechtstreeks
  naar Supabase of het CRM sturen.
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

### Fase 5 — CRM-synchronisatie

- [ ] Vanuit de duurzame Supabase-intake naar de officiële CRM-API sturen.
  WordPress stuurt niet parallel onafhankelijk naar twee systemen; dat zou
  afwijkende of dubbele records veroorzaken.
- [ ] Externe CRM-ID's centraal bewaren in `external_identifiers`.
- [ ] Kandidaat, werkgever, contactpersoon en sollicitatie met hun onderlinge
  relaties synchroniseren.
- [ ] CRM-uitval opvangen met retry, zichtbare foutstatus en handmatige
  herstelactie zonder de oorspronkelijke inzending opnieuw te laten invullen.

### Fase 6 — testen, kandidaten inhalen en gecontroleerd omschakelen

- [ ] Testen met synthetische gegevens: nieuwe, bestaande en ambigue kandidaat;
  nieuwe en bestaande werkgever/contactpersoon; dubbele verzending; verkeerd
  bestand; te groot bestand; uitval van Supabase; uitval van CRM; herstel na
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
  Supabase en daarna het nieuwe doel-CRM sturen. Iedere broninzending krijgt
  een vaste submission-UUID, zodat opnieuw uitvoeren geen duplicaten maakt.
- [ ] Na de inhaalslag aantallen en relaties tussen Elementor, Supabase en
  het nieuwe doel-CRM afstemmen en alle ambigue gevallen handmatig beoordelen.
- [ ] Pas na een geslaagde end-to-endcontrole de oude AFI-route per formulier
  uitschakelen. Niet alle formulieren tegelijk omschakelen.
- [ ] Monitoring, foutoverzicht en een korte herstelhandleiding opleveren.

## Overige bestaande backlog

- [ ] WordPress-, Supabase- en Appsmith-taxonomieën verder gelijktrekken,
  waaronder werkgebied, Reisprofielen en de resterende vacaturekoppelingen.
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
