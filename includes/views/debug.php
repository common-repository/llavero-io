<?php


global $wpdb;
global $wp_version;


$oicill_tema_actual = wp_get_theme();
$oicill_usuario_actual = wp_get_current_user();
$oicill_uploaddir = wp_get_upload_dir();

    $oicill_info = array(
            	'Versión WordPress' => $wp_version,
            	'Url Sitio' => site_url(),
            	'Home Path' => get_home_path(),
		'Upload dir' => $oicill_uploaddir['basedir'],
		'Nombre Usuario' => $oicill_usuario_actual->display_name,
		'email Usuario' => $oicill_usuario_actual->user_email,
            	'Versión PHP' => PHP_VERSION,
      		'Servidor Web' => (!empty($_SERVER["SERVER_SOFTWARE"])) ? $_SERVER["SERVER_SOFTWARE"] : 'N/A',
            	'Versión MySQL' => $wpdb->db_version(),
            	'Multisitio' => is_multisite() ? 'yes' : 'no',
            	'WP_MEMORY_LIMIT' => WP_MEMORY_LIMIT,
            	'WP_MAX_MEMORY_LIMIT' => WP_MAX_MEMORY_LIMIT,
            	'PHP memory_limit' => ini_get('memory_limit'),
      		'PHP max_execution_time' => ini_get('max_execution_time'),
      		'PHP upload_max_filesize' => ini_get('upload_max_filesize'),
      		'PHP post_max_size' => ini_get('post_max_size'),
      		'WP_DEBUG' => WP_DEBUG,
      		'WordPress Idioma' => get_locale(),
		'Tema en uso' => $oicill_tema_actual->get('Name'). ' (version '.$oicill_tema_actual->get('Version').')',
      		'Plugins Activos' => join(", ", get_option('active_plugins'))
                );
    ?>
    <br />
    <h1>Información de Debug:</h1>
    <br />
        <textarea rows="30" cols="150" ><?php foreach($oicill_info as $key => $val){
		echo $key ." = ". $val ."\n";
	}
	?>
        </textarea>
	<p>Nuestros técnicos te pueden solicitar esta información para revisar problemas con el plugin.</p>


