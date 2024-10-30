<?php

    $resultado = CiberLlaveroHelper::getCiberLlaveroStats( );
    $res = $resultado[1];

?>

<p>
<?php if ( $resultado[0]["apistatus"] == "error" ): ?>

<?php echo CiberLlaveroViewHelper::printMessage("WARN", "La configuración actual de AppID y APIKey es incorrecta") ; ?>


<h2 style="color:#ca4a1f;">Error obteniendo datos a partir del AppID y API Key proporcionados en la pestaña de Configuración</h2>
<p>
Accede a la pestaña de <a href="admin.php?page=cill-llavero&tab=display_config" >Configuración Automática</a> e ingresa tu usuario y contraseña de Llavero.io para configurar.
</p>
<?php endif; ?>

<?php if ( $resultado[0]["apistatus"] == "success" && isset($res->result) ): ?>
<?php $candado = $res->result->candado ; ?>


                    <h1><?php echo esc_html( $candado->URL ) ; ?></h1>
                    <h3><?php echo esc_html( $candado->Description ) ; ?></h3>
                    <h5>ApplicationID: <u><?php echo esc_html( $candado->ApplicationID ) ; ?></u></h5>


    <?php if ( count($res->result->userlist ) > 0 ){ ?>

<table class="wp-list-table widefat fixed striped posts" >
    <thead>
	<tr>
		<th>Username</th>
        <th>Status</th>
        <th>Notificaciones Push</th>
        <th>Key</th>
        <th>Creado</th>
	</thead>
    <tbody>
    <?php 

        for ( $i = 0; $i < count($res->result->userlist ); $i++ ) {

            	echo "<tr>";
                echo "<td>" . esc_html( $res->result->userlist[$i]->Username ) . "</td>" ;
                echo "<td>";

                if ( $res->result->userlist[$i]->Status == -1  ) {
                    echo "Desactivado";
                }else {
                    if ( $res->result->userlist[$i]->Status == 0 ){
                         echo "<span style='color:seagreen;'><b>Abierto</b></span>"; 
                    }else if ( $res->result->userlist[$i]->Status == 1 ){
                        echo "<span style=color:red; ><b>Cerrado</b></span>";
                    }   
                }
            echo "</td>" ;               
	
		if ( isset($res->result->userlist[$i]->TwoFactor) && $res->result->userlist[$i]->TwoFactor == 1  ) {
			echo "<td>SI</td>";
		}else {
			echo "<td>NO</td>";
		}

                echo "<td>" . esc_html( $res->result->userlist[$i]->Actkey )  . "</td>" ; 
                echo "<td>" . esc_html( $res->result->userlist[$i]->Created ) . "</td>" ;
            echo "</tr>";
        }        
    ?>
    </tbody>
</table>
    <?php }else {  ?>
        <b>No hay usuarios registrados en este AppID aún.</b>
    <?php } ?>

<?php endif; ?>

</p>
