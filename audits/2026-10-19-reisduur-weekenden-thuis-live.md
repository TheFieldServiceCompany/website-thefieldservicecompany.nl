# LIVE-uitvoering — Reisduur en Weekenden thuis

Datum: 19 oktober 2026  
Omgeving: LIVE WordPress  
Status: additive structuur uitgevoerd en via publieke REST gecontroleerd

## Uitgevoerde wijzigingen

- De bestaande interne taxonomie `reisprofiel` is behouden. Alleen de
  beheerlabels zijn aangepast naar `Reisduur`.
- De nieuwe taxonomie `weekenden-thuis` is aangemaakt met beheerlabel
  `Weekenden thuis`.
- Beide taxonomieën zijn openbaar, openbaar opvraagbaar, hiërarchisch,
  gekoppeld aan post type `vacature` en beschikbaar via WordPress REST.
- Er zijn geen bestaande termen, URL's of vacaturekoppelingen verwijderd of
  automatisch omgezet.
- Vacature `23277` is niet gewijzigd.

### Reisduur in `reisprofiel`

| ID | Term | Slug | Gebruik na uitvoering |
| ---: | --- | --- | ---: |
| 625 | Elke nacht thuis | `elke-nacht-thuis` | 0 |
| 626 | 2–7 dagen | `2-7-dagen` | 0 |
| 631 | 8–14 dagen | `8-14-dagen` | 0 |
| 627 | 15–30 dagen | `15-30-dagen` | 0 |
| 632 | 31+ dagen | `31-plus-dagen` | 0 |

Iedere term heeft een korte operationele description die de meetbare band
uitlegt. De oorspronkelijke 25 legacytermen staan nog in dezelfde taxonomie.

### Weekenden thuis in `weekenden-thuis`

| ID | Term | Slug | Gebruik na uitvoering |
| ---: | --- | --- | ---: |
| 628 | 0% | `0-procent` | 0 |
| 629 | 25% | `25-procent` | 0 |
| 633 | 50% | `50-procent` | 0 |
| 634 | 75% | `75-procent` | 0 |
| 630 | 100% | `100-procent` | 0 |

Iedere term heeft een korte operationele description. `Onbekend` wordt niet
als term opgeslagen; een ontbrekende waarde blijft de lege datastatus.

## Search & Filter

Twee ingeschakelde velden zijn aan de bestaande query `Vacatures` toegevoegd:

| Veld-ID | Naam | Bron | Toegestane termen | Matchwijze |
| ---: | --- | --- | --- | --- |
| 14 | Reisduur | `reisprofiel` | uitsluitend de vijf nieuwe duurbanden | any |
| 15 | Weekenden thuis | `weekenden-thuis` | uitsluitend 0%, 25%, 50%, 75%, 100% | any |

Beide velden gebruiken checkboxes en automatisch verzenden. Tri-state is
uitgeschakeld. De velden bestaan nu in Search & Filter, maar zijn in deze stap
niet in een Elementor-layout geplaatst. Omdat alle nieuwe termen nog
`count = 0` hebben, leveren de filters pas resultaten op nadat vacatures
inhoudelijk zijn gevalideerd en gekoppeld.

## Verificatie

De publieke REST-controle gaf:

- `reisprofiel`: naam `Reisduur`, REST-basis `reisprofiel`, gekoppeld aan
  `vacature`;
- `weekenden-thuis`: naam en REST-basis `Weekenden thuis` /
  `weekenden-thuis`, gekoppeld aan `vacature`;
- alle tien nieuwe termen kwamen terug met de bedoelde slug en description;
- alle tien nieuwe termen hadden nog telling 0;
- de legacytermen en hun bestaande tellingen waren nog aanwezig.

## Appsmith-branchcontrole

Na de WordPress-uitvoering is de GitHub-branchhistorie van
`TheFieldServiceCompany/tfsc-appsmith-crm` opnieuw gecontroleerd.

- De gebruikte en actuele werkbranch is `crm-layout-redesign`.
- `document-preview-backfill` eindigt op `8dbceef` en is daarna met mergecommit
  `204e1ec` opgenomen in `crm-layout-redesign`; het is dus een historische
  voorouder en geen nieuwere of completere werkbasis.
- Commit `445b20d` verwijderde vervolgens bewust de oude pagina's `Candidates`
  en `Vacancies_Old` met bericht `Remove legacy candidate and vacancy pages`.
  Kandidaatfunctionaliteit zit daarna in de actuele Contacts/Contact Detail-
  opbouw.
- Na die verwijdering volgen op `crm-layout-redesign` nog commits voor de
  documentpreview-worker, originele en PDF-downloads, kandidaatvoorkeuren en
  de WordPress-opleidingsuitlijning.
- De laatste commit vóór de travelwijziging was `e709b22` (`data: align
  education taxonomies with WordPress`, 18 oktober 2026).
- De travelwijziging van deze sessie staat daar correct bovenop in `6f182a4`.

In deze stap is geen Appsmith-app gepulld, gedeployd of van branch gewisseld.

## Nog niet uitgevoerd

- Geen automatische mapping of wijziging van bestaande vacatures.
- Geen verwijdering of redirect van legacytermen.
- Geen plaatsing van de nieuwe Search & Filter-velden in Elementor.
- Geen wijziging aan de live Appsmith-app; de bronwijziging staat wel op de
  correcte branch `crm-layout-redesign`.
- Geen wijziging aan de bestaande kandidaatreisrecords in Supabase.

