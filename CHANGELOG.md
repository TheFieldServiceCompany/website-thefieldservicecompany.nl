# Changelog

## 2026-10-18 — Opleidingsniveaus en studierichtingen gesynchroniseerd

- In WordPress de zichtbare niveaunamen aangepast naar `VMBO / LBO` en
  `WO / Universiteit`, met behoud van de bestaande slugs, URL's en alle
  vacaturekoppelingen.
- De vijf goedgekeurde studierichtingen aan de vacaturetaxonomie toegevoegd:
  Industriële Automatisering, Civiele Techniek, Mobiele
  Werktuigentechniek, Koude- en Klimaattechniek en Operationele Techniek.
- Supabase gelijkgetrokken naar vier actieve niveaus en 23 actieve
  studierichtingen. Associate degree is zonder dataverlies gedeactiveerd als
  zelfstandig filter, omdat het record geen kandidaat- of vacaturekoppelingen
  had en binnen TFSC onder HBO valt.
- Vier opleidingsniveau- en 23 studierichtingmappings van WordPress-term-ID
  naar Supabase-UUID vastgelegd in `source_term_mappings`.
- Eindcontrole bevestigt dat WordPress en Supabase exact dezelfde namen en
  aantallen bevatten. De bestaande Appsmith-functies voor zowel kandidaten als
  vacatures leveren automatisch dezelfde 4 niveau- en 23
  studierichtingopties.
- Appsmith-schermen en Pods zijn nog niet gewijzigd. Appsmith wordt hierna aan
  beide kanten Nederlandstalig getest; voor Pods blijft het voorstel een
  centrale pagina `Opleidingen` met afzonderlijke taxonomieën voor
  opleidingsniveau en studierichting.

## 2026-10-18 — Opleidingenketen WordPress, Supabase en Appsmith onderzocht

- Alleen-lezen vastgesteld dat WordPress bij vacatures al de twee benodigde
  taxonomieën heeft: `opleidingsniveau` met vier gebruikte termen en
  `vac_studierichting` met achttien nog ongebruikte termen.
- De WordPress- en Supabase-lijsten vergeleken. Opleidingsniveau heeft twee
  directe matches, twee naam-/synoniemmatches en één Supabase-only niveau
  (`Associate degree (Ad)`). Bij studierichtingen zijn vijftien namen exact,
  is één naam een duidelijke variant en heeft elk systeem twee eigen termen.
- Bevestigd dat Supabase het juiste gescheiden model al bevat via
  `education_levels`, `education_fields`, `vacancy_education_requirements` en
  `candidate_education`. De productievulling en stabiele WordPress-mappings
  ontbreken nog.
- Bevestigd dat Appsmith beide dimensies al afzonderlijk kan selecteren,
  opslaan en gebruiken bij vacaturematching. Nieuwbouw is niet nodig; de UI,
  termmapping, WordPress-vacaturekoppeling, synchronisatie en tests moeten
  worden afgemaakt.
- Als voorstel vastgelegd: `Opleidingen` wordt een overkoepelende
  Beroepengidsingang, terwijl opleidingsniveau en studierichting technisch
  gescheiden blijven. Er is niets gewijzigd in WordPress, Supabase of
  Appsmith. De volledige audit staat in
  `audits/2026-10-18-education-chain-wordpress-supabase-appsmith.md`.
- Daarna inhoudelijk besloten dat de publieke niveaufilters `VMBO / LBO`,
  `MBO`, `HBO` en `WO / Universiteit` worden. Associate degree (Ad) krijgt
  geen eigen publiek filter maar valt voor TFSC onder HBO, terwijl de precieze
  diplomanaam als ruwe kandidaatwaarde kan worden behouden.
- Besloten de brede studierichting `ICT & Technische Informatica` te behouden.
  `Mobiele werktuigentechniek` is bevestigd als geldige studierichting, met de
  officiële en gangbare opleidingsnamen als synoniemen. Deze besluiten zijn
  alleen gedocumenteerd; er is nog geen data gewijzigd.

