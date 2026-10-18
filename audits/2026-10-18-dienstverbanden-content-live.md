# Dienstverbanden: archieftitels en descriptions LIVE

Datum: 18 oktober 2026  
Omgeving: `https://thefieldservicecompany.nl`

## Uitgevoerd

De twee termen van de vacaturetaxonomie `dienstverband` zijn voorzien van een
nieuwe `pods_archive_title` en een volledig herschreven description.

| Term | Archieftitel | Woorden | Gekoppelde vacatures tijdens controle |
| --- | --- | ---: | ---: |
| Freelance/ZZP | Freelance en ZZP field service vacatures | 369 | 6 |
| Vast Dienstverband | Field service vacatures in vast dienstverband | 371 | 57 |

De exacte live teksten staan in
`content/dienstverbanden/dienstverbanden-live.json`.

## Positionering

### Freelance/ZZP

De tekst richt zich op ervaren specialisten die vrijheid, projectmatig werken
en zelfstandigheid zoeken. Concrete field service werkzaamheden zijn onder
meer commissioning, installatie, revisies, onderhoud, complexe storingen en
upgrades. Naast avontuur en afwisseling benoemt de tekst eerlijk de eigen
verantwoordelijkheid voor administratie, verzekeringen, pensioen, vakantie,
ziekte en continuïteit tussen opdrachten.

### Vast Dienstverband

De tekst combineert technisch avontuur met zekerheid en vermeldt expliciet:

- rechtstreeks in dienst bij de uiteindelijke werkgever;
- geen detachering en geen uitzendconstructie;
- investering in productkennis, opleidingen, technische trainingen en
  certificaten;
- betaald verlof;
- loondoorbetaling bij ziekte volgens de geldende afspraken;
- pensioenopbouw via de regeling van de werkgever;
- ruimte voor doorgroei en langdurige ontwikkeling.

## Technische uitvoering en controle

De oorspronkelijke descriptions en lege archieftitels zijn vooraf opgeslagen
in `work/dienstverbanden-2026-10-18/before.json`. Beide velden waren voor deze
taxonomie schrijfbaar via WordPress REST. Publicatie en onafhankelijke
herlezing bevestigden 2 van 2 exacte matches.

Het openbare archief voor `Vast Dienstverband` is daarnaast in een echte
browsersessie gecontroleerd. De H1 toont
`Field service vacatures in vast dienstverband`, de vacaturelijst blijft
zichtbaar en het description-accordion gebruikt dezelfde archieftitel.

Niet gewijzigd:

- termnamen, slugs en term-ID's;
- vacaturekoppelingen;
- Pods-definitie;
- Elementor-templates;
- snippets;
- Rank Math-velden.
