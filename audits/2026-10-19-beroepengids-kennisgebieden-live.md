# Beroepengids-kennisgebieden als artikelarchief ingericht — LIVE

Datum: 19 oktober 2026
Taxonomie: `beroep_kennisgebied`
Omvang: 25 bestaande termen

## Uitgangspunt

Iedere termpagina is een compact artikelarchief over technische kennis die in
verschillende field service functies kan terugkomen. De bestaande descriptions
van vacaturetaxonomie `kennisgebied` zijn gebruikt om de techniek en toepassing
per term correct te interpreteren. De Beroepengids-teksten zijn afzonderlijk
geschreven en niet gekopieerd.

Per term is toegepast:

1. archieftitel `Kennisgebied [term] | Artikelen`;
2. unieke, uitnodigende intro met directe verbinding naar field service;
3. H2 `Kennisgebied [term]` zonder `| Artikelen`;
4. drie unieke compacte alinea's over het kennisgebied, toekomstige artikelen
   en het vergelijken van functies en vacatures.

Verwante domeinen, zoals Besturingstechniek, PLC Techniek en Industrial
Automation of Motion Control en Servotechniek, zijn in hun technische
samenhang beschreven. De concrete vacature blijft bepalend voor platformkennis,
bevoegdheden en vereiste ervaring.

## Alleen-lezen preflight

Voor publicatie is bevestigd:

- bron en LIVE bevatten exact dezelfde 25 namen en slugs;
- alle 25 termen hebben parent `0`;
- alle 25 vacaturedescriptions waren gevuld en zijn uitsluitend als
  inhoudelijke bron gelezen;
- alle 25 doel-descriptions waren leeg;
- de bestaande Pods-velden `intro_taxonomy` en `pods_archive_title` zijn
  hergebruikt;
- de bestaande relatie `relationship_kennisgebied` is niet gewijzigd.

## Publicatie en rollbacktest

Descriptions zijn via de standaard WordPress REST API opgeslagen. Intro's en
archieftitels zijn via een tijdelijk beheerders-only REST-endpoint in de
bestaande Pods-velden geplaatst. De routine maakte vooraf een herstelbestand,
controleerde de volledige termset en kon descriptions en metadata bij iedere
afwijking terugzetten.

De eerste uitvoering signaleerde dat WordPress een ampersand in drie intro's
als HTML-entiteit teruggaf. Omdat dit niet exact gelijk was aan de bron, brak
de eindcontrole af. De routine herstelde alle 25 descriptions en alle metadata.
Een onafhankelijke controle bevestigde daarna 25 lege descriptions en een 404
op het verwijderde tijdelijke endpoint. De drie intro's zijn zonder ampersand
geformuleerd, opnieuw gevalideerd en daarna in één volledige tweede uitvoering
gepubliceerd.

Herstelbestand:

`audits/exports/beroep-kennisgebieden-before-archive-content-2026-10-19.json`

Definitief resultaat:

- descriptions gewijzigd: 25;
- intro's gewijzigd: 25;
- archieftitels gewijzigd: 25;
- descriptions exact geverifieerd: 25 van 25;
- intro's exact geverifieerd: 25 van 25;
- archieftitels exact geverifieerd: 25 van 25;
- tijdelijke snippet verwijderd: ja; het tijdelijke endpoint gaf daarna 404.

De descriptions bevatten 100–110 woorden, exact één H2 en drie alinea's. De
intro's bevatten 17–21 woorden.

## Openbare controle

De gerenderde archieven van Elektrotechniek, Mechatronica, Profibus &
Ethernet, Rotating Equipment en Vacuümtechniek zijn gecontroleerd. De pagina's
gaven HTTP 200 en toonden de juiste H1 met `| Artikelen` en de juiste H2 zonder
die suffix.

De actuele SEO-output van deze vijf archieven bevat `follow, noindex` en geen
canonicaltag. Deze contentronde heeft geen SEO-velden of template-instellingen
gewijzigd; de canonicalstrategie voor losse vacaturetaxonomiearchieven blijft
een afzonderlijk open punt.

Er zijn geen termen, slugs, parents, relaties, Pods-definities, velden,
vacatures, Beroepengidsartikelen of template-instellingen gewijzigd.

## Bronnen

- canonieke content:
  `content/beroepengids-descriptions/beroep-kennisgebieden.md`;
- schrijf- en publicatieregels:
  `content/education-descriptions/reusable-taxonomy-text-prompt.md`;
- contentvalidatie: `scripts/build-beroep-kennisgebieden.mjs`;
- preflight, backup, publicatie, verificatie en rollback:
  `scripts/publish-beroep-kennisgebieden.py`.
