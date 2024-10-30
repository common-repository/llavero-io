
var cilib = function(){
}



cilib.BASE_URL = 'https://api.llavero.io'

cilib.LOGIN_URL = cilib.BASE_URL + '/login'
cilib.LOGOUT_URL =  cilib.BASE_URL + '/logout'
cilib.storagePrefix = 'cill_'

var pki = forge.pki;

cilib.publicPEM = '-----BEGIN PUBLIC KEY-----\nMIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAtawodp5ToEn+n4rJr++r\ngmKHvjzHuNhopmF4bASi5mXS0gzOuWnsTGmPmTS9+4bAuGDMA6u3AlSaP8+5CbC4\nwyjkJR1HnShOT/vMnXUgg3MO2NiY0Tb3LnKcXIRH7577OjR812CPnZdG21AQzpSe\n9JM6pqWM/KKGQV0bKVwpK53EfoH1nWabKlL98Tz2VOX1aAR6vqCpiP060eRIbxBB\n0eUjsVU6U/zg+T060fR1ur1rO/ekHhBfcloPiRvqYKNNFzsCJnMwg8x1zlmV+HK/\nt7kSbohA8xwGtIC+LXFnwtsypr6xUdR75HoOE4SpiJtTh/PVL7felPRF9d/v0UQY\nzYU9F/m9EhhqP52Vp/DL0VJSfmSK9n4U7eQH8i7fYxyWnTr1xFJkzrznUdYAyogQ\ne81SBUxUqdw9ptPPYBQ2O1SKlavnttoo7hB3jMjt3bArezCraJNTS80485HBi15E\nwH/iD+eTQTO8vtc/h/l6aOUgXgs4jlP3pGZ04wCjT1E2aXrpEuJSkMAbfkJ2iovF\nJArZaTIXaAfJnygbCD2x9yQ4B2YZfo9PV/9+yzHtzDW04lK/2+FIkIAn2i/dPsok\nU2gxePf0AzWu0AY5qckhehn4evhy+xT7NSrq84JfvBYP8J2ZQ6X+HLixKj7bt5v8\nU81TDxtlSp8VAjZnTFe2IUECAwEAAQ==\n-----END PUBLIC KEY-----'


cilib.publicKey = pki.publicKeyFromPem(cilib.publicPEM)


cilib.crearformdata = function(data){
    //
    if ( data !== null && typeof data !== 'object' ) { 
        return( ( data === null ) ? "" : data.toString() ); 
    } 
    var multipart= "";
    var boundary=Math.random().toString().substr(2);

    //client.setRequestHeader("content-type", "multipart/form-data; charset=utf-8; boundary=" + boundary);

    for(var name in data){          
        multipart  += "--" + boundary
                + "\r\nContent-Disposition: form-data; name=" + name
                + "\r\nContent-type: application/octet-stream"
                + "\r\n\r\n" + data[name] + "\r\n";
    }
    multipart += "--"+boundary+"--\r\n";
    return {multipart: multipart, boundary: boundary};
    //
}

cilib.headerpush = function(nombre, valor, headers) {
    var match= false;
    headers.forEach(function(header){
        if ( header[0] == nombre ) match= true;
            console.log(header[0]+' ' +nombre);
        });
        if (!match){
            var header= [nombre, valor];
            (headers == null)?(headers= [header]):(headers.push(header));      
        }
    return true;
}

