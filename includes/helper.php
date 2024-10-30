<?php


class CiberLlaveroHelper 
{

    // public static $apiURL = "http://localhost:85";
    public static $apiURL = "https://api.llavero.io";

    public static function getApiURL() {

        $cill_apiURL = get_option("cill_apiurl");

		// Only return API_URL if it is a valid URL, else we return our hardcoded URL
		if (filter_var($cill_apiURL, FILTER_VALIDATE_URL) ) {
	        if ( !empty($cill_apiURL) && $cill_apiURL != "") {
	            return $cill_apiURL;
	        }
		}


        return self::$apiURL;
    }


    public static function curl_post_to_api($apiCallPath, $post_data) {

        $apiCallPath = trim($apiCallPath);

        $ch = curl_init();

        $cill_apikey = get_option("cill_apikey");
        $cill_appid = get_option("cill_appid");

        $post_data["appid"] = $cill_appid ;

        $apiURL = self::getApiURL();
        $finalURL = $apiURL . $apiCallPath ;


        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 8); //timeout in seconds

		// For compatibility, many host providers and servers does not have correctly configued rootCA and SSL verification fails :( 
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($ch, CURLOPT_URL, $finalURL );
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        


        $datastr = "";
        $i = 0;
        foreach ( $post_data as $key => $value){
            $i++;
            if ($i == count($post_data) ){
                $datastr .= $key . "=" . $value  ;
            }else {
                $datastr .= $key . "=" . $value . "&" ;
            }
        }
        

        curl_setopt($ch, CURLOPT_POSTFIELDS, $datastr  );
        curl_setopt($ch, CURLOPT_POST, 1);   

        $headers = array();
        //$headers[] = "Content-Type: application/x-www-form-urlencoded";
        $headers[] = "Apikey: " . $cill_apikey ;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $curlError = curl_getinfo($ch);
        

        $result = curl_exec($ch);
        if (curl_errno($ch)) {

            $info = array() ;
            $info["apistatus"] = "error" ;
            $info["curlerror"] = $curlError ;
            return array($info, array( "error" => "Curl Error" ) );
        }  

        curl_close ($ch);

        $resObj = json_decode($result);
        if (!isset($resObj->result) || isset($resObj->error) ){
            $info["apistatus"] = "error";
        }else{
            $info["apistatus"] = "success";
        }

