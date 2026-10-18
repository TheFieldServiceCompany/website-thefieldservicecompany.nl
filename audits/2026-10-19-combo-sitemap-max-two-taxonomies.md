# Vacature-combositemap — maximaal twee taxonomieën

Datum: 19 oktober 2026

Omgeving: LIVE (`https://thefieldservicecompany.nl`)

## Besluit

De eerste indexeringsfase publiceert uitsluitend vacaturecombinaties met exact
twee verschillende taxonomieën in de combo-sitemap. Combinaties met meer dan
twee taxonomieën blijven technisch bereikbaar, maar mogen niet worden
geïndexeerd.

## Uitgevoerde wijziging

- Actieve snippet 148:
  `Vac Combo Archives | SiteMap [v2.3] PHASE 1 - max 2 taxonomies`.
- `TFSC_COMBO_SITEMAP_MIN_DIMENSIONS` staat op `2`.
- `TFSC_COMBO_SITEMAP_MAX_DIMENSIONS` staat op `2`.
- De cache-sleutel is vernieuwd naar
  `tfsc_combo_sitemap_links_v5_phase1_max2`.
- Actieve snippet 193:
  `Vac Combo Archive | SEO & Canonical [v2.3] PHASE 1 - max 2 taxonomies`.
- `TFSC_COMBO_SEO_MAX_TAXONOMIES` staat op `2`.
- De interne uitleg en pluginomschrijving noemen eveneens maximaal twee
  taxonomieën.

Vooraf en tijdens het herstel zijn de inactieve veiligheidskopieën 243, 244,
245 en 246 bewaard. Er is niets aan vacatures, taxonomietermen, relaties of
pagina-inhoud veranderd.

## Sitemap opnieuw opgebouwd

Na de snippetwijziging bevatte Rank Math nog de oude sitemapindeling in de
cache. De eenvoudige, eerder gebruikte werkwijze bleek voldoende:

1. Rank Math-module **Sitemap** uitschakelen.
2. Controleren dat de module daadwerkelijk uit staat.
3. Dezelfde module weer inschakelen.
4. De openbare sitemapindex opnieuw uitlezen.

Een tijdelijk geteste zwaardere globale cacheverwijdering vanuit snippet 148
bleek onnodig belastend. Die variant is volledig teruggedraaid. De actieve
snippet bevat geen permanente cache-bypass en geen globale Rank Math-
cacheverwijdering.

## Live verificatie

De openbare `sitemap_index.xml` bevat precies deze twee combo-sitemaps:

- `https://thefieldservicecompany.nl/combo-vacatures-sitemap1.xml`
- `https://thefieldservicecompany.nl/combo-vacatures-sitemap2.xml`

Resultaat van de volledige URL-controle:

| Controle | Resultaat |
| --- | ---: |
| URL's in sitemapdeel 1 | 501 |
| URL's in sitemapdeel 2 | 299 |
| Totaal | 800 |
| Unieke URL's | 800 |
| URL's met exact twee taxonomieën | 800 |
| URL's met een ongeldig aantal taxonomieën | 0 |

Frontendcontroles:

- `https://thefieldservicecompany.nl/vacatures/functienaam/commissioning-engineer/bedrijfssector/energie/`
  geeft `follow, index` en een self-canonical.
- `https://thefieldservicecompany.nl/vacatures/functienaam/commissioning-engineer/bedrijfssector/energie/dagen-internationaal/geen/`
  geeft `follow, noindex` en geen canonical.
- De homepage geeft HTTP 200 en reageerde na herstel normaal.

## Terugzetten

De actieve versie van snippet 148 kan indien nodig worden teruggezet vanuit
inactieve snippet 245. De oorspronkelijke veiligheidskopieën van snippets 148
en 193 zijn respectievelijk 243 en 244. Snippet 246 bewaart de tijdelijke stand
vóór het terugdraaien van de zwaardere cachepoging en hoort inactief te
blijven.