cilib.httprequest = function (method, url, data, headersP) {
    
    if (typeof headersP == "undefined" || typeof headersP == null || headersP == null){
        headersP = []
    }

    var promise= new Promise(function(resolve, reject){
        var client = new XMLHttpRequest();
            client.open(method, url);
            //client.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            //client.setRequestHeader("Content-type", "multipart/form-data");
            
            var headers = []

            if( localStorage.getItem(cilib.storagePrefix + "_" + window.cill_userDetails.user.ID + '_token') && localStorage.getItem(cilib.storagePrefix +  "_" + window.cill_userDetails.user.ID + '_secret') ){
                cilib.headerpush("Authorization", "Bearer " + localStorage.getItem(cilib.storagePrefix +   "_" + window.cill_userDetails.user.ID +  '_token') , headers )
                cilib.headerpush("Secret", localStorage.getItem(cilib.storagePrefix +  "_" + window.cill_userDetails.user.ID +   '_secret') , headers )
            }

            console.log("====       HEADERS PPPPP")
            console.log(headersP)

            headersP.forEach(function(header){
                console.log ("HEader Iteracion....")
                console.log(header) 
                if (header.length == 2){
                    console.log("Dentro del header")
                    cilib.headerpush(header[0], header[1], headers)
                }                       
            });
            if ('undefined' !== typeof headers && Array.isArray(headers)){
                headers.forEach(function(header){
                    if (header.length == 2) client.setRequestHeader(header[0], header[1]);
                });
            }

            // method
            switch (true) {
            case /post|put/i.test(method):                 
                var formdata= cilib.crearformdata(data);
                client.setRequestHeader("content-type", "multipart/form-data; charset=utf-8; boundary=" + formdata.boundary);
                client.send(formdata.multipart);
                break;
            case /get|delete/i.test(method):
                client.send();
                break;
            default:
                reject( method + 'no permitido');
                break;
            } // switch
            client.onload= function(){     
            if (this.status == 200){
                try {
                var data=  JSON.parse(this.response);
                resolve(data);
                } catch(e) {
                reject('no se ha podido parsear');
                }
                
            } else {
                reject('error '+this.statusText);
            }
            };
            client.onerror= function(){
                    reject('client error '+this.statusText);
                    };
            });        
            return promise;
}



/* Crypto Funciones BEGIN */

cilib.getNonce = function() {
    function s4() { 
    return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1); 
    }
    //return encriptarRSA_OAEP_SHA256(s4()+s4()+'-'+s4()+'-'+s4()+'-'+s4()+'-'+s4()+s4()+s4(), true);
    var nonce= s4()+s4()+'-'+s4()+'-'+s4()+'-'+s4()+'-'+s4()+s4()+s4();
    return nonce;
}


cilib.generateRSA = function(objres, callback ){

    var rsa = forge.pki.rsa;
    rsa.generateKeyPair({bits: 2048, workers: 2}, function(err, keypair) {
    //keypair.privateKey, keypair.publicKey
        objres.private = keypair.privateKey
        objres.public =  keypair.publicKey
        callback(objres);
    });

}

cilib.encriptarRSA_OAEP_SHA256 = function(message){
    var encrypted = cilib.publicKey.encrypt(message, 'RSA-OAEP', {
        md: forge.md.sha256.create()
    });
    return forge.util.encode64(encrypted);
}
    
cilib.desEncriptarRSA_OAEP_SHA256 = function(message) {

    var prkPEM = localStorage.getItem(cilib.storagePrefix + 'prk')
    var privateKey = forge.pki.privateKeyFromPem( prkPEM );
   
    return privateKey.decrypt(forge.util.decode64(message), 'RSA-OAEP',  {md: forge.md.sha256.create()} );
}


/* Crypt Funciones END */

cilib.deleteItem = function ( ItemID ){

    if( !Number.isInteger(ItemID) ) {
        return -2    
    }
    return cilib.httprequest('DELETE', cilib.BASE_URL + '/item/' + ItemID , null, null) 

}


cilib.getItem = function ( ItemID ){

    if( !Number.isInteger( parseInt(ItemID) ) ) {
        console.log("ITEMID No es un entero")
        return -2    
    }
    return cilib.httprequest('GET', cilib.BASE_URL + '/item/' + ItemID , null, null) 

}

cilib.getItemMasterP = function ( ItemID, masterP ){

    if( !Number.isInteger( parseInt(ItemID) ) ) {
        console.log("ITEMID No es un entero")
        return -2    
    }
    if (masterP == ""){
        console.log("MasterP no es un string:: " + masterP)
        return -1
    }

    var encriptedMasterP = cilib.encriptarRSA_OAEP_SHA256(masterP)


    return cilib.httprequest('GET', cilib.BASE_URL + '/item/' + ItemID , null, [ [ "Password", encriptedMasterP ]  ] ) 

}


