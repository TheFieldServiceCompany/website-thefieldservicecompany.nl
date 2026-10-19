# Changelog

## 2026-10-19 — Beroepengids-bedrijfssectoren als artikelarchief ingericht

- Alle 33 bestaande descriptions, intro's en archieftitels van
  `beroep_bedrijfssector` zijn LIVE ingericht als artikelarchief.
- Iedere H1 gebruikt `Bedrijfssector [term] | Artikelen`; iedere description
  begint met `Bedrijfssector [term]` als H2 en bevat daarna drie compacte,
  sectorspecifieke alinea's.
- De bestaande lange vacaturedescriptions zijn als inhoudelijke bron gebruikt.
  De nieuwe Beroepengids-teksten introduceren toekomstige artikelen en zijn
  geen kopieën van de vacatureteksten.
- De descriptions bevatten 100–125 woorden en de intro's 17–24 woorden.
- Alle 99 waarden zijn na publicatie exact via REST geverifieerd. Aerospace &
  Luchtvaart, Machinebouw, Semiconductor, Watertechnologie en HVAC &
  Klimaattechniek zijn ook op de openbare pagina gecontroleerd.
- Er zijn geen termen, slugs, parents, relaties, Pods, velden, vacatures,
  artikelen of template-instellingen gewijzigd.
- Volledig log:
  `audits/2026-10-19-beroepengids-bedrijfssectoren-live.md`.

## 2026-10-19 — Beroepengids-functienamen als artikelarchief ingericht

- Alle 32 bestaande descriptions, intro's en archieftitels van
  `beroep_functienaam` zijn LIVE ingericht als artikelarchief.
- Iedere description bevat drie compacte alinea's: introductie op de
  functienaam, verwijzing naar toekomstige artikelen en uitleg hoe de lezer
  daarmee functies en vacatures kan vergelijken.
- Iedere description begint nu met `Functienaam [term]` als H2. De
  H1/Pods-archieftitel blijft `Functienaam [term] | Artikelen`; intro's en de
  drie bestaande alinea's zijn bij deze H2-toevoeging niet gewijzigd.
- De eerdere vier H2-vragen per term zijn verwijderd. Die zoekvragen worden
  later als zelfstandige artikelen uitgewerkt.
- Alle H1's gebruiken het patroon `Functienaam [term] | Artikelen`; de
  descriptions bevatten inclusief H2 107–147 woorden en de intro's 21–38
  woorden.
- De teksten zijn gebaseerd op de inhoud en het titelgebruik in actuele
  vacatures. Overlappende titels en spellingvarianten worden eerlijk
  uitgelegd en niet als kunstmatig verschillende beroepen gepresenteerd.
- Alle 96 waarden zijn na publicatie exact via REST geverifieerd. Zes openbare
  archieven zijn cachevrij gecontroleerd; alle gaven HTTP 200 en toonden de
  juiste H1, intro en drie archiefalinea's.
- Er zijn geen termen, slugs, relaties, Pods, velden, vacatures, artikelen of
  template-instellingen gewijzigd.
- Volledig log:
  `audits/2026-10-19-beroepengids-functienamen-live.md`.

## 2026-10-19 — Beroepengids-studierichtingen als artikelarchief ingericht

- Alle 23 descriptions, intro's en archieftitels van
  `beroep_studierichting` zijn LIVE ingericht als artikelarchief.
- Iedere description begint met `Studierichting [term]` als H2 en
  bevat daarna drie compacte alinea's over de studie, toekomstige artikelen en
  het vergelijken van field service functies en vacatures.
- De H1/Pods-archieftitel blijft `Studierichting [term] | Artikelen`; alleen
  de suffix `| Artikelen` is uit de H2 verwijderd. Intro's en alinea's zijn
  daarbij ongewijzigd gebleven.
- De eerdere vier uitgebreide vraagkoppen per term zijn verwijderd en worden
  later als zelfstandige artikelen uitgewerkt.
- De descriptions bevatten 102–121 woorden en de intro's 19–26 woorden.
- Alle 69 waarden zijn exact via REST geverifieerd. Vijf openbare archieven
  gaven HTTP 200 en toonden de juiste H1, H2, intro en drie alinea's.
- Er zijn geen termen, slugs, relaties, Pods, velden, vacatures, artikelen of
  template-instellingen gewijzigd.
- Volledig log:
  `audits/2026-10-19-beroepengids-studierichtingen-herschreven-live.md`.

## 2026-10-19 — Beroepengids-taxonomieën gelijkgetrokken met vacatures

- De termen en slugs van Bedrijfssectoren, Functie-inhoud, Functienamen,
  Kennisgebieden en Werkgebieden zijn LIVE één-op-één naar de bestaande
  Beroepengids-taxonomieën gespiegeld.
- 126 ontbrekende termen zijn via de standaard WordPress REST API aangemaakt;
  daarbij zijn uitsluitend `name` en `slug` verstuurd. De ene bestaande
  Functienaam is alleen qua hoofdletters gelijkgetrokken.
- De vijf al bestaande Pods-relatievelden zijn gebruikt. 126 lege relaties
  zijn ingevuld en de ene bestaande correcte relatie is behouden; de
  eindcontrole bevestigt 127 van 127 matches op slug.
- De eindtotalen zijn 33 Bedrijfssectoren, 29 Functie-inhoud-termen, 32
  Functienamen, 25 Kennisgebieden en 8 Werkgebieden.
- Er zijn geen descriptions, intro's, archieftitels of SEO-teksten ingevuld en
  geen Pods, velden of GUI-configuraties gewijzigd. Alle 127 doeltermen hebben
  nog een lege description.
- Volledig log:
  `audits/2026-10-19-beroepengids-vacaturetaxonomieen-live.md`.

## 2026-10-19 — Canoniek travelmodel afgerond en Appsmith-branches opgeschoond

- De definitieve travelvelden zijn voor kandidaat en vacature gelijkgetrokken:
  `International travel`, `Max international days / year`, `Maximum travel
  duration`, `Weekends home` en `Hotel nights/month`.
- Supabase-migratie `20261019151611_canonical_travel_fields_and_appsmith` is
  toegepast en getest. De 1.773 kandidaat-reisrecords en het bestaande
  vacature-reisrecord zijn behouden en niet automatisch geconverteerd.
- Appsmith gebruikt op `crm-layout-redesign` dezelfde veldnamen en
  validatiewaarden. De rasteroverlap van de travelvelden en de afwijkende
  desktoppositie van de kandidaat-documentpreview zijn gecorrigeerd.
- Het langste Reisduur-label is definitief `meer dan 30 dagen` in WordPress en
  `More than 30 days` in Appsmith. De interne waarde blijft
  `days_31_plus`.
- LIVE WordPress bevat nu alleen de vijf canonieke Reisduur-termen. De 25 oude
  Reisprofiel-termen zijn verwijderd; vacature `23277` bestaat nog en heeft nu
  geen Reisduur-relatie, zodat die later inhoudelijk opnieuw wordt ingevuld.
- De nieuwe LIVE-taxonomie `Hotel nachten/maand` bevat 0, 1–7, 8–14 en 15–31.
  Search & Filter-veld #16 is aangemaakt, maar bewust niet in de zichtbare GUI
  geplaatst.
- `Dagen internationaal`-term ID 358 heet nu `Meer dan 200 dagen`; de slug
  `vanaf-200-dagen` bleef behouden.
- Acht volledig opgenomen remote Appsmith-branches zijn verwijderd. De
  actuele werkbranch, `main`, de actieve backfillbranch en vier branches met
  elk één unieke commit zijn behouden.
- Volledig log:
  `audits/2026-10-19-travel-uitvoering-appsmith-en-branchopschoning.md`.

## 2026-10-19 — Reisduur en Weekenden thuis additive LIVE in WordPress

- Het beheerlabel van de bestaande taxonomie `reisprofiel` is LIVE aangepast
  naar `Reisduur`; de interne slug en alle 25 legacytermen zijn behouden.
- De openbare vacaturetaxonomie `weekenden-thuis` is LIVE toegevoegd en via
  REST beschikbaar gemaakt.
- Vijf Reisduur-banden en vijf percentages Weekenden thuis zijn met meetbare
  operationele descriptions toegevoegd. Geen vacature is automatisch gemapt.
