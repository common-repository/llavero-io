<?php
/**
* Plugin Name: Llavero.io
* Plugin URI: https://llavero.io/
* Description: Protege los accesos de los usuarios de WordPress cerrándolos cuando no se están usando para evitar que alguién pueda entrar si te roban los accesos.
* Version: 0.1.4
* Author: Webempresa.com
* Author URI: https://www.webempresa.com/
**/

require_once("includes/constants.php");
require_once("includes/helper.php");
require_once("includes/views.php");



add_action( 'admin_menu', 'cill_ciberllavero_main' );


function cill_ciberllavero_main()
{
    add_menu_page(
        'Llavero.io - Tu gestor de accesos para WordPress', // Title of the page
        'llavero.io', // Text to show on the menu link
        'manage_options', // Capability requirement to see the link
        'cill-llavero',
        'cill_view_main', // The 'slug' - file to display when clicking the link
        'dashicons-admin-network',
        ++$GLOBALS['_wp_last_object_menu']
    );
}


function cill_admin_enqueue_javascript($hook) {
    if ('profile.php' !== $hook && 'cill-llavero' !== $_GET["page"] ) {
        return;
    }

    wp_enqueue_script('cill_custom_script', 'https://unpkg.com/node-forge@0.7.0/dist/forge.min.js' );
    wp_enqueue_script('cill_custom2_script', plugin_dir_url(__FILE__) . '/includes/js/cilib.js');
}

add_action('admin_enqueue_scripts', 'cill_admin_enqueue_javascript');



/**
*
* User Profiles section of Llavero.io for users
*
*/

function cill_additional_profile_fields( $user ) {

   //  update_usermeta(  $user->ID , 'ciberllaverouserkey',  "" );

    $malConfigurado = 0;

    $ciberllaverouserkey = sanitize_text_field(get_the_author_meta( 'ciberllaverouserkey', $user->ID ) ) ;

    $cill_appid = sanitize_text_field(get_option("cill_appid"));
	if ( !CiberLlaveroHelper::validateAppID($cill_appid) ) {
		$malConfigurado = 1;
	}

    $tieneCiberLlavero = CiberLlaveroHelper::userHasCiberLlavero($user->ID ); 

    $resultado = CiberLlaveroHelper::getCiberLlaveroStats( );
    
    if ( $resultado[0]["apistatus"] == "error" ) {
        $malConfigurado = 1;
    }


    ?>

    <h3>Llavero.io</h3>

	<?php if ($malConfigurado == 1 ) { ?>
		<h4>Llavero.io está instalado, pero no está configurado correctamente. Consulta con el administrador de WordPress.</h4>
	<?php } ?>

    <?php if ($malConfigurado == 0 ) { 

            $mostrarLoginCiber = 0;
            // Comprobar si $ciberllaverouserkey contiene cill_appid
			// ciberllaverouserkey
            if (strpos($ciberllaverouserkey, $cill_appid . ":") === false) {
                $mostrarLoginCiber = 1;
            }

    ?>


        <div id="cill_messages" >
        </div>
        <?php if ( $mostrarLoginCiber == 1) { ?>
        <fieldset style="border: 0; padding: 5px; background: white; border-radius: 5px; margin-top:10px; " >

            <table class="form-table" id="llaverologinform" >
                <tr>
                    <th><label for="ciberllavelogin_user">Usuario de Llavero.io</label></th>
                    <td>
                            <input name="ciberllavelogin_user" id="ciberllavelogin_user" type="text" style="width:350px;font-weight:bold;" value="" /> 
                    </td>
                </tr>
                <tr>
                    <th><label for="ciberllavelogin_password">Contraseña de Llavero.io</label></th>
                    <td>
                            <input name="ciberllavelogin_password" id="ciberllavelogin_password" type="password" style="width:350px;font-weight:bold;" value="" /> 
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:left;" >
                        <input type="hidden" name="empezarcill" value="1" />
                        <input type="submit" name="submit" class="button button-primary" value="Login en Llavero.io"  onclick="return cilib.processLogin();"  >
                    </td>

                </tr>
            </table>

        </fieldset>
        <?php } ?>

        <table class="form-table">
            <?php



             if ( ( $tieneCiberLlavero || trim($ciberllaverouserkey) != "" ) &&  $mostrarLoginCiber == 0 ){
                 $llaveDeUsuarioArray = explode( ":" , $ciberllaverouserkey );
                 $llaveDeUsuario =  $llaveDeUsuarioArray[1];
            ?>


                    <?php
                        if ($cill_appid != ""){
                    ?>
                    <tr>
                        <th> <label for="cill_appid">ApplicationID</label> </th>
                        <td> <input name="cill_appid" id="cill_appid" type="text" style="width:350px;font-weight:bold;" disabled value="<?php echo esc_attr( $cill_appid) ; ?>" /> </td> <!-- Sanitized and validated an begining of function -->
                    </tr>
                    <?php
                        }
                    ?>

                    <?php
                        if ($llaveDeUsuario != ""){
                    ?>
                    <tr>
                        <th> <label for="cill_appid">ID Usuario</label> </th>
                        <td> <input name="cill_appid" id="cill_appid" type="text" style="width:350px;font-weight:bold;" disabled value="<?php echo esc_attr( $llaveDeUsuario ) ; ?>" /> </td> <!-- Sanitized and validated an begining of function -->
                    </tr>
                    <?php
                        }
                    ?>

                <tr>
                    <th><label for="ciberllaverouserkey"> ¿Quieres <u>dejar</u> de usar Llavero.io en tu cuenta ?</label> </th>
                    <td> <input onclick="return cilib.desvincularCuenta('<?php echo esc_js( $cill_appid ); ?>' , '<?php echo esc_js( $llaveDeUsuario )  ; ?>' );" type="submit" name="cill_dejardeusar" style="background-color:#ca4a1f; border-color:#ca4a1f;text-shadow:none;box-shadow:none;" class='button button-primary' value="Dejar de usar Llavero.io" /> </td> 
                </tr>

            <?php
             }
            ?>

        </table>
        <?php
	    //  END Mal configurado 
    }

}

