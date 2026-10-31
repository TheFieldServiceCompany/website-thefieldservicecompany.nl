# Onderzoek vacaturetemplate: zichtbaarheid in Elementor en formuliercontext

Datum: 31 oktober 2026.
Status: LIVE-template en bridgeversie gecontroleerd; editoruitzondering via REST opgeslagen. Visuele GUI-controle staat nog open.
Eerste fase: alleen lezen en deze notitie opslaan. Vervolgfase na expliciete toestemming: alleen de editoruitzondering in de vacaturetemplate aangepast. Geen formulierinzendingen of formulier-/pluginwijzigingen.

## Opdracht en toestemming

De eigenaar meldt dat een element pas na een bepaalde scrollpositie zichtbaar wordt.
Dat werkt voor bezoekers, maar het element verdwijnt ook tijdens het bewerken in Elementor.
De bestaande uitlijning met de onderliggende content moet behouden blijven.

Daarnaast vraagt de eigenaar of een script dat vacaturegegevens naar het sollicitatieformulier
doorgeeft nog nodig is sinds de Form Bridge. Onderzoek is toegestaan; iedere wijziging
aan website, template of scripts vereist eerst afzonderlijke toestemming.
De eigenaar heeft apart opdracht gegeven het onderzoek in de juiste GitHub-repository vast te leggen.

## Repositorykeuze en geraadpleegde bronnen

- Deze repository beheert de WordPress-/Elementor-website. De README vermeldt expliciet
  dat repositorybestanden niet automatisch naar WordPress of Elementor worden uitgerold.
  Daarom hoort de onderzoeksnotitie hier, niet in de artikelen-, analytics- of SEO-repository.
- Onderzochte websitebasis: commit `bccc1d65a5da6e5cfc0406c5a6a532dd83ca40ae`.
- `wordpress/elementor.md` is nog een inventarisatieplaceholder.
  `wordpress/elementor/custom-code/` bevat op deze basis alleen een README.
  De bedoelde vacaturetemplate en beide scripts zijn hier niet als bruikbare bron aangetroffen.
- De actuele website-`wordpress/snippets/README.md`, onderdeel “Elementor Custom Code / Adviesknop”,
  beschrijft al een vereenvoudigde adviesroute die alleen het vacaturenummer doorgeeft.
