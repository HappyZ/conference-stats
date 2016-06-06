<?php
// HappyZ
// Last updated: Jun. 5, 2016

$usr ;//'username (email) address';
$pwd ;//'password';

function isHotCRP( $content ) {
	return ( strpos( $content, 'HotCRP' ) != false );
}

function getFormFields($data) {
    if (preg_match('/(<form.*?<\/form>)/is', $data, $matches)) {
        $inputs = getInputs($matches[1]);
        return $inputs;
    } else {
        die('didnt find login form');
    }
}

function getInputs($form) {
    $inputs = array();

    $elements = preg_match_all('/(<input[^>]+>)/is', $form, $matches);

    if ($elements > 0) {
        for($i = 0; $i < $elements; $i++) {
            $el = preg_replace('/\s{2,}/', ' ', $matches[1][$i]);

            if (preg_match('/name=(?:["\'])?([^"\'\s]*)/i', $el, $name)) {
                $name  = $name[1];
                $value = '';

                if (preg_match('/value=(?:["\'])?([^"\'\s]*)/i', $el, $value)) {
                    $value = $value[1];
                }

                $inputs[$name] = $value;
            }
        }
    }

    return $inputs;
}

function initializeFetch( $ch, $url, $username, $password ) {
	// init
	$cookie_file_path = "./cookies.txt";
	$ua = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.36";
	// set headers
	$headers[] = "Accept: */*";
	$headers[] = "Connection: Keep-Alive";
	// curl options
	curl_setopt($ch, CURLOPT_HTTPHEADER,  $headers);
	curl_setopt($ch, CURLOPT_HEADER, 0);
// 	curl_setopt($ch, CURLOPT_NOBODY, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);         
	curl_setopt($ch, CURLOPT_USERAGENT, $ua); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path); 
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path); 
	// fetch the form fields
	curl_setopt($ch, CURLOPT_URL, $url);
	$content = curl_exec($ch);
	if ( ! isHotCRP( $content ) ) {
		return 1; // code 1: this is not hotcrp
	} 
	preg_match( "/\?post=(.{8})/i", $content, $postCode); // 8-char 
	if ( empty( $postCode ) ) {
		return 2; // code 2: postCode is empty
	}
	$postCode = $postCode[1];
	// ready to login
	$fields = getFormFields($content);
	$fields['action'] = 'login';
	$fields['signin'] = 'Sign in';
	$fields['email'] = $username;
	$fields['password'] = $password;
	$postFields = http_build_query($fields); 
 	// var_dump($fields);
	curl_setopt($ch, CURLOPT_URL, $url.'/?post='.$postCode);
	curl_setopt($ch, CURLOPT_POST, 1); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields); 
	$result = curl_exec($ch);
	if ( strpos( $result, 'signout=1' ) != false ) {
// 		echo "succeed to log in";
		return 0; // code 0: everything is fine
	} else {
// 		echo "cannot log in!";
// 	echo htmlspecialchars($result);
		return 3; // code 3: cannot log in
	}
}

function isPaperExist( $ch, $url ) {
	curl_setopt($ch, CURLOPT_URL, $url);
	$myContent = curl_exec($ch);
	// echo strpos( $myContent, 'No such paper' ).'<br />';
	if ( strpos( $myContent, 'No such paper' ) != false ) {
		return false;
	} else  {
		return true;
	}
}

function searchMaxPaperNum( $ch, $url, $prefix, $suffix = null, $maxNum = 2000, $minNum = 1 ) {
	if ($maxNum <= $minNum) {
		return $minNum;
	}
	// param initialization
	$paperURL = $url.$prefix;
	$myCurrent = ( $maxNum >> 1 ); // half
	// echo ' >> '.$minNum.'/'.$maxNum.'/'.$myCurrent.'<br/>';
	while ( ( $myCurrent != $minNum ) && ( $myCurrent != $maxNum ) ) {
 		// echo $minNum.'/'.$maxNum.'/'.$myCurrent.'<br />';
		if ( isPaperExist( $ch, $paperURL.$myCurrent.$suffix ) )
		{
			$minNum = $myCurrent;
		}
		else
		{
			$maxNum = $myCurrent;
		}
		$myCurrent = $minNum + ( ( $maxNum - $minNum ) >> 1 );
		// echo ' >> '.$minNum.'/'.$maxNum.'/'.$myCurrent.'<br/>';
	}
// 	echo $minNum.'/'.$maxNum.'/'.$myCurrent.'<br/>';
	return $myCurrent;
}

function fetchCurNum($url, $prefix, $suffix = null, $maxNum = 2000, $minNum = 1) {
	global $usr, $pwd;
	$curNum = 0;
	$ch = curl_init();
	if (is_null($usr) || is_null($pwd)) {
		curl_close($ch);
		return 'No username and password specified';
	}
	$err = initializeFetch( $ch, $url, $usr, $pwd );
	if ( $err == 1) {
		curl_close($ch);
		return 'This is NOT hotcrp';
	}
	if ( $err == 2) {
		curl_close($ch);
		return 'Cannot find 8-char postcode';
	}
	if ( $err == 3) {
		curl_close($ch);
		return 'Cannot log in';
	}
	$curNum = searchMaxPaperNum( $ch, $url, $prefix, $suffix, $maxNum, $minNum);
	curl_close($ch);
	return $curNum;
}

function fetchDeadline($url) {
	$ch = curl_init();
	$ua = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.36";
	// set headers
	$headers[] = "Accept: */*";
	$headers[] = "Connection: Keep-Alive";
	// curl options
	curl_setopt($ch, CURLOPT_HTTPHEADER,  $headers);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);         
	curl_setopt($ch, CURLOPT_USERAGENT, $ua); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
	curl_setopt($ch, CURLOPT_URL, $url.'/deadlines.php');
	$myContent = curl_exec($ch);
	// echo htmlspecialchars($myContent);
	preg_match('/reg":([0-9]{10})[,}]/',$myContent,$reg);
	if (empty($reg)) {
		curl_close($ch);
		return 'cannot find registration date';
	}
	preg_match('/sub":([0-9]{10})[,}]/',$myContent,$sub);
	if (empty($sub)) {
		curl_close($ch);
		return 'cannot find registration date';
	}
	$results[] = $reg[1];
	$results[] = $sub[1];
	curl_close($ch);
	return $results;
}


?>