add_action( 'show_user_profile', 'cill_additional_profile_fields' );
add_action( 'edit_user_profile', 'cill_additional_profile_fields' );


function cill_save_profile_fields( $user_id ) {

		if ( ! is_int( $user_id ) ){
			return false;
		}


        $cill_appid = sanitize_text_field(  get_option("cill_appid") ) ;
 	   	if ( !CiberLlaveroHelper::validateAppID($cill_appid) ) {
    	   	return false;
    	}

        if ($cill_appid  == ""){
            return false;
        }

        if ( ! current_user_can( 'edit_user', $user_id ) ) {
            return false;
        }
    
        if ( !isset( $_POST["empezarcill"]  ) && !isset( $_POST["cill_dejardeusar"] ) ) {
            return false;
        }

                
        if ( isset( $_POST["empezarcill"] ) && $_POST["empezarcill"] == 1 ) {

			// user_id is validated at begining of function
            $userObj = get_user_by("id",$user_id);
            $username = $userObj->data->user_login ;


            $resultado = CiberLlaveroHelper::createCiberLlaveroUser( $username );           
            if ($resultado[0]["apistatus"] == "success") {

                update_usermeta( $user_id, 'ciberllaverouserkey', $cill_appid . ":" . $resultado[1]->result->Actkey );               
            }

        }

        if ( isset($_POST["cill_dejardeusar"]) ){

            // $userObj = get_user_by("id",$user_id);
            // $username = $userObj->data->user_login ;

            update_usermeta( $user_id, 'ciberllaverouserkey', "" );
            
        }


    }
    

add_action( 'personal_options_update', 'cill_save_profile_fields' );
add_action( 'edit_user_profile_update', 'cill_save_profile_fields' );


/**
*
* Verificación de autenticacion. Determina si se puede hacer login o no en base al resultado del API de Llavero.io
*
*/

