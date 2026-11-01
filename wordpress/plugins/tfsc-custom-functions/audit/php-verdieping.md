# Verdiepende PHP-controle — 1 november 2026

De functies uit de exacte live back-up 0.2.0 zijn opnieuw vergeleken met kandidaat 0.3.0. De kandidaat bevat zes benoemde callbacks en één extra filter dat externe Google Fonts van Elementor uitschakelt. Er is geen aanvullende aantoonbare functionele fout in de kandidaat gevonden. De al voorbereide correcties blijven nodig; de live plugin is niet gewijzigd.

## Geconstateerde problemen in de oude versie

| Onderdeel | Bevinding | Status in kandidaat |
| --- | --- | --- |
| CSS laden | Vaste versie `0.1.0` en een dubbele slash in de URL. | Hersteld met wijzigingsdatum als versie en correcte padconstructie. |
| Fontpreloads | Verwijzen naar oude Inter-bestanden terwijl de huidige stijl Inter Tight en Barlow gebruikt. | Huidige lokale fonts worden vooraf geladen. |
| Leesduur | Veronderstelt altijd een globale post; ontbrekende post kan waarschuwingen opleveren. `str_word_count()` kan Nederlandse woorden met accenten splitsen. Enkelvoud geeft `1 minuten`. | Ontbrekende post afgevangen, Unicode-woordtelling en enkelvoud toegevoegd. |
| Globale functienamen | Namen als `archive_callback`, `tmm_add_to_header` en `wpdocs_override_stylesheets` zijn generiek. | Unieke `tfsc_custom_functions_`-prefix. Geen externe verwijzingen naar de oude callbacknamen gevonden in onderzochte templates en actieve snippets. |
| WordPress-blokstijlen | De stijlen worden globaal uitgezet, hoewel de site onder andere tabellen en afbeeldingen als blokken bevat. | Globale uitschakeling verwijderd. Het precieze visuele effect van WordPress’ eigen laadgedrag vraagt nog een volledige omgevingstest. |
| Direct uitvoeren van PHP | De oude plugin heeft geen `ABSPATH`-controle. Direct openen buiten WordPress kan eindigen in een fout wegens ontbrekende WordPress-functies. Dit is geen aangetoonde mogelijkheid tot code-uitvoering door bezoekers. | Guard toegevoegd. |

## Oude code en onduidelijke afhankelijkheden

- De filter voor de recente-reacties-widget schakelde alleen widget-CSS uit. De oude comment suggereerde onterecht dat een sidebar uit de HTML werd verwijderd. Geen gebruik van die widget aangetroffen; filter verwijderd uit de kandidaat.
- De Eicons-code was al uitgecommentarieerd. Verwijderen van die commentcode verandert geen actief gedrag. Eicons worden niet alsnog uitgeschakeld.
- Een oud fontpreloadvoorbeeld en legacycommentaar zijn vervangen door concrete documentatie.
- `custom-stylesheet-wp-admin.css` wordt door deze plugin niet geladen. Ook geen verwijzing gevonden in onderzochte actieve snippets of inhoud. Het bestand blijft alleen in de back-up.
- `rm_get_estimated_time_to_read` staat in opgeslagen Elementor-template 18786. Of deze template momenteel wordt ingezet, is niet aangetoond. De shortcode blijft beschikbaar.
- Voor `current_page_url` is geen bronverwijzing gevonden. De shortcode blijft voor compatibiliteit beschikbaar. Hij retourneert het pad via `home_url($wp->request)`, zonder queryparameters. Dat was al het gedrag van de oude versie en is bewust niet gewijzigd.
- De Font Awesome-optimalisatie blijft voorlopig behouden. Er zijn geen fontgebaseerde `i`/`span`-iconen in de onderzochte HTML gevonden; iconen worden als SVG uitgevoerd. De controle van dynamisch ingevoegde Elementor-onderdelen staat nog open. De registratie van Font Awesome in template-instellingen alleen bewijst geen defect of overbodigheid.

## Aanvullende uitvoering met WordPress-code

De 18 bestaande PHP-controles waren uitgevoerd met vereenvoudigde WordPress-stubs. Daar zijn nu **13 aanvullende geslaagde controles** naast gezet die de echte WordPress-implementaties van hooks, shortcodes, tekstescaping en tagverwijdering laden.

Getest:

- Beide publieke shortcode-namen registreren en uitvoeren via `do_shortcode()`.
- Behoud van de stylesheetprioriteit in de echte WordPress-hookregistratie.
- Uitschakeling van externe Elementor-fonts via de echte filterketen.
- Gewone archieftitel blijft gelijk; zoektekst kan geen scriptmarkup invoegen en wordt niet dubbel geëncodeerd.
- Woorden met accenten, HTML-spaties, script-/style-inhoud en shortcode-attributen verstoren de leesduurberekening niet.
- Een ontbrekende post wordt ook via de echte shortcodeparser zonder waarschuwing afgehandeld.

De beschikbare lokale WordPress-kernbron was versie **6.8.3**; PHP was **8.3.33**. Site-instellingen, berichtopvraging, zoekcontext en URL-helpers zijn fixtures. Dit is een gerichte integratiecontrole van hulpfuncties, geen volledige test met de live WordPress-versie, database of Elementor. De resultaten staan in `core-validation.json`.

Herhalen: installeer de testdependencies volgens de README en voer vanuit `audit/` uit:

```sh
node verify-core.mjs /pad/naar/wordpress
```

Er zijn geen extra productieaanpassingen toegevoegd alleen omdat code oud oogt. Niet-aangetoond gebruik is apart vastgelegd; bestaande shortcode-namen en doelbewuste compatibiliteit blijven behouden.