- De Form Bridge-bron en mappingdocumentatie zijn aangetroffen in
  [tfsc-crm-ats, branch codex/wordpress-form-bridge](https://github.com/TheFieldServiceCompany/tfsc-crm-ats/tree/codex/wordpress-form-bridge/wordpress/tfsc-form-bridge),
  branchstand bij onderzoek `76280c3608edc4fb84d44086ef9b7f2cc54ebc65`.
  Ze staan niet op de onderzochte `main` van die repository.
- Daar zijn `src/SubmissionNormalizer.php` en
  `docs/FORM-DATA-MAPPING-2026-10-20.md` rechtstreeks uit GitHub gelezen.
  Deze broncontrole bevestigt niet welke pluginversie en mapping momenteel op de website actief zijn.
- [Elementors eigen frontendbron](https://github.com/elementor/elementor/blob/main/assets/dev/js/frontend/frontend.js)
  biedt `elementorFrontend.isEditMode()`, gebaseerd op `config.environmentMode.edit`.

## Beperking websitecontrole

Het openen van `https://dev.thefieldservicecompany.nl/wp-admin/` werd door het
beheerdersbeleid van de browser geblokkeerd. Die blokkade is niet omzeild.
Er is geen Elementor-editor geopend en geen actuele DOM-, CSS- of scriptinspectie
van de vacaturetemplate uitgevoerd. Het gemelde gedrag is daarom nog een
gebruikerswaarneming, geen zelfstandig gereproduceerde fout.
De exacte doelomgeving moet ook worden bevestigd wanneer de eigenaar de hoofdsite bedoelt.

## Voorlopige oplossingsrichting voor de editor

Een gerichte uitzondering voor Elementor-bewerkmodus is technisch mogelijk:
laat het betrokken element in de editor zichtbaar en bewerkbaar, en voer de
scrollgestuurde verberging alleen voor bezoekers uit.

Dit is een voorstel, geen gecontroleerde patch. Voor de concrete aanpassing moeten
de selector, start-CSS, eventuele inline stijlen, scrollhandler en initialisatie
van het bestaande script worden gelezen. Alleen een vroege return uit JavaScript
is onvoldoende als CSS het element aanvankelijk al verbergt.

Behoud de bestaande frontendlogica voor positie, breedte, uitlijning, marges en
eventuele ruimtehouder. Pas alleen in de editor de werkelijk relevante
verbergende eigenschappen aan. Bepaal aan de hand van de bron of positionering
in de editor eveneens een uitzondering nodig heeft; schrijf geen algemene
CSS-reset die de bestaande uitlijning onbedoeld verandert.
De uitzondering moet ook blijven werken wanneer Elementor het widget opnieuw rendert.

## Voorlopige beoordeling formuliergegevens

Volgens de bridge-mapping is bij solliciteren het veld `vacaturenummer` gekoppeld
aan `vacancy.number`. Dat nummer identificeert de vacature. De normalizer vertaalt
aangeleverde formuliervelden volgens de mapping; deze bron toont geen automatische
vervanging van de overdracht van het vacaturenummer vanaf de vacaturepagina.

De documentatie noemt vacaturetitel, vacaturelink, bedrijfsnaam en dienstverband
bij sollicitaties als “Niet versturen”; de canonieke informatie wordt uit de
vacature afgeleid. Voor advies is alleen het doorgeven van het nummer al
gedocumenteerd. Dit betekent niet automatisch dat de bestaande velden of het
volledige script verwijderd mogen worden: de formulierweergave, bevestigingen,
e-mails en andere bestemmingen kunnen er nog gebruik van maken.

Conclusie: mogelijk vereenvoudigen tot het vacaturenummer; volledig verwijderen
is nog niet onderbouwd. Eerst vaststellen of het onbekende script dat nummer,
de knopbestemming, een popup of andere benodigde formuliercontext verzorgt.
Als Elementor het nummer al onafhankelijk en correct invult, kan de betreffende
overdrachtscode mogelijk vervallen. Dat moet op de actuele template worden bewezen.

## Nog nodig vóór een wijzigingsvoorstel

1. Beide actuele scriptblokken en bijbehorende CSS, liefst met template-export
   of template-ID en de exacte omgeving.
2. De doelknoppen, formulierbestemming en bron van het verborgen vacaturenummerveld.
3. Een kleine, concrete wijziging met behoud van de bestaande uitlijning ter goedkeuring.

Na afzonderlijke toestemming: editorzichtbaarheid en bewerken na opnieuw renderen
controleren; bezoekersweergave vóór en na de scrolldrempel op desktop en mobiel
controleren; formuliercontext en eventuele bevestigingsmails controleren.
Een echte inzending of wijziging aan live systemen valt niet onder deze onderzoeksopdracht.

## Besluit

Geen scripts verwijderd of aangepast. Geen template opgeslagen. Geen cache geleegd.
Geen formuliertest verstuurd. Alleen deze notitie is toegevoegd.


## Vervolgonderzoek: adviesknoppen en noodzakelijke gegevens

De eigenaar heeft een screenshot aangeleverd van “Script Advies Buttons VarLink Build”.
De getoonde code zoekt op `.elementor-icon-list-text` naar tekst die begint met
`Vacaturenummer:`, bouwt een URL naar `/formulier-advies/` op het huidige domein
en voegt uitsluitend `vacaturenummer` toe wanneer het nummer is gevonden.
Dit screenshot bevat geen scrollscript.

Deze adviescode verstuurt geen bedrijfsnaam, titel, dienstverband of vacaturelink.
Hij stuurt ook geen sollicitatie naar de bridge: hij navigeert naar het formulier.
De bridge verwerkt de formulierinzending pas daarna. Op een pagina met meerdere
gevonden vacaturenummers overschrijft de lus de waarde telkens; de laatste
gevonden waarde wint. De juiste vacaturecontext per knop moet dus expliciet
worden gecontroleerd wanneer de code ook op vacatureoverzichten wordt gebruikt.

### Exacte verantwoordelijkheid per stap

| Stap | Noodzakelijk voor de bedoelde vacaturecontext |
| --- | --- |
| Vacaturepagina naar sollicitatieformulier | Het canonieke vacaturenummer |
| Vacaturepagina naar adviesformulier | Het vacaturenummer als het advies bij die vacature hoort |
| Algemene adviesknop | Geen vacaturenummer; een algemene adviesaanvraag blijft mogelijk |
| Formulier naar bridge | Ingevulde contact-/kandidaatgegevens, privacyakkoord en toepasselijke vacaturecontext |
| Bridge naar ontvanger | Versiegebonden bericht met formulieridentiteit, inzendings-UUID, mapping en gekoppelde waarden; dit maakt de plugin zelf |

Voor solliciteren gebruikt de gedocumenteerde mapping `vacaturenummer -> vacancy.number`.
Advies gebruikt `vacancy_number -> vacancy.number`, gevuld vanuit de URL-parameter
`vacaturenummer`. Dit verschil tussen veld-ID en URL-parameter is geen fout.

De overige gedocumenteerde sollicitatievelden zijn voornaam, achternaam,
woonplaats, mobiel, e-mail, cv, LinkedIn en privacyakkoord. Dat zijn gegevens van
de kandidaat; de vacatureknop hoeft ze niet vanaf de vacaturepagina mee te nemen.
Advies bevat daarnaast dienstverbandvoorkeuren en wensen/ambitie.
“Benodigd voor de bridge” betekent niet dat ieder van deze profielvelden technisch
verplicht is. De onderzochte verwerkingsfunctie controleert privacyakkoord,
minstens één naamdeel en minstens één bruikbaar identiteitssignaal
(e-mail, telefoon/mobiel of LinkedIn). De actuele Elementor-formulieren kunnen
strengere verplichte velden hebben. Cv, woonplaats, voorkeuren en ambitie zijn
profielinhoud, geen vervanging voor vacature-identiteit.

Voor het aanmaken van een correcte sollicitatie zijn kandidaat-aanmaak en een
bestaande, niet-gearchiveerde vacature nodig. Bij een ontbrekend/onbekend nummer
ontstaat geen correcte vacaturekoppeling. De latere migratie
`20261026123000_web_form_nonblocking_review.sql` behandelt sommige
vacatureproblemen als niet-blokkerende review; daarom betekent een ontvangen
bericht of aangemaakt kandidaatprofiel nog niet dat er een juiste sollicitatie is.
Voor advies is een nummer optioneel; een meegegeven onbekend nummer leidt tot een reviewcode.

### Concrete bevinding: “Niet versturen” sluit ruwe velden niet uit

Onderzocht: TFSC Form Bridge 0.5.4, commit
`76280c3608edc4fb84d44086ef9b7f2cc54ebc65`.
Dit is een broncodecontrole, geen bevestiging van de geïnstalleerde liveversie.

1. `MappingRepository.php` slaat een keuze `not_sent` niet op in de veldmapping.
2. `SubmissionNormalizer.php:34-38` voegt de waarde van ieder aangeleverd veld
   eerst toe aan `raw_fields`, vóór de controle op een CRM-doel vanaf regel 48.
3. `raw_fields` komt onder `fields` in de volledige inzendingsenvelop.
   `OutboxRepository.php` slaat deze envelop lokaal op.
4. `DeliveryWorker.php` geeft de envelop via `EnvelopeSanitizer::for_receiver()`
   aan de ondertekende aflevering.
5. `EnvelopeSanitizer.php:9-32` verwijdert bestandsreferenties en uploadwaarden,
   maar verwijdert geen overige ongemapte veldwaarden.

Gevolg: “Niet versturen” betekent hier feitelijk “niet mappen naar een CRM-doel”,
niet “deze waarde verlaat WordPress niet”. Overbodige verborgen tekstvelden kunnen
dus nog onder `fields` worden meegestuurd, zelfs zonder canonieke verwerking.
Dit is een concrete bronbevinding en moet bij echte databeperking worden meegenomen.
Er is geen liveverkeer onderschept en geen werkelijk datavolume gemeten.

Daarnaast verzamelt de bridge zelf bronmetadata, waaronder pagina-URL, referrer,
IP, browseridentificatie en beschikbare campagneparameters. Browserdiagnostiek
heeft nog een afzonderlijke route. Het vervangen van een knopscript verandert
deze pluginfunctionaliteit niet. Overbodige vacaturevelden, bronregistratie en
diagnostiek hebben verschillende doelen en moeten bewust worden beoordeeld.
Een lokale uitvoeringsproef is niet gestart omdat PHP in deze werkomgeving ontbreekt;
de bevinding berust op het gevolgde codepad.

### Alternatief voor het losse JavaScript

Voorkeur: bouw de knoplink tijdens het renderen uit het opgeslagen vacatureveld,
bijvoorbeeld `/formulier-advies/?vacaturenummer=<canoniek-nummer>`.
Het verborgen formulierveld leest vervolgens de GET-parameter via Elementors
Request Parameter. Zo blijft de bridge-aflevering ongewijzigd en vervalt het
uitlezen van zichtbare tekst.

Elementor ondersteunt dynamische tags, custom fields en Request Parameter.
De officiële documentatie vermeldt wel dat niet iedere tag in ieder veld beschikbaar
is. Daarom moet op de bestaande knop worden gecontroleerd of het vacatureveld
rechtstreeks in de link kan worden gebruikt. Als dat niet kan, is een kleine
gedeelde PHP-shortcode of Elementor dynamic tag voor de volledige knop-URL een
alternatief, eventueel centraal beheerd in de bridge-plugin. Dat vereist een
afzonderlijke implementatiekeuze; het is geen bestaande functie van de onderzochte bridge.

Bij een formulier op de vacaturepagina zelf kan een dynamisch verborgen veld
rechtstreeks het vacatureveld gebruiken. Bij het huidige aparte formulier moet
de context de overgang tussen pagina’s blijven maken. De bridge kan de vacature
niet betrouwbaar raden uit alleen titel, referrer of de formulierpagina.
Een algemene adviesknop houdt een gewone advieslink zonder verzonnen nummer.

Bronnen:
- [Elementor dynamic tags](https://elementor.com/help/dynamic-tags-pro-2/)
- [Elementor Request Parameter](https://elementor.com/help/dynamic-request-parameter-pro/)
- De hierboven vastgelegde bridgebron en mappingdocumentatie.

### Bijgestelde toestemming en toegang

De eigenaar heeft inmiddels expliciet toestemming gegeven de editorzichtbaarheid
en uitzondering te herstellen, met behoud van de bestaande werking.
Die toestemming omvat niet automatisch het verwijderen of wijzigen van
formulier-/adviescode of de dataverwerking van de bridge.

De eigenaar wijst op bestaande GUI-, Elementor-MCP- en WordPress-REST-toegang.
De beschikbare tools zijn opnieuw op WordPress/Elementor gecontroleerd en Plugin
Management is geraadpleegd. Deze chat biedt geen WordPress-/Elementor-MCP-tool;
de pluginzoekopdracht leverde alleen een niet-geïnstalleerde WordPress.com-koppeling
op. Dat is geen bevestiging van toegang tot de bestaande zelfbeheerde website.
De browserblokkade is niet omzeild met rechtstreekse netwerkverzoeken.
De bestaande Elementor-MCP moet beschikbaar worden gemaakt in deze chat, of de
actuele scrollcode en CSS moeten als bron worden aangeleverd, voordat de
geautoriseerde zichtbaarheidspatch gericht kan worden uitgevoerd en geverifieerd.

Geen website-, template- of pluginwijziging uitgevoerd. Alleen dit onderzoekslog is bijgewerkt.


## Toegang hersteld en geautoriseerde editorreparatie uitgevoerd

### Correctie op de eerdere REST-conclusie

De eerdere gevolgtrekking dat deze chat niet via REST verder kon was te stellig.
Het ontbreken van een ingebouwde WordPress-tool sluit een bestaande,
geautoriseerde lokale REST-route niet uit. De eigenaar wees terecht op
**article write #2 | work**. De lokale publicatiescripts van die chat tonen
rechtstreekse REST-toegang met een bestaand authenticatiebestand en een vaste
serverroute voor het productiedomein. Diezelfde route is in deze chat met een
uitsluitend lezende `users/me`-controle geverifieerd: HTTP 200, met de benodigde
bewerkrechten. Er zijn geen nieuwe credentials aangemaakt of gepubliceerd.
Een eerste verzoek via de gewone domeinroute gaf een HTML-403 van LiteSpeed;
de bestaande artikelpublicatieroute werkt wel. Dit onderscheid is geen
ontbrekende WordPress-MCP-koppeling.

De oorspronkelijke browserblokkade betrof de ontwikkelomgeving. Er is niet via
een andere browser of directe netwerkroute op die geblokkeerde ontwikkelorigin
ingelogd. De nu gebruikte verbinding is de al bestaande API-route voor de
productiewebsite, op uitdrukkelijk verzoek van de eigenaar.

### Browserinstellingen

De lokale browserconfiguratie heeft standaard `access = deny`; alleen Appsmith
had een website-uitzondering. Dit ondersteunt het vermoeden van de eigenaar
dat eerdere toegangsbeperkingen de GUI blokkeren. Native-appbesturing staat ook
standaard uit, maar is voor de ingebouwde browser niet nodig.

Voor de visuele WordPress-controle is een gerichte browseruitzondering voor
`https://thefieldservicecompany.nl` nodig. Voor toekomstig werk op DEV komt daar
`https://dev.thefieldservicecompany.nl` bij. Uploads, downloads, volledige CDP-
toegang en toegang tot andere websites hoeven hiervoor niet algemeen open.
Bij een aanvullende beheerde denial moet de toepasselijke beheerde regel worden
aangepast door de beheerder. Geen veiligheidsinstelling is in deze opdracht gewijzigd.

Bronnen voor de uitleg:
- https://learn.chatgpt.com/docs/browser
- https://learn.chatgpt.com/docs/agent-approvals-security
- https://learn.chatgpt.com/docs/enterprise/managed-configuration

### Werkelijke LIVE-bronnen

- WordPress REST geeft de Elementor-documentmeta inclusief `_elementor_data`
  en `_elementor_page_settings` terug voor bevoegde toegang.
- Actieve vacaturetemplate: `26022`, **single | vacature**,
  conditie `include/singular/vacature`.
- Scrollpaneel: container `044c4a6`, class `.scroll-sticky-wrapper`.
- Scrollcode en basis-CSS: HTML-widget `2c4849c`.
- Formulieroverdracht: HTML-widget `62cd538`; geeft daadwerkelijk
  `vacaturenummer`, `contractvorm`, `postTitle`, `postUrl` en `cn` door aan
  solliciteren en vraag stellen. Deze code is niet veranderd.
- Globaal adviesscript: Elementor Custom Code `21893`, gepubliceerd,
  body-start, prioriteit 1. Code komt overeen met het screenshot: alleen
  `vacaturenummer`. Niet veranderd.
- De pluginlijst bevestigt **TFSC Form Bridge 0.5.4, actief**. Daarmee is de
  onderzochte bronversie nu ook aan de actieve versie gekoppeld. De actuele
  opgeslagen veldmappings zijn hiermee niet opnieuw uitgelezen.
- De Elementor-MCP is bereikbaar via de bestaande WordPress-authenticatie.
  Initialisatie en `tools/list` slagen. De beschikbare bewerkingstools ondersteunen
  deze V3 HTML-widget niet; daarom is de ondersteunde WordPress-documentmeta
  via REST gebruikt. Geen MCP- of OAuth-credentials aangemaakt of uitgebreid.

### Oorzaak en uitgevoerde wijziging

De bestaande basis-CSS zet het paneel op `position: absolute`, `opacity: 0`,
`visibility: hidden` en `pointer-events: none`. Pas met `.is-visible` wordt
het paneel `position: sticky` en zichtbaar. De scrollcode bepaalde dit ook in
het editorcanvas; CSS bevatte geen bewerkmodusuitzondering.

Alleen `settings.html` van widget `2c4849c` is aangepast:

1. Een inline stijl die uitsluitend onder Elementors editormarkeringen werkt
   maakt het paneel zichtbaar en aanklikbaar, in normale documentflow met
   `position: relative` en `z-index: auto`. De bestaande breedtes, marges,
   gridinstellingen en padding zijn niet gewijzigd.
2. Een vroege JavaScript-uitzondering herkent de editor via bodyclass,
   documentmarker of `elementorFrontend.isEditMode()`, toont het paneel en
   registreert daar geen scroll-/resizehandlers.

De inline stijl blijft ook werken als Elementor de widget opnieuw rendert of
het bestaande DOMContentLoaded-script niet opnieuw afvuurt. Voor bezoekers
zijn de oorspronkelijke JavaScript-logica en bestaande Custom CSS behouden.

Vóór opslaan zijn wijzigingsdatum en volledige meta opnieuw gelezen en met
de voorbereidingskopie vergeleken. Er waren geen tussenliggende veranderingen.
Vervolgens is uitsluitend `_elementor_data` van dit document geschreven.
De teruggelezen JSON komt exact overeen met de beoogde wijziging. Status,
auteur, titel, slug, datum en alle overige meta zijn gelijk gebleven.

### Verificatie en grenzen

- Tien geïsoleerde JavaScript-scenario’s geslaagd: drie editorherkenningspaden,
  ontbrekende elementen en vergelijking met het oorspronkelijke bezoekersgedrag
  vóór/op/na de scrolldrempel, bij resize en orientationchange.
- Zes CSS-cascadecontroles geslaagd: frontend verborgen/zichtbaar identiek;
  beide editor-markeringen met en zonder `.is-visible` zichtbaar en in flow.
- REST-readback en controle op ongerelateerde wijzigingen geslaagd.
- Openbare voorbeeldvacature HTTP 200. De pagina gebruikt LiteSpeed-geoptimaliseerde
  externe assets. Alleen een bronverzoek is geen visuele scroll-/editorcontrole.
- Visuele controle op desktop/mobiel en in Elementor blijft open totdat
  browsertoegang beschikbaar is. Er is geen globale cache gewist.

Beheerde bronbestanden en rollback-HTML staan nu onder
`wordpress/elementor/templates/26022-single-vacature/` en
`wordpress/exports/elementor/2026-10-31/`.

Toestemming: de eigenaar gaf expliciet opdracht de zichtbaarheid en
uitzonderingsmodus op te lossen. Het verwijderen/vervangen van formuliercode,
wijzigen van de bridge en verruimen van browserrechten zijn niet uitgevoerd.


## Browserrechten: gerichte persoonlijke uitzonderingen toegevoegd

Op afzonderlijke opdracht van de eigenaar zijn browseruitzonderingen voor het
productiedomein en de ontwikkelomgeving toegevoegd aan de persoonlijke
Codex-configuratie. Alleen toegang is toegestaan; uploads, downloads en volledige
CDP-toegang blijven op beide origins geblokkeerd. Alle overige waarden zijn
behouden en de persoonlijke configuratie is vooraf geback-upt en teruggelezen.

De daarna uitgevoerde browserproef voor de productie-WordPress-admin wordt nog
steeds geweigerd door de beheerde originpolicy. Deze wijziging is dus nog geen
werkende GUI-toegang. Er is geen alternatieve browserroute geprobeerd.

Een back-up van vóór de privacywijziging bevat geen browserbeperkingen; de
huidige persoonlijke configuratie bevat die wel. Eerdere lokale herstelscripts
verwijzen naar een systeembeleid met het actieve beheerde profiel. Dit onderbouwt
een lokale beheerconfiguratie als verklaring voor “Managed by your organization”.
De actuele inhoud van het systeembestand is in deze chat niet uitgelezen: dat
valt buiten de toegestane mappen.

Er is een afzonderlijke, lokale beheerderherstelstap voorbereid voor uitvoering
door de eigenaar. Zij verplaatst uitsluitend eventuele lokale beheerde
browserregels naar de persoonlijke instellingen en voegt de twee bedoelde
originuitzonderingen toe. Daardoor kunnen de browserregels weer door de eigenaar
worden bewerkt, met behoud van de bestaande beperkingen als persoonlijke regels.
Onbekende conflicten stoppen de uitvoering; systeem- en persoonlijke configuratie
krijgen eerst een back-up. Overige systeemeisen en bestands-/netwerkrechten worden
niet gewijzigd. De herstelstap is met synthetische configuraties en proefbestanden
gecontroleerd, maar niet op het systeembestand uitgevoerd. Eventuele aanvullende
cloud- of legacybeheerregels kunnen na deze stap nog beperkingen opleggen.

Status: wachten op de lokale beheerdersstap en het opnieuw starten van de app;
de visuele Elementor-eindcontrole blijft open. De eerder opgeslagen websitefix
is in deze browserrechtenstap niet verder gewijzigd.
