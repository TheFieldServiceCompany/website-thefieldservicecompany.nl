# Rank Math-audit — Opleidingen

Datum: 18 oktober 2026  
Omgeving: LIVE  
Status: alleen-lezen controle uitgevoerd; twee correcties nodig vóór indexering

## Reikwijdte

Gecontroleerd zijn:

- `beroep_opleidingsniveau`;
- `beroep_studierichting`;
- beide virtuele basisarchieven;
- alle 27 termarchieven;
- Rank Math title-, robots- en sitemapinstellingen;
- de werkelijke sitemapindex en drie relevante taxonomy-sitemap-URL's.

De tijdelijke auditsnippets 237 en 238 zijn na controle gedeactiveerd en
definitief verwijderd. Er is tijdens deze audit geen SEO-instelling of content
gewijzigd.

## Wat goed staat

- Basisarchief title Opleidingsniveaus:
  `Opleidingsniveaus voor field service | Beroepengids`.
- Basisarchief title Studierichtingen:
  `Studierichtingen voor field service | Beroepengids`.
- De basisarchieven hebben eigen meta-descriptions van respectievelijk 160 en
  163 tekens en overeenkomstige Open Graph-uitvoer.
- Termtitles gebruiken `%term% voor field service | Beroepengids`.
- 26 van de 27 gegenereerde termtitles zijn maximaal 60 tekens. Alleen
  `ICT & Technische Informatica voor field service | Beroepengids` is langer.
- De zichtbare H1's van basis- en termarchieven zijn aanwezig.
- Sitemap en `include_empty` staan voor beide taxonomieën op `on`.
- `sitemap_index.xml` geeft HTTP 200 met geldige XML en bevat beide nieuwe
  taxonomysitemaps precies eenmaal.
- `beroep_opleidingsniveau-sitemap.xml` geeft HTTP 200 en bevat 4 URL's.
- `beroep_studierichting-sitemap.xml` geeft HTTP 200 en bevat 23 URL's.
- `beroep_opleiding-sitemap.xml` geeft 404 en bevat geen oude URL's.

## Probleem 1 — meta-descriptions zijn veel te lang

Beide taxonomieën gebruiken nu `%term_description%`. Omdat de description het
volledige inhoudelijke artikel onderaan het archief bevat, wordt die complete
tekst ook in de HTML-meta-description en Open Graph-description geplaatst.

- Kortste termdescription: circa 3.619 tekens.
- Langste termdescription: circa 4.058 tekens.
- Aantal termarchieven boven 160 tekens: 27 van 27.
- Geen van de 27 termen heeft een term-specifieke Rank Math-override.

Dit is geen geschikt SEO-snippet. De inhoudelijke description moet zichtbaar
op de pagina blijven, maar er moeten afzonderlijke, unieke meta-descriptions
van ongeveer 140–160 tekens komen.

## Probleem 2 — lege taxonomieën worden op noindex gezet

De globale Rank Math-optie `noindex_empty_taxonomies` staat aan. Alle 27 termen
hebben voor WordPress een post count van nul, omdat de pagina's hun inhoud en
vacaturekoppelingen via Pods-relaties krijgen in plaats van gewone
WordPress-termtoewijzingen.

Voor bezoekers zijn dit dus inhoudelijk gevulde landingspagina's, maar Rank
Math ziet ze technisch als leeg. Zonder gerichte uitzondering kunnen ze na het
uitschakelen van de onderhoudsmodus op `noindex` blijven staan.

De huidige live HTML toont `nofollow, noindex` en geen canonical. Dat is nu
mede het bedoelde gevolg van Elementor-onderhoudsmodus. Daarom moeten robots
en canonical na uitschakeling van onderhoud opnieuw worden gecontroleerd. De
onderliggende lege-taxonomie-instelling moet echter vóór dat moment gericht
worden opgelost.

## Aanbevolen correctie

1. De globale bescherming voor andere lege taxonomieën behouden.
2. Alleen `beroep_opleidingsniveau` en `beroep_studierichting` gericht op
   `index, follow` zetten wanneer Elementor-onderhoudsmodus uit staat.
3. Voor beide Pods een afzonderlijk bewerkbaar meta-descriptionveld gebruiken
   of per term Rank Math-meta opslaan.
4. Voor alle 27 termen een inhoudelijk unieke meta-description van ongeveer
   140–160 tekens schrijven; niet automatisch de lange description afkappen.
5. Daarna cache vernieuwen en live opnieuw controleren: title, description,
   robots, canonical, Open Graph en sitemap.

De sitemapstructuur zelf hoeft niet te worden aangepast.
