# Onderzoek vacaturetemplate: zichtbaarheid in Elementor en formuliercontext

Datum: 31 oktober 2026.
Status: gedeeltelijk onderzocht; actuele templatecode en editor nog niet geverifieerd.
Uitvoering: uitsluitend lezen en deze onderzoeksnotitie opslaan. Geen websitewijzigingen, formulierinzendingen of deployments uitgevoerd.

## Opdracht en toestemming

De eigenaar meldt dat een element pas na een bepaalde scrollpositie zichtbaar wordt.
Dat werkt voor bezoekers, maar het element verdwijnt ook tijdens het bewerken in Elementor.
De bestaande uitlijning met de onderliggende content moet behouden blijven.

Daarnaast vraagt de eigenaar of een script dat vacaturegegevens naar het sollicitatieformulier
doorgeeft nog nodig is sinds de Form Bridge. Onderzoek is toegestaan; iedere wijziging
aan website, template of scripts vereist eerst afzonderlijke toestemming.
De eigenaar heeft apart opdracht gegeven het onderzoek in de juiste GitHub-repository vast te leggen.

## Repositorykeuze en geraadpleegde bronnen

- Deze repository beheert de WordPress-/Elementor-website. De README vermeldt expliciet
  dat repositorybestanden niet automatisch naar WordPress of Elementor worden uitgerold.
  Daarom hoort de onderzoeksnotitie hier, niet in de artikelen-, analytics- of SEO-repository.
- Onderzochte websitebasis: commit `bccc1d65a5da6e5cfc0406c5a6a532dd83ca40ae`.
- `wordpress/elementor.md` is nog een inventarisatieplaceholder.
  `wordpress/elementor/custom-code/` bevat op deze basis alleen een README.
  De bedoelde vacaturetemplate en beide scripts zijn hier niet als bruikbare bron aangetroffen.
- De actuele website-`wordpress/snippets/README.md`, onderdeel “Elementor Custom Code / Adviesknop”,
  beschrijft al een vereenvoudigde adviesroute die alleen het vacaturenummer doorgeeft.
- De Form Bridge-bron en mappingdocumentatie zijn aangetroffen in
  [tfsc-crm-ats, branch codex/wordpress-form-bridge](https://github.com/TheFieldServiceCompany/tfsc-crm-ats/tree/codex/wordpress-form-bridge/wordpress/tfsc-form-bridge),
  branchstand bij onderzoek `76280c3608edc4fb84d44086ef9b7f2cc54ebc65`.
  Ze staan niet op de onderzochte `main` van die repository.
- Daar zijn `src/SubmissionNormalizer.php` en
  `docs/FORM-DATA-MAPPING-2026-10-20.md` rechtstreeks uit GitHub gelezen.
  Deze broncontrole bevestigt niet welke pluginversie en mapping momenteel op de website actief zijn.
- [Elementors eigen frontendbron](https://github.com/elementor/elementor/blob/main/assets/dev/js/frontend/frontend.js)
  biedt `elementorFrontend.isEditMode()`, gebaseerd op `config.environmentMode.edit`.

## Beperking websitecontrole

Het openen van `https://dev.thefieldservicecompany.nl/wp-admin/` werd door het
beheerdersbeleid van de browser geblokkeerd. Die blokkade is niet omzeild.
Er is geen Elementor-editor geopend en geen actuele DOM-, CSS- of scriptinspectie
van de vacaturetemplate uitgevoerd. Het gemelde gedrag is daarom nog een
gebruikerswaarneming, geen zelfstandig gereproduceerde fout.
De exacte doelomgeving moet ook worden bevestigd wanneer de eigenaar de hoofdsite bedoelt.

## Voorlopige oplossingsrichting voor de editor

Een gerichte uitzondering voor Elementor-bewerkmodus is technisch mogelijk:
laat het betrokken element in de editor zichtbaar en bewerkbaar, en voer de
scrollgestuurde verberging alleen voor bezoekers uit.

Dit is een voorstel, geen gecontroleerde patch. Voor de concrete aanpassing moeten
de selector, start-CSS, eventuele inline stijlen, scrollhandler en initialisatie
van het bestaande script worden gelezen. Alleen een vroege return uit JavaScript
is onvoldoende als CSS het element aanvankelijk al verbergt.

Behoud de bestaande frontendlogica voor positie, breedte, uitlijning, marges en
eventuele ruimtehouder. Pas alleen in de editor de werkelijk relevante
verbergende eigenschappen aan. Bepaal aan de hand van de bron of positionering
in de editor eveneens een uitzondering nodig heeft; schrijf geen algemene
CSS-reset die de bestaande uitlijning onbedoeld verandert.
De uitzondering moet ook blijven werken wanneer Elementor het widget opnieuw rendert.

## Voorlopige beoordeling formuliergegevens

Volgens de bridge-mapping is bij solliciteren het veld `vacaturenummer` gekoppeld
aan `vacancy.number`. Dat nummer identificeert de vacature. De normalizer vertaalt
aangeleverde formuliervelden volgens de mapping; deze bron toont geen automatische
vervanging van de overdracht van het vacaturenummer vanaf de vacaturepagina.

De documentatie noemt vacaturetitel, vacaturelink, bedrijfsnaam en dienstverband
bij sollicitaties als “Niet versturen”; de canonieke informatie wordt uit de
vacature afgeleid. Voor advies is alleen het doorgeven van het nummer al
gedocumenteerd. Dit betekent niet automatisch dat de bestaande velden of het
volledige script verwijderd mogen worden: de formulierweergave, bevestigingen,
e-mails en andere bestemmingen kunnen er nog gebruik van maken.

Conclusie: mogelijk vereenvoudigen tot het vacaturenummer; volledig verwijderen
is nog niet onderbouwd. Eerst vaststellen of het onbekende script dat nummer,
de knopbestemming, een popup of andere benodigde formuliercontext verzorgt.
Als Elementor het nummer al onafhankelijk en correct invult, kan de betreffende
overdrachtscode mogelijk vervallen. Dat moet op de actuele template worden bewezen.

## Nog nodig vóór een wijzigingsvoorstel

1. Beide actuele scriptblokken en bijbehorende CSS, liefst met template-export
   of template-ID en de exacte omgeving.
2. De doelknoppen, formulierbestemming en bron van het verborgen vacaturenummerveld.
3. Een kleine, concrete wijziging met behoud van de bestaande uitlijning ter goedkeuring.

Na afzonderlijke toestemming: editorzichtbaarheid en bewerken na opnieuw renderen
controleren; bezoekersweergave vóór en na de scrolldrempel op desktop en mobiel
controleren; formuliercontext en eventuele bevestigingsmails controleren.
Een echte inzending of wijziging aan live systemen valt niet onder deze onderzoeksopdracht.

## Besluit

Geen scripts verwijderd of aangepast. Geen template opgeslagen. Geen cache geleegd.
Geen formuliertest verstuurd. Alleen deze notitie is toegevoegd.
