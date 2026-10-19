# Uitvoeringslog — travelmodel, Appsmith en branchopschoning

Datum: 19 oktober 2026  
Omgevingen: LIVE WordPress, Supabase staging en Appsmith/GitHub  
Status: uitgevoerd en gecontroleerd; inhoudelijke vacaturevalidatie blijft open

## Definitief travelmodel

Vacature en kandidaat gebruiken in Supabase en Appsmith dezelfde vijf
veldnamen. Alleen de keuzewaarden van `International travel` verschillen per
recordsoort.

| Betekenis | WordPress | Supabase en Appsmith |
| --- | --- | --- |
| Internationaal reizen | Niet als aparte WordPress-taxonomie uitgevoerd | `International travel`; kandidaat: `International only`, `Acceptable`, `No`; vacature: `Yes`, `No`; leeg = onbekend |
| Internationale dagen per jaar | `Dagen internationaal` in publieke banden | `Max international days / year`, getal 0–366 |
| Maximale duur van één reis | `Reisduur` | `Maximum travel duration` |
| Weekenden thuis | `Weekenden thuis` | `Weekends home`, 0%, 25%, 50%, 75% of 100% |
| Hotelnachten per maand | `Hotel nachten/maand` | `Hotel nights/month`, getal 0–31 |

De definitieve Reisduur-banden zijn:

1. `Elke nacht thuis` / `Home every night`;
2. `2–7 dagen` / `2–7 days`;
3. `8–14 dagen` / `8–14 days`;
4. `15–30 dagen` / `15–30 days`;
5. `meer dan 30 dagen` / `More than 30 days`.

De interne waarde van de laatste band blijft `days_31_plus`; alleen het
zichtbare label is aangepast. `Reisfrequentie` is geen aparte dimensie.
Hotelnachten blijven wel apart meetbaar, omdat ook nationaal werk
hotelovernachtingen kan vereisen.

## Supabase uitgevoerd

Project: `tfsc-crm-ats-staging` (`bpfocgapqsrokmargsjr`).  
Migratie: `20261019151611_canonical_travel_fields_and_appsmith`.

De kandidaat- en vacaturetabellen gebruiken dezelfde canonieke kolomnamen:

- `international_travel`;
- `max_international_days_per_year`;
- `maximum_travel_duration`;
- `weekends_home_percentage`;
- `hotel_nights_per_month`.

De constraints beperken de waarden tot de hierboven vastgelegde keuzes en
getalbereiken. De Appsmith-read/savefuncties en directionele matching zijn
hierop aangepast. Onder andere de volgende functies zijn bijgewerkt:

- `appsmith_candidate_travel_workspot`;
- `appsmith_save_candidate_travel_workspot`;
- `appsmith_vacancy_travel`;
- `appsmith_save_vacancy_travel`;
- `appsmith_save_vacancy_travel_tab`;
- `appsmith_vacancy_matches`;
- `appsmith_vacancy_match_filter_options`.

Kandidaat `No` botst met vacature `Yes`; kandidaat `International only` botst
met vacature `No`; `Acceptable` kan beide matchen. De numerieke maxima en
Reisduur worden directioneel vergeleken. Lege waarden blijven onbekend.

De 1.773 bestaande kandidaatreisrecords en het ene bestaande
vacature-reisrecord zijn behouden. Er is niets automatisch geconverteerd of
verwijderd. Rollbacktests voor kandidaat en vacature zijn geslaagd.

## Appsmith uitgevoerd

Repository: `TheFieldServiceCompany/tfsc-appsmith-crm`.  
Actuele werkbranch: `crm-layout-redesign`.

Belangrijkste commits:

| Commit | Wijziging |
| --- | --- |
| `04e434e` | Canonieke travelvelden, Appsmith-formulieren, SQL-functies en matching uitgelijnd |
| `ef0e64c` | Travelvelden in het tweekoloms raster gecorrigeerd |
| `508a57e` | Kandidaat-documentpreviewwidgets visueel uitgelijnd zonder de wissellogica te veranderen |
| `d4876c7` | Appsmith-editorcommit voor kandidaat-travel |
| `fa909f2` | Appsmith/GitHub-merge op de actuele werkbranch |
| `47972ac` | Zichtbaar langste Reisduur-label gewijzigd naar `More than 30 days` |

De kandidaat- en vacatureformulieren tonen dezelfde vijf veldnamen. De layout
is gecontroleerd op het eerdere probleem waarbij `Hotel nights/month`,
`Maximum travel duration` en `Own vehicle required` elkaar overlapten.

### Documentpreview

`contact_docs_image` en `contact_docs_viewer` staan functioneel op dezelfde
previewpositie omdat hun visibility elkaar uitsluit: afbeeldingen gebruiken
de image-widget en overige ondersteunde documenten de viewer. De desktop-
coördinaten waren door latere layoutdrift niet meer gelijk. Alleen de
coördinaten van `contact_docs_image` zijn met commit `508a57e` weer gelijkgezet;
de bestaande previewlogica is niet gewijzigd.

