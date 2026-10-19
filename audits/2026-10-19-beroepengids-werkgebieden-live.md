# Beroepengids-werkgebieden als artikelarchief ingericht — LIVE

Datum: 19 oktober 2026
Taxonomie: `beroep_werkgebied`
Omvang: 8 bestaande termen

## Uitgangspunt

Iedere termpagina is een compact artikelarchief over de geografische inzet van
field service functies. De bestaande descriptions van vacaturetaxonomie
`werkgebied` zijn gebruikt om de gebieden en vacaturecontext te interpreteren.
De Beroepengids-teksten zijn afzonderlijk geschreven en niet gekopieerd.

Per term is toegepast:

1. archieftitel `Werkgebied [term] | Artikelen`;
2. unieke, uitnodigende intro met directe verbinding naar field service;
3. H2 `Werkgebied [term]` zonder `| Artikelen`;
4. drie unieke compacte alinea's over het gebied, toekomstige artikelen en het
   vergelijken van functies en vacatures.

Een werkgebied zegt waar iemand inzetbaar is en niet automatisch hoe vaak of
hoe lang iemand reist. Internationale dagen, maximale reisduur, hotelnachten
en weekenden thuis zijn daarom bewust als afzonderlijke vacaturekenmerken
benoemd. Internationaal en Wereldwijd zijn niet kunstmatig als volledig
verschillende reisprofielen gepresenteerd; de concrete vacature blijft
doorslaggevend.

## Alleen-lezen preflight

Voor publicatie is bevestigd:

- bron en LIVE bevatten exact dezelfde 8 namen en slugs;
- alle 8 termen hebben parent `0`;
- alle 8 vacaturedescriptions waren gevuld en zijn uitsluitend als
  inhoudelijke bron gelezen;
- alle 8 doel-descriptions waren leeg;
- de bestaande Pods-velden `intro_taxonomy` en `pods_archive_title` zijn
  hergebruikt;
- de bestaande relatie `relationship_werkgebied` is niet gewijzigd.

## Publicatie

Descriptions zijn via de standaard WordPress REST API opgeslagen. Intro's en
archieftitels zijn via een tijdelijk beheerders-only REST-endpoint in de
bestaande Pods-velden geplaatst. De routine maakte vooraf een herstelbestand,
controleerde de volledige termset en kon descriptions en metadata bij iedere
afwijking terugzetten.

Herstelbestand:

`audits/exports/beroep-werkgebieden-before-archive-content-2026-10-19.json`

Resultaat:

- descriptions gewijzigd: 8;
- intro's gewijzigd: 8;
- archieftitels gewijzigd: 8;
- descriptions exact geverifieerd: 8 van 8;
- intro's exact geverifieerd: 8 van 8;
- archieftitels exact geverifieerd: 8 van 8;
- tijdelijke snippet verwijderd: ja; het tijdelijke endpoint gaf daarna 404.

De descriptions bevatten 110–118 woorden, exact één H2 en drie alinea's. De
intro's bevatten 19–22 woorden.

## Openbare controle

Alle acht gerenderde archieven zijn gecontroleerd: België, Benelux, Duitsland,
Europa, Internationaal, Luxemburg, Nederland en Wereldwijd. De pagina's gaven
HTTP 200 en toonden de juiste H1 met `| Artikelen` en de juiste H2 zonder die
suffix.

De actuele SEO-output van alle acht archieven bevat `follow, noindex` en geen
canonicaltag. Deze contentronde heeft geen SEO-velden of template-instellingen
gewijzigd; de canonicalstrategie voor losse vacaturetaxonomiearchieven blijft
een afzonderlijk open punt.

Er zijn geen termen, slugs, parents, relaties, Pods-definities, velden,
vacatures, Beroepengidsartikelen of template-instellingen gewijzigd.

## Bronnen

- canonieke content:
  `content/beroepengids-descriptions/beroep-werkgebieden.md`;
- schrijf- en publicatieregels:
  `content/education-descriptions/reusable-taxonomy-text-prompt.md`;
- contentvalidatie: `scripts/build-beroep-werkgebieden.mjs`;
- preflight, backup, publicatie, verificatie en rollback:
  `scripts/publish-beroep-werkgebieden.py`.
