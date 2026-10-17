# Changelog

## 2026-10-17

- Eerste dev-WordPress-audit vastgelegd.
- Eigen actieve plugins en Code Snippets uit dev geëxporteerd.
- Elementor Custom Code geïnventariseerd; EngageBay-identificatoren bewust niet opgenomen.
- Pluginmanifest van dev toegevoegd.
- Ontwerp voor een duurzame CRM-outbox en testplan toegevoegd.
- Op LIVE gecontroleerd dat het werkgeverscontactformulier inzendingen lokaal
  in Elementor opslaat.
- Privacytoestemming in de zes actieve Elementor-formulieren verplicht gemaakt
  en standaard uitgevinkt; bestaande formulier-CSS ongewijzigd gelaten.
- Zes actieve formulierpaden end-to-end getest; lokale Elementor-opslag en alle
  ingestelde acties rapporteerden succes.
- De ontbrekende expliciete AFI-actie bij het employer-contactformulier
  toegevoegd met integratie-ID 7 en succesvol hertest: Elementor `5/5
  Geslaagd`, EngageBay contact- en bedrijfsaanmaak HTTP 200.
- De LIVE vacaturezoeker op desktop en mobiel getest: filters, locatie-kruis,
  combinaties, reset, Meer/Minder, filtergroepen, mobiele filterchips,
  paginering en detailnavigatie werkten zonder functioneel defect.
- De SEO-migratiecontrole uitgevoerd: WordPress/Rank Math-instellingen,
  robots, sitemapstructuur, metadata, schema, content-URL's en 199 actieve
  omleidingen geïnventariseerd. De twee gevonden omleidingsfouten zijn in Rank
  Math gecorrigeerd, de gerichte LiteSpeed-cache is geleegd en beide oude URL's
  zijn anoniem als correcte 301 gecontroleerd. Tijdelijke onderhouds- en
  indexeringsblokkades zijn bewust nog niet verwijderd.
- Een alleen-lezen content- en taxonomie-inventaris uitgevoerd voor LIVE
  WordPress en Supabase. De 18 bestaande blogs, 19 relevante WordPress-
  taxonomieën, 256 WordPress-termen en 250 Supabase-vocabulaire-items zijn in
  een Google Sheet vastgelegd en vergeleken. Voor Beroepengids, Kennisbank en
  de 12 vacaturetaxonomieën is een gefaseerd voorstel gedocumenteerd in
  `content/taxonomy-and-content-inventory.md`. Er zijn hierbij geen gegevens in
  WordPress of Supabase gewijzigd.
- Alle geëxporteerde PHP-snippets, eigen plugins en Elementor Custom Code
  functioneel doorgelicht en beschreven in `snippets/README.md`, inclusief
  hooks, Query ID's, afhankelijkheden, bekende defecten en inactieve code.
  Daarbij is vastgelegd dat de huidige SNIP-integratie Google Jobs-locaties uit
  WordPress-termmeta opbouwt, maar bedrijfsadres, vacaturelocatie en woon-/
  wervingsregio nog niet als afzonderlijke relaties modelleert.
- De afgesproken content-, taxonomie- en locatielogica vastgelegd in
  `functionality/content-taxonomy-logic.md`: alle WordPress-plaatsen en
  postcodes moeten naar de canonieke geografielaag, bestaande land- en
  provinciekoppelingen blijven behouden, Beroepengids spiegelt de vacature-
  taxonomieën en Blog wordt alleen binnen WordPress herplaatst.

## 2026-09-28

- Basisstructuur van de repository voorbereid.
