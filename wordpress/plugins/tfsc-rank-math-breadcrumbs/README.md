# TFSC Rank Math Breadcrumbs 0.1.1

Deze uitbreiding laat de bestaande Elementor-breadcrumbwidgets werken met Rank Math Free. De templates bevatten het widgettype `breadcrumbs`. De plugin registreert datzelfde type en roept `rank_math_the_breadcrumbs()` aan. Daardoor blijven de opgeslagen widget-ID’s, stijlinstellingen, CSS-klassen en de filters van TFSC-snippets 99 en 199 bruikbaar.

Bij actieve Rank Math Pro slaat de uitbreiding haar registratie over. Na deactivering van Pro neemt ze het widget bij de volgende aanvraag over. Elementor en Rank Math Free moeten actief zijn en de breadcrumbfunctie van Rank Math moet aanstaan.

## Installatie en live controle

1. Maak een herstelkopie van de huidige site. Upload `tfsc-rank-math-breadcrumbs-0.1.1.zip` via WordPress → Plugins → Nieuwe plugin → Plugin uploaden en activeer de uitbreiding terwijl Rank Math Pro nog actief is.
2. Leg op de testpagina’s de huidige breadcrumbs en vormgeving vast. Deactiveer daarna uitsluitend Rank Math Pro. Laat Elementor, Elementor Pro en Rank Math Free actief.
3. Vernieuw de paginacache van de test-URL’s en vergelijk de zichtbare paden, linkbestemmingen, kleuren, marges, mobiele scroll en `BreadcrumbList`. Controleer het vacature-comboarchief, het Beroepengids-tussenarchief, een detailarchief, een artikel en het blog.
4. Controleer in de Elementor-editor of het bestaande widget als TFSC Breadcrumbs beschikbaar is. Controleer ook opnieuw gegenereerde Elementor-CSS voordat de overstap definitief wordt afgerond.

Herstel gaat door Rank Math Pro opnieuw te activeren. De uitbreiding laat het widget dan weer aan Pro over. Daarna kan de uitbreiding worden gedeactiveerd.

## Getest op 7 november 2026

- 27 controles met PHP 8.3.33, de echte WordPress-hookregistratie, Rank Math Free 1.0.281 en de opgeslagen bronnen van snippets 99 en 199. De Elementor-controls en querycontext gebruiken hier fixtures. De uitvoer komt per getest paginatype byte voor byte overeen met de Free-renderer.
- 37 controles in een tijdelijke, volledige WordPress 6.8.3-installatie met SQLite, Elementor 4.3.6 en Rank Math Free 1.0.281. Het normale Elementor-widgetregister herkent de opgeslagen data. Getest zijn een Beroepengids-tussenarchief, detailarchief, artikel, artikelarchief en een combo-context, de widget-ID en klassen, de binnenste CSS-wrapper, breadcrumb-schema en opnieuw gegenereerde stijlen.
- In de volledige test zijn de artikel- en termqueries echte WordPress-queries. De virtuele basisquery en de combo-routergegevens zijn nagebootst. Snippets 99 en 199 worden ongewijzigd geladen. Elementor Pro, snippet 113, de volledige router 406 en de schema-aanpassing van 193 zitten buiten deze lokale testinstallatie.
- Na de ontwikkelvalidatie is versie 0.1.1 op productie geïnstalleerd en actief bevonden. Rank Math Pro blijft actief. De proef met Pro uit en de visuele controle op desktop en mobiel staan open. De [tweede functionaliteitsaudit](../../../audits/2026-11-07-rank-math-pro-feature-audit.md) beschrijft aanvullende Pro-afhankelijkheden die eerst moeten worden behandeld.

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
