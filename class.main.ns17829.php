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

require_once dirname(__FILE__).'/class.node_base.ns8054.php';
require_once dirname(__FILE__).'/class.low_level_error.ns28655.php';
require_once dirname(__FILE__).'/class.site_error.ns14329.php';
require_once dirname(__FILE__).'/class.not_authorized_error.ns3300.php';

class main__ns17829 {
    public function __construct() {}
    
    public function _main__get_arg($arg_name) {
        if(array_key_exists($arg_name, $_GET)) {
            $arg_value = stripslashes($_GET[$arg_name]);
            
            return $arg_value;
        } else {
            return NULL;
        }
    }
    
    public function _main__init_session() {
        $error_msg = 'Ошибка открытия HTTP-сессии';
        
        $lifetime = 60 * 60 * 24 * 7 * 10; // 10 недель
        
        session_set_cookie_params($lifetime);
        session_cache_expire($lifetime / 60);
        
        $success = @session_start();
        if(!$success) {
            throw new low_level_error__ns28655($error_msg);
        }
        
        $success = @session_regenerate_id();
        if(!$success) {
            throw new low_level_error__ns28655($error_msg);
        }
        
        if(!array_key_exists('post_key', $_SESSION)) {
            $_SESSION['post_key'] = 
                rand().':'.rand().':'.rand().':'.rand();
        }
        
        if(!array_key_exists('authorized', $_SESSION)) {
            $_SESSION['authorized'] = FALSE;
        }
    }
    
    public function _main__run() {
        try {
            $this->_main__init_session();
            
            $node = $this->_main__get_arg('node');
            if($node == NULL) {
                $node = 'home';
            }
            
            $environ = array(
                // ...
            );
            
            try {
                switch($node) {
                case 'home':
                    require_once dirname(__FILE__).'/class.home_node.ns25120.php';
                    
                    $node = new home_node__ns25120($environ);
                    
                    break;
                
                case 'error':
                    require_once dirname(__FILE__).'/class.error_node.ns21717.php';
                    
                    $node = new error_node__ns21717($environ);
                    
                    break;
                
                case 'auth':
                    require_once dirname(__FILE__).'/class.auth_node.ns2464.php';
                    
                    $node = new auth_node__ns2464($environ);
                    
                    break;
                
                case 'about':
                    require_once dirname(__FILE__).'/class.about_node.ns5982.php';
                    
                    $node = new about_node__ns5982($environ);
                    
                    break;
                
                case 'exit':
                    require_once dirname(__FILE__).'/class.exit_node.ns212.php';
                    
                    $node = new exit_node__ns212($environ);
                    
                    break;
                
                default:
                    throw new site_error__ns14329('Узел страницы не найден');
                }
            } catch(not_authorized_error__ns3300 $e) {
                $error = $e->getMessage();
                
                @header('Location: ?node=auth&error='.urlencode($error));
                
                return;
            } catch(site_error__ns14329 $e) {
                $error = $e->getMessage();
                
                @header('Location: ?node=error&error='.urlencode($error));
                
                return;
            }
            
            $redirect = $node->get_redirect();
            if($redirect) {
                @header('Location: '.$redirect);
                
                return;
            }
            
            $html = $node->get_html();
            
            @header('Content-Type: text/html;charset=UTF-8');
            @header('X-UA-Compatible: chrome=1');
            echo $html."\n";
        } catch(low_level_error__ns28655 $e) {
            $error = $e->getMessage();
            $message = sprintf('Низкоуровневая Ошибка: %s', $error);
            
            @header('Content-Type: text/plain;charset=UTF-8');
            if(array_key_exists('HTTP_REFERER', $_SERVER)) {
                @header('Refresh: 1;url='.$_SERVER['HTTP_REFERER']);
            }
            echo $message."\n";
        } catch(Exception $e) {
            $error = $e->getMessage();
            $message = sprintf('Неожидаемая Ошибка: %s', $error);
            
            @header('Content-Type: text/plain;charset=UTF-8');
            echo $message."\n";
        }
    }
    
    public function run() {
        return $this->_main__run();
    }
}



