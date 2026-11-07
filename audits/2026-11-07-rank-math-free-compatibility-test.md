# Rank Math Free — opvang en geslaagde live proef

Gecontroleerd op 7 november 2026 voor thefieldservicecompany.nl.

**Latere eindstand:** de eigenaar heeft Pro na deze proef zelf gedeactiveerd. Free en de uitbreiding zijn nu actief. De [volledige eindcontrole](2026-11-07-rank-math-final-free-pro-double-check.md) bevestigt de werking opnieuw en corrigeert het eerdere Pro-label voor wachtwoord-noindex. Onderstaand verslag beschrijft de voorafgaande gecontroleerde omschakelproef.

De aangetroffen openbare afhankelijkheden van Rank Math Pro zijn opgevangen met **TFSC Rank Math Breadcrumbs 0.2.0**. De uitbreiding is geïnstalleerd en gecontroleerd met Pro tijdelijk uit. De onderzochte uitvoer bleef gelijk. Na de proef is Pro weer aangezet; de uitbreiding blijft actief en laat de uitvoering dan aan Pro over. De zoekintentieanalyse is op verzoek uitgezet.

## Wat About en Mentions betekenen

`about` beschrijft het onderwerp van een pagina. `mentions` benoemt een begrip waarnaar de pagina verwijst, zonder dat het noodzakelijk het hoofdonderwerp is. Het gaat om extra informatie voor machines in de gestructureerde gegevens. De gewone tekst en links zijn voor bezoekers hetzelfde. [Schema.org: about](https://schema.org/about), [Schema.org: mentions](https://schema.org/mentions).

Er zijn 36 gepubliceerde TFSC-records met zulke linkmarkeringen. Op 14 pagina’s worden die momenteel ook uitgegeven als extra entiteiten in JSON-LD. Het Beroepengids-artikel [Wat is een Field Service Engineer?](https://thefieldservicecompany.nl/beroepengids/artikel/wat-is-een-field-service-engineer/) bevat bijvoorbeeld vijf mentions. De uitbreiding bewaart de bestaande koppelingen en de organisatieverwijzing. Zij voegt geen automatische markeringen toe aan andere links.

Voor TFSC is geen afzonderlijke rankingwinst van deze eigenschappen aangetoond. Google beschrijft gestructureerde gegevens als hulp om pagina’s te begrijpen, niet als algemene rankingfactor op zichzelf. Mijn beoordeling: de bestaande informatie behouden is zinvol omdat dit met een kleine uitbreiding kan; alleen deze functie rechtvaardigt geen Pro-abonnement. [Google Search Central](https://developers.google.com/search/blog/2020/08/rich-results-search-console#will-a-site-rank-higher-against-competitors-if-it-implements-structured-data).

## Wat is opgevangen en wat kan vervallen

| Onderdeel | Uitvoering en uitkomst |
| --- | --- |
| Breadcrumbs in Elementor | De uitbreiding registreert het opgeslagen widgettype `breadcrumbs` en gebruikt de renderer van Free. Templates, widget-ID’s en klassen blijven gelijk. De bestaande TFSC-filters voor comboarchieven en Beroepengids blijven werken. |
| About/Mentions | De bestaande gemarkeerde links worden op hetzelfde schemafiltermoment verwerkt. Op alle 36 gemarkeerde pagina’s bleef het volledige openbare schema gelijk. Ook de 22 pagina’s zonder actuele Thing-uitvoer bleven gelijk. |
| Redirect met queryparameters | De uitbreiding laat Free vergelijken met de volledige URI. De echte opgeslagen regel 133 matcht met en zonder Pro; een afwijkende query matcht niet. Er is geen aanvraag naar de beveiligings-URL gestuurd. |
| Lokale sitemap | Free bevat de generator, maar Pro schakelt hem normaal in. De uitbreiding doet dat voor de bestaande enkele bedrijfslocatie. De lokale sitemap, KML en sitemapindex bleven gelijk. Meerdere locaties worden niet nagebouwd. |
| Local SEO-bedrijfsgegevens | De bedrijfsidentiteit en enkele locatie worden door Free geleverd. Het openbare bedrijfsschema bleef gelijk tijdens de proef. Meerdere locaties stonden al uit. |
| Zoekintentieanalyse in Rank Math | Uitgezet. Alleen `determine_search_intent` veranderde van `on` naar `off`; alle andere algemene instellingen bleven exact gelijk. Deze redactiefunctie wordt niet nagebouwd. |
| Pro-analytics, tracker en extra rapportage | Worden niet nagebouwd. De Analytics-module stond effectief uit. De opgeslagen 15-daagse e-mailkeuze is slapend; de rapportcallback was niet actief. Bij toekomstige herinschakeling moeten Free-instellingen worden gekozen. |
| Overige Pro-beheermiddelen | Extra bulkfuncties, uitgebreide schema-editor, redirectcategorieën, exportfuncties en soortgelijke mogelijkheden vervallen. Toekomstige automatische opruiming van redirects bij het verwijderen van berichten/termen verandert; bestaande regels blijven beschikbaar. Zie de volledige [functionaliteitsaudit](2026-11-07-rank-math-pro-feature-audit.md). |

SEO Analyzer is een andere module dan zoekintentieanalyse. De effectieve SEO Analyzer-module stond bij deze controle aan. Die is niet gewijzigd op basis van het verzoek om zoekintentie uit te zetten.

## Bewijs uit de live proef

Versies: WordPress op productie met PHP 8.5.12, Rank Math Free 1.0.281, Pro 3.0.124, Elementor 4.3.6, Elementor Pro 4.3.3 en de eigen uitbreiding 0.2.0. De lokale volledige test gebruikt WordPress 6.8.3, PHP 8.3.33 en dezelfde versies van Free en Elementor; de live proef vult dat verschil aan.

| Controle | Dekking | Resultaat met Pro uit |
| --- | --- | --- |
| Openbare pagina’s | 48 URL’s: alle 36 gemarkeerde records, homepage, Beroepengids en tussen-/detail-/artikelarchief, vacatureoverzicht, comboarchief, blogoverzicht en blogartikel, werkgeverspagina, Kennisbankoverzicht en artikel | Alle 48 HTTP 200. Titels, descriptions, robots, canonicals, sociale metadata, volledige JSON-LD, breadcrumbs en widgetmarkup gelijk. |
| Cachecontrole | Voor en na unieke controleparameters en cacheheaders beoordeeld | Alle 48 pagina’s in beide rondes: LiteSpeed- en QUIC.cloud-cache `miss`. De proef is niet gebaseerd op oude gecachte Pro-pagina’s. |
| Sitemaps en locatiebestand | 30 bestanden: sitemapindex, alle 28 daarin genoemde sitemaps en `locations.kml` | HTTP 200 en inhoud gelijk, na weglaten van sitemap-`lastmod`-velden. Die tijdstempels kunnen per aanvraag veranderen. URL’s en andere inhoud zijn wel vergeleken. |
| Desktop en mobiel | Zeven pagina’s op 1440×1000 en 390×844: Beroepengids-tussenarchief, detailarchief, artikel, comboarchief, blogoverzicht, werkgeverspagina en blogartikel | Alle 14 vergelijkingen gelijk voor markup, berekende stijlen en breadcrumb-screenshots; geen pixelverschillen. Bestaande horizontale mobiele weergave bleef gelijk. |
| Actieve redirects | 234 actieve regels; checksum van ID, bronnen, bestemming en code vóór en na | Gelijk. Queryregel 133 matcht exact en wijst een afwijkende query af. Een gewone bestaande 301-regel is ook via een echte aanvraag bevestigd met dezelfde bestemming. Niet alle 234 regels zijn afzonderlijk via HTTP aangeroepen. |
| Werkelijk geladen widget | Geauthenticeerde runtimecontrole met Pro uit | `TFSC\RankMathBreadcrumbs\Widget_Breadcrumbs` werd geladen. Na herstel van Pro gaf de uitbreiding de uitvoering weer aan Pro. |
| Instellingen | Snapshot vóór, na uitschakeling van zoekintentie en na herstel van Pro | Zoekintentie blijft uit. De andere algemene instellingen bleven bij die wijziging gelijk. |

Lokale tests: 27 gerichte controles en 56 controles in een volledige tijdelijke WordPress-installatie. Daarbij zijn ook opnieuw gegenereerde Elementor-CSS, schema-samenvoeging, queryvergelijking, ontbrekende gegevens en voorrang van Pro getest. Zie [testinstructies](../wordpress/plugins/tfsc-rank-math-breadcrumbs/README.md).

De [samengevatte bewijzen](evidence/rank-math-free-2026-11-07.json) bevatten de uitkomsten en hashes. Ruwe instellingen, aanmeldgegevens en queryparameterwaarden staan niet in de repository. De tijdelijke beheersnippet 410 is gedeactiveerd; de auditroute retourneert HTTP 404.

## Definitief overstappen en terugzetten

De geteste uitbreiding is al geïnstalleerd. Voor de definitieve overstap:

1. Laat **Rank Math SEO (Free)**, **TFSC Rank Math Breadcrumbs**, Elementor en Elementor Pro actief. Deactiveer uitsluitend **Rank Math SEO PRO** via WordPress → Plugins. Er zijn geen shortcodevervangingen of templatewijzigingen nodig.
2. Controleer na een verse aanvraag het Beroepengids-tussenarchief, het detailarchief en het vacature-comboarchief. Controleer ook `sitemap_index.xml`, `local-sitemap.xml` en `locations.kml`. Bij oude cacheweergave kan de betreffende paginacache worden ververst; tijdens deze proef is geen brede cacheopschoning uitgevoerd.
3. Bewaar de Pro-plugin voorlopig gedeactiveerd voor eenvoudig herstel. Terugzetten kan door Pro weer te activeren. De uitbreiding stopt dan automatisch haar eigen aansluitingen, zodat er geen dubbele widgets of entiteiten ontstaan.

De ZIP is [versie 0.2.0](../wordpress/plugins/dist/tfsc-rank-math-breadcrumbs-0.2.0.zip). Installatie op een andere omgeving: upload deze via Plugins → Nieuwe plugin → Plugin uploaden, activeer eerst naast Pro en volg daarna dezelfde controle. Installeer het pakket niet over lokaal aangepaste pluginbestanden zonder die te bewaren.

De bevinding is dat de **aangetroffen, gecontroleerde websitefuncties met Free en deze uitbreiding kunnen blijven werken**. De proef bewijst geen gelijkwaardigheid van alle Pro-beheerfuncties, alle mogelijke pagina’s, toekomstige versies of de verwerking door Google. Er zijn geen abonnementen of accountkoppelingen gewijzigd.
