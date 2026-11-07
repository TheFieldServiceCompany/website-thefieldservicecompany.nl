<?php
/** Real installed Form_Record/Upload code; minimal base/utility wiring only. */
namespace ElementorPro\Core { class Utils {public static function _unstable_get_super_global_value($data,$key){return $data[$key]??[];}} }
namespace ElementorPro\Modules\Forms\Fields { class Field_Base {public function __construct(){}} }
namespace {
define('WP_ADMIN',true);require '/wordpress/wp-load.php';wp_set_current_user(0);
require '/audit-dependencies/elementor-pro/modules/forms/classes/form-record.php';
require '/audit-dependencies/elementor-pro/modules/forms/classes/ajax-handler.php';
require '/audit-dependencies/elementor-pro/modules/forms/fields/upload.php';
$upload=new \ElementorPro\Modules\Forms\Fields\Upload();
add_action('elementor_pro/forms/validation/upload',[$upload,'validation'],10,3);
add_action('elementor_pro/forms/process/upload',[$upload,'process_field'],10,3);
$targets=new \TFSC\FormBridge\TargetRegistry();$maps=new \TFSC\FormBridge\MappingRepository($targets);
$maps->save('elementor:321:provenance','open_registration',true,['email'=>'person.email','cv'=>'candidate.cv_files'],['create_candidate'=>true]);
$uploads=wp_upload_dir();wp_mkdir_p($uploads['basedir'].'/tfsc-form-bridge');
$path=$uploads['basedir'].'/tfsc-form-bridge/other-document.pdf';file_put_contents($path,"%PDF-1.4\nfixture owned by another submission\n%%EOF");chmod($path,0600);
$forged_url=$uploads['baseurl'].'/tfsc-form-bridge/other-document.pdf';
$form=['widgetType'=>'form','settings'=>['form_name'=>'Provenance fixture','form_metadata'=>[],'form_fields'=>[['custom_id'=>'email','field_type'=>'email','field_label'=>'Email','required'=>true],['custom_id'=>'cv','field_type'=>'upload','field_label'=>'CV','required'=>false,'file_types'=>'pdf','attachment_type'=>'link']]]];
$record=new \ElementorPro\Modules\Forms\Classes\Form_Record(['email'=>'synthetic@example.test','cv'=>$forged_url],$form);
$_POST=['post_id'=>321,'form_id'=>'provenance'];
$_FILES=['form_fields'=>['name'=>['cv'=>''],'type'=>['cv'=>''],'tmp_name'=>['cv'=>''],'error'=>['cv'=>UPLOAD_ERR_NO_FILE],'size'=>['cv'=>0]]];
$handler=(new \ReflectionClass(\ElementorPro\Modules\Forms\Classes\Ajax_Handler::class))->newInstanceWithoutConstructor();
$valid=$record->validate($handler);$record->process_fields($handler);
do_action('elementor_pro/forms/new_record',$record,$handler);
global $wpdb;$payload=$wpdb->get_var("SELECT payload FROM {$wpdb->prefix}tfsc_form_bridge_outbox ORDER BY id DESC LIMIT 1");$envelope=json_decode($payload,true);
$documents=\TFSC\FormBridge\DocumentManifest::from_envelope($envelope);$accepted=false;
if($documents){$client=new \TFSC\FormBridge\BackblazeClient();$method=new \ReflectionMethod(get_class($client),'validate_local_document');$result=$method->invoke($client,$documents[0]);$accepted=is_array($result)&&$result['path']===realpath($path);}
if(!$valid||$handler->errors||$record->get('files')!==[]||$accepted||$documents!==[])throw new \Exception('Unverified document reference was not blocked.');
$checks=['Forged existing document discarded without blocking the form'];
function upload_check($ok,$name){global $checks;if(!$ok)throw new \Exception('FAILED: '.$name);$checks[]=$name;}
$normal_record=new \ElementorPro\Modules\Forms\Classes\Form_Record(['email'=>'synthetic@example.test','cv'=>$forged_url],$form);
$tmp=$uploads['basedir'].'/fixture-incoming.pdf';file_put_contents($tmp,"%PDF-1.4\nnew verified fixture\n%%EOF");
$_FILES=['form_fields'=>['cv'=>[['name'=>'new-document.pdf','tmp_name'=>$tmp,'error'=>UPLOAD_ERR_OK,'size'=>filesize($tmp)]]]];
$adapter=new \TFSC\FormBridge\ElementorProAdapter($maps,new \TFSC\FormBridge\SubmissionNormalizer($targets),new \TFSC\FormBridge\OutboxRepository(),['is_uploaded_file'=>function($p){return is_file($p);}]);
upload_check($adapter->stage_mapped_uploads($normal_record->get('fields')['cv'],$normal_record,$handler),'A new upload is staged through the real adapter (fixture upload detector only)');
$normal_record->process_fields($handler);do_action('elementor_pro/forms/new_record',$normal_record,$handler);
$normal=json_decode($wpdb->get_var("SELECT payload FROM {$wpdb->prefix}tfsc_form_bridge_outbox ORDER BY id DESC LIMIT 1"),true);
$normal_docs=\TFSC\FormBridge\DocumentManifest::from_envelope($normal);
upload_check(count($normal_docs)===1&&\TFSC\FormBridge\UploadProvenance::verify_document($normal_docs[0]),'New document carries a proof bound to this submission');
$client=new \TFSC\FormBridge\BackblazeClient();$method=new \ReflectionMethod(get_class($client),'validate_local_document');
upload_check(is_array($method->invoke($client,$normal_docs[0])),'Proven document passes normal path, MIME and size checks');
foreach(['submission_id'=>wp_generate_uuid4(),'local_path'=>$path,'original_filename'=>'stolen.pdf','source_field'=>'other','upload_proof'=>str_repeat('0',64)] as $key=>$value){$changed=$normal_docs[0];$changed[$key]=$value;upload_check(!\TFSC\FormBridge\UploadProvenance::verify_document($changed),'Tampering rejected: '.$key);}
$unsigned=$normal_docs[0];unset($unsigned['upload_proof']);upload_check(is_wp_error($method->invoke($client,$unsigned)),'Unsigned legacy document fails closed');
$key=get_option(\TFSC\FormBridge\UploadProvenance::KEY_OPTION);delete_option(\TFSC\FormBridge\UploadProvenance::KEY_OPTION);
upload_check(!\TFSC\FormBridge\UploadProvenance::verify_document($normal_docs[0])&&get_option(\TFSC\FormBridge\UploadProvenance::KEY_OPTION,false)===false,'Missing key neither authorizes upload nor creates a key on reads');update_option(\TFSC\FormBridge\UploadProvenance::KEY_OPTION,$key,false);
$remote=\TFSC\FormBridge\EnvelopeSanitizer::for_receiver($normal);upload_check(strpos(wp_json_encode($remote),'upload_proof')===false&&strpos(wp_json_encode($remote),'local_path')===false,'Receiver envelope excludes proof and local paths');
define('TFSC_B2_APPLICATION_KEY_ID','fixture');define('TFSC_B2_APPLICATION_KEY','fixture');define('TFSC_B2_BUCKET_ID','fixture-bucket');
define('TFSC_FORM_BRIDGE_ENDPOINT','https://example.test/receiver');define('TFSC_FORM_BRIDGE_HMAC_SECRET',str_repeat('fixture-',8));
$http_calls=0;
add_filter('pre_http_request',function($pre,$args,$url)use(&$http_calls){$http_calls++;
 if(strpos($url,'b2_authorize_account')!==false)$body=['authorizationToken'=>'fixture','apiInfo'=>['storageApi'=>['apiUrl'=>'https://example.test/b2','allowed'=>['capabilities'=>['writeFiles'],'buckets'=>[['id'=>'fixture-bucket']],'namePrefix'=>'documents/']]]];
 elseif(strpos($url,'b2_get_upload_url')!==false)$body=['uploadUrl'=>'https://example.test/b2-upload','authorizationToken'=>'fixture'];
 elseif($url==='https://example.test/b2-upload')$body=['fileId'=>'fixture-id','fileName'=>rawurldecode($args['headers']['X-Bz-File-Name']),'contentSha1'=>sha1($args['body'])];
 elseif($url==='https://example.test/receiver')$body=['ok'=>true,'candidate_profile_id'=>'00000000-0000-4000-8000-000000000001'];
 else return new \WP_Error('unexpected_test_http','Unexpected fixture request');
 return ['response'=>['code'=>200],'headers'=>[],'body'=>wp_json_encode($body)];
},9999,3);
upload_check(is_array($client->upload($normal_docs[0]))&&$http_calls===3,'Normal B2 client upload completes against three local mocked HTTP responses');
$old_calls=$http_calls;upload_check(is_wp_error($client->upload($unsigned))&&$http_calls===$old_calls,'Unsigned upload cannot initiate a network request');
$worker=new \TFSC\FormBridge\DeliveryWorker(new \TFSC\FormBridge\OutboxRepository());$worker->run();
upload_check((int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}tfsc_form_bridge_outbox WHERE status='delivered'")===2,'Optional no-upload form and genuine new document both deliver through the real worker');
$state=json_decode($wpdb->get_var("SELECT document_state FROM {$wpdb->prefix}tfsc_form_bridge_outbox ORDER BY id DESC LIMIT 1"),true);
upload_check($state[0]['status']==='registered'&&$state[0]['local_path']==='','Registered document is confirmed and private staging cleaned');
$legacy=$normal;$legacy['submission_id']=wp_generate_uuid4();foreach($legacy['files'] as &$list)foreach($list as &$f)unset($f['upload_proof']);unset($list,$f);
$outbox=new \TFSC\FormBridge\OutboxRepository();$legacy_row=$outbox->enqueue($legacy);$worker->run();
upload_check($wpdb->get_var($wpdb->prepare("SELECT status FROM {$wpdb->prefix}tfsc_form_bridge_outbox WHERE id=%d",$legacy_row['id']))==='review','Unsigned queued legacy document is retained for review rather than uploaded or discarded');
echo json_encode(['test'=>'fixed normal upload and proof','passed'=>count($checks),'checks'=>$checks],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
echo json_encode(['test'=>'upload provenance','wordpress'=>$wp_version,'php'=>PHP_VERSION,'form_validation_passes'=>$valid,'process_errors'=>$handler->errors,'new_uploaded_file_count'=>count($record->get('files')),'existing_private_upload_accepted_for_new_document'=>$accepted,'network_upload_performed'=>false,'scope'=>'Actual installed Form_Record and Upload classes with minimal Field_Base/Utils wiring; fixture mapping and file; no complete Elementor request, CAPTCHA, CRM or B2 integration'],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
$_POST=$_FILES=[];
}
