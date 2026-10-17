# Redirects

Inventarisatie op 17 oktober 2026.

## Overzicht

- 199 actieve omleidingen
- 0 inactieve omleidingen
- 28 omleidingen in de prullenbak
- geen exacte dubbele bronpatronen
- geen redirectlussen
- 24 redirectketens: 22 met twee stappen en 2 met drie stappen

De steekproeven voor `field-service-vacatures`, `field-service-blog`, de oude
spelfout `werelwijd` en een oude kennisbanktaxonomie gaven de verwachte 301.

## Gecorrigeerd vóór livegang

| ID | Bron | Oorspronkelijke fout | Uitgevoerde correctie |
| --- | --- | --- | --- |
| 155 | `field-service-engineer-pompen-en-pomptechniek` | 301-bestemming eindigde op `/(opens%20in%20a%20new%20tab)` | 301 naar `/vacature-ingevuld/field-service-engineer-pompen-en-pomptechniek/` |
| 134 | `privacy-verklaring` | 410 ondanks bestaande opvolger | 301 naar `/privacy-statement/` |

De 404-monitor bevestigt dat de foutieve bestemming van regel 155 daadwerkelijk
wordt geraakt. Een andere oude regel leidt bovendien via deze regel, waardoor
de correctie meer dan één oud pad herstelt.

Na het opslaan is de gerichte LiteSpeed-cache geleegd. Beide bron-URL's zijn
daarna anoniem zonder cachebuster gecontroleerd en geven HTTP 301 met exact de
hierboven vermelde bestemming (`x-qc-cache: miss`).

## Niet blokkerend vervolgwerk

- De 24 ketens direct naar hun eindbestemming laten wijzen.
- De overige 410-regels met een ingevulde maar genegeerde bestemming beoordelen,
  met name de oude test- en onderhoudspaden.
- Na livegang nieuwe 404's beoordelen zonder de bestaande loghistorie te wissen.