add_filter('wp_authenticate_user', 'cill_comprueba_login_ciberllavero', 10, 3);
function cill_comprueba_login_ciberllavero($user, $password) {
 

    $cill_appid = sanitize_text_field(get_option("cill_appid"));
    if ( !CiberLlaveroHelper::validateAppID($cill_appid) ) {
       	return $user;
    }

    $ciberllaverouserkey = sanitize_text_field( get_the_author_meta( 'ciberllaverouserkey', $user->ID ) ) ;
    if ( !CiberLlaveroHelper::validateUserkey($ciberllaverouserkey) ) {
        return $user;
    }

    // Comprobamos si el AppID almacenado en la MetaInfo del user es el correcto
    if (strpos($ciberllaverouserkey, $cill_appid) === false) {
            // El AppID no es correcto
            return $user;
    }


    $partes = explode(":", $ciberllaverouserkey);
    

    $res2 = CiberLlaveroHelper::getUserStatusFromActKey($partes[1] );



    $now = new DateTime(null, new DateTimeZone('Europe/Madrid'));
    $now->setTimezone(new DateTimeZone('Europe/London'));   
	// Obtenemos la zona horaria de London GMT +0 para hacer calculos de zona horaria

        if ( $res2[0]["apistatus"] == "success" ) {
            if ( isset($res2[1]->result) ){

                $optionsRaw = $res2[1]->result->Options ;
                $opcionesArray = explode("\n", $optionsRaw);

                $tieneAutoClose = 0;
                $horaInicio = 0;
                $minInicio  = 0;
                $horaFin    = 0;
                $minFin     = 0;

                $horaInicioValue = "00:00";
                $horaFinValue = "00:00";

                for($i = 0; $i < count($opcionesArray); $i++) {
                    $opcionParticular = explode("=", $opcionesArray[$i]) ;
                    if (trim($opcionParticular[0]) == "autoclose" ) {
                        if ( trim($opcionParticular[1] . "") == "1" ) {
                            $tieneAutoClose = 1 ;
                        }
                    }

                    if (trim($opcionParticular[0]) == "scinicio" ) {

                        $horaInicioValue =  trim($opcionParticular[1] . "");
                        $inicioDetalle = explode(":", $horaInicioValue) ;

                        $horaInicio = (int)$inicioDetalle[0];
                        $minInicio = (int)$inicioDetalle[1];
                    }
                    if (trim($opcionParticular[0]) == "scfin" ) {

                        $horaFinValue =  trim($opcionParticular[1] . "");
                        $finDetalle = explode(":", $horaFinValue) ;

                        $horaFin = (int)$finDetalle[0];
                        $minFin = (int)$finDetalle[1];
                    }

                }


                // Comprobamos status usuario y de AppID
                if ( $res2[1]->result->Status == 1 || $res2[1]->result->AppStatus->Int64 == 1 ){
                    if ( $res2[1]->result->TwoFactor != 1 ) {
                        return new WP_Error( 'broke',  "Usuario bloqueado" );
                    }
                 } 
                // Comprobamos global status del usuario de Llavero.io
                if ( $res2[1]->result->CiberUserGlobalStatus->Valid == 1 && $res2[1]->result->CiberUserGlobalStatus->Int64 == 1 ){
                    // if ( $res2[1]->result->TwoFactor != 1 ) {
                        return new WP_Error( 'broke',  "Usuario bloqueado" );
                    // }
                } 

                // Comprobamos hora siempre cerrado
                if ( $res2[1]->result->CiberUserOffset->Valid == 1 && $res2[1]->result->TwoFactor != 1 ){
                    $userOffset = $res2[1]->result->CiberUserOffset->Int64 / 60 ; // minutos
                    $curHour = $now->format('H');
                    $curHour = (int)$curHour + $userOffset ; // Aplicamos offset
                    $curMin = $now->format('i');


                    if ($horaInicioValue != "00:00" && $horaFinValue != "00:00"){
                       
                        if ($curHour > $horaInicio && $curHour < $horaFin){
                            return new WP_Error( 'broke',  "Usuario bloqueado por horario" );
                        }else {
                            // Si la hora actual es = que la hora inicio y curmin es mayor que minInicio => Bloquear
                            if ( $curHour == $horaInicio &&  $curMin > $minInicio && $horaFin > $curHour){
                                // echo "$curHour == $horaInicio &&  $curMin > $minInicio  AAA";
                                return new WP_Error( 'broke',  "Usuario bloqueado por horario" );
                            }

                            // Si la hora actual es == que hora fin y curl min es menor que minFin => Bloquear
                            if ( $curHour == $horaFin &&  $curMin < $minFin && $horaInicio < $curHour ){
                                return new WP_Error( 'broke',  "Usuario bloqueado por horario" );
                            }

                        }
                        if ($curHour == $horaInicio && $curHour == $horaFin){
                            if ( $curMin < $minFin && $curMin > $minInicio ){
                                return new WP_Error( 'broke',  "Usuario bloqueado por horario" );
                            }
                        }

                        // Para cuando el periodo es por ejemplo 22:00 - 08:00 
                        if ($horaInicio > $horaFin){
                            // 23:00
                            if ($curHour > $horaInicio ){
                                return new WP_Error( 'broke',  "Usuario bloqueado por horario" );
                            }
                            // 05:00
                            if ($curHour < $horaInicio && $curHour < $horaFin ){
                                return new WP_Error( 'broke',  "Usuario bloqueado por horario" );
                            }
                            if ( $curHour == $horaInicio && $curMin > $minInicio ){
                                return new WP_Error( 'broke',  "Usuario bloqueado por horario" );
                            }

                            if ( $curHour == $horaFin && $curMin < $minFin ){
                                return new WP_Error( 'broke',  "Usuario bloqueado por horario" );
                            }

                        }


                    }
                }

                if ( $res2[1]->result->TwoFactor == 1 && $res2[1]->result->HasTempApertura != 1){
                    return new WP_Error( 'broke',  "Usuario bloqueado por 2FA" ); 
                }


                // AUTOCLOSE
                // En caso de que se haya hecho password OK, autocerrar si autoclose esta habilitado
                if ( function_exists("wp_check_password") && $tieneAutoClose == 1 &&  $res2[1]->result->Status == 0 ) {
                    if ( $user && wp_check_password( $password, $user->data->user_pass, $user->ID) ) {
                        // PASSWORD OK 
                        $res = CiberLlaveroHelper::closeUserByUserKey($partes[1]);
                    }
                }



            }
        }


    return $user;
}


