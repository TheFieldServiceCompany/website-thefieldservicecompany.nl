# Formulierinventaris dev

Inventarisdatum: 16 oktober 2026. Geen formulierwaarden of persoonsgegevens zijn
in dit document opgenomen.

| Formulier | WordPress-ID | Huidige route |
| --- | ---: | --- |
| Registratie | 21400 | Elementor-opslag/e-mail en AFI naar EngageBay |
| Mailing | 21438 | AFI naar EngageBay |
| Advies | 21445 | Elementor/AFI |
| Vraag | 21455 | Elementor/AFI |
| Solliciteren | 21465 | Elementor-opslag/e-mail en AFI naar EngageBay |
| Employer contact | pagina 9428 | Elementor-opslag, e-mail, redirect, popup en AFI naar EngageBay |

## Behoudsregel

Een formulier mag alleen succes tonen nadat de inzending en eventuele uploads
duurzaam lokaal zijn vastgelegd. Een storing bij CRM, Supabase, e-mail of een
andere externe dienst mag de broninzending niet verwijderen.

## Uitgevoerde correcties op LIVE — 17 oktober 2026

- De lokale Elementor-actie `Inzendingen verzamelen` is bij het
  werkgeverscontactformulier actief bevonden.
- Privacytoestemming is in registratie, mailing, advies, vraag, sollicitatie en
  werkgeverscontact verplicht gemaakt en staat nergens meer standaard
  aangevinkt.
- De bestaande formulier-CSS is ongewijzigd gebleven; de lege en aangevinkte
  toestand gebruiken dezelfde bestaande 18×18-pixel styling.
- Registratie, mailing, advies, vraag, solliciteren en employer contact zijn met
  synthetische gegevens end-to-end getest. Elementor sloeg alle zes lokaal op
  en rapporteerde voor iedere inzending dat alle ingestelde acties slaagden.
- Bij employer contact ontbrak na de migratie de expliciete Elementor-actie
  `Advanced Form Integration`. Deze is toegevoegd met integratie-ID 7. De
  hertest is lokaal vastgelegd als inzending 1136 (`5/5 Geslaagd`); EngageBay
  bevestigde zowel het contact als het bedrijf met HTTP 200.
- AFI-logregels met HTTP 400 tijdens deze tests betroffen uitsluitend pogingen
  om reeds bestaande tags opnieuw aan te maken. De contact- en bedrijfsrecords
  zelf zijn succesvol verwerkt.

## Eerstvolgende correcties

- Uploadlimieten en toegestane extensies expliciet documenteren en testen.
- De 1.050 eerder aanwezige inzendingen en de zeven synthetische
  migratietests voorlopig behouden; bewaartermijn en opschoning worden een
  afzonderlijk privacybesluit. Het actuele totaal is 1.057.
