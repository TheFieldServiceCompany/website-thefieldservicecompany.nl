# Livecontrole vacaturecontext solliciteren — 2 november 2026

## Aanleiding

De Elementor-widget `62cd538` probeerde het vacaturenummer uit zichtbare tekst
met het label `Vacaturenummer:` te halen. De live template toont inmiddels
`Vacature:`. Daardoor werd een leeg vacaturenummer aan het sollicitatieformulier
doorgegeven en belandden geldige inzendingen in review met
`vacancy_not_found`.

De widget stuurde daarnaast titel, bedrijf, contractvorm en pagina-URL als losse
queryparameters. Die browseruitlezing was niet nodig voor de canonieke
vacaturekoppeling en maakte de route kwetsbaar voor tekst- en templatewijzigingen.

## Uitvoering

- Website PR #6, mergecommit `c45317b`.
- Live pluginupdate: TFSC Custom Functions 0.4.1 naar 0.5.0.
- De actieve pluginversie is na installatie in WordPress teruggelezen.
- Het opgeslagen Pods-/postveld `vacaturenummer` is de enige bedrijfsreferentie.
- Alle sollicitatieknoppen gebruiken één verborgen POST naar
  `/formulier-solliciteren/`.
- De zichtbare URL blijft schoon; de gevalideerde POST-waarde wordt server-side
  aan het bestaande Elementor Request Parameter-veld aangeboden.
- De oude widgetinhoud wordt niet meer gerenderd.
- Bestaande `data-tfsc-*`-attributen en knop-ID's blijven staan voor GTM.
- Vraagknoppen krijgen alleen het vacaturenummer mee.

## Gedeelde vacaturenummers

De nummers 301, 313, 326 en 358 worden ieder bewust op twee verschillende
publicatiepagina's gebruikt. CRM/Supabase PR
`TheFieldServiceCompany/tfsc-appsmith-crm#60`, mergecommit `8b25076`, koppelt
beide publicaties per nummer aan één actieve vacatureprocedure. De vier
overbodige vacaturekaarten zijn gearchiveerd; publicatiebrondata en historische
extractiedata zijn behouden.

## Controle na livegang

- 63 vacaturepagina's gevonden in de live vacaturesitemap.
- 63/63 pagina's gaven HTTP 200.
- 63/63 pagina's bevatten een numeriek verborgen vacaturenummer.
- 63/63 pagina's bevatten de centrale handler
  `tfsc-vacancy-context-handler`.
- 63/63 pagina's bevatten geen oude `postTitle=`- of `contractvorm=`-code.
- Op de gecontroleerde vacature 358 vielen drie sollicitatieknoppen onder
  dezelfde centrale handler; vier vraagknoppen gebruikten dezelfde
  vacaturecontext.
- Een POST-proef met vacaturenummer 358 vulde het bestaande Elementor-veld
  `form_fields[vacancy_number]` met `358`.
- Een POST-proef met een niet-numerieke waarde gaf HTTP 400.
- In Supabase slaagden 12/12 resolvercontroles: vier nummer-only gevallen en
  acht afzonderlijke publicatie-URL's.
- De bestaande sollicitatie op vacature 358 bleef aan dezelfde actieve
  vacature gekoppeld.
- Er is tijdens deze controle geen formulier verzonden en geen testpersoon of
  sollicitatie aangemaakt.

## Herstelpad

Het vorige pakket blijft beschikbaar als
`wordpress/plugins/dist/tfsc-custom-functions-0.4.1.zip`. Terugzetten kan via
WordPress Plugins > Nieuwe plugin > Plugin uploaden en vervolgens de bestaande
plugin vervangen. Dat herstelpad brengt ook de oude, foutgevoelige
browseruitlezing terug en is daarom alleen bedoeld voor een noodrollback.
