# Eigen WordPress-plugins: beveiliging, aanvalstests en updates

**Later op 7 november:** de vier bevindingen zijn gerepareerd en de twee nieuwe versies zijn geplaatst. Zie [het herstelverslag](2026-11-07-custom-plugin-security-fixes.md). Hieronder staat de oorspronkelijke nulmeting, inclusief de toenmalige nog-open-status.

Onderzocht op 7 november 2026, op basis van de daadwerkelijk geïnstalleerde broncode van elf TFSC-plugins. Productie: WordPress **7.1.4**, PHP **8.5.12**. Er zijn vier bevindingen gereproduceerd. **De audit is afgerond; de hieronder voorgestelde reparaties zijn nog niet uitgevoerd.**

De hoogste prioriteit ligt bij de herkomstcontrole van documenten in Form Bridge. Daarna volgen de integriteit en begrenzing van de diagnostiek en de aanvraaglimiet van de serverprobe. De andere plugins hebben in de onderzochte paden geen nieuwe aangetoonde beveiligingsfout opgeleverd. Dat is geen verklaring dat alle mogelijke aanvallen uitgesloten zijn.

## Wat werkelijk is gedaan

- Via de bestaande WordPress-authenticatie de versies en 83 geïnstalleerde bron-/documentatiebestanden opgehaald, waaronder 56 PHP-bestanden. De [manifestgegevens](evidence/custom-plugin-security-2026-11-07-manifest.json) bevatten hashes, geen broncode met persoonsgegevens of geheimen.
- De vier relevante geïnstalleerde Elementor Pro-bestanden aanvullend gelezen om de uploadketen te controleren. Deze commerciële afhankelijkheidsbron wordt niet in dit rapport gerepubliceerd.
- Een nieuwe, tijdelijke WordPress 7.1.4-database in PHP-geheugen gebruikt, met PHP 8.3.33 en 8.5.10. Alle elf eigen plugins zijn geladen; productieconfiguratie en productiegegevens zijn niet gekopieerd. Uitgaande WordPress HTTP-verzoeken en automatische cronverwerking zijn geblokkeerd.
- Per PHP-versie 195 gerichte controles uitgevoerd, plus 16 begrensde stresstests, een afzonderlijke uploadherkomstproef en 56 directe PHP-aanroepen. De 195 omvatten ook controles dat beheerhandlers werkelijk geregistreerd zijn; dit zijn geen 195 verschillende kwetsbaarheden. Een voltooide proef betekent hier óók dat de hieronder beschreven aanvallen reproduceerbaar zijn.
- Anoniem, als abonnee, auteur en redacteur de 13 beheerde REST-acties getest: 52 geweigerde verzoeken. Veertien beheerhandlers zijn zowel met auteursrechten als met beheerdersrechten zonder geldige nonce aangeroepen. Deze stopten vóór uitvoering.
- Op productie uitsluitend kleine openbare GET-controles uitgevoerd: vijf gevoelige leesroutes weigeren anonieme toegang met 401; de elf hoofd-PHP-bestanden geven rechtstreeks een lege body. De tijdelijke inventarisatieroute is verwijderd door de beheersnippet weer te deactiveren en de oorspronkelijke code te herstellen. Er zijn geen live formulieren ingestuurd, aanvalspayloads opgeslagen, echte documenten doorgestuurd, back-ups hersteld of stresstests op productie uitgevoerd.

De [bewijsgegevens](evidence/custom-plugin-security-2026-11-07-results.json) en [testbronnen](security-tests-2026-11-07/README.md) leggen de uitvoering en grenzen vast.

## Vier gereproduceerde bevindingen

### 1. Form Bridge accepteert bestaande bestanden zonder nieuwe upload — eerst repareren

**Versie 0.5.5; privacyrisico, hoge herstelprioriteit.** Bij een optioneel documentveld kan een ingestuurde gewone tekstwaarde een URL naar een bestaand document in de uploadmap bevatten. Als er geen nieuwe upload is, blijft die waarde in Elementor `raw_value` staan. Onze adapter neemt haar over; de documentmanifestatie en de lokale Backblaze-validatie accepteren vervolgens het bestaande bestand. Zij controleren pad, type en grootte, maar niet dat het bestand bij deze inzending is geüpload.

