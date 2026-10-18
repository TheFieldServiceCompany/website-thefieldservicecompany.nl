# Compacte herbruikbare prompt voor taxonomyteksten

Gebruik deze prompt voor nieuwe descriptions bij vacatures, Beroepengids of
andere taxonomyarchieven. Vul eerst alle waarden tussen blokhaken in.

## Invoer

- Website: `[DOMEIN]`
- Exacte WordPress-taxonomie: `[TAXONOMY_SLUG]`
- Term: `[TERM]`
- Type term: `[BIJVOORBEELD STUDIERICHTING, FUNCTIENAAM OF KENNISGEBIED]`
- Doelgroep: `[DOELGROEP]`
- Belangrijkste technieken, machines en sectoren: `[CONTEXT]`
- Gerelateerde actuele vacatures of artikelen: `[RELATIES]`
- Gewenste lengte: `[BIJVOORBEELD 425-475 WOORDEN]`

## Prompt

Schrijf een unieke, publicatieklare description voor `[TERM]` binnen de
WordPress-taxonomie `[TAXONOMY_SLUG]` op `[DOMEIN]`.

Onderzoek voor het schrijven de bestaande term, relaties en relevante actuele
vacatures of artikelen. Gebruik uitsluitend geloofwaardige technische
raakvlakken. Leg concreet uit hoe kennis, werkzaamheden, systemen en sectoren
met elkaar verbonden zijn; beweer niet alleen dat iets aansluit.

Schrijf energiek, motiverend en in natuurlijk Nederlands vanuit de jij-vorm.
De lezer moet enthousiast worden door herkenbare mogelijkheden, machines,
problemen en resultaten, niet door holle superlatieven. Houd de tekst sterk
gericht op field service en de specifieke term.

Gebruik vier zelfstandig begrijpelijke H2-vragen. Laat de eerste H2 direct de
verbinding leggen tussen `[TERM]` en service, field service, machines of de
relevante sector. Wissel de zinsbouw af met bijvoorbeeld `Waarom`, `Hoe`,
`Welke`, `Wat maakt`, `Waar liggen` of `Van ... naar ...`. Begin niet
automatisch met `Wat leer je tijdens de opleiding ...`.

Gebruik bij onderwijscontent natuurlijke varianten zoals `opleiding [TERM]`,
`studierichting [TERM]`, `studie [TERM]` en `achtergrond in [TERM]`; zet ze
niet allemaal geforceerd in iedere kop. Schrijf `field service` als twee
woorden. Gebruik functienamen in lopende zinnen met kleine letters.

Behandel waar relevant:

- de belangrijkste kennis en vaardigheden;
- concrete toepassing bij installatie, onderhoud, diagnose, reparatie,
  commissioning, optimalisatie of klantondersteuning;
- passende machines, installaties en bedrijfssectoren;
- realistische functies en overstapmogelijkheden;
- aanvullende praktijkervaring of producttraining zonder die als universele
  eis te presenteren;
- een natuurlijke vervolgstap naar passende vacatures of artikelen.

Vermijd generieke koppen en opvulling zoals `Welke sectoren passen hierbij?`,
`Hoe vind je een vacature?`, `in dit archief`, `talloze kansen`, `dynamische
wereld` en `onmisbare schakel`. Maak geen handmatige vacaturelinks wanneer de
website relaties automatisch vult. De template levert de H1; gebruik in de
description alleen H2-koppen en alinea's.

Lever eerst een concept met:

1. vier H2-koppen en de volledige tekst;
2. het woordenaantal;
3. een korte feiten- en herhalingscontrole;
4. de exact bedoelde WordPress-taxonomie.

Publiceer pas na inhoudelijke goedkeuring.

## Verplichte publicatiecontrole

1. Bevestig voor de eerste wijziging de exacte taxonomy-slug en term-ID's.
2. Maak een herstelbestand van uitsluitend de bedoelde taxonomie.
3. Wijzig geen gelijknamige of spiegelende taxonomie, zoals
   `vac_studierichting` tegenover `beroep_studierichting`.
4. Publiceer via REST naar de bevestigde taxonomie.
5. Lees alle gewijzigde termen opnieuw uit en vergelijk de opgeslagen HTML
   exact met de goedgekeurde bron.
6. Controleer minimaal drie gerenderde archieven met verschillende soorten
   termen en omzeil daarbij de cache.
7. Controleer dat relaties, templateblokken, H1, canonical en metadata niet
   onverwacht zijn veranderd.
8. Leg bron, doelgroep, taxonomy-slug, wijzigingen, controles en herstelwijze
   vast in GitHub.

## Eindcontrole per tekst

- Is de eerste H2 anders opgebouwd dan bij de omliggende teksten?
- Noemt de kop direct het onderwerp en de relatie met service of field service?
- Bevat iedere alinea concrete informatie in plaats van algemene claims?
- Kloppen opleiding, machines, technieken, functies en sectoren inhoudelijk?
- Is de tekst prettig, actief en motiverend om te lezen?
- Is goed Nederlands gebruikt en is `field service` consequent los geschreven?
- Staat de tekst aantoonbaar in de juiste WordPress-taxonomie?
