import fs from 'node:fs';
import path from 'node:path';
import {createRequire} from 'node:module';
import {createHash} from 'node:crypto';
const [version,sources,core,sqlite,modules,manifestFile,dependencies,results]=process.argv.slice(2);
if(!results)throw Error('Usage: node run.mjs PHP_VERSION SOURCES WORDPRESS SQLITE NODE_MODULES MANIFEST DEPENDENCIES RESULTS');
const require=createRequire(path.resolve(modules,'loader.cjs'));
const {PHP}=require('@php-wasm/universal');const {loadNodeRuntime}=require('@php-wasm/node');
const manifest=JSON.parse(fs.readFileSync(manifestFile));
for(const [name,file] of Object.entries(manifest.sources)){
 if(name.includes('..')||!name.match(/^[a-zA-Z0-9_./-]+$/))throw Error('Invalid manifest path');
 if(createHash('sha256').update(fs.readFileSync(path.join(sources,name))).digest('hex')!==file.sha256)throw Error('Source differs from reviewed installation: '+name);
}
const php=new PHP(await loadNodeRuntime(version,{emscriptenOptions:{processId:4}}));
function copy(source,target){php.mkdir(target);for(const e of fs.readdirSync(source,{withFileTypes:true})){if(e.name==='wp-config.php')continue;const s=path.join(source,e.name),t=target+'/'+e.name;if(e.isDirectory())copy(s,t);else if(e.isFile())php.writeFile(t,fs.readFileSync(s));}}
copy(core,'/wordpress');copy(sqlite,'/wordpress/wp-content/plugins/sqlite-database-integration');
copy(sources,'/audit-sources');copy(dependencies,'/audit-dependencies');
for(const name of fs.readdirSync(sources))copy(path.join(sources,name),'/wordpress/wp-content/plugins/'+name);
php.writeFile('/wordpress/wp-content/db.php',fs.readFileSync(path.join(sqlite,'db.copy')));
php.mkdir('/wordpress/wp-content/mu-plugins');
php.writeFile('/wordpress/wp-content/mu-plugins/no-network.php',`<?php add_filter('pre_http_request',function(){return new WP_Error('isolated_network_blocked','External HTTP blocked in disposable test.');},-999);`);
php.writeFile('/plugins.json',JSON.stringify(Object.keys(manifest.plugins)));
php.writeFile('/wordpress/wp-config.php',`<?php
define('DB_NAME','test');define('DB_USER','test');define('DB_PASSWORD','test');define('DB_HOST','localhost');define('DB_CHARSET','utf8');define('DB_COLLATE','');
define('WP_HOME','https://example.test');define('WP_SITEURL','https://example.test');define('WP_MEMORY_LIMIT','512M');define('WP_MAX_MEMORY_LIMIT','512M');
define('WP_DEBUG',true);define('WP_DEBUG_DISPLAY',false);define('WP_DEBUG_LOG',true);define('DISABLE_WP_CRON',true);define('AUTOMATIC_UPDATER_DISABLED',true);define('WP_HTTP_BLOCK_EXTERNAL',true);
define('AUTH_KEY','isolated-fixture');define('SECURE_AUTH_KEY','isolated-fixture');define('LOGGED_IN_KEY','isolated-fixture');define('NONCE_KEY','isolated-fixture');
$table_prefix='wp_';if(!defined('ABSPATH'))define('ABSPATH',__DIR__.'/');require ABSPATH.'wp-settings.php';`);
async function run(code){const r=await php.run({code,relativeUri:'/',$_SERVER:{HTTP_HOST:'example.test',REQUEST_URI:'/',HTTPS:'on',SERVER_PROTOCOL:'HTTP/1.1',REQUEST_METHOD:'GET',REMOTE_ADDR:'192.0.2.10'}});if(r.text)console.log(r.text);if(r.errors)console.error(r.errors);if(r.exitCode)throw Error('PHP exit '+r.exitCode);return r;}
try{
 await run(`<?php define('WP_INSTALLING',true);require '/wordpress/wp-load.php';require '/wordpress/wp-admin/includes/upgrade.php';wp_install('Security fixture','localadmin','local@example.test',false,'','fixture-only');update_option('active_plugins',json_decode(file_get_contents('/plugins.json'),true));echo 'Disposable WordPress installed';`);
 await run(`<?php define('WP_ADMIN',true);require '/wordpress/wp-load.php';wp_set_current_user(1);do_action('admin_init');echo json_encode(['wordpress'=>$wp_version,'php'=>PHP_VERSION,'plugins_loaded'=>count(get_option('active_plugins'))]);`);
 for(const name of ['security.php','stress.php','upload-provenance.php'])await run(fs.readFileSync(new URL(name,import.meta.url),'utf8'));
 let entries=0;
 for(const relative of Object.keys(manifest.sources)){
  if(!relative.endsWith('.php'))continue;
  const r=await run(`<?php require '/audit-sources/${relative}';`);if(r.text!=='')throw Error('Unexpected direct output: '+relative);entries++;
 }
 console.log(JSON.stringify({direct_php_files_checked:entries}));
}finally{
 fs.mkdirSync(results,{recursive:true});
 if(php.fileExists('/wordpress/wp-content/debug.log')){const log=php.readFileAsText('/wordpress/wp-content/debug.log');fs.writeFileSync(path.join(results,'php-debug-'+version+'.log'),log,{mode:0o600});console.log(JSON.stringify({debug_log_bytes:log.length}));}
 php.exit();
}
