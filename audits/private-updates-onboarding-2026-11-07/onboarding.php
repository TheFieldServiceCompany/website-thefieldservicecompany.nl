<?php
define('WP_ADMIN',true);define('DOING_AJAX',true);require '/wordpress/wp-load.php';
use TFSC\PrivateUpdates\Plugin;
$checks=[];function check($ok,$name){global $checks;if(!$ok)throw new Exception('FAILED: '.$name);$checks[]=$name;}
wp_set_current_user(1);rest_get_server();require_once ABSPATH.'wp-admin/includes/class-wp-upgrader.php';
$plan=json_decode(file_get_contents('/update-fixtures/release-plan.json'),true);$active=get_option('active_plugins');$auto=get_site_option('auto_update_plugins',[]);update_option('onboarding_preserved_setting',['keep'=>'unchanged'],false);
foreach($plan as $release){
 $bytes=file_get_contents('/update-fixtures/'.$release['filename']);check(hash('sha256',$bytes)===$release['sha256'],'Exact baseline ZIP checksum: '.$release['plugin']);$headers=Plugin::inspect($bytes,$release['plugin']);check(!is_wp_error($headers)&&$headers['version']===$release['version'],'Complete baseline package inspection: '.$release['plugin']);
 $request=new WP_REST_Request('POST','/tfsc-updates/v1/releases');$request->set_header('content-type','application/json');$request->set_body(wp_json_encode(['plugin'=>$release['plugin'],'archive'=>base64_encode($bytes),'sha256'=>$release['sha256'],'tested'=>'7.1.4','notes'=>'Synthetic isolated onboarding of the exact reviewed current package.']));check(rest_do_request($request)->get_status()===200,'Approve baseline through real REST route: '.$release['plugin']);
 $main=WP_PLUGIN_DIR.'/'.$release['plugin'];$source=file_get_contents($main);$older=preg_replace('/(^[ \t\/*#@]*Version:\s*)[^\r\n]+/mi','${1}0.0.0',$source,1,$changed);check($changed===1&&file_put_contents($main,$older)!==false,'Fixture-only older installed version created: '.$release['plugin']);
}
wp_clean_plugins_cache(false);$updates=get_site_transient('update_plugins');foreach($plan as $release)check(isset($updates->response[$release['plugin']])&&$updates->response[$release['plugin']]->new_version===$release['version'],'Baseline reaches native update offer: '.$release['plugin']);
$upgrader=new Plugin_Upgrader(new Automatic_Upgrader_Skin());$result=$upgrader->bulk_upgrade(array_column($plan,'plugin'),['clear_update_cache'=>false]);
foreach($plan as $release){
 $plugin=$release['plugin'];check(isset($result[$plugin])&&is_array($result[$plugin]),'Real native update installs complete baseline: '.$plugin);$zip=new ZipArchive();$zip->open('/update-fixtures/'.$release['filename']);$same=true;for($i=0;$i<$zip->numFiles;$i++){$name=$zip->getNameIndex($i);$actual=WP_PLUGIN_DIR.'/'.$name;$same=$same&&is_file($actual)&&hash_file('sha256',$actual)===hash('sha256',$zip->getFromIndex($i));}$zip->close();check($same,'All installed package files including assets match: '.$plugin);
}
wp_clean_plugins_cache(false);check(get_option('active_plugins')===$active,'All active plugin identities preserved after nine native updates');check(get_site_option('auto_update_plugins',[])===$auto,'Auto-update selection preserved after nine native updates');check(get_option('onboarding_preserved_setting')===['keep'=>'unchanged'],'Database setting preserved after nine native updates');foreach($plan as $release)check(!isset(get_site_transient('update_plugins')->response[$release['plugin']]),'Completed baseline is no longer offered: '.$release['plugin']);
echo json_encode(['test'=>'complete package onboarding and native updates','wordpress'=>$wp_version,'php'=>PHP_VERSION,'passed'=>count($checks),'native_plugins_updated'=>count($plan),'checks'=>$checks],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
