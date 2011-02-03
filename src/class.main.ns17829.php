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

require_once dirname(__FILE__).'/class.paths.ns1609.php';
require_once dirname(__FILE__).'/class.base_node.ns8054.php';
require_once dirname(__FILE__).'/class.low_level_error.ns28655.php';
require_once dirname(__FILE__).'/class.site_error.ns14329.php';
require_once dirname(__FILE__).'/class.not_authorized_error.ns3300.php';
require_once dirname(__FILE__).'/utils/class.gpc.ns2886.php';
require_once dirname(__FILE__).'/utils/class.msg_bus.ns1438.php';
require_once dirname(__FILE__).'/utils/class.cached_time.ns29922.php';

function get_session_save_path__ns17829() {
    $session_save_path = get_sessions__ns1609();
    
    if(!@file_exists($session_save_path)) {
        @mkdir($session_save_path, 0700);
    }
    
    return $session_save_path;
}

class main__ns17829 {
    public function __construct() {}
    
    public function _main__init_session() {
        $error_msg = 'Ошибка открытия HTTP-сессии';
        
        $lifetime = 60 * 60 * 24 * 7 * 4; // 4 недели, секунд
        $session_save_path = get_session_save_path__ns17829();
        
        $success = @ini_set('session.save_path', $session_save_path) !== FALSE;
        if(!$success) {
            throw new low_level_error__ns28655($error_msg);
        }
        
        $success = @ini_set('session.gc_probability', '1') !== FALSE;
        if(!$success) {
            throw new low_level_error__ns28655($error_msg);
        }
        
        $success = @ini_set('session.gc_divisor', '10') !== FALSE;
        if(!$success) {
            throw new low_level_error__ns28655($error_msg);
        }
        
        $success = @ini_set('session.gc_maxlifetime', strval($lifetime)) !== FALSE;
        if(!$success) {
            throw new low_level_error__ns28655($error_msg);
        }
        
        $success = @session_start();
        if(!$success) {
            throw new low_level_error__ns28655($error_msg);
        }
        
        $success = @setcookie(session_name(), session_id(), get_time__ns29922() + $lifetime);
        if(!$success) {
            throw new low_level_error__ns28655($error_msg);
        }
        
        // инициализируем изначальные значения сессии:
        
        if(!array_key_exists('post_token', $_SESSION)) {
            $_SESSION['post_token'] = new_token__ns29922();
        }
        
        if(!array_key_exists('authorized', $_SESSION)) {
            $_SESSION['authorized'] = FALSE;
        }
    }
    
    public function _main__run() {
        try {
            $this->_main__init_session();
            
            $node = get_get__ns2886('node');
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
                
                case 'search_items':
                    require_once dirname(__FILE__).'/class.search_items_node.ns8184.php';
                    
                    $node = new search_items_node__ns8184($environ);
                    
                    break;
                
                case 'item_detail_frame':
                    require_once dirname(__FILE__).'/class.item_detail_frame.ns13033.php';
                    
                    $node = new item_detail_frame__ns13033($environ);
                    
                    break;
                
                case 'error':
                    require_once dirname(__FILE__).'/class.error_node.ns21717.php';
                    
                    $node = new error_node__ns21717($environ);
                    
                    break;
                
                case 'error_frame':
                    require_once dirname(__FILE__).'/class.error_frame.ns22966.php';
                    
                    $node = new error_frame__ns22966($environ);
                    
                    break;
                
                case 'auth':
                    require_once dirname(__FILE__).'/class.auth_node.ns2464.php';
                    
                    $node = new auth_node__ns2464($environ);
                    
                    break;
                
                case 'new_items':
                    require_once dirname(__FILE__).'/class.new_items_node.ns16127.php';
                    
                    $node = new new_items_node__ns16127($environ);
                    
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
                    throw_site_error__ns14329(
                        'Узел страницы не найден',
                        array(
                            'return_back' => TRUE,
                        )
                    );
                }
            } catch(not_authorized_error__ns3300 $e) {
                $error_message = $e->getMessage();
                $msg_token = send_msg__ns1438(
                    'auth_node__ns2464::args',
                    array(
                        'error_message' => $error_message,
                    )
                );
                
                @header('Location: ?node=auth&msg_token='.urlencode($msg_token));
                
                return;
            } catch(site_error__ns14329 $e) {
                $message = $e->getMessage();
                $msg = array(
                    'message' => $message,
                );
                
                $error_options = get_error_options__ns14329($e);
                if(array_key_exists('return_back', $error_options) &&
                        $error_options['return_back']) {
                    if(array_key_exists('HTTP_REFERER', $_SERVER)) {
                        $msg['next'] = $_SERVER['HTTP_REFERER'];
                    }
                }
                if(array_key_exists('next', $error_options)) {
                    $msg['next'] = $error_options['next'];
                }
                
                $msg_token = send_msg__ns1438('error_node__ns21717::args', $msg);
                
                @header('Location: ?node=error&msg_token='.urlencode($msg_token));
                
                return;
            } catch(site_frame_error__ns14329 $e) {
                $message = $e->getMessage();
                $msg = array(
                    'message' => $message,
                );
                
                $msg_token = send_msg__ns1438('error_frame__ns22966::args', $msg);
                
                @header('Location: ?node=error_frame&msg_token='.urlencode($msg_token));
                
                return;
            }
            
            $redirect = $node->get_redirect();
            if($redirect) {
                @header('Location: '.$redirect);
                
                return;
            }
            
            $html = $node->get_html();
            
            @header('Content-Type: text/html;charset=utf-8');
            echo $html."\n";
        } catch(low_level_error__ns28655 $e) {
            $error = $e->getMessage();
            $message = sprintf('Низкоуровневая Ошибка: %s', $error);
            
            @header('Content-Type: text/plain;charset=utf-8');
            if(array_key_exists('HTTP_REFERER', $_SERVER)) {
                @header('Refresh: 1;url='.$_SERVER['HTTP_REFERER']);
            }
            echo $message."\n";
        } catch(Exception $e) {
            $error = $e->getMessage();
            $message = sprintf('Неожидаемая Ошибка: %s', $error);
            
            @header('Content-Type: text/plain;charset=utf-8');
            echo $message."\n";
        }
    }
    
    public function run() {
        return $this->_main__run();
    }
}