## 2026-10-17 — Alle overige vacaturetaxonomiekoppelingen gecontroleerd

- Een alleen-lezen koppelingenaudit uitgevoerd voor alle 63 gepubliceerde
  vacatures en alle tien taxonomieën die zowel in de oude live-export als op de
  huidige live-site bestaan. Er ontbreken geen vacature-slugs en er zijn geen
  extra slugs.
- Na normalisatie van drie bekende termcorrecties (`high-tech` → `hightech`,
  `service-monteur` → `servicemonteur` en `heereveen` → `heerenveen`) zijn
  bedrijfssector, dagen internationaal, dienstverband, opleidingsniveau,
  vacaturestatus en werkgebied volledig gelijk aan de oude live-data.
- De resterende inhoudelijke verschillen zijn beperkt tot vacature 23277 op
  vier taxonomieën, twee koelmonteurvacatures met minder brede functienamen en
  zes inhoudelijk passende nieuwe koppelingen aan kennisgebied
  `rotating-equipment`. Deze zijn niet automatisch aangepast.
- Vastgesteld dat de huidige functienaamtaxonomie zowel `servicemonteur`
  (term-ID 230, 16 vacatures) als `service-monteur` (term-ID 420, 1 vacature)
  bevat. Consolidatie vereist later een expliciet SEO- en relatieplan.
- De later toegevoegde taxonomieën `reisprofiel` en `vac_studierichting`
  bevatten respectievelijk 25 en 18 termen, maar nog geen enkele koppeling aan
  de 63 vacatures. Dit blijft onderdeel van de bestaande WordPress-, Supabase-,
  Appsmith- en Beroepengids-TODO.
- De volledige bevindingen en besluitpunten zijn vastgelegd in
  `audits/2026-10-17-live-vacancy-taxonomy-connections.md`. Er zijn tijdens deze
  audit geen gegevens in WordPress of Supabase gewijzigd.

## 2026-10-17 — Opleidingsniveaus van 63 live vacatures hersteld

- Vastgesteld dat de huidige vacaturetaxonomie `opleidingsniveau` tijdens een
  eerdere migratie vrijwel al haar termkoppelingen had verloren: 26 vacatures
  waren leeg en 33 hadden uitsluitend `LBO`; slechts 2 van de 63 gepubliceerde
  vacatures kwamen exact overeen met de oude live-data.
- De Updraft-databasekopieën van 16 en 17 oktober bleken aan
  `https://dev.thefieldservicecompany.nl` te behoren en zijn daarom niet als
  oude live-bron gebruikt. De autoritatieve bron was de volledige WordPress-
  export `vacatures-all.xml` van de oude live-site, gemaakt op 16 oktober 2026
  om 22:13 UTC.
- De 63 gepubliceerde vacatures uit die export zijn op slug vergeleken met de
  63 huidige live-vacatures. Er ontbrak geen enkele slug en er was geen extra
  live-slug. De oude taxonomie `opleiding` is één-op-één gemapt naar de huidige
  taxonomie `opleidingsniveau`: HBO `69`, MBO `70`, Universiteit `71` en LBO
  `210`.
- Na een verse voorcontrole via de WordPress REST API zijn uitsluitend de
  `opleidingsniveau`-term-ID's van de 61 afwijkende vacatures bijgewerkt. Titels,
  inhoud, publicatiestatus en alle andere taxonomieën zijn niet meegestuurd of
  gewijzigd. De 2 reeds juiste vacatures zijn overgeslagen.
- Een onafhankelijke REST-export na afloop bevestigde 63 verwachte en 63
  aanwezige vacatures, 0 ontbrekende slugs, 0 extra slugs, 0 termverschillen en
  0 vacatures zonder opleidingsniveau.
- De functionele hertest van het gepubliceerde Beroepengidsartikel `Wat is een
  Field Service Engineer?` toont onder `Opleidingsniveau` nu correct HBO, LBO,
  MBO en Universiteit. Dit bevestigt tevens dat de snippets de vacaturedata
  correct aggregeren en dat geen handmatige opleidingsrelatie op het artikel
  nodig is.
