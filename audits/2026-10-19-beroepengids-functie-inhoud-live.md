# Beroepengids-functie-inhoud als artikelarchief ingericht — LIVE

Datum: 19 oktober 2026
Taxonomie: `beroep_functie-inhoud`
Omvang: 29 bestaande termen

## Uitgangspunt

Iedere termpagina is een compact artikelarchief over een werkzaamheid of
verantwoordelijkheidsgebied binnen field service. De bestaande descriptions
van vacaturetaxonomie `functie-inhoud` zijn gebruikt om de betekenis en
technische context correct te interpreteren. De Beroepengids-teksten zijn
afzonderlijk geschreven en niet gekopieerd.

Per term is toegepast:

1. archieftitel `Functie-inhoud [term] | Artikelen`;
2. unieke, uitnodigende intro met directe verbinding naar field service;
3. H2 `Functie-inhoud [term]` zonder `| Artikelen`;
4. drie unieke compacte alinea's over de werkzaamheid, toekomstige artikelen
   en het vergelijken van functies en vacatures.

Overlappende vacaturetermen, zoals Commissioning en Inbedrijfstellen of
Diagnose en Troubleshooting, zijn niet kunstmatig als verschillende beroepen
gepresenteerd. De description benoemt het overlapgebied en laat de inhoud van
de concrete vacature doorslaggevend zijn.

## Alleen-lezen preflight

Voor publicatie is bevestigd:

- bron en LIVE bevatten exact dezelfde 29 namen en slugs;
- alle 29 termen hebben parent `0`;
- alle 29 doel-descriptions waren leeg;
- de bestaande Pods-velden `intro_taxonomy` en `pods_archive_title` zijn
  hergebruikt;
- de bestaande relatie `relationship_functie-inhoud` is niet gewijzigd.

## Publicatie

Descriptions zijn via de standaard WordPress REST API opgeslagen. Intro's en
archieftitels zijn via een tijdelijk beheerders-only REST-endpoint in de
bestaande Pods-velden geplaatst. De routine maakte vooraf een herstelbestand,
controleerde de volledige termset en kon descriptions en metadata bij iedere
afwijking terugzetten.

Herstelbestand:

`audits/exports/beroep-functie-inhoud-before-archive-content-2026-10-19.json`

Resultaat:

- descriptions gewijzigd: 29;
- intro's gewijzigd: 29;
- archieftitels gewijzigd: 29;
- descriptions exact geverifieerd: 29 van 29;
- intro's exact geverifieerd: 29 van 29;
- archieftitels exact geverifieerd: 29 van 29;
- tijdelijke snippet verwijderd: ja; het tijdelijke endpoint gaf daarna 404.

De descriptions bevatten 100–113 woorden, exact één H2 en drie alinea's. De
intro's bevatten 19–24 woorden.

## Openbare controle

De gerenderde archieven van Commissioning, Diagnose, Field service, PLC
Programmeren en Technical Support zijn gecontroleerd. De pagina's gaven HTTP
200 en toonden de juiste H1 met `| Artikelen` en de juiste H2 zonder die
suffix.

Er zijn geen termen, slugs, parents, relaties, Pods-definities, velden,
vacatures, Beroepengidsartikelen of template-instellingen gewijzigd.

## Bronnen

- canonieke content:
  `content/beroepengids-descriptions/beroep-functie-inhoud.md`;
- schrijf- en publicatieregels:
  `content/education-descriptions/reusable-taxonomy-text-prompt.md`;
- contentvalidatie: `scripts/build-beroep-functie-inhoud.mjs`;
- preflight, backup, publicatie, verificatie en rollback:
  `scripts/publish-beroep-functie-inhoud.py`.
