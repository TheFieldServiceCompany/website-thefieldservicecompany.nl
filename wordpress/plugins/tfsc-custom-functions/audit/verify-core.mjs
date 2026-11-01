import fs from 'node:fs';
import path from 'node:path';
import {PHP} from '@php-wasm/universal';
import {loadNodeRuntime} from '@php-wasm/node';

const root=process.argv[2];
if(!root) throw new Error('Usage: node verify-core.mjs /path/to/wordpress');
const php=new PHP(await loadNodeRuntime('8.3',{emscriptenOptions:{processId:2}}));
const base=new URL('./.test-fixtures/core/',import.meta.url).pathname;
php.mkdir(base);
for(const f of ['plugin.php','class-wp-hook.php','formatting.php','shortcodes.php','kses.php']){
  php.writeFile(base+f,fs.readFileSync(path.join(root,'wp-includes',f),'utf8'));
}
php.writeFile(base+'candidate.php',fs.readFileSync(new URL('../tfsc-custom-functions.php',import.meta.url),'utf8'));
const code=`<?php
error_reporting(E_ALL);
set_error_handler(function($level,$message){throw new Exception($message);});
define('ABSPATH','/');
function get_option($key){return $key==='blog_charset'?'UTF-8':false;}
function is_utf8_charset($charset=null){return true;}
function _canonical_charset($charset){return 'UTF-8';}
function wp_allowed_protocols(){return ['http','https'];}
function __return_false(){return false;}
function get_post(){global $test_post;return $test_post;}
function is_search(){global $search;return $search;}
function get_search_query(){global $query;return esc_attr($query);}
function home_url($request){return 'https://example.test'.($request?'/'.ltrim($request,'/'):'');}
class WP_Post {public $post_content;function __construct($text){$this->post_content=$text;}}
require '${base}plugin.php';
require '${base}formatting.php';
require '${base}kses.php';
require '${base}shortcodes.php';
require '${base}candidate.php';
$checks=[];
function check($test,$name){global $checks;if(!$test)throw new Exception('FAIL: '.$name);$checks[]=$name;}
check(apply_filters('elementor/frontend/print_google_fonts',true)===false,'Real WordPress filter disables remote Elementor fonts');
check(has_action('wp_enqueue_scripts','tfsc_custom_functions_enqueue_styles')===PHP_INT_MAX,'Real WordPress hook registry preserves enqueue priority');
check(shortcode_exists('rm_get_estimated_time_to_read')&&shortcode_exists('current_page_url'),'Both shortcodes registered in real WordPress registry');
$search=false;check(apply_filters('elementor/utils/get_the_archive_title','Original')==='Original','Real filter keeps ordinary title unchanged');
$search=true;$query='pomp & motor <script>alert(1)</script>';
$title=apply_filters('elementor/utils/get_the_archive_title','Original');
check(strpos($title,'<script>')===false,'Search query cannot insert script markup');
check(strpos($title,'&amp;amp;')===false && strpos($title,'pomp &amp; motor')!==false,'Search query entities are not double-escaped');
$test_post=new WP_Post(str_repeat('coördinatie ',200));
check(tfsc_custom_functions_reading_time()==='1 minuut','Accented Dutch words are counted as whole words');
$test_post=new WP_Post('<script>'.str_repeat('noise ',1000).'</script><style>'.str_repeat('noise ',1000).'</style>'.str_repeat('woord ',199));
check(tfsc_custom_functions_reading_time()==='Minder dan 1 minuut.','Real WordPress tag stripping excludes script and style contents');
add_shortcode('test',function(){return '';});
$test_post=new WP_Post('[test title="word word"]'.str_repeat('woord ',199));
check(tfsc_custom_functions_reading_time()==='Minder dan 1 minuut.','Real WordPress shortcode attributes excluded from reading time');
$test_post=new WP_Post(str_repeat('woord&nbsp;',200));
check(tfsc_custom_functions_reading_time()==='1 minuut','HTML non-breaking spaces decoded before counting');
$test_post=new WP_Post(str_repeat('woord ',400));
check(do_shortcode('[rm_get_estimated_time_to_read]')==='2 minuten','Reading-time callback invoked by real shortcode parser');
$wp=(object)['request'=>'vacatures'];
check(do_shortcode('[current_page_url]')==='https://example.test/vacatures','URL callback invoked by real shortcode parser');
$test_post=null;
check(do_shortcode('[rm_get_estimated_time_to_read]')==='','Missing post handled through real shortcode parser');
echo json_encode(['passed'=>count($checks),'php'=>PHP_VERSION,'checks'=>$checks],JSON_PRETTY_PRINT);
`;
const result=await php.runStream({code});
const stdout=await result.stdoutText;
const stderr=await result.stderrText;
if(stderr||stdout.includes('Fatal error'))throw new Error(stderr+stdout);
const versionSource=fs.readFileSync(path.join(root,'wp-includes/version.php'),'utf8');
const report={...JSON.parse(stdout),wordpressCoreVersion:versionSource.match(/\$wp_version\s*=\s*['"]([^'"]+)/)?.[1],scope:'Real WordPress hook/shortcode/formatting/kses implementations. Site configuration, query, post lookup and URL helpers use fixtures; no full database or Elementor runtime.'};
fs.writeFileSync(new URL('./core-validation.json',import.meta.url),JSON.stringify(report,null,2));
console.log(JSON.stringify(report,null,2));
php.exit();