Reproductie met de daadwerkelijke geïnstalleerde Elementor `Form_Record`- en `Upload`-klassen: normale validatie geslaagd, nul nieuwe bestanden, geen verwerkingsfouten, maar een bestaand beschermd test-PDF uit `uploads/tfsc-form-bridge/` wordt aanvaard voor een nieuw document. Alleen de basisregistratie en superglobal-helper van Elementor zijn minimaal nagebootst. Er is geen volledige HTTP-/CAPTCHA-/CRM-/Backblaze-keten uitgevoerd en er is geen netwerkexfiltratie geclaimd.

Voor misbruik moet iemand een bruikbare bestaande bestandsverwijzing kennen en een gemapt uploadveld kunnen gebruiken dat zonder nieuwe upload door de formuliercontrole komt. Het is geen bewezen manier om willekeurige serverbestanden te raden of bestanden buiten de uploadmap te lezen. De openbare sollicitatie- en adviesformulieren tonen een CV-bestandsveld zonder HTML-`required`; dit ondersteunt de relevantie, maar bewijst op zichzelf niet alle serverinstellingen.

**Reparatie:** laat de Elementor-adapter uitsluitend documenten doorgeven uit de servermatig opgebouwde verzameling van gecontroleerde uploads voor deze inzending. Wis of weiger losse POST-/`raw_value`-bestandsverwijzingen wanneer zo’n uploadbewijs ontbreekt. Pas dezelfde herkomstregel toe op toekomstige Bricks-ondersteuning en op de overdracht aan de worker. Houd bestanden buiten de bedoelde stagingmap geweigerd en bewaar het verband tussen inzending en document. Test daarna zowel normale uploads als deze vervalsing opnieuw.

Betrokken bron: `tfsc-form-bridge/src/ElementorProAdapter.php` rond 235–300, `SubmissionNormalizer.php` rond 193–218, `DocumentManifest.php` en `BackblazeClient.php` rond 177–236. De oude pad-/typecontrole blijft nuttig, maar is niet voldoende als bewijs van eigendom of herkomst.

### 2. Een browsermelding kan een serverdiagnose overschrijven

**Form Bridge 0.5.5; middelmatig integriteitsrisico.** De openbare browserdiagnostiek accepteert een correlatie-ID. Als invoegen door een bestaand ID mislukt, schrijft zij met `update()` over de bestaande regel heen. Er wordt daarbij niet gecontroleerd of die regel van de browser, hetzelfde formulier of deze poging is. Het oorspronkelijke label `source=server` blijft staan.

In de proef veranderde een bestaande serverregel van afgeleverd naar mislukt, met een aangeleverde antwoordtekst. De bezoeker had geen beheerdersrechten. Een geldige publieke gastnonce en een bekende correlatie-ID waren voldoende. De nonce is een verzoekcontrole, geen geheim beheerderswachtwoord. Willekeurig raden van andermans UUID is niet aangetoond en is niet nodig om de integriteitsfout met een bekende ID aan te tonen.

**Reparatie:** behandel browsermeldingen als aparte, onbetrouwbare gebeurtenissen met een nieuw server-ID. Bewaar het aangeleverde ID uitsluitend als correlatieverwijzing. Laat een browsermelding nooit de gezaghebbende serverstatus vervangen. Als samenvoegen gewenst is, moeten formulier, bron en servermatig vastgesteld verband expliciet worden gecontroleerd. Test botsende ID’s en verschillende formulieren.

Betrokken bron: `tfsc-form-bridge/src/ElementorDiagnostics.php` rond 195–227 en `DiagnosticRepository.php::update()`.

### 3. Diagnostiek schrijft al vóór formuliercontrole, zonder eigen burstgrens

**Form Bridge 0.5.5; middelmatig beschikbaarheids- en logintegriteitsrisico.** `begin_request()` hangt vóór de eigenlijke Elementor-formulierverwerking aan de openbare AJAX-actie. Een actief gemapt formulier is voldoende om een diagnoseregel te schrijven. De limiet van tien browserfoutmeldingen geldt niet voor dit serverpad. Hier ontbreekt ook de vroege payloadgrens die het browserpad wel heeft.

Zestig pogingen van dezelfde testafzender maakten zestig serverdiagnoses aan zonder formulierauthenticatie of geslaagde formulierverwerking. Dit is een lokale, opeenvolgende burstproef; geen productieserver-DoS of gelijktijdige loadtest. De bestaande totale recordlimiet en bewaartermijn voorkomen onbeperkte blijvende groei, maar voorkomen geen veelvuldige schrijfacties of verdringing van nuttige diagnoses.

