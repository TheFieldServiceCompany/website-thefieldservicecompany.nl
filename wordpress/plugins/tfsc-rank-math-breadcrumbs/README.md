# TFSC Rank Math Breadcrumbs 0.3.0

Deze uitbreiding laat de bestaande Elementor-breadcrumbwidgets werken met Rank Math Free. De templates bevatten het widgettype `breadcrumbs`. De plugin registreert datzelfde type en roept `rank_math_the_breadcrumbs()` aan. Daardoor blijven de opgeslagen widget-ID’s, stijlinstellingen, CSS-klassen en de filters van TFSC-snippets 99 en 199 bruikbaar.

Bij actieve Rank Math Pro slaat de uitbreiding haar registratie over. Na deactivering van Pro neemt ze het widget bij de volgende aanvraag over. Elementor en Rank Math Free moeten actief zijn en de breadcrumbfunctie van Rank Math moet aanstaan.

Versie 0.2.0 bewaart daarnaast drie bestaande aansluitingen zodra Pro uitstaat:

- `about` en `mentions` uit al gemarkeerde links in de oorspronkelijke berichtinhoud, op hetzelfde `rank_math/json_ld`-moment als Pro. De plugin markeert geen nieuwe links en maakt geen nieuw WebPage-schema aan.
- Matchen van opgeslagen redirectregels inclusief queryparameters, met de vergelijking uit Rank Math Free. Bestaande matchtypes en HTTP-codes blijven door Free beheerd.
- De ingeschakelde lokale sitemap en `locations.kml`, met de generator uit Free. Alleen de bestaande enkele bedrijfslocatie wordt ondersteund; meerdere locaties blijven een Pro-functie.

Alle aansluitingen laten de uitvoering aan Pro over zodra Pro actief is. De uitbreiding wijzigt geen opgeslagen instellingen, inhoud of templates en vervangt geen Rank Math-editorfuncties. De afzonderlijk door de eigenaar gevraagde uitschakeling van zoekintentieanalyse gebeurt eenmalig buiten deze plugin.

## Automatische opruiming vanaf 0.3.0

De uitbreiding bewaart nu ook de relatie tussen een bericht/term en de automatisch door Rank Math gemaakte redirect. Zij gebruikt daarvoor hetzelfde metadata-veld `rank_math_auto_redirect` als Pro. Daardoor blijven zowel bestaande Pro-koppelingen als nieuwe URL-wijzigingen bruikbaar.

Bij definitieve verwijdering van een bericht of term verwijdert zij uitsluitend de expliciet eraan gekoppelde regels via de cachebewuste Free-API. Verplaatsen naar de prullenbak en herstellen verwijderen geen regels. Handmatige regels zonder zo’n koppeling blijven bestaan, ook wanneer hun bestemming hetzelfde is. De uitbreiding zoekt niet achteraf op bestemming om regels te verwijderen en voert bij installatie geen opruimronde uit.

De redirectmodule moet aanstaan. Bij actieve Pro laat de uitbreiding ook deze registratie en opruiming aan Pro over. De installatie wijzigt geen bestaande koppelingen of redirects; toekomstige automatische URL-wijzigingen slaan de koppeling op en toekomstige definitieve verwijderingen activeren de opruiming.

## Installatie en live controle

1. Maak een herstelkopie van de huidige site. Upload `tfsc-rank-math-breadcrumbs-0.3.0.zip` via WordPress → Plugins → Nieuwe plugin → Plugin uploaden. Bij een eerste overstap kan de uitbreiding eerst naast Pro worden geactiveerd. Bij een bestaande Free-installatie moeten Free en deze uitbreiding actief blijven.
2. Leg op de testpagina’s de huidige breadcrumbs en vormgeving vast. Deactiveer daarna uitsluitend Rank Math Pro. Laat Elementor, Elementor Pro en Rank Math Free actief.
3. Vergelijk met verse pagina-aanvragen de zichtbare paden, linkbestemmingen, kleuren, marges, mobiele scroll en `BreadcrumbList`. Controleer het vacature-comboarchief, het Beroepengids-tussenarchief, een detailarchief, een artikel en het blog. Vergelijk ook de volledige JSON-LD, metadata, XML-sitemaps en `locations.kml`. Test queryregels met fixtures, zonder beveiligings-URL’s te activeren.
4. Controleer in de Elementor-editor of het bestaande widget als TFSC Breadcrumbs beschikbaar is. Controleer ook opnieuw gegenereerde Elementor-CSS voordat de overstap definitief wordt afgerond.

