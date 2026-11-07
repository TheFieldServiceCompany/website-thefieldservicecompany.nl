# Alle pluginpakketten: aansluiting en extra aanvalstests

De runner gebruikt nieuwe WordPress-/SQLite-instanties in PHP-WASM, de exact geverifieerde huidige bronselectie van twaalf eigen plugins en alleen synthetische testgegevens. Uitgaande WordPress-HTTP-aanroepen zijn geblokkeerd. Er worden geen productie-instellingen, echte inzendingen of credentials gekopieerd.

```text
node run.mjs PHP_VERSION SOURCES WORDPRESS SQLITE NODE_MODULES MANIFEST DEPENDENCIES RESULTS PRIVATE_RELEASE_ARCHIVES
```

Gebruik PHP `8.3` en `8.5`, WordPress 7.1.4, SQLite Database Integration 2.2.14 en `@php-wasm/node` / `@php-wasm/universal` 3.1.61. De argumenten vóór `PRIVATE_RELEASE_ARCHIVES` hebben dezelfde betekenis als bij de [oorspronkelijke updaterproef](../private-updates-tests-2026-11-07/README.md). Het [complete bronmanifest](../evidence/private-plugin-updates-complete-manifest-2026-11-07.json) legt alle 96 geïnstalleerde bestandshashes vast.

De volledige basis-ZIP's staan in het [besloten architectuurarchief](https://github.com/TheFieldServiceCompany/architecture/tree/main/wordpress/releases/2026-11-07). Gebruik die lokale map als laatste argument. De runner leest uitsluitend de negen in `release-plan.json` genoemde archieven en controleert hun beoordeelde SHA-256 vóór het kopiëren naar de wegwerpomgeving. Die private bronarchieven worden niet in deze openbare testmap verspreid. De ZIP's onder `fixtures/` zijn uitsluitend onschuldige kunstmatige plugins en geweigerde aanvalsvormen.

De drie opeenvolgende proeven zijn:

1. `onboarding.php`: inspectie en publicatie van alle negen volledige huidige pakketten, gevolgd door een echte native WordPress-bulkupdate. Alleen in de fixture krijgen de vooraf geïnstalleerde hoofdheaders tijdelijk 0.0.0 zodat de normale updateprocedure kan worden getest. Daarna wordt ieder uitgepakt bestand, inclusief CSS en overige assets, met zijn originele ZIP-inhoud vergeleken. Actieve identiteit, een database-instelling en auto-updateselectie moeten behouden blijven. 75 controles, negen native updates per runtime.
2. `additional-attacks.php`: echte WordPress cookie-noncecontrole zonder/voor een vervalste/geldige nonce, afzonderlijk ontbrekende beheercapabilities, ongeldige gestructureerde invoer, base64/ZIP- en bodygrenzen, dubbele/Windows-/absolute/colonpaden, entry-/hoofd-PHP-grenzen, verkeerde updatercontext, downgrades, gewijzigde goedkeuringsvelden en een bestaande databaselock bij een verouderde objectcache. 33 controles per runtime.
3. `updater.php`: de oorspronkelijke 60 updatercontroles en een aanvullende echte native fixture-update blijven geslaagd, inclusief rollen, metadata-/checksumcontrole, intrekken/hergoedkeuren, pakketretentie en expliciete native auto-updatekeuze.

De cookieproef gebruikt de echte WordPress-authenticatiecontrole in de wegwerpruntime; zij is geen volledige browser-/HTTP-sessietest. De native updates worden uitsluitend lokaal uitgevoerd. Op productie worden huidige identieke basisversies vrijgegeven zonder de pluginbestanden opnieuw te installeren.

De [resultaten](../evidence/private-plugin-updates-complete-2026-11-07.json) bevatten alle controles, de herhaalde algemene beveiligingsproeven en de geschoonde productiecontrole. De bekende SQLite-deprecation, doelbewust geblokkeerde core-updatechecks en de bestaande vroege Cache Warmer-vertaling blijven omgevingsmeldingen. Een volledige formulier-/CRM-/cloudketen, WAF-omzeiling, massale gelijktijdige HTTP-belasting en multisite zijn hiermee niet bewezen.