cilib.deleteItemMultiple = function (ItemIDArray){
    ItemIDArray.forEach(function(value, index, ar){
        if( !Number.isInteger(value) ) {
            return -1    
        }
    });

    var ids = ""
    ItemIDArray.forEach(function(value, index, ar){
        ids += value.value + ","
    });
    ids = ids.slice(0, -1);

    var data = {
        "ids": ids 
    }

    return cilib.httprequest('POST', cilib.BASE_URL + '/item/delete/multiple', data , null)

}

cilib.createItem = function ( ItemObj ){

    // TODO:: Comprobar que todos los campos necesarios estan

    return cilib.httprequest('POST', cilib.BASE_URL + '/item' , ItemObj, null) 

}

cilib.updateItem = function ( ItemObj ){

    // TODO:: Comprobar que todos los campos necesarios estan
    return cilib.httprequest('PUT', cilib.BASE_URL + '/item/' + ItemObj.ItemID , ItemObj, null) 

}

cilib.updateItemMasterP = function (ItemObj, masterP){

    var encriptedMasterP = cilib.encriptarRSA_OAEP_SHA256(masterP)
    return cilib.httprequest('PUT', cilib.BASE_URL + '/item/' + ItemObj.ItemID , ItemObj,  [[ "Password", encriptedMasterP ]] )

}


cilib.createUser = function (User) {

    var emailEncr = cilib.encriptarRSA_OAEP_SHA256(User.email)
    var passwordEncr = cilib.encriptarRSA_OAEP_SHA256(User.password)

    var data = {
        "username": emailEncr,
        "password": passwordEncr ,
        "email": User.email ,
        "fname": User.firstName ,
        "lname": User.lastName 
    }

    return cilib.httprequest('POST', cilib.BASE_URL + '/user' , data , null)

}

cilib.cambiarContrasenaMaestra = function(oldPass, newPass){

    var oldPass = cilib.encriptarRSA_OAEP_SHA256(oldPass)
    var newPass = cilib.encriptarRSA_OAEP_SHA256(newPass)
    
    var data = {
        "oldmpass": oldPass,
        "newmpass": newPass
    }
    return cilib.httprequest('POST', cilib.BASE_URL + '/account/changemaster' , data , null)
}

cilib.shareItem = function(ItemID, DestUser){

    var data = {
        "itemid": ItemID,
        "destUser": DestUser
    }

    return cilib.httprequest('POST', cilib.BASE_URL + '/item/share' , data , null)

}

cilib.pendingSharedOffers = function(){

    return cilib.httprequest('GET', cilib.BASE_URL + '/item/share/pending' , null , null)

}




cilib.getPendingSharedItemsWithCache = function() {

    var promise= new Promise((resolve, reject) => {

        // Comprobar que la versión actual de los pendings sharedObjects no sea demasiado actual y en ese caso devolver la version cacheada
        var currentdate = new Date();
        var datetime = currentdate.getFullYear() + "-"
                    + (currentdate.getMonth() + 1)  + "-" 
                    + currentdate.getDate() + " "  
                    + currentdate.getHours() + ":"  
                    + currentdate.getMinutes() + ":" 
                    + currentdate.getSeconds();
        console.log("Route changeeee!!! " + new Date(datetime).getTime() )


      if (localStorage.getItem("pendingSharedOffers") !== null && localStorage.getItem("pendingSharedOffers") != "" ) {
        var pendingSharesLocal = JSON.parse(localStorage['pendingSharedOffers'])
        if (typeof pendingSharesLocal.timestamp != "undefined"){

            var dataDateTime = pendingSharesLocal.timestamp
            var currentDateTime = new Date(datetime).getTime() 

            var timeDiff = (currentDateTime - dataDateTime) / 1000

            console.log("Los datos son de hace " + timeDiff + " segundos...")
            if (timeDiff <= 60){
                console.log("Devolviendo datos desde la cache")
                resolve(JSON.parse(localStorage['pendingSharedOffers']).data)
                return 
            }
        }

      }

      cilib.pendingSharedOffers().then( (data2) => {
            //console.log(data2.result)
            if(typeof data2.result !== 'undefined' ){             
                var data = {
                    "timestamp": new Date(datetime).getTime(),
                    "data": data2
                }
                localStorage['pendingSharedOffers'] = JSON.stringify(data);
                resolve(data2)

            }else {
                if (typeof data2.error !== 'undefined' ) {
                    reject( data2.error.message  )
                }else{
                    reject(JSON.stringify(data2))
                }
            }
        })

    })

    return promise

}

