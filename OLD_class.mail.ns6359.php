<?php

function mail__ns6359($to, $subject, $message, $kwargs=array()) {
	$headers = array();
	
	if(array_key_exists('from', $kwargs)) {
		$from_kwarg = $kwargs['from'];
		
		$headers[] = 'From: '.$from_kwarg;
	}
	
	if(array_key_exists('headers', $kwargs)) {
		$headers = array_merge($headers, $kwargs['headers']);
	}
	
	if($headers) {
		$result = mail(
			$to, 
			"=?UTF-8?B?".base64_encode($subject)."?=", 
			base64_encode($message),
			"Content-Type: text/plain;charset=UTF-8\nContent-Transfer-Encoding: base64",
			join("\n", $headers));
	} else {
		$result = mail(
			$to, 
			"=?UTF-8?B?".base64_encode($subject)."?=", 
			base64_encode($message),
			"Content-Type: text/plain;charset=UTF-8\nContent-Transfer-Encoding: base64");
	}
	
	return $result;
};



