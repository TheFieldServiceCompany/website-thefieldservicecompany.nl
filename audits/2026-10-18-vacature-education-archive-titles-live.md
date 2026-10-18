# Vacatureopleidingen: generieke archieftitels LIVE

Datum: 18 oktober 2026  
Omgeving: `https://thefieldservicecompany.nl`

## Resultaat

Het Pods-veld `pods_archive_title` is voor alle vacaturetermen van de twee
opleidingstaxonomieën ingevuld met één vaste naamstructuur:

- `opleidingsniveau`: `Field service vacatures voor opleidingsniveau [term]`;
- `vac_studierichting`: `Field service vacatures voor studierichting [term]`.

Daarmee hebben alle vier opleidingsniveaus en alle 23 studierichtingen een
expliciete, onderwerpspecifieke archieftitel. De al correct ingevulde titel van
`WO / Universiteit` hoefde niet te worden gewijzigd. De overige 26 waarden
zijn via de gewone WordPress REST-routes bijgewerkt en daarna allemaal opnieuw
uitgelezen.

De gelijknamige Beroepengids-taxonomieën `beroep_opleidingsniveau` en
`beroep_studierichting` zijn niet gewijzigd.

## Herstelbaarheid van de contentwijziging

Voor de update zijn de oorspronkelijke waarden van alle 27 termen opgeslagen
in:

`work/vacancy-education-archive-titles-2026-10-18/before.json`

Het publicatiescript bevat rollback bij een schrijffout. Na de publicatie
bevestigde een tweede REST-uitlezing exact 4 van 4 opleidingsniveaus en 23 van
23 studierichtingen.

## Gevonden verschil in de live template

De bestaande shortcode `[combo_heading]` in actieve snippet 186 gebruikte
`pods_archive_title` al voor gewone taxonomiearchieven. De nieuwere
single-term-combo-URL's, waaronder
`/vacatures/vac_studierichting/[slug]/`, sloegen dit veld echter over. Daardoor
verscheen de nieuwe studierichtingstitel wel in het description-accordion,
maar nog niet in de H1.

Volgens de vaste veiligheidsafspraak is vóór de correctie een inactieve kopie
gemaakt:

- snippet 242;
- `Vac Combo Archives | Base Code [v4.5.3] [BACKUP 2026-10-18 vóór single-term archive title override]`.

Actieve snippet 186 is daarna bijgewerkt naar versie 4.5.4. De logica is
bewust beperkt:

1. betreft de combo-URL exact één geldige term;
2. en heeft die term een niet-lege `pods_archive_title`;
3. dan gebruikt `[combo_heading]` die waarde als H1;
4. bij een leeg veld en bij combinaties van meerdere termen blijft de
   bestaande titelopbouw ongewijzigd.

Code Snippets meldt na de update geen codefout.

## Functionele controle

De gerenderde frontend is na de REST-verificatie in een echte browsersessie
gecontroleerd:

- `/opleidingsniveau/hbo/` toont als H1
  `Field service vacatures voor opleidingsniveau HBO`;
- `/vacatures/vac_studierichting/industriele-automatisering/` toont als H1
  `Field service vacatures voor studierichting Industriële Automatisering`;
- de combinatie Industriële Automatisering + HBO behoudt de bestaande compacte
  combinatie-H1 `Vacatures Industriële Automatisering HBO`.

De vacaturelijsten en description-accordions bleven op alle drie de
testpagina's zichtbaar. De wijziging past geen termnamen, slugs, URL's,
breadcrumbs, descriptions, relaties of Rank Math-velden aan.
