# Beveiligingsreview TFSC Rank Math-uitbreiding — 7 november 2026

**Versie 0.3.1 staat actief op productie.** Dit is een zelfstandige WordPress-plugin, geen blijvend Code Snippet. De tijdelijke beheersnippet voor installatie en gecontroleerde migratie is gedeactiveerd; beide tijdelijke REST-routes geven daarna 404. Rank Math Free blijft actief, Pro blijft uit en SEO Analyzer blijft aan.

## Gevonden en verholpen

De opruimfunctie in 0.3.0 vertrouwde het gewone metadataveld `rank_math_auto_redirect` als bevoegdheid om redirectregels te verwijderen. Dat was onveilig. In een wegwerpinstallatie kon een gebruiker met de rol Auteur via de bestaande Rank Math REST-route een willekeurig redirect-ID in zijn eigen bericht zetten. Bij definitief verwijderen van dat bericht verwijderde de uitbreiding vervolgens ook die onafhankelijke redirect.

De aanval vereiste een aangemelde gebruiker die zijn eigen bericht mocht aanpassen en verwijderen. Er was geen beheerdersrecht nodig. Het ging om misbruik van de bestaande Rank Math-metadataroute, niet om een openbare route die onze plugin toevoegt. De gewone WordPress-capability voor dat specifieke metadataveld was zelfs niet nodig: de bestaande Rank Math-route accepteerde de bewerking op basis van de berichtrechten. De aanval is uitsluitend lokaal uitgevoerd, nooit met productiecontent of productieredirects.

Dezelfde test geeft met 0.3.1 opnieuw HTTP 200 voor het aanpassen en verwijderen van het eigen testbericht, maar de onafhankelijke redirect blijft nu bestaan. We blokkeren dus de ongeoorloofde opruiming zonder de normale berichtbewerking uit te schakelen.

## Wijziging

- Alleen een intern serverbewijs geeft nog toestemming voor opruiming. De HMAC-SHA256-handtekening bindt de exacte lijst redirect-ID’s aan site, objecttype en object-ID. Kopiëren naar een ander bericht, een term of een andere site werkt niet.
- Een eigen willekeurige sleutel van 256 bits staat in een niet-autoloadende, niet via REST gepubliceerde WordPress-optie. Het bewijs staat in private metadata. Leescontroles maken geen sleutel aan. Bij een ontbrekende sleutel of ongeldig bewijs wordt opruiming overgeslagen.
- Nieuwe bewijzen ontstaan uitsluitend vanuit de echte automatische redirectgebeurtenis. Een geïnjecteerde waarde in het oude veld wordt ook bij een latere echte URL-wijziging niet overgenomen.
- De bestaande 37 bericht- en 15 termkoppelingen zijn eenmalig gecontroleerd overgenomen. De beheeractie was gebonden aan de checksum van de vooraf beoordeelde verzameling. Zij verwijderde nul redirects. Er is geen blijvende migratieroute of automatische vertrouwensfallback.
- ID-validatie weigert ook integer-overflow en afwijkende invoertypen. De schemafilters stoppen veilig bij ontbrekende afhankelijkheden of ongeldige gegevens. Beide PHP-bestanden hebben een directe-toegangsbeveiliging; de widget behoudt zijn beperkte lijst toegestane HTML-tags.

De plugin gebruikt voor verwijdering de bestaande Free-API met cacheopruiming. Zij voegt geen eigen openbaar REST-endpoint, uploadfunctie of uitvoer van aangeleverde code toe. Bij actieve Pro blijft Pro verantwoordelijk voor de daadwerkelijke verwijdering; onze uitbreiding houdt dan alleen interne bewijzen bij vanuit echte redirectgebeurtenissen.

## Uitgevoerde tests

De volledige test draait met een nieuwe database in PHP-geheugen: WordPress 6.8.3, PHP 8.3.33, Elementor 4.3.6, Rank Math Free 1.0.281 en SQLite Database Integration 2.2.14. Productiegegevens, configuratiebestanden en authenticatie zijn niet gekopieerd. REST-aanvallen gebruiken de echte WordPress REST-dispatcher en rechtencontrole in die installatie; er is geen afzonderlijke HTTP-server getest.

| Testgroep | Resultaat |
| --- | --- |
| Oorspronkelijke aanval via Auteur → Rank Math REST → definitief verwijderen | Reproduceerbaar in 0.3.0; onafhankelijke redirect behouden in 0.3.1 |
| Rollen, bewijsvervalsing, kopiëren, ontbrekende/verkeerde sleutel, 20 ongeldige ID-varianten, echte systeemverwijdering, schema- en query-invoer | 54 controles geslaagd |
| Schadelijke HTML-taginstellingen door de echte Elementor-renderer | 9 controles geslaagd |
| Free en Elementor daadwerkelijk gedeactiveerd in de wegwerpinstallatie | 6 controles geslaagd |
| Direct aanroepen van beide PHP-bestanden zonder WordPress | 2 controles geslaagd |
| Bestaande volledige compatibiliteitsproef | 56 controles geslaagd |
| Free-baseline en automatische opruimworkflows met echte databasebewerkingen | 8 + 18 controles geslaagd |
| Gerichte widget-, CSS- en TFSC-breadcrumbfilterproef | 27 controles geslaagd |

