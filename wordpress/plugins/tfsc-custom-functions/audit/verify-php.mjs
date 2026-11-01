import fs from 'node:fs';
import { PHP } from '@php-wasm/universal';
import { loadNodeRuntime } from '@php-wasm/node';

const php = new PHP(await loadNodeRuntime('8.3', {emscriptenOptions: {processId: 1}}));
const plugin = fs.readFileSync(new URL('../tfsc-custom-functions.php', import.meta.url), 'utf8');
const fixturePath = new URL('./.test-fixtures/plugin/', import.meta.url).pathname;
fs.mkdirSync(fixturePath, {recursive:true});
php.mkdir(fixturePath);
php.writeFile(fixturePath + 'tfsc-custom-functions.php', plugin);
php.writeFile(fixturePath + 'tfsc-custom-stylesheet.css', 'body { color: black; }');
const code = `<?php
error_reporting(E_ALL);
set_error_handler(function($level,$message,$file,$line) { throw new Exception($message . ' at ' . $line); });
define('ABSPATH','/');
$actions=[];$filters=[];$shortcodes=[];$styles=[];$removed=[];
function add_action($hook,$callback,$priority=10){global $actions;$actions[$hook]=[$callback,$priority];}
function add_filter($hook,$callback,$priority=10){global $filters;$filters[$hook]=[$callback,$priority];}
function add_shortcode($name,$callback){global $shortcodes;$shortcodes[$name]=$callback;}
function plugin_dir_path($file){return dirname($file).'/';}
function plugin_dir_url($file){return 'https://example.test/wp-content/plugins/tfsc-custom-functions/';}
function wp_enqueue_style($handle,$url,$deps,$version,$media){global $styles;$styles[]=compact('handle','url','deps','version','media');}
function wp_deregister_style($handle){global $removed;$removed[]=$handle;}
function is_search(){global $search;return $search;}
function get_search_query(){return '<script>bad</script>&test';}
function esc_html($s){return htmlspecialchars($s,ENT_QUOTES,'UTF-8');}
function esc_url($s){return htmlspecialchars($s,ENT_QUOTES,'UTF-8');}
function content_url($s){return 'https://example.test/wp-content'.$s;}
function home_url($s){return 'https://example.test'.($s ? '/'.ltrim($s,'/') : '');}
class WP_Post { public $post_content; function __construct($text){$this->post_content=$text;} }
function get_post(){global $test_post;return $test_post;}
function wp_strip_all_tags($s){return strip_tags($s);}
function strip_shortcodes($s){return preg_replace('/\\[test[^\\]]*\\]/','',$s);}
require '${fixturePath}tfsc-custom-functions.php';
$checks=[];
function check($condition,$name){global $checks;if(!$condition){throw new Exception('FAIL: '.$name);}$checks[]=$name;}
check($actions['wp_enqueue_scripts'][1] === PHP_INT_MAX,'Styles retain final enqueue priority');
tfsc_custom_functions_enqueue_styles();
$first=end($styles);
check($first['handle']==='theme-override','Existing stylesheet handle preserved');
check(strpos($first['url'],'functions//')===false,'Stylesheet URL has no duplicate slash');
touch('${fixturePath}tfsc-custom-stylesheet.css',1700000000);clearstatcache();tfsc_custom_functions_enqueue_styles();
check(end($styles)['version']==='1700000000','CSS version follows file modification time');
touch('${fixturePath}tfsc-custom-stylesheet.css',1700000010);clearstatcache();tfsc_custom_functions_enqueue_styles();
check(end($styles)['version']==='1700000010','Next CSS save changes the version');
unlink('${fixturePath}tfsc-custom-stylesheet.css');clearstatcache();$count=count($styles);tfsc_custom_functions_enqueue_styles();
check(count($styles)===$count,'Missing CSS does not emit warnings or a broken link');
$search=false;check(tfsc_custom_functions_archive_title('Original')==='Original','Normal archive title unchanged');
$search=true;check(strpos(tfsc_custom_functions_archive_title('Original'),'<script>')===false,'Search text escaped');
$test_post=null;check(tfsc_custom_functions_reading_time()==='','No post is handled without warnings');
$test_post=new WP_Post(str_repeat('één ',199));check(tfsc_custom_functions_reading_time()==='Minder dan 1 minuut.','Accented words counted correctly below threshold');
$test_post=new WP_Post(str_repeat('één ',200));check(tfsc_custom_functions_reading_time()==='1 minuut','One minute uses singular');
$test_post=new WP_Post(str_repeat('woord ',400));check(tfsc_custom_functions_reading_time()==='2 minuten','Longer reading time preserved');
$test_post=new WP_Post('[test word word]'.str_repeat('woord ',199));check(tfsc_custom_functions_reading_time()==='Minder dan 1 minuut.','Shortcode syntax excluded from word count');
$wp=(object)['request'=>'vacatures'];check(tfsc_custom_functions_current_page_url()==='https://example.test/vacatures','Current URL shortcode remains compatible');
unset($wp);check(tfsc_custom_functions_current_page_url()==='https://example.test','Current URL handles absent request');
ob_start();tfsc_custom_functions_preload_fonts();$fonts=ob_get_clean();
check(strpos($fonts,'inter-tight-v9')!==false && strpos($fonts,'barlow-semi-condensed-v16')!==false && strpos($fonts,'inter-v12')===false,'Preloads reference current local fonts');
tfsc_custom_functions_remove_icon_fonts();check(count($removed)===3,'Existing SVG/icon-font policy preserved');
check(isset($shortcodes['rm_get_estimated_time_to_read']) && isset($shortcodes['current_page_url']),'Public shortcode names preserved');
echo json_encode(['php'=>PHP_VERSION,'passed'=>count($checks),'checks'=>$checks],JSON_PRETTY_PRINT);
`;
const result = await php.runStream({code});
const stdout = await result.stdoutText;
const stderr = await result.stderrText;
if (stderr || stdout.includes('Fatal error')) throw new Error(stderr + stdout);
console.log(stdout);
fs.writeFileSync(new URL('./php-validation.json', import.meta.url), stdout);
php.exit();
