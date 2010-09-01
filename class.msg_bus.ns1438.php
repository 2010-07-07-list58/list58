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


$msg_bus_queue_size_limit__ns1438 = 1000;

function recv_msg__ns1438($msg_key, $ns, $def=NULL) {
    if(
            !$msg_key || 
            !array_key_exists('msg_bus', $_SESSION)
    ) {
        return $def;
    }
    
    foreach($_SESSION['msg_bus'] as $i => $stored_msg) {
        $stored_msg_key = $stored_msg['msg_key'];
        $stored_ns = $stored_msg['ns'];
        
        if($stored_msg_key == $msg_key && $stored_ns == $ns) {
            // сообщение найдено!
            
            $params = $stored_msg['params'];
            
            // удалить это сообщение...
            unset($_SESSION['msg_bus'][$i]);
            // ... и положить в список на первое место
            array_unshift($_SESSION['msg_bus'], $stored_msg);
            
            return $params;
        }
    }
    
    return $def;
}

function send_msg__ns1438($ns, $params) {
    global $msg_bus_queue_size_limit__ns1438;
    
    if(array_key_exists('msg_bus', $_SESSION)) {
        // поиск возможных совпадений:
        foreach($_SESSION['msg_bus'] as $i => $stored_msg) {
            $stored_ns = $stored_msg['ns'];
            $stored_params = $stored_msg['params'];
            
            if($stored_ns == $ns && $stored_params == $params) {
                // совпедение найдено! больше ничего делать не придётся
                
                $msg_key = $stored_msg['msg_key'];
                
                return $msg_key;
            }
        }
    } else {
        $_SESSION['msg_bus'] = array();
    }
    
    // чистка устаревших сообщений:
    while(sizeof($_SESSION['msg_bus']) >= $msg_bus_queue_size_limit__ns1438) {
        array_pop($_SESSION['msg_bus']);
    }
    
    // создание новых данных о сообщении:
    $msg_key = sprintf('%s-%s', @time(), rand());
    $msg = array(
        'msg_key' => $msg_key,
        'ns' => $ns,
        'params' => $params,
    );
    array_unshift($_SESSION['msg_bus'], $msg);
    
    return $msg_key;
}