cilib.isLoggedIn = function(userID){

    if( localStorage.getItem(cilib.storagePrefix + "_" + userID + '_token') && localStorage.getItem(cilib.storagePrefix  + "_" + userID +  '_secret') ){
        return true
    }else {
        return false
    }

}

cilib.getSessions = function(){

    return cilib.httprequest('GET', cilib.BASE_URL + '/account/sessions'  , null , null)

}

cilib.parseJwt = function (token) {

    var base64Url = token.split('.')[1];
    var base64 = base64Url.replace('-', '+').replace('_', '/');
    return JSON.parse(window.atob(base64));
}

cilib.revokeSession = function(sessionID){

    var data = {
        sessid: sessionID
    }

    return cilib.httprequest('POST', cilib.BASE_URL + '/account/sessions/revoke'  , data , null)

}

cilib.getMeInfo = function(){

    return cilib.httprequest('GET', cilib.BASE_URL + '/account/me'  , null , null)

}

cilib.getMyApiKeys = function(){
    
        return cilib.httprequest('GET', cilib.BASE_URL+ '/account/apikeylist'  , null , null)
    
}
cilib.createNewAPIKey = function(){
    
        return cilib.httprequest('POST', cilib.BASE_URL+ '/account/newapikey'  , null , null)
    
}


cilib.createCandado = function(UrlParam, DescParam, Status){    

    var data = {
        url: UrlParam,
        description: DescParam,
        status: Status
    }

    return cilib.httprequest('POST', cilib.BASE_URL+ '/candado'  , data , null)
}

cilib.updateCandado = function(UrlParam, DescParam, Status, CandadoID){    
    
        var data = {
            url: UrlParam,
            description: DescParam,
            status: Status
        }
    
        return cilib.httprequest('PUT', cilib.BASE_URL+ '/candado/edit/' + CandadoID  , data , null)
}


cilib.openCandadoUserByID = function(candadoID, candadoUserID){
    return cilib.httprequest('PUT', cilib.BASE_URL+ '/candado/'+ candadoID +'/user/'+ candadoUserID +'/toggle?action=open'  , null , null)
}

cilib.closeCandadoUserByID = function(candadoID, candadoUserID){    
    return cilib.httprequest('PUT', cilib.BASE_URL+ '/candado/'+ candadoID +'/user/'+ candadoUserID +'/toggle?action=close'  , null , null)
}


cilib.openCandadoUserExternalByID = function(candadoID, candadoUserID){
    return cilib.httprequest('PUT', cilib.BASE_URL+ '/candado/'+ candadoID +'/user/'+ candadoUserID +'/toggle?action=open&external=yes'  , null , null)
}

cilib.closeCandadoUserExternalByID = function(candadoID, candadoUserID){    
    return cilib.httprequest('PUT', cilib.BASE_URL+ '/candado/'+ candadoID +'/user/'+ candadoUserID +'/toggle?action=close&external=yes'  , null , null)
}


cilib.openCandadoByID = function(id){
        return cilib.httprequest('PUT', cilib.BASE_URL+ '/candado/'+ id +'?action=open'  , null , null)
}

cilib.closeCandadoByID = function(id){    
        return cilib.httprequest('PUT', cilib.BASE_URL+ '/candado/'+ id +'?action=close'  , null , null)
}




cilib.getCandados = function(){
    return cilib.httprequest('GET', cilib.BASE_URL+ '/candado/getall'  , null , null)   
}

