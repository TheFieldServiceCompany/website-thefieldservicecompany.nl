# Beroepengids-content per taxonomieterm

Status: alle 32 Functienamen, alle 33 Bedrijfssectoren, alle 29
Functie-inhoud-termen, alle 25 Kennisgebieden en alle 8 Werkgebieden hebben
sinds 19 oktober 2026 gecontroleerde descriptions, intro's en archieftitels op
LIVE.

## Doel en inhoud

Iedere functienaampagina is een artikelarchief. De description geeft een grove
introductie op de functienaam, benoemt welke onderwerpen in de artikelen
worden uitgewerkt en legt uit hoe die informatie helpt bij het vergelijken van
functies en vacatures. Uitgebreide vragen over werkzaamheden, opleiding,
salaris, reizen en doorgroei worden later zelfstandige artikelen.

Functietitels worden niet kunstmatig scherper gemaakt dan de vacaturepraktijk
toelaat. Spellingvarianten zijn als varianten benoemd en bij overlappende
titels staat steeds dat de inhoud van de vacature doorslaggevend is.

## Bestanden en relatie

- Canonieke bronnen: `beroep-functienamen.md`, `beroep-bedrijfssectoren.md`,
  `beroep-functie-inhoud.md`, `beroep-kennisgebieden.md` en
  `beroep-werkgebieden.md`.
- Validatie: `../../scripts/build-beroep-functienamen.mjs` en
  `../../scripts/build-beroep-bedrijfssectoren.mjs` en
  `../../scripts/build-beroep-functie-inhoud.mjs` en
  `../../scripts/build-beroep-kennisgebieden.mjs` en
  `../../scripts/build-beroep-werkgebieden.mjs`.
- Veilige publicatie en rollback:
  `../../scripts/publish-beroep-functienamen.py`.
- Publicatie en rollback Bedrijfssectoren:
  `../../scripts/publish-beroep-bedrijfssectoren.py`.
- Publicatie en rollback Functie-inhoud:
  `../../scripts/publish-beroep-functie-inhoud.py`.
- Publicatie en rollback Kennisgebieden:
  `../../scripts/publish-beroep-kennisgebieden.py`.
- Publicatie en rollback Werkgebieden:
  `../../scripts/publish-beroep-werkgebieden.py`.
- Herstelbestand Functienamen:
  `../../audits/exports/beroep-functienamen-before-archive-rewrite-2026-10-19.json`.
- Herstelbestand Bedrijfssectoren:
  `../../audits/exports/beroep-bedrijfssectoren-before-archive-content-2026-10-19.json`.
- Herstelbestand Functie-inhoud:
  `../../audits/exports/beroep-functie-inhoud-before-archive-content-2026-10-19.json`.
- Herstelbestand Kennisgebieden:
  `../../audits/exports/beroep-kennisgebieden-before-archive-content-2026-10-19.json`.
- Herstelbestand Werkgebieden:
  `../../audits/exports/beroep-werkgebieden-before-archive-content-2026-10-19.json`.
- Uitvoeringslog Functienamen:
  `../../audits/2026-10-19-beroepengids-functienamen-live.md`.
- Uitvoeringslog Bedrijfssectoren:
  `../../audits/2026-10-19-beroepengids-bedrijfssectoren-live.md`.
- Uitvoeringslog Functie-inhoud:
  `../../audits/2026-10-19-beroepengids-functie-inhoud-live.md`.
- Uitvoeringslog Kennisgebieden:
  `../../audits/2026-10-19-beroepengids-kennisgebieden-live.md`.
- Uitvoeringslog Werkgebieden:
  `../../audits/2026-10-19-beroepengids-werkgebieden-live.md`.

De bestaande Pods-relatie `relationship_beroep_functienaam` koppelt iedere
Beroepengids-term aan de gelijknamige vacaturetaxonomie `functienaam`. Deze
relaties zijn in deze contentronde niet gewijzigd.

De bestaande Pods-relatie `relationship_bedrijfssector` koppelt iedere
Beroepengids-sector aan de gelijknamige vacaturetaxonomie `bedrijfssector`.
Ook deze relaties zijn tijdens de contentpublicatie niet gewijzigd.

