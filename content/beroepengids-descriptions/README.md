# Beroepengids-content per taxonomieterm

Status: alle 32 Functienamen en alle 33 Bedrijfssectoren hebben sinds 19
oktober 2026 gecontroleerde descriptions, intro's en archieftitels op LIVE.

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

- Canonieke bronnen: `beroep-functienamen.md` en
  `beroep-bedrijfssectoren.md`.
- Validatie: `../../scripts/build-beroep-functienamen.mjs` en
  `../../scripts/build-beroep-bedrijfssectoren.mjs`.
- Veilige publicatie en rollback:
  `../../scripts/publish-beroep-functienamen.py`.
- Publicatie en rollback Bedrijfssectoren:
  `../../scripts/publish-beroep-bedrijfssectoren.py`.
- Herstelbestand Functienamen:
  `../../audits/exports/beroep-functienamen-before-archive-rewrite-2026-10-19.json`.
- Herstelbestand Bedrijfssectoren:
  `../../audits/exports/beroep-bedrijfssectoren-before-archive-content-2026-10-19.json`.
- Uitvoeringslog Functienamen:
  `../../audits/2026-10-19-beroepengids-functienamen-live.md`.
- Uitvoeringslog Bedrijfssectoren:
  `../../audits/2026-10-19-beroepengids-bedrijfssectoren-live.md`.

De bestaande Pods-relatie `relationship_beroep_functienaam` koppelt iedere
Beroepengids-term aan de gelijknamige vacaturetaxonomie `functienaam`. Deze
relaties zijn in deze contentronde niet gewijzigd.

De bestaande Pods-relatie `relationship_bedrijfssector` koppelt iedere
Beroepengids-sector aan de gelijknamige vacaturetaxonomie `bedrijfssector`.
Ook deze relaties zijn tijdens de contentpublicatie niet gewijzigd.

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
