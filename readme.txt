=== Llavero.io ===
Contributors: davidnoguera
Tags: security, authentication, 2fa, login
Requires at least: 4.6
Tested up to: 4.9
Stable tag: trunk
Requires PHP: 5.3
License: GPLv2 or later
Donate link: https://llavero.io
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Este plugin permite vincular las cuentas de usuario de WordPress con Llavero.io para tener un segundo factor de authenticación (2FA) en el login de los usuarios. Llavero.io permite vincular tus cuentas de WordPress a tus dispositivos móviles de forma que recibas notificaciones push para validar el proceso de login. También permite crear horarios de cierre automático entre otras caracteríticas.


== Description ==

Llavero.io es un servicio creado por Webempresa.com con el propósito de facilitar en WordPress una protección extra en momento de hacer login.

El plugin crea un segundo factor de autenticación que permite vincular la cuenta de tu blog WordPress a tu dispositivo móvil, de forma que nadie pueda hacer login en tu cuenta sin antes validar el acceso desde tu móvil, a través e una notificación Push a tu dispositivo.

Llavero.io permite definir horarios de autocierre de forma que a esas horas nadie pueda loguearse aunque tenga tu contraseña correcta, por ejemplo mientras estás durmiendo o de vacaciones.

El administrador del blog podrá sobreescribir los valores de los usuarios y obligar a que cada usuario solo pueda acceder durante unos horarios específicos definidos por él.

Puedes ver una guía de inicio rápido para empezar a usar Llavero.io en este enlace: https://llavero.io/empieza-a-usar-llavero-wordpress/

El plugin no rastrea ningún tipo de información de los usuarios, el uso de APIs externas es tan solo para establecer un estado de abierto y cerrado de la cuenta asociada, en ningún momento se obtiene información de WordPress para almacenarla en serviores externos.



== Installation ==

1. Crea una cuenta en Llavero.io ( https://app.llavero.io ), es gratis
2. Instala el plugin en tu blog desde el repositorio público de WordPress o descargando el plugin y subiéndolo al directorio `/wp-content/plugins/` de tu blog .
3. Haz login dentro de tu WordPress usando la cuenta de administrador del blog, ves a Llavero.io, luego a Configuración, usa las credenciales de la cuenta de Llavero.io que creaste en el paso anterior y pulsa pulsa en Enviar. Tu blog se sincronizará con tu cuenta de Llavero.io.
4. Ahora todos los usuarios de tu blog pueden proteger  sus propias cuentas usando sus respectivas cuentas de Llavero.io 
5. Cada usuario debe crear su propia cuenta de Llavero.io (https://app.llavero.io) para poder proteger su cuenta, luego acceder a Usuario >> Perfil. Verás que aparece una sección nueva llamada "Llavero.io". Aquí cada usuario puede usar sus propias credenciale sde Llavero.io para proteger sus propias cuentas.
6. Consulta la guía de inicio rápido: https://llavero.io/empieza-a-usar-llavero-wordpress/

== Frequently Asked Questions ==

= ¿Como realiza la doble validación Llavero.io? =

Mediante notificaciones push a tu movil, o si no quieres vincular tu dispositivo móvil, simplemente mediante un interruptor que cada usuario puede abrir o cerrar desde la App de Llavero.io.

= ¿Dispone de aplicación móvil para las notificaciones?  =

Sí, dispone de aplicación para iOS y para Android que puedes ver aquí: 

https://play.google.com/store/apps/details?id=io.llavero.llaveroio

y aqui:

https://itunes.apple.com/us/app/llavero-io/id1345700110?mt=8

= ¿Tenéis un tutorial que explique como funciona el servicio?

Sí, consulta este enlace: https://llavero.io/empieza-a-usar-llavero-wordpress/

== Screenshots ==

1. El plugin intercepta el login standard de WordPress
2. En el momento del login envía una notificación push que debes validar desde la App de Llavero.io de tu móvil
3. Recibes una notificación en tu movil, de forma que si te roban la contraseña, no podrán acceder salvo que tamién tengan acceso a tu móvil
4. El plugin te permite acceder solo si aceptas la petición
5. Tan solo necesitas poner tus datos de Llavero.io para configurar tu cuenta en WordPress

== Changelog ==

= 0.1.1 =
Primera release

= 0.1.2 =
Añadidos links de documentación

= 0.1.3 =
Candado global tiene prioridad sobre las notificaciones push

= 0.1.4 =
Añadida funcionalidad emparejamiento de usuarios de llavero que tienen habilitada autenticación en 2 pasos para la cuenta de Llavero.io. Solucionados algunos avisos Notice sin importancia, pero que afectaban si tenias el error_reporting configurado del modo verboso. Solucionado un pequeño bug con la posición del menú en el SideBar izquierdo.


== Upgrade notice ==
= 0.1.4 =
Añadida funcionalidad emparejamiento de usuarios de llavero que tienen habilitada autenticación en 2 pasos para la cuenta de Llavero.io. Solucionados algunos avisos Notice sin importancia, pero que afectaban si tenias el error_reporting configurado del modo verboso. Solucionado un pequeño bug con la posición del menú en el SideBar izquierdo.


== Translations ==

* Spanish - default, always included