De bestaande Pods-relatie `relationship_functie-inhoud` koppelt iedere
Beroepengids-term aan de gelijknamige vacaturetaxonomie `functie-inhoud`. Ook
deze 29 relaties zijn tijdens de contentpublicatie niet gewijzigd.

De bestaande Pods-relatie `relationship_kennisgebied` koppelt ieder
Beroepengids-kennisgebied aan de gelijknamige vacaturetaxonomie
`kennisgebied`. Ook deze 25 relaties zijn tijdens de contentpublicatie niet
gewijzigd.

De bestaande Pods-relatie `relationship_werkgebied` koppelt ieder
Beroepengids-werkgebied aan de gelijknamige vacaturetaxonomie `werkgebied`.
Ook deze 8 relaties zijn tijdens de contentpublicatie niet gewijzigd.

## Gevalideerde omvang

- 32 bestaande termen;
- 32 descriptions van 107–147 woorden, één H2 en exact drie alinea's;
- 32 uitnodigende intro's van 21–38 woorden;
- 32 archieftitels volgens `Functienaam [term] | Artikelen`;
- iedere description begint met `Functienaam [term]` als H2;
- geen losse H2-vragen in de descriptions;
- iedere intro en description bevat de relatie met field service.

De template levert de H1 en dynamische blokken. De H1/Pods-archieftitel blijft
`Functienaam [term] | Artikelen`; de description-H2 gebruikt
`Functienaam [term]` zonder `| Artikelen`. Verder bevatten de descriptions
alleen archiefintroductie in alinea's, zonder handmatig toegevoegde
vacaturelinks, losse artikelvragen of templatecode.

De bestaande live description van Commissioning Engineer gebruikt platte
alinea-opmaak in plaats van `<p>`-tags. De marker `Description markup: plain`
in de bron zorgt dat de builder die bestaande opmaak behoudt.

## Gevalideerde omvang Bedrijfssectoren

- 33 bestaande termen;
- 33 descriptions van 100–125 woorden, één H2 en exact drie alinea's;
- 33 unieke intro's van 17–24 woorden;
- 33 archieftitels volgens `Bedrijfssector [term] | Artikelen`;
- iedere description begint met `Bedrijfssector [term]` als H2;
- iedere intro en description verbindt de sector met field service;
- de bestaande lange vacaturedescriptions zijn gebruikt als inhoudelijke bron,
  maar niet gekopieerd naar de Beroepengids.

## Gevalideerde omvang Functie-inhoud

- 29 bestaande termen;
- 29 descriptions van 100–113 woorden, één H2 en exact drie alinea's;
- 29 unieke intro's van 19–24 woorden;
- 29 archieftitels volgens `Functie-inhoud [term] | Artikelen`;
- iedere description begint met `Functie-inhoud [term]` als H2;
- iedere intro en description verbindt de werkzaamheid met field service;
- overlappende begrippen worden als verwante vacaturetermen uitgelegd; de
  concrete vacature-inhoud blijft bepalend.

## Gevalideerde omvang Kennisgebieden

- 25 bestaande termen;
- 25 descriptions van 100–110 woorden, één H2 en exact drie alinea's;
- 25 unieke intro's van 17–21 woorden;
- 25 archieftitels volgens `Kennisgebied [term] | Artikelen`;
- iedere description begint met `Kennisgebied [term]` als H2;
- iedere intro en description verbindt het kennisgebied met field service;
- verwante domeinen worden in hun technische samenhang geplaatst zonder
  vacature-eisen of specialistische bevoegdheden te veralgemeniseren.

## Gevalideerde omvang Werkgebieden

- 8 bestaande termen;
- 8 descriptions van 110–118 woorden, één H2 en exact drie alinea's;
- 8 unieke intro's van 19–22 woorden;
- 8 archieftitels volgens `Werkgebied [term] | Artikelen`;
- iedere description begint met `Werkgebied [term]` als H2;
- iedere intro en description verbindt het gebied met field service;
- geografische inzet blijft duidelijk gescheiden van internationale dagen,
  maximale reisduur, hotelnachten en weekenden thuis.
