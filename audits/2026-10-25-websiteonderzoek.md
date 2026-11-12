# Websiteonderzoek en groeiplan — The Field Service Company

**Datum:** 25 oktober 2026 · **Versie:** 1.0 · **Onderzochte site:** https://thefieldservicecompany.nl/

## 1. Het advies

TFSC heeft een bruikbare basis: een herkenbare specialisatie, persoonlijke bemiddeling, concrete technische vacatureteksten en een groeiende Beroepengids. De grootste kans is die expertise beter te vertalen naar vindbaarheid, vertrouwen en aanvragen. Een volledige herbouw is daarvoor nu niet de eerste investering.

Een professional zou eerst drie zaken op orde brengen: betrouwbare vacaturegegevens en formulieren, een scherpe route voor beide doelgroepen en een meetbaar groeiproces. Daarna kan AI de productie en het onderhoud versnellen.

De opdracht is nadrukkelijk groei aan beide kanten. Meer passende kandidaten én meer opdrachten van machinebouwers, uitbreiding van landelijke functies naast internationaal werk, betere vindbaarheid in zoekmachines en AI-systemen en minder handmatig websiteonderhoud. Het gewenste werkgeversprofiel is **machinebouwers met meer dan 100 medewerkers**. Technische servicebedrijven zijn geen primaire doelgroep. Naast nieuwe organisaties is uitbreiding binnen bestaande klantorganisaties een zelfstandige groeiroute.

De reputatie en eigen uitstraling blijven uitgangspunt. Het doel is een effectievere TFSC-site, geen kopie van een groot bureau. Met een beperkt budget en veel eigen technische kennis past een reeks gerichte verbeteringen beter dan een omvangrijk ontwikkelproject.

**Aanbevolen volgorde:**

1. Herstel vacaturecontext, salarisgegevens, locatiebetekenis en verkeerde canonical-verwijzingen.
2. Onderzoek de langetermijndaling en maak de commerciële funnel betrouwbaar meetbaar.
3. Verbeter vacaturekaarten, vacaturekoppen en de werkgeversroute.
4. Bouw een gerichte landelijke propositie en inhoud voor werkgevers met koopintentie.
5. Automatiseer controle, voorbereiding en onderhoud met n8n en AI.

## 2. Wat onderzocht is — en hoe zeker de conclusies zijn

Het onderzoek combineert actuele browserinspectie, anonieme HTTP-responses, HTML, JSON-LD, robots.txt, sitemaps, repositorycode en documentatie. Na toestemming zijn geaggregeerde gegevens in Metabase en de analytics-workflows in n8n alleen gelezen. Ook de relevante eerdere chat en de aangeleverde Search Console-afbeelding zijn betrokken.

De technische steekproef omvat de homepage, vacatureoverzicht en filterinteractie, twee vacatures en hun sollicitatieroute, werkgeverspagina en contactpop-up, Beroepengids en functiearchief, een locatiearchief, een gecombineerd vacaturearchief en een native sectorarchief met canonical-doel. De openbare sitemaps zijn afzonderlijk geteld. Publieke technische meetgegevens staan in [de bewijsbijlage](2026-10-25-websiteonderzoek-bewijs.json).

**Labels in dit rapport:** *vastgesteld* betekent actueel waargenomen; *hypothese* vraagt aanvullend bewijs; *advies* is een voorgestelde verbetering. De repository bevat historische exports en verwijzingen naar dev én LIVE. Documentatie uit die repo is dus geen automatisch bewijs van de huidige productieconfiguratie.

Beperkingen: er zijn geen formulieren verzonden, geen kandidaat- of klantdossiers ingezien en geen productie-instellingen gewijzigd. De browser was ingelogd; belangrijke metadata zijn daarom ook anoniem gecontroleerd. Er is geen volledige mobiele apparaattest, WCAG-audit, serverloganalyse, Google URL Inspection of Core Web Vitals-veldmeting uitgevoerd. De beschermde schema-REST-route gaf anoniem HTTP 401. Het rapport beweert niet dat die route publiek toegankelijk is.

Interne absolute bezoekcijfers en klantvoorbeelden staan uitsluitend in een aparte lokale privébijlage. De openbare versie bevat conclusies, openbare voorbeelden en het uitvoerplan.

## 3. De verkeersdaling is een echt strategisch probleem

**Vastgesteld:** de aangeleverde Search Console-grafiek toont een neergaande lijn over een lange periode. De opgeslagen Search Console-data ondersteunen dat. In juli–september 2026 waren er ongeveer **44% minder klikken** en **37% minder impressies** dan in november 2025–januari 2026. Beide blokken hebben 92 dagen. Dit is een beschrijvende vergelijking tussen verschillende seizoenen, geen zuivere jaar-op-jaarverklaring.

Ook de recentere vergelijking is zwakker: 1–22 oktober tegenover 1–22 september geeft ongeveer **15% minder Google-klikken**, bij circa **3% minder impressies**. Daardoor verdient zowel bereik als doorklikgedrag aandacht.

