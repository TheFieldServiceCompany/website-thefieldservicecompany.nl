<?php
/** All modifications below target the disposable in-memory WordPress only. */
define('WP_ADMIN',true);define('DOING_AJAX',true);
require '/wordpress/wp-load.php';
require_once ABSPATH.'wp-admin/includes/upgrade.php';
$checks=[];$findings=[];
function audit_check($ok,$label){global $checks;if(!$ok)throw new Exception('FAILED: '.$label);$checks[]=$label;}
function invoke_private($class,$method,$object,...$args){$m=new ReflectionMethod($class,$method);return $m->invoke($object,...$args);}
class Audit_Die extends Exception {public $status;public function __construct($status){$this->status=$status;parent::__construct('Intercepted WordPress termination');}}
$handler=function(){return function($message,$title='',$args=[]){throw new Audit_Die(is_array($args)?($args['response']??(http_response_code()?:500)):(is_int($args)?$args:500));};};
add_filter('wp_die_ajax_handler',$handler);add_filter('wp_die_handler',$handler);
function intercepted($call){ob_start();$stopped=false;try{$call();$status=200;}catch(Audit_Die $e){$status=$e->status;$stopped=true;}finally{$body=ob_get_clean();}return [$status,$body,$stopped];}
wp_set_current_user(1);do_action('admin_init');
$roles=['anonymous'=>0];foreach(['subscriber','author','editor'] as $role)$roles[$role]=wp_insert_user(['user_login'=>'audit-'.$role,'user_pass'=>'fixture-only','user_email'=>$role.'@example.test','role'=>$role]);
$routes=[['GET','/tfsc/v1/schema'],['GET','/tfsc-crawler-log/v1/records'],['GET','/tfsc-cache-warmer/v1/status'],['POST','/tfsc-cache-warmer/v1/discover'],['POST','/tfsc-cache-warmer/v1/run'],['GET','/server-location-test-probe/v1/status'],['POST','/server-location-test-probe/v1/token'],['POST','/server-location-test-probe/v1/probe'],['GET','/tfsc-combo-manager/v1/state'],['POST','/tfsc-combo-manager/v1/scan'],['POST','/tfsc-combo-manager/v1/settings'],['POST','/tfsc-combo-manager/v1/decision'],['POST','/tfsc-combo-manager/v1/rollback']];
$server=rest_get_server();
foreach($roles as $role=>$id){wp_set_current_user($id);foreach($routes as [$method,$route]){$request=new WP_REST_Request($method,$route);$request->set_body_params(['url'=>'https://127.0.0.1/']);$response=rest_do_request($request);audit_check(in_array($response->get_status(),[401,403],true),$role.' denied '.$method.' '.$route);}}
wp_set_current_user(1);
$r=rest_do_request(new WP_REST_Request('GET','/tfsc/v1/schema'));audit_check($r->get_status()===200,'Administrator can use the real schema route');
audit_check(tfsc_schema_safe_scalar(['api_key'=>'fixture-secret','nested'=>['access_token'=>'fixture-token']])===['api_key'=>'[redacted]','nested'=>['access_token'=>'[redacted]']],'Schema inventory redacts nested snake-case secrets');
$core_options_before=hash('sha256',serialize(get_option('upc_settings',[])));
$hooks=['admin_post_upc_change','admin_post_tfsc_combo_manager','admin_post_tfsc_cache_warmer_save','admin_post_tfsc_cache_warmer_discover','admin_post_tfsc_cache_warmer_run','admin_post_tfsc_cache_warmer_rewarm_all','admin_post_tfsc_cache_warmer_purge','admin_post_tfsc_ai_log_delete_all','admin_post_tfsc_ai_log_delete_old','admin_post_tfsc_ai_log_save_settings','admin_post_tfsc_ai_log_export_csv','admin_post_tfsc_structured_data_confirm_open','admin_post_tfsc_structured_data_bulk_vacancies','admin_post_tfsc_structured_data_purge_audit_cache'];
foreach(['author'=>$roles['author'],'admin'=>1] as $role=>$id){wp_set_current_user($id);$_POST=$_GET=$_REQUEST=[];foreach($hooks as $hook){audit_check(has_action($hook)!==false,'Registered handler '.$hook.' for '.$role);[$status,$body,$stopped]=intercepted(function()use($hook){do_action($hook);});audit_check($stopped,$role.' cannot execute '.$hook.' without valid authorization/nonce');}}
audit_check($core_options_before===hash('sha256',serialize(get_option('upc_settings',[]))),'Rejected management requests preserve backup settings');
wp_set_current_user(1);$_POST=$_GET=$_REQUEST=[];
$mf=new TFSC_Media_Filters();[$status]=intercepted([$mf,'index_ajax']);audit_check($status===403,'Media indexing rejects missing nonce even for administrator');
$bad=['extension'=>"pdf') OR 1=1 --",'width_min'=>'1e999','size_max'=>['x'],'orientation'=>'landscape OR 1=1','alt'=>'present OR 1=1'];
$sanitized=TFSC_Media_Filters::sanitize($bad);audit_check(count(array_diff(array_keys($sanitized),['extension']))===0&&(!isset($sanitized['extension'])||preg_match('/^[a-z0-9,]+$/D',$sanitized['extension'])),'Media filter SQL/type payload reduced to harmless extension tokens');
$valid=TFSC_Media_Filters::sanitize(['extension'=>str_repeat('pdf,',1000),'width_max'=>'999999999','size_max'=>'999999999999']);audit_check($valid['extension']==='pdf'&&$valid['width_max']===1000000&&$valid['size_max']===1000000000,'Media filter list and numeric inputs bounded');
$unsafe=['http://example.test/','https://evil.test/','https://example.test.evil.test/','https://example.test@evil.test/','https://user:pass@example.test/','https://example.test:444/','file:///etc/passwd','gopher://example.test/','https://127.0.0.1/','https://[::1]/'];
foreach($unsafe as $url)audit_check(TFSC_Cache_Warmer_Discovery::normalize_url($url)===false,'Cache warmer refuses foreign/unsafe URL '.$url);
audit_check(TFSC_Cache_Warmer_Discovery::normalize_url('https://example.test/public/?paged=2')==='https://example.test/public/?paged=2','Cache warmer retains legitimate same-site URL');
$settings=TFSC_Cache_Warmer_Plugin::sanitize_settings(['batch_size'=>1000000,'run_budget_seconds'=>1000000,'origin_ip'=>'127.0.0.1','request_mode'=>'origin_edge']);audit_check($settings['batch_size']===10&&$settings['run_budget_seconds']===120&&$settings['origin_ip']===''&&$settings['request_mode']==='edge','Cache warmer clamps work and rejects private origin IP');
$probe=new SLTP_Probe(['example.test']);foreach($unsafe as $url){if($url==='https://example.test/public/?paged=2')continue;audit_check(is_wp_error($probe->validate_url($url)),'Probe rejects unsafe target '.$url);}
$token='sltp_'.str_repeat('a',64);update_option(SLTP_Plugin::OPTION_TOKEN_HASH,hash('sha256',$token),false);
$a=new WP_REST_Request('POST','/server-location-test-probe/v1/mcp');$a->set_header('authorization','Bearer '.$token);
$b=new WP_REST_Request('POST','/server-location-test-probe/v1/mcp');$b->set_header('authorization','bEaReR '.$token);
audit_check(SLTP_Plugin::request_has_valid_token($a)&&SLTP_Plugin::request_has_valid_token($b),'Equivalent Bearer headers authenticate identically');
for($i=0;$i<30;$i++)audit_check(SLTP_Plugin::consume_rate_limit($a),'Probe permits request within limit '.$i);
audit_check(!SLTP_Plugin::consume_rate_limit($a),'Probe blocks the 31st identical-header request');
$bypass=SLTP_Plugin::consume_rate_limit($b);audit_check(!$bypass,'Equivalent Bearer case cannot bypass exhausted probe budget');$findings['probe_rate_limit_header_bypass']=$bypass;
$c=new WP_REST_Request('POST','/server-location-test-probe/v1/mcp');$c->set_header('x-tfsc-probe-key',' '.$token.' ');audit_check(!SLTP_Plugin::consume_rate_limit($c),'Alternate header and whitespace share exhausted probe budget');
$r=new WP_REST_Request('POST','/server-location-test-probe/v1/mcp');$r->set_header('authorization','Bearer invalid');$r->set_body('{"jsonrpc":"2.0","id":1,"method":"tools/list"}');wp_set_current_user(0);audit_check(rest_do_request($r)->get_status()===401,'MCP rejects invalid token');
$r->set_header('authorization','Bearer '.$token);$r->set_body(str_repeat(' ',65537));audit_check(rest_do_request($r)->get_status()===413,'MCP rejects oversized body for valid token');
$targets=new TFSC\FormBridge\TargetRegistry();$maps=new TFSC\FormBridge\MappingRepository($targets);$diagrepo=new TFSC\FormBridge\DiagnosticRepository();$diag=new TFSC\FormBridge\ElementorDiagnostics($maps,$diagrepo);
update_option(TFSC\FormBridge\MappingRepository::OPTION_NAME,['elementor:123:fixture'=>['enabled'=>true,'form_type'=>'candidate','field_map'=>[]]],false);
$_POST=$_REQUEST=[];[$status]=intercepted([$diag,'record_client_failure']);audit_check($status===403,'Anonymous client diagnostic rejects missing nonce');
$uuid=wp_generate_uuid4();$diagrepo->start(['request_uuid'=>$uuid,'form_key'=>'elementor:123:fixture','builder'=>'elementor','source'=>'server','status'=>'delivered','stage'=>'complete']);
$_POST=['post_id'=>123,'form_id'=>'fixture','nonce'=>wp_create_nonce('tfsc_form_bridge_diagnostic'),'request_uuid'=>$uuid,'http_status'=>503,'response_text'=>'fixture-poisoned-response'];$_REQUEST=$_POST;
[$status,$body]=intercepted([$diag,'record_client_failure']);global $wpdb;$row=$wpdb->get_row($wpdb->prepare("SELECT source,status,stage,response_body FROM {$wpdb->prefix}tfsc_form_bridge_diagnostics WHERE request_uuid=%s",$uuid),ARRAY_A);
$findings['browser_can_overwrite_server_diagnostic']=($row['source']==='server'&&$row['status']==='failed'&&$row['response_body']==='fixture-poisoned-response');
audit_check(!$findings['browser_can_overwrite_server_diagnostic']&&$row['status']==='delivered','Browser cannot overwrite a server diagnostic');
$browser=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}tfsc_form_bridge_diagnostics WHERE source='browser' ORDER BY id DESC LIMIT 1",ARRAY_A);$context=json_decode($browser['request_data'],true);
audit_check($browser['request_uuid']!==$uuid&&$context['correlation_request_uuid']===$uuid,'Browser event gets a new identity and retains correlation only as data');
$diag->mark_outbox_enqueued($uuid,wp_generate_uuid4(),999,'pending');
audit_check($wpdb->get_var($wpdb->prepare("SELECT outbox_id FROM {$wpdb->prefix}tfsc_form_bridge_diagnostics WHERE request_uuid=%s",$uuid))===null,'An outbox notification without a server-owned request cannot select another diagnostic');
$_POST=['post_id'=>123,'form_id'=>'fixture','tfsc_diagnostic_request_id'=>$uuid];$_REQUEST=$_POST;$_FILES=[];$level=ob_get_level();$diag->begin_request();$new_id=$_POST['tfsc_diagnostic_request_id'];while(ob_get_level()>$level)ob_end_clean();
audit_check($new_id!==$uuid&&$wpdb->get_var($wpdb->prepare("SELECT status FROM {$wpdb->prefix}tfsc_form_bridge_diagnostics WHERE request_uuid=%s",$uuid))==='delivered','A forged request correlation ID cannot select an existing server diagnostic');
$_POST=['nonce'=>wp_create_nonce('tfsc_form_bridge_diagnostic'),'response_text'=>str_repeat('x',70000)];$_REQUEST=$_POST;[$status]=intercepted([$diag,'record_client_failure']);audit_check($status===413,'Anonymous diagnostic rejects payload above 64KiB');
audit_check(TFSC\FormBridge\ElementorDiagnostics::client_rate_limit_key('192.0.2.10','elementor:123:fixture')===TFSC\FormBridge\ElementorDiagnostics::client_rate_limit_key('192.0.2.10','elementor:123:fixture'),'Diagnostic limiter has stable server-address identity');
$uploads=wp_upload_dir();wp_mkdir_p($uploads['basedir']);file_put_contents('/outside-secret.pdf','%PDF-1.4 fixture');file_put_contents($uploads['basedir'].'/bad.php','<?php echo "fixture";');file_put_contents($uploads['basedir'].'/good.pdf','%PDF-1.4\nfixture document\n%%EOF');
$client=new TFSC\FormBridge\BackblazeClient();
foreach([['local_path'=>'/outside-secret.pdf','extension'=>'pdf','original_filename'=>'outside.pdf'],['local_path'=>$uploads['basedir'].'/../../outside-secret.pdf','extension'=>'pdf','original_filename'=>'outside.pdf'],['local_url'=>'https://evil.test/good.pdf','extension'=>'pdf','original_filename'=>'outside.pdf'],['local_path'=>$uploads['basedir'].'/bad.php','extension'=>'php','original_filename'=>'bad.php']] as $case)audit_check(is_wp_error(invoke_private(get_class($client),'validate_local_document',$client,$case)),'Document boundary rejects '.json_encode($case));
$good=invoke_private(get_class($client),'validate_local_document',$client,['local_path'=>$uploads['basedir'].'/good.pdf','extension'=>'pdf','original_filename'=>'good.pdf']);audit_check(is_wp_error($good)&&$good->get_error_code()==='document_provenance_invalid','A valid existing PDF without upload proof is rejected');
$worker=new TFSC\FormBridge\DeliveryWorker(new TFSC\FormBridge\OutboxRepository());audit_check(!invoke_private(get_class($worker),'delete_private_staging_file',$worker,['local_path'=>$uploads['basedir'].'/good.pdf'])&&file_exists($uploads['basedir'].'/good.pdf'),'Worker cannot delete a non-staging upload');
foreach(['../backup_x_abcdef123456-db.gz','backup_x_abcdef123456-db.gz/evil','backup_x_abcdef123456-db.gz.php',"backup_x_abcdef123456-db\0.gz",'backup_x_abcdef123456-db\\evil.gz'] as $name)audit_check(!Updraft_PCloud_Copy\valid_archive_name($name,'abcdef123456'),'Backup archive rejects traversal or extension payload');
audit_check(Updraft_PCloud_Copy\valid_archive_name('backup_fixture_abcdef123456-db.gz','abcdef123456'),'Backup archive accepts valid name');
try{Updraft_PCloud_Copy\token_from(['sealed'=>base64_encode(str_repeat('x',60))]);$rejected=false;}catch(RuntimeException $e){$rejected=true;}audit_check($rejected,'Corrupt encrypted pCloud token fails closed');
$iv=random_bytes(12);$cipher=openssl_encrypt('fixture-token','aes-256-gcm',Updraft_PCloud_Copy\secret_key(),OPENSSL_RAW_DATA,$iv,$tag);audit_check(Updraft_PCloud_Copy\token_from(['sealed'=>base64_encode($iv.$tag.$cipher)])==='fixture-token','pCloud token encryption round trip succeeds');
$tag[0]=chr(ord($tag[0])^1);try{Updraft_PCloud_Copy\token_from(['sealed'=>base64_encode($iv.$tag.$cipher)]);$rejected=false;}catch(RuntimeException $e){$rejected=true;}audit_check($rejected,'Tampered pCloud GCM tag rejected');
$builder=new TFSC_Custom_Schema();$settings=['custom_schemas'=>[['enabled'=>true,'type'=>'Thing','placement'=>'sitewide','properties'=>[['name'=>'description','source'=>'fixed','value'=>'</script><script>alert(1)</script>']]]]];
$graph=$builder->build_for_current_request($settings);$encoded=wp_json_encode($graph,JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);audit_check(stripos($encoded,'</script')===false&&json_decode($encoded,true)!==null,'Structured Data hostile string cannot close JSON-LD script');
audit_check(TFSC_AI_Crawler_Log::sanitize_datetime_cursor("2026-01-01 00:00:00' OR 1=1 --")==='','Crawler cursor rejects SQL payload');
audit_check(TFSC_AI_Crawler_Log::rate_limit_key('192.0.2.10','GPTBot')!==TFSC_AI_Crawler_Log::rate_limit_key('192.0.2.11','GPTBot'),'Crawler limiter uses actual remote identity');
foreach(['=1+1','+cmd','-cmd','@SUM(1)'] as $cell)audit_check(str_starts_with(invoke_private('TFSC_AI_Crawler_Log','csv_safe_value',null,$cell),"'"),'Crawler CSV escapes spreadsheet formula prefix');
$_POST=$_GET=$_REQUEST=[];
audit_check(!TFSC\FormBridge\RequestBudget::payload_allowed(str_repeat('x',70000)),'Oversized diagnostic metadata rejected before JSON encoding');
$deep='x';for($n=0;$n<20;$n++)$deep=[$deep];audit_check(!TFSC\FormBridge\RequestBudget::payload_allowed($deep),'Deep diagnostic nesting rejected within bounded work');
audit_check(TFSC\FormBridge\RequestBudget::payload_allowed([['post_id'=>1],['cv'=>[['size'=>20000000,'name'=>'file.pdf']]]]),'Multipart file size is metadata and does not block legitimate upload logging');
$probe_budget=new SLTP_Probe([]);$start=new ReflectionMethod(get_class($probe_budget),'begin_budget');$chunk=new ReflectionMethod(get_class($probe_budget),'accept_chunk');$start->invoke($probe_budget);
$bytes=new ReflectionProperty(get_class($probe_budget),'remaining_bytes');$bytes->setValue($probe_budget,32);audit_check($chunk->invoke($probe_budget,str_repeat('x',16))===16&&$chunk->invoke($probe_budget,str_repeat('x',17))===0,'Probe stops a download chunk beyond remaining total byte budget');
$deadline=new ReflectionProperty(get_class($probe_budget),'deadline');$deadline->setValue($probe_budget,microtime(true)-1);audit_check($chunk->invoke($probe_budget,'x')===0,'Probe stops after total work deadline');
get_option(TFSC\FormBridge\RequestBudget::LOCK,false);$lock_value=wp_json_encode(['token'=>'another-worker','at'=>time()]);
$wpdb->query($wpdb->prepare("INSERT IGNORE INTO {$wpdb->options} (option_name,option_value,autoload) VALUES (%s,%s,'no')",TFSC\FormBridge\RequestBudget::LOCK,$lock_value));
audit_check(!TFSC\FormBridge\RequestBudget::consume('new-fixture',10,600),'Database lock refuses a contender even if WordPress cached the option as absent');
audit_check($wpdb->get_var($wpdb->prepare("SELECT option_value FROM {$wpdb->options} WHERE option_name=%s",TFSC\FormBridge\RequestBudget::LOCK))===$lock_value,'A contender cannot overwrite the existing lock owner');
$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name=%s",TFSC\FormBridge\RequestBudget::LOCK));wp_cache_delete(TFSC\FormBridge\RequestBudget::LOCK,'options');
echo json_encode(['test'=>'isolated security attacks','wordpress'=>$wp_version,'php'=>PHP_VERSION,'passed'=>count($checks),'checks'=>$checks,'confirmed_findings'=>$findings],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
