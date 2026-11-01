# Plugins

Wordt ingevuld tijdens de inventarisatie van de huidige website.

## TFSC Custom Functions

- Broncode kandidaat 0.3.0: [plugins/tfsc-custom-functions](plugins/tfsc-custom-functions/).
- [Audit en functiebeoordeling](plugins/tfsc-custom-functions/audit/bevindingen.md).
- [Exacte live back-up 0.2.0 van 1 november 2026](exports/plugins/2026-11-01-before-custom-functions-cleanup/).
- [Installatiepakket kandidaat 0.3.0](plugins/dist/tfsc-custom-functions-0.3.0-kandidaat.zip).
- Doel: globale frontend-CSS met automatische URL-versie, lokale fonts, zoekresultatentitel en bestaande shortcodes.
- Status: bron- en PHP-controles geslaagd; kandidaat staat in GitHub en is nog niet live geïnstalleerd. Statische desktop-/mobiele vergelijkingen zijn geslaagd; de volledige WordPress-runtimecontrole staat nog open.

## TFSC Mediafilters

- Broncode: [plugins/tfsc-media-filters](plugins/tfsc-media-filters/).
- Installatiepakket: [tfsc-media-filters-1.0.0.zip](plugins/dist/tfsc-media-filters-1.0.0.zip).
- Doel: mediabestanden filteren op extensie, pixelafmetingen, bestandsgrootte,
  oriëntatie en alt-tekst, gecombineerd met HappyFiles-mapselecties.
- Uitvoerlocatie: WordPress-beheer; HappyFiles optioneel.
- Status: 1.0.0 gebouwd en lokaal getest; nog niet op de website geïnstalleerd.
- Na activeren: eenmalig indexeren via Media → Mediafilters.
- Controleer de geïnstalleerde HappyFiles-versie op de dev-site voor productiegebruik.

