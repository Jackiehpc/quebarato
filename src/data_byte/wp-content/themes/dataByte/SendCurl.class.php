<?php 

class SendCurl{

		/**
		* Open an url on https using curl and return content
		*
		* @author hatem <info@phptunisie.net>
		* @param string url            The url to open
		* @param string refer        Referer (optional)
		* @param mixed usecookie    If true, cookie.txt    will be used as default, or the usecookie value.
		* @return string
		*/
		public static function open_https_url($url,$refer = "", $usecookie = false) {
		    if ($usecookie) {
		        if (file_exists($usecookie)) {
		            if (!is_writable($usecookie)) {
		                return "Can't write to $usecookie cookie file, change file permission to 777 or remove read only for windows.";
		            }
		        } else {
		            $usecookie = "cookie.txt";
		            if (!is_writable($usecookie)){
		                return "Can't write to $usecookie cookie file, change file permission to 777 or remove read only for windows.";
		            }
		        }
		   
		    }
		
		    $ch = curl_init();
		   
		    curl_setopt($ch, CURLOPT_URL, $url);
		   
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		   
		    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		   
		    curl_setopt($ch, CURLOPT_HEADER, 1);
		   
		    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
		   
		    if ($usecookie) {
		        curl_setopt($ch, CURLOPT_COOKIEJAR, $usecookie);
		       
		        curl_setopt($ch, CURLOPT_COOKIEFILE, $usecookie);   
		    }
		   
		    if ($refer != "") {
		   
		        curl_setopt($ch, CURLOPT_REFERER, $refer );
		       
		    }
		   
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		   
		   $result =curl_exec ($ch);
		   
		   curl_close ($ch);
		   
		   return $result;
		}
};