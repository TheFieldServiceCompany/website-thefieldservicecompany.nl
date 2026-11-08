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
if(!$valid||$handler->errors||$record->get('files')!==[]||!$accepted)throw new \Exception('Provenance reproduction did not reach the expected boundary.');
echo json_encode(['test'=>'upload provenance','wordpress'=>$wp_version,'php'=>PHP_VERSION,'form_validation_passes'=>$valid,'process_errors'=>$handler->errors,'new_uploaded_file_count'=>count($record->get('files')),'existing_private_upload_accepted_for_new_document'=>$accepted,'network_upload_performed'=>false,'scope'=>'Actual installed Form_Record and Upload classes with minimal Field_Base/Utils wiring; fixture mapping and file; no complete Elementor request, CAPTCHA, CRM or B2 integration'],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
$_POST=$_FILES=[];
}