/**
*
* Columnas del listado de usuarios
*
*/

add_action('manage_users_columns','cill_columnas_de_usuario');
function cill_columnas_de_usuario($column_headers) {
  $column_headers['ciberllavero'] = "Llavero.io";
  return $column_headers;
}

add_action('manage_users_custom_column', 'cill_rellena_columnas_ciberllavero', 10, 3);
function cill_rellena_columnas_ciberllavero($value, $column_name, $user_id) {
  if ( 'ciberllavero' == $column_name ) {

        $user = get_userdata( $user_id );
    
        // $ciberllaverouserkey = get_the_author_meta( 'ciberllaverouserkey', $user->ID ) ;

        $tieneCiberLlavero = CiberLlaveroHelper::userHasCiberLlavero($user->ID );
       
        if ($tieneCiberLlavero){
            $ciberllaverouserkey = get_the_author_meta( 'ciberllaverouserkey', $user->ID ) ;
            
            $parts = explode(":", $ciberllaverouserkey); 

            return "<span style='color:green;'><b>Activado</b></span><br />" . "<div><b>AppID</b>: " . esc_html( sanitize_text_field(  $parts[0] ) ) . "</div><br /><div><b>UserID</b>: " . esc_html( sanitize_text_field( $parts[1] ) ) . "</div>" ;
        }else {
            return "<span style='color:red;'><b>Sin activar</b></span>";
        }    
  }
  return $value;
}

/*
//
// AJAX HANDLER
//
*/

add_action( 'wp_ajax_cill_get_user_data', 'cill_get_user_data' );