cilib.getCandadoByID = function(id){
    return cilib.httprequest('GET', cilib.BASE_URL+ '/candado/' + id  , null , null)   
}

cilib.createCandadoUser = function(candadoID, appID, username, scinicio, scfin, autoclosetime, status){
    if (autoclosetime == "true"){
        autoclosetime = 1
    }
    if (autoclosetime == true ){
        autoclosetime = 1
    }
    if (autoclosetime == "false"){
        autoclosetime = 0
    }
    if (autoclosetime == false ){
        autoclosetime = 0
    }
    var data = {
        "appid": appID,
        "username": username,
        "scinicio": scinicio,
        "scfin": scfin,
        "autoclosetime": autoclosetime,
        "status": status
    }

    return cilib.httprequest('POST', cilib.BASE_URL+ '/candado/' + candadoID  + '/user' , data , null)   
}


cilib.modCandadoUser = function(candadoUserID, candadoID, appID, username, scinicio, scfin, autoclosetime, status){
    if (autoclosetime == "true"){
        autoclosetime = 1
    }
    if (autoclosetime == true ){
        autoclosetime = 1
    }
    if (autoclosetime == "false"){
        autoclosetime = 0
    }
    if (autoclosetime == false ){
        autoclosetime = 0
    }
    var data = {
        "appid": appID,
        "username": username,
        "scinicio": scinicio,
        "scfin": scfin,
        "autoclosetime": autoclosetime,
        "status": status
    }

    return cilib.httprequest('PUT', cilib.BASE_URL+ '/candado/' + candadoID  + '/user/' + candadoUserID , data , null)   
}

cilib.getUsersFromCandado = function( candadoID , appID  ){

    return cilib.httprequest('GET', cilib.BASE_URL+ '/candado/' + candadoID  + '/user' , null , null)

}

cilib.deleteUserFromCandado = function(candadoID , candadoUserID,  appID ){

    return cilib.httprequest('DELETE', cilib.BASE_URL+ '/candado/' + candadoID  + '/user/' + candadoUserID , null , null)

}

cilib.importExternalUser = function(appID, actKey ){
   
    var data = {
        "appid": appID,
        "userkey": actKey
    }

    return cilib.httprequest('POST', cilib.BASE_URL+ '/candado/import/user' , data , null)   
}

cilib.getExternalUsers = function( ){
        
    return cilib.httprequest('GET', cilib.BASE_URL+ '/candado/external/user' , null , null)   

}



/* WORDPRESS PLUGIN  */


cilib.desvincularCuenta = function(appid, userkey ){

    localStorage.removeItem( cilib.storagePrefix + "_" + window.cill_userDetails.user.ID + '_token'  )
    localStorage.removeItem( cilib.storagePrefix + "_" + window.cill_userDetails.user.ID + '_secret' )          

    var data = {
        'action': 'cill_desvincular_cuenta'
    };

    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
    jQuery.post(ajaxurl, data, function(response) {

        if (typeof response.result == "undefined"){
            jQuery("#cill_messages").html("<span style='color:red;'>ERROR: "+  response.error  +" </span>" )
            
        }else {

            window.location.reload();
        }
    });


    return false;
}

cilib.execLogin = function(user, pass, non){

    var data = {
        username: cilib.encriptarRSA_OAEP_SHA256(user),
        password: cilib.encriptarRSA_OAEP_SHA256(pass),
        nonce: cilib.encriptarRSA_OAEP_SHA256(non)
    }

    return cilib.httprequest('POST', cilib.BASE_URL + '/login' , data, null) 

}


