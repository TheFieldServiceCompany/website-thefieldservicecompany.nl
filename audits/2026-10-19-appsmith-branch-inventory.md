# Appsmith-branches — inventarisatie en uitgevoerde opschoning

Datum: 19 oktober 2026  
Repository: `TheFieldServiceCompany/tfsc-appsmith-crm`  
Actuele werkbranch: `crm-layout-redesign`  
Referentiecommit: `47972ac`

## Doel

De remote commitgrafiek en open pull requests zijn eerst alleen-lezen
gecontroleerd. Er waren geen open pull requests. Alleen branches waarvan de
head volledig in `crm-layout-redesign` was opgenomen, kwamen voor verwijdering
in aanmerking. Branches met een unieke commit en de actieve backfillbranch zijn
behouden.

## Behouden

| Branch | Reden |
| --- | --- |
| `crm-layout-redesign` | Actuele Appsmith-werkbranch; remote head `47972ac` |
| `main` | Standaardbranch; niet verwijderen |
| `document-preview-backfill` | Gemerged, maar nog bewust in gebruik door de afzonderlijke backfillchat/worktree |

## Verwijderd van GitHub

De volgende branches bevatten geen unieke commit meer en zijn na expliciet
akkoord van GitHub verwijderd:

- `candidate-document-archive-employer-review`;
- `candidate-technical-profile-fix`;
- `vacancy-edit`;
- `vacancy-language-requirements`;
- `work/candidate-documents-company`;
- `work/contact-country-date-pickers`;
- `work/employment-company-links`;
- `work/general-contact-profile`.

`document-preview-backfill` is met commit `204e1ec` in de actuele branch
opgenomen. Commit `445b20d` verwijderde daarna bewust de oude pagina's
`Candidates` en `Vacancies_Old`. De branch is dus niet de huidige basis, maar
blijft bestaan zolang de afzonderlijke backfilltaak hem nog nodig heeft.

## Eerst handmatig beoordelen

Vier branches zijn van de actuele lijn afgesplitst en bevatten elk precies één
commit die niet in `crm-layout-redesign` zit:

| Branch | Unieke headcommit | Omschrijving |
| --- | --- | --- |
| `candidate-technical-profile` | `6ee6364` | `final commit before redesign` |
| `recovery/vacancy-detail-local-20261013` | `f0d702c` | `Preserve local Vacancy Detail changes` |
| `work/codex-vacancy-wysiwyg-20261013` | `b2dba2a` | `Add vacancy number detail and rich text editor` |
| `work/company-state-review-20261012` | `ebefab2` | `Preserve Appsmith-generated Companies widget state for review` |

Deze vier branches worden pas verwijderd nadat hun unieke diff inhoudelijk is
beoordeeld en expliciet is besloten of de commit vervallen is, al elders is
gerealiseerd of alsnog moet worden overgenomen.

## Verificatie en vervolgstappen

Een nieuwe remote controle bevestigde dat nog exact zeven branches bestaan:
`main`, `crm-layout-redesign`, `document-preview-backfill` en de vier branches
met unieke commits uit de tabel hierboven.

1. De vier unieke commits inhoudelijk vergelijken met de actuele pagina's.
2. Per branch besluiten of de wijziging vervallen, elders gerealiseerd of
   alsnog over te nemen is.
3. Pas na nieuw expliciet akkoord een volgende remote branch verwijderen.

