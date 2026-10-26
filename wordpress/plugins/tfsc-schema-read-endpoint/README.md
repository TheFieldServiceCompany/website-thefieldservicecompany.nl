# TFSC Schema Read Endpoint

Read-only WordPress REST endpoint voor een inventarisatie van posttypes, taxonomieën en Pods-structuren.

## Toegang

De route `GET /wp-json/tfsc/v1/schema` vereist standaard de WordPress-capability `manage_options`. Daardoor hebben anonieme bezoekers, abonnees en andere laag-bevoorrechte accounts geen toegang.

Een expliciet integratieaccount kan een eigen capability gebruiken via het filter `tfsc_schema_read_capability`. Ken die capability alleen toe aan accounts die de volledige interne sitestructuur mogen uitlezen.

```php
add_filter(
    'tfsc_schema_read_capability',
    static function () {
        return 'tfsc_read_schema';
    }
);
```

Waarden waarvan de sleutel of veldnaam op een wachtwoord, token, API-sleutel, secret, cookie of autorisatiegegeven duidt, worden als `[redacted]` teruggegeven. Interne foutmeldingen worden niet letterlijk in de REST-respons opgenomen.