function cill_get_user_data() {


    global $wpdb;

    $user = wp_get_current_user();

    $ciberllaverouserkey = get_the_author_meta( 'ciberllaverouserkey', $user->ID ) ;
    $cill_appid = sanitize_text_field( get_option("cill_appid") ) ;

    $tieneCiberLlavero = CiberLlaveroHelper::userHasCiberLlavero($user->ID );

    $result = array( "user" => $user , "ciberllaverouserkey" => $ciberllaverouserkey, "cillappid" => $cill_appid , "tieneCiberLlavero" => $tieneCiberLlavero  );
    wp_send_json($result);

	wp_die();

}



add_action( 'wp_ajax_cill_empezar', 'cill_empezar' );

function cill_empezar() {


    $user = wp_get_current_user();

    $ciberllaverouserkey = get_the_author_meta( 'ciberllaverouserkey', $user->ID ) ;

    $cill_appid = sanitize_text_field(get_option("cill_appid"));
    if ( !CiberLlaveroHelper::validateAppID($cill_appid) ) {
		$result = array( "error" => "ApplicationID not valid"  );
	    wp_send_json($result);
	    wp_die();
       	return;
    }


    $userObj = get_user_by("id", $user->ID );
    $username = $userObj->data->user_login ;


    $resultado = CiberLlaveroHelper::createCiberLlaveroUser( $username );
    if (isset($resultado[0]) && isset($resultado[0]["apistatus"]) && $resultado[0]["apistatus"] == "success") {

		if ( !isset($resultado[1]) ||  !isset($resultado[1]->result) || !isset($resultado[1]->result->Actkey) || !CiberLlaveroHelper::validateUserkey($resultado[1]->result->Actkey) ){
			$result = array( "error" => $resultado  );
			wp_send_json($result);
			wp_die();
		}

		// $cill_appid previusly validated using regexp
        update_usermeta( $user->ID  , 'ciberllaverouserkey', sanitize_text_field($cill_appid . ":" . $resultado[1]->result->Actkey ) );
        $result = array( "result" => "OK"  );

    }else{
        $result = array( "error" => $resultado  );
    }

    wp_send_json($result);

	wp_die(); 

}



add_action( 'wp_ajax_cill_desvincular_cuenta', 'cill_desvincular_cuenta' );

function cill_desvincular_cuenta() {


    $user = wp_get_current_user();

    $ciberllaverouserkey = get_the_author_meta( 'ciberllaverouserkey', $user->ID ) ;

    $userObj = get_user_by("id", $user->ID );
    $username = $userObj->data->user_login ;

    // TODO:: En este momento llamar a API para activar el usuario usando mi API Key , el APPID y el nombre del usuario


    $porciones = explode(":", $ciberllaverouserkey);
    $userkey = $porciones[1];

    $resultado = CiberLlaveroHelper::deleteUserFromLlavero( $userkey  );
    if ($resultado[0]["apistatus"] == "success") {

        $result = array( "result" => "Deleted OK"  );
        update_usermeta( $user->ID  , 'ciberllaverouserkey', "" );
    }else{

        $result = $resultado;

        if (isset( $resultado[1]->error )  && isset($resultado[1]->error->message )  ){
            if (strpos($resultado[1]->error->message , 'Error obteniendo el usuario a partir de userkey') !== false ) {
                update_usermeta( $user->ID  , 'ciberllaverouserkey', "" );  
                $result = array( "result" => "Deleted OK"  );
            }else {
                $result = array( "error" => $resultado[1]  );
            }
        }else {
            $result = array( "error" => $resultado[1]  );
        }
    }    

    wp_send_json($result);

	wp_die(); 
}



add_action( 'wp_ajax_cill_set_apikey', 'cill_set_apikey' );

function cill_set_apikey() {

	if ( ! current_user_can( 'activate_plugins', get_current_user_id() ) ) {
        $result = array( "result" => "You dont have enought permissions to configure this plugin"  );
        wp_send_json($result);
        wp_die();
        return;
	}


    $cill_apikey = sanitize_text_field( trim($_POST["apikey"]) );
    if ( !CiberLlaveroHelper::validateApiKey( $cill_apikey ) ) {
		$result = array( "result" => "API Key is not valid"  );
		wp_send_json($result);
		wp_die();
       	return;
    }


	// $cill_apikey previusly sanitized and validated using regexp
    update_option("cill_apikey", $cill_apikey );
    $result = array( "result" => "API Key updated"  );
    wp_send_json($result);

	wp_die();
}



