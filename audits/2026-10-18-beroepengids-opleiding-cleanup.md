# Beroepengids Opleidingen — cleanup tijdelijke structuur

Datum: 18 oktober 2026  
Omgeving: LIVE  
Status: uitgevoerd en technisch gecontroleerd

## Goedgekeurde reikwijdte

Alleen de vervangen, tijdelijke architectuur rond `beroep_opleiding` is
verwijderd. De huidige taxonomieën `beroep_opleidingsniveau` en
`beroep_studierichting`, de vacaturetaxonomieën, Elementor-inhoud, Supabase en
Appsmith zijn niet verwijderd of inhoudelijk gewijzigd.

## Verwijderd

- Pod `beroep_opleiding` (ID 27600).
- Groep `More Fields` (ID 27608).
- Velden 27601–27604: featured image, intro en beide relaties.
- 29 termen binnen uitsluitend deze oude taxonomie:
  - twee technische containertermen;
  - vier dubbele opleidingsniveaus;
  - 23 dubbele studierichtingen.
- Oude Rank Math-sleutels met prefix `tax_beroep_opleiding_`.
- Tijdelijke migratie-, resultaat-, versie- en flushopties.
- Eenmalige/diagnose/testsnippets 213, 214, 216–220, 226–229, 231 en 233.
- Tijdelijke uitvoeringssnippets 234 en 235 direct na gebruik.

Herstelkopieën 210–212, 221–225, 230 en 232 zijn bewust behouden.

## Veiligheidscontrole vóór verwijdering

De 27 inhoudelijke slugs uit de oude taxonomie zijn vergeleken met de som van
de twee huidige taxonomieën. Beide sets waren exact gelijk: 27 tegenover 27,
zonder ontbrekende of extra slug. De twee technische containertermen hadden
geen inhoudelijke tegenhanger nodig.

Er was al een lokale, afgeschermde herstelset met de oude Pod, velden, termen
en Elementor-pagina vóór de splitsing. Dagelijkse hostingback-ups blijven
daarnaast beschikbaar.

## Controle na verwijdering

- Pods meldt voor `beroep_opleiding`: **Pod niet gevonden**.
- De WordPress REST-route voor de oude taxonomie retourneert 404.
- `beroep_opleidingsniveau` bestaat als Pod 24177 en bevat 4 termen.
- `beroep_studierichting` bestaat als Pod 27609 en bevat 23 termen.
- Alle 27 huidige termen hebben een gevulde relatie naar hun
  vacaturetaxonomie.
- Elementor-pagina 24266 bevat geen exacte oude taxonomieverwijzing en wel
  beide huidige taxonomieën.
- Actieve snippets 39, 62, 113, 116, 202, 209 en 215 zijn actief, niet
  verwijderd en melden geen codefout.
- Voor beide huidige taxonomieën staan Rank Math sitemap en `include_empty`
  op `on`; oude sitemapkeys zijn verwijderd en de sitemapcache is vernieuwd.

De publieke XML-responses worden na het uitschakelen van Elementor-
onderhoudsmodus nog eenmaal bekeken. Tijdens onderhoud is HTTP 503 voor
niet-ingelogde bezoekers de bedoelde respons.
