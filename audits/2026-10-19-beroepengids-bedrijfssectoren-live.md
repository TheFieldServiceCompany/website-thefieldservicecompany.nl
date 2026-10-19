# Beroepengids-bedrijfssectoren als artikelarchief ingericht — LIVE

Datum: 19 oktober 2026
Taxonomie: `beroep_bedrijfssector`
Omvang: 33 bestaande termen

## Uitgangspunt

Iedere sectorpagina is een compact artikelarchief en geen volledig
sectorartikel. De bestaande lange descriptions van vacaturetaxonomie
`bedrijfssector` zijn gebruikt om de techniek, machines en werkomgeving per
sector correct te interpreteren. De Beroepengids-teksten zijn afzonderlijk
geschreven en niet gekopieerd.

Per term is toegepast:

1. archieftitel `Bedrijfssector [term] | Artikelen`;
2. unieke, uitnodigende intro met directe verbinding naar field service;
3. H2 `Bedrijfssector [term]` zonder `| Artikelen`;
4. drie unieke compacte alinea's over de sector, toekomstige artikelen en het
   vergelijken van functies en vacatures.

## Alleen-lezen preflight

Voor publicatie is bevestigd:

- bron en LIVE bevatten exact dezelfde 33 namen en slugs;
- alle 33 termen hebben parent `0`;
- alle 33 doel-descriptions waren leeg;
- de bestaande Pods-velden `intro_taxonomy` en `pods_archive_title` zijn
  hergebruikt;
- de bestaande relatie `relationship_bedrijfssector` is niet gewijzigd.

## Publicatie

Descriptions zijn via de standaard WordPress REST API opgeslagen. Intro's en
archieftitels zijn via een tijdelijk beheerders-only REST-endpoint in de
bestaande Pods-velden geplaatst. De routine maakte vooraf een herstelbestand,
controleerde de volledige termset en kon descriptions en metadata bij iedere
afwijking terugzetten.

Herstelbestand:

`audits/exports/beroep-bedrijfssectoren-before-archive-content-2026-10-19.json`

Resultaat:

- descriptions gewijzigd: 33;
- intro's gewijzigd: 33;
- archieftitels gewijzigd: 33;
- descriptions exact geverifieerd: 33 van 33;
- intro's exact geverifieerd: 33 van 33;
- archieftitels exact geverifieerd: 33 van 33;
- tijdelijke snippet verwijderd: ja.

De descriptions bevatten 100–125 woorden, exact één H2 en drie alinea's. De
intro's bevatten 17–24 woorden.

## Openbare controle

De gerenderde archieven van Aerospace & Luchtvaart, Machinebouw,
Semiconductor, Watertechnologie en HVAC & Klimaattechniek zijn gecontroleerd.
De pagina's gaven HTTP 200 en toonden de juiste H1 met `| Artikelen` en de
juiste H2 zonder die suffix.

Er zijn geen termen, slugs, parents, relaties, Pods-definities, velden,
vacatures, Beroepengidsartikelen of template-instellingen gewijzigd.

## Bronnen

- canonieke content:
  `content/beroepengids-descriptions/beroep-bedrijfssectoren.md`;
- schrijf- en publicatieregels:
  `content/education-descriptions/reusable-taxonomy-text-prompt.md`;
- contentvalidatie: `scripts/build-beroep-bedrijfssectoren.mjs`;
- preflight, backup, publicatie, verificatie en rollback:
  `scripts/publish-beroep-bedrijfssectoren.py`.