**Reparatie:** begrens diagnostische verwerking vóór het verzamelen en encoderen van de invoer. Gebruik een aparte, consistente limiet voor deze vroege serverdiagnostiek, met aandacht voor gedeelde proxy-adressen en gelijktijdige verzoeken. Bij overschrijding moet vooral het loggen worden overgeslagen; een limiet op diagnostiek mag niet onbedoeld geldige sollicitaties blokkeren. Behoud beperkte tellingen van overgeslagen gebeurtenissen en de bestaande totale recordgrens. CAPTCHA later in de keten beschermt deze eerdere schrijfhandeling niet.

Betrokken bron: `tfsc-form-bridge/src/ElementorDiagnostics.php` rond 34–88 versus het afzonderlijk begrensde browserpad rond 176–253.

### 4. Aanvraaglimiet van de serverprobe is te omzeilen met dezelfde sleutel

**Server Location Test Probe 0.1.1; middelmatig misbruikrisico binnen de sleutelbevoegdheid.** Authenticatie accepteert onder andere `Bearer` ongeacht hoofdletters. De aanvraaglimiet gebruikt de ruwe headertekst als identiteit. Dezelfde geldige sleutel met een andere schrijfwijze krijgt daardoor een nieuwe teller. Wisselen tussen de twee ondersteunde sleutelheaders geeft hetzelfde ontwerpprobleem.

In de proef werd het 31e identieke verzoek geweigerd, maar werd dezelfde sleutel met gewijzigde Bearer-schrijfwijze weer toegelaten. Zonder geldige sleutel blijft toegang geweigerd. Dit is geen anonieme authenticatieomzeiling. Allowlist, HTTPS-/poortcontrole, DNS-controle, IP-vastzetting en redirectcontrole blijven afzonderlijke verdedigingslagen.

**Reparatie:** koppel de limiet aan de geverifieerde sleutelidentiteit, onafhankelijk van de aangeboden header. Maak de teller bestand tegen gelijktijdige verhogingen en voeg een totale werkgrens toe. Beperk tevens de totaal opgehaalde bytes en de totale tijd over een redirect-/herhaalreeks. De huidige probe telt downloadbytes maar breekt niet op een maximale omvang af.

Betrokken bron: `server-location-test-probe/includes/class-sltp-plugin.php::request_has_valid_token()` en `consume_rate_limit()`; netwerkwerk in `class-sltp-probe.php`.

## Uitkomst per plugin en gevolgen voor onderhoud

Alle onderstaande versies zijn op het moment van inventarisatie actief. Geen van de elf bevat een eigen updatekoppeling of een `Update URI`. Geen van de elf stond geselecteerd voor automatische pluginupdates; de aanwezige updatecache bood voor geen ervan een pakket aan. Dat is geen nieuwe online controle van beschikbare releases geweest.