Herstel gaat door Rank Math Pro opnieuw te activeren. De uitbreiding laat het widget dan weer aan Pro over. Daarna kan de uitbreiding worden gedeactiveerd.

## Getest op 7 november 2026

- 27 controles met PHP 8.3.33, de echte WordPress-hookregistratie, Rank Math Free 1.0.281 en de opgeslagen bronnen van snippets 99 en 199. De Elementor-controls en querycontext gebruiken hier fixtures. De uitvoer komt per getest paginatype byte voor byte overeen met de Free-renderer.
- 56 controles in een tijdelijke, volledige WordPress 6.8.3-installatie met SQLite, Elementor 4.3.6 en Rank Math Free 1.0.281. Naast widgetregistratie, breadcrumbs en opnieuw gegenereerde CSS zijn de gemarkeerde entiteiten, behoud van de organisatieverwijzing, ontbrekende gegevens, queryvergelijking, lokale sitemap en Pro-voorrang getest.
- Acht afzonderlijke baselinecontroles tonen de Free-werkwijze zonder onze opruimcallbacks: gewone automatische post-/termredirects werken en de wachtwoord-noindex-optie werkt; automatische opruiming ontbreekt daar.
- Achttien aanvullende controles testen de opruimaansluiting met echte WordPress-wijzigingen en verwijderingen: post-/termkoppeling, herhaalde URL-wijzigingen, prullenbak/herstel, definitief verwijderen, bestaande Pro-metadata, onaangetaste handmatige regels, cacheverwijdering, ongeldige gegevens, uitgeschakelde module en voorrang van Pro. Deze bewerkingstests draaien uitsluitend in de tijdelijke lokale database.
- In de volledige test zijn de artikel- en termqueries echte WordPress-queries. De virtuele basisquery en de combo-routergegevens zijn nagebootst. Snippets 99 en 199 worden ongewijzigd geladen. Elementor Pro, snippet 113, de volledige router 406 en de schema-aanpassing van 193 zitten buiten deze lokale testinstallatie.
- De [functionaliteitsaudit](../../../audits/2026-11-07-rank-math-pro-feature-audit.md) en het vervolgverslag beschrijven de actuele productiecontrole en migratiestatus. Lokale tests vormen op zichzelf geen bewijs voor iedere live template of toekomstige pluginversie.

De plugin bevat eigen widgetcode. De breadcrumb-engine en de gestructureerde gegevens worden door Rank Math Free geleverd. De gebruikte opgeslagen instellingen zijn geïnventariseerd op de TFSC-site. Versie 0.1.1 volgt na controle van de geïnstalleerde Pro-bron ook de standaard globale typografie, responsive uitlijningsklassen en de optionele HTML-tag van het bestaande widget.

## Tests herhalen

Node.js en de pakketten `@php-wasm/node` en `@php-wasm/universal` zijn nodig. Gebruik uitgepakte bronmappen van WordPress, Rank Math Free, Elementor en de officiële SQLite Database Integration-plugin. De volledige test maakt een nieuwe database in het tijdelijke PHP-geheugen en kopieert de opgegeven `wp-config.php` en databases niet.

```bash
node tests/run.mjs /pad/naar/node_modules /pad/naar/wordpress /pad/naar/seo-by-rank-math
node tests/full-runtime.mjs /pad/naar/node_modules /pad/naar/wordpress /pad/naar/seo-by-rank-math /pad/naar/elementor /pad/naar/sqlite-database-integration
```

Het installatiepakket bevat uitsluitend de twee PHP-bestanden en deze handleiding. De tests staan in de repository.

## Bronnen

- [Rank Math breadcrumbs en Elementor-integratie](https://rankmath.com/kb/breadcrumbs/)
- [Elementor-widgetregistratie](https://developers.elementor.com/docs/managers/registering-widgets/)
- [Elementor widgetnaam als opgeslagen ID](https://developers.elementor.com/docs/widgets/widget-information.html)
- [Elementor-caching van dynamische widgets](https://developers.elementor.com/docs/widgets/widget-output-caching/)
