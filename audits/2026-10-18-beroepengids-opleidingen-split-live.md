# Beroepengids Opleidingen — gesplitste LIVE-architectuur

Datum: 18 oktober 2026

Status: actief op LIVE; enkele opruim- en sitemapcontroles staan nog open

## Definitief structuurbesluit

De eerste gecombineerde taxonomie `beroep_opleiding` bleek een extra
hiërarchisch niveau te creëren dat niet aansloot op de vacaturekant. De
Beroepengids gebruikt daarom dezelfde twee inhoudelijke dimensies als de
vacatures:

| Beroepengids | Vacaturetaxonomie | Basisroute |
| --- | --- | --- |
| `beroep_opleidingsniveau` | `opleidingsniveau` | `/beroepengids/opleidingsniveaus/` |
| `beroep_studierichting` | `vac_studierichting` | `/beroepengids/studierichtingen/` |

Er is geen aparte Beroepengids-pagina `Opleidingen` en geen parentterm nodig.
Beide taxonomieën hebben een eigen basisarchief, termarchieven, relaties,
content en kaart op de centrale Beroepengids-pagina.

## Pods en migratie

- Bestaande Pod `beroep_opleidingsniveau` (ID 24177) is behouden.
- Nieuwe Pod `beroep_studierichting` heeft ID 27609.
- 4 opleidingsniveaus en 23 studierichtingen zijn vanuit de tijdelijke
  gecombineerde structuur gekopieerd, inclusief intro, description en relatie
  naar de overeenkomstige vacatureterm.
- Eenmalige migratiesnippet 226 verwerkte alle 27 termen zonder fout en staat
  inactief.
- Eenmalige snippets 227–229 vulden basiscontent, Rank Math-instellingen en
  rewrites/caches; zij staan inactief.
- De lege testterm `Opleiding A` is verwijderd.
- De verouderde Pod `beroep_opleiding` bestaat op dit meetmoment nog als
  herstel-/opruimobject en wordt pas na de laatste sitemapcontrole verwijderd.

## Actieve snippets

| Snippet | Actieve versie | Functie | Herstelkopie |
| ---: | --- | --- | ---: |
| 62 | 2.0 | generieke Elementor-taxonomielussen | 222 |
| 113 | 3.2 | beide virtuele basisarchieven | 223 |
| 202 | 6.3 | generieke Pods-archiefinstellingen zonder oude opleiding-alias | 230 |
| 209 | 1.2 | afzonderlijke SEO-mapping voor beide basisarchieven | 224 |
| 215 | 1.2 | basisroutes en beide taxonomysitemaps | 225 |

## Centrale Beroepengids-pagina

Pagina 24266 bevat twee zelfstandige kaarten:

- **Artikelen op opleidingsniveau** naar
  `/beroepengids/opleidingsniveaus/`;
- **Artikelen op studierichting** naar
  `/beroepengids/studierichtingen/`.

Beide kaarten zijn exacte structurele klonen van de bestaande, werkende kaart
**Artikelen op functienaam**. Alleen titel, URL, achtergrondafbeelding en
taxonomie verschillen. Na het legen van de Elementor-cache zijn beide blauwe
banners visueel gecontroleerd.

## Functionele controles

- Beide basisarchieven renderen met eigen H1, intro en basisdescription.
- Opleidingsniveau `HBO` toont de gekoppelde vacatureknop en vacatures.
- Studierichting `Elektrotechniek` rendert correct; de vacaturelijst is leeg
  omdat de bijbehorende vacatureterm momenteel nul vacatures heeft.
- De centrale Beroepengids-pagina toont 4 niveaus en de studierichtingenloop.
- Snippets 62, 113, 202, 209 en 215 zijn actief zonder gemelde codefout.

## Nog open

1. In Rank Math voor beide nieuwe taxonomieën **lege termen opnemen** activeren
   en daarna controleren dat de XML-sitemaps respectievelijk 4 en 23 term-URL's
   bevatten.
2. Daarna de oude Pod `beroep_opleiding` gecontroleerd verwijderen, rewrites en
   sitemapcache vernieuwen en bevestigen dat de oude sitemap verdwenen is.
3. De namingstructuur van de relatievelden door de beheerder nalopen; deze
   handmatige correcties mogen niet door automatische migraties worden
   overschreven.
4. De definitieve waarden en koppelingen later synchroniseren en testen in
   Supabase en Appsmith.
