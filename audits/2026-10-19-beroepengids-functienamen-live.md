# Beroepengids-functienamen als artikelarchief ingericht — LIVE

Datum: 19 oktober 2026  
Taxonomie: `beroep_functienaam`  
Omvang: 32 bestaande termen

## Definitief inhoudelijk uitgangspunt

Een functienaamterm in de Beroepengids is een artikelarchief. De description
is daarom geen volledig beroepsartikel en bevat geen vier uitgebreide
zoekvragen. Vragen als wat iemand doet, welke opleiding nodig is, wat het
salaris is of hoe reizen eruitziet worden later zelfstandige artikelen die in
het bijbehorende archief verschijnen.

Iedere term gebruikt nu dezelfde duidelijke archiefstructuur:

1. archieftitel `Functienaam [term] | Artikelen`;
2. korte uitnodigende intro met een directe verbinding naar field service;
3. H2 `Functienaam [term]` boven de description, zonder `| Artikelen`;
4. grove introductie op de functienaam en mogelijke variatie;
5. verwijzing naar toekomstige artikelen en relevante onderwerpen;
6. uitleg hoe die artikelen helpen functies en vacatures te vergelijken.

De teksten zijn per functienaam uniek. Overlappende titels worden niet
kunstmatig uit elkaar getrokken. `Service Monteur` en `Servicemonteur`,
`Internationaal Service Monteur` en `Internationaal Servicemonteur`, en `Site
Manager` en `Sitemanager` zijn correct als schrijfvarianten behandeld.

## Uitgevoerde livewijzigingen

- 32 bestaande term-descriptions herschreven naar drie compacte alinea's;
- 32 bestaande velden `intro_taxonomy` herschreven;
- 32 bestaande velden `pods_archive_title` ingesteld op het goedgekeurde
  patroon;
- later uitsluitend `Functienaam [term]` als H2 boven alle 32 bestaande
  descriptions toegevoegd;
- de eerdere vier H2-vragen per description volledig verwijderd;
- geen termen, namen, slugs, parents, relaties, Pods, velden, vacatures,
  Beroepengidsartikelen, handmatige links of template-instellingen gewijzigd.

De definitieve descriptions bevatten inclusief H2 107–147 woorden. De intro's bevatten
21–38 woorden. Iedere description verwijst naar de artikelen in het archief
en iedere intro en description maakt de relatie met field service.

## Bestaande structuur bevestigd

De alleen-lezen controle bevestigde vooraf:

- 32 bronitems en exact dezelfde 32 live slugs en namen;
- alle termen hebben parent `0`;
- de bestaande velden `intro_taxonomy` en `pods_archive_title` zijn aanwezig;
- `relationship_beroep_functienaam` koppelt naar vacaturetaxonomie
  `functienaam`;
- er hoefde geen Pod, veld of relatie te worden aangelegd of gewijzigd.

## Backups en herstel

De toestand direct vóór de definitieve archiefherschrijving staat in:

`audits/exports/beroep-functienamen-before-archive-rewrite-2026-10-19.json`

Eerdere herstelpunten van dezelfde werksessie zijn behouden in:

- `audits/exports/beroep-functienamen-before-content-2026-10-19.json`;
- `audits/exports/beroep-functienamen-before-inline-markup-fix-2026-10-19.json`.

Voor de latere H2-toevoeging zijn deze herstelpunten gemaakt:

- `audits/exports/beroep-functienamen-before-description-h2-2026-10-19.json`;
- `audits/exports/beroep-functienamen-before-description-h2-resume-2026-10-19.json`.

De H2-publicatie werd na een eerste deel onderbroken. Een REST-herlezing liet
zien dat de eerste termen correct waren gewijzigd en de overige descriptions
volledig onaangeraakt waren. Dezelfde idempotente routine is daarna hervat en
heeft alleen de ontbrekende H2's toegevoegd. Alle 32 eindwaarden zijn exact
geverifieerd. Intro's en archieftitels zijn niet aangeraakt.

De live description van Commissioning Engineer gebruikte al platte
alinea-opmaak waar de overige 31 descriptions `<p>`-tags gebruiken. De tekst
was inhoudelijk exact gelijk aan de bron. Deze bestaande opmaak is bewust
behouden; ook daar is uitsluitend de H2 toegevoegd.

De eerste lange descriptionversie met vier H2-vragen is na inhoudelijke
beoordeling afgekeurd en dezelfde dag volledig vervangen door de definitieve
artikelarchiefstructuur.

Descriptions zijn via de standaard WordPress REST API gepubliceerd. Intro's
en archieftitels zijn via een tijdelijk beheerders-only REST-endpoint in de
bestaande Pods-velden opgeslagen. De publicatieroutine had rollback voor alle
drie waarden. Rollback was niet nodig; de tijdelijke Code Snippets-snippet is
na verificatie gedeactiveerd en verwijderd.

## Verificatie

Na publicatie zijn alle waarden opnieuw uit LIVE gelezen en exact met de lokale
bron vergeleken:

- descriptions gewijzigd en exact geverifieerd: 32 van 32;
- intro's gewijzigd en exact geverifieerd: 32 van 32;
- archieftitels gewijzigd en exact geverifieerd: 32 van 32;
- afwijkingen: 0;
- tijdelijke snippet verwijderd: ja.

Na de latere H2-toevoeging zijn alle 32 descriptions opnieuw via REST gelezen:

- descriptions exact inclusief H2: 32 van 32;
- intro's aangeraakt: nee;
- archieftitels aangeraakt: nee;
- bestaande alinea's inhoudelijk gewijzigd: nee.

Openbare cache-omzeilende controles zijn uitgevoerd voor:

- Field Service Engineer;
- Servicemonteur;
- Internationaal Field Service Engineer;
- Commissioning Engineer;
- Service Coördinator;
- Site Manager.

Alle zes URL's gaven HTTP 200 en toonden exact de nieuwe H1, intro en drie
archiefalinea's. Na de H2-toevoeging zijn Commissioning Engineer, Field
Service Engineer en Service Manager opnieuw openbaar gecontroleerd. Hun H1
bevat nog `| Artikelen`; de nieuwe H2 niet.

## Bronnen

- contentbron: `content/beroepengids-descriptions/beroep-functienamen.md`;
- schrijf- en publicatieregels:
  `content/education-descriptions/reusable-taxonomy-text-prompt.md`;
- contentvalidatie: `scripts/build-beroep-functienamen.mjs`;
- preflight, backup, publicatie, verificatie en rollback:
  `scripts/publish-beroep-functienamen.py`.
