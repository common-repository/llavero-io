<?php

require_once("view_helper.php");

function cill_view_main(){

    $active_page = "cill-llavero";
    $active_tab = "";

    if( isset( $_REQUEST[ 'tab' ] ) ) {
        $active_tab = $_REQUEST[ 'tab' ];
    }else {
        $active_tab = "display_inicio";
    }

    $cill_apikey = get_option("cill_apikey");
    $cill_appid = get_option("cill_appid");


    if ($cill_apikey == "" || $cill_appid == ""){
        $active_tab = "display_config";
    }


    echo '<div class="wrap">';
    echo '   <h1>Llavero.io: segundo factor de autenticación para WordPress </h1> <br />';

    ?>




        <div id="bwp-donation">
            <form class="paypal-form" action="https://www.paypal.com/cgi-bin/webscr" method="post">
            <p style="margin-bottom: 3px;">
                <input type="hidden" name="cmd" value="_xclick">
                <input type="hidden" name="business" value="LTTC84C826PFQ">
                <input type="hidden" name="lc" value="ES">
                <input type="hidden" name="button_subtype" value="services">
                <input type="hidden" name="no_note" value="0">
                <input type="hidden" name="cn" value="Would you like to say anything to me?">
                <input type="hidden" name="no_shipping" value="1">
                <input type="hidden" name="rm" value="1">
                <!-- <input type="hidden" name="return" value="https://optimizador.io"> -->
                <input type="hidden" name="currency_code" value="EUR">
                <input type="hidden" name="bn" value="PP-BuyNowBF:icon-paypal.gif:NonHosted">
                <input type="hidden" name="item_name" value="Donar a Optimizador.io" />
                <select name="amount">
                    <option value="1.00">Donar 1 Euro </option>
                    <option value="5.00">Una cerveza (5 Euros)</option>
                    <option value="10.00">1 ronda (10 Euros)</option>
                    <option value="25.00">1 cena (25 Euros) !</option>
                </select>
                <span class="paypal-alternate-input" style="display: none;"></span>
                &nbsp;
                <button class="we-button-paypal button-secondary" type="submit" name="submit">
                    <span class="paypal-via">Vía</span>
                    <span class="paypal-pay">Pay</span><span class="paypal-pal">Pal</span>
                </button>
                <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
            </p>
            </form>
        </div>




	<style type="text/css" >

		#we-get-social {
			padding:10px;
			padding-bottom:30px;
		}

		.we-twitter-buttons , .we-fb-buttons , .we-gplus-buttons {
			padding:5px;
			float:left;
		}

	</style>

    <div id="we-get-social" class="postbox">
        <h1 class="hndle"><span>Comparte si te resulta de utilidad <img src="../wp-includes/images/smilies/icon_smile.gif" /> </span></h1>
        <divo class="inside">
            <div id="we-social-buttons" class="clearfix">
                <!-- Twitter buttons -->
                <div class="we-twitter-buttons">
                    <a href="https://twitter.com/share"
                        class="twitter-share-button"
                        data-url="https://llavero.io/"
                        data-text="Securiza el acceso al login de tu WordPress gratis con este plugin."
                        data-via="webempresa"
                        data-hashtags="llaveroio"
                        data-dnt="true">Tweet</a>
                    <a href="https://twitter.com/webempresa"
                        class="twitter-follow-button"
                        data-show-screen-name="false"
                        data-show-count="true"
                        data-dnt="true">Follow Me!</a>
                    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
                </div>
                <!-- Google plus -->
                <!--div class="we-gplus-buttons">
                    <div class="g-plusone" data-size="medium" data-href="https://llavero.io/"></div>
                    <script src="https://apis.google.com/js/platform.js" async defer></script>
                </div-->



                <!-- Facebook button -->
                <div class="we-fb-buttons">
                    <div class="fb-like"
                        data-href="https://llavero.io/"
                        data-layout="standard"
                        data-action="like"
                        data-share="true"
			data-show-faces="true">
		     </div>
                    <div id="fb-root"></div>
                    <script>(function(d, s, id) {
                        var js, fjs = d.getElementsByTagName(s)[0];
                        if (d.getElementById(id)) return;
                        js = d.createElement(s); js.id = id;
                        js.src = "//connect.facebook.net/es_ES/sdk.js#xfbml=1&version=v2.5";
                        fjs.parentNode.insertBefore(js, fjs);
                        }(document, 'script', 'facebook-jssdk'));</script>
                </div>
            </div>
        <br style="clear:both" />
        <div class="clear-fix">
            <a href="https://llavero.io/empieza-a-usar-llavero-wordpress/" target="_blank" ><h1>Guía de inicio rápido, aprende a usar el plugin..</h1></a>
            <a href="https://llavero.io/dejar-testimonio/" target="_blank" ><h1>Dejanos tu testimonio y obten un link para mejorar tu SEO</h1></a>
            <a href="https://llavero.io/sugerencias-o-dudas/" target="_blank" ><h2>¿Sugerencias o dudas? comunícate con nosotros y te responderemos</h2></a>
        </div>
        </div>

    </div>

<style type="text/css" >

.we-button-paypal{
    color: #003580!important;
    text-shadow: 0 1px 1px #ffcf84!important;
    background: #ffe3b7!important;
    border-color: #ffc975!important;
    box-shadow: 0 1px 0 #ffc975!important;
    font-weight: 600;
}
</style>






    <h2 class="nav-tab-wrapper">
        <a href="?page=cill-llavero&tab=display_inicio" class="nav-tab <?php echo ( $active_tab == 'display_inicio' || $active_tab == '' ) ? 'nav-tab-active' : ''; ?> ">Inicio</a>
        <a href="?page=cill-llavero&tab=display_config" class="nav-tab <?php echo $active_tab == 'display_config' ? 'nav-tab-active' : ''; ?> ">Configuración automática</a>
        <a href="?page=cill-llavero&tab=display_config_manual" class="nav-tab <?php echo $active_tab == 'display_config_manual' ? 'nav-tab-active' : ''; ?> ">Configuración manual (avanzado)</a>
        <a href="?page=cill-llavero&tab=display_help" class="nav-tab <?php echo $active_tab == 'display_help' ? 'nav-tab-active' : ''; ?>"><span class="icon dashicons dashicons-editor-help" ></span> Ayuda</a>
        <a href="?page=cill-llavero&tab=display_debug" class="nav-tab <?php echo $active_tab == 'display_debug' ? 'nav-tab-active' : ''; ?>"><span class="icon dashicons dashicons-editor-help" ></span> Debug</a>
    </h2>

    <?php


    if ($active_tab == "display_inicio"){
        require_once("views".  DIRECTORY_SEPARATOR ."inicio.php");
    }else if ($active_tab == "display_config"){
        require_once("views".  DIRECTORY_SEPARATOR ."config.php");
    }else if ($active_tab == "display_config_manual"){
        require_once("views".  DIRECTORY_SEPARATOR ."configmanual.php");
    }else if ($active_tab == "display_help"){
        require_once("views".  DIRECTORY_SEPARATOR ."help.php");
    }else if ($active_tab == "display_debug"){
        require_once("views".  DIRECTORY_SEPARATOR ."debug.php");
    }

    echo '</div>';
}

