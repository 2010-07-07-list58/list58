<?php

/*
    This file is part of List58.

    List58 is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    List58 is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with List58.  If not, see <http://www.gnu.org/licenses/>.

*/

function _gcf__split_http_accept($http_accept) {
    $result = array();
    
    foreach(explode(',', $http_accept) as $splitted_level_0) {
        if($splitted_level_0) {
            $splitted_level_1 = explode(';', $splitted_level_0);
            
            if($splitted_level_1) {
                $splitted = trim($splitted_level_1[0]);
                
                $result []= $splitted;
            }
        }
    }
    
    return $result;
}

function _gcf__check_user_agent_with_gcf() {
    // проверка на то что в UserAgent содержится признак GoogleChromeFrame
    
    if(
        array_key_exists('HTTP_USER_AGENT', $_SERVER) && $_SERVER['HTTP_USER_AGENT'] &&
        strpos($_SERVER['HTTP_USER_AGENT'], ' chromeframe/') !== FALSE
    ) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function _gcf__activate_gcf() {
    global $_gcf__activate_gcf__was_activated;
    
    if(!$_gcf__activate_gcf__was_activated) {
        header('X-UA-Compatible: chrome=1');
        
        $_gcf__activate_gcf__was_activated = TRUE;
    }
}

function gcf__check_xhtml_support() {
    // проверяем факт того что броузер умеет отображать XHTML
    
    if(array_key_exists('HTTP_ACCEPT', $_SERVER) && $_SERVER['HTTP_ACCEPT']) {
        // броузер умеет показывать список поддерживаемых форматов...
        
        if(in_array('application/xhtml+xml', _gcf__split_http_accept($_SERVER['HTTP_ACCEPT']))) {
            return TRUE;
        } else {
            // ...но в этом списке нет XHTML.
            // поидее это УЖЕ говорит о том что нужно вывести сообщение об ошибке,
            // но возможно есть ещё какие-то исключительные ситуации. проверим и их!
            
            if(
                // покачто список исключительных ситуаций состоит только из одного пункта:
                _gcf__check_user_agent_with_gcf()
                // но возможно в будущем этот список будет расширен
            ) {
                // выяснили что есть поддержка GoogleChromeFrame.
                // активируем её!
                // (это заставит MsIE-броузер работать так как нужно)
                
                _gcf__activate_gcf();
                
                return TRUE;
            } else {
                // исключительных ситуаций нет! XHTML не поддерживается
                
                return FALSE;
            }
        }
    } else {
        // если броузер НЕ умеет показывать список поддерживаемых форматов,
        // то щитаем что нужный формат (XHTML в данном случае) он поддерживает.
        // презумция невиновности
        
        return TRUE;
    }
}

function gcf__show_xhtml_error() {
    // генерируем сообщение об ошибке и способе исправления
    
    $html = 
        '<!DOCTYPE html>'."\n".
        '<html>'.
            '<head>'.
                '<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />'.
                '<title>Ошибка: 400 Bad Request (Плохой запрос)</title>'.
            '</head>'.
            '<body>'.
                '<h1>Ошибка</h1>'.
                '<h2>400 Bad Request (Плохой запрос)</h2>'.
                '<p>Вашим броузером был передан некорректный (устаревший) тип запроса</p>'.
                '<p>'.
                    'Вероятнее всего это связанно с тем что используется сильно-устаревший броузер,<br />'.
                    'либо не установлен компонент ChromeFrame '.
                    '(<a href="http://code.google.com/intl/ru/chrome/chromeframe/">узнать больше</a>)'.
                    ' для броузера Microsoft Internet Explorer '.
                '</p>'.
                '<p></p>'.
                '<p><a href="http://www.google.com/chromeframe/eula.html">Установить ChromeFrame</a></p>'.
            '</body>'.
        '</html>';
    
    header('HTTP/1.0 400 Bad Request');
    header('Content-Type: text/html;charset=utf-8');
    echo $html;
}

