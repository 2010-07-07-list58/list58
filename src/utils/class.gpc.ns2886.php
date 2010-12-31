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

// библиотека для портируемого взаимодействия с данными: $_GET, $_POST, $_COOKIE
//
//      [КОСТЫЛЬ для хостингов. если хостинг настроен правильно
//       (а точнее сказать имеет НЕ ИСПОРЧЕННУЮ конфигурацию поумолчанию)
//       то этот костыль не нужен.. но разные хостинги настроены по разному]
//
// суть решаемой проблемы в том что в зависимости от конфигурации хостинга -- в некоторых случаях
// происходит излишнее (паразитное) добавление знаков "\" внутри данных
//

function get_magic_quotes_gpc__ns2886() {
    return function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc();
}

function get_get__ns2886($name) {
    if(array_key_exists($name, $_GET)) {
        if(get_magic_quotes_gpc__ns2886()) {
            $value = stripslashes($_GET[$name]);
        } else {
            $value = $_GET[$name];
        }
        
        return $value;
    }
}

function get_post__ns2886($name) {
    if(array_key_exists($name, $_POST)) {
        if(get_magic_quotes_gpc__ns2886()) {
            $value = stripslashes($_POST[$name]);
        } else {
            $value = $_POST[$name];
        }
        
        return $value;
    }
}

function get_cookie__ns2886($name) {
    if(array_key_exists($name, $_COOKIE)) {
        if(get_magic_quotes_gpc__ns2886()) {
            $value = stripslashes($_COOKIE[$name]);
        } else {
            $value = $_COOKIE[$name];
        }
        
        return $value;
    }
}