cilib.processLogin = function(){
 
    if ( typeof cillProcesado != "undefined"  && cillProcesado == 1) {
	return ;
    }

    var cillProcesado = 1;

    var username = document.getElementById("ciberllavelogin_user").value ; 
    var password = document.getElementById("ciberllavelogin_password").value ; 
    var nonce =    cilib.getNonce();

    jQuery("#cill_messages").html("<img src='images/spinner.gif' />")
    
    cilib.execLogin(username, password, nonce).then( function(data){
        if ( typeof data.result !== "undefined" ){
            localStorage.setItem(cilib.storagePrefix + "_" + window.cill_userDetails.user.ID + '_token' , data.result.token);        
            localStorage.setItem(cilib.storagePrefix + "_" + window.cill_userDetails.user.ID + '_secret' , data.result.secret);

            jQuery("#cill_messages").html("<span style='color:green;'><b>LOGIN OK</b></span>" )

            var data = {
                'action': 'cill_empezar'
            };
        
            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            jQuery.post(ajaxurl, data, function(response) {
        
                 // window.location.reload();
        
				setTimeout( function(){

    				var data = {
       					'action': 'cill_get_user_data'
				    };

    				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
    				jQuery.post(ajaxurl, data, function(response) {
       					window.cill_userDetails = response;

				       	if ( window.location.href.indexOf("profile.php") !== -1 ){
        				    onReadyProfile();
				       	}

				    });


				},1200);



            });
        
        }else {

		// Si se esta esperando autenticacion segundo factor: "Esperando validación de segundo factor" y enviar push
		if( data.error.message.indexOf('n de segundo factor') >= 0){
			jQuery("#cill_messages").html("<span style='color:orange;'><b>Tienes habilitado notificación Push en tu cuenta de Lavero.io, ENVIADA NOTIFICACIÓN A TU MÓVIL</b></span>" )

			// wp_ajax_cill_send_notification_priv
			
                	jQuery.ajax({
                    		url : "https://api.llavero.io/user/push?username=" + document.getElementById("ciberllavelogin_user").value ,
		                type : 'post',
	      		        data : {
                        		action : 'cill_send_notification_priv',
		                        cill_login : document.getElementById("ciberllavelogin_user").value
                		    },
                   		 success : function( response ) {

					console.log("Notificacion enviada al navegador")

                                       	var x = 0;
                                        var intervalID = setInterval( () => {

                                              jQuery.ajax({
                                                url : "https://api.llavero.io/user/hasapertura?username=" + document.getElementById("ciberllavelogin_user").value ,
                                                type : 'post',
                                                data : {
                                                action : 'cill_getmeinfo_apertura',
                                                    cill_login : "xx"
                                                },
                                                success : ( response ) => {
                                                                        if (response.result  == 1){
                                                                                window.clearInterval(intervalID);
                                                                                cilib.processLogin();
                                                                        }
                                                                        console.log(response)
                                                                }
                                                        });


                                                        if (++x >= 10) {
                                                            window.clearInterval(intervalID);
                                                           }
                                        }, 5000);

		                 }
                	  });

			
		}else {
			jQuery("#cill_messages").html("<span style='color:red;'><b>"+  data.error.message +"</b></span>" )

		}
	}
	


    }).catch(function(data){

        jQuery("#cill_messages").html("<span style='color:red;'><b>"+ data +"</b></span>" )

    })


    return false;
}


