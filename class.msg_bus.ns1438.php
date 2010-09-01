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


function recv_msg__ns1438($msg_key, $ns, $def=NULL) {
    if(!$msg_key) {
        return $def;
    }
    
    if(array_key_exists('msg_bus', $_SESSION)) {
        $msg_bus = $_SESSION['msg_bus'];
    } else {
        return $def;
    }
    
    foreach($msg_bus as $i => $stored_msg) {
        $stored_msg_key = $stored_msg['msg_key'];
        $stored_ns = $stored_msg['ns'];
        
        if($stored_msg_key == $msg_key && $stored_ns == $ns) {
            // сообщение найдено!
            
            $params = $stored_msg['params'];
            
            // удалить это сообщение...
            unset($msg_bus[$i]);
            // ... и положить в список на первое место
            array_unshift($msg_bus, $stored_msg);
            
            $_SESSION['msg_bus'] = $msg_bus;
            
            return $params;
        }
    }
    
    return $def;
}

function send_msg__ns1438($ns, $params) {
    $size_limit = 100;
    
    if(array_key_exists('msg_bus', $_SESSION)) {
        $msg_bus = $_SESSION['msg_bus'];
        
        // поиск возможных совпадений:
        foreach($msg_bus as $i => $stored_msg) {
            $stored_ns = $stored_msg['ns'];
            $stored_params = $stored_msg['params'];
            
            if($stored_ns == $ns && $stored_params == $params) {
                // совпедение найдено! больше ничего делать не придётся
                
                $msg_key = $stored_msg['msg_key'];
                
                return $msg_key;
            }
        }
    } else {
        $msg_bus = array();
    }
    
    // чистка устаревших сообщений:
    while(sizeof($msg_bus) > $size_limit) {
        array_pop($msg_bus);
    }
    
    // создание новых данных о сообщении:
    $msg_key = sprintf('%s-%s', @time(), rand());
    $msg_bus['msg_key'] = $msg_key;
    $msg_bus['ns'] = $ns;
    $msg_bus['params'] = $params;
    
    $_SESSION['msg_bus'] = $msg_bus;
    
    return $msg_key;
}


