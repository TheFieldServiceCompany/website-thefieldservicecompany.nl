# LIVE-uitvoering — Reisduur en Weekenden thuis

Datum: 19 oktober 2026  
Omgeving: LIVE WordPress  
Status: canonieke structuur uitgevoerd en via publieke REST gecontroleerd

## Uitgevoerde wijzigingen

- De bestaande interne taxonomie `reisprofiel` is behouden. Alleen de
  beheerlabels zijn aangepast naar `Reisduur`.
- De nieuwe taxonomie `weekenden-thuis` is aangemaakt met beheerlabel
  `Weekenden thuis`.
- Beide taxonomieën zijn openbaar, openbaar opvraagbaar, hiërarchisch,
  gekoppeld aan post type `vacature` en beschikbaar via WordPress REST.
- De eerste uitvoering was additive. Later op 19 oktober zijn de 25 oude
  Reisprofiel-termen na expliciet akkoord verwijderd.
- Vacature `23277` zelf is niet verwijderd. Door verwijdering van de oude
  termen heeft hij geen Reisduur-relatie meer; er is niets automatisch naar
  een nieuwe waarde omgezet.

### Reisduur in `reisprofiel`

| ID | Term | Slug | Gebruik na uitvoering |
| ---: | --- | --- | ---: |
| 625 | Elke nacht thuis | `elke-nacht-thuis` | 0 |
| 626 | 2–7 dagen | `2-7-dagen` | 0 |
| 631 | 8–14 dagen | `8-14-dagen` | 0 |
| 627 | 15–30 dagen | `15-30-dagen` | 0 |
| 632 | meer dan 30 dagen | `meer-dan-30-dagen` | 0 |

Iedere term heeft een korte operationele description die de meetbare band
uitlegt. De oorspronkelijke 25 legacytermen zijn later op 19 oktober na
expliciet akkoord verwijderd. Vacature `23277` bleef bestaan, maar heeft na
die termverwijdering geen Reisduur-relatie en moet inhoudelijk opnieuw worden
geclassificeerd.

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

### Hotel nachten/maand

De taxonomie `hotel-nachten-maand` is later dezelfde dag LIVE toegevoegd met
de waarden 0, 1–7, 8–14 en 15–31. De term-ID's zijn respectievelijk 635, 636,
637 en 638. Alle tellingen waren bij de REST-controle 0.

## Search & Filter

Drie ingeschakelde velden zijn aan de bestaande query `Vacatures` toegevoegd:

| Veld-ID | Naam | Bron | Toegestane termen | Matchwijze |
| ---: | --- | --- | --- | --- |
| 14 | Reisduur | `reisprofiel` | uitsluitend de vijf nieuwe duurbanden | any |
| 15 | Weekenden thuis | `weekenden-thuis` | uitsluitend 0%, 25%, 50%, 75%, 100% | any |
| 16 | Hotel nachten/maand | `hotel-nachten-maand` | uitsluitend 0, 1–7, 8–14, 15–31 | any |

De velden gebruiken checkboxes en `match any`. De velden bestaan nu in Search
& Filter, maar zijn niet in een Elementor-layout geplaatst; die koppeling doet
de gebruiker handmatig. Omdat alle nieuwe termen nog
`count = 0` hebben, leveren de filters pas resultaten op nadat vacatures
inhoudelijk zijn gevalideerd en gekoppeld.

## Verificatie

De publieke REST-controle gaf:

- `reisprofiel`: naam `Reisduur`, REST-basis `reisprofiel`, gekoppeld aan
  `vacature`;
- `weekenden-thuis`: naam en REST-basis `Weekenden thuis` /
  `weekenden-thuis`, gekoppeld aan `vacature`;
- alle canonieke Reisduur-, Weekenden-thuis- en Hotel-nachten-termen kwamen
  terug met de bedoelde slug en description;
- alle nieuwe termen hadden nog telling 0;
- de later uitgevoerde REST-controle gaf voor `reisprofiel` alleen de vijf
  canonieke Reisduur-termen terug.

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
- De live Appsmith-editor stond bij controle ten onrechte op `main` en is
  overgeschakeld naar `crm-layout-redesign`. Daarna werd de actuele
  `Contacts`-opbouw geladen.
- De pullactie was op `crm-layout-redesign` uitgeschakeld. In het
  commitvenster waren de committekst en commitactie eveneens uitgeschakeld en
  werd geen wijzigingslijst getoond. Daarmee was er niets te pullen of te
  committen en liep de branch al gelijk met GitHub.
- Er is niet gedeployd.

## Nog niet uitgevoerd

- Geen automatische mapping of wijziging van bestaande vacatures.
- Redirects en eventuele herbestemming van oude Reisprofiel-URL's blijven
  inhoudelijk te beoordelen; de legacytermen zelf zijn verwijderd.
- Geen plaatsing van de nieuwe Search & Filter-velden in Elementor.
- Geen Appsmith-deployment. Alleen de editorbranch is naar de correcte branch
  `crm-layout-redesign` gezet; de inhoudelijke live test van de nieuwe
  travelvelden volgt nog.
- Geen wijziging aan de bestaande kandidaatreisrecords in Supabase.