- Search & Filter-velden `Reisduur` (#14) en `Weekenden thuis` (#15) zijn
  ingeschakeld voor de query `Vacatures`, met uitsluitend de vijf canonieke
  waarden per veld en `match any`.
- REST bevestigde beide vacaturetaxonomieën, alle tien slugs en descriptions,
  en telling 0 voor de nieuwe termen. De legacytermen en bestaande relaties
  bleven aanwezig.
- GitHub bevestigde dat `crm-layout-redesign` de actuele Appsmith-werkbranch
  is. `document-preview-backfill` is daarin al gemerged; `Candidates` en
  `Vacancies_Old` zijn vervolgens bewust verwijderd. Travelcommit `6f182a4`
  staat correct boven op de laatste bestaande commit `e709b22`.
- De live Appsmith-editor is van de onjuiste branch `main` overgeschakeld naar
  `crm-layout-redesign`. De actuele `Contacts`-opbouw werd geladen, de
  pullactie was uitgeschakeld en het commitvenster bevatte geen wijzigingen;
  de branch liep dus al gelijk met GitHub. Er is niet gedeployd.
- Volledig uitvoeringslog:
  `audits/2026-10-19-reisduur-weekenden-thuis-live.md`.

## 2026-10-19 — Meetbaar travelmodel uitgevoerd in Supabase en Appsmith

- Definitief besloten dat de eerste Reisduur-optie `Elke nacht thuis` heet;
  dit is geen reisduur van één dag maar betekent dat er niet buitenshuis wordt
  overnacht.
- Reisduur vastgesteld op `Elke nacht thuis`, `2–7 dagen`, `8–14 dagen`,
  `15–30 dagen` en `meer dan 30 dagen`; Weekenden thuis op 0%, 25%, 50%, 75%
  en 100%.
- Reisfrequentie als aparte dimensie geschrapt. Hotelovernachtingen is later
  die dag alsnog als het meetbare veld `Hotel nachten/maand` toegevoegd,
  omdat dit ook bij nationaal werk relevant is.
- Vier nullable velden plus check constraints toegevoegd in Supabase staging;
  Appsmith-read/savefuncties, widgets en matching daarop aangepast.
- De 1.773 bestaande kandidaatreisrecords en het bestaande vacature-record
  niet automatisch geconverteerd. Nieuwe waarden blijven leeg tot inhoudelijke
  validatie.
- Rollbacktests voor kandidaat en vacature geslaagd. Bestaande legacyvelden en
  terugvalmatching blijven beschikbaar.
- De additive WordPress-uitvoering voorbereid: bestaande interne slug
  `reisprofiel` behouden als Reisduur, een tweede taxonomie Weekenden thuis
  toevoegen en legacytermen pas na relatie-, redirect- en contentcontrole
  verwijderen.

## 2026-10-19 — Reisduur en Weekenden thuis meetbaar gemaakt

Dit was een tussenbesluit. Het is later dezelfde dag aangescherpt door
`1 dag` te vervangen door `Elke nacht thuis`, `31+ dagen` te hernoemen naar
`meer dan 30 dagen` en `Hotel nachten/maand` als aparte meetbare dimensie toe
te voegen.

- Het eerdere voorstel met `Kort verblijf` en `Lang verblijf` ingetrokken;
  deze labels zijn zonder vaste grens niet meetbaar.
- Als nieuwe publieke filterdimensie `Reisduur` voorgesteld met `1 dag`,
  `2–7 dagen`, `8–14 dagen`, `15–30 dagen` en `31+ dagen`.
- Vastgelegd dat `0 dagen` geen Reisduur is maar geen reis betekent, en dat
  `1 dag` apart moet staan om overnachtingen exact te kunnen afleiden.
- `Weekenden thuis` voorgesteld als 0%, 25%, 50%, 75% en 100%, met onbekend als
  aparte lege datastatus. De huidige ja/nee-velden in Supabase zijn hiervoor
  onvoldoende en worden niet zonder goedgekeurde migratie gewijzigd.
- `Hotelovernachtingen` niet langer als aparte operationele filter voorgesteld;
  vanaf een Reisduur van 2 dagen is minimaal één overnachting afleidbaar, maar
  niet het type accommodatie. Het zoekwoord blijft beschikbaar voor
  descriptions, landingspagina's en SEO.
- Vastgelegd dat Werkgebieden en Dagen internationaal de andere hoofdvragen al
  afdekken. Reisfrequentie is de enige mogelijke aanvullende kwantitatieve
  dimensie en wordt alleen toegevoegd als daar aantoonbare filter- of
  matchbehoefte voor is.
- Er zijn geen livegegevens, databasevelden of instellingen gewijzigd.

## 2026-10-19 — Naamrichting Verblijfsduur en contentlaag vastgelegd

- Voorgesteld de korte filterdimensie `Verblijfsduur` te gebruiken met de
  opties `Kort verblijf` en `Lang verblijf`, omdat Supabase de maximale
  aaneengesloten nachten weg bewaart en niet de gemiddelde projectduur.
- Voorgesteld `Hotelovernachting` te hernoemen naar
  `Hotelovernachtingen` en `Weekenden thuis` als korte filterterm te behouden.
- `Weekdagen weg` niet als tweede overlappende filterterm voorgesteld; de
  zoekvariant wordt verwerkt in descriptions met de juiste nuance.
- Vastgelegd dat langere, beschrijvende reisthema's later als inhoudelijke
  landingspagina of Beroepengidsartikel met een dynamisch vacatureblok kunnen
  worden uitgewerkt, zonder voor iedere zoekterm een extra filterwaarde te
  maken.
- Er zijn geen livegegevens of instellingen gewijzigd.

## 2026-10-19 — Reisprofielen live geïnventariseerd en syncplan vastgelegd

- De 25 LIVE-Reisprofiel-termen via WordPress REST alleen-lezen vastgelegd,
  inclusief slugs, lege descriptions, gebruikstellers en de ene gekoppelde
  vacature.
- Voorgesteld vijf onderscheidende publieke profielen te behouden en twintig
  overlappende, geografische of niet-canonieke termen gecontroleerd te mappen,
  samen te voegen of te verwijderen. Zoekvarianten blijven in descriptions en
  relevante archiefteksten behouden; oude URL's worden vóór verwijdering op
  redirects en indexering beoordeeld.
- Supabase alleen-lezen gecontroleerd. De gestructureerde reis-, werkgebied- en
  mobiliteitsvelden bestaan al; de 25 WordPress-termen zijn niet als dubbele
  vocabulaire of termmappings gekopieerd.
- Vastgelegd dat 1.773 geïmporteerde kandidaat-reisrecords nog `unreviewed`
  zijn en later inhoudelijk moeten worden gevalideerd, maar niet blind worden
  verwijderd of overschreven.
- Bevestigd dat Opleidingsniveau 4 en Studierichting 23 actieve WordPress-naar-
  Supabase-mappings hebben. Een centrale herhaalbare werkwijze voor alle
  WordPress–Supabase–Appsmith-keuzes is toegevoegd.
- Er zijn geen livegegevens of instellingen in WordPress, Supabase of Appsmith
  gewijzigd. Zie
  `audits/2026-10-19-reisprofielen-wordpress-supabase-appsmith.md` en
  `audits/2026-10-19-wordpress-supabase-appsmith-sync-plan.md`.

## 2026-10-19 — Reisprofielen en CRM-architectuur gecorrigeerd

- Gecorrigeerd dat Appsmith het CRM en de beheerinterface is en Supabase de
  canonieke database; EngageBay blijft uitsluitend legacy- en migratiebron.
- De uitvoervolgorde aangepast: eerst de WordPress-taxonomieën afronden,
  beginnend met de 25 Reisprofiel-termen en hun relatie met de bestaande
  gestructureerde reis- en mobiliteitsvelden in Supabase/Appsmith.
- De eerdere Reisprofiel-bespreking vastgelegd, inclusief de gemengde
  dimensies, bekende doublures en het besluit dat bewuste zoektermvarianten
  niet automatisch worden verwijderd of als dubbele Appsmith-keuzes worden
  overgenomen.
- Er zijn geen gegevens of instellingen in WordPress, Supabase of Appsmith
  gewijzigd. Details staan in
  `audits/2026-10-19-reisprofielen-wordpress-supabase-appsmith.md`.

## 2026-10-19 — Centrale TODO en integratieroute

- Een centrale `TODO.md` toegevoegd met de open SEO- en sitemapcontroles uit
  de vorige werksessie, de eerstvolgende LiteSpeed-/Clarity- en
  formuliercontroles en de bestaande content- en onderhoudsbacklog.
- De geplande WordPress–Supabase–Appsmith-koppeling uitgewerkt in zes veilige fasen:
  veldmapping, alleen-lezen inventarisatie, duurzame ontvangst/foutopvang,
  private documentopslag, Appsmith-aansluiting en gecontroleerde omschakeling.
- Alleen-lezen bevestigd dat het bestaande Supabase-model al relaties bevat
  voor personen, kandidaatprofielen, sollicitaties, bedrijven,
  contactpersonen, documenten en externe ID's. Er zijn geen database-,
  WordPress- of CRM-wijzigingen uitgevoerd.
- Een gecontroleerde kandidaat-inhaalslag toegevoegd voor registraties en
  sollicitaties die ná de eerdere Supabase-import via de website zijn
  binnengekomen. Eerst wordt de exacte importgrens teruggevonden, daarna volgt
  een droge deduplicatiecontrole en pas vervolgens verwerking via dezelfde
  idempotente route als nieuwe inzendingen.
- Gecorrigeerd dat EngageBay niet het toekomstige CRM blijft. Het staat alleen
  als tijdelijke legacyroute en historische externe-ID-bron in de roadmap.
- De kandidaat-inhaalslag uitgebreid naar twee bronnen: latere Elementor-
  inzendingen én latere EngageBay-kandidaten. Beide bronnen worden eerst
  onderling en daarna tegen Supabase ontdubbeld.
- `HANDOFF-2026-10-19.md` toegevoegd voor voortzetting in een nieuwe lokale
  Codex-chat, inclusief verbindingen, veiligheidsgrenzen, open taken en lokale
  startinstructies.

## 2026-10-19 — Vacaturecombinaties beperkt tot twee taxonomieën

- Snippet 148 begrensd tot exact twee taxonomieën per sitemap-URL en voorzien
  van een nieuwe cache-sleutel voor deze eerste publicatiefase.
- Snippet 193 begrensd tot maximaal twee taxonomieën voor indexering; een
  combinatie met drie taxonomieën krijgt nu `noindex, follow`.
- De uitleg in de snippet en de pluginomschrijving gelijkgetrokken met deze
  grens van twee taxonomieën.
- De Rank Math-sitemapmodule eenmaal uit- en weer ingeschakeld om de
  sitemapindex via de bestaande, eenvoudige werkwijze opnieuw op te bouwen.
- Live resultaat gecontroleerd: twee combo-sitemaps met respectievelijk 501 en
  299 URL's, samen 800 unieke URL's met exact twee taxonomieën.
- Een tweedimensionale test-URL geeft `index, follow` met self-canonical; een
  driedimensionale test-URL geeft `noindex, follow` zonder canonical.
- Een tijdelijk geteste zwaardere cacheverwijdering is teruggedraaid; de
  actieve code bevat alleen de eenvoudige limiet en normale cachewerking.
- Inactieve herstelkopieën 243, 244, 245 en 246 blijven in WordPress bewaard.
  Er zijn geen vacatures, taxonomietermen of overige pagina-inhoud gewijzigd.
- Details staan in
  `audits/2026-10-19-combo-sitemap-max-two-taxonomies.md`.

## 2026-10-19 — LIVE opnieuw indexeerbaar gecontroleerd

- Homepage, Vacatures, Beroepengids, Kennisbank en een functie-inhoudarchief
  geven HTTP 200 en `follow, index`; de sitebrede `noindex, nofollow` is weg.
- `robots.txt` en de sitemapindex zijn bereikbaar.
- Het bestaande canonicalbeleid van snippet 193 opnieuw zichtbaar en op
  meerdere taxonomieën bevestigd: andere native vacaturetaxonomieën dan
  functienaam en locatie verwijzen momenteel naar een combo met
  `field-service-engineer` als vaste context.
- Vastgelegd dat dit canonicalbesluit en de 15.836 combo-URL's zonder
  geleidelijke sitemaplimiet op 20 oktober als eerste SEO-punten opnieuw
  worden beoordeeld.
- Er is niets in WordPress gewijzigd. Details staan in
  `audits/2026-10-19-live-indexability-reenabled.md`.

## 2026-10-19 — Centrale archievenpagina en reisprofielen als TODO vastgelegd

- TODO toegevoegd voor één centrale, logisch gegroepeerde overzichtspagina
  waar bezoekers naar alle relevante publieke archieven kunnen doorklikken.
- Vastgelegd dat vóór bouw eerst indexeerbaarheid, inhoudskwaliteit,
  groepering, interne links, sitemap en automatische versus handmatige
  opbouw worden bepaald.
- Vastgelegd dat de dubbele reisprofieltermen deels bewust als
  zoektermvarianten bestaan en niet zonder gezamenlijk modelbesluit worden
  verwijderd of samengevoegd.
- Er zijn geen WordPress-termen, relaties, teksten of instellingen gewijzigd.

## 2026-10-18 — Alle functie-inhoudtitels gelijkgetrokken

- De resterende twintig `pods_archive_title`-velden onder `functie-inhoud`
  aangepast naar `Field service vacatures met functie-inhoud [term]`.
- Voor de term `Field service` de expliciete uitzondering toegepast:
  `Field service vacatures met functie-inhoud field service`.
- De volledige taxonomie opnieuw via REST gecontroleerd: 29 van 29 titels
  komen exact overeen met het afgesproken format.
- De publieke H1's van Field service en Commissioning aanvullend gecontroleerd.
- Uitsluitend de twintig titelvelden gewijzigd; descriptions, relaties,
  termnamen, snippets, templates en Pods-instellingen zijn niet aangepast.
- De exacte waarden staan in
  `content/functie-inhoud/functie-inhoud-overige-titels-live.json` en de
  controle in `audits/2026-10-18-functie-inhoud-eight-terms-live.md`.

## 2026-10-18 — Acht functie-inhoudtitels volgens vast schema gezet

- De archieftitels van Sales, Storingen, Technical Support, Testen, Training,
  Troubleshooting, Upgrades en Werkvoorbereiding gecorrigeerd naar
  `Field service vacatures met functie-inhoud [term]`, zonder
  aanhalingstekens.
- Alle 29 titels van `functie-inhoud` exact met dit schema vergeleken: 9
  voldoen volledig en 20 gebruiken nog `Field Service Vacatures` met andere
  hoofdletters. Deze twintig zijn alleen geïnventariseerd en niet gewijzigd.
- De nieuwe H1 van Sales op de gerenderde frontend gecontroleerd.
- Alleen de acht expliciet opgedragen titelvelden zijn gewijzigd; descriptions,
  relaties, termnamen, snippets, templates en Pods-instellingen bleven
  onaangeraakt.
- De volledige afwijkingenlijst staat in
  `audits/2026-10-18-functie-inhoud-eight-terms-live.md`.

## 2026-10-18 — Functie-inhoud volledig gevuld

- De acht nog lege termen onder `functie-inhoud` voorzien van unieke
  archieftitels en descriptions van 400–409 woorden: Sales, Storingen,
  Technical Support, Testen, Training, Troubleshooting, Upgrades en
  Werkvoorbereiding.
- Sales gericht op technische serviceverkoop, waaronder spare parts,
  onderhoud, advies, upgrades en servicecontracten.
- Storingen en troubleshooting bewust afzonderlijk uitgewerkt als
  respectievelijk direct herstel en systematische root-causeanalyse.
- Werkvoorbereiding beschreven rond zowel klantprojecten vóór vertrek als de
  voorbereiding vanuit kantoor, werkplaats of magazijn.
- Alle acht waarden via REST gepubliceerd en exact opnieuw uitgelezen. De
  gerenderde archieven Storingen en Werkvoorbereiding tonen de nieuwe H1 en
  description correct.
- Geen snippets, templates, Pods-instellingen, relaties of Rank Math-velden
  gewijzigd. De ontbrekende functienaamterm `Werkvoorbereider` is alleen als
  later te beoordelen aandachtspunt vastgelegd.
- Details staan in
  `audits/2026-10-18-functie-inhoud-eight-terms-live.md`.

## 2026-10-18 — Dienstverbanden inhoudelijk herschreven

- `Freelance/ZZP` en `Vast Dienstverband` voorzien van unieke archieftitels en
  descriptions van 369 en 371 woorden.
- Freelance/ZZP gepositioneerd rond vrijheid, specialistische projecten en de
  eigen verantwoordelijkheid voor administratie, verzekeringen, pensioen,
  vakantie, ziekte en continuïteit.
- Vast dienstverband gepositioneerd rond rechtstreekse indiensttreding bij de
  werkgever, kennisinvestering, betaald verlof, loondoorbetaling bij ziekte,
  pensioenopbouw en doorgroeimogelijkheden. Geen detachering of
  uitzendconstructie.
- Oorspronkelijke waarden vooraf opgeslagen; beide termen via REST gepubliceerd
  en exact opnieuw uitgelezen.
- Het archief Vast Dienstverband in de gerenderde frontend gecontroleerd: H1,
  vacaturelijst en description-accordion werken.
- Geen snippets, templates, Pods-instellingen, slugs, relaties of Rank Math-
  velden gewijzigd. Details staan in
  `audits/2026-10-18-dienstverbanden-content-live.md`.

## 2026-10-18 — Dagen internationaal inhoudelijk herschreven

- Alle vier termen van `dagen-internationaal` voorzien van een unieke
  archieftitel en een nieuwe description van 369–411 woorden.
- `Geen` bewust gepositioneerd als nationaal field service werk met technische
  afwisseling en ruimte voor het privéleven.
- De drie internationale categorieën inhoudelijk opgebouwd van avontuur met
  balans via een internationale levensstijl naar field service als complete
  lifestyle.
- Bestaande descriptions vooraf opgeslagen; alle vier nieuwe teksten via REST
  gepubliceerd en exact opnieuw uitgelezen.
- De al bestaande Pods-titelvelden via de WordPress-interface ingevuld omdat
  deze taxonomie ze niet als schrijfbaar REST-veld ontsluit. Alle vier waarden
  na opslag gecontroleerd.
- Geen snippets, templates, Pods-instellingen, slugs, relaties of Rank Math-
  velden gewijzigd. Details staan in
  `audits/2026-10-18-dagen-internationaal-content-live.md`.

## 2026-10-18 — Archieftitels voor vacatureopleidingen gelijkgetrokken

- Voor alle 4 vacature-opleidingsniveaus `pods_archive_title` ingesteld als
  `Field service vacatures voor opleidingsniveau [term]`.
- Voor alle 23 vacature-studierichtingen `pods_archive_title` ingesteld als
  `Field service vacatures voor studierichting [term]`.
- Alle 27 waarden via REST herlezen en exact geverifieerd; de spiegelende
  Beroepengids-taxonomieën zijn niet gewijzigd.
- Een templateverschil opgelost waardoor single-term-combo-archieven het
  ingevulde Pods-veld nog niet als H1 gebruikten. Vooraf inactieve
  herstelkopie 242 gemaakt; snippet 186 daarna bijgewerkt van 4.5.3 naar
  4.5.4 en zonder codefout herlezen.
- HBO, Industriële Automatisering en de combinatie van beide op de gerenderde
  frontend getest. De override werkt op single-term-archieven en de bestaande
  combinatie-H1 blijft intact.
- Details staan in
  `audits/2026-10-18-vacature-education-archive-titles-live.md`.

## 2026-10-18 — Ontbrekende taxonomy-term descriptions geïnventariseerd

- Alle 12 vacaturetaxonomieën en 7 Beroepengidstaxonomieën alleen-lezen
  gecontroleerd op het gewone WordPress-termveld `description`.
- Bij Vacatures ontbreken descriptions bij 1 term onder Dagen internationaal,
  8 onder Functie-inhoud, 3 opleidingsniveaus, alle 25 reisprofielen, alle 3
  statustermen en alle 23 studierichtingen.
- Bij Beroepengids ontbreken descriptions voor `field service engineer` en
  `World`. Bedrijfssectoren en Kennisgebieden hebben daar nog geen termen.
- Bedrijfssectoren, Dienstverbanden, Functienamen, Kennisgebieden, Locaties en
  Werkgebieden aan vacaturezijde zijn volledig gevuld. De 4
  opleidingsniveaus en 23 studierichtingen aan Beroepengidszijde zijn eveneens
  volledig gevuld.
- Er is niets gewijzigd. Details staan in
  `audits/2026-10-18-taxonomy-term-description-audit.md`.

## 2026-10-18 — REST-mogelijkheden van LIVE-plugins geïnventariseerd

- De actuele lijst van 32 geïnstalleerde plugins en alle geregistreerde
  REST-namespaces via de beveiligde WordPress-inlog alleen-lezen vergeleken.
- Bevestigd dat Code Snippets, Pods, Search & Filter, Elementor, Rank Math,
  Wordfence, Site Kit, Akismet, SNIP Pro en WP Rollback eigen REST-routes
  registreren. LiteSpeed en Advanced Form Integration hebben slechts beperkte
  routes.
- Bevestigd dat WordPress Core pluginstatus en -installaties via
  `/wp/v2/plugins` ontsluit voor een bevoegde beheerder.
- Alle vijf Pods-posttypes en twintig Pods-taxonomieën hebben `rest_enable=1`.
  De vacature-Pod staat expliciet op `read_all=1` en `write_all=1`; de
  customvelden zijn aantoonbaar aanwezig in de vacature-REST-respons.
- UpdraftPlus, WPvivid, Admin Columns, Advanced Cache Warmer, Duplicate Page,
  Members en WordPress Importer registreren momenteel geen eigen REST-
  namespace. Voor back-up en restore blijft de GUI bewust de voorkeursroute.
- Er zijn geen plugin- of Pods-instellingen gewijzigd. De volledige matrix en
  aanbevolen werkwijze staan in
  `audits/2026-10-18-live-plugin-rest-capabilities.md`.

## 2026-10-18 — Elementor-relatiequery behoudt vacaturestatusfilters

- Oorzaak vastgesteld in actieve Code Snippet 204: de query
  `relationship_posts` verving de volledige bestaande Elementor `tax_query`.
  Daardoor verdween onder meer de ingestelde uitsluiting
  `vac_status = Ingevuld` zodra relatievelden werden toegepast.
- Vooraf inactieve herstelkopie 240 gemaakt en herkenbaar benoemd.
- Snippet 204 bijgewerkt van versie 3.0 naar 3.1. De bestaande Elementor-
  voorwaarden en de relatievoorwaarden worden nu als twee geneste groepen met
  `AND` gecombineerd; de interne `AND`/`IN`-logica van beide groepen blijft
  intact.
- Functioneel getest op de Beroepengidsarchieven **Maritieme techniek** en
  **MBO**. De ingevulde vacature *Internationaal Service Monteur Maritiem*
  wordt niet meer getoond, terwijl gekoppelde open vacatures zichtbaar
  blijven.
- REST-herlezing bevestigt dat snippet 204 actief is en geen codefout meldt.
  Details staan in
  `audits/2026-10-18-elementor-relationship-query-preserve-filters.md`.

## 2026-10-18 — Rank Math-audit Opleidingen uitgevoerd

- Rank Math-instellingen, live HTML-meta, robots, canonicals en de werkelijke
  XML-sitemaps van beide Opleidingen-taxonomieën alleen-lezen gecontroleerd.
- Titles werken volgens het ingestelde patroon. De twee virtuele
  basisarchieven hebben afzonderlijke titles en descriptions.
- Vastgesteld dat alle 27 termarchieven hun volledige lange termdescription
  als meta-description gebruiken: circa 3.619–4.058 tekens. Dit moet vóór
  indexering worden vervangen door unieke korte meta-descriptions.
- Rank Math heeft `noindex_empty_taxonomies` ingeschakeld, terwijl alle 27
  termen voor WordPress een post count van nul hebben. Hiervoor is een smalle
  oplossing nodig die uitsluitend deze twee inhoudelijk gevulde taxonomieën
  indexeerbaar maakt.
- Sitemapindex geeft XML/HTTP 200 en bevat beide taxonomysitemaps eenmaal. De
  afzonderlijke sitemaps bevatten exact 4 en 23 URL's. De oude
  `beroep_opleiding`-sitemap geeft 404.
- Elementor-onderhoudsmodus bleef aan; er zijn geen Rank Math-waarden of
  content gewijzigd. Details en hersteladvies staan in
  `audits/2026-10-18-rank-math-education-audit.md`.

## 2026-10-18 — Opleidingen-intro's inhoudelijk herschreven

- Vastgesteld dat alle 27 introducties hetzelfde generieke patroon gebruikten
  en met `Ontdek` begonnen.
- De 4 opleidingsniveau-intro's en 23 studierichtingintro's vervangen door
  afzonderlijke teksten die inhoudelijk aansluiten op het betreffende niveau
  of vakgebied en de toepassing binnen field service.
- Alleen het Pods-veld `intro_taxonomy` aangepast. Termdescriptions,
  relaties, namen, slugs, Rank Math en vacaturekoppelingen bleven ongewijzigd.
- Controle na publicatie: 27 verschillende intro's, nul openingen met
  `Ontdek`, nul afwijkingen ten opzichte van de goedgekeurde payload en nul
  gewijzigde descriptions.
- Live snapshot vastgelegd in
  `content/education-descriptions/intros-live-2026-10-18.json`.

## 2026-10-18 — Tijdelijke Opleidingen-structuur opgeruimd

- De vervangen Pods-taxonomie `beroep_opleiding` (ID 27600), de vier velden,
  veldgroep en alle 29 uitsluitend daarin bestaande termen verwijderd.
- Vooraf bevestigd dat de 27 inhoudelijke waarden één-op-één aanwezig zijn in
  `beroep_opleidingsniveau` en `beroep_studierichting`. Na afloop bevatten de
  huidige taxonomieën nog steeds respectievelijk 4 en 23 termen, allemaal met
  een gevulde vacaturetaxonomierelatie.
- Oude Rank Math-sleutels en tijdelijke migratieopties verwijderd. Voor beide
  huidige taxonomieën staan sitemap en `include_empty` aan; rewrites en de
  Rank Math-sitemapcache zijn vernieuwd.
- De goedgekeurde eenmalige, diagnose- en testsnippets 213, 214, 216–220,
  226–229, 231 en 233 definitief verwijderd. De tijdelijke cleanupsnippets
  234 en 235 zijn eveneens direct na gebruik verwijderd.
- Herstelkopieën 210–212, 221–225, 230 en 232 bewust behouden. De actieve
  snippets zijn gecontroleerd en melden geen codefout.
- Uitvoering en verificatie vastgelegd in
  `audits/2026-10-18-beroepengids-opleiding-cleanup.md`.

## 2026-10-18 — Generieke archieftiteloverride voor Beroepengids

- Op de zeven actieve Beroepengids-taxonomie-Pods een groep `Archive` met het
  Plain Text-veld `pods_archive_title` toegevoegd, exact volgens de bestaande
  vacature-Podconfiguratie. Bestaande velden en relaties bleven ongemoeid.
- Snippet 39 bijgewerkt naar versie 2.0. Een ingevulde
  `pods_archive_title` overschrijft nu de zichtbare Elementor-archieftitel;
  bij een leeg of ontbrekend veld blijft de normale termnaam staan.
- Voor de wijziging inactieve herstelkopie 232 gemaakt. Tijdelijke testsnippet
  233 en de Rank Math-debugsnippet 231 zijn na de latere eindcontrole
  definitief verwijderd.
- Lege waarde, ingevulde override en herstel functioneel getest op
  `beroep_functienaam`-term 437. De oorspronkelijke lege waarde en H1 zijn
  hersteld. Elementor-templates, Rank Math, breadcrumbs, slugs en URL's zijn
  niet gewijzigd.
- Technische details en alle groep- en veld-ID's vastgelegd in
  `audits/2026-10-18-beroepengids-archive-title-override.md`.

## 2026-10-18 — Opleidingenmodel gesplitst naar twee taxonomieën

- De tijdelijke gecombineerde taxonomie `beroep_opleiding` vervangen in de
  actieve architectuur door `beroep_opleidingsniveau` en
  `beroep_studierichting`, direct aansluitend op de twee vacaturetaxonomieën.
- 4 opleidingsniveau- en 23 studierichtingtermen met content en relaties
  gemigreerd. De lege testterm `Opleiding A` is verwijderd.
- Snippets 62, 113, 202, 209 en 215 naar de gesplitste opzet gebracht met
  inactieve herstelkopieën 222–225 en 230.
- Pagina 24266 bevat nu twee zelfstandig werkende kaarten, exact opgebouwd
  zoals het bestaande Functienamenblok.
- De oude Pod `beroep_opleiding` en zijn dubbele termen zijn na controle
  verwijderd.
- Actuele architectuur en open punten vastgelegd in
  `audits/2026-10-18-beroepengids-opleidingen-split-live.md`.

## 2026-10-18 — Beroepengids Opleidingen LIVE uitgevoerd (historische eerste opzet)

- Op LIVE de hiërarchische Pods-taxonomie `beroep_opleiding` aangemaakt met
  `Opleidingsniveau` en `Studierichting` als parenttermen, plus 4 niveau- en
  23 studierichtingtermen.
- Alle 27 kindtermen gekoppeld aan de juiste bestaande vacaturetaxonomie en
  voorzien van een unieke intro en SEO-description. Het virtuele
  Opleidingen-archief kreeg eveneens een intro en een description van 459
  woorden.
- Snippets 62, 113 en 209 veilig aangepast. Voor iedere wijziging is eerst een
  inactieve kopie gemaakt: respectievelijk snippets 210, 211 en 212.
- Snippet 215 uitgebreid naar versie 1.1 voor de Rank Math-sitemapindex, met
  inactieve herstelkopie 221. Zowel `/beroepengids/opleidingen/` als de
  taxonomy-sitemap met alle 29 termen staan nu in de sitemapindex.
- De kaart op Beroepengids-pagina 24266 aangepast naar **Opleidingen**, met de
  juiste link, taxonomielus, introverwijzing en interne tekstlink. De
  oorspronkelijke Elementor-data is bewaard in
  `audits/exports/page-24266-beroepengids-before-opleidingen-2026-10-18.json`.
- De parentarchieven bevatten nu navigatielijsten naar hun directe kinderen.
  De Rank Math-titelformule voor opleidingstermen is ingesteld op
  `%term% voor field service | Beroepengids`.
- Basisarchief, parentterm, kindterm, relaties, vacatureknop, sitemap en
  Beroepengidsingang functioneel gecontroleerd. Daarna is de LiteSpeed-cache
  volledig opgeschoond. Onderhoudsmodus en de tijdelijke algemene noindex zijn
  bewust niet gewijzigd.
- Nog open: de oude basis-URL redirecten, de oude testterm gecontroleerd
  opruimen, beslissen over plaatsing van de lange termdescription onderaan en
  de generieke vacaturesecties op parentarchieven conditioneren.

## 2026-10-18 — Pods- en snippetimpact voor Opleidingen vastgesteld

- Het Beroepengidsmodel verduidelijkt: één hiërarchische Pods-taxonomie
  `beroep_opleiding`, met `Opleidingsniveau` en `Studierichting` als
  parenttermen en de 4 + 23 canonieke waarden als kindtermen.
- Vastgelegd dat er geen gewone Beroepengidspagina komt;
  `/beroepengids/opleidingen/` wordt een virtueel taxonomy-basearchief zoals
  functie-inhoud en bedrijfssectoren.
- Alleen-lezen vastgesteld dat de bestaande Pods-velden
  `archive_beroep_opleiding_intro` en `archive_beroep_opleiding_description`
  door snippet 202 automatisch passen op de interne naam
  `beroep_opleiding`.
- De snippetimpact per onderdeel vastgelegd. Verplicht zijn een kleine
  allowlistwijziging in snippet 113 en een SEO-mapping in snippet 209.
  Snippet 62 krijgt waarschijnlijk een directe-kinderenquery. De generieke
  relatie-, termveld- en vacatureknopsnippets kunnen ongewijzigd blijven.
- Vastgelegd dat snippet 131 niet voor Opleidingen moet worden gebruikt,
  omdat deze één doeltaxonomie per basisarchief verwacht terwijl Opleidingen
  twee gescheiden vacaturetaxonomieën spiegelt.
- Sitemap-, Elementor-, URL- en redirectimpact plus een veilige
  uitvoeringsvolgorde gedocumenteerd in
  `audits/2026-10-18-beroepengids-opleidingen-pods-snippet-impact.md`.
- Er zijn nog geen live Pods, termen, snippets, templates of redirects
  gewijzigd.

## 2026-10-18 — Descriptions voor opleidingen voorbereid

- Een vaste redactionele productieprompt toegevoegd voor unieke,
  vraaggerichte en SEO-sterke Beroepengidsteksten over studierichtingen en
  opleidingsniveaus.
- Voor alle 23 goedgekeurde studierichtingen een inhoudelijk unieke description
  geschreven over leerinhoud, toepassing in field service, passende functies
  en relevante verdieping.
- Voor VMBO / LBO, MBO, HBO en WO / Universiteit vier afzonderlijke
  niveaudescriptions geschreven. VMBO wordt correct als voorbereiding op
  beroepsonderwijs behandeld; Associate degree wordt binnen het TFSC-filter
  onder HBO geplaatst zonder het gelijk te stellen aan een hbo-bachelor.
- Gecontroleerd dat alle 27 teksten vier vraaggerichte H2-koppen bevatten en
  tussen 450 en 486 woorden tellen, exclusief koppen en redactionele notities.
- Bij de vier niveaus is een concrete TFSC-route naar aantoonbaar gekoppelde
  vacatures opgenomen. Voor studierichtingen is bewust nog geen vacatureclaim
  toegevoegd, omdat de WordPress-termen nog nul vacaturekoppelingen hebben.
- Vastgelegd dat een dynamische TFSC-CTA alleen gekoppelde, actuele vacatures
  mag tonen. De teksten zijn nog niet gepubliceerd: eerst moet de
  hiërarchische Beroepengids-Pod `beroep_opleiding` met beide relatietakken
  worden ingericht en getest.

## 2026-10-18 — Opleidingsniveaus en studierichtingen gesynchroniseerd

- In WordPress de zichtbare niveaunamen aangepast naar `VMBO / LBO` en
  `WO / Universiteit`, met behoud van de bestaande slugs, URL's en alle
  vacaturekoppelingen.
- De vijf goedgekeurde studierichtingen aan de vacaturetaxonomie toegevoegd:
  Industriële Automatisering, Civiele Techniek, Mobiele
  Werktuigentechniek, Koude- en Klimaattechniek en Operationele Techniek.
- Supabase gelijkgetrokken naar vier actieve niveaus en 23 actieve
  studierichtingen. Associate degree is zonder dataverlies gedeactiveerd als
  zelfstandig filter, omdat het record geen kandidaat- of vacaturekoppelingen
  had en binnen TFSC onder HBO valt.
- Vier opleidingsniveau- en 23 studierichtingmappings van WordPress-term-ID
  naar Supabase-UUID vastgelegd in `source_term_mappings`.
- Eindcontrole bevestigt dat WordPress en Supabase exact dezelfde namen en
  aantallen bevatten. De bestaande Appsmith-functies voor zowel kandidaten als
  vacatures leveren automatisch dezelfde 4 niveau- en 23
  studierichtingopties.
- Appsmith-schermen en Pods zijn nog niet gewijzigd. Appsmith wordt hierna aan
  beide kanten Nederlandstalig getest. Voor de Beroepengids is inmiddels één
  hiërarchische taxonomie `beroep_opleiding` vastgesteld, met
  `Opleidingsniveau` en `Studierichting` als twee afzonderlijke termtakken en
  zonder gewone pagina `Opleidingen`.

## 2026-10-18 — Opleidingenketen WordPress, Supabase en Appsmith onderzocht

- Alleen-lezen vastgesteld dat WordPress bij vacatures al de twee benodigde
  taxonomieën heeft: `opleidingsniveau` met vier gebruikte termen en
  `vac_studierichting` met achttien nog ongebruikte termen.
- De WordPress- en Supabase-lijsten vergeleken. Opleidingsniveau heeft twee
  directe matches, twee naam-/synoniemmatches en één Supabase-only niveau
  (`Associate degree (Ad)`). Bij studierichtingen zijn vijftien namen exact,
  is één naam een duidelijke variant en heeft elk systeem twee eigen termen.
- Bevestigd dat Supabase het juiste gescheiden model al bevat via
  `education_levels`, `education_fields`, `vacancy_education_requirements` en
  `candidate_education`. De productievulling en stabiele WordPress-mappings
  ontbreken nog.
- Bevestigd dat Appsmith beide dimensies al afzonderlijk kan selecteren,
  opslaan en gebruiken bij vacaturematching. Nieuwbouw is niet nodig; de UI,
  termmapping, WordPress-vacaturekoppeling, synchronisatie en tests moeten
  worden afgemaakt.
- Als voorstel vastgelegd: `Opleidingen` wordt een overkoepelende
  Beroepengidsingang, terwijl opleidingsniveau en studierichting aan de
  datakant technisch gescheiden blijven. Dit is later verduidelijkt als één
  hiërarchische Beroepengids-taxonomie met twee termtakken, zonder gewone
  pagina. Er was op dit moment nog niets gewijzigd in WordPress, Supabase of
  Appsmith. De volledige audit staat in
  `audits/2026-10-18-education-chain-wordpress-supabase-appsmith.md`.
- Daarna inhoudelijk besloten dat de publieke niveaufilters `VMBO / LBO`,
  `MBO`, `HBO` en `WO / Universiteit` worden. Associate degree (Ad) krijgt
  geen eigen publiek filter maar valt voor TFSC onder HBO, terwijl de precieze
  diplomanaam als ruwe kandidaatwaarde kan worden behouden.
- Besloten de brede studierichting `ICT & Technische Informatica` te behouden.
  `Mobiele werktuigentechniek` is bevestigd als geldige studierichting, met de
  officiële en gangbare opleidingsnamen als synoniemen. Deze besluiten zijn
  alleen gedocumenteerd; er is nog geen data gewijzigd.

## 2026-10-17 — Alle overige vacaturetaxonomiekoppelingen gecontroleerd

- Een alleen-lezen koppelingenaudit uitgevoerd voor alle 63 gepubliceerde
  vacatures en alle tien taxonomieën die zowel in de oude live-export als op de
  huidige live-site bestaan. Er ontbreken geen vacature-slugs en er zijn geen
  extra slugs.
- Na normalisatie van drie bekende termcorrecties (`high-tech` → `hightech`,
  `service-monteur` → `servicemonteur` en `heereveen` → `heerenveen`) zijn
  bedrijfssector, dagen internationaal, dienstverband, opleidingsniveau,
  vacaturestatus en werkgebied volledig gelijk aan de oude live-data.
- De resterende inhoudelijke verschillen zijn beperkt tot vacature 23277 op
  vier taxonomieën, twee koelmonteurvacatures met minder brede functienamen en
  zes inhoudelijk passende nieuwe koppelingen aan kennisgebied
  `rotating-equipment`. Deze zijn niet automatisch aangepast.
- Vastgesteld dat de huidige functienaamtaxonomie zowel `servicemonteur`
  (term-ID 230, 16 vacatures) als `service-monteur` (term-ID 420, 1 vacature)
  bevat. Consolidatie vereist later een expliciet SEO- en relatieplan.
- De later toegevoegde taxonomieën `reisprofiel` en `vac_studierichting`
  bevatten respectievelijk 25 en 18 termen, maar nog geen enkele koppeling aan
  de 63 vacatures. Dit blijft onderdeel van de bestaande WordPress-, Supabase-,
  Appsmith- en Beroepengids-TODO.
- De volledige bevindingen en besluitpunten zijn vastgelegd in
  `audits/2026-10-17-live-vacancy-taxonomy-connections.md`. Er zijn tijdens deze
  audit geen gegevens in WordPress of Supabase gewijzigd.

## 2026-10-17 — Opleidingsniveaus van 63 live vacatures hersteld

- Vastgesteld dat de huidige vacaturetaxonomie `opleidingsniveau` tijdens een
  eerdere migratie vrijwel al haar termkoppelingen had verloren: 26 vacatures
  waren leeg en 33 hadden uitsluitend `LBO`; slechts 2 van de 63 gepubliceerde
  vacatures kwamen exact overeen met de oude live-data.
- De Updraft-databasekopieën van 16 en 17 oktober bleken aan
  `https://dev.thefieldservicecompany.nl` te behoren en zijn daarom niet als
  oude live-bron gebruikt. De autoritatieve bron was de volledige WordPress-
  export `vacatures-all.xml` van de oude live-site, gemaakt op 16 oktober 2026
  om 22:13 UTC.
- De 63 gepubliceerde vacatures uit die export zijn op slug vergeleken met de
  63 huidige live-vacatures. Er ontbrak geen enkele slug en er was geen extra
  live-slug. De oude taxonomie `opleiding` is één-op-één gemapt naar de huidige
  taxonomie `opleidingsniveau`: HBO `69`, MBO `70`, Universiteit `71` en LBO
  `210`.
- Na een verse voorcontrole via de WordPress REST API zijn uitsluitend de
  `opleidingsniveau`-term-ID's van de 61 afwijkende vacatures bijgewerkt. Titels,
  inhoud, publicatiestatus en alle andere taxonomieën zijn niet meegestuurd of
  gewijzigd. De 2 reeds juiste vacatures zijn overgeslagen.
- Een onafhankelijke REST-export na afloop bevestigde 63 verwachte en 63
  aanwezige vacatures, 0 ontbrekende slugs, 0 extra slugs, 0 termverschillen en
  0 vacatures zonder opleidingsniveau.
- De functionele hertest van het gepubliceerde Beroepengidsartikel `Wat is een
  Field Service Engineer?` toont onder `Opleidingsniveau` nu correct HBO, LBO,
  MBO en Universiteit. Dit bevestigt tevens dat de snippets de vacaturedata
  correct aggregeren en dat geen handmatige opleidingsrelatie op het artikel
  nodig is.
- QUIC.cloud blokkeerde de terminal-REST-aanroepen met HTTP 403. De herstel- en
  controleaanroepen zijn daarom rechtstreeks naar de eigen Cloud86-oorsprong
  gestuurd, met behoud van HTTPS-hostnaam en certificaatcontrole. QUIC.cloud is
  niet uitgeschakeld.
- Voor de herstelactie is een nieuwe toepassingssleutel van gebruiker
  `codex-rest-audit` gemaakt. Een bij de eerste lokale overdracht ontstane maar
  nooit gebruikte sleutel is direct daarna ingetrokken; de werkende sleutel
  blijft volgens afspraak lokaal en buiten de repository bewaard.

## 2026-10-17 — Eerste blogartikel naar Beroepengids gemigreerd

- Van het bestaande blogartikel `Wat is een Field Service Engineer? Lees alles
  over deze geweldige baan!` een nieuw Beroepengids-concept gemaakt met post-ID
  `27533`. Het oude blogartikel is niet gewijzigd en er is nog geen redirect
  aangemaakt.
- De nieuwe slug is `wat-is-een-field-service-engineer`; auteur Rudi van Vliet,
  oorspronkelijke publicatiedatum 10 januari 2025, samenvatting en uitgelichte
  afbeelding zijn overgenomen.
- Alleen de bestaande Beroepengids-taxonomieterm `field service engineer`
  (`beroep_functienaam`, term-ID `437`) is gekoppeld. Andere taxonomieën zijn
  leeg gebleven.
- De Rank Math-instellingen zijn overgenomen: SEO-title met `%currentyear%`, de
  twee bestaande focuszoekwoorden en de pillar-contentmarkering. De
  metabeschrijving blijft, net als bij de bron, gebaseerd op de samenvatting.
- Het verouderde Rank Math FAQ-blok is verwijderd. Zeven unieke vragen staan nu
  als gewone H3-vragen met antwoorden onder de H2 `Veelgestelde vragen over de
  functie field service engineer`; één inhoudelijk dubbele vraag is weggelaten.
- De zichtbare introductie blijkt uit het Pods-veld `introduction` te komen en
  niet uit de standaard WordPress-samenvatting. De vacatureknop wordt normaal
  automatisch uit de gekozen `relationship_*`-velden opgebouwd;
  `relationship_vacature_button_url` is alleen een expliciete override.
- Het artikel is na inhoudelijke controle gepubliceerd op
  `/beroepengids/artikel/wat-is-een-field-service-engineer/`. De bestaande 301
  van `/blog/wat-is-een-field-service-engineer/` is rechtstreeks naar deze URL
  omgezet en voor de lange voormalige blog-URL is eveneens een directe 301
  toegevoegd. Beide regels zijn met de Rank Math-debugger gecontroleerd.
- Het oude blogartikel met post-ID `19232` is na expliciete goedkeuring naar de
  WordPress-prullenbak verplaatst en blijft daardoor herstelbaar.
- In het dynamische blok met vacatureonderwerpen verschijnt alleen `LBO`. Dit
  komt uit de taxonomie van de gekoppelde open vacatures, niet uit het
  Beroepengidsartikel. Gecontroleerde vacatures noemen inhoudelijk MBO/HBO maar
  zijn momenteel uitsluitend aan `LBO` gekoppeld; correctie van de bestaande
  vacaturedata blijft daarom een afzonderlijke TODO.

## 2026-10-17 — Beroepengids: taxonomie Arbeidsvoorwaarden als TODO

- Vastgelegd dat een Beroepengidsartikel meerdere taxonomische ingangen kan
  krijgen, bijvoorbeeld zowel de functienaam `field service engineer` als de
  taxonomie `Arbeidsvoorwaarden` met de term `Salaris`.
- De gekozen richting is een custom taxonomie `Arbeidsvoorwaarden`, met later
  onder meer salaris, secundaire arbeidsvoorwaarden, toeslagen, vergoedingen,
  bonus, pensioen, reisuren en overwerk en verblijfskosten of dagvergoedingen.
- Vastgelegd dat Supabase hiervoor één canonieke termenlaag met WordPress-
  mappings krijgt, terwijl concrete salaris- en toeslagbedragen gestructureerde
  velden blijven. Appsmith moet later op beide onderdelen aansluiten.
- Besloten de custom taxonomie nu nog niet aan te maken, omdat aan de
  vacaturezijde nog geen overeenkomstige taxonomie of definitieve relatie
  bestaat. Eerst wordt de gedeelde WordPress-, Supabase- en Appsmith-keten
  ontworpen; daarna worden de bestaande Beroepengids-taxonomieën als
  instellingenvoorbeeld gebruikt.
- Er zijn hiervoor nog geen wijzigingen in WordPress, Pods, snippets,
  taxonomietermen of URL-structuur uitgevoerd.

## 2026-10-17

- Eerste dev-WordPress-audit vastgelegd.
- Eigen actieve plugins en Code Snippets uit dev geëxporteerd.
- Elementor Custom Code geïnventariseerd; EngageBay-identificatoren bewust niet opgenomen.
- Pluginmanifest van dev toegevoegd.
- Ontwerp voor een duurzame CRM-outbox en testplan toegevoegd.
- Op LIVE gecontroleerd dat het werkgeverscontactformulier inzendingen lokaal
  in Elementor opslaat.
- Privacytoestemming in de zes actieve Elementor-formulieren verplicht gemaakt
  en standaard uitgevinkt; bestaande formulier-CSS ongewijzigd gelaten.
- Zes actieve formulierpaden end-to-end getest; lokale Elementor-opslag en alle
  ingestelde acties rapporteerden succes.
- De ontbrekende expliciete AFI-actie bij het employer-contactformulier
  toegevoegd met integratie-ID 7 en succesvol hertest: Elementor `5/5
  Geslaagd`, EngageBay contact- en bedrijfsaanmaak HTTP 200.
- De LIVE vacaturezoeker op desktop en mobiel getest: filters, locatie-kruis,
  combinaties, reset, Meer/Minder, filtergroepen, mobiele filterchips,
  paginering en detailnavigatie werkten zonder functioneel defect.
- De SEO-migratiecontrole uitgevoerd: WordPress/Rank Math-instellingen,
  robots, sitemapstructuur, metadata, schema, content-URL's en 199 actieve
  omleidingen geïnventariseerd. De twee gevonden omleidingsfouten zijn in Rank
  Math gecorrigeerd, de gerichte LiteSpeed-cache is geleegd en beide oude URL's
  zijn anoniem als correcte 301 gecontroleerd. Tijdelijke onderhouds- en
  indexeringsblokkades zijn bewust nog niet verwijderd.
- Een alleen-lezen content- en taxonomie-inventaris uitgevoerd voor LIVE
  WordPress en Supabase. De 18 bestaande blogs, 19 relevante WordPress-
  taxonomieën, 256 WordPress-termen en 250 Supabase-vocabulaire-items zijn in
  een Google Sheet vastgelegd en vergeleken. Voor Beroepengids, Kennisbank en
  de 12 vacaturetaxonomieën is een gefaseerd voorstel gedocumenteerd in
  `content/taxonomy-and-content-inventory.md`. Er zijn hierbij geen gegevens in
  WordPress of Supabase gewijzigd.
- Alle geëxporteerde PHP-snippets, eigen plugins en Elementor Custom Code
  functioneel doorgelicht en beschreven in `snippets/README.md`, inclusief
  hooks, Query ID's, afhankelijkheden, bekende defecten en inactieve code.
  Daarbij is vastgelegd dat de huidige SNIP-integratie Google Jobs-locaties uit
  WordPress-termmeta opbouwt, maar bedrijfsadres, vacaturelocatie en woon-/
  wervingsregio nog niet als afzonderlijke relaties modelleert.
- De afgesproken content-, taxonomie- en locatielogica vastgelegd in
  `functionality/content-taxonomy-logic.md`: alle WordPress-plaatsen en
  postcodes moeten naar de canonieke geografielaag, bestaande land- en
  provinciekoppelingen blijven behouden, Beroepengids spiegelt de vacature-
  taxonomieën en Blog wordt alleen binnen WordPress herplaatst.
- Besluit aangescherpt: `dagen-internationaal` blijft een gedeelde canonieke
  termenlijst voor vacatures én kandidaten; bestaande data wordt later onder
  de juiste termen ingedeeld. De huidige regio's en grotere plaatsen zijn voor
  nu vastgesteld, met een TODO om aanvullende plaatsen en postcodes later te
  beoordelen.
- Eerste ondubbelzinnige snippetcorrecties in de repository voorbereid: de
  adviesknop gebruikt niet langer een hardcoded dev-domein en encodeert
  parameters veilig, de Beroepengids-CPT-naam is gecorrigeerd, formulieruploads
  lezen alleen bestaande bestanden, de zoekterm wordt escaped, ongerichte
  JavaScript-preloads zijn verwijderd en twee CSS-syntaxfouten zijn hersteld.
  Deze correcties zijn nog niet naar WordPress uitgerold.
- TODO toegevoegd om de uitzonderlijk grote WordPress-installatie en back-ups
  te analyseren en te verkleinen. De analyse omvat onder meer uploads,
  afgeleide en ongebruikte media, cache- en back-upmappen, migratiebestanden,
  logs, revisies, transients, grote tabellen en verweesde Elementor-/Pods-data.
  Opruimen gebeurt pas na inventarisatie, back-up en expliciete goedkeuring.
- TODO toegevoegd om alle formuliermeldingen naar begrijpelijk Nederlands om
  te zetten. De verplichte privacy-checkbox krijgt een eigen, concrete melding
  in plaats van een algemene Engelse validatiefout.
- De drie actieve LIVE-snippets achter de virtuele vacaturearchieven en Rank
  Math-combositemap (148, 186 en 193) geëxporteerd met behoud van alle uitleg en
  functioneel gecontroleerd. De sitemap bevat momenteel 15.836 URL's verdeeld
  over 32 bestanden: 622 combinaties met twee en 15.214 met drie taxonomieën;
  iedere URL bevat `functienaam`. De aanduiding “fase 1” vormt dus geen kleine
  publicatielimiet. Ook vastgesteld dat de geteste noindex- en Search &
  Filter-URL's geen canonical-link renderen, ondanks de beschreven canonical
  voor filter-URL's.
- De volledige LIVE-snippetstatus vergeleken. De inactieve snippets zijn
  hoofdzakelijk oudere versies en klonen van de 26 actieve opvolgers; debug- en
  testsnippets 185 en 197 staan eveneens terecht uit. Voorlopig blijven alle
  huidige actieve snippets aan en alle voorgangers uit.
- Alle 47 LIVE-snippets, inclusief 21 inactieve voorgangers en tests, als
  afzonderlijke leesbare bestanden geëxporteerd naar
  `snippets/exports/live/all-2026-10-17/`. Daarmee blijven alle handleidingen
  en historische codevergelijkingen beschikbaar zonder WordPress te wijzigen.
- De eerder vastgelegde SEO-keuze voor oorspronkelijke vacature-
  taxonomiearchieven teruggevonden in de opeenvolgende snippets 146, 150, 187
  en 193 en op LIVE gecontroleerd: native archieven blijven bereikbaar en
  `index, follow`, met canonical naar de passende vacature-combo. Functienaam
  en locatie werken één-op-één; andere taxonomieën gebruiken in de huidige code
  `field-service-engineer` als vaste functienaamcontext.
- De Beroepengids-archiefketen op LIVE getest. Taxonomie-base- en termarchief
  laden met de juiste templates en context, waardoor de betrokken snippets
  actief moeten blijven. Het CPT-archief mist door de oude CPT-naam in snippet
  49 de extra contextclass. Verder toont het functienaam-base-archief zichtbaar
  de ruwe veldnaam `archive_beroep_functienaam_description` en gebruikt het nog
  een generieke Rank Math-titel; beide zijn als gerichte herstelpunten
  vastgelegd.
- Snippet 49 op LIVE gecorrigeerd van CPT-naam `beroep` naar `beroepengids`.
  WordPress bevestigde de update. De functionele hertest toont nu
  `tfsc-archive-beroepen` op `/beroepengids/artikelen/`, terwijl
  `tfsc-archive-taxonomy` op het functienaam-base-archief behouden bleef. De
  bijgewerkte LIVE-export staat onder
  `snippets/exports/live/applied-2026-10-17/`.
- De zichtbare ruwe veldnaam op het functienaam-base-archief herleid tot de
  centrale Pods-inhoud. Elementor gebruikt correct de dynamische tag
  `description`, maar zes description-velden bevatten nog letterlijk hun eigen
  veldnaam en twee introvelden bevatten testtekst. Verder wijkt het Pods-
  naamdeel `opleiding` af van de echte taxonomie `beroep_opleidingsniveau`, en
  staat `beroep_locatie` wel in de snippet-allowlist maar niet in het huidige
  Beroepengids-beheer.
- Na expliciete goedkeuring uitsluitend de acht tijdelijke LIVE-Pods-waarden
  leeggemaakt: zes description-placeholders plus `test bedrijfssector intro`
  en `intro functienaam`. WordPress bevestigde de opslag. De echte
  Beroepengids-CPT-teksten en de twee kennisbankwaarden zijn ongewijzigd
  gebleven. De openbare base-archieven voor functienaam, functie-inhoud,
  opleidingsniveau en werkgebied zijn daarna opnieuw gecontroleerd en tonen
  geen ruwe veldnamen of testteksten meer. De generieke Rank Math-titel
  `Archive • The Field Service Company` blijft een afzonderlijk herstelpunt.
- Het inhouds- en SEO-model voor de zes Beroepengids-base-archieven vastgelegd.
  De gids bedient primair informatieve loopbaanoriëntatie en leidt daarna door
  naar gerelateerde actuele vacatures, zodat hij niet concurreert met de
  vacaturearchieven. Per pagina komen een korte intro bovenaan, unieke
  verdiepende tekst onderaan, afzonderlijke SEO-metadata en expliciete relaties
  tussen beroep, werkzaamheden, kennis, opleiding, sector en werkgebied. Ook de
  gekozen werktitels en de uitgangspunten voor vindbaarheid in generatieve
  zoekfuncties zijn vastgelegd in `functionality/content-taxonomy-logic.md`.
- De eerste complete voorbeeldpagina voor het archief `Functienamen` opgesteld
  in `content/drafts/beroepengids-functienamen-voorbeeld.md`: SEO-title, unieke
  meta description, korte intro, concrete verdiepende ondertekst, natuurlijke
  gebruikersvragen, interne-linkplan en vertrouwens-/AI-signalen. De zichtbare
  concepttekst is na inhoudelijke feedback aangescherpt tot een energieke
  archiefwegwijzer van 61 woorden intro en 379 woorden verdieping. Het
  vergelijkingsblok tussen Field Service Engineer en servicemonteur is
  verwijderd; doel, navigatie, passende functie vinden en bedrijfssectoren
  staan nu centraal. De tekst gebruikt consequent `jij` en `jouw`, schrijft
  functienamen in lopende zinnen met kleine letters en omvat naast service en
  commissioning ook site supervision en site management.
- Na expliciete opdracht de goedgekeurde intro en description in de centrale
  LIVE-Pods-velden voor `beroep_functienaam` geplaatst. WordPress bevestigde de
  opslag en beide waarden zijn exact teruggelezen. De publieke hertest toont
  één H1 `Functienamen`, de nieuwe intro, vier inhoudelijke H2-vragen en een
  correcte HTML-lijst zonder zichtbare Markdowntekens. SEO-title en meta
  description zijn in deze stap bewust nog niet technisch toegepast.
- De tweede voorbeeldpagina voorbereid voor het archief `Functie-inhoud` in
  `content/drafts/beroepengids-functie-inhoud-voorbeeld.md`. Het concept bevat
  een intro van 53 woorden en een concrete description van 399 woorden. De
  pagina helpt bezoekers vanuit het soort werk dat zij graag doen navigeren
  naar functienamen, kennis, opleidingen, branches en vacatures. Het huidige
  LIVE-archief toont alleen de term `inbedrijfstellen`.
- Na expliciete opdracht de intro en description voor `Functie-inhoud` in de
  centrale LIVE-Pods-velden geplaatst. WordPress bevestigde de opslag en beide
  waarden zijn exact teruggelezen. De publieke hertest toont één H1
  `Functie-inhoud`, de intro, vier inhoudelijke H2-secties en een correcte
  HTML-lijst zonder zichtbare Markdowntekens. De bestaande templatekop
  `Vacatures op functienaam gerelateerd aan functie-inhoud` is als afzonderlijk
  tekstherstelpunt vastgelegd en niet stilzwijgend aangepast. SEO-title en meta
  description zijn nog niet technisch toegepast.
- De derde voorbeeldpagina voor het archief `Kennisgebieden` is op 17 oktober
  2026 op LIVE geplaatst vanuit de centrale Pods-archiefinstellingen. De pagina
  bevat een intro van 50 woorden en een concrete description van 413 woorden.
  De publieke controle bevestigt één H1 `Kennisgebieden`, vijf inhoudelijke
  H2-koppen, een echte HTML-lijst en geen Markdown-restanten. Het archief toont
  nog geen termen; daarom blijven de genoemde vakgebieden voorbeelden totdat
  de canonieke lijst is goedgekeurd. De bestaande templatekop
  `Vacatures op functienaam gerelateerd aan kennisgebied` blijft als afzonderlijk
  tekstherstelpunt staan. SEO-title en meta description zijn nog niet technisch
  toegepast.
- De vierde voorbeeldpagina voorbereid voor het archief `Opleidingsniveaus` in
  `content/drafts/beroepengids-opleidingsniveaus-voorbeeld.md`. De tekst leidt
  bezoekers vanuit opleiding, werk- en denkniveau en praktijkervaring naar
  passende beroepen, ontwikkelroutes en vacatures. Het LIVE-archief toont nog
  alleen de testterm `Opleiding A`; die is bewust niet gewijzigd. De definitieve
  niveaus en de relatie met studierichtingen worden later gezamenlijk bepaald.
- TODO vastgelegd voor opleidingsrichtingen als één later traject over
  WordPress-vacatures, Supabase en Appsmith. De huidige Beroepengids-uitwerking
  blijft voorlopig beperkt tot `beroep_opleidingsniveau`; er zijn nu geen
  opleidingsrichtingen, mappings of databasevelden gewijzigd. Ook vastgelegd
  dat de eventuele overgang naar een overkoepelende Beroepengids-taxonomie
  `Opleidingen` pas daarna plaatsvindt, inclusief migratie of behoud van
  bestaande URL's, relaties, snippets, sitemap en SEO.
- De vijfde Beroepengids-archieftekst voorbereid voor `Bedrijfssectoren` in
  `content/drafts/beroepengids-bedrijfssectoren-voorbeeld.md`. De tekst helpt
  bezoekers sectoren te vergelijken op machines, installaties, werkomgeving,
  functies en vacatures. De lege LIVE-taxonomy is bewust nog niet met termen
  gevuld; die lijst wordt later afgestemd met vacatures en Supabase.
- Na expliciete goedkeuring de intro en description van het LIVE-archief
  `Bedrijfssectoren` gevuld. De openbare pagina is gecontroleerd: H1, intro,
  vraaggerichte H2-koppen, alinea's en opsomming worden correct weergegeven.
  Er zijn geen taxonomy-termen, Pods-definities of relaties gewijzigd.
- De zesde Beroepengids-archieftekst voorbereid voor `Werkgebieden` in
  `content/drafts/beroepengids-werkgebieden-voorbeeld.md`. De tekst maakt
  onderscheid tussen werkgebied, standplaats, vacaturelocatie en woon- of
  wervingsregio en koppelt het onderwerp aan reisprofiel, dagen internationaal,
  fieldservicefuncties en vacatures. De bestaande testterm `World` is bewust
  niet gewijzigd.
- Na expliciete goedkeuring de intro en description van het LIVE-archief
  `Werkgebieden` gepubliceerd. De openbare weergave is gecontroleerd; de
  bestaande testterm `World`, taxonomie en relaties zijn niet gewijzigd.
- De hoofdpage `Beroepengids` voorzien van een nieuwe vraaggerichte intro en
  description. De tekst helpt bezoekers vanuit functienamen, werkzaamheden,
  kennisgebieden, bedrijfssectoren, opleidingsniveaus en werkgebieden naar een
  passende fieldservicefunctie en actuele vacatures. Koppen en interne links
  zijn na publicatie op LIVE gecontroleerd.
- Het hoofdarchief `Kennisbank` voorzien van een nieuwe intro en description
  voor professionals, zij-instromers en werkgevers. De tekst positioneert de
  site als praktisch kennisplatform voor de fieldservicesector en koppelt door
  naar de Beroepengids en vacatures. De Pods-instellingen, koppen en interne
  links zijn na publicatie op LIVE gecontroleerd.
- De taxonomy-TODO aangescherpt: `Studierichtingen` (`vac_studierichting`) en
  `Reisprofielen` (`reisprofiel`) bestaan al bij vacatures. Later worden de
  termen en vacaturekoppelingen gecontroleerd en worden passende
  Beroepengids-spiegels ontworpen. Ook vastgelegd dat vacature-`werkgebied` nog
  moet worden afgewerkt en dat `opleidingsniveau` bij vacatures en
  Beroepengids nog vanuit één canonieke termenlijst moet worden gevuld.
- De blogmigratie als gecontroleerde TODO vastgelegd: per artikel bestemming,
  actualisatie, slug, taxonomieën en metadata bepalen, daarna verplaatsen en in
  Rank Math een permanente 301-redirect van de oude naar de nieuwe URL maken.
  Oude en nieuwe URL, canonical, sitemap en interne links worden per artikel
  getest voordat het als afgerond geldt.
- De huidige SEO-metadata van Beroepengids, Kennisbank en de zes virtuele
  Beroepengids-basearchieven gecontroleerd. De hoofdpagina heeft nog de
  description `Home`, het Kennisbank-archief gebruikt generieke archieftekst en
  alle zes basearchieven tonen `Archive • The Field Service Company` als title
  en description.
- Een strikt afgebakende Rank Math-oplossing als LIVE-snippet 209 opgeslagen,
  maar nog niet geactiveerd. Deze bevat unieke title en description voor exact
  de acht bedoelde pagina's en laat canonicals, robots, sitemaps,
  vacaturearchieven, gewone termarchieven en zichtbare content ongemoeid.
- Na expliciete goedkeuring LIVE-snippet 209 geactiveerd. Beroepengids,
  Kennisbank en alle zes virtuele Beroepengids-basearchieven geven nu de
  bedoelde unieke HTML-title, meta description, Open Graph-title/-description
  en Twitter-title/-description terug. Een vacaturearchief en een gewoon
  Beroepengids-termarchief behielden hun bestaande metadata. De nog lege
  canonicals en `noindex, nofollow` zijn afkomstig van de bewust actieve
  onderhouds- en zoekmachineblokkade en zijn niet door snippet 209 gewijzigd.

## 2026-09-28

- Basisstructuur van de repository voorbereid.
