# Dev WordPress-audit — 16–17 oktober 2026

Dit rapport bevat geen wachtwoorden, API-sleutels, licentiesleutels,
formulierwaarden, CV's of andere persoonsgegevens.

## Samenvatting

De dev-site kan technisch verder worden afgemaakt, maar is nog niet klaar voor
productie. De grootste blokkades zijn de formulierbetrouwbaarheid, een mismatch
tussen Elementor Core en Pro, niet-geversioneerde Elementor/Pods-configuratie en
vacatures zonder stabiele CRM-ID.

## Back-up en gegevensbehoud

- Volledige UpdraftPlus-back-up succesvol afgerond op 16 oktober 2026 om 23:20.
- Database, plugins, thema's, uploads en overige bestanden staan in de
  geconfigureerde Google Drive-opslag.
- Elementor bewaarde vóór de synthetische migratietests 1.050 historische
  formulierinzendingen vanaf februari 2023. Na zeven synthetische tests staat
  de teller op 1.057. Er zijn geen inzendingen verwijderd of geëxporteerd.
- De bestaande formulierstroom blijft actief totdat de vervanging is bewezen.

## Eigen code

### TFSC Custom Functions

De plugin functioneert als de ontbrekende child-theme/code-laag. Belangrijkste
bevindingen:

1. De uploadbijlagecode loopt altijd door 20 posities, ook bij één bestand. Dit
   kan ongeldige bijlagepaden en waarschuwingen veroorzaken.
2. De filter voor bestandsnamen leest `$_FILES` zonder voldoende vorm- en
   bestaancontroles en forceert de originele naam. Dit moet veilig worden
   vervangen zonder bestaande uploads te verwijderen.
3. Zoektekst wordt in de archieftitel niet expliciet ge-escaped.
4. Google Fonts-filter wordt dubbel geregistreerd.
5. Font Awesome en WordPress block CSS worden globaal verwijderd; dit kan
   iconen of blokken breken.
6. Alle geregistreerde JavaScriptbestanden worden automatisch gepreload. Dit
   kan juist extra netwerkbelasting en dubbele downloads veroorzaken.
7. De CSS bevat een waarschijnlijk onwerkzame selector `grecaptcha-badge`
   (zonder punt) en een kapotte commentaargrens bij het footerblok.

### Schema-endpoint

`GET /wp-json/tfsc/v1/schema` is read-only en vereist een ingelogde gebruiker,
maar de capability `read` is te ruim voor een technisch schema inclusief
debuggegevens. Na de inventaris moet dit endpoint minimaal naar
`manage_options`, zonder debugdetails, of geheel worden verwijderd.

### Code Snippets

Zes actieve snippets zijn geëxporteerd. Twee opvallende punten:

- De body-class snippet controleert nog het oude posttype `beroep`; dev gebruikt
  `beroepengids`.
- De Rank Math combo-breadcrumbsnippet stopt altijd vroeg omdat de vereiste
  functies `tfsc_is_combo_archive()` en `tfsc_get_combo_archive()` niet actief
  aanwezig zijn. De snippet is daardoor momenteel inert.

## Formulieren

- Sollicitatie-, registratie-, vraag-, advies- en mailingformulieren gebruiken
  Elementor en/of Advanced Form Integration.
- Zes AFI-mappings wijzen nog naar EngageBay.
- Na migratie is op LIVE vastgesteld dat de employer-contactpagina (ID 9428) de
  lokale actie `Inzendingen verzamelen` gebruikt.
- Op 17 oktober 2026 zijn de privacyvelden van de zes actieve formulieren op
  LIVE verplicht gemaakt en standaard uitgevinkt, zonder de formulier-CSS te
  wijzigen.
- De zes formulierpaden zijn op LIVE end-to-end getest. Alle inzendingen zijn
  lokaal in Elementor vastgelegd en alle door Elementor aangestuurde acties
  rapporteerden succes.
- Het employer-contactformulier miste na de migratie de expliciete
  `Advanced Form Integration`-actie. Integratie-ID 7 is toegevoegd; de hertest
  is als inzending 1136 met `5/5 Geslaagd` opgeslagen. EngageBay bevestigde de
  contact- en bedrijfsaanmaak met HTTP 200.
- De HTTP 400-regels in het AFI-log betroffen bij deze tests alleen reeds
  bestaande tags; zij blokkeerden de contact- en bedrijfsaanmaak niet.
- Historische formulierdata en uploads blijven voorlopig behouden.

## Vacatures

- Dev bevat 63 gepubliceerde en 2 conceptvacatures.
- De vacaturezoeker op LIVE is op 17 oktober 2026 op desktop en mobiel
  interactief getest. De standaardquery gaf 47 publieke resultaten; locatie-,
  taxonomie-, combinatie-, reset-, uitklap-, mobiele filterchip-, paginering-
  en detailnavigatie werkten zonder aangetroffen functioneel defect.
- Er is nog geen stabiele CRM-UUID op de WordPress-records.
- Twee vacatures missen een vacature-nummer.
- De nummers 301, 313, 326 en 358 komen dubbel voor.
- Record 22711 heeft conflicterende statustermen.
- Live-only vacature 23645 moet nog volledig naar dev worden overgezet. De
  huidige live REST-rol kan de benodigde privévelden niet lezen, dus een
  gedeeltelijke kopie is bewust niet gemaakt.

## Live/dev-inhoud

- Aantallen pagina's, blogs, actieve vacatures en ingevulde vacatures zijn op
  hoofdniveau gelijk, behalve enkele verklaarde deltas.
- Blog 19232 is op dev inhoudelijk bijgewerkt met de nieuwere live-tekst terwijl
  de dev-opmaak behouden bleef.
- Live vacature 23186 heeft dezelfde zichtbare tekst als dev; alleen markup wijkt af.
- Dev-only vacature 26671 blijft behouden.
- Taxonomieslug `high-tech` op live is op dev bewust genormaliseerd naar
  `hightech`; typo `heereveen` is op dev gecorrigeerd naar `heerenveen`.

## Volgorde vóór productie

1. Huidige code en configuratie versieerbaar maken.
2. Uploadcode repareren en formulieren lokaal duurzaam laten opslaan.
3. CRM-connector met outbox bouwen en met synthetische data testen.
4. Elementor-versies gelijk trekken en regressietesten uitvoeren.
5. Live-only vacature 23645 volledig en gecontroleerd migreren.
6. EngageBay pas uitschakelen na geslaagde parallelle end-to-endtests.
7. SEO, redirects, formulieren, bestanden, logging en rollback aftekenen.
