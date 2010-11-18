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

function get_src__ns1609() {
    global $src__ns1609;
    
    if(!$src__ns1609) {
        $src__ns1609 = dirname(__FILE__);
    }
    
    return $src__ns1609;
}

function get_cgi_bin__ns1609() {
    global $cgi_bin__ns1609;
    
    if(!$cgi_bin__ns1609) {
        $cgi_bin__ns1609 = dirname(__FILE__).'/../cgi-bin';
    }
    
    return $cgi_bin__ns1609;
}

function get_var__ns1609() {
    global $var__ns1609;
    
    if(!$var__ns1609) {
        $var__ns1609 = dirname(__FILE__).'/../var';
    }
    
    return $var__ns1609;
}

function get_sessions__ns1609() {
    global $sessions__ns1609;
    
    if(!$sessions__ns1609) {
        $sessions__ns1609 = get_var__ns1609().'/sessions';
    }
    
    return $sessions__ns1609;
}

function get_htdocs__ns1609() {
    global $htdocs__ns1609;
    
    if(!$htdocs__ns1609) {
        $htdocs__ns1609 = dirname(__FILE__).'/../htdocs';
    }
    
    return $htdocs__ns1609;
}

