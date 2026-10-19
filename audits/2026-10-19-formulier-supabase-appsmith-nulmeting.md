# Formulieren, Supabase en Appsmith — actuele nulmeting

Datum: 19 oktober 2026  
Omgevingen: LIVE WordPress en Supabase staging  
Status: LIVE-configuratie alleen-lezen uitgelezen; veldmapping en implementatie
nog niet uitgevoerd

## Besloten prioriteit

De gebruiker heeft de actuele werkprioriteit verlegd naar de koppeling van de
WordPress-formulieren met Supabase en Appsmith. Het travelwerk blijft open maar
is geparkeerd.

De zes bekende formulierpaden zijn:

| Formulier | WordPress-ID | Bestaande route |
| --- | ---: | --- |
| Registratie | 21400 | Elementor-opslag/e-mail en actieve AFI-koppeling naar EngageBay |
| Mailing | 21438 | Elementor-opslag/e-mail en actieve AFI-koppeling naar EngageBay |
| Advies | 21445 | Elementor-opslag/e-mail en actieve AFI-koppeling naar EngageBay |
| Vraag | 21455 | Elementor-opslag/e-mail en actieve AFI-koppeling naar EngageBay |
| Solliciteren | 21465 | Elementor-opslag/e-mail en actieve AFI-koppeling naar EngageBay |
| Werkgeverscontact | pagina 9428 | Elementor-opslag/e-mail en actieve AFI-koppeling naar EngageBay |

De actuele configuraties zijn op 19 oktober rechtstreeks in de ingelogde
Elementor-editor en het AFI-beheer alleen-lezen uitgelezen. Er zijn geen
inzendingen, persoonsgegevens of bestanden geopend of geëxporteerd.

## LIVE-formulierinventaris

### Registratie — Elementor 21400

| Veld-ID | Type | Verplicht | Actuele bijzonderheid |
| --- | --- | --- | --- |
| `vacaturenaam` | hidden | nee | vaste waarde `Registratie` |
| `vacaturenummer` | hidden | nee | vaste waarde `1` |
| `wens_dienstverband` | checkbox | nee | Vast Dienstverband; Freelance/ZZP |
| `bedrijfsnaam` | hidden | nee | vaste waarde `The Field Service Company` |
| `first_name` | text | ja | voornaam |
| `surname` | text | ja | achternaam |
| `city` | text | ja | woonplaats |
| `mobiel` | tel | ja | standaard `+31` |
| `email` | email | ja |  |
| `cv_upload` | upload | nee | maximaal 3 bestanden van 2 MB; jpg, pdf, rtf, txt, doc, odt, wpd, docx; opslagwijze `both` |
| `linkedin` | url | nee |  |
| `wensen` | textarea | nee |  |
| `form_privacy` | acceptance | ja | standaard niet aangevinkt |

Elementor-acties: Inzendingen verzamelen, E-mail, E-mail 2, Omleiden en
Pop-up.

### Mailing — Elementor 21438

| Veld-ID | Type | Verplicht | Actuele bijzonderheid |
| --- | --- | --- | --- |
| `dienstverband` | checkbox | nee | Vast Dienstverband; Freelance/ZZP |
| `werkgebied` | checkbox | nee | Nationaal/Benelux; Europa; Internationaal/Wereldwijd |
| `voornaam` | text | nee |  |
| `achternaam` | text | nee |  |
| `email` | email | ja |  |
| `field_cadabc8` | acceptance | ja | privacytoestemming |

Elementor-acties: Inzendingen verzamelen, Advanced Form Integration, E-mail,
E-mail 2, Omleiden en Pop-up.

### Advies — Elementor 21445

| Veld-ID | Type | Verplicht | Actuele bijzonderheid |
| --- | --- | --- | --- |
| `wens_dienstverband` | checkbox | nee | Vast Dienstverband; Freelance/ZZP |
| `first_name` | text | ja | voornaam |
| `surname` | text | ja | achternaam |
| `woonplaats` | text | ja |  |
| `mobiel` | tel | ja | standaard `+31` |
| `email` | email | ja |  |
| `cv_upload` | upload | nee | maximaal 2 bestanden van 2 MB; jpg, pdf, rtf, txt, doc, odt, wpd, docx; opslagwijze `both` |
| `linkedin` | url | nee |  |
| `wensen` | textarea | nee |  |
| `postTitle` | hidden | nee | vaste waarde `Inschrijving` |
| `postUrl` | hidden | nee | vaste waarde `Inschrijving` |
| `vacaturenaam` | hidden | nee | vaste waarde `Verzoek Advies` |
| `vacaturenummer` | hidden | nee | vaste waarde `2` |
| `bedrijfsnaam` | hidden | nee | vaste waarde `The Field Service Company` |
| `form_privacy` | acceptance | ja | standaard niet aangevinkt |