In de opgeslagen GA4-acquisitierijen is de sessiedaling veel groter. Ongeveer **89% van het absolute verschil** tussen die twee periodes zit in Direct. Eerdere Direct-pieken komen opvallend vaak terecht op diepe locatiearchiefpagina's en hebben daar geen key events. Dat is een onderzoekssignaal; het bewijst geen bots, warming, fraude of meetfout. Herkomst, interne bezoeken, attributie en eventuele geautomatiseerde bezoeken moeten met bronmetingen en logs worden onderscheiden.

Er is tegelijk een vroeg positief signaal rond de in de eerdere chat vastgelegde hernieuwde indexeerbaarheid op 19 oktober: 19–22 oktober heeft circa 22% meer impressies en 10% meer klikken dan dezelfde weekdagen een week eerder. Vier dagen zijn te kort om herstel of een oorzakelijk effect van de wijzigingen vast te stellen.

**Wat een professional nu zou doen:**

- Een tijdlijn maken van onderhoudsmodus, redirects, contentverplaatsingen, templates, tags en andere wijzigingen, naast dagelijkse Google-prestaties.
- Per URL-groep kijken: vacatures, functie-/regioarchieven, Beroepengids, Kennisbank, werkgeverspagina's en merkverkeer. Oude en nieuwe URL's bij migraties samen beoordelen.
- Verloren klikken splitsen in minder impressies, lagere positie en lagere CTR. Gemiddelde positie nooit los van de veranderde mix van zoektermen interpreteren.
- Nederland en België, mobiel en desktop en zoekintenties afzonderlijk analyseren. Algemene kennisvragen zijn een andere markt dan vacatures en werkgevers die willen inkopen.
- De geïmporteerde GA4-sessies vergelijken met een apart propertytotaal. De huidige workflow combineert onder meer `pageReferrer` met sessiedimensies; optellen over deze dimensies kan afwijken van unieke sessietotalen. Gebruikersaantallen over dagen of dimensies zijn evenmin simpel optelbaar.

**Conclusie:** herstel moet draaien om gekwalificeerd zoekverkeer en commerciële resultaten. Het terughalen van een oud totaalcijfer zonder de samenstelling te begrijpen is geen goed doel.

## 4. Bevindingen met de hoogste prioriteit

### F01 — Vacaturenummer verdwijnt op weg naar solliciteren

**Vastgesteld op vacatures 358 en 359.** De pagina toont `Vacature: 358` respectievelijk `Vacature: 359`. Na klikken op de sollicitatieknop bevat de formulier-URL een lege `vacaturenummer=`. Bij de eerste controle was ook het verborgen veld `vacancy_number` leeg. De zichtbare formulierkop noemt de gekozen functie niet.

De in de openbare HTML opgenomen code zoekt letterlijk naar `Vacaturenummer:`. Dat komt niet overeen met het huidige label. Dezelfde sollicitatiecode bouwt URL-parameters met tekstconcatenatie; bij vacature 359 staat een onbeschermde `&` in de titel. Ook de contractparameter blijft in deze twee routes leeg.

**Gevolg:** risico op ontbrekende of onbetrouwbare vacaturecontext. Dat de sollicitatie achteraf daadwerkelijk verloren gaat, is niet getest: er is niets verzonden.

**Advies:** geef een stabiele vacature-ID expliciet mee vanuit de template of het datamodel. Gebruik `URL`/`URLSearchParams`, valideer de ID aan de ontvangende kant en toon de functienaam in het formulier. Vertrouw niet op een presentatielabel om bedrijfslogica te vinden. Beoordeel de vraag- en adviesroute die vergelijkbare logica gebruikt mee.

**Klaar wanneer:** beide vacatures, een titel met `&` en een generieke adviesaanvraag correct openen; een gecontroleerde testinzending één juiste activiteit en vacaturekoppeling oplevert; foutieve ID's herkenbaar worden afgehandeld.

