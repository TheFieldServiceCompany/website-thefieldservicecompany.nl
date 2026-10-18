# LIVE-indexeerbaarheid opnieuw ingeschakeld

Datum: 19 oktober 2026
Omgeving: `https://thefieldservicecompany.nl`

## Resultaat

Na het uitschakelen van WordPress' instelling om zoekmachines te ontmoedigen
zijn representatieve publieke pagina's opnieuw gecontroleerd.

| Pagina | HTTP | Robots | Canonical aanwezig |
| --- | ---: | --- | --- |
| Homepage | 200 | `follow, index` | ja |
| Vacatures | 200 | `follow, index` | ja |
| Beroepengids | 200 | `follow, index` | ja |
| Kennisbank | 200 | `follow, index` | ja |
| Functie-inhoud Sales | 200 | `follow, index` | ja |

De sitebrede `noindex, nofollow` is dus niet meer actief. `robots.txt` blijft
bereikbaar, blokkeert de gewone publieke inhoud niet en verwijst naar
`https://thefieldservicecompany.nl/sitemap_index.xml`.

## Canonicalgedrag opnieuw zichtbaar

Nu Rank Math weer indexeerbare metadata rendert, is ook de eerder vastgelegde
strategie van actieve snippet 193 opnieuw zichtbaar:

- native vacaturetaxonomiearchieven blijven `index, follow`;
- functienaam en locatie canonicaliseren naar hun equivalente combo-URL;
- andere vacaturetaxonomieën canonicaliseren naar een combo-URL met
  `field-service-engineer` als vaste functienaamcontext.

Dit is representatief bevestigd bij Sales, Storingen, Werkvoorbereiding,
Dagen internationaal, Bedrijfssector en Vast Dienstverband. Het is dus geen
los Sales-probleem, maar de bestaande generieke strategie. Die keuze moet
opnieuw inhoudelijk worden bevestigd nu de site weer indexeerbaar is.

## Eerste vervolgpunten voor 20 oktober 2026

1. Beslissen of native archieven voor functie-inhoud, bedrijfssector,
   dienstverband, dagen internationaal en andere taxonomieën canonical mogen
   blijven verwijzen naar een vaste `field-service-engineer`-combo, of een
   self-canonical / andere doel-URL moeten krijgen.
2. Snippet 193 alleen na dit besluit beoordelen; vóór iedere eventuele
   wijziging eerst een inactieve herstelkopie maken en expliciet akkoord
   vragen.
3. De combo-sitemapstrategie herbevestigen. De eerdere audit telde 15.836
   combo-URL's in 32 sitemapbestanden en vond geen geleidelijke
   publicatielimiet, terwijl zo'n fasering eerder wel de bedoeling leek.
4. Search & Filter-URL's en andere `noindex`-combo's opnieuw controleren op
   robots, canonical en interne links.
5. Daarna verder met het inhoudelijke model voor Reisprofielen. De dubbele
   termen zijn deels bewuste zoektermvarianten en worden niet zonder besluit
   samengevoegd of verwijderd.
6. De centrale overzichtspagina voor alle publieke archieven later ontwerpen;
   dit blijft een afzonderlijke TODO.

Er zijn tijdens deze controle geen WordPress-instellingen, snippets,
taxonomieën of pagina's gewijzigd.