| Plugin en live versie | Beveiligingsuitkomst in deze ronde | Onderhoud bij updates |
| --- | --- | --- |
| Form Bridge 0.5.5 | Drie bevindingen hierboven; documentpad/type/grootte en beheergrenzen afzonderlijk getest. | Hoogste aandacht: Elementor Pro-uploadhooks en Bricks-adapter. Eigen database-upgrade aanwezig. Volledige synthetische ontvangst-/uploadproef op dev nodig na reparatie en builderupdates. |
| Server Location Test Probe 0.1.1 | Sleutelgebonden limiet te omzeilen; slechte sleutel en onveilige URL-varianten geweigerd. | Standaard REST/opties, daarnaast cURL/DNS-gedrag testen bij PHP-/hostingupdates. Geen databasetabellen. |
| Cache Warmer 0.6.4 | Geen nieuwe aangetoonde fout in geteste rechten, URL-controle, invoergrenzen en workerlock. | Eigen database-upgrade. LiteSpeed-/Rank Math-hooks en sitemap-/cURL-gedrag blijven afhankelijkheden. Vroege vertaalinitialisatie gaf een onderhoudsnotice. |
| Combo Archive Manager 0.5.0 | Beheerrechten, nonces, lock en begrensde combinatiefunctie getest. | Eigen optiemigratie; sterke afhankelijkheid van de bestaande archiefmotor/snippets 148 en 193. Die motor is niet volledig in deze fixture geladen. |
| Crawler Log 1.4.5 | Exportrechten, datum-/SQL-invoer en CSV-formuleprefixen getest. | Eigen schema-upgrade en logopruiming. Minimumversies voor WordPress/PHP ontbreken in de pluginheader; aanvullen op basis van tests. Proxyheaders zijn geen onafhankelijk bewijs van clientidentiteit; verificatie en limiter gebruiken terecht de directe afzender. |
| Custom Functions 0.6.0 | Geen nieuwe aangetoonde fout; beperkte functies, geëncodeerde uitvoer en grote artikelinvoer gecontroleerd. | Standaard hooks, maar Elementor-widget-ID’s en vaste formulierpagina-ID’s maken functionele controle nodig. Minimumversies ontbreken. |
| Mediafilters 1.0.0 | Geen nieuwe aangetoonde fout; nonce, rechten en 1000 ongeldige filters getest. | Indextabel wordt in beheer gecontroleerd/geïnitialiseerd. WordPress-mediabibliotheek, media-JavaScript en HappyFiles vereisen een echte beheerinterfaceproef na updates. |
| Schema Read Endpoint 2.1.0 | Alleen bevoegde beheerders, alleen lezen; geneste gangbare geheimvelden geredigeerd. | Standaard REST; Pods-exportstructuur is extra afhankelijkheid. Minimumversies ontbreken. Geheimredactie is geen garantie voor iedere denkbare veldnaam. |
| Structured Data 0.5.3 | Beheeracties beschermd; schadelijke schematekst kan de JSON-LD-scriptgrens niet doorbreken. | Standaard WordPress/Pods, instellingen worden met defaults aangevuld. Controleer vacaturelevenscyclus, locaties en schema-uitvoer na updates. |
| Updraft to pCloud 0.2.4 | Beheeracties, archiefpadvervalsing en manipulatie van versleutelde tokens getest; geen nieuwe fout aangetoond. | UpdraftPlus-history-/backupinterfaces, cURL en OpenSSL zijn extra afhankelijkheden. Geen echte cloudverwijdering of herstelproef in deze audit. In de website-repository staat al 0.2.5; dat is niet automatisch live gekomen. |
| Rank Math-uitbreiding 0.3.1 | Eerder deze dag gerepareerd; huidige hoofdcode gelijk aan die beveiligde versie. Deze ronde controleerde ook laden zonder afhankelijkheden. | Sterkste koppeling met Elementor/Rank Math en TFSC-breadcrumbfilters. De eerdere uitgebreide rendererproef staat in de aparte Rank Math-review; deze ronde bewijst niet opnieuw alle bouwersjablonen. |

Aanvullend onderhoudspunt bij Form Bridge: de volledige afleverpayload blijft in de lokale outbox staan nadat aflevering is gemarkeerd. De eindige bewaartermijn van het diagnoselog is geen bewaartermijn voor die outbox. Bepaal apart hoelang afgeronde inzendingen en achtergebleven stagingdocumenten nodig zijn en ruim die pas op nadat ontvangst en eventuele documentregistratie zijn bevestigd. Lopende of te beoordelen inzendingen mogen niet blind worden gewist.

## Gaan onze plugins vanzelf mee met WordPress?

