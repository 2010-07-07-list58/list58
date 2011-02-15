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

$real_ip_cache__ns5513 = NULL;

function get_real_ip__ns5513() {
    // получение реального ip-адреса
    // пояснение:
    //      если PHP-интерпретатор (Apache) работает в качестве backend для кэширующего прокси (frontend),
    //      то может так получиться, что переменная в которой хранитсья удалённый ip-адрес --
    //      на самом деле хранит ip-адрес этого самого кэширующего прокси (например 127.0.0.1)
    //
    //      чтобы избежать использования этого неправильного ip -- придётся просмотреть несколько источников
    
    // в будущем эту функцию возможно придётся подкорректировать для определённых frontend
    // (каждый из frontend может по разному предоставлять информацию об ip)
    
    global $real_ip_cache__ns5513;
    
    if($real_ip_cache__ns5513) {
        return $real_ip_cache__ns5513;
    } else {
        if(array_key_exists('HTTP_X_REAL_IP', $_SERVER)) {
            $real_ip = $_SERVER['HTTP_X_REAL_IP'];
        } else {
            $real_ip = $_SERVER['REMOTE_ADDR'];
        }
        
        $real_ip_cache__ns5513 = $real_ip;
        
        return $real_ip;
    }
}

