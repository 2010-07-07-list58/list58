// google-chrome-frame-for-microsoft-ie.js
// writed by Andrej A Antonov <polymorphm@gmail.com>
// version 2010-02-17 01:35
// licensed by LGPL version 3

(function() {
	var addEventListener = function(element, type, listener, useCapture) {
	// потомучто 'Microsoft IE' не поддерживает addEventListener()
		if(element.addEventListener != null) {
			element.addEventListener(type, listener, useCapture);
		} else if(element.attachEvent != null) {
			element.attachEvent('on' + type, listener);
		} else {
			throw "addEventListener() not implemented";
		}
	};
	
	var detectMicrosoftIE = function() {
	// детектирование 'Microsoft IE' по характерным несоответствиям стандартам
		
		if( // детектирование версий: '6.0', '7.0', '8.0'
			window.addEventListener == null && // <-- нет важной функции
			window.attachEvent != null // <-- но есть нестандартная альтернатива
		) {
			return true;
		}
		
		// <ЗДЕСЬ> в будущем возможно будет детектирование других версий
		
		return false;
	};
	
	var makeClearBothDiv = function() {
		var clearBothDiv = document.createElement('div');
		clearBothDiv.style.clear = 'both';
		
		return clearBothDiv;
	};
	
	var makeGoogleChromeFrameNotify = function() {
		var install = document.createElement('input');
		install.type = 'button';
		install.value = 'Установть';
		install.style.cssFloat = 'right'; // <-- 'Microsoft IE' не поддерживает это
		install.style.styleFloat = 'right'; // <-- специально для 'Microsoft IE'
		addEventListener(install, 'click', function(event) {
			window.location.assign('http://www.google.com/chromeframe/eula.html');
		}, false);
		
		var googleChromeFrame = document.createElement('span');
		googleChromeFrame.style.fontWeight = 'bold';
		googleChromeFrame.appendChild(
			document.createTextNode(
				'Chrome Frame'
			)
		);
		
		var learnMore = document.createElement('span');
		learnMore.style.cursor = 'pointer';
		learnMore.style.color = 'rgb(0,0,255)';
		learnMore.appendChild(
			document.createTextNode(
				'Узнать больше'
			)
		);
		addEventListener(learnMore, 'click', function(event) {
			window.location.assign('http://code.google.com/intl/ru/chrome/chromeframe/');
		}, false);
			
		
		var text = document.createElement('div');
		text.style.padding = '5px';
		text.appendChild(
			document.createTextNode(
				'У Вас не установлен компонент '
			)
		);
		text.appendChild(googleChromeFrame);
		text.appendChild(
			document.createTextNode(
				', необходимый для корректной работы Вашего браузера ('
			)
		);
		text.appendChild(learnMore);
		text.appendChild(
			document.createTextNode(')')
		);
		
		var notify = document.createElement('div');
		notify.style.padding = '3px';
		notify.style.font = '12px "DejaVu Sans", "Sans", sans-serif';
		notify.style.border = '1px rgb(245,245,181) outset';
		notify.style.background = 'rgb(245,245,181)';
		notify.style.color = 'rgb(0,0,0)';
		notify.appendChild(install);
		notify.appendChild(text);
		notify.appendChild(makeClearBothDiv());
		
		return notify;
	};
	
	var showNotify = function(notify) {
		if(document.body.firstChild != null) {
			document.body.insertBefore(notify, document.body.firstChild);
		} else {
			document.body.appendChild(notify);
		}
	};
	
	var main = function(event) {
		if(detectMicrosoftIE()) {
			var notify = makeGoogleChromeFrameNotify();
			
			showNotify(notify);
		}
	};
	
	addEventListener(window, 'load', main, false);
})();

