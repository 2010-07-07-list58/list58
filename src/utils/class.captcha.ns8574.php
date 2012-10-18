<?php

require_once dirname(__FILE__).'/../../var/class.recaptcha_conf.ns25302.php';
require_once dirname(__FILE__).'/import-libs/recaptcha-php-1.11/recaptchalib.php';

global $captcha_last_error__ns8574;

$captcha_last_error__ns8574 = NULL;

function get_captcha_last_error__ns8574 () {
    global $captcha_last_error__ns8574;
    
    return $captcha_last_error__ns8574;
}

function captcha_get_html__ns8574 () {
    $recaptcha_conf = recaptcha_conf__ns25302();
	$public_key = $recaptcha_conf['public_key'];
	
	$html = '';
	
	$html .= 
		'<div style="min-height: 130px; ">'.
			recaptcha_get_html($public_key, NULL, TRUE).
		'</div>';
	
	return $html;
};

function translate_error__ns8574 ($error) {
	switch($error) {
	case 'incorrect-captcha-sol': return 'Введенна неправильная Каптча. Попробуйте ещё раз';
	default: return $error;
	}
}

function captcha_check_answer__ns8574 ($fields) {
    global $captcha_last_error__ns8574;
    
    $recaptcha_conf = recaptcha_conf__ns25302();
    $private_key = $recaptcha_conf['private_key'];
	
	if (!array_key_exists('recaptcha_challenge_field', $fields) ||
			!array_key_exists('recaptcha_response_field', $fields)) {
		$captcha_last_error__ns8574 = 'Ошибка передачи параметров';
		
		return FALSE;
	}
	
	$resp = recaptcha_check_answer ($private_key,
		$_SERVER['REMOTE_ADDR'],
		$fields['recaptcha_challenge_field'],
		$fields['recaptcha_response_field']);
    
	if ($resp->is_valid) {
		$captcha_last_error__ns8574 = NULL;
		
		return TRUE;
	} else {
		$captcha_last_error__ns8574 = translate_error__ns8574($resp->error);
		
		return FALSE;
	}
};
