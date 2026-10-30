# Back-up vóór opruimen — 30 oktober 2026

`all-snippets.code-snippets.json` bevat de volledige WordPress REST-export van 317 snippets: 31 actief en 286 inactief (inclusief items in de prullenbak). Dit is de situatie na de archief-terugvalwijzigingen en vóór het verwijderen van inactieve snippets.

De export is na upload teruggedownload en byte voor byte gecontroleerd.

SHA-256: `1cc861f7714899273c77bb00bea19196d05cf137c84e144cc219f8050524bdb7`.

## Eén snippet herstellen

1. Download de JSON-export en zoek de gewenste snippet op naam of oorspronkelijk ID.
2. Kopieer de code naar een nieuwe snippet in WordPress, met dezelfde scope, prioriteit en eventuele voorwaarden. Of maak een importbestand met alleen de gewenste snippet uit de `snippets`-lijst.
3. Laat de herstelde snippet eerst inactief. Controleer of een andere actieve snippet dezelfde functies of shortcodes registreert.
4. Activeer pas na controle; test de betrokken pagina.

Een herstelde snippet kan een nieuw ID krijgen. Controleer eventuele condition_id-verwijzingen apart: deze export bewaart de verwijzing, niet de afzonderlijke voorwaardenconfiguratie. Importeer de volledige export niet blind met alle historische versies tegelijk.
