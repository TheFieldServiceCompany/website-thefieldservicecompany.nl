# Vaste buffer voor inhoudslinks — 1 november 2026

Na terugdraaiing van de scrollheader vroeg de gebruiker uitsluitend om ruimte
voor aangeklikte koppen onder de oorspronkelijke sticky header.

Gewijzigd via REST: header 17275, container 5b8468be, één instelling:
`sticky_anchor_link_offset` van ontbrekend/default 0 naar 90. De opgeslagen
Elementor-data is teruggelezen en structureel vergeleken: alleen deze
instelling verschilt. Overige meta ongewijzigd. Elementor- en LiteSpeed-cache
ververst. Snippet 359 is nog steeds inactief. Geen nieuwe scrollcode toegevoegd.

## Verificatie

- Browser, Beroepengids, desktop: TOC-link Welke metingen voert een
  koeltechnieker uit aangeklikt. scroll-margin-top 90 px, kopbovenrand
  circa 136 px, headeronderrand 102 px inclusief beheerbalk. Kop is zichtbaar.
- Browser, mobiel 390 px: TOC-link Hoe lost een koeltechnieker storingen op
  aangeklikt. scroll-margin-top 90 px, kopbovenrand circa 150 px,
  headeronderrand 50 px. Kop is zichtbaar. Viewport daarna hersteld.
- Publieke Kennisbank-HVAC- en Blog-recruitmentbureaupagina geven HTTP 200
  en dezelfde native headerinstelling 90 px, met sticky=top.
- Nieuwe scrollcontroller ontbreekt in openbare HTML en documentclass.

De 90 px betreft de ankerafstand; bestaande kopmarges komen daar bovenop.
Het is dus geen belofte dat de zichtbare kop precies op pixel 90 staat.

## Herstel

Oorspronkelijke header-meta:
wordpress/exports/elementor/2026-11-01-before-anchor-buffer/17275.json.
Voor normaal herstel alleen de ene instelling verwijderen/naar 0 zetten;
geen volledige templatekopie over nieuwere wijzigingen heen plaatsen.