- QUIC.cloud blokkeerde de terminal-REST-aanroepen met HTTP 403. De herstel- en
  controleaanroepen zijn daarom rechtstreeks naar de eigen Cloud86-oorsprong
  gestuurd, met behoud van HTTPS-hostnaam en certificaatcontrole. QUIC.cloud is
  niet uitgeschakeld.
- Voor de herstelactie is een nieuwe toepassingssleutel van gebruiker
  `codex-rest-audit` gemaakt. Een bij de eerste lokale overdracht ontstane maar
  nooit gebruikte sleutel is direct daarna ingetrokken; de werkende sleutel
  blijft volgens afspraak lokaal en buiten de repository bewaard.

## 2026-10-17 — Eerste blogartikel naar Beroepengids gemigreerd

- Van het bestaande blogartikel `Wat is een Field Service Engineer? Lees alles
  over deze geweldige baan!` een nieuw Beroepengids-concept gemaakt met post-ID
  `27533`. Het oude blogartikel is niet gewijzigd en er is nog geen redirect
  aangemaakt.
- De nieuwe slug is `wat-is-een-field-service-engineer`; auteur Rudi van Vliet,
  oorspronkelijke publicatiedatum 10 januari 2025, samenvatting en uitgelichte
  afbeelding zijn overgenomen.
- Alleen de bestaande Beroepengids-taxonomieterm `field service engineer`
  (`beroep_functienaam`, term-ID `437`) is gekoppeld. Andere taxonomieën zijn
  leeg gebleven.
- De Rank Math-instellingen zijn overgenomen: SEO-title met `%currentyear%`, de
  twee bestaande focuszoekwoorden en de pillar-contentmarkering. De
  metabeschrijving blijft, net als bij de bron, gebaseerd op de samenvatting.
- Het verouderde Rank Math FAQ-blok is verwijderd. Zeven unieke vragen staan nu
  als gewone H3-vragen met antwoorden onder de H2 `Veelgestelde vragen over de
  functie field service engineer`; één inhoudelijk dubbele vraag is weggelaten.
- De zichtbare introductie blijkt uit het Pods-veld `introduction` te komen en
  niet uit de standaard WordPress-samenvatting. De vacatureknop wordt normaal
  automatisch uit de gekozen `relationship_*`-velden opgebouwd;
  `relationship_vacature_button_url` is alleen een expliciete override.
- Het artikel is na inhoudelijke controle gepubliceerd op
  `/beroepengids/artikel/wat-is-een-field-service-engineer/`. De bestaande 301
  van `/blog/wat-is-een-field-service-engineer/` is rechtstreeks naar deze URL
  omgezet en voor de lange voormalige blog-URL is eveneens een directe 301
  toegevoegd. Beide regels zijn met de Rank Math-debugger gecontroleerd.
- Het oude blogartikel met post-ID `19232` is na expliciete goedkeuring naar de
  WordPress-prullenbak verplaatst en blijft daardoor herstelbaar.
- In het dynamische blok met vacatureonderwerpen verschijnt alleen `LBO`. Dit
  komt uit de taxonomie van de gekoppelde open vacatures, niet uit het
  Beroepengidsartikel. Gecontroleerde vacatures noemen inhoudelijk MBO/HBO maar
  zijn momenteel uitsluitend aan `LBO` gekoppeld; correctie van de bestaande
  vacaturedata blijft daarom een afzonderlijke TODO.

## 2026-10-17 — Beroepengids: taxonomie Arbeidsvoorwaarden als TODO

- Vastgelegd dat een Beroepengidsartikel meerdere taxonomische ingangen kan
  krijgen, bijvoorbeeld zowel de functienaam `field service engineer` als de
  taxonomie `Arbeidsvoorwaarden` met de term `Salaris`.
- De gekozen richting is een custom taxonomie `Arbeidsvoorwaarden`, met later
  onder meer salaris, secundaire arbeidsvoorwaarden, toeslagen, vergoedingen,
  bonus, pensioen, reisuren en overwerk en verblijfskosten of dagvergoedingen.
