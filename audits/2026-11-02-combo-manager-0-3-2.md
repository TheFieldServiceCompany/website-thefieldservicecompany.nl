# Combo Archive Manager 0.3.2 — 2 november 2026

De nieuwe beheerlaag is actief en stuurt dezelfde bestaande archiefmotor aan. De twee kleine compatibiliteitswijzigingen in snippet 148 en 193 zijn live geplaatst en vergeleken met de geteste bron.

- Maximaal twee taxonomieën; geen diepe releases toegevoegd.
- 47 open vacatures, 1.144 unieke sitemap-URL's (70 één- en 1.074 tweedimensionaal).
- 799 gerichte FSE-voorstellen zijn voorstellen, geen indexeringsvrijgave.
- Beheer van exacte URL-vrijgave en -uitsluiting, taxonomy-allowlist, opgeslagen controles en versiegebonden terugrollen.
- 186 blijft router/query/paginering; geen dubbele motor in de plugin. 376 blijft het extra adviesblok.
- De compatibele 193 blijft expliciete uitsluitingen respecteren wanneer de managerplugin wordt gedeactiveerd.
- Anonieme beheer-API geweigerd; PHP 8.5.10 en 49 integratiechecks geslaagd. Live paginering/filterkeuze opnieuw gecontroleerd.

[Broncode en volledige gebruiksaanwijzing](https://github.com/TheFieldServiceCompany/tfsc-combo-archive-manager/tree/fb08c6ea988a1d3f7188da6a4c45d96f470edc49)

[Volledig uitvoeringsrapport, inclusief gevonden en herstelde opstartproblemen](https://github.com/TheFieldServiceCompany/tfsc-combo-archive-manager/blob/fb08c6ea988a1d3f7188da6a4c45d96f470edc49/docs/2026-11-02-upgrade-and-validation.md)

De initiële allowlist werd in de eerste liveproef te vroeg geladen vóór Pods. Deze is direct hersteld en de initialisatie is verplaatst naar wp_loaded met regressietests. Er is niet vastgesteld of Google in dat korte venster een ongecachete response heeft gezien. Eindcontrole: alle 13 bestaande taxonomieën behouden, volledige actuele snapshot en sitemapdekking hersteld.

Volledige snippetmigratie is afzonderlijk werk per module, met behoud van bestaande publieke functies en vergelijkende tests. Deze update vervangt geen bestaande router door een tweede implementatie.
