# Appsmith-branches — alleen-lezen inventarisatie

Datum: 19 oktober 2026  
Repository: `TheFieldServiceCompany/tfsc-appsmith-crm`  
Actuele werkbranch: `crm-layout-redesign`  
Referentiecommit: `6f182a4`

## Doel

De repository bevat meerdere oude feature-, herstel- en backfillbranches. Deze
inventarisatie bepaalt uitsluitend hun positie ten opzichte van de actuele
werkbranch. Er is geen branch verwijderd.

## Behouden

| Branch | Reden |
| --- | --- |
| `crm-layout-redesign` | Actuele Appsmith-werkbranch; bevat de nieuwste functionele lijn en de travelwijziging |
| `main` | Standaardbranch; inhoudelijk achtergelopen, maar niet verwijderen tijdens branchopschoning |

## Volledig opgenomen in `crm-layout-redesign`

De headcommit van iedere onderstaande branch is een voorouder van
`crm-layout-redesign`. Deze branches bevatten volgens de commitgrafiek geen
unieke commits meer en zijn kandidaten voor latere verwijdering na één laatste
naam- en PR-controle:

- `candidate-document-archive-employer-review`;
- `candidate-technical-profile-fix`;
- `document-preview-backfill`;
- `vacancy-edit`;
- `vacancy-language-requirements`;
- `work/candidate-documents-company`;
- `work/contact-country-date-pickers`;
- `work/employment-company-links`;
- `work/general-contact-profile`.

`document-preview-backfill` is met commit `204e1ec` in de actuele branch
opgenomen. De daarna bewust verwijderde oude pagina's maken deze historische
branch niet tot de huidige basis.

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

## Veilige vervolgstappen

1. Openstaande PR's en branchgebruik in Appsmith controleren.
2. De vier unieke commits inhoudelijk vergelijken met de actuele pagina's.
3. Een definitieve lijst `behouden`, `reeds gemerged` en `unieke wijziging`
   tonen.
4. Alleen na expliciet akkoord de bevestigde remote branches verwijderen.
5. Na verwijdering de actuele werkbranch en standaardbranch opnieuw controleren.

