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

De beste route blijft daarom:

1. Elementor bewaart de inzending lokaal en blijft tijdens de overgang de
   bestaande e-mail- en AFI-acties uitvoeren.
2. Een kleine TFSC-connector haakt server-side aan op een succesvolle
   Elementor-inzending en schrijft een lokaal outboxitem met een unieke
   submission-ID.
3. Een achtergrondtaak levert het bericht met beperkte headers en een
   ondertekend verzoek af bij één Supabase Edge Function.
4. De Edge Function valideert bron, handtekening, tijdvenster, bestandstype,
   grootte en idempotentiesleutel en verwerkt pas daarna de goedgekeurde
   mapping naar de bestaande tabellen en private Storage.
5. Mislukte afleveringen blijven herstelbaar in WordPress; functionele
   twijfelgevallen krijgen in Supabase/Appsmith een reviewstatus.

Dit vraagt iets meer gericht ontwikkelwerk dan alleen een webhook aanklikken,
maar voorkomt een derde CRM of externe verwerker en sluit precies aan op het
bestaande Supabase/Appsmith-model.

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

### 2. Huidige Advanced Form Integration Premium — behouden voor legacy

LIVE staan AFI Premium 2.10.3 en zes actieve Elementor→EngageBay-integraties.
Alle zes worden in het AFI-overzicht als falend in de afgelopen zeven dagen
aangemerkt. Het scherm voor een nieuwe integratie biedt op deze installatie
alleen EngageBay en EngageBay [PRO]; geen Supabase en geen algemene Webhook.

De actuele openbare pluginbeschrijving van AFI noemt voor de algemene versie
wel webhooks, achtergrondverwerking via Action Scheduler, activiteitenlogs en
handmatig opnieuw verzenden. Dat maakt een andere AFI-uitgave theoretisch
interessant, maar is geen reden om de bestaande Premium-installatie te
vervangen of om te bouwen: daarmee kan de huidige legacyroute en licentie
worden geraakt.

Beoordeling: laten staan voor EngageBay en schaduwvergelijking; niet als nieuwe
Supabase-route kiezen zonder afzonderlijke migratie- en licentieproef.

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

Beoordeling: beste kant-en-klare pluginvariant als de voorkeur uitgaat naar een
visuele WordPress-workflow.

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

Start met het functionele contract en de veldmapping. Bouw daarna de kleine
TFSC-connector plus Edge Function. Houd WP Webhooks Pro als terugvaloptie als
de gebruiker liever pluginbeheer en visuele retries heeft dan een kleine eigen
connector. Gebruik de directe Elementor Webhook alleen voor een afgeschermde
synthetische proef, niet als definitieve levering zonder lokale outbox.

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