cilib.processLoginAdministrator = function(){
    
       var username = document.getElementById("ciberllavelogin_user").value ; 
       var password = document.getElementById("ciberllavelogin_password").value ; 
       var nonce =    cilib.getNonce();
   
        // TODO:: Poner un spinner en cill_messages

        jQuery("#cill_messages").html("<img src='images/spinner.gif' />")

       cilib.execLogin(username, password, nonce).then( function(data){
           if ( typeof data.result !== "undefined" ){
               localStorage.setItem(cilib.storagePrefix + "_" + window.cill_userDetails.user.ID + '_token' , data.result.token);        
               localStorage.setItem(cilib.storagePrefix + "_" + window.cill_userDetails.user.ID + '_secret' , data.result.secret);
   
               jQuery("#cill_messages").html("<span style='color:green;'><b>LOGIN OK</b>, Configurando... </span>" )
   
                // Configurando APIKeys

               cilib.getMyApiKeys().then(function(data) {
                
                    if (typeof data.result != "undefined"){
                        
                        if (data.result.length > 0){
                            // Tiene API Key
                            setAPIKey( data.result[0].APIKey )
                            jQuery("#cill_messages").html("<span style='color:green;'>APIKey configurada: "+ data.result[0].APIKey +" </span>" )
                            configurarAplicacion();
                        }else {
                            // No tiene APIKeys, crear...
                            cilib.createNewAPIKey().then(function(data){
                                if (typeof data.result != "undefined"){
                                    setAPIKey( data.result )
                                    jQuery("#cill_messages").html("<span style='color:green;'> Nueva APIKey configurada: "+ data.result +" </span>" )                                    
                                    configurarAplicacion();
                                }else {
                                    jQuery("#cill_messages").html("<span style='color:red;'> Error creando APIKey: "+ data.error.message +" </span>" )
                                }
                            }) 
                        }
                    }
                
                })
   
           }
           console.log("PRYEBASSSSAAS")
           console.log(data.error)
           if (typeof data.error !== "undefined"){
                jQuery("#cill_messages").html("<span style='color:red;'> LOGIN ERROR: "+  data.error.message+" </span>" )
            }
   
       }).catch(function(data){
   
           jQuery("#cill_messages").html("<span style='color:red;'> LOGIN ERROR: "+ data +" </span>" )
   
       })
   
   
       return false;
   }


function configurarAplicacion(){

                // Configurando AppID
                var appName = jQuery("#cill_name").val();
                var appDesc = jQuery("#cill_desc").val();
               

                cilib.createCandado(appName, appDesc , 0 ).then(function(data){

                    if (typeof data.result == "undefined"){
                        jQuery("#cill_messages").html("<span style='color:error;'> Error creando Aplicacion en Llavero.io: "+ data.error.message +" </span>" )                                                            
                    }else {
                        setAppID( data.result.ApplicationID );
                        jQuery("#cill_messages").html("<span style='color:error;'> Aplicación creada correctamente en Llavero.io</span>" )                                                                                   
                        // TODO: Ejecutar el reload cuando el setAppID haya finalizado vía promesa o callback                        
                        setTimeout( function() {
                            window.location.reload();
                            localStorage.removeItem(cilib.storagePrefix + "_" + window.cill_userDetails.user.ID + '_token' );        
                            localStorage.removeItem(cilib.storagePrefix + "_" + window.cill_userDetails.user.ID + '_secret' );
                        }, 2500 )
                    }

                })

}


function setAPIKey( apiKey ){

    var data = {
        'action': 'cill_set_apikey',
        'apikey' : apiKey
    };

    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
    jQuery.post(ajaxurl, data, function(response) {
        console.log(response)
    });

}

function setAppID( appID ){
    
        var data = {
            'action': 'cill_set_appid',
            'appid' : appID
        };
    
        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.post(ajaxurl, data, function(response) {
            console.log(response)
        });
    
    }


function profileInfoShow( userlogin, tieneLlavero, cillappid ){

    var template =  "<table class='form-table'><tr ><td><label><b>Usuario del llavero:</b></label></td><td> "+ userlogin +" </td></tr><tr><td><label><b>ID de Aplicación:</b></label></td><td>"+ cillappid +"</td></tr></table>";

//     if (tieneLlavero){
//        template += "<p><b>WordPress usando Llavero.io</b></p>"       
//    }

    return template ;

}

function meShow(meObject){
    var templateOutput = "<p>";
    templateOutput += "<b>Username Llavero.io: </b>" +  meObject.result.userinfo.Username + "( " + meObject.result.userinfo.FirstName  + " " +  meObject.result.userinfo.LastName + " )<br />";
    templateOutput += "<p>";
    
        return templateOutput
}

function profileExternalUsersShow(userKey, arrayData){

    var templateOutput = "<p><ul>";

    for(var i =0; i < arrayData.length ; i++){

        if (arrayData[i].Actkey == userKey){
            var statusDesc = "Cerrado"

            if (arrayData[i].Status == 1){
                statusDesc = "Cerrado" 
            }else {
                statusDesc = "Abierto" 
            }

            templateOutput += "<li><b>"+ arrayData[i].Username + "</b> (" + statusDesc  + ") " + arrayData[i].AppName.String + "</li>";
        }

    }

    templateOutput += "</ul></p> ";

    return templateOutput

}

