# SEO-migratiechecklist

Status: gecontroleerd op 17 oktober 2026, terwijl LIVE bewust in
onderhoudsmodus staat.

## Afgerond

- De WordPress-adressen verwijzen naar `https://thefieldservicecompany.nl`.
- De permalinkstructuur is `/%postname%/`.
- De Rank Math-hoofdinstelling voor robots staat op `index`; de tijdelijke
  blokkering komt van WordPress en de Elementor-onderhoudsmodus.
- Een anonieme bezoeker krijgt tijdens onderhoud HTTP 503 met `noindex,
  nofollow` en een `Retry-After`-header.
- `robots.txt` is bereikbaar en verwijst naar `/sitemap_index.xml`.
- De sitemapindex en de belangrijke deelsitemaps zijn bereikbaar. Aantallen op
  het controlemoment: 64 vacature-URL's, 68 ingevulde vacatures, 19 blog-URL's,
  14 pagina-URL's en 1 lokale SEO-URL.
- In gepubliceerde pagina's, blogs, vacatures en ingevulde vacatures zijn geen
  verwijzingen naar `dev.thefieldservicecompany.nl` of onbeveiligde LIVE-URL's
  gevonden.
- Representatieve pagina's hebben bruikbare titels, beschrijvingen, H1's,
  OpenGraph/Twitter-meta en vacature-schema.
- Google Search Console is volgens Rank Math gekoppeld.
- 199 actieve Rank Math-omleidingen zijn geïnventariseerd. Er zijn geen exacte
  dubbele bronregels en geen redirectlussen gevonden.
- Omleiding 155 is gecorrigeerd naar de juiste ingevulde-vacature-URL en
  omleiding 134 is gewijzigd van 410 naar 301 richting `/privacy-statement/`.
  Na een gerichte LSCache-opschoning zijn beide oude URL's anoniem getest: ze
  geven HTTP 301 met exact de bedoelde bestemming.
- Er staan na deze correcties geen bekende SEO-blokkades meer open vóór de
  definitieve livegang.

## Pas uitvoeren bij de definitieve livegang

1. Elementor-onderhoudsmodus uitschakelen.
2. Bij **Instellingen > Lezen** “Zoekmachines ontmoedigen” uitvinken.
3. Cache legen.
4. Anoniem controleren: homepage en kernpagina's HTTP 200, geen `noindex`,
   correcte zelfverwijzende canonical en werkende formulieren/vacaturezoeker.
5. Controleren dat sitemap en deelsitemaps geen tijdelijke `x-robots-tag:
   noindex` meer teruggeven.
6. Sitemap opnieuw aanbieden/controleren in Google Search Console.
7. De eerste dagen 404-monitor, formulierinzendingen en koppelingen volgen.

## Later verbeteren, niet blokkerend

- 24 redirectketens inkorten: 22 ketens van twee stappen en 2 van drie stappen.
- Focus-keywords nalopen voor de door Rank Math genoemde oudere content.
- Vacature-schema desgewenst uitbreiden met `validThrough` en `directApply`.
- Beroepengids en kennisbank vullen en daarna hun taxonomieën, interne links en
  sitemaps opnieuw beoordelen.
