<?php

require_once dirname(__FILE__).'/class.node.ns32056.php';
require_once dirname(__FILE__).'/class.not_authorized_error.ns3300.php';

class info_node__ns10325
		extends node__ns32056 {
	
	public function __construct() {
		parent::__construct();
		
		if(!$_SESSION['authorized']) {
			throw new not_authorized_error__ns3300(
				'Для входа в информационный раздел - необходима авторизация');
		}
	}
	
	public function _node__get_head() {
		$html = '';
		
		$html .= parent::_node__get_head();
		
		//$html .= 
		//	'<script type="text/javascript" src="/media/js/info_node.js"></script>'.
		//	'<link rel="stylesheet" type="text/css" href="/media/css/info_node.css" />';
		
		return $html;
	}
	
	public function _node__get_content() {
		$html = '';
		
		$html .= 
			'<div class="MaxWidth800Px TextAlignCenter">'.
				'---------- GALAXY 58 представляет--------<br />'.
				'<br />'.
				'Долгожданную техно-транс вечеринку GALAXY - МЕТЕОР!<br />'.
				'<br />'.
				'<br />'.
				'Ночь, где любители первоклассной электронной музыки смогут сполна насладиться техно и транс звучанием!<br />'.
				'<br />'.
				'Ночь, которая объединит только самых лучших людей нашего города!<br />'.
				'Ночь, которая зарядит энергией и позитивом!<br />'.
				'<br />'.
				'Тебя ожидает качественный звук и свет, безумные сеты от любимых Dj, великолепная атмосфера полёта, приятно удивляющие цены в баре и доброжелательная охрана.<br />'.
				'<br />'.
				'Управляющие полётом на Part 1 (19:00-...):<br />'.
				'Vladimir Borisov<br />'.
				'Semen Listva<br />'.
				'Snip Jest (Live)<br />'.
				'<br />'.
				'<br />'.
				'Пилоты в метеоритный дождь Part2 (...- 6:30)<br />'.
				'Tv@rdovsky<br />'.
				'Sharkoff (Msk)<br />'.
				'anN (Регион 68)<br />'.
				'<br />'.
				'<br />'.
				'Приноси с собой кальян и сможешь спокойно покурить на препати под отличную музыку!<br />'.
				'Адрес клуба: ул. Лермонтова,3 (Здание "Спорт Академии")<br />'.
				'<br />'.
				
									
			'</div>';
		
		return $html;
	}
}

class info_node_type__ns10325
		extends node_type__ns32056 {
	public function __construct($obj) {
		parent::__construct($obj);
	}
	public static function new_obj() {
		return new self(new info_node__ns10325());
	}
}


