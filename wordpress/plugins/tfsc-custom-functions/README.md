# TFSC Custom Functions 0.3.0 — opgeschoonde kandidaat

Status op 1 november 2026: broncode en herstelversie opgeslagen in GitHub. **Deze versie is nog niet op de live website geïnstalleerd.**

De live versie 0.2.0 gebruikt een vast CSS-versienummer en bevat oude instellingen. De kandidaat vernieuwt de CSS-URL automatisch na een bestandswijziging, herstelt toetsenbordfocus en voorkomt dat een link midden in een opsomming als een aparte kolom wordt behandeld.

## Bestanden

- `tfsc-custom-functions.php`: actuele functies en de automatische CSS-versie.
- `tfsc-custom-stylesheet.css`: globale frontendstijlen.
- `audit/`: bevindingen, gebruiksinventarisatie en uitgevoerde controles.
- [Ongewijzigde live back-up](../../exports/plugins/2026-11-01-before-custom-functions-cleanup/): alle drie de oorspronkelijke bestanden en SHA-256-controlegetallen.

## Belangrijkste wijzigingen

- CSS-versie via `filemtime()` in plaats van de vaste waarde `0.1.0`. De bestaande naam `theme-override` en laadprioriteit blijven behouden.
- 19 niet aangetroffen maatwerkregels zijn gearchiveerd in `unused-selectors.css`, buiten de actieve plugin. Eén exact dubbel stijlblok, één leeg blok en uitgeschakelde voorbeeldcode zijn verwijderd.
- Zichtbare focus voor toetsenbordgebruik; de focus van verborgen checkboxinputs wordt op hun zichtbare vakje getoond.
- Opsommingen blijven normale tekstregels. Links en vetgedrukte woorden worden geen losse flexkolommen. Vierkante opsommingstekens blijven behouden.
- Brede tabellen schuiven binnen hun eigen kader, zonder de mobiele pagina uit te rekken.
- Checkboxanimaties respecteren de instelling voor minder beweging.
- Twee oude Inter-preloads vervangen door de daadwerkelijk gebruikte lokale Inter Tight- en Barlow-lettertypen.
- De algemene uitschakeling van de WordPress-blokbibliotheek is verwijderd. WordPress kan daardoor zelf de stijlen voor onder andere tabellen en afbeeldingen laden.
- PHP-functienamen hebben een TFSC-prefix. Leesduur kan met ontbrekende berichten omgaan en telt woorden met accenten beter. De bestaande shortcode-namen blijven werken.

De CSS gaat van **18.544 naar 16.786 bytes**: 1.758 bytes minder, ongeveer 9,5%. Dit is een onderhouds- en betrouwbaarheidsverbetering; er is geen gemeten snelheidswinst geclaimd.

## Plaatsen en terugzetten

1. Test de kandidaat eerst in een volledige WordPress/Elementor-testomgeving. Controleer vooral de herstelde blokstijlen, opsommingen, knoppen en formulieren op desktop en mobiel.
2. Plaats de twee bestanden in de bestaande map `wp-content/plugins/tfsc-custom-functions/`.
3. Verwijder het niet-geladen `custom-stylesheet-wp-admin.css` alleen als de beheerde map exact aan deze kandidaat gelijk moet zijn. De oude versie staat in de back-up.
4. Vernieuw de LiteSpeed-pagina- en CSS-cache en een eventuele CDN-cache. Anders kan oude HTML naar de vorige CSS-versie blijven verwijzen.
5. Controleer dat de pagina naar een nieuwe, op wijzigingsdatum gebaseerde `?ver=` verwijst. Open een vacature, artikel, archief en formulier. Verstuur geen echte sollicitatie als test.

Terugzetten: herstel de drie originele bestanden uit de back-up in dezelfde pluginmap en vernieuw daarna de caches.

Deze wijziging vernieuwt bestanden bij een volgende paginalading. Een reeds geopende pagina krijgt hierdoor geen live stylesheet-update. De parameter vernieuwt de browsercache; het is geen Git-versiegeschiedenis. De broncodegeschiedenis staat apart in GitHub.

## Uitgevoerde controles en grenzen

171 openbare HTML-pagina's, 96 gepubliceerde/concept-Elementor-templates en gepubliceerde inhoud via REST zijn onderzocht. 18 functionele PHP-controles op PHP 8.3.33 en 41 CSS-/bronnencontroles zijn geslaagd. De PHP-controles gebruiken WordPress-stubs, geen volledige WordPress-installatie.

De lokale browsertoegang en mobiele scherminstelling werken nu. Zes paginatypen zijn met oude en nieuwe CSS vergeleken op desktop (1280 × 720) en mobiel (390 × 844): homepage, adviesformulier, vacature, blogartikel, vacaturearchief en Kennisbankartikel. Dit zijn 12 voor/na-paren en 24 paginaladingen. De inhoud en aantallen gemeten elementen blijven gelijk. De gemeten stijlen verschillen uitsluitend bij de bedoelde lijstaanpassingen.

Geen nieuwe kopie heeft horizontale pagina-overloop. De blog- en Kennisbankkopieën hadden die op mobiel wel met de oude CSS. Brede tabellen schuiven nu binnen hun eigen kader. De browser bevestigt ook dat de tekst 'Basissalaris afhankelijk van jouw kennis en werkervaring' één doorlopende zin is geworden. De formuliercheckbox heeft een zichtbare focusrand en is met de spatiebalk aangevinkt; het zichtbare vinkje verscheen. Geen formulier is verstuurd.

Dit zijn statische HTML-kopieën met scripts en iframes verwijderd en formulierverzending uitgeschakeld. Afbeeldingen die via lazy loading verschijnen zijn placeholders. Dynamische filters, menu's en popups zijn hiermee niet volledig getest. De nieuwe PHP-plugin is nog niet binnen een volledige WordPress-/Elementoromgeving geladen. Vooral het hervatten van native WordPress-blokstijlen vereist nog die controle. De meetresultaten staan in `browser-validation.json`.

Zie [het auditrapport](audit/bevindingen.md) voor de functiebeoordeling, gebruiksinventaris en resterende aandachtspunten.

De PHP-controles zijn herhaalbaar met `npm install` en `npm test` in `audit/` (Node.js vereist). Dit testscript en de ontwikkeldependencies zitten niet in het installatiepakket.