Bronnen: [vacature 358](https://thefieldservicecompany.nl/vacature/field-service-engineer-hvac-koeltechniek-internationaal-allround/), [vacature 359](https://thefieldservicecompany.nl/vacature/service-monteur-mechanisch-benelux-separatoren-decanters/).

### F02 — Zichtbaar salaris en JobPosting verschillen

**Vastgesteld:** vacature 359 noemt €3.600–€4.100 bruto per maand. Het JobPosting-object bevat €3.000–€4.100. In de twee onderzochte JobPosting-objecten ontbreekt `baseSalary.currency`. Beide noemen TFSC als `hiringOrganization`, terwijl de teksten werk bij een andere werkgever beschrijven.

**Advies:** laat kaarten, tekst en structured data dezelfde gecontroleerde salarisvelden gebruiken. Controleer de werkgevermapping; gebruik de daadwerkelijke werkgever of de passende vertrouwelijke variant wanneer die anoniem werft. Verzin geen salaris- of ervaringsgegevens om velden te vullen. Google's [JobPosting-documentatie](https://developers.google.com/search/docs/appearance/structured-data/job-posting) beschrijft de betekenis en consistentie van deze gegevens.

**Klaar wanneer:** salarisbedragen, valuta en werkgeverbetekenis kloppen op beide voorbeelden én een steekproef van andere vacatures; de Rich Results Test geen relevante fouten geeft. Een geslaagde test garandeert geen vertoning.

### F03 — Woonregio en werklocatie lopen door elkaar

**Vastgesteld:** vacatures 358 en 359 bevatten respectievelijk 46 en 30 `jobLocation`-objecten. Veel corresponderen met de getoonde woonregio's van kandidaten. Het Rotterdamse vacaturearchief bevat ook een functie voor wonen en werken in Australië. De titel van die landingspagina wekt ondertussen de verwachting van banen in Rotterdam.

**Advies:** modelleer afzonderlijk de standplaats, het reis-/servicegebied, de toegestane woonregio en eventueel verhuizing. Laat een regioselectie expliciet vertellen wat hij selecteert: banen *in* de regio of banen waarvoor kandidaten *uit* die regio kunnen reageren. Geef Google feitelijke werklocaties; reizen vanuit huis maakt field service niet automatisch een volledig remote functie. Zie [Google JobPosting](https://developers.google.com/search/docs/appearance/structured-data/job-posting).

**Klaar wanneer:** kandidaat, zoekfilter, vacaturetekst en Google dezelfde locatiebetekenis krijgen. Dit is ook een voorwaarde voor geloofwaardige uitbreiding in landelijke functies.

### F04 — Canonical verwijst naar een andere vacatureselectie

**Vastgesteld:** [het native Maritiem-archief](https://thefieldservicecompany.nl/bedrijfssector/maritiem/) verwijst met zijn canonical naar [Field Service Engineer + Maritiem](https://thefieldservicecompany.nl/vacatures/functienaam/field-service-engineer/bedrijfssector/maritiem/). De eerste resultaatpagina's verschillen: de native pagina bevat bijvoorbeeld Inbedrijfsteller Offshore Kranen, terwijl het doel een andere selectie toont. Dit is meer dan twee URL's voor exact hetzelfde overzicht.

**Advies:** leg per archieftype vast welke pagina zelfstandig waarde heeft en wanneer twee URL's werkelijk gelijkwaardig zijn. Gebruik canonicals voor dubbele of sterk gelijkende inhoud. Een generieke vaste functienaam toevoegen kan de betekenis vernauwen. Pas deze logica gericht aan, met een URL- en redirectinventaris; verwijder niet ineens alle bestaande archieven. Onderbouwing: [Google over canonicals](https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls).

**Klaar wanneer:** sector-, functie- en locatiearchieven een verklaarbare canonical hebben, interne links de gekozen routes volgen en relevante bestaande instroom behouden kan blijven.

### F05 — Twee meta descriptions op beide geteste vacatures

**Vastgesteld in anoniem opgehaalde HTML:** vacatures 358 en 359 hebben elk twee `meta[name="description"]`-tags. Bij 358 verschillen de teksten. De repository bevat bovendien een nog als niet uitgerold beschreven description-correctie; alleen die toevoegen lost dubbele uitvoer niet aantoonbaar op.

**Advies:** identificeer beide uitvoerbronnen en kies één eigenaar voor metadata. Controleer daarnaast Open Graph, Twitter en cache. Een meta description is een voorstel voor de snippet; Google kan een andere tekst kiezen.

**Klaar wanneer:** iedere geteste single vacature precies één bedoelde description heeft, ook anoniem na cacheopbouw.

## 5. Wat bezoekers direct beter moet helpen

### F06 — Maak vacatures vergelijkbaar voordat iemand doorklikt

De onderzochte lijstkaarten tonen vooral een titel en enthousiasmerende intro. Salaris en andere beslisinformatie staan dieper in de tekst. Voeg vaste kenmerken toe: salarisband, feitelijke standplaats/vertrekpunt, reisgebied, overnachtingen of reisfrequentie, uren indien bekend en dienstverband. Gebruik dezelfde velden bovenaan de vacature.

Een kandidaat moet snel kunnen beoordelen: kan ik dit werk, wil ik deze reisbelasting en past het aanbod bij mijn leven? Details over techniek en team mogen daaronder uitgebreid blijven.

**Pilot:** verbeter eerst tien relevante vacatures en één kaarttemplate. Meet kaart → vacature → formulier → geldige aanvraag → gekwalificeerde kandidaat. Extra klikken zonder geschiktere kandidaten zijn geen geslaagde verbetering.

### F07 — Maak de eerste keuze concreet

De [homepage](https://thefieldservicecompany.nl/) legt de nadruk op kennismaken en toont veel routes. Test bovenaan een duidelijke primaire actie naar passende banen en een tweede route naar persoonlijk advies. Geef werkgevers een herkenbare eigen ingang. Behoud de specialistische uitstraling en persoonlijke toon.

Voorgestelde inhoudsvolgorde: belofte en twee keuzes, compacte bewijslast, bruikbare vacatures, werkwijze, ervaringen en verdiepende kennis. Dit is een ontwerphypothese die met bezoekers moet worden getoetst.

### F08 — Geef werkgevers een beslispagina

De [werkgeverspagina](https://thefieldservicecompany.nl/voor-werkgevers/) heeft een relevante specialisatie, maar de intro noemt nog freelancers terwijl de verdere propositie sterk op directe indiensttreding is gericht. Ook ontbreken bovenaan een compacte samenvatting van de samenwerking en bewijs voor het bedoelde klantprofiel.

Maak duidelijk: voor welke machinebouwers TFSC werkt, welke servicefuncties worden ingevuld, hoe technisch en persoonlijk geselecteerd wordt, wat de opdrachtgever moet aanleveren en wat na een eerste gesprek gebeurt. Benoem commerciële voorwaarden op het niveau dat in de praktijk klopt. Maak de bestaande 70%-claim alleen prominent als definitie, periode en onderbouwing beschikbaar zijn.

Voeg twee controleerbare cases toe: probleem, beperkingen, aanpak, uitkomst. Een herkenbare beslisser en een heldere kennismakingsactie zijn belangrijker dan een lange lijst sectorlinks. Toets of sectorlinks vanuit deze pagina werkgevers helpen; momenteel komen ze op vacaturearchieven uit.

### F09 — Formulieren: context en kleine drempels

Het sollicitatieformulier heeft vijf verplichte persoonsgegevensvelden en optionele CV-/LinkedIn-invoer. Dat zonder CV reageren kan, is goed. Maak boven het formulier de vacature, contactpersoon en vervolgstap zichtbaar. Test een kortere eerste aanvraag met naam en één voorkeurscontactmethode; bepaal op basis van kandidaatkwaliteit welke aanvullende velden nodig blijven.

Gebruik een duidelijke verzendknop, begrijpelijke fouten, behoud van invoer bij fouten en een bevestiging die uitlegt wat er gaat gebeuren. Meet een **geaccepteerde aanvraag** na technische verwerking, niet alleen een knopklik. Houd sollicitatie, advies en werkgeversaanvraag als verschillende doelen bij. De werkgeverspop-up opent; verzending en de tweede stap zijn niet functioneel getest.

### F10 — Trek presentatie en toegankelijkheid gelijk

Er zijn verschillende ervaringsclaims: meer dan 20 jaar op de homepage en +15 jaar op de onderzochte vacatures en werkgeverspagina. Kies één onderbouwde definitie, bijvoorbeeld bedrijfservaring versus persoonlijke vakervaring, en beheer die centraal. Verifieer de reviewscore, toon bron en aantal en voorkom verouderde handmatige kopieën. De actuele twee vacatures tonen 4,9; een oudere zoekextractie met 4,6 is daarom niet als actuele fout overgenomen.

Op de primaire homepageknop is wit op `rgb(232,117,0)` gemeten: ongeveer **3,01:1** bij 16px vet. Dat blijft onder 4,5:1 voor deze tekstgrootte. Kies een toegankelijke kleurcombinatie. De [WCAG 2.2-referentie](https://www.w3.org/WAI/WCAG22/quickref/) is het toetskader; dit is geen verklaring over de toegankelijkheid van de hele site.

Controleer verder toetsenbordbediening, focus in pop-ups en filters, foutmeldingen, vergroting en kleine schermen. Geef gegenereerde labels een redactionele eindcontrole: bijvoorbeeld de huidige knoptekst “Alle Functienaam Field Service Engineer | Artikelen Vacatures” helpt de lezer onvoldoende.

## 6. SEO en landelijke groei

### F11 — Selecteer op zoekintentie en aanbod

De twee combo-sitemaps bevatten nu **934 unieke pagina-URL's**, allemaal met twee taxonomieën. De oude repositorydocumentatie noemt 800: die telling is inmiddels achterhaald. Meer pagina's leveren niet automatisch meer relevante instroom op. Google's [richtlijn voor facetten](https://developers.google.com/crawling/docs/faceted-navigation) benadrukt beheersing van de URL-ruimte.

Beoordeel clusters op daadwerkelijke zoekvraag, beschikbare banen, onderscheidende informatie en resultaat. Geef de beste functie-/kennis-/reisgebiedcombinaties voorrang. Stel geen minimumaantal vacatures als universele SEO-regel in; één goede nichevacature kan waardevol zijn. Publiceer evenmin honderden bijna gelijke pagina's alleen omdat een combinatie technisch bestaat.

Het geteste filter werkt en levert resultaten. Na de AJAX-actie bleven robots/canonical in de browser van het basisoverzicht staan; een directe anonieme aanvraag van de filter-URL gaf `noindex, follow` zonder canonical. Die ontbrekende canonical is op zichzelf geen bewezen fout. Leg het gewenste gedrag vast en test directe URL's, paginering en terugnavigatie samen.

### F12 — Landelijke banen vragen een herkenbaar aanbod

Maak een landingsroute voor servicewerk in Nederland naast internationaal werk. Benoem vertrek vanuit huis of standplaats, storingsdienst, reistijdvergoeding, overnachtingen, opleiding en salaris. Gebruik die kenmerken ook in filters. De huidige filtergroep “Dagen Internationaal” is voor iemand die in Nederland wil werken geen vanzelfsprekende eerste keuze.

Start met twee of drie functiegroepen waarvoor echte opdrachten beschikbaar zijn, bijvoorbeeld servicemonteur machinebouw en service engineer elektrotechniek/mechatronica. Kies de definitieve clusters op basis van klantvraag en kandidaatdata. Breid de publieke belofte pas uit zodra het aanbod die ondersteunt.

### F13 — Benut bestaande commerciële zoekkansen vóór brede productie

In de recente Nederlandse zoekdata staan vacature-intenties zoals commissioning engineer en service engineer met zichtbaarheid maar weinig klikken. Een deel staat al op een bruikbare gemiddelde positie. Controleer voor zulke termen de werkelijk getoonde pagina, snippet, zoekresultaten en bedoeling. Test concretere titels, een passend aanbod en een beter eerste scherm.

Algemene kennisvragen over cleanrooms, gaswassers en preventief onderhoud hebben eveneens bereik, maar liggen verder van een plaatsing of werkgeversopdracht. Behoud nuttige kennis; leg een inhoudelijke route naar het relevante beroep en passende banen. Geef geen voorrang aan een artikel uitsluitend omdat het veel impressies heeft.

De [Beroepengids](https://thefieldservicecompany.nl/beroepengids/) en [functiepagina](https://thefieldservicecompany.nl/beroepengids/functienamen/field-service-engineer/) hebben al relaties met vacatures. Verbeter die keten gericht. Maak bij locatiecontent de praktische arbeidsmarktinformatie belangrijker dan toeristische beschrijvingen. In het Rotterdamse archief begint de uitleg onder meer met skyline en bezienswaardigheden; een kandidaat heeft eerder baat bij werkgebied, bereikbaarheid en reisbelasting.

## 7. Groei bij machinebouwers: nieuwe klanten én uitbreiding

**Nieuwe organisaties:** bouw één sterke werkgeversroute voor machinebouwers met een substantiële serviceafdeling. Maak daarna alleen sectorpagina's waar TFSC aantoonbaar expertise en bewijs heeft. Formuleer vanuit problemen van service managers en recruitment: technische geschiktheid, internationaal versus landelijk inzetbaar zijn, lastige reisverwachtingen en het bereiken van latent zoekende engineers.

**Bestaande klantorganisaties:** gebruik bewezen werk bij één onderdeel als aanleiding voor een gesprek met andere vestigingen of bedrijfsonderdelen. Maak een vestigingsoverstijgende propositie, met ruimte voor lokale techniek en teams. Meet dit als uitbreiding bij een bestaande klant, afzonderlijk van een nieuwe klantorganisatie.

De site moet bij beide routes de overtuigende bestemming zijn. Alleen meer kandidaatverkeer zal niet vanzelf voldoende werkgeversvraag creëren. Plan ook gerichte menselijke acquisitie en warme introducties. AI kan openbare bedrijfsinformatie en een gespreksvoorbereiding helpen ordenen; een onderzoekssignaal mag geen ongecontroleerde contactcampagne worden.

**Eerste commerciële pilot:** kies een beheersbare lijst van 20 passende organisaties/onderdelen, verdeel in nieuwe klanten en uitbreiding, definieer per account het servicevraagstuk en de relevante beslisser en toets de propositie in echte gesprekken. Gebruik de vragen en bezwaren om de werkgeverspagina te verbeteren. Dit rapport heeft niemand benaderd.

Meet de keten: passende organisatie → gesprek → gekwalificeerde personeelsvraag → opdracht → plaatsing. Aantallen aanvragen zonder fit zeggen weinig. Zet de eerste 30 dagen een nulmeting neer; kies daarna haalbare kwartaaldoelen.

## 8. Vindbaarheid en waarde in een wereld met AI

### F14 — Maak TFSC een bruikbare bron en een waardevolle tussenpersoon

AI kan banen helpen vinden en werkgevers achter teksten herkennen. De kans is daarom tweeledig: TFSC als bron laten terugkomen én duidelijk maken waarom bemiddeling meer oplevert dan alleen de naam van een werkgever.

Een te bewijzen propositie kan bestaan uit technische beoordeling, kennis van teams en leidinggevenden, realistische reisvoorwaarden, voorbereiding, onderhandeling en begeleiding. **Dit zijn te valideren bouwstenen, geen in dit onderzoek bewezen exclusieve diensten.** Werk twee echte praktijkgevallen uit voordat de site zulke voordelen claimt. Werkgeversgeheimhouding is geen duurzaam verdienmodel; verwijder ook niet alle nuttige details uit vacatures uit angst voor herkenning.

Maak inhoud citeerbaar: geef direct antwoord op een concrete vraag, vermeld auteur en relevante ervaring, onderbouw cijfers en leg actualisering vast. Gebruik vergelijkingen, echte interviews en eigen geanonimiseerde inzichten waar de bronbasis dat toestaat. Verbind expertise aan de herkenbare TFSC-organisatie en aan Rudi als inhoudelijk aanspreekpunt. Een AI-systeem kan een bron kiezen, maar een vermelding of positie is niet te garanderen.

Google zegt dat normale SEO en inhoudskwaliteit blijven gelden voor zijn AI-functies; er is geen speciaal AI-bestand of bijzonder schema nodig. Investeer dus eerst in toegankelijke, consistente inhoud, technische bereikbaarheid en links. Zie [Google over AI-functies](https://developers.google.com/search/docs/appearance/ai-features).

### F15 — Meet AI op vier verschillende niveaus

| Niveau | Meting | Beperking |
| --- | --- | --- |
| Bereikbaarheid | robotsregels, CDN/firewall, geverifieerde crawlerlogs | Een crawl bewijst geen aanbeveling |
| Aanwezigheid in antwoorden | Vast panel van vragen, bronlinks en vermeldingen | Antwoorden variëren per tijd, model en context |
| Verkeer | Genormaliseerde herkenbare AI-bronnen en landingspagina's | Ontbrekende referrers en onzichtbare bezoeken blijven mogelijk |
| Zakelijk resultaat | Geldige aanvragen, gekwalificeerde kandidaten en opdrachten | Key events zijn niet automatisch sollicitaties of klanten |

Er is al herkenbare AI-instroom in de opgeslagen gegevens. Dezelfde bron komt door de tijd onder verschillende kanalen en media voor. Alleen het kanaal `AI Assistant` tellen zou historische bezoeken missen. Normaliseer bekende brondomeinen centraal en houd de oorspronkelijke waarde bewaard.

Maak een panel met kandidaatvragen én werkgeversvragen, waaronder landelijke servicebanen, reisbelasting, technische overstap en werving voor machinebouwers. Leg vraag, datum, systeem, antwoord, bronlink en inhoudelijke juistheid vast. Gebruik dit als richtinggevende steekproef, niet als representatief marktaandeel.

De opgehaalde robots.txt blokkeert OAI-SearchBot niet expliciet. Controle aan de CDN-kant ontbreekt nog. OpenAI onderscheidt zoektoegang via OAI-SearchBot van training via GPTBot; dat zijn afzonderlijke beleidskeuzes. Zie [de crawlerdocumentatie](https://developers.openai.com/api/docs/bots). De bestaande blokkades voor Indeed en Jooble zijn een apart distributiebesluit: heroverweeg op leadkwaliteit en afspraken, niet alleen op verkeersvolume.

## 9. Wat we van andere sites kunnen leren

Deze vergelijking betreft waargenomen functies en presentatie, geen bewezen conversieprestaties of oordeel over imago.

| Voorbeeld | Waargenomen | Toepassing voor TFSC |
| --- | --- | --- |
| [Profield-overzicht](https://www.profield.nl/vacatures) en [vacature](https://www.profield.nl/vacatures/field-service-engineer-auto-van-de-zaak/) | Postcode/reistijd, salaris in lijsten, compacte kerninformatie en samenvatting | Maak reisbelasting en aanbod direct vergelijkbaar |
| [Synsel](https://synsel.nl/expertises/field-service-engineers) | Duidelijke keuze tussen vacatures en een oriënterende scan | Bied ook twijfelende kandidaten een concrete volgende stap |
| [Techsharks-vacature](https://www.techsharks.nl/job/field-service-engineer_799) | Salaris en locatie, directe recruiter en een uitgewerkt vervolgproces | Maak het contact en de stappen na reageren helder |

TFSC kan zich inhoudelijk onderscheiden met nichekennis en directe indiensttreding. Kopieer geen vacaturevolumes, claims, toon of omvangrijke tooling zonder onderbouwing. Een korte goede adviesroute kan dezelfde gebruikersbehoefte bedienen als een dure scan.

## 10. AI en n8n als onderhoudsproces

### F16 — Bouw verder op wat er al is

Er zijn drie gepubliceerde analytics-workflows aangetroffen: Search Console, GA4 en Clarity. De laatste bekeken dagelijkse uitvoeringen zijn geslaagd. Er waren ook oudere fouten; dat bewijst geen actuele storing. Metabase heeft al analytische voorzieningen. Een tweede los meetplatform bouwen heeft nu weinig prioriteit.

Het grootste gemis is een betrouwbare brug tussen meting, redactionele actie en gecontroleerde publicatie. Voorgestelde werkstromen:

| Werkstroom | Wat automatisch kan | Wat eerst inhoudelijke controle nodig heeft | Klaar wanneer |
| --- | --- | --- | --- |
| Websitecontrole | Publieke status, redirects, canonicals, dubbele metadata, schema-verschillen en ontbrekende vacature-ID's controleren | Wijzigingen met gevolgen voor indexering of formulierverwerking | Afwijking geeft een reproduceerbaar voorstel met getroffen URL's |
| Contentonderhoud | Verouderde feiten, lege velden, gebroken links en relevante zoekkansen signaleren; conceptwijziging maken | Feiten, claim, werkgeversinformatie en nieuwe positionering | Herkomst en wijzigingsreden staan bij iedere aanpassing |
| Vacaturepublicatie | Goedgekeurde velden omzetten naar kaart, pagina en structured data | Salaris, standplaats, werkgever en beschikbaarheid | Eén gevalideerde invoer levert overal dezelfde gegevens |
| Rapportage | Dagelijkse bronverversing, volledigheidscontrole en wekelijkse samenvatting | Commerciële interpretatie bij weinig data | Ontbrekende data wordt geen nulwaarde en een storing geen groeisignaal |

Begin met deterministische controles; gebruik AI voor uitleg, selectie en voorstellen. Zo ontstaan minder foutieve waarschuwingen en lagere gebruikskosten. Geef voorstellen een vaste structuur: probleem, bewijs, gewenste verandering, risico, test en herstelmogelijkheid. Ontdubbel werk op URL + type probleem + versie, zodat elke run niet dezelfde taak maakt.

Beperk automatische writes tot afgebakende, vooraf afgesproken wijzigingen en bewaar de vorige versie. Een salariswijziging, redirect of employerclaim vraagt meer controle dan het herstellen van een bekende link. Vacatures krijgen een vervaldatum of hercontrole; sluiting moet ook doorwerken in lijsten en structured data. Geen nieuwe externe onderhoudsautomatiseringen zijn in dit onderzoek geactiveerd.

### F17 — Maak techniek reproduceerbaar en meet prestaties

De repo scheidt beheerde snippets en historische exports al goed. De algemene plugin-, configuratie-, uitrol- en rollbackbestanden zijn grotendeels placeholders. Vul de werkelijke configuratie en eigenaar per onderdeel aan. Leg vast wat op productie actief is, welk bestand leidend is en hoe wijzigingen worden getest en teruggezet. Behoud herstelkopieën tot een vervanging aantoonbaar werkt.

De geteste anonieme homepage bevat 27 script-elementen met `src` en 24 stylesheetlinks; beide vacatures 30 respectievelijk 25. Daaronder zitten ook data-URL's. De HTML-documenten zijn ongeveer 199, 278 en 254 kB. Dit zijn **geen** externe requesttellingen, gemeten downloadsnelheden of Core Web Vitals-scores.

Meet eerst representatieve templates op mobiel en desktop, anoniem en met cache. Prioriteer daarna heroafbeelding, onnodige assets, tracking en interactievertraging. Controleer de in de repo beschreven brede script-preloads op actuele aanwezigheid voordat je ze verandert. Richtdoelen voor velddata op het 75e percentiel zijn LCP ≤2,5 s, INP ≤200 ms en CLS ≤0,1, zoals beschreven bij [Web Vitals](https://web.dev/articles/vitals). Deze audit heeft geen snelheidsoordeel gemeten.

## 11. Uitvoerbare planning en prioriteiten

P1 = direct commercieel/gegevensrisico of noodzakelijke meetbasis; P2 = groeiverbetering; P3 = uitbreiding na bewijs. Inspanning is een grove inschatting in eigen werkdagen, inclusief gerichte controle, geen offerte. Afhankelijkheden kunnen de planning veranderen.

| Volgorde | Actie | Prioriteit | Inspanning | Eigenaar | Acceptatie/resultaat |
| --- | --- | --- | --- | --- | --- |
| 1 | F01 vacature-ID en parameteropbouw herstellen | P1 | 0,5–1,5 dag | Rudi + implementatie | Beide voorbeeldvacatures en proefinzending correct gekoppeld |
| 2 | F02 salaris/werkgever en F05 dubbele descriptions | P1 | 1–2 dagen | Techniek + redactie | Tekst, velden en uitvoer consistent |
| 3 | F03 locatiebetekenis vastleggen en pilotmapping | P1 | 1–3 dagen | Rudi + techniek | Woonregio, vertrekpunt en werkgebied apart |
| 4 | F04 canonicalmatrix en gerichte correctie | P1 | 1–2 dagen | SEO + techniek | Native en combo-routes inhoudelijk juist behandeld |
| 5 | Verkeersdiagnose en funneldefinities | P1 | 1–3 dagen | Analytics + Rudi | Bronvergelijking, meetgaten en nulmeting vastgelegd |
| 6 | F06 tien vacatures en kaarttemplate verbeteren | P2 | 1–3 dagen | Redactie + ontwerp | Kernvoorwaarden zichtbaar vóór lange tekst |
| 7 | F10 contrast, claims en onhandige labels | P2 | 0,5–1 dag | Ontwerp + redactie | Centrale juiste claims en toegankelijke primaire knop |
| 8 | F08 werkgeverspagina en twee bewijsgevallen | P2 | 2–4 dagen | Rudi + redactie | Propositie voor machinebouwers en duidelijke vervolgstap |
| 9 | F07/F09 homepage en formulieren testen | P2 | 2–4 dagen | Ontwerp + techniek | Gelijkwaardige funnelmeting vóór/na |
| 10 | F12 landelijke pilot + gerichte acquisitie | P2 | 3–6 dagen, daarna doorlopend | Rudi | Echt aanbod, gesprekken en passende instroom |
| 11 | F13/F14 tien pagina's op commerciële intentie verbeteren | P2 | 3–5 dagen | SEO + inhoud | Antwoord, bewijs en relevante volgende stap |
| 12 | F15/F16 controles en AI-rapportage ontwerpen | P2 | 2–4 dagen | Analytics + techniek | Eén betrouwbare signalerings- en voorstelketen |
| 13 | F17 actieve configuratie, uitrol en rollback documenteren | P2 | 1–2 dagen | Techniek | Een wijziging is navolgbaar en terug te zetten |
| 14 | Uitbreiding van bewezen clusters en automatisering | P3 | Per bewezen onderdeel ramen | Rudi | Groei met behoud van kwaliteit en beheersbaarheid |

**Eerste 14 dagen:** acties 1–7, met beperkte parallelle contentproductie. De formulier- en gegevensproblemen hoeven niet op een nieuw ontwerp te wachten.

**Dag 15–30:** werkgeverspropositie, landelijke pilot, nulmeting en gesprekken met passende organisaties. Reserveer wekelijks tijd voor acquisitie én inhoudelijke beoordeling.

**Dag 31–60:** verbeter de tien meest kansrijke bestaande pagina's; toets kaarten en formulieren met vijf kandidaten en de werkgeversroute met drie passende beslissers. Kleine aantallen zijn kwalitatief onderzoek. Bij weinig verkeer zijn langdurige A/B-tests vaak te traag; gebruik dan gefaseerde invoering en benoem seizoensinvloed en andere wijzigingen.

**Dag 61–90:** schaal alleen bewezen inhoud en klantroutes. Evalueer gekwalificeerde kandidaten, nieuwe opdrachtgevers en uitbreiding bij bestaande klanten. Zet controles om in beheerste n8n-processen.

**Maand 4–12:** bouw eigen kennis en aantoonbare praktijkcases uit, veranker klantuitbreiding en ontwikkel zo nodig een eenvoudig voorkeuren-/adviesinstrument. Overweeg een herbouw pas wanneer metingen aantonen dat de huidige templates of datalaag de gewenste snelheid, betrouwbaarheid of ontwikkeling structureel blokkeren.

## 12. Waarop we sturen

| Doel | Hoofdmeting | Ondersteunende meting |
| --- | --- | --- |
| Meer geschikte kandidaten | Gekwalificeerde kandidaatcontacten uit de website | Geldige sollicitaties/adviezen per landingsgroep en reisvoorkeur |
| Meer nieuwe klanten | Gekwalificeerde opdrachten van nieuwe machinebouwers | Gesprekken en aanvragen die bij het klantprofiel passen |
| Uitbreiding bestaande klanten | Nieuwe opdrachtgevende vestigingen/onderdelen | Kennismakingen en personeelsvragen per bestaande organisatie |
| Relevant bereik | Niet-merkverkeer met kandidaat- of werkgeversintentie | Klikken, impressies, CTR en positie per vast cluster |
| AI-vindbaarheid | Gekwalificeerde instroom en juiste bronvermeldingen | Herkenbare AI-verwijzingen en vast vragenpanel |
| Onderhoud | Tijd per verbetering en aandeel foutloze publicaties | Tijd tot detectie, openstaande afwijkingen en herstel |

Een GA4-key event krijgt pas commerciële betekenis als bekend is welke gebeurtenis het is en of de aanvraag is verwerkt. Een telefoon- of WhatsApp-klik blijft een contactintentie totdat een gesprek of aanvraag bevestigd is. Stuur geen CV's, namen, e-mailadressen of telefoonnummers naar analytics. Koppel commerciële uitkomsten met een afgebakende interne identificatie.

Stel na de eerste nulmeting getalsdoelen per kwartaal vast. Een verdubbeling beloven zonder actuele aantallen, beschikbare vacatures en opvolgcapaciteit is geen professioneel groeiplan.

## 13. Nog te valideren vóór uitbreiding

1. Welke landelijke functiegroepen hebben de eerste echte opdrachten en welke regio's kan TFSC goed bedienen?
2. Welke onderscheidende voordelen van bemiddeling kan TFSC met concrete voorbeelden bewijzen?
3. Welke key events vertegenwoordigen werkelijk verwerkte aanvragen en hoe worden kwaliteit en plaatsing teruggekoppeld?
4. Welke bestaande klantcases mogen met naam of geanonimiseerd worden gepubliceerd?
5. Welke onderhoudsacties mogen na de pilot automatisch worden uitgevoerd?

Deze punten verhinderen de technische quick wins niet. Ze bepalen vooral de inhoud en schaal van de volgende groeifase.

## Bronnen en herleidbaarheid

- Actuele website-inspectie en publieke responses van 25 oktober 2026; zie de [bewijsbijlage](2026-10-25-websiteonderzoek-bewijs.json). De belangrijkste URLs staan bij de bevindingen.
- Repositorybasis: [`8d4f8f4`](https://github.com/TheFieldServiceCompany/website-thefieldservicecompany.nl/tree/8d4f8f499cbb18e0b2c8cb76e43b97248f27e94f), met [de snippetinventaris](https://github.com/TheFieldServiceCompany/website-thefieldservicecompany.nl/blob/8d4f8f499cbb18e0b2c8cb76e43b97248f27e94f/wordpress/snippets/README.md) en [repositorybeleid](https://github.com/TheFieldServiceCompany/website-thefieldservicecompany.nl/blob/8d4f8f499cbb18e0b2c8cb76e43b97248f27e94f/README.md).
- Interne broncontrole: Metabase Website Analytics, tabellen `mart.gsc_property_daily`, `mart.gsc_page_daily`, `mart.gsc_query_daily`, `mart.ga4_daily_acquisition` en `mart.clarity_metrics_long`; alleen-lezen onderzocht op 25 oktober. Absolute totalen en definities staan in de lokale privébijlage. Search Console loopt tot 22 oktober, GA4 tot 24 oktober, Clarity begint 23 oktober.
- n8n: gepubliceerde analytics-workflows, compacte uitvoeringsstatussen en GA4-rapportdimensies; alleen-lezen gecontroleerd. Geen credentials of uitvoeringspayloads in deze publicatie.
- Search Console-afbeelding en doelen/klantprofiel door Rudi aangeleverd op 25 oktober 2026; eerdere chat over de indexeerbaarheid als historische context, actuele data opnieuw gecontroleerd.
- Externe primaire richtlijnen en vergelijkingspagina's zijn hierboven direct gekoppeld en op onderzoeksdatum geraadpleegd.
