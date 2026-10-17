# TFSC Schema Read Endpoint v2

Deze versie is aangepast om de Pods-structuur robuuster uit te lezen.

Endpoint:

```text
GET /wp-json/tfsc/v1/schema
```

Belangrijkste wijziging:
- probeert eerst `pods_api()->load_pods()`;
- als dat niets bruikbaars teruggeeft, gebruikt hij `_pods_pod` config-posts als fallback;
- per Pod worden de velddefinities geëxporteerd;
- relationship/select-gerelateerde options worden meegenomen;
- voegt `pods_status.debug` toe zodat we precies zien wat Pods teruggeeft.

Nog steeds read-only:
- geen POST/PUT/PATCH/DELETE;
- geen vacature-, kandidaat- of companyrecords;
- geen post-term relaties.

## Updaten

Je kunt de oude plugin vervangen door deze zip/pluginversie.

Daarna:

```bash
curl -s \
  -u 'chatgpt:APPLICATION_PASSWORD' \
  'https://dev.thefieldservicecompany.nl/wp-json/tfsc/v1/schema' \
  -o tfsc_wordpress_schema_v2.json
```

Controleer eventueel snel:

```bash
python3 - <<'PY'
import json
p='tfsc_wordpress_schema_v2.json'
d=json.load(open(p))
print(d.get('pods_status'))
print('pods:', len(d.get('pods', [])))
for pod in d.get('pods', []):
    print(pod.get('name'), len(pod.get('fields', [])))
PY
```
