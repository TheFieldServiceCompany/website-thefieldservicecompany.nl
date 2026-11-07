# Reproduceerbare beveiligingsproef — 7 november 2026

Deze tests onderzoeken de exacte geïnstalleerde versies uit het [manifest](../evidence/custom-plugin-security-2026-11-07-manifest.json). Ze maken een nieuwe WordPress-database in PHP-geheugen. Gebruik ze uitsluitend met een afzonderlijke kopie van de bronbestanden. Er zijn geen productie-inloggegevens nodig.

**Een succesvolle uitvoering is geen veiligheidsverklaring.** Deze auditproef reproduceert ook bestaande fouten en schrijft die onder `confirmed_findings` weg. De uploadproef verwacht dat de onderzochte kwetsbare versie de vervalste verwijzing accepteert. Voor een reparatierelease moeten deze scenario’s als regressietests worden omgekeerd naar weigeren/behouden en mogen zij niet als ongewijzigde groene releasepoort worden gebruikt.

Benodigd: Node.js, `@php-wasm/node` en `@php-wasm/universal` 3.1.61, schone WordPress 7.1.4-bron, SQLite Database Integration 2.2.14, de eigen pluginbestanden volgens het manifest, en de vier daar genoemde geïnstalleerde Elementor Pro-afhankelijkheidsbestanden. Geen productie-`wp-config.php`, uploads of database meenemen. Het manifest controleert de eigen bronhashes vóór de uitvoering. Commerciële afhankelijkheidsbron wordt apart aangeleverd binnen de eigen licentie/toegang.

```text
node run.mjs 8.5 /bron/eigen-plugins /bron/wordpress /bron/sqlite-database-integration /pakketten/node_modules ../evidence/custom-plugin-security-2026-11-07-manifest.json /bron/afhankelijkheden /uitvoer
node run.mjs 8.3 /bron/eigen-plugins /bron/wordpress /bron/sqlite-database-integration /pakketten/node_modules ../evidence/custom-plugin-security-2026-11-07-manifest.json /bron/afhankelijkheden /uitvoer
```

De eigen bronmap bevat de elf pluginmappen met de oorspronkelijke namen. De afhankelijkheidsmap bevat `elementor-pro/modules/forms/...`. Het testprogramma kopieert de bestanden naar het tijdelijke PHP-bestandssysteem, installeert WordPress en activeert uitsluitend de elf eigen plugins. Elementor Pro en de overige productieplugins worden niet integraal geïnstalleerd. De uploadproef laadt de werkelijke Form_Record-, Ajax_Handler- en Upload-klassen met minimale Utils-/Field_Base-aansluiting; formulier/CAPTCHA/ontvanger/cloud blijven buiten die proef.

- `security.php`: rollen, REST, beheerhandlers/nonces, schema-/SQL-/bestandsinvoer, tokenintegriteit en twee misbruikscenario’s.
- `stress.php`: zestig vroege diagnoseschrijfacties, drie reeksen van duizend ongeldige inputs, begrensde combinaties, locks, een groot artikel en retries.
- `upload-provenance.php`: bestaand testdocument wordt zonder nieuwe upload aanvaard door de onderzochte versie.
- `run.mjs`: isolatie, laden en directe aanroep van de 56 PHP-bestanden.

De externe HTTP-blokkade geldt voor WordPress HTTP-aanroepen; de tests roepen geen echte cURL-probe of cloudverwerking aan. Dit is geen gelijktijdige netwerklasttest. Meetwaarden zijn lokale uitvoeringstijden en voorspellen geen productiecapaciteit. Logs kunnen verwachte waarschuwingen bevatten van de geblokkeerde updater, SQLite en de bewuste database-ID-botsing. De audit beschrijft die afzonderlijk.
