import fs from 'node:fs';
import vm from 'node:vm';
import assert from 'node:assert/strict';
const code=fs.readFileSync(process.argv[2] || 'wordpress/elementor/custom-code/shared-scroll-navigation/scroll-navigation.js','utf8');
function setup({editor=false,headerExists=true,admin=0,height=70,panelHeight=100}={}) {
    const events={},winEvents={},vars={},classes=new Set(),headerClasses=new Set(['elementor-sticky--active']);
    let frame, menu=false,clock=2000;
    const root={scrollHeight:10000,classList:{contains:c=>classes.has(c),add:c=>classes.add(c),toggle:(c,on)=>on?classes.add(c):classes.delete(c)},style:{setProperty:(k,v)=>vars[k]=v}};
    const header={classList:{contains:c=>headerClasses.has(c)},getBoundingClientRect:()=>({height}),querySelector:()=>menu?{}:null,contains:e=>e===header,matches:()=>true,addEventListener:(k,f)=>events['header-'+k]=f};
    const wrapper={classList:{contains:()=>true}};
    const panel={classList:{contains:()=>true},closest:()=>wrapper,getBoundingClientRect:()=>({height:panelHeight})};
    const body={classList:{contains:()=>editor}};
    const document={documentElement:root,body,readyState:'complete',activeElement:body,querySelector:s=>s.includes('17275')?(headerExists?header:null):s.includes('26022')?panel:null,getElementById:id=>id==='wpadminbar'?(admin?{getBoundingClientRect:()=>({bottom:admin})}:null):id==='target'?{}:null,addEventListener:(k,f)=>events[k]=f};
    const window={scrollY:0,innerHeight:800,location:{href:'https://example.com/article/',origin:'https://example.com',pathname:'/article/'},addEventListener:(k,f)=>winEvents[k]=f,requestAnimationFrame:f=>frame=f};
    vm.runInNewContext(code,{document,window,URL,performance:{now:()=>clock}});
    return {root,header,classes,vars,events,document,window,scroll(y){window.scrollY=y;winEvents.scroll();if(frame){const f=frame;frame=null;f();}},setMenu(v){menu=v;},setHeight(v){height=v;winEvents.resize();if(frame)frame();},advance(ms){clock+=ms;}};
}
let checks=0;
function check(ok,name){assert.ok(ok,name);checks++;console.log('PASS',name);}
let s=setup();check(s.classes.has('tfsc-scroll-navigation')&&s.vars['--tfsc-navigation-top']==='70px','controller activates with measured header height');
s.scroll(400);check(s.header.inert&&s.vars['--tfsc-navigation-top']==='0px','downward scroll hides header and lifts vacancy panel');
s.scroll(395);check(s.header.inert,'five pixel movement does not flicker');
s.scroll(375);check(!s.header.inert&&s.vars['--tfsc-navigation-top']==='70px','upward scroll reveals header and lowers vacancy panel');
s.scroll(0);check(!s.header.inert,'top always shows header');
s=setup({admin:32});s.scroll(500);check(s.vars['--tfsc-navigation-top']==='32px','hidden header retains visible admin bar clearance');s.scroll(470);check(s.vars['--tfsc-navigation-top']==='102px','visible header adds admin bar clearance');
s=setup({height:50});s.scroll(500);s.scroll(470);check(s.vars['--tfsc-navigation-top']==='50px','mobile measures its own header height');s.setHeight(60);check(s.vars['--tfsc-navigation-top']==='60px','resize updates measured height');
s.setMenu(true);s.scroll(700);check(!s.header.inert,'open navigation menu prevents hiding');
s.setMenu(false);s.document.activeElement=s.header;s.scroll(900);check(!s.header.inert,'focused header remains accessible');
s.header.matches=()=>false;s.scroll(1100);check(s.header.inert,'pointer focus after closing a menu does not permanently pin the header');
s.document.activeElement=s.document.body;s.scroll(1200);s.events.keydown({key:'Tab'});check(!s.header.inert,'Tab restores hidden navigation before focus moves');
s.advance(500);s.scroll(1500);const link={href:'https://example.com/article/#target'};s.events.click({target:{closest:()=>link}});check(!s.header.inert&&s.vars['--tfsc-anchor-top']==='180px','anchor click reveals header and reserves header + panel + gap');
s.scroll(600);check(!s.header.inert,'anchor scroll cannot hide header during destination calculation');
s=setup({editor:true});check(!s.classes.has('tfsc-scroll-navigation'),'Elementor editor excluded');
s=setup({headerExists:false});check(!s.classes.has('tfsc-scroll-navigation'),'pages with other headers remain unchanged');
console.log(JSON.stringify({passed:checks}));
