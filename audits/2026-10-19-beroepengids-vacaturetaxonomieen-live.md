# Beroepengids-taxonomieën gelijkgetrokken met vacatures — LIVE

Datum: 19 oktober 2026  
Omgeving: `https://thefieldservicecompany.nl`  
Uitvoering: WordPress REST API, zonder GUI

## Afgesproken scope

Alleen de termen en bestaande relaties van deze vijf taxonomieparen zijn
gelijkgetrokken:

| Vacaturetaxonomie | Beroepengids-taxonomie | Bestaand relatieveld |
|---|---|---|
| `bedrijfssector` | `beroep_bedrijfssector` | `relationship_bedrijfssector` |
| `functie-inhoud` | `beroep_functie-inhoud` | `relationship_functie-inhoud` |
| `functienaam` | `beroep_functienaam` | `relationship_beroep_functienaam` |
| `kennisgebied` | `beroep_kennisgebied` | `relationship_kennisgebied` |
| `werkgebied` | `beroep_werkgebied` | `relationship_werkgebied` |

Niet uitgevoerd:

- geen descriptions, intro's, archieftitels of SEO-teksten ingevuld;
- geen Pods, velden of taxonomieconfiguraties gemaakt of gewijzigd;
- geen Elementor- of Search & Filter-GUI gewijzigd;
- geen Beroepengidsartikelen of vacatures inhoudelijk aangepast;
- geen Supabase- of Appsmith-wijzigingen uitgevoerd.

## Preflight

De live REST-schema-export bevestigde vooraf dat alle vijf relatievelden al
bestonden en naar de juiste vacaturetaxonomie wezen. De bronsets waren vlak:
alle brontermen hadden `parent = 0`.

Voor uitvoering bevatte alleen `beroep_functienaam` al één term:

- ID `437`, slug `field-service-engineer`;
- doelnaam `field service engineer`;
- bronnaam `Field Service Engineer`;
- de bestaande relatie naar vacatureterm ID `226` was al correct.

Er waren geen onverwachte doeltermen en geen slugconflicten.

## Uitvoering

Via de standaard WordPress REST-routes zijn 126 ontbrekende termen aangemaakt.
Per nieuwe term zijn bewust uitsluitend `name` en `slug` verzonden. De naam
van de bestaande term ID `437` is gelijkgetrokken naar `Field Service
Engineer`; slug, artikelrelatie en overige inhoud zijn behouden.

| Beroepengids-taxonomie | Aangemaakt | Bestaand behouden | Eindtotaal |
|---|---:|---:|---:|
| `beroep_bedrijfssector` | 33 | 0 | 33 |
| `beroep_functie-inhoud` | 29 | 0 | 29 |
| `beroep_functienaam` | 31 | 1 | 32 |
| `beroep_kennisgebied` | 25 | 0 | 25 |
| `beroep_werkgebied` | 8 | 0 | 8 |
| **Totaal** | **126** | **1** | **127** |

Voor het vullen van uitsluitend de bestaande Pods-relatievelden is via de
Code Snippets REST API tijdelijk een beheerders-only REST-endpoint geactiveerd.
Dat endpoint voerde eerst een volledige set- en conflictcontrole uit, vulde de
lege relaties op slug en controleerde daarna opnieuw iedere relatie. De
tijdelijke snippet is na de controle gedeactiveerd en verwijderd.

Resultaat relaties:

- 126 lege relaties ingevuld;
- 1 bestaande correcte relatie ongewijzigd behouden;
- 127 van 127 relaties verwijzen naar de vacatureterm met dezelfde slug.

## Eindcontrole

Een nieuwe, onafhankelijke alleen-lezen REST-controle bevestigde:

- bron- en doelsets hebben per paar exact dezelfde slugs;
- bron- en doeltermen hebben exact dezelfde weergegeven namen;
- aantallen zijn respectievelijk 33, 29, 32, 25 en 8;
- alle 127 relaties matchen de juiste vacatureterm;
- alle vijf doelsets bevatten **0** ingevulde term-descriptions;
- de tijdelijke uitvoeringssnippet is verwijderd.

De herhaalbare routine staat in
`scripts/sync-beroepengids-vacancy-taxonomies.py`. Zonder `--apply` voert deze
alleen preflight- en setcontroles uit.