## LIVE WordPress uitgevoerd en opnieuw gecontroleerd

### Reisduur

De taxonomie gebruikt intern nog steeds `reisprofiel`, maar bevat nu alleen de
vijf canonieke Reisduur-termen:

| ID | Actueel label | Slug | Telling bij controle |
| ---: | --- | --- | ---: |
| 625 | Elke nacht thuis | `elke-nacht-thuis` | 0 |
| 626 | 2–7 dagen | `2-7-dagen` | 0 |
| 631 | 8–14 dagen | `8-14-dagen` | 0 |
| 627 | 15–30 dagen | `15-30-dagen` | 0 |
| 632 | meer dan 30 dagen | `meer-dan-30-dagen` | 0 |

De 25 oude, overlappende Reisprofiel-termen zijn verwijderd nadat hiervoor
akkoord was gegeven. De bestaande gekoppelde vacature is niet verwijderd,
maar de verwijderde termrelaties bestaan vanzelfsprekend niet meer. De
alleen-lezen REST-controle van vacature `23277` toont nu geen Reisduur,
Weekenden thuis of Hotel nachten/maand. Die vacature moet later inhoudelijk
worden gevalideerd en opnieuw met de juiste canonieke waarden worden ingevuld.

Search & Filter-veld #14 bestaat voor `Reisduur`. Plaatsing in de zichtbare
Elementor-/filter-GUI is bewust niet gedaan; dat doet de gebruiker handmatig.

### Weekenden thuis

De taxonomie `weekenden-thuis` bevat 0%, 25%, 50%, 75% en 100%. Een onbekende
waarde is een leeg veld en geen zesde term. Search & Filter-veld #15 bestaat,
maar is niet handmatig in de frontend-GUI geplaatst.

### Hotel nachten/maand

Nieuwe LIVE-taxonomie/POD: ID `27639`, slug `hotel-nachten-maand`, gekoppeld
aan vacatures en publiek beschikbaar via REST.

| ID | Term | Slug | Telling bij controle |
| ---: | --- | --- | ---: |
| 635 | 0 | `0` | 0 |
| 636 | 1–7 | `1-7` | 0 |
| 637 | 8–14 | `8-14` | 0 |
| 638 | 15–31 | `15-31` | 0 |

Search & Filter-veld #16 `Hotel nachten/maand` is aangemaakt voor query
`Vacatures`, als checkboxveld met `match any`. Het veld is niet gekoppeld aan
een zichtbare GUI-layout; dat blijft een handmatige stap van de gebruiker.

### Dagen internationaal

Term ID `358` heet nu zichtbaar `Meer dan 200 dagen`. De bestaande slug
`vanaf-200-dagen` en het archiefadres zijn behouden. Bij de controle was de
term aan 18 vacatures gekoppeld.

## GitHub-branchopschoning

Vooraf is gecontroleerd dat er geen open pull requests waren. Daarna zijn de
volgende acht volledig opgenomen remote branches verwijderd:

- `candidate-document-archive-employer-review`;
- `candidate-technical-profile-fix`;
- `vacancy-edit`;
- `vacancy-language-requirements`;
- `work/candidate-documents-company`;
- `work/contact-country-date-pickers`;
- `work/employment-company-links`;
- `work/general-contact-profile`.

Na een nieuwe remote controle blijven exact deze zeven branches bestaan:

- `main`;
- `crm-layout-redesign`;
- `document-preview-backfill` — gemergede, maar bewust bewaarde actieve
  backfillbranch/worktree;
- `candidate-technical-profile` — unieke commit `6ee6364`;
- `recovery/vacancy-detail-local-20261013` — unieke commit `f0d702c`;
- `work/codex-vacancy-wysiwyg-20261013` — unieke commit `b2dba2a`;
- `work/company-state-review-20261012` — unieke commit `ebefab2`.

De vier branches met een unieke commit worden niet verwijderd voordat hun
diff inhoudelijk is beoordeeld. `document-preview-backfill` wordt niet
opgeruimd zolang de afzonderlijke backfillchat/worktree die branch nog nodig
heeft.

## Nog open

1. De zichtbare Search & Filter-velden handmatig in de gewenste frontend-GUI
   plaatsen.
2. Vacatures, waaronder `23277`, inhoudelijk valideren en daarna pas de
   canonieke Reisduur-, Weekenden-thuis- en Hotel-nachtenwaarden invullen.
3. De bandmapping tussen WordPress `Dagen internationaal` en het numerieke
   Supabaseveld formeel vastleggen en end-to-end testen.
4. De volledige WordPress–Supabase–Appsmith-keuzematrix voor onder andere
   Werkgebieden, Opleidingsniveau en Studierichting afronden.
5. De vier overgebleven Appsmith-branches met unieke commits inhoudelijk
   beoordelen voordat verdere branchverwijdering wordt overwogen.

