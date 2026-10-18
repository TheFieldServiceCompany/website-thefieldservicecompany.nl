# Dagen internationaal: archieftitels en descriptions LIVE

Datum: 18 oktober 2026  
Omgeving: `https://thefieldservicecompany.nl`

## Uitgevoerd

Alle vier termen van de vacaturetaxonomie `dagen-internationaal` zijn voorzien
van een eigen `pods_archive_title` en een volledig herschreven description.

| Term | Archieftitel | Woorden |
| --- | --- | ---: |
| Geen | Field service vacatures voor nationaal werken | 382 |
| tot 120 dagen | Field service vacatures tot 120 dagen internationaal per jaar | 369 |
| tot 200 dagen | Field service vacatures tot 200 dagen internationaal per jaar | 389 |
| vanaf 200 dagen | Field service vacatures vanaf 200 dagen internationaal per jaar | 411 |

De exacte live teksten staan in
`content/dagen-internationaal/dagen-internationaal-live.json`.

## Redactionele lijn

- `Geen` is positief uitgewerkt als nationaal field service werk: op locatie
  bij klanten binnen Nederland, met ruimte voor gezin, vrienden, sport en een
  sociaal leven.
- `tot 120 dagen` combineert internationale projecten en avontuur met
  herkenbare periodes thuis.
- `tot 200 dagen` behandelt internationaal werken als een belangrijke
  levensstijl en benoemt dat dit in vacatures ook als circa 70% internationaal
  kan worden omschreven, afhankelijk van de rekenmethode van de werkgever.
- `vanaf 200 dagen` beschrijft wereldwijde field service als een complete
  lifestyle waarin techniek, reizen, vrijheid en verantwoordelijkheid
  samenkomen.

Elke tekst benoemt naast de aantrekkingskracht ook concrete aandachtspunten,
zoals reisplanning, tripduur, rotaties, rust, weekenden en de gevolgen voor het
privéleven. Daardoor blijven de teksten enthousiast zonder de realiteit van
veel reizen te romantiseren.

## Technische uitvoering en controle

De bestaande descriptions zijn vóór publicatie opgeslagen in
`work/dagen-internationaal-2026-10-18/before.json`. De descriptions zijn via
de gewone WordPress REST-route gepubliceerd en daarna exact opnieuw
uitgelezen: 4 van 4 correct.

Het bestaande Pods-veld `pods_archive_title` is bij deze taxonomie niet als
schrijfbaar veld opgenomen in de WordPress REST-response. Om geen Pods-
instelling of snippet te wijzigen, zijn de vier titels daarom in het bestaande
WordPress-bewerkformulier opgeslagen. Na iedere update is de opgeslagen waarde
opnieuw uit het veld gelezen: 4 van 4 correct.

Niet gewijzigd:

- termnamen, slugs en term-ID's;
- relaties met vacatures;
- Pods-definitie of REST-instellingen;
- Elementor-templates;
- snippets;
- Rank Math-velden.