add_action( 'wp_ajax_cill_set_appid', 'cill_set_appid' );

function cill_set_appid() {

    if ( ! current_user_can( 'activate_plugins', get_current_user_id() ) ) {
        $result = array( "result" => "You dont have enought permissions to configure this plugin"  );
        wp_send_json($result);
        wp_die();
        return;
    }


    $cill_appid = sanitize_text_field( $_POST["appid"] ) ;
    if ( !CiberLlaveroHelper::validateAppID($cill_appid) ) {
        $result = array( "result" => "POST parameter 'appid' is not valid"  );
        wp_send_json($result);
        wp_die();
       	return;
    }


    update_option("cill_appid", $cill_appid );
    $result = array( "result" => "AppID updated"  );

    wp_send_json($result);
	wp_die(); 
}



add_action( 'wp_ajax_cill_config_test', 'cill_config_test' );

function cill_config_test() {


    $cill_apikey = sanitize_text_field( get_option("cill_apikey")  );
    $cill_appid  = sanitize_text_field( get_option("cill_appid" )  );
    $cill_apiURL = sanitize_text_field( get_option("cill_apiurl")  );

    $noConfigurado = 0;
    $malConfigurado = 0;

    if ($cill_apikey == "" || $cill_appid == ""){
        $noConfigurado = 1;
    }

    $resultado = CiberLlaveroHelper::getCiberLlaveroStats( );
    if ( $resultado[0]["apistatus"] == "error" ) {
        $noConfigurado = 1;
        $malConfigurado = 1;
    }

    $result = array( "result" => array ( "noconfigurado" => $noConfigurado , "malconfigurado" =>  $malConfigurado )  );

    wp_send_json($result);

	wp_die();

}



add_action( 'wp_ajax_cill_send_2fanotify', 'cill_send_2fanotify' );

function cill_send_2fanotify() {


    $user = wp_get_current_user();

    $resultado = CiberLlaveroHelper::notify2FA_User( $user->ID );
    if ( $resultado[0]["apistatus"] == "error" ) {
	$result = array( "result" => array ( "error" => "Notify error" )  );
	wp_send_json($result);
    }else {
	    $result = array( "result" => "Notify OK"  );
	    wp_send_json($result);
    }

    wp_die(); 
}


/**
* Summary: Dependencies wp-login.php page
*
*
*/

