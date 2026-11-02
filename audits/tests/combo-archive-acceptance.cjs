const {chromium}=require('playwright');
const fs=require('fs'),assert=require('node:assert/strict');
(async()=>{const browser=await chromium.launch({headless:true});
const p=await browser.newPage({viewport:{width:1440,height:1000}});await p.route(/google-analytics\.com|analytics\.google\.com|clarity\.ms\/collect/,r=>r.abort());
p.setDefaultTimeout(15000);p.setDefaultNavigationTimeout(20000);let errors=[];p.on('pageerror',e=>errors.push(e.message));const base='https://thefieldservicecompany.nl/vacatures/functienaam/field-service-engineer/';
await p.goto(base+'?tfsc_test=codex',{waitUntil:'networkidle'});await p.waitForFunction(()=>window.TFSC_SF_COMBO_READY);await p.getByRole('button',{name:'Weigeren',exact:true}).click().catch(()=>{});
const snap=()=>p.evaluate(()=>({url:location.href,canonical:document.querySelector('link[rel=canonical]')?.href,ids:[...document.querySelectorAll('[data-id="44137129"] [data-elementor-id="16237"].e-loop-item')].map(n=>+n.className.match(/e-loop-item-(\d+)/)[1]),ad:document.querySelectorAll('[data-id="44137129"] .tfsc-extra-advice').length,links:[...document.querySelectorAll('[data-id="44137129"] a.page-numbers')].map(a=>a.href)}));
let results={pages:[]};results.pages.push(await snap());assert.equal(results.pages[0].ids.length,10);assert.equal(results.pages[0].ad,1);
for(const n of [2,3]){await p.locator('[data-id="44137129"] a.page-numbers.next').click();await p.waitForURL('**/page/'+n+'/**');await p.waitForFunction(()=>window.TFSC_SF_COMBO_READY);results.pages.push(await snap());}
assert.equal(new Set(results.pages.flatMap(x=>x.ids)).size,21);assert.equal(results.pages.flatMap(x=>x.ids).length,21);
await p.locator('[data-id="44137129"] a.page-numbers.prev').click();await p.waitForURL('**/page/2/**');results.previous=await snap();assert.equal(results.previous.canonical,base+'page/2/');
await p.waitForTimeout(1800);
const input=p.locator('input[type=checkbox][value="commissioning-engineer"]');if(await input.count()){const inputId=await input.getAttribute('id');await p.locator('label[for="'+inputId+'"]').click();await p.waitForTimeout(5000);console.log('AFTER CLICK',p.url());await p.waitForURL(u=>u.pathname==='/vacatures/'&&u.search.includes('commissioning-engineer'),{timeout:10000});await p.waitForTimeout(1400);results.filter=await snap();assert.match(results.filter.url,/field-service-engineer/);console.log('FILTER PASS',results.filter.url);}else{console.log('FILTER NEEDS SELECTOR');}
results.errors=errors;assert.deepEqual(errors,[]);fs.writeFileSync(__dirname+'/acceptance-results.json',JSON.stringify(results,null,2));await browser.close();})();