- Vastgelegd dat Supabase hiervoor één canonieke termenlaag met WordPress-
  mappings krijgt, terwijl concrete salaris- en toeslagbedragen gestructureerde
  velden blijven. Appsmith moet later op beide onderdelen aansluiten.
- Besloten de custom taxonomie nu nog niet aan te maken, omdat aan de
  vacaturezijde nog geen overeenkomstige taxonomie of definitieve relatie
  bestaat. Eerst wordt de gedeelde WordPress-, Supabase- en Appsmith-keten
  ontworpen; daarna worden de bestaande Beroepengids-taxonomieën als
  instellingenvoorbeeld gebruikt.
- Er zijn hiervoor nog geen wijzigingen in WordPress, Pods, snippets,
  taxonomietermen of URL-structuur uitgevoerd.

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
- Besluit aangescherpt: `dagen-internationaal` blijft een gedeelde canonieke
  termenlijst voor vacatures én kandidaten; bestaande data wordt later onder
  de juiste termen ingedeeld. De huidige regio's en grotere plaatsen zijn voor
  nu vastgesteld, met een TODO om aanvullende plaatsen en postcodes later te
  beoordelen.
- Eerste ondubbelzinnige snippetcorrecties in de repository voorbereid: de
  adviesknop gebruikt niet langer een hardcoded dev-domein en encodeert
  parameters veilig, de Beroepengids-CPT-naam is gecorrigeerd, formulieruploads
  lezen alleen bestaande bestanden, de zoekterm wordt escaped, ongerichte
  JavaScript-preloads zijn verwijderd en twee CSS-syntaxfouten zijn hersteld.
  Deze correcties zijn nog niet naar WordPress uitgerold.
- TODO toegevoegd om de uitzonderlijk grote WordPress-installatie en back-ups
  te analyseren en te verkleinen. De analyse omvat onder meer uploads,
  afgeleide en ongebruikte media, cache- en back-upmappen, migratiebestanden,
  logs, revisies, transients, grote tabellen en verweesde Elementor-/Pods-data.
  Opruimen gebeurt pas na inventarisatie, back-up en expliciete goedkeuring.
- TODO toegevoegd om alle formuliermeldingen naar begrijpelijk Nederlands om
  te zetten. De verplichte privacy-checkbox krijgt een eigen, concrete melding
  in plaats van een algemene Engelse validatiefout.
- De drie actieve LIVE-snippets achter de virtuele vacaturearchieven en Rank
  Math-combositemap (148, 186 en 193) geëxporteerd met behoud van alle uitleg en
  functioneel gecontroleerd. De sitemap bevat momenteel 15.836 URL's verdeeld
  over 32 bestanden: 622 combinaties met twee en 15.214 met drie taxonomieën;
  iedere URL bevat `functienaam`. De aanduiding “fase 1” vormt dus geen kleine
  publicatielimiet. Ook vastgesteld dat de geteste noindex- en Search &
  Filter-URL's geen canonical-link renderen, ondanks de beschreven canonical
  voor filter-URL's.
- De volledige LIVE-snippetstatus vergeleken. De inactieve snippets zijn
  hoofdzakelijk oudere versies en klonen van de 26 actieve opvolgers; debug- en
  testsnippets 185 en 197 staan eveneens terecht uit. Voorlopig blijven alle
  huidige actieve snippets aan en alle voorgangers uit.
- Alle 47 LIVE-snippets, inclusief 21 inactieve voorgangers en tests, als
  afzonderlijke leesbare bestanden geëxporteerd naar
  `snippets/exports/live/all-2026-10-17/`. Daarmee blijven alle handleidingen
  en historische codevergelijkingen beschikbaar zonder WordPress te wijzigen.
- De eerder vastgelegde SEO-keuze voor oorspronkelijke vacature-
  taxonomiearchieven teruggevonden in de opeenvolgende snippets 146, 150, 187
  en 193 en op LIVE gecontroleerd: native archieven blijven bereikbaar en
  `index, follow`, met canonical naar de passende vacature-combo. Functienaam
  en locatie werken één-op-één; andere taxonomieën gebruiken in de huidige code
  `field-service-engineer` als vaste functienaamcontext.