        return array($info,$resObj);

    }

    public static function createCiberLlaveroUser($username){

		$username = sanitize_text_field( $username ) ;

        $post = [
            'username' => $username,
            'scinicio' => '00:00',
            'scfin'    =>  '00:00',
            'autoclosetime' => '0',
            'status'   =>  -1
        ];

        $result = self::curl_post_to_api("/cill/remote/user", $post);

        return $result;

    }

    public static function deleteUserFromLlavero($userkey){

        $post = [
            'userkey' => $userkey
        ];

        $result = self::curl_post_to_api("/cill/remote/user/delete", $post);

        return $result;


    }

    public static function getCiberLlaveroStats(){

        $post = array();
        $result = self::curl_post_to_api("/cill/remote/stats", $post);

        return $result;

    }

    public static function getUserStatusFromActKey($userkey){

		$userkey = sanitize_text_field( $userkey );
		if ( ! self::validateUserkey( $userkey )  ) {
			return array("error", "userkey not valid"); ;
		}

        $post = [
            'userkey' => $userkey
        ];

        $result = self::curl_post_to_api("/cill/remote/user/get", $post);

        return $result;
    }

    public static function closeUserByUserKey($userKey){

        $userkey = sanitize_text_field( $userkey );
        if ( ! self::validateUserkey( $userkey )  ) {
            return array("error", "userkey not valid"); ;
        }


        $post = [
            'userkey' => $userKey
        ];

        $result = self::curl_post_to_api("/cill/remote/user/close", $post);

        return $result;

    }


    public static function userHasCiberLlavero($userID){

        $ciberllaverouserkey = get_the_author_meta( 'ciberllaverouserkey', (int)$userID ) ;
        $cill_appid = get_option("cill_appid");


        if (!isset($ciberllaverouserkey) || empty($ciberllaverouserkey) || $ciberllaverouserkey == ""){
            return false;
        }

        if (strpos($ciberllaverouserkey, $cill_appid . ":" ) === 0) {
            return true;
        }else {
            return false;
        }

    }

    public static function notify2FA_User($userID){

        $ciberllaverouserkey = get_the_author_meta( 'ciberllaverouserkey', (int)$userID ) ;
        $cill_appid = sanitize_text_field( get_option("cill_appid") ) ;
        if ( ! self::validateAppID( $cill_appid )  ) {
            return array("error", "userkey not valid"); ;
        }

         $post = [
             'userkey' => $ciberllaverouserkey,
             'appid' =>   $cill_appid
        ];

        $result = self::curl_post_to_api("/cill/remote/2fa/notify", $post);

        return $result;

    }

    public static function meInfo($username) {

		$user = new stdClass;

        if ( username_exists( $username ) ) {
	 		$username = sanitize_user( $username ) ;
	        $user = get_user_by( 'login' , $username );
     	} else {

			$username = sanitize_email( $username ) ;
			if ( ! filter_var($username, FILTER_VALIDATE_EMAIL) ) {
			    
	            $info = array() ;
    	        $info["apistatus"] = "error" ;
        	    $info["validationerror"] = "NotValidEmail" ;
            	return array($info );

			}

			global $wpdb;
			$results = $wpdb->get_results( "select * from {$wpdb->prefix}users WHERE user_email = '". $username ."'", OBJECT );

			$user = $results[0];
		}


        $ciberllaverouserkey = sanitize_text_field( get_the_author_meta( 'ciberllaverouserkey', (int)$user->ID )  ) ;
        $cill_appid = sanitize_text_field( get_option("cill_appid") ) ;

        $parts = explode( ":", $ciberllaverouserkey );

        $post = [
                'userkey' => $parts[1],
                'appid' =>   $cill_appid
                ];
        $result = self::curl_post_to_api("/cill/remote/user/get", $post);   

        return $result;
    }

    public static function sendNotification($username) {


       	$user = new stdClass;

       	if ( username_exists( $username ) ) {
			$username = sanitize_user( $username ) ;
            $user = get_user_by( 'login' , $username );
        } else {

            $username = sanitize_email( $username ) ;
            if ( ! filter_var($username, FILTER_VALIDATE_EMAIL) ) {
                return $user;
            }

            global $wpdb;
            $results = $wpdb->get_results( "select * from {$wpdb->prefix}users WHERE user_email = '". $username ."'", OBJECT );

            $user = $results[0];
        }


        $ciberllaverouserkey = sanitize_text_field( get_the_author_meta( 'ciberllaverouserkey', (int)$user->ID ) ) ;
        $cill_appid = sanitize_text_field( get_option("cill_appid") );

        $parts = explode( ":", $ciberllaverouserkey );

        $post = [
            'userkey' => $parts[1],
            'appid' =>   $cill_appid
        ];
        $result = self::curl_post_to_api("/cill/remote/2fa/notify", $post);

        return $result;

    }



	/**
	* Validation functions
	*/

	// Validate AppID
    public static function validateAppID($appid) {

		// Example:  SD3qP5YztZxsw3yBalULo28K954nZ3Om
		if (preg_match("/^[A-Za-z0-9]+$/i", trim($appid) )) {
			return true;
		} else {
			return false;
		}

	}

	// Validate UserKey
    public static function validateUserkey($userkey) {

		// Example: UUTXxfSRsd23GV1g2
		if (preg_match("/^[A-Za-z0-9\:]+$/i", trim($userkey) )) {
			return true;
		} else {
			return false;
		}


    }

	// Validate ApiKey
    public static function validateApiKey($apikey) {

        // Example: DFHDF76ds-SDFKJf7df6-ASFSDF-saddsF723-asdSDHASF766
        if (preg_match("/^[A-Za-z0-9\-]+$/i", trim($apikey) )) {
            return true;
        } else {
            return false;
        }

    }

}



