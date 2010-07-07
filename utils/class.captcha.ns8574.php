<?php

require_once dirname(__FILE__).'/import-libs/recaptcha-php-1.11/recaptchalib.php';

global
	$captcha_public_key__ns8574,
	$captcha_private_key__ns8574,
	$captcha_last_error__ns8574;

$captcha_public_key__ns8574 = '6LcLHLoSAAAAABHep_EjLPM8lKxuBJozOobS9MNd';
$captcha_private_key__ns8574 = '6LcLHLoSAAAAAMX1SDPAXnUc9T_K9hYdOPLdnuBP ';
$captcha_last_error__ns8574 = NULL;

function captcha_get_html__ns8574() {
	global $captcha_public_key__ns8574;
	
	$html = '';
	
	$html .= 
		'<div style="min-height: 130px; ">'.
			recaptcha_get_html($captcha_public_key__ns8574, NULL).
		'</div>';
	
	return $html;
};

function translate_error__ns8574($error) {
	switch($error) {
	case 'incorrect-captcha-sol': return 'Введенна неправильная Каптча. Попробуйте ещё раз';
	default: return $error;
	}
}

function captcha_check_answer__ns8574($fields) {
	global
		$captcha_private_key__ns8574,
		$captcha_last_error__ns8574;
	
	if(!array_key_exists('recaptcha_challenge_field', $fields) ||
			!array_key_exists('recaptcha_response_field', $fields)) {
		$captcha_last_error__ns8574 = 'Ошибка передачи параметров';
		
		return FALSE;
	}
	
	$resp = recaptcha_check_answer ($captcha_private_key__ns8574,
		$_SERVER['REMOTE_ADDR'],
		$fields['recaptcha_challenge_field'],
		$fields['recaptcha_response_field']);

	if($resp->is_valid) {
		$captcha_last_error__ns8574 = NULL;
		
		return TRUE;
	} else {
		$captcha_last_error__ns8574 = translate_error__ns8574($resp->error);
		
		return FALSE;
	}
};