- De Beroepengids-archiefketen op LIVE getest. Taxonomie-base- en termarchief
  laden met de juiste templates en context, waardoor de betrokken snippets
  actief moeten blijven. Het CPT-archief mist door de oude CPT-naam in snippet
  49 de extra contextclass. Verder toont het functienaam-base-archief zichtbaar
  de ruwe veldnaam `archive_beroep_functienaam_description` en gebruikt het nog
  een generieke Rank Math-titel; beide zijn als gerichte herstelpunten
  vastgelegd.
- Snippet 49 op LIVE gecorrigeerd van CPT-naam `beroep` naar `beroepengids`.
  WordPress bevestigde de update. De functionele hertest toont nu
  `tfsc-archive-beroepen` op `/beroepengids/artikelen/`, terwijl
  `tfsc-archive-taxonomy` op het functienaam-base-archief behouden bleef. De
  bijgewerkte LIVE-export staat onder
  `snippets/exports/live/applied-2026-10-17/`.
- De zichtbare ruwe veldnaam op het functienaam-base-archief herleid tot de
  centrale Pods-inhoud. Elementor gebruikt correct de dynamische tag
  `description`, maar zes description-velden bevatten nog letterlijk hun eigen
  veldnaam en twee introvelden bevatten testtekst. Verder wijkt het Pods-
  naamdeel `opleiding` af van de echte taxonomie `beroep_opleidingsniveau`, en
  staat `beroep_locatie` wel in de snippet-allowlist maar niet in het huidige
  Beroepengids-beheer.
- Na expliciete goedkeuring uitsluitend de acht tijdelijke LIVE-Pods-waarden
  leeggemaakt: zes description-placeholders plus `test bedrijfssector intro`
  en `intro functienaam`. WordPress bevestigde de opslag. De echte
  Beroepengids-CPT-teksten en de twee kennisbankwaarden zijn ongewijzigd
  gebleven. De openbare base-archieven voor functienaam, functie-inhoud,
  opleidingsniveau en werkgebied zijn daarna opnieuw gecontroleerd en tonen
  geen ruwe veldnamen of testteksten meer. De generieke Rank Math-titel
  `Archive • The Field Service Company` blijft een afzonderlijk herstelpunt.
- Het inhouds- en SEO-model voor de zes Beroepengids-base-archieven vastgelegd.
  De gids bedient primair informatieve loopbaanoriëntatie en leidt daarna door
  naar gerelateerde actuele vacatures, zodat hij niet concurreert met de
  vacaturearchieven. Per pagina komen een korte intro bovenaan, unieke
  verdiepende tekst onderaan, afzonderlijke SEO-metadata en expliciete relaties
  tussen beroep, werkzaamheden, kennis, opleiding, sector en werkgebied. Ook de
  gekozen werktitels en de uitgangspunten voor vindbaarheid in generatieve
  zoekfuncties zijn vastgelegd in `functionality/content-taxonomy-logic.md`.
- De eerste complete voorbeeldpagina voor het archief `Functienamen` opgesteld
  in `content/drafts/beroepengids-functienamen-voorbeeld.md`: SEO-title, unieke
  meta description, korte intro, concrete verdiepende ondertekst, natuurlijke
  gebruikersvragen, interne-linkplan en vertrouwens-/AI-signalen. De zichtbare
  concepttekst is na inhoudelijke feedback aangescherpt tot een energieke
  archiefwegwijzer van 61 woorden intro en 379 woorden verdieping. Het
  vergelijkingsblok tussen Field Service Engineer en servicemonteur is
  verwijderd; doel, navigatie, passende functie vinden en bedrijfssectoren
  staan nu centraal. De tekst gebruikt consequent `jij` en `jouw`, schrijft
  functienamen in lopende zinnen met kleine letters en omvat naast service en
  commissioning ook site supervision en site management.
