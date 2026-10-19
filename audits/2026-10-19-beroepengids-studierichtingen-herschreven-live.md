# Beroepengids-studierichtingen als artikelarchief ingericht — LIVE

Datum: 19 oktober 2026
Taxonomie: `beroep_studierichting`
Omvang: 23 bestaande termen

## Definitieve structuur

Iedere studierichtingpagina is een artikelarchief. De description introduceert
de studie en de relatie met field service, verwijst naar de onderwerpen die in
latere artikelen worden uitgewerkt en helpt de bezoeker functies en vacatures
vergelijken. De uitgebreide opleidingsvragen uit de eerdere versie zijn geen
onderdelen van de archive-description meer; zij kunnen later zelfstandige
artikelen worden.

Per term is toegepast:

1. archieftitel `Studierichting [term] | Artikelen`;
2. een volledig nieuwe, korte intro met field service verbinding;
3. H2 `Studierichting [term]` boven de description, zonder `| Artikelen`;
4. drie unieke compacte archiefalinea's.

De descriptions bevatten 102–121 woorden en de intro's 19–26 woorden.

## Uitgevoerde livewijzigingen

- 23 bestaande term-descriptions herschreven;
- 23 bestaande velden `intro_taxonomy` vervangen;
- 23 bestaande velden `pods_archive_title` ingesteld;
- de eerdere vier uitgebreide H2-vragen per term verwijderd;
- geen termen, namen, slugs, parents, relaties, Pods, velden, vacatures,
  Beroepengidsartikelen, handmatige links of template-instellingen gewijzigd.

## Bestaande structuur bevestigd

De alleen-lezen preflight bevestigde exact 23 overeenkomstige namen en slugs,
allemaal met parent `0`. De bestaande velden `intro_taxonomy` en
`pods_archive_title` zijn hergebruikt. Er hoefde geen nieuw veld of nieuwe Pod
te worden gemaakt.

## Back-up en veilige uitvoering

De toestand direct vóór de definitieve herschrijving staat in:

`audits/exports/beroep-studierichtingen-before-archive-rewrite-2026-10-19.json`

Descriptions zijn via de standaard WordPress REST API gepubliceerd. Intro's
en archieftitels zijn via een tijdelijk beheerders-only REST-endpoint in de
bestaande Pods-velden opgeslagen. De publicatieroutine bevat rollback voor
alle drie waarden. De tijdelijke snippet is na iedere poging gedeactiveerd en
verwijderd.

Tijdens de uitvoering werkte de beveiliging tweemaal zoals bedoeld:

- de eerste poging stopte vóór metadata-opslag omdat WordPress de termnaam
  `ICT & Technische Informatica` intern met een HTML-entiteit aanleverde; alle
  descriptions zijn automatisch teruggezet en de rollback is exact
  gecontroleerd;
- de tweede poging signaleerde na opslag alleen bij de intro van die term een
  ampersand-encodingverschil en draaide alle waarden terug;
- daarna is uitsluitend de introformulering aangepast naar `ICT en Technische
  Informatica`; de officiële archieftitel en H2 behouden exact `&`.

De derde publicatieronde slaagde volledig.

Daarna is op verzoek uitsluitend de suffix ` | Artikelen` uit de eerste H2
van alle 23 descriptions verwijderd. De intro's, de drie bestaande
descriptionalinea's en de Pods-archieftitels zijn daarbij ongewijzigd gebleven.
De uitvoering accepteerde alleen deze exacte H2-wijziging en stopte bij ieder
ander inhoudelijk verschil. Het herstelbestand staat in
`audits/exports/beroep-studierichtingen-before-h2-articles-removal-2026-10-19.json`.

## Verificatie

Na de definitieve publicatie zijn alle waarden opnieuw uit LIVE gelezen en
exact met de lokale bron vergeleken:

- descriptions: 23 van 23 exact;
- intro's: 23 van 23 exact;
- archieftitels: 23 van 23 exact;
- afwijkingen: 0;
- tijdelijke snippet verwijderd: ja.

De H2-correctie is daarna afzonderlijk via de standaard WordPress REST API
gecontroleerd: 23 van 23 descriptions exact, intro's niet aangeraakt en
archieftitels niet aangeraakt. Het tijdelijke beheerdersendpoint gaf daarna
HTTP 404 en was dus verwijderd.

Openbare cache-omzeilende controles zijn uitgevoerd voor:

- Autotechniek;
- Elektrotechniek;
- ICT & Technische Informatica;
- Technische Bedrijfskunde;
- Werktuigbouwkunde.

Alle vijf URL's gaven HTTP 200. Na de H2-correctie zijn Autotechniek, ICT &
Technische Informatica en Werktuigbouwkunde opnieuw openbaar gecontroleerd:
de H1 bevat nog `| Artikelen` en de description-H2 niet.

## Bronnen

- canonieke content:
  `content/education-descriptions/beroep-studierichtingen.md`;
- schrijf- en publicatieregels:
  `content/education-descriptions/reusable-taxonomy-text-prompt.md`;
- validatie: `scripts/build-beroep-studierichtingen.mjs`;
- preflight, backup, publicatie, verificatie en rollback:
  `scripts/publish-beroep-studierichtingen.py`.
