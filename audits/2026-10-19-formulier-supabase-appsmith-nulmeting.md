# Formulieren, Supabase en Appsmith — actuele nulmeting

Datum: 19 oktober 2026  
Omgevingen: LIVE WordPress en Supabase staging  
Status: alleen-lezen gestart; veldmapping en implementatie nog niet uitgevoerd

## Besloten prioriteit

De gebruiker heeft de actuele werkprioriteit verlegd naar de koppeling van de
WordPress-formulieren met Supabase en Appsmith. Het travelwerk blijft open maar
is geparkeerd.

De zes bekende formulierpaden zijn:

| Formulier | WordPress-ID | Bestaande route |
| --- | ---: | --- |
| Registratie | 21400 | Elementor/e-mail en AFI naar EngageBay |
| Mailing | 21438 | AFI naar EngageBay |
| Advies | 21445 | Elementor/AFI |
| Vraag | 21455 | Elementor/AFI |
| Solliciteren | 21465 | Elementor/e-mail en AFI naar EngageBay |
| Werkgeverscontact | pagina 9428 | Elementor/e-mail/redirect/popup en AFI |

De actuele configuraties met veld-ID's, veldtypes, verplichtheid, uploads,
limieten en acties moeten nog opnieuw rechtstreeks op LIVE worden uitgelezen.
Daarbij worden geen inzendingen, persoonsgegevens of bestanden geëxporteerd.

## Alleen-lezen Supabase-controle

Project: `tfsc-crm-ats-staging` (`bpfocgapqsrokmargsjr`). Het project was
`ACTIVE_HEALTHY` op PostgreSQL 17.6.1.

Het bestaande model bevat de benodigde basisobjecten:

- `persons` en `candidate_profiles`;
- `applications`, `application_statuses` en `vacancies`;
- `companies`, `company_contacts` en `company_addresses`;
- `documents`, `document_versions` en `document_links`;
- `external_identifiers` voor stabiele bron-ID's.

Alle gecontroleerde tabellen hebben RLS aan en één of meer bestaande policies.
De huidige kolommen bevestigen onder meer dat een sollicitatie verplicht aan
één kandidaatprofiel en één vacature gekoppeld wordt en ruimte heeft voor
`source_channel` en `source_external_id`.

## Wat nog ontbreekt

- Er is geen publieke tabel gevonden waarvan de naam op intake, submission of
  outbox wijst.
- Er is nog geen Supabase Edge Function in dit project.
- Er is nog geen goedgekeurde één-op-één mapping van Elementor-veld-ID naar
  Supabase-kolom en Appsmith-scherm.
- Toestemming, bron-eigenaarschap, deduplicatie en bewaartermijn zijn nog niet
  per formulier definitief vastgelegd.

Appsmith-functies bestaan al voor onder meer kandidaat aanmaken/bijwerken en
documenten registreren. Die functies bewijzen dat het bestaande model bruikbaar
is, maar vormen nog geen veilige, idempotente publieke formulierintake.

## Alleen-lezen Appsmith- en WordPress-controle

De lokale Appsmith-bron staat schoon op branch `crm-layout-redesign`, commit
`47972ac`. De actieve bron bevat de pagina's Companies, Company Detail,
Contacts, Contact Detail, Vacancies en Vacancy Detail. Bestaande queries en
databasefuncties ondersteunen onder meer kandidaatprofielen bijwerken,
documenten registreren, werkgeverscontacten beheren en sollicitaties bij een
vacature tonen of wijzigen. Er is geen afzonderlijke intake-, submission- of
outboxpagina of -query aangetroffen.

De openbare WordPress REST-index bevestigt dat Elementor de routes
`/elementor/v1/forms` en `/elementor/v1/form-submissions` aanbiedt. Een
anonieme alleen-lezen aanvraag op `/elementor/v1/forms` geeft terecht HTTP 401.
De exacte LIVE-formulierconfiguratie moet daarom via een bestaande ingelogde
WordPress-sessie of een nieuw application password worden gelezen. Er is nog
niets via de GUI geopend of gewijzigd.

## Eerstvolgende veilige stap

1. Exporteer alleen de configuratie van de zes LIVE-formulieren als
   herstel- en vergelijkingspunt, zonder inzendingen of persoonsgegevens.
2. Maak per formulier een tabel met bronveld, betekenis, verplichtheid,
   validatie, uploadgedrag, Supabase-doel en Appsmith-weergave.
3. Leg ontbrekende doelen en beslispunten aan de gebruiker voor.
4. Ontwerp daarna pas de WordPress-outbox en beveiligde server-side
   afleverroute; voer geen schemawijziging uit voordat de mapping is
   goedgekeurd.

## Veiligheidsgrenzen

- Elementor bewaart iedere inzending en upload eerst duurzaam lokaal.
- AFI/EngageBay blijft actief totdat de nieuwe route in schaduwmodus is
  bewezen.
- Geen service-role key, databasewachtwoord of persoonsgegevens in browsercode,
  GitHub of gewone logs.
- Geen bestaande kandidaat-, werkgever-, sollicitatie- of documentdata
  wijzigen tijdens de inventarisatie.