- Na expliciete opdracht de goedgekeurde intro en description in de centrale
  LIVE-Pods-velden voor `beroep_functienaam` geplaatst. WordPress bevestigde de
  opslag en beide waarden zijn exact teruggelezen. De publieke hertest toont
  één H1 `Functienamen`, de nieuwe intro, vier inhoudelijke H2-vragen en een
  correcte HTML-lijst zonder zichtbare Markdowntekens. SEO-title en meta
  description zijn in deze stap bewust nog niet technisch toegepast.
- De tweede voorbeeldpagina voorbereid voor het archief `Functie-inhoud` in
  `content/drafts/beroepengids-functie-inhoud-voorbeeld.md`. Het concept bevat
  een intro van 53 woorden en een concrete description van 399 woorden. De
  pagina helpt bezoekers vanuit het soort werk dat zij graag doen navigeren
  naar functienamen, kennis, opleidingen, branches en vacatures. Het huidige
  LIVE-archief toont alleen de term `inbedrijfstellen`.
- Na expliciete opdracht de intro en description voor `Functie-inhoud` in de
  centrale LIVE-Pods-velden geplaatst. WordPress bevestigde de opslag en beide
  waarden zijn exact teruggelezen. De publieke hertest toont één H1
  `Functie-inhoud`, de intro, vier inhoudelijke H2-secties en een correcte
  HTML-lijst zonder zichtbare Markdowntekens. De bestaande templatekop
  `Vacatures op functienaam gerelateerd aan functie-inhoud` is als afzonderlijk
  tekstherstelpunt vastgelegd en niet stilzwijgend aangepast. SEO-title en meta
  description zijn nog niet technisch toegepast.
- De derde voorbeeldpagina voor het archief `Kennisgebieden` is op 17 oktober
  2026 op LIVE geplaatst vanuit de centrale Pods-archiefinstellingen. De pagina
  bevat een intro van 50 woorden en een concrete description van 413 woorden.
  De publieke controle bevestigt één H1 `Kennisgebieden`, vijf inhoudelijke
  H2-koppen, een echte HTML-lijst en geen Markdown-restanten. Het archief toont
  nog geen termen; daarom blijven de genoemde vakgebieden voorbeelden totdat
  de canonieke lijst is goedgekeurd. De bestaande templatekop
  `Vacatures op functienaam gerelateerd aan kennisgebied` blijft als afzonderlijk
  tekstherstelpunt staan. SEO-title en meta description zijn nog niet technisch
  toegepast.
- De vierde voorbeeldpagina voorbereid voor het archief `Opleidingsniveaus` in
  `content/drafts/beroepengids-opleidingsniveaus-voorbeeld.md`. De tekst leidt
  bezoekers vanuit opleiding, werk- en denkniveau en praktijkervaring naar
  passende beroepen, ontwikkelroutes en vacatures. Het LIVE-archief toont nog
  alleen de testterm `Opleiding A`; die is bewust niet gewijzigd. De definitieve
  niveaus en de relatie met studierichtingen worden later gezamenlijk bepaald.
- TODO vastgelegd voor opleidingsrichtingen als één later traject over
  WordPress-vacatures, Supabase en Appsmith. De huidige Beroepengids-uitwerking
  blijft voorlopig beperkt tot `beroep_opleidingsniveau`; er zijn nu geen
  opleidingsrichtingen, mappings of databasevelden gewijzigd. Ook vastgelegd
  dat de eventuele overgang naar een overkoepelende Beroepengids-taxonomie
  `Opleidingen` pas daarna plaatsvindt, inclusief migratie of behoud van
  bestaande URL's, relaties, snippets, sitemap en SEO.
- De vijfde Beroepengids-archieftekst voorbereid voor `Bedrijfssectoren` in
  `content/drafts/beroepengids-bedrijfssectoren-voorbeeld.md`. De tekst helpt
  bezoekers sectoren te vergelijken op machines, installaties, werkomgeving,
  functies en vacatures. De lege LIVE-taxonomy is bewust nog niet met termen
  gevuld; die lijst wordt later afgestemd met vacatures en Supabase.
