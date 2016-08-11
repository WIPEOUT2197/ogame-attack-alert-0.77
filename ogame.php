<?php 

// Helpers

$universe = "http://ogame1304.de";

function ogame_get_session($login, $pass) {
	global $universe;

	$post_fields = array();
	$post_fields['login'] = $login;
	$post_fields['pass'] = $pass;
	$post_fields['v'] = '2';
	$post = http_build_query($post_fields);
	
	$login_url = "$universe/game/reg/login2.php"; 

	$curl = curl_init();
	curl_setopt_array(
		$curl, 
		array(
			CURLOPT_URL => $login_url,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $post,
			CURLOPT_RETURNTRANSFER => true,
			)
		);

	$response = curl_exec($curl);
	curl_close($curl);
	
	preg_match('/session=([^&]*)/', $response, $matches);

	return $matches[1];
}

function ogame_get_overview($session_token) {
	global $universe;

	$overview_url = "$universe/game/index.php?page=overview&session=$session_token&lgn=1";

	$curl = curl_init();
	curl_setopt_array(
		$curl,
		array(
			CURLOPT_URL => $overview_url,
			CURLOPT_RETURNTRANSFER => true,
			)
		);

	$response = curl_exec($curl);
	curl_close($curl);

	return $response;
}

function sendEmail($from, $to, $to_name, $subject, $title, $message) {
	$body = '
	<html>
	<head>
		<title>' . $title . '</title>
	</head>
	<body>
		<p>' . $message . '</p>
	</body>
	</html>
	';
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= "From: $from\r\n";
	$headers .= "To: $to_name <$to>\r\n";

	mail($to, $subject, $body, $headers);
}

// Beginning of real code

$session_token = ogame_get_session("username", "password");
$overview = ogame_get_overview($session_token);

if ($pos = strpos($overview, "flight attack")) {
	sendEmail(
		'OGAME ATTAQUE <attaque-ogame@thibaultmonteiro.fr>',
		'124643@supinfo.com',
		'Thibault',
		'[OGAME] - ATTAQUE EN COURS',
		'[OGAME] - ATTAQUE EN COURS',
		'UNE ATTAQUE EST EN TRAIN D AVOIR LIEU !'
	);
} else {
	sendEmail(
		'SONDE <sonde-ogame@thibaultmonteiro.fr>',
		'tmonte.sup@gmail.com',
		'Thibault',
		'[OGAME] - SONDE OK',
		'[OGAME] - SONDE OK',
		'LA SONDE EST TOUJOURS EN LIGNE'
	);
}

