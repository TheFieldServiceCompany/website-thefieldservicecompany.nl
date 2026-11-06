# Voortgang Updraft naar pCloud

De gekoppelde pCloud-projectmap `TvC Website Plugins/backup plugin WordPress` is de werkmap. GitHub bewaart de broncode, de handleiding, de installatie-ZIP per versie en de voortgang. Leg per wijziging vast wat is aangepast, wat is getest, op welke omgeving de versie draait en wat nog openstaat. Bewaar WordPress-back-ups, toegangssleutels en interne beheerbeelden buiten de openbare repository.

## 2026-11-05

- Versie 0.1.0 is op de ontwikkelsite geïnstalleerd en geactiveerd. Het instellingenscherm opende en de REST-controle bevestigde dat de plug-in actief was.
- De planning, hervatting en bestandscontrole zijn getest op een geïsoleerde WordPress-installatie met een echte UpdraftPlus-back-up en nagebootste pCloud-antwoorden.
- Op dev stond automatische upload uit. Het instellingenscherm gaf een melding over geplande WordPress-taken.
- Openstaand na goedkeuring door pCloud zijn de koppeling op dev, de controle van geplande taken, één proefupload en een herstelproef. Daarna kan het automatische schema worden getest.

## 2026-11-06

- De broncode, handleiding en installatie-ZIP in de werkmap zijn byte voor byte vergeleken met GitHub. Het beheerbeeld van dev staat in de werkmap.
- [TeamUpdraft beschrijft](https://teamupdraft.com/privacy/) een ingebouwde pCloud-app met OAuth. De aanmelding loopt via de authenticatieserver van TeamUpdraft. De back-upbestanden gaan rechtstreeks van de WordPress-host naar pCloud. Met onze eigen OAuth-app volgt eerst de goedkeuring van pCloud.
- [TeamUpdraft documenteert](https://teamupdraft.com/docs/updraftplus/migration/how-to/wordpress-migration-failed/) uploads in delen met cURL. De precieze pCloud-endpoints voor die uploads zijn niet vastgesteld.
- pCloud heeft de app goedgekeurd volgens de beheerder. De eerste koppelpoging met 0.1.0 gaf een algemene tokenfout. Versie 0.1.1 accepteert zowel het veld `uid` uit de API-documentatie als `userid` uit het SDK-voorbeeld. Het scherm gebruikt de pCloud-labels in het Engels en bewaart de laatste koppelstatus zonder geheimen te loggen.
- De OAuth-varianten en foutafhandeling zijn getest met nagebootste API-antwoorden. Versie 0.1.1 is verpakt voor dev. De echte koppeling en proefupload staan nog open.
- Versie 0.1.1 is op dev geïnstalleerd. WordPress REST bevestigde de actieve versie. De opgeslagen Client ID is nog aanwezig en het scherm toont de Engelse pCloud-velden. De koppeling stond op `Not connected`, het automatische schema stond uit en de melding over geplande taken was zichtbaar.