- Na expliciete goedkeuring de intro en description van het LIVE-archief
  `Bedrijfssectoren` gevuld. De openbare pagina is gecontroleerd: H1, intro,
  vraaggerichte H2-koppen, alinea's en opsomming worden correct weergegeven.
  Er zijn geen taxonomy-termen, Pods-definities of relaties gewijzigd.
- De zesde Beroepengids-archieftekst voorbereid voor `Werkgebieden` in
  `content/drafts/beroepengids-werkgebieden-voorbeeld.md`. De tekst maakt
  onderscheid tussen werkgebied, standplaats, vacaturelocatie en woon- of
  wervingsregio en koppelt het onderwerp aan reisprofiel, dagen internationaal,
  fieldservicefuncties en vacatures. De bestaande testterm `World` is bewust
  niet gewijzigd.
- Na expliciete goedkeuring de intro en description van het LIVE-archief
  `Werkgebieden` gepubliceerd. De openbare weergave is gecontroleerd; de
  bestaande testterm `World`, taxonomie en relaties zijn niet gewijzigd.
- De hoofdpage `Beroepengids` voorzien van een nieuwe vraaggerichte intro en
  description. De tekst helpt bezoekers vanuit functienamen, werkzaamheden,
  kennisgebieden, bedrijfssectoren, opleidingsniveaus en werkgebieden naar een
  passende fieldservicefunctie en actuele vacatures. Koppen en interne links
  zijn na publicatie op LIVE gecontroleerd.
- Het hoofdarchief `Kennisbank` voorzien van een nieuwe intro en description
  voor professionals, zij-instromers en werkgevers. De tekst positioneert de
  site als praktisch kennisplatform voor de fieldservicesector en koppelt door
  naar de Beroepengids en vacatures. De Pods-instellingen, koppen en interne
  links zijn na publicatie op LIVE gecontroleerd.
- De taxonomy-TODO aangescherpt: `Studierichtingen` (`vac_studierichting`) en
  `Reisprofielen` (`reisprofiel`) bestaan al bij vacatures. Later worden de
  termen en vacaturekoppelingen gecontroleerd en worden passende
  Beroepengids-spiegels ontworpen. Ook vastgelegd dat vacature-`werkgebied` nog
  moet worden afgewerkt en dat `opleidingsniveau` bij vacatures en
  Beroepengids nog vanuit één canonieke termenlijst moet worden gevuld.
- De blogmigratie als gecontroleerde TODO vastgelegd: per artikel bestemming,
  actualisatie, slug, taxonomieën en metadata bepalen, daarna verplaatsen en in
  Rank Math een permanente 301-redirect van de oude naar de nieuwe URL maken.
  Oude en nieuwe URL, canonical, sitemap en interne links worden per artikel
  getest voordat het als afgerond geldt.
- De huidige SEO-metadata van Beroepengids, Kennisbank en de zes virtuele
  Beroepengids-basearchieven gecontroleerd. De hoofdpagina heeft nog de
  description `Home`, het Kennisbank-archief gebruikt generieke archieftekst en
  alle zes basearchieven tonen `Archive • The Field Service Company` als title
  en description.
- Een strikt afgebakende Rank Math-oplossing als LIVE-snippet 209 opgeslagen,
  maar nog niet geactiveerd. Deze bevat unieke title en description voor exact
  de acht bedoelde pagina's en laat canonicals, robots, sitemaps,
  vacaturearchieven, gewone termarchieven en zichtbare content ongemoeid.
- Na expliciete goedkeuring LIVE-snippet 209 geactiveerd. Beroepengids,
  Kennisbank en alle zes virtuele Beroepengids-basearchieven geven nu de
  bedoelde unieke HTML-title, meta description, Open Graph-title/-description
  en Twitter-title/-description terug. Een vacaturearchief en een gewoon
  Beroepengids-termarchief behielden hun bestaande metadata. De nog lege
  canonicals en `noindex, nofollow` zijn afkomstig van de bewust actieve
  onderhouds- en zoekmachineblokkade en zijn niet door snippet 209 gewijzigd.

## 2026-09-28

- Basisstructuur van de repository voorbereid.
