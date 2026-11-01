# Vacatureterugval relationship_posts — 1 november 2026

Op verzoek: passende vacatures eerst, algemene Elementor-vacatureselectie bij
nul matches of ontbrekende relaties. Live Code Snippets-ID 204, versie 3.3,
scope global, actief. Geen artikelinhoud of templates gewijzigd.

## Implementatie

Alleen de algemene relatiecode voor uitsluitend post_type `vacature` krijgt
een terugval. De bestaande termarchieftak uit live 3.2 is behouden. De
bestaanscontrole neemt de oorspronkelijke queryvoorwaarden over en vraagt
één ID op zonder totaaltelling, zonder pagina-offset. Een probe-markering
voorkomt recursie via de Elementor-queryhook. Als er matches zijn, blijft de
bestaande AND-combinatie exact behouden. Bij nul matches wordt de extra
relatiebeperking niet toegevoegd. Ook bij ontbrekende Pods/velden/relaties
blijft voor vacatures de oorspronkelijke selectie staan.

## Verificatie

- 11 lokale PHP 8.3-gedragstests geslaagd, inclusief nul matches,
  ontbrekende Pods/velden/relaties, gemengde en andere Sources, behoud van
  statusuitsluiting en paginering, en geneste Elementor-aanroepen.
- Live REST-code teruggelezen en bytegelijk met de beheerde bron.
- Koeltechnieker (27838): publiek vóór 0, na 10 vacature-items.
- Maritieme sector (28895): de 5 bestaande vacature-items exact behouden.
- Servicemonteur (28539): de 10 bestaande vacature-items exact behouden.
- Field service engineer (27533): de 10 bestaande vacature-items exact behouden.
- HTTP 200 voor alle vier pagina’s; de volledige itemklassen inclusief
  vacature-ID’s zijn vóór/na vergeleken. Openbare koeltechniekerpagina na
  cacheverversing opnieuw gecontroleerd: 10 items, geen vac_status-ingevuld.
- Visueel bevestigd in browser: vacaturelijst zichtbaar op koeltechniekerartikel.
- Tijdelijke beheerdersdiagnostiek 357 en cacheverversing 358 gedeactiveerd en verwijderd.

Tijdens de eerste openbare controle bleek dat Elementor de probe opnieuw
via dezelfde hook aanriep (503). Dit is verholpen met de probe-markering;
daarna slaagden de vier openbare vergelijkingen. De eerste REST-contexttest
representeerde geen echte frontend-artikelcontext en geldt daarom niet als
bewijs van behoud van passende resultaten; de bovenstaande openbare
vóór/na-vergelijkingen zijn daarvoor leidend.

## Herstel

De exacte actieve code van vóór deze wijziging staat als ongewijzigde export
in wordpress/exports/code-snippets/2026-11-01-before-relationship-fallback/204.json.
Herstel uitsluitend het codeveld, naam en oorspronkelijke actieve status van
snippet 204 via Code Snippets REST; ververs daarna de paginacache. Controleer
vóór herstel op eventuele nieuwere wijzigingen.

Tests herhalen vanuit de repositoryroot met PHP 8.3:

```sh
php audits/tests/relationship-posts.php wordpress/snippets/php/204-related-posts-pods-relationships.php
```

De gedragstests gebruiken nagebootste WordPress-/Pods-objecten. De openbare
vóór/na-controles zijn daarnaast apart uitgevoerd op de echte website.
