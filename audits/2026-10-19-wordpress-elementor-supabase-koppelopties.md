# WordPress/Elementor naar Supabase — koppelopties

Datum: 19 oktober 2026  
Status: alleen-lezen onderzoek; geen plugin geïnstalleerd en geen koppeling
gewijzigd

## Conclusie

Er is geen volwassen, algemene WordPress-plugin gevonden die de bestaande
Elementor Pro-formulieren rechtstreeks en passend op het huidige TFSC-
datamodel in Supabase schrijft. De gevonden plugins met `Supabase` in de naam
richten zich op login/authenticatie, zoekindexen, chats of een eigen specifiek
datamodel; niet op kandidaat-, sollicitatie-, document- en werkgeversintake.

De beste bestaande route is daarom:

1. Elementor bewaart de inzending lokaal en blijft tijdens de overgang de
   bestaande e-mail- en AFI-acties uitvoeren.
2. In de al geïnstalleerde AFI Premium-plugin wordt, pas na goedkeuring, het
   bestaande maar inactieve platform `Webhook` geactiveerd.
3. AFI levert de inzending via de al ingeschakelde jobqueue, met beperkte
   headers, een unieke submission-ID en een ondertekend verzoek af bij één
   Supabase Edge Function.
4. De Edge Function valideert bron, handtekening, tijdvenster, bestandstype,
   grootte en idempotentiesleutel en verwerkt pas daarna de goedgekeurde
   mapping naar de bestaande tabellen en private Storage.
5. AFI-log en resend ondersteunen technisch herstel; functionele
   twijfelgevallen krijgen in Supabase/Appsmith een reviewstatus. Loginhoud en
   bewaartermijn worden vóór ingebruikname privacybewust ingesteld.

Hiermee is geen nieuwe WordPress-plugin en waarschijnlijk geen afzonderlijke
TFSC-outboxplugin nodig. De eigen ontwikkeling blijft beperkt tot het
ontvangstcontract, de Edge Function, idempotente verwerking, bestandsroute en
Appsmith-review.

## Beoordeelde mogelijkheden

### 1. Elementor Pro Webhook — bruikbaar als transport, niet als volledige oplossing

Elementor Pro heeft zelf een `Webhook`-actie en kan formulierdata na verzenden
naar een HTTP-endpoint sturen. Daardoor is geen extra plugin nodig om een
Supabase Edge Function te bereiken.

Nadelen voor de definitieve TFSC-route:

- geen aantoonbare lokale leveringswachtrij met automatische retries;
- mapping, idempotentie en foutafhandeling moeten alsnog server-side worden
  gebouwd;
- uploads komen als link/waarde uit Elementor en vragen een afzonderlijke,
  gecontroleerde overdracht naar private Supabase Storage;
- directe aflevering mag nooit een Supabase secret/service-role key in de
  browser of formulierconfiguratie vereisen.

Beoordeling: geschikt voor een synthetische proef of als transport achter een
lokale outbox, niet als enige veiligheidslaag.

### 2. Huidige Advanced Form Integration Premium — voorkeursroute

LIVE staan AFI Premium 2.10.3 en zes actieve Elementor→EngageBay-integraties.
Alle zes worden in het AFI-overzicht als falend in de afgelopen zeven dagen
aangemerkt.

De eerste controle van `Add New` liet alleen EngageBay zien. Dat was geen
volledige platformlijst: **AFI > Settings** bevat 343 activeerbare platforms en
alleen EngageBay staat nu aan. `Webhook` is aanwezig maar inactief. AFI's
jobqueue en logging staan al aan. De queue meldde bij controle `0 pending`,
`0 failed`; automatische logverwijdering staat uit en foutmail staat uit.

De actuele openbare pluginbeschrijving bevestigt daarnaast algemene Webhooks,
achtergrondverwerking via Action Scheduler, activiteitenlogs en handmatig
opnieuw verzenden. Via de Webhook kan AFI server-side één Supabase Edge
Function aanroepen; een native Supabase-platform in AFI is daarvoor niet
nodig.

Beoordeling: beste bestaande pluginroute. Eerst veldmapping en ontvangstcontract
goedkeuren; daarna Webhook activeren en met synthetische data per formulier in
schaduwmodus testen. De EngageBay-integraties blijven tijdens die proef staan.

### 3. WP Webhooks Pro — serieuze pluginoptie

WP Webhooks ondersteunt Elementor Pro `Form submitted` als trigger en kan een
HTTP-request met eigen methode, headers en JSON-body versturen. De Pro-
functionaliteit vermeldt asynchrone flows, uitgaande-requestlogs,
authenticatietemplates en het opnieuw uitvoeren van een mislukte flow.