De aanvalstests controleren ook dat een legitieme URL-wijziging een eerder geïnjecteerd ID niet alsnog ondertekent, dat een Auteur de private bewijsgegevens niet via de Rank Math-route kan vervangen, en dat een systeemtaak zonder ingelogde gebruiker uitsluitend een echt gekoppelde redirect verwijdert. Schema-invoer is getest tot en met de JSON-uitvoerketen van Free. Overige geharde invoerpaden zijn geen afzonderlijk aangetoonde kwetsbaarheden in eerdere versies.

De reproduceerbare tests staan in [de testmap](../wordpress/plugins/tfsc-rank-math-breadcrumbs/tests/). De [geschoonde bewijsgegevens](evidence/rank-math-security-2026-11-07.json) bevatten de tellingen, hashes en productiecontroles, zonder sleutels of inloggegevens.

## Controle na installatie

- Alle 265 bestaande redirectregels en de 52 bestaande legacykoppelingen zijn gelijk gebleven aan de vooraf gecontroleerde snapshot. Dit is geen volledige forensische uitspraak over de websitegeschiedenis.
- De drie geïnstalleerde bestanden komen byte voor byte overeen met de beoordeelde bron en het pakket 0.3.1.
- De hashes van de algemene Rank Math-, titel- en sitemapinstellingen zijn behouden. De vijf gecontroleerde PHP-bestanden van TFSC Structured Data 0.5.3 zijn ongewijzigd gebleven.
- Bij 48 openbare pagina’s zijn metadata, volledige JSON-LD en breadcrumb-/widgetmarkup gelijk aan de eerdere referentie. Alle 30 sitemap-/KML-adressen zijn bereikbaar. Tien ruwe sitemapchecksums verschillen van de ochtendreferentie door nieuwe artikelen en volgordeverschillen; er wordt geen bytegelijke vergelijking van alle sitemaps geclaimd.
- Rechtstreeks opvragen van de twee plugin-PHP-bestanden levert een lege body. De twee tijdelijke beheerroutes geven 404 en snippet 410 is inactief.

## Gebruik en herstel

Gebruik [pakket 0.3.1](../wordpress/plugins/dist/tfsc-rank-math-breadcrumbs-0.3.1.zip) of hoger. Dit pakket bevat alleen de twee PHP-bestanden en de handleiding. Het staat al geïnstalleerd; opnieuw uploaden is nu niet nodig. Laat Free en de uitbreiding actief. Bestaande templates en CSS hoeven niet te worden vervangen door shortcodes.

Neem de interne sleutel en bewijsmetadata mee in normale databasebackups. Bij verlies wordt automatische opruiming overgeslagen; bestaande redirects worden daardoor niet breed verwijderd. Op een andere bestaande site moeten legacykoppelingen eerst gecontroleerd worden overgenomen. Alleen bestanden uploaden importeert geen bewijsgegevens uit deze TFSC-database.

Bij een compatibiliteitsprobleem kan deze uitbreiding worden uitgezet of kan Pro het widget weer overnemen. Zet de opruimfunctie niet terug naar 0.3.0: daarmee komt de gevonden fout terug. Als Pro wordt gereactiveerd, gelden de eigen implementatie en beveiligingsgrenzen van Pro.

Dit is een gerichte bron- en aanvalstest van onze uitbreiding, geen onafhankelijke certificering of volledige WordPress-pentest. Productie draait met PHP 8.5.12; de geïsoleerde gedragstests draaiden met PHP 8.3.33. Elementor Pro, alle routers en toekomstige versies zijn niet volledig lokaal nagebouwd. Een beheerder of andere plugin met rechtstreekse PHP-/databasetoegang valt buiten de hier geteste lage-rechtenaanval. De handtekening bewijst de relatie met een redirect-ID, niet de inhoud van een later door een beheerder gewijzigde regel; de opruimpolitiek blijft daarin gelijk aan de bestaande Pro-werkwijze.

## Technische uitgangspunten

Ook databasewaarden zijn onbetrouwbare invoer; zie [WordPress: beveiliging](https://developer.wordpress.org/apis/security/). Een voorvoegsel met underscore maakt metadata [afgeschermd in de WordPress-conventie](https://developer.wordpress.org/reference/functions/is_protected_meta/), maar vormt op zichzelf geen bewijs van herkomst. Daarom gebruikt deze versie daarnaast een handtekening, [veilige vergelijking](https://www.php.net/manual/en/function.hash-equals.php) en een sleutel uit [random_bytes](https://www.php.net/manual/en/function.random-bytes.php).
