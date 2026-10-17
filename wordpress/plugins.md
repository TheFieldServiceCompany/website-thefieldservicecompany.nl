# Plugins

Inventarisdatum: 16 oktober 2026.

- 31 geïnstalleerd
- 24 actief
- 7 inactief

Het volledige, niet-gevoelige manifest staat in
`wordpress/plugin-manifest/dev-2026-10-16.json`.

## Eigen actieve plugins

- `tfsc-custom-functions` 0.1
- `tfsc-schema-read-endpoint` 2.0.0
- `snip-tfsc.php_` 0.2.0 (in deze repository genormaliseerd naar `snip-tfsc`)

De aangetroffen bron staat onder `wp-content/plugins/` en is als status-quo
opgenomen. Dat betekent niet dat alle code al geschikt is voor productie.

## Belangrijke compatibiliteit

- Elementor Core 4.3.5 en Elementor Pro 4.2.3 lopen op dev uit de pas.
- Search & Filter meldt eveneens een compatibiliteitswaarschuwing met Elementor.
- Eerst regressietesten, daarna Elementor Pro naar een bijpassende 4.3.x-versie
  brengen. Niet rechtstreeks op productie bijwerken.
- Advanced Form Integration Premium blijft voorlopig actief zolang de nieuwe
  CRM-connector nog niet end-to-end is bewezen.
