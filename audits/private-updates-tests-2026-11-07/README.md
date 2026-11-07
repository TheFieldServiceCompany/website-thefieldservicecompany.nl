# Private pluginupdates: geïsoleerde aanval- en installatietest

`updater.php` gebruikt de echte WordPress REST-router, capabilities, catalogus, updates en native `Plugin_Upgrader::bulk_upgrade()`. De ZIP-fixtures zijn kunstmatige, onschuldige plugins; zij worden uitsluitend in de wegwerpomgeving verwerkt. De traversal- en symlinkfixtures moeten worden geweigerd. Publiceer deze fixtures nooit op productie.

`run.mjs` start PHP-WASM met een verse in-memory WordPress-/SQLite-installatie, kopieert de geverifieerde pluginbronnen en blokkeert externe WordPress-HTTP-aanroepen. Het manifest bevat de SHA-256 per bronbestand en de actieve pluginidentiteiten. De commerciële afhankelijke bronbestanden uit de audit blijven privé en worden niet in deze repository verspreid. Deze updaterproef doet geen volledige Elementor-/CRM-/B2-inzending.

Benodigd: Node met `@php-wasm/node` en `@php-wasm/universal` 3.1.61, officiële WordPress 7.1.4-bron, SQLite Database Integration 2.2.14 en de private geverifieerde bronselectie. Deze hulpmiddelen zijn alleen testgereedschap; zij worden niet in WordPress geïnstalleerd.

Argumenten:

```text
node run.mjs PHP_VERSION SOURCES WORDPRESS SQLITE NODE_MODULES MANIFEST DEPENDENCIES RESULTS
```

Voer eenmaal met `8.3` en eenmaal met `8.5` uit. `SOURCES` bevat de twaalf eigen pluginmappen. `MANIFEST` is de bijpassende JSON-bronlijst; zie de [vastgelegde hashes](../evidence/private-plugin-updates-manifest-2026-11-07.json). `DEPENDENCIES` is de bestaande private auditmap met eventuele bronafhankelijkheden; een lege map volstaat voor uitsluitend de updaterproef. `RESULTS` is een eigen lokale uitvoermap voor diagnostiek. De fixturemap naast de runner wordt automatisch gebruikt.

De test bewijst onder meer toegangsweigering voor vier niet-bevoegde rollen, veilige ZIP-afhandeling, checksum- en metadatacontrole, versie-immutabiliteit, intrekken/hergoedkeuren, afscherming van andere updateproviders, cataloguslocking, native plugininformatie, echte installatie, instellingenbehoud, begrensde pakketretentie en expliciete auto-updatekeuze. De [bewijsgegevens](../evidence/private-plugin-updates-2026-11-07.json) bevatten de controles per runtime.

De direct-aanroepcontrole vereist dat alle PHP-bestanden uit de pluginselectie bij een ontbrekende WordPress-bootstrap zonder ongewenste uitvoer stoppen. De SQLite-runtime kan deprecationmeldingen geven; externe updatechecks zijn doelbewust geblokkeerd. De oude Cache Warmer meldt een vroeg geladen vertaling. Die bekende omgevings-/bestaande-pluginmeldingen worden niet als nieuwe updaterfouten gepresenteerd.
