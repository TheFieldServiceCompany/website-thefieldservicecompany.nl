# Overgang naar productie

## Reikwijdte

- Bron: `https://dev.thefieldservicecompany.nl` (**DEV**).
- Doel: `https://thefieldservicecompany.nl` (**LIVE**).
- **DEV** wordt integraal naar **LIVE** gemigreerd via het WordPress-beheer.
- Supabase wordt tijdens deze overgang niet gewijzigd.
- De historische Elementor-inzendingen op **DEV** blijven ongewijzigd. De nieuwere
  inzendingen van **LIVE** worden apart veiliggesteld en later met CRM/Supabase
  vergeleken; ze worden niet als historische Elementor-rijen in **DEV** geïmporteerd.

## Voorwaarden vóór de overgang

- Een volledige, handmatig te verwijderen UpdraftPlus-back-up van **DEV** staat in
  Google Drive: database, plugins, thema's, uploads en overige bestanden.
- Eenzelfde volledige back-up van **LIVE** staat in Google Drive en is als
  handmatig te verwijderen gemarkeerd.
- De laatste **LIVE**-inzendingen en bijbehorende uploads zijn opnieuw geëxporteerd
  vlak vóór de overgang.
- Er is een kort migratievenster afgesproken waarin geen nieuwe formulieren worden
  verwerkt.
- De gebruikte migratietool voert een serialisatie-veilige zoek/vervangactie uit
  van `dev.thefieldservicecompany.nl` naar `thefieldservicecompany.nl`.

## Uitvoering

1. Maak de laatste delta-export van Elementor-inzendingen op **LIVE** en controleer
   dat alle gekoppelde uploads lokaal zijn opgeslagen buiten Git.
2. Pauzeer formulierverkeer op **LIVE** voor het korte migratievenster.
3. Migreer de volledige **DEV**-site naar **LIVE** via WPvivid. De standaard gratis
   UpdraftPlus-installatie wordt hiervoor niet gebruikt, omdat de noodzakelijke
   domeinvervanging daar een Premium-functie is.
4. Meld opnieuw aan op **LIVE** met de gebruikersgegevens van **DEV** als de
   migratietool de gebruikersdatabase heeft vervangen.
5. Sla de permalinks opnieuw op en maak caches leeg.
6. Controleer de startpagina, navigatie, vacatures, zoekfilters, media, formulieren,
   e-mailacties en het verzamelen van Elementor-inzendingen.
7. Controleer dat interne links en mediakoppelingen naar **LIVE** wijzen en niet
   meer naar **DEV**.
8. Hef het migratievenster op zodra de controles slagen.

## Go/no-go

De daadwerkelijke overschrijving van **LIVE** begint pas na expliciet akkoord. Bij
een fout tijdens migratie of controle wordt het rollbackplan gevolgd.

## Taken na de migratie

- Inventariseer ongebruikte en dubbele mediabestanden; verwijder pas na controle
  van pagina's, vacatures, formulieren, metadata en externe koppelingen.
- Breng de databasevervuiling in kaart, waaronder revisies, verlopen transients,
  verweesde metadata en tabellen van oude plugins, en maak eerst een herstelbare
  back-up voordat er wordt opgeschoond.
- Leg vóór en na de opschoning de opslagomvang vast en voer daarna een functionele
  controle van de website en formulieren uit.
