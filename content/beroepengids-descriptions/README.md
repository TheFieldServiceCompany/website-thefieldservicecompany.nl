# Beroepengids-content per functienaam

Status: alle 32 descriptions, intro's en archieftitels van
`beroep_functienaam` staan sinds 19 oktober 2026 gecontroleerd op LIVE.

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

- Canonieke bron: `beroep-functienamen.md`.
- Validatie: `../../scripts/build-beroep-functienamen.mjs`.
- Veilige publicatie en rollback:
  `../../scripts/publish-beroep-functienamen.py`.
- Herstelbestand:
  `../../audits/exports/beroep-functienamen-before-archive-rewrite-2026-10-19.json`.
- Uitvoeringslog:
  `../../audits/2026-10-19-beroepengids-functienamen-live.md`.

De bestaande Pods-relatie `relationship_beroep_functienaam` koppelt iedere
Beroepengids-term aan de gelijknamige vacaturetaxonomie `functienaam`. Deze
relaties zijn in deze contentronde niet gewijzigd.

## Gevalideerde omvang

- 32 bestaande termen;
- 32 descriptions van 104–143 woorden en exact drie alinea's;
- 32 uitnodigende intro's van 21–38 woorden;
- 32 archieftitels volgens `Functienaam [term] | Artikelen`;
- geen H2-vragen in de descriptions;
- iedere intro en description bevat de relatie met field service.

De template levert de H1 en dynamische blokken. De descriptions bevatten
daarom alleen archiefintroductie in alinea's, zonder handmatig toegevoegde
vacaturelinks, losse artikelvragen of templatecode.
