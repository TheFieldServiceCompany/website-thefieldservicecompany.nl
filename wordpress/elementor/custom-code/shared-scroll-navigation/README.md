# Gedeelde scrollheader en vacaturepaneel

LIVE sinds 1 november 2026, versie 1.0.0. Uitvoering via Code Snippets **359**,
scope **front-end**; CSS via wp_head prioriteit 90, JavaScript via wp_footer
prioriteit 90. Geen Elementor Custom Code-post; deze map bevat de beheerde
frontendbronnen. Het uitvoerbare pakket staat in
`wordpress/snippets/php/359-scroll-navigation.php`.

## Gedrag en afhankelijkheden

- Headerdocument 17275, container 5b8468be; omlaag verbergen, omhoog tonen.
- Vacaturedocument 26022, container 9d55293 binnen scroll-sticky-wrapper;
  volgt de gemeten zichtbare headerhoogte en eventuele beheerbalk.
- De bestaande Elementor-stickylogica en vacaturetrigger blijven actief.
  De oorspronkelijke offsets 70/60/50 blijven beschikbaar als terugval.
- Een richtingsdrempel van 12 px voorkomt wisselen bij kleine bewegingen.
- Bij een open menu of focus binnen de header blijft deze zichtbaar.
  Tab herstelt de navigatie voordat focus verplaatst.
- Inhoudslinks gebruiken ruimte voor de zichtbare balken plus 20 px;
  tijdens een ankersprong blijft de header kort zichtbaar.
- Elementor-editor en pagina's met een andere header zijn uitgesloten.
- prefers-reduced-motion schakelt de toegevoegde animatie uit.
- De aparte mobiele sollicitatiebalk onderaan is niet gewijzigd.

Geen templates of pluginbestanden gewijzigd. Herstel: snippet 359 deactiveren
en paginacache verversen. Zie de audit en herstelkopieën:

- `audits/2026-11-01-shared-scroll-navigation.md`
- `wordpress/exports/elementor/2026-11-01-before-scroll-navigation/`

## Bronnen bijwerken

Pas JS/CSS aan en neem hun volledige inhoud over in de twee nowdoc-blokken
van snippet 359. Controleer JS-syntaxis, PHP-syntaxis en gedragstests vóór
plaatsen; vergelijk de actuele live code vóór iedere nieuwe schrijfactie.

Tests vanuit de repositoryroot:

```sh
node audits/tests/scroll-navigation.test.mjs
```