Voordelen:

- veel minder eigen WordPress-code voor triggers, logging en handmatige retry;
- per formulier selecteerbaar en visueel te configureren;
- een Edge Function blijft de enige ingang naar het canonieke datamodel.

Aandachtspunten:

- nieuwe betaalde plugin en extra afhankelijkheid;
- bestandsoverdracht, idempotentie en privacy moeten alsnog specifiek worden
  getest;
- het productlog is geen vervanging voor een eigen minimale, PII-arme
  verwerkingsstatus;
- eerst in een geïsoleerde proef aantonen dat fouten niet tot verloren of
  dubbele kandidaten, sollicitaties of documenten leiden.

Beoordeling: goede terugvaloptie, maar niet eerst installeren zolang AFI de
benodigde Webhook, queue, logging en resend al biedt.

### 4. Zapier, Make, Pabbly, OttoKit/SureTriggers en vergelijkbare diensten

Elementor kan via een webhook naar automatiseringsdiensten sturen. Ze kunnen
vervolgens een HTTP-call of databaseactie uitvoeren. Voor deze intake voegen
ze echter een extra gegevensverwerker en foutpunt toe, juist voor cv's en
persoonsgegevens. Bestandstoegang, bewaartermijnen, logging en subverwerkers
moeten dan ook buiten WordPress en Supabase worden beheerst.

Beoordeling: technisch mogelijk, maar niet aanbevolen zolang WordPress en een
Edge Function de route zonder derde partij kunnen afhandelen.

### 5. Overige plugins met Supabase in de naam

De aangetroffen WordPress.org-plugins zijn taakgebonden, bijvoorbeeld
Supabase-authenticatie of WooCommerce-/zoekindexering. Ze beheren hun eigen
schema en lossen de Elementor→TFSC-CRM-mapping, cv-opslag, deduplicatie en
sollicitatierelaties niet op.

Beoordeling: niet geschikt voor dit project.

## Beveiligings- en ontwerpvoorwaarden

- Geen Supabase secret key of legacy service-role key in Elementor,
  frontend-JavaScript, GitHub, URL's of gewone logs.
- De ontvangende Edge Function accepteert alleen een server-to-server request
  met een afzonderlijk roteerbaar geheim of ondertekende payload, rate limit en
  replaybescherming.
- Iedere submission krijgt één stabiele idempotentiesleutel. Een retry mag
  nooit een tweede persoon, kandidaatprofiel, sollicitatie of document maken.
- CV's en vacaturebestanden gaan naar een private Storage-bucket met MIME- en
  groottecontrole; publieke WordPress-upload-URL's zijn geen eindopslag.
- Elementor blijft tijdens bouw en schaduwmodus de lokale herstelbron.
- De precieze veldmapping, toestemming, bewaartermijn en matchregels worden
  goedgekeurd voordat tabellen, policies, functies of formulieracties wijzigen.

## Voorgestelde keuze

Start met het functionele contract en de veldmapping. Gebruik daarna AFI
Webhook plus één beveiligde Supabase Edge Function als eerste proefroute. Zet
Webhook en de nieuwe integraties alleen na expliciete goedkeuring aan en test
eerst synthetisch in schaduwmodus. Houd WP Webhooks Pro en een kleine eigen
outboxconnector alleen als terugvalopties. Gebruik de directe Elementor Webhook
niet als definitieve levering zonder aantoonbare queue en herstelroute.

## Bronnen

- Elementor, `Actions after submit` en Webhook:
  https://elementor.com/help/actions-after-submit/
- Elementor, formuliervelden en eigen veld-ID's:
  https://elementor.com/help/form-widget/
- Advanced Form Integration op WordPress.org:
  https://wordpress.org/plugins/advanced-form-integration/
- WP Webhooks Elementor-integratie:
  https://wp-webhooks.com/integrations/elementor/
- WP Webhooks functies, logging en asynchrone flows:
  https://wp-webhooks.com/features/
- Supabase Edge Functions:
  https://supabase.com/docs/guides/functions
- Supabase beveiligde Edge Functions:
  https://supabase.com/docs/guides/functions/auth
- Supabase secrets:
  https://supabase.com/docs/guides/functions/secrets
- Supabase Storage vanuit Edge Functions:
  https://supabase.com/docs/guides/functions/storage-caching
- Supabase API keys:
  https://supabase.com/docs/guides/getting-started/api-keys
