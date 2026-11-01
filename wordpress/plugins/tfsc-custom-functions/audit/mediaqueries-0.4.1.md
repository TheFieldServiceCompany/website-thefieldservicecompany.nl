# Mediaquery-controle en herstel 0.4.1 — 1 november 2026

## Uitkomst

De mobiele mediaquery in de eigen stylesheet stond op `max-width:768px`. Elementor gebruikt mobiel tot en met 767 px, tablet vanaf 768 tot en met 1024 px en desktop vanaf 1025 px. De regel is op de live website gewijzigd naar `max-width:767px`.

In de overige onderzochte website-eigen frontendcode is geen tweede afwijkende grens voor deze Elementor-weergaven gevonden. Er zijn geen andere mediaqueries alleen op basis van een getal veranderd.

## Gecontroleerde bronnen

- 171 openbare pagina-adressen: 169 geven HTTP 200; twee geven een 301 naar een al onderzochte bestemming.
- Alle 80 bij deze pagina’s aangetroffen stylesheets geven HTTP 200 en zijn op mediaqueries onderzocht.
- Via REST: 28 pagina’s, 96 gepubliceerde/concept-Elementor-bibliotheekitems, 4 Elementor Custom Code-items, 4 blogs, 58 Beroepengids-artikelen, 76 Kennisbankartikelen, 63 vacatures en 68 ingevulde vacatures.
- 32 actieve Code Snippets; inactieve snippetcode is onderscheiden van actieve code.
- Aanvullende WordPress-CSS: geen post en geen inhoud aanwezig voor het actieve thema.
- Bronbestanden van het actieve thema en actieve eigen TFSC-plugins: PHP, CSS en JavaScript. Vendor-/node_modules-mappen, symlinks en bestanden groter dan 1 MB zijn niet doorzocht.

De inventaris bevat 479 aangetroffen media-/breedtevoorwaarden uit opgeslagen inhoud, inline code en geladen CSS. Deze telling bevat herhaling tussen pagina’s. Het is geen browsercontrole van elke pagina bij iedere breedte en geen volledige crawl van alle vacaturecombinaties.

## Wat bleef terecht staan?

| Regel | Beoordeling |
| --- | --- |
| `max-width:767px` | Correcte bovengrens voor mobiel. |
| `max-width:1024px` | Correcte bovengrens voor mobiel en tablet. |
| JavaScript `innerWidth < 768` | Correct: bij gehele viewportbreedtes hetzelfde gebied als t/m 767 px. Niet vervangen door `< 767`. |
| MatchMedia met 767/1024 | Correct; aangetroffen in het vacature-comboarchief. |
| 782 px in beheerpagina’s van TFSC Cache Warmer en Form Bridge | Betreft WordPress-beheer, geen Elementor-frontendgrens. Niet gewijzigd. |
| 600 px in Mediafilters en 900 px in Structured Data-beheer | Onderdeelspecifieke beheeropmaak. Niet gewijzigd. |
| Print-, hover-, oriëntatie- en reduced-motion-queries | Andere voorwaarden dan de Elementor-paginabreedte. Niet gewijzigd. |
| Oude exports en back-ups met 768 px | Historische herstelbestanden, geen actieve stylesheet. Niet gewijzigd. |

## Exacte wijziging en bescherming van bestaand gedrag

In de actieve CSS is uitsluitend de mediaqueryvoorwaarde van 768 naar 767 px veranderd. `.hide-mobile`, de 1024-pixelgroep, popuphoogtes, marges, padding en scrollinstellingen zijn bytegelijk gebleven. De PHP-logica is niet gewijzigd; alleen de versiegegevens zijn verhoogd van 0.4.0 naar 0.4.1.

Er is een verse back-up gemaakt vóór vervanging. De server controleerde de hashes tegen de geïnventariseerde versie om gelijktijdige wijzigingen niet te overschrijven. De Elementor-kit is niet opnieuw opgeslagen; de instellingenhash vóór en na is gelijk. LiteSpeed is vernieuwd en de tijdelijke onderhoudsroute is verwijderd (HTTP 404).

## Live browsercontrole

| Breedte | Elementor-weergave | Basistekst | Artikelalinea | H1 |
| --- | --- | --- | --- | --- |
| 766 px | Mobiel | 17 px | 17 px | 30 px |
| 767 px | Mobiel | 17 px | 17 px | 30 px |
| 768 px | Tablet | 18 px | 18 px | 30 px |
| 769 px | Tablet | 18 px | 18 px | 30 px |
| 1023 px | Tablet | 18 px | 18 px | 30 px |
| 1024 px | Tablet | 18 px | 18 px | 30 px |
| 1025 px | Desktop | 18 px | 18 px | 40 px |
| 1026 px | Desktop | 18 px | 18 px | 40 px |

Getest op het live Kennisbankartikel ‘Wat is een AI-agent?’. Op deze acht breedtes is geen horizontale pagina-overloop gemeten. De normale weergavewissels op 768 en 1025 px blijven bestaan; de afwijkende extra wissel tussen 768 en 769 px is verwijderd. De eerder gemeten combinatie van basistekst 18 px en artikelalinea 17 px op precies 768 px komt niet meer voor.

De actuele stylesheet is teruggelezen en komt overeen met het geplaatste bestand. De structuurparser vond geen CSS-fout. WordPress bevestigt actieve pluginversie 0.4.1.

Zie `0.4.1/` voor de inventaris, browsermetingen, hashes en controle op ongewijzigde popup-/hide-mobile-regels. Bronexports met mogelijk niet-openbare inhoud zijn niet in GitHub opgenomen.
