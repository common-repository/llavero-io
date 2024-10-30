<?php

if ( is_super_admin() ) {
    

    if ($_POST["submitaction"] == "Guardar" ){

		$cill_appid = sanitize_text_field($_POST["appid"]);
    	if ( CiberLlaveroHelper::validateAppID($cill_appid) ) {
	        update_option("cill_appid", $cill_appid );
    	}

       	$cill_apikey = sanitize_text_field($_POST["apikey"]);
        if ( CiberLlaveroHelper::validateApiKey($cill_apikey) ) {
            update_option("cill_apikey", $cill_apikey );
        }

        $cill_apiurl = sanitize_text_field($_POST["apiurl"]);
		if (filter_var( $cill_apiurl , FILTER_VALIDATE_URL)) {
            update_option("cill_apiurl", esc_url_raw( $cill_apiurl ) );
		}

        CiberLlaveroViewHelper::printMessage("SUCCESS", "Guardado! "  );
    }

    $cill_apikey = sanitize_text_field(get_option("cill_apikey"));
    $cill_appid = sanitize_text_field(get_option("cill_appid"));
    $cill_apiURL = sanitize_text_field(get_option("cill_apiurl"));
    

    $showAutoConfig = 0;
    $malConfigurado = 0;


    if ($cill_apikey == "" || $cill_appid == ""){
        CiberLlaveroViewHelper::printMessage("WARN", "No has especificado el APIKey de tu cuenta de CiberProtector o el AppID de CiberLlavero, ambos necesarios para el funcionamiento del plugin.");
        $showAutoConfig = 1;

    }

    $resultado = CiberLlaveroHelper::getCiberLlaveroStats( );
    

    if ( $resultado[0]["apistatus"] == "error" ) {
        $showAutoConfig = 1;
        $malConfigurado = 1;
    }

?>


<?php 
    if ($malConfigurado == 1 && ($cill_apikey != "" || $cill_appid != "") ) {
        echo CiberLlaveroViewHelper::printMessage("WARN", "La configuración actual de AppID y APIKey es incorrecta") ;
    }
?>

<div id="cill_messages" >

</div>

<h3>Configuración manual</h3>

<?php 
    if ($malConfigurado == 0) {
        echo CiberLlaveroViewHelper::printMessage("SUCCESS", "TEST OK: Configurado correctamente") ;
    }
?>

<p>


<form method="post">

                <input type="text" name="page" value="cill-llavero" style="display:none;" >
                <input type="text" name="tab" value="display_config_manual" style="display:none;" >
                <input type="text" name="action" value="" style="display:none;" >


    <div style="display:block; width:100%;">



    <table class="form-table">
        <tbody>
            <tr>
                <td>
                   <label for="apikey"><b>API Key de tu cuenta de CiberLlavero</b></label>
                </td>
                <td>
                    <input type="text" id="apikey" name="apikey" value="<?php echo  $cill_apikey != "" ? $cill_apikey : "" ; ?>" style="width:350px;padding:10px;" class="input"  placeholder="API Key .."  >
                </td>
            </tr>
            <tr>
                <td>
                    <label for="appid"><b>ID de aplicación en CiberLlavero</b></label>
                </td>
                <td>
                    <input type="text" id="appid" name="appid" value="<?php echo  $cill_appid != "" ? $cill_appid : "" ; ?>" style="width:350px;padding:10px;" class="input"  placeholder="App ID .."  >
                </td>
            </tr>
            <tr>
                <td>
                    <label for="appid"><b>URL API Key</b></label>
                </td>
                <td> 
                    <input type="text" id="apiurl" name="apiurl" value="<?php echo  $cill_apiURL != "" ? $cill_apiURL : "" ; ?>" style="width:350px;padding:10px;" class="input"  placeholder="API URL .."  >
                </td>
            </tr>

            <tr>
                <td colspan="2" >
                    <input type="submit" name="submitaction"  class="button" value="Guardar" style="height:40px;">
                <td>
            <tr>
        </table>
</form>


</p>

<?php

}
