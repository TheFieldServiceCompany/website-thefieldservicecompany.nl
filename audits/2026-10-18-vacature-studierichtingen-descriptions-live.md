# Vacature-studierichtingen: descriptions LIVE

Datum: 18 oktober 2026  
Status: uitgevoerd, gecorrigeerd en gecontroleerd

## Definitief resultaat

De 23 nieuwe studierichtingteksten staan bij de vacaturetaxonomie
`vac_studierichting`. Alle 23 termen zijn na publicatie opnieuw via WordPress
REST gelezen en komen exact overeen met de lokale bron in
`content/education-descriptions/studierichtingen.md`.

De al bestaande teksten van de Beroepengids-taxonomie
`beroep_studierichting` zijn behouden. Een eerste foutieve publicatieronde naar
die taxonomie is volledig teruggedraaid met de exacte teksten uit Git-commit
`bba82c6`. Ook die 23 herstelresultaten zijn opnieuw via REST geverifieerd.

## Controlecijfers

- vacature-studierichtingen gevuld: 23 van 23;
- vacature-studierichtingen exact geverifieerd: 23 van 23;
- oorspronkelijke Beroepengids-descriptions hersteld: 23 van 23;
- Beroepengids-herstel exact geverifieerd: 23 van 23;
- resterende afwijkingen: 0.

## Frontendcontrole

Het openbare vacaturearchief van Autotechniek toont de vier nieuwe koppen,
waaronder de relatie tussen de opleiding Autotechniek en industriële machines.

Het Beroepengids-archief van Industriële Automatisering toont na herstel weer
de oorspronkelijke koppen, waaronder `Wat leer je binnen industriële
automatisering?`.

## Herstelbestanden

Direct voor de correctie zijn beide actuele reeksen lokaal opgeslagen:

- `work/correct-study-direction-targets-2026-10-18/vacature-before.json`;
- `work/correct-study-direction-targets-2026-10-18/beroepengids-before.json`.

De correctiescript had een gezamenlijke rollback: bij een fout zouden zowel de
vacaturetaxonomie als de Beroepengids-taxonomie naar de toestand van direct
voor de correctie zijn teruggezet.
