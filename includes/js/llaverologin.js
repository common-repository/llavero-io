
window.onload = cill_procesa_login

// Ocultar boton de login
function cill_procesa_login() {

        window.cill_procesado = 0 ;
	jQuery("#loginform").submit( cill_pre_procesa2fa );
}

function cill_pre_procesa2fa(event){

        if (        window.cill_procesado == 0  ) {
        event.preventDefault();

	window.cillLogin = jQuery("#user_login").val();
	window.cillPass =  jQuery("#user_pass").val();

        jQuery.ajax({
            url : cillajaxurl.ajax_url ,
            type : 'post',
            data : {
                action : 'cill_getmeinfo',
                cill_login : window.cillLogin
            },
            success : function( response ) {
                if (response.result == 0){
			window.cill_procesado = 1
			jQuery("#loginform").submit();
		}else {
			cill_procesa2fa();		
		}
            }
        });

	}


}


function cill_procesa2fa() {

	if (        window.cill_procesado == 0  ) {
		window.cill_login_form = jQuery("#login").html();
		window.cill_procesado = 1 ;
	        var cillLogin = jQuery("#user_login").val();
		jQuery("#login").html(" 				<p style='background-color:white;padding:10px;border-radius: 5px;' > 				<span style='color:;font-weight:bold;' >						<p style='text-align:center !important;background-color:white;padding:10px; '> <img src=' data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB0AAAAvCAYAAAAfIcGpAAAAs0lEQVRYhe3XsQ0DMQgF0JvKA7hzzW4s4RWYAImJXPx0KaLz6Ugu8UX6SHTIT7gBNiyIjShRopegYwyoKlprKKWksrUGVcUYI4eqahp7TVXNoe90uNdxCp09VGuFuyMiEBFwd9Rap/WXoNn8H7T3PiuZRu/9MzQi0mhEECVKlChRokSxeIjfZkcSEZgZzAwi8hvUzJ51ZnYderRsn0XTy/bRWXH2e9NnxZID6ptBlCjR+6MPrKmtWSdTUEMAAAAASUVORK5CYII=' /> </p> <p style='background-color:white;text-align:center;padding:0px 5px 25px 5px;font-weight:bold; border-radius: 5px; '><label> Enviada notificación de segundo factor a tu dispositivo móvil. <br /> Valida el acceso desde tu móvil para poder acceder.</label></p></span></p><br /> <button onclick='cill_siguiente();' class='button button-primary button-large' >Siguiente</button>");

		// Enviar notificacion 2FA al movil

	        jQuery.ajax({
	            url : cillajaxurl.ajax_url ,
	            type : 'post',
	            data : {
	                action : 'cill_send_notification',
	                cill_login : cillLogin
	            },
	            success : function( response ) {
					// TODO: Handle Notification error
					console.log(JSON.stringify(response));


					var x = 0;
					var intervalID = setInterval( () => {
						
							jQuery.ajax({
               					url : cillajaxurl.ajax_url ,
			    	           	type : 'post',
            				   	data : {
                    				action : 'cill_getmeinfo_apertura',
			                	    cill_login : cillLogin
            				   	},
            				   	success : ( response ) => {
									if (response.result  == 1){
										window.clearInterval(intervalID);
										cill_siguiente();
									}
									console.log(response)
								}
							});

					   		
					   		if (++x >= 10) {
					   		    window.clearInterval(intervalID);
							   }
					}, 5000);
					// Cada 5 segundos durante 10 iteraciones comprobamos si ya tiene Apertura

	            }
	        });

	}else {
		jQuery("#loginform").submit();
	}

}

function cill_siguiente(){

	jQuery("#login").html( window.cill_login_form );

        jQuery("#user_login").val(window.cillLogin);
        jQuery("#user_pass").val(window.cillPass);

	jQuery("#loginform").submit();

    jQuery("#loginform").html("<p style='text-align:center !important;'><label>Accediendo</label><p>")

	window.cillPass = "";
}