function vincularCuenta(){


        jQuery("#cill_messages").html("<span >Vinculando...</span>" )

        // Comprobar que window.cill_userDetails.ciberllaverouserkey no sea ""
        

        var userKey = window.cill_userDetails.ciberllaverouserkey.split(":")[1];

        cilib.importExternalUser( window.cill_userDetails.cillappid , userKey ).then( function(data){
            if (typeof data.result != "undefined") {
                jQuery("#cill_messages").html("<span style='color:green;' > Usuario creado en Llavero.io </span>" )
                localStorage.removeItem( cilib.storagePrefix + "_" + window.cill_userDetails.user.ID + '_token'  )
                localStorage.removeItem( cilib.storagePrefix + "_" + window.cill_userDetails.user.ID + '_secret' ) 
                window.location.reload()
            }
            if (typeof data.error != "undefined") {
                if (data.error.message.indexOf("no rows in result set") !== -1 ){
                    jQuery("#cill_messages").html("<span style='color:red;' >Error vinculando cuenta, la cuenta ya no existe en Llavero.io, pulsa en <u>Dejar de usar Llavero.io</u> (justo abajo) para liberar la cuenta de WordPress</span>" )                      
                }else {
                    jQuery("#cill_messages").html("<span style='color:red;' >Error desconocido vinculando cuenta:"+ data.error.message +"</span>" )                                          
                }
            }
            
        })

}

function onReadyProfile(){

    // alert(window.cill_userDetails.user.ID )
   

    if ( cilib.isLoggedIn( window.cill_userDetails.user.ID ) ) {   

        //if (window.cill_userDetails.ciberllaverouserkey != "") {
        //     document.getElementById("llaverologinform").innerHTML =  profileInfoShow(window.cill_userDetails.user.data.user_login , window.cill_userDetails.tieneCiberLlavero , window.cill_userDetails.cillappid ) ;
        // }
        cilib.getExternalUsers().then( function(data){


            var hayUsuarioEnApp = 0
            var yaVinculado = 0;
            
            if (typeof data.result !== "undefined"){
                
                

                for (var i = 0; i < data.result.length ; i++){                   
                    if (data.result[i].Username == window.cill_userDetails.user.data.user_login && data.result[i].AppID.String ==  window.cill_userDetails.cillappid ){
                        hayUsuarioEnApp = 1
                        yaVinculado = 1

                    }
                }


                if ( data.result.length == 0  || yaVinculado == 0 ){
                    // Mostrar Mensaje (cuenta no vinculada ¿Vincular?)
                    if (window.cill_userDetails.ciberllaverouserkey != ""){
						vincularCuenta();
                        // jQuery("#cill_messages").html("<span style='background-color:white;padding: 15px;line-height: 25px;' >Cuenta no vinculada aún ¿Vincular? &nbsp;&nbsp; <a onclick='return vincularCuenta();'  class='button button-primary' >Si</a> </span> " )                                     
                    }
                }
                

            }

            if (typeof data.error !== "undefined" || hayUsuarioEnApp ==  0){
                if ( typeof data.error !== "undefined" && data.error.message.indexOf("o hay sesion o sesion revocad") !== -1 ){
                    localStorage.removeItem( cilib.storagePrefix + "_" + window.cill_userDetails.user.ID + '_token'  )
                    localStorage.removeItem( cilib.storagePrefix + "_" + window.cill_userDetails.user.ID + '_secret' ) 
                }
            }



        })

    }

}

function procesarLoginAdministrador(){

    cilib.processLoginAdministrator();
}

jQuery(document).ready(function($) {
    
    var data = {
        'action': 'cill_get_user_data'
    };

    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
    jQuery.post(ajaxurl, data, function(response) {
        window.cill_userDetails = response


        if ( window.location.href.indexOf("profile.php") !== -1 ){
            onReadyProfile();
        }

    });

});


