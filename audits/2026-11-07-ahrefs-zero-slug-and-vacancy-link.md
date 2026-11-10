# Ahrefs-opvolging — live herstel en automatisering

7 november 2026 · The Field Service Company · expliciet goedgekeurde reparaties.

## Uitgevoerd en gecontroleerd

De vacature **Service Monteur Verpakkingsmachines**, WordPress-ID 13315, linkt nu rechtstreeks naar het bestaande Beroepengids-artikel **Service monteur salaris: loon, toeslagen en vergoedingen**, ID 27845:

https://thefieldservicecompany.nl/beroepengids/artikel/service-monteur-salaris-loon-toeslagen-en-vergoedingen/

Alleen de betreffende anchorbestemming en bijbehorende WordPress-linkmetadata zijn gewijzigd. Linktekst, overige inhoud, publicatiestatus, auteur, slug, publicatiedatum en uitgelichte afbeelding zijn behouden. De opgeslagen content is teruggelezen; de openbare HTML bevat de nieuwe bestemming en niet meer de oude testing-link.

De geldige hotelnachten-term `0` werkt nu op `/vacatures/hotel-nachten-maand/0/`: HTTP 200, indexeerbaar, self-canonical en **drie getoonde open vacatures**. Het WordPress-termgebruik van vier gekoppelde objecten was geen telling van open vacatures.

Vóór de wijziging is vastgesteld dat de daadwerkelijke actieve router **406** was. De oudere export 186 is niet gebruikt als uitrolbasis. De live 406-code bevatte de gevonden callbackloze filter. Er is één callback `'strlen'` toegevoegd in `tfsc_resolve_term_path()`. Alle bestaande native-pagineringlogica is behouden. Actieve opvolger: **411**. De vorige 406-versie is bewaard en staat inactief voor rollback.

Gerichte nacontroles:

| Controle | Uitkomst |
| --- | --- |
| Hotelnachten `0` | 200, self-canonical, 3 vacatures |
| Hotelnachten `8-14` | 200, self-canonical, 3 vacatures |
| Hotelnachten `15-31` | 200, self-canonical, 2 vacatures |
| Field Service Engineer × Drenthe | 200, self-canonical, 10 vacatures |
| Leiden, pagina 2 | 200, canonical naar pagina 2, 10 vacatures |
| Niet-bestaande hotelnachten-term | 404 |

Daarna is de nulroute nogmaals zonder extra cachecontroleveld opgevraagd, met uitsluitend de verplichte testmarker. Ook die controle gaf 200. De huidige hotelnachtensitemap is bereikbaar en bevat de werkende nulroute. Er is geen nieuwe Ahrefs-crawl gestart; het verdwijnen van de oude Ahrefs-meldingen moet dus nog door een volgende geldige scan worden bevestigd.

Rollback: 411 uitschakelen en 406 weer inschakelen. De oorspronkelijke vacaturecontent en volledige routerversies zijn lokaal bewaard onder `local-archive/production-changes/2026-11-07/`. Die lokale back-ups worden niet als bezoekers- of toegangsgegevens naar GitHub gestuurd; alleen geschoonde bewijsbestanden en de gerichte codeversies worden gearchiveerd.

## Zzp-pagina en redirect blijven behouden

De eigenaar heeft deze URL bewust doorgestuurd omdat er nog links naartoe bestaan. De bestaande Metabase-gegevens voor het oude pad tonen, over het aangevraagde jaarvenster 7 november 2025–6 november 2026:

- Search Console: **8 klikken en 9.736 vertoningen**; laatste klik 24 juni, laatste vertoning 11 augustus.
- GA4-acquisitie: **62 geregistreerde landingssessies**; laatste passende rij 24 juli.
- De laatste 90 gevraagde dagen bevatten nul geregistreerde landingssessies en nul GSC-klikken op dit oude pad. GSC bevat in dit onderzoek data tot en met 4 november, GA4 tot en met 6 november; de laatste twee GSC-dagen ontbreken. In het GSC-deel van dat venster staan nog 95 vertoningen.

Dit is **geen bewijs van nul gebruik van de redirect**. Een serverredirect kan het verzoek al afhandelen voordat Analytics de oude URL registreert. Er zijn geen servertoegangslogs beoordeeld die nul inkomende menselijke verzoeken aantonen. De pagina en de 301 naar de homepage zijn daarom niet verwijderd. Ook de sitemapvermelding is in deze opdracht niet aangepast.

Meetgrens: GA4 is op 29 oktober 2026 overgezet naar GTM. Bovenstaande cijfers zijn verschillende bronmetingen en zijn niet bij elkaar opgeteld. Er is geen conversie- of uniekebezoekerclaim gedaan; ontbrekende pagina-engagementrijen zijn geen nul bezoekers.

## Maandelijks informeren en n8n

De bestaande taak **Maandelijkse Ahrefs-controle TFSC** blijft actief op de eerste dag van de maand om **09:30 Europe/Warsaw**, vanaf **1 december 2026**. De instructie is aangescherpt: informeer de gebruiker iedere maand, ook bij een stabiel beeld, en lees eerst het nieuwste verbeterregister. Ontbrekende toegang wordt als ontbrekende toegang gemeld.

Advies: begin met de bestaande maandcontrole via de ingelogde Ahrefs-interface en beschikbare exports. Bouw nog geen nieuwe n8n-koppeling die volledige Ahrefs-auditdata probeert op te halen met de huidige sleutel: die geeft alleen toegang tot publieke endpoints. De eerdere Site Audit-verzoeken kregen 401. De actuele [Ahrefs-API-voorwaarden](https://help.ahrefs.com/en/articles/6559232-about-api-v3) noemen Lite of hoger voor reguliere toegang.

Een latere praktische tussenstap is: een vaste, toegankelijke CSV-aanlevering → n8n-validatie en vergelijking → bewaarde historie → Metabase/verbeterregister → maandbericht. Daarvoor moet n8n daadwerkelijk bij de bestanden kunnen; de lokale desktopmap is niet vanzelf een servermap. Eerst twee of drie consistente vergelijkingen uitvoeren en controleren of de 429's zijn opgelost. Daarna automatiseren we een bewezen verwerking. Een geldige volledige API-toegang zou het ophalen vereenvoudigen, maar is geen vereiste om nu al zinvolle maandcontroles te doen.

Er is in deze ronde geen nieuwe n8n-workflow gebouwd en er zijn geen Metabase-tabellen of dashboards aangepast.

## Bewijs

- `ahrefs-approved-fixes-live.json`: twee uitgevoerde reparaties, hashes, routecontroles en rollback.
- `zzp-legacy-url-traffic-review.json`: bronvensters, dekkingsgrenzen en behoudbesluit.
- `ahrefs-improvements.json`: actuele status per verbeterpunt.
- `ahrefs-monthly-review.json`: actieve maandafspraak.

Onderzoeksdossier: https://github.com/TheFieldServiceCompany/tfsc-website-analytics/pull/4