Een gewone WordPress-coreupdate herschrijft onze plugins niet. Een plugin die de ondersteunde API’s gebruikt kan gewoon blijven werken; hij hoeft niet voor iedere WordPress-versie opnieuw gebouwd te worden. Een update van WordPress repareert echter ook geen fout in onze eigen code. Daarvoor blijft een nieuwe pluginrelease nodig. Zie [WordPress-updates](https://developer.wordpress.org/advanced-administration/upgrade/upgrading/).

**Nu leveren we die nieuwe eigen versies apart aan.** GitHub bevat broncode en ZIP’s, maar een nieuwe commit of ZIP wordt niet vanzelf door WordPress opgehaald. Een interne database-upgrade zoals `maybe_upgrade()` helpt pas nadat de nieuwe bestanden zijn geïnstalleerd; zij is geen updatebezorging. De ontbrekende `Update URI` verdient ook aandacht: WordPress introduceerde deze header om onbedoelde vervanging van eigen plugins door een gelijknamige WordPress.org-plugin te voorkomen. Er is hier geen daadwerkelijke overname aangetoond. Zie [de officiële toelichting](https://make.wordpress.org/core/2021/06/29/introducing-update-uri-plugin-header-in-wordpress-5-8/).

Er is geen algemene plugin die onze code automatisch compatibel maakt met iedere toekomstige WordPress-, PHP-, Elementor- of Rank Math-versie. Een updater kan de installatie gemakkelijker maken; compatibiliteitscontrole en onderhoud blijven nodig.

### Concrete werkwijze vanaf nu

1. Repareer eerst Form Bridge en daarna de probe. Herhaal zowel de aanval als de normale werkstroom. Voor Form Bridge moet een normale PDF/DOC/DOCX-upload op dev aankomen, terwijl een aangeleverde oude bestandsverwijzing wordt geweigerd. Blokkeer daarbij uitgaande e-mail en gebruik een testontvanger/testopslag.
2. Leg per plugin één bronlocatie, geïnstalleerde versie, release-tag, pakketchecksum, vereisten en herstelversie vast. Bewaar dezelfde pluginmap/-bestandsnaam bij een update. Verwijder de plugin niet eerst: uninstall kan gegevens opruimen.
3. Voeg een unieke `Update URI`, onderbouwde WordPress-/PHP-minima en release-informatie toe. Een `Update URI` alleen downloadt nog geen updates. WordPress biedt de [updatehook voor de opgegeven host](https://developer.wordpress.org/reference/hooks/update_plugins_hostname/) om een beheerde updatebron aan te sluiten.
4. Begin met versiegebonden ZIP-vervanging via **Plugins → Nieuwe plugin → Plugin uploaden → huidige versie vervangen**. Maak eerst een herstelkopie; test het vervangingspakket en de benodigde datamigratie op dev. Bewaar bij een rollback ook database en instellingen die bij de oude code horen.
5. Maak daarna desgewenst één beheerde updatevoorziening waarmee de geteste releases in het gewone WordPress-updatescherm verschijnen. Laat die alleen expliciet vrijgegeven versies aanbieden, met vaste pakketidentiteit, HTTPS, integriteitscontrole en een beperkte downloadbron. Stop geen breed bruikbaar privé-GitHub-token in browsercode of openbare updategegevens. Dit updatekanaal is tijdens de audit niet gebouwd of geïnstalleerd.
6. Test bij een nieuwe WordPress-/PHP-versie de huidige productieversies eerst op dev, en voeg de betreffende versie aan de herhaalbare testmatrix toe. Bij Elementor-/Rank Math-/UpdraftPlus-updates test je daarnaast de directe integratie. Pas code aan als een test, gewijzigde API of beveiligingsfix dat nodig maakt; verhoog niet alleen een versielabel.

De [officiële pluginheaders](https://developer.wordpress.org/plugins/plugin-basics/header-requirements/) zijn het uitgangspunt voor vereisten en identificatie. Databasewijzigingen moeten versiegebonden en herhaalbaar zijn en na afloop op succes worden gecontroleerd; uitsluitend een activatiehook is daarvoor niet voldoende bij een bestaande installatie.

## Grenzen en resterend werk

Deze ronde combineert gerichte broncontrole met echte WordPress-rechten, REST-dispatcher, hooks en databasebewerkingen. De stresstests waren opeenvolgend en begrensd. Er zijn geen massale gelijktijdige HTTP-aanvallen, CDN-/WAF-omzeiling, volledige browser-/buildertests, malwareanalyse van Office-documenten of echte cloudherstelproeven uitgevoerd. PHP 8.5.10 is dicht bij maar niet exact de productiepatch 8.5.12; SQLite vervangt lokaal de productie-MySQL/MariaDB. SQLite gaf een eigen PHP 8.5-deprecation, de bewuste UUID-botsing een verwachte databasefout en geblokkeerde updateverzoeken verwachte netwerkwaarschuwingen.

**De vier gevonden fouten zijn nog aanwezig in de onderzochte live versies.** Er is geen algemene veiligheidsverklaring afgegeven en geen plugin ongevraagd vervangen. Het volgende uitvoerbare werk is een gecontroleerde reparatierelease voor Form Bridge en de probe, gevolgd door een dev-proef en pas daarna live vervanging. De updatevoorziening is een afzonderlijke verbetering.
