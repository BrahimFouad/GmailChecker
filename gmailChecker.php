<?php
/*
  created by Guia Brahim Fouad ( https://twitter.com/BrahimFouad4 )
  based on : https://blog.0day.rocks/abusing-gmail-to-get-previously-unlisted-e-mail-addresses-41544b62b2
  thanks to x0rz
*/
namespace Gmail{

    class Checker {

        private $urlGmailCheck = 'https://mail.google.com/mail/gxlu?email=<valid_account>';
        private $debug = FALSE;
        private $urlDefaultOpts = [
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_TIMEOUT => 3,
            CURLOPT_VERBOSE => FALSE,
            CURLOPT_HEADER => TRUE,
            CURLOPT_FOLLOWLOCATION => FALSE,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; pl; rv:1.9) Gecko/2008052906 Firefox/3.0',
            CURLOPT_AUTOREFERER => FALSE,
            CURLOPT_NOBODY => TRUE,
            CURLOPT_HTTPPROXYTUNNEL => FALSE,
            CURLOPT_PROXY => ''
        ];

        public function __construct() {

            if(in_array('curl', @get_loaded_extensions()) === FALSE || extension_loaded('curl') === FALSE)
                trigger_error('CURL Lib not loaded',E_USER_ERROR );

            return TRUE;
        }

        public function exist($Email){

            if ( filter_var((string)$Email,FILTER_VALIDATE_EMAIL) === FALSE )
                return FALSE;

            if ( ($return = self::get(str_replace('<valid_account>',urlencode($Email),$this->urlGmailCheck))) === FALSE )
                return FALSE;

            if(preg_match('@set-cookie@is', $return) != FALSE)
                return TRUE;

            return FALSE;
        }

        private function get($url,array $opt = []) {

            $link = @curl_init($url);
            curl_setopt_array($link, ($this->urlDefaultOpts+$opt));
            if(($result = @curl_exec($link)) == FALSE ){
                if ( $this -> debug === TRUE ) {
                    $curl_errno = @curl_errno($link);
                    $curl_error = @curl_error($link);
                    trigger_error("cURL Error ($curl_errno): $curl_error", E_USER_ERROR );
                }
                @curl_close($link);
                return FALSE;
            }
            @curl_close($link);
            return $result;
        }
    }
}

namespace root {
    use Gmail\Checker;
    $A = new Checker();
    var_dump($A ->exist('guia.brahimfouad@gmail.com'));
}

?>