Elementor-acties: Inzendingen verzamelen, E-mail, E-mail 2 en Omleiden.

### Vraag — Elementor 21455

| Veld-ID | Type | Verplicht | Actuele bijzonderheid |
| --- | --- | --- | --- |
| `first_name` | text | ja | voornaam |
| `surname` | text | ja | achternaam |
| `mobiel` | tel | ja | standaard `+31` |
| `email` | email | ja |  |
| `vraag` | textarea | ja |  |
| `vacaturenaam` | hidden | nee | dynamische waarde |
| `bedrijfsnaam` | hidden | nee | dynamische waarde |
| `vacaturenummer` | hidden | nee | dynamische waarde |
| `vacaturelink` | hidden | nee | dynamische waarde |
| `field_944d626` | acceptance | ja | privacytoestemming |

Elementor-acties: Inzendingen verzamelen, E-mail, E-mail 2, Omleiden en
Pop-up.

### Solliciteren — Elementor 21465

| Veld-ID | Type | Verplicht | Actuele bijzonderheid |
| --- | --- | --- | --- |
| `first_name` | text | ja | voornaam |
| `surname` | text | ja | achternaam |
| `city` | text | ja | woonplaats |
| `mobiel` | tel | ja | standaard `+31` |
| `email` | email | ja |  |
| `cv_upload` | upload | nee | maximaal 2 bestanden van 2 MB; jpg, pdf, rtf, txt, doc, odt, wpd, docx; opslagwijze `link` |
| `linkedin` | url | nee |  |
| `vacaturenaam` | hidden | nee | dynamische waarde |
| `vacaturelink` | hidden | nee | dynamische waarde |
| `vacaturenummer` | hidden | nee | dynamische waarde |
| `bedrijfsnaam` | hidden | nee | dynamische waarde |
| `dienstverband` | hidden | nee | dynamische waarde |
| `form_privacy` | acceptance | ja | standaard niet aangevinkt |

Elementor-acties: Inzendingen verzamelen, E-mail, E-mail 2, Omleiden en
Pop-up.

### Werkgeverscontact — Elementor-pagina 9428

| Veld-ID | Type | Verplicht | Actuele bijzonderheid |
| --- | --- | --- | --- |
| `field_74a0588` | step | n.v.t. | stapmarkering |
| `companyname` | text | ja | bedrijfsnaam |
| `first_name` | text | ja | voornaam |
| `achternaam` | text | ja | achternaam |
| `telefoon_vast` | tel | ja |  |
| `field_ed942af` | step | n.v.t. | stapmarkering |
| `email` | email | ja |  |
| `field_ef49d3a` | url | nee | vacaturelink |
| `vacatures` | upload | nee | maximaal 4 bestanden van 2 MB; jpg, pdf, rtf, txt, doc, odt, wpd, docx; opslagwijze `link` |
| `message` | textarea | nee |  |
| `form_privacy` | acceptance | ja | privacytoestemming |

Elementor-acties: Inzendingen verzamelen, Advanced Form Integration, E-mail,
E-mail 2, Omleiden en Pop-up.

## AFI-status op LIVE

AFI Premium 2.10.3 bevat zes actieve integraties, één per formulier. Alle zes
maken een nieuwe contactpersoon in `EngageBay [PRO]` aan. Het AFI-dashboard
classificeerde op 19 oktober alle zes als `Failing (7d)`. De laatste zichtbare
foutmomenten lagen tussen vier uur en twee dagen geleden. De foutinhoud en
eventuele formulierwaarden zijn niet geopend.

Alleen Mailing en Werkgeverscontact vermelden `Advanced Form Integration`
expliciet als Elementor-actie. Toch heeft AFI ook voor de vier andere
formulieren een actieve providerintegratie. Het ontbreken van de widgetactie
is daarom op zichzelf geen bewijs dat de AFI-hook niet draait; het actuele
AFI-foutoverzicht is het relevante operationele signaal.

In het live scherm voor een nieuwe AFI-integratie zijn uitsluitend EngageBay
en EngageBay [PRO] als actieplatform beschikbaar. `Supabase` en `Webhook`
worden daar niet aangeboden. De huidige AFI-installatie kan daardoor niet
zonder nadere plugin-/licentiewijziging als Supabase-route worden gebruikt.

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
De exacte LIVE-formulierconfiguratie is daarom via de bestaande ingelogde
WordPress-sessie gelezen. Er is niets opgeslagen of gewijzigd.

## Eerstvolgende veilige stap

1. Maak per formulier een tabel met bronveld, betekenis, verplichtheid,
   validatie, uploadgedrag, Supabase-doel en Appsmith-weergave.
2. Leg ontbrekende doelen en beslispunten aan de gebruiker voor.
3. Ontwerp daarna pas de WordPress-outbox en beveiligde server-side
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