function cill_loginscript() {


    wp_enqueue_script('jquery'); 
	wp_enqueue_script( 'cill-llavero-login', plugins_url( '/includes/js/llaverologin.js', __FILE__ )  , false );

    wp_localize_script( 'cill-llavero-login', 'cillajaxurl', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

	// wp_localize_script( 'cill-llavero-login', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

}

add_action( 'login_enqueue_scripts', 'cill_loginscript', 1 );



add_action( 'wp_ajax_nopriv_cill_getmeinfo', 'cill_getmeinfo' );

function cill_getmeinfo() {


	// Primero comprobar que el usuario tiene CILL  Habilirado
	// userHasCiberLlavero
	// Obtenemos el user a partir del nombre

        $user = new stdClass;

		$cill_Sanitized_username = sanitize_user( trim( $_POST["cill_login"] ) );
       	$cill_Sanitized_email = sanitize_email( trim( $_POST["cill_login"] ) );

		$isUser = 0;

        if ( username_exists( $cill_Sanitized_username  )   ) {
            $user = get_user_by( 'login' , $cill_Sanitized_username );
			$isUser = 1;
        } else {

			if (!filter_var( $cill_Sanitized_email , FILTER_VALIDATE_EMAIL)) {
        		$result = array( "result" => 0 , "msg" => "Username or email not valid"  );
		        wp_send_json($result);
		        wp_die();
			}

            global $wpdb;
            $results = $wpdb->get_results( "select * from {$wpdb->prefix}users WHERE user_email = '". $cill_Sanitized_email ."'", OBJECT );

            $user = $results[0];
        }




	if (!isset($user->ID) || !CiberLlaveroHelper::userHasCiberLlavero( $user->ID ) ) {
		$result = array( "result" => 0  );
		wp_send_json($result);
		wp_die();
	}


    $finalUser = "";
    if (  $isUser == 1 ) {
        $finalUser = $cill_Sanitized_username ;
    }else {
		$finalUser = $cill_Sanitized_email;
    }


    $resultado = CiberLlaveroHelper::meInfo( $finalUser );
    if ( $resultado[0]["apistatus"] == "error" ) {
        $result = array( "result" => array ( "error" => "Cill error " . print_r($resultado, true) )  );
        wp_send_json($result);
		wp_die();
    }else {

        //
        // Si candado global de uusario cerrado, no enviamos notificacion
        //

        if ( $resultado[1]->result->CiberUserGlobalStatus->Valid == 1 && $resultado[1]->result->CiberUserGlobalStatus->Int64 == 1 ){
                $result = array( "result" => 0  );
                wp_send_json($result);
                wp_die();
        }


	if ( $resultado[1]->result->TwoFactor == 1 ){
            $result = array( "result" => 1  );
	}else {
	    $result = array( "result" => 0  );
	}

        wp_send_json($result);
    }

    wp_die();


}


add_action( 'wp_ajax_nopriv_cill_getmeinfo_apertura', 'cill_getmeinfo_apertura' );

function cill_getmeinfo_apertura() {


        $user = new stdClass;


        $cill_Sanitized_username = sanitize_user( trim( $_POST["cill_login"] ) );
        $cill_Sanitized_email = sanitize_email( trim( $_POST["cill_login"] ) );

		$isUser = 0;

        if ( username_exists( trim( $cill_Sanitized_username ) ) ) {
            $user = get_user_by( 'login' , $cill_Sanitized_username );
			$isUser = 1;
        } else {

            if (!filter_var( $cill_Sanitized_email , FILTER_VALIDATE_EMAIL)) {
                $result = array( "result" => 0 , "msg" => "Username or email not valid"  );
                wp_send_json($result);
                wp_die();
            }

            global $wpdb;
            $results = $wpdb->get_results( "select * from {$wpdb->prefix}users WHERE user_email = '". $cill_Sanitized_email ."'", OBJECT );

            $user = $results[0];
        }




    if (!isset($user->ID) || !CiberLlaveroHelper::userHasCiberLlavero( $user->ID ) ) {
        $result = array( "result" => 0  );
        wp_send_json($result);
        wp_die();
    }

	$finalUser = "";
	if (  $isUser == 1 ) {
		$finalUser = $cill_Sanitized_username ;
	}else {
		$finalUser = $cill_Sanitized_email;
	}


    $resultado = CiberLlaveroHelper::meInfo( $finalUser );
    if ( $resultado[0]["apistatus"] == "error" ) {
        $result = array( "result" => array ( "error" => "Cill error " . print_r($resultado, true) )  );
        wp_send_json($result);
    }else {

    	if ( $resultado[1]->result->HasTempApertura == 1 ){
    	    $result = array( "result" => 1  );
	    }else {
			$result = array( "result" => 0  );
    	}

         wp_send_json($result);
    }

    wp_die();

}



add_action( 'wp_ajax_nopriv_cill_send_notification', 'cill_send_notification' );

function cill_send_notification() {


    $cill_Sanitized_login =  sanitize_text_field( trim( $_POST["cill_login"] ) );

    $resultado = CiberLlaveroHelper::sendNotification( $cill_Sanitized_login );
    if ( $resultado[0]["apistatus"] == "error" ) {
        $result = array( "result" => array ( "error" => "Cill error " . print_r($resultado, true) )  );
        wp_send_json($result);
    }else {

            $result = array( "result" => 1  );
            wp_send_json($result);
    }

    wp_die();

}



