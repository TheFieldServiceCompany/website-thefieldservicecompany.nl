import fs from 'node:fs';import vm from 'node:vm';import assert from 'node:assert/strict';
const code=fs.readFileSync(process.argv[2]||'wordpress/elementor/templates/26022-single-vacature/scroll-sticky.js','utf8');new Function(code);
function setup({editor=false,headerBottom=70,triggerBottom=500}={}){
 const vars={},classes=new Set(),events={};let frame;
 const column={getBoundingClientRect:()=>({left:264,width:767})};
 const panel={dataset:{},style:{setProperty:(k,v)=>vars[k]=v},classList:{add:c=>classes.add(c),toggle:(c,v)=>v?classes.add(c):classes.delete(c)},parentElement:column};
 const trigger={getBoundingClientRect:()=>({bottom:triggerBottom})};const header={getBoundingClientRect:()=>({bottom:headerBottom})};
 const doc={readyState:'complete',body:{classList:{contains:()=>editor}},querySelector:s=>s.includes('scroll-sticky-wrapper')?panel:s.includes('sticky-trigger')?trigger:s.includes('17275')?header:null};
 const win={addEventListener:(k,f)=>events[k]=f,requestAnimationFrame:f=>frame=f};vm.runInNewContext(code,{document:doc,window:win});
 return {panel,vars,classes,events,move(bottom){triggerBottom=bottom;events.scroll();if(frame){const f=frame;frame=null;f();}},resize(top){headerBottom=top;events.resize();if(frame)frame();}};
}
let n=0;function check(ok,name){assert.ok(ok,name);n++;console.log('PASS',name);}
let s=setup();check(!s.classes.has('is-visible')&&s.panel.inert,'hidden panel excluded from keyboard focus');s.move(71);check(!s.classes.has('is-visible'),'not shown before trigger reaches header');s.move(70);check(s.classes.has('is-visible')&&s.vars['--tfsc-vacancy-panel-top']==='70px','shown exactly below header at threshold');s.move(75);check(s.classes.has('is-visible'),'small reverse scroll does not flicker');s.move(87);check(!s.classes.has('is-visible'),'hidden after reverse margin');s.move(60);check(s.vars['--tfsc-vacancy-panel-left']==='264px'&&s.vars['--tfsc-vacancy-panel-width']==='767px','fixed panel preserves article-column alignment');s.resize(50);check(s.vars['--tfsc-vacancy-panel-top']==='50px','mobile/resize follows actual header');s=setup({headerBottom:102});s.move(100);check(s.classes.has('is-visible')&&s.vars['--tfsc-vacancy-panel-top']==='102px','admin bar included through measured header edge');s=setup({editor:true});check(s.classes.has('is-visible')&&!s.panel.inert&&!s.events.scroll,'editor remains visible without visitor scroll handling');console.log(JSON.stringify({passed:n}));
