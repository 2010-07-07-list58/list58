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

require_once dirname(__FILE__).'/class.base_node.ns8054.php';

class node__ns21085 extends base_node__ns8054 {
    protected $_base_node__need_db = TRUE;
    
    protected $_node__main_menu;
    
    protected function _node__init_main_menu() {
        $menu = array();
        
        if($this->_base_node__authorized) {
            $menu[] = array(
                'menu_name' => 'Начало',
                'menu_link' => '?',
            );
            
            if($this->_base_node__is_permitted('view_items')) {
                // группа кнопок меню, связанных с Просмотром и Редактированием Данных
                
                if($this->_base_node__is_permitted('search_items')) {
                    // меню для тех кому разрешено искать Элементы Данных
                    
                    $menu[] = array(
                        'menu_name' => 'Поиск',
                        'menu_link' => '?node=search_items',
                    );
                }
                
                if($this->_base_node__is_permitted('new_items')) {
                    // меню для тех кому дополнительно разрешено и
                    //  создавать Новые Элементы Данных
                    
                    $menu[] = array(
                        'menu_name' => 'Новые Данные',
                        'menu_link' => '?node=mod_item',
                    );
                }
            }
            
            if($this->_base_node__is_permitted('multisession')) {
                $menu[] = array(
                    'menu_name' => 'Закрыть все сессии ['.$_SESSION['reg_data']['login'].']',
                    'menu_link' => sprintf(
                        '?node=exit&clean_all=1&post_token=%s',
                        urlencode($_SESSION['post_token'])
                    ),
                    'is_right' => TRUE,
                );
            }
            
            $menu[] = array(
                'menu_name' => 'Выход ['.$_SESSION['reg_data']['login'].']',
                'menu_link' => sprintf(
                    '?node=exit&post_token=%s',
                    urlencode($_SESSION['post_token'])
                ),
                'is_right' => TRUE,
            );
        } else {
            $menu[] = array(
                'menu_name' => 'Вход',
                'menu_link' => '?node=auth',
                'is_right' => TRUE,
            );
        }
        
        $menu[] = array(
            'menu_name' => 'О Системе', 
            'menu_link' => '?node=about',
            'is_right' => TRUE,
        );
        
        $this->_node__main_menu = $menu;
    }
    
    protected function _base_node__on_init() {
        parent::_base_node__on_init();
        
        $this->_node__init_main_menu();
    }
    
    protected function _node__get_title() {
        return 'List58';
    }
    
    protected function _node__get_head() {
        $html = '';
        
        $html .=
            '<meta charset="utf-8" />'.
            '<title>'.htmlspecialchars($this->_node__get_title()).'</title>'.
            '<link rel="shortcut icon" href="/media/share/favicon.png" />'.
            '<link rel="stylesheet" href="/media/share/css/style.css" />'.
            '<script src="/media/share/js/frame_exit.js"></script>'.
            '<script src="/media/share/import-lib/jquery/jquery-1.4.4.js"></script>'.
            '<link rel="stylesheet" href="/media/share/import-lib/fancybox/jquery.fancybox-1.3.4.css" />'.
            '<script src="/media/share/import-lib/fancybox/jquery.fancybox-1.3.4.js"></script>'.
            '<script src="/media/share/js/fancybox.js"></script>';
        
        return $html;
    }
    
    protected function _node__get_main_menu_widget() {
        $html = '';
        
        foreach($this->_node__main_menu as $menu_item) {
            $is_right = 
                array_key_exists('is_right', $menu_item)?
                $menu_item['is_right']:FALSE;
            
            $menu_item_html =
                '<a href="'.htmlspecialchars($menu_item['menu_link']).'" >'.
                    htmlspecialchars($menu_item['menu_name']).
                '</a>';
            
            if(!$is_right) {
                $html .=
                    '<div class="FloatLeft MarginLeft5Px">'.
                        $menu_item_html.
                    '</div>';
                $html .=
                    '<div class="FloatLeft MarginLeft5Px">'.
                        '|'.
                    '</div>';
            } else {
                $html .=
                    '<div class="FloatRight MarginRight5Px">'.
                        $menu_item_html.
                    '</div>';
                $html .=
                    '<div class="FloatRight MarginRight5Px">'.
                        '|'.
                    '</div>';
            }
        }
        
        $html .= '<div class="ClearBoth"></div>';
        
        return $html;
    }
    
    protected function _node__get_aside() {
        throw new abstract_function_error__ns8054();
    }
    
    protected function _node__get_body() {
        $html = '';
        
        $html .=
            '<table class="Width100Per Height100Per">'.
                '<tr>'.
                    '<td class="Padding10Px MarginColor">'.
                        $this->_node__get_main_menu_widget().
                    '</td>'.
                '</tr>'.
                '<tr>'.
                    '<td class="Height100Per Padding10Px">'.
                        '<table class="MarginAuto">'.
                            '<tr>'.
                                '<td>'.
                                    $this->_node__get_aside().
                                '</td>'.
                            '</tr>'.
                        '</table>'.
                    '</td>'.
                '</tr>'.
                '<tr>'.
                    '<td class="Padding10Px MarginColor"></td>'.
                '</tr>'.
            '</table>';
        
        return $html;
    }
    
    protected function _base_node__get_html() {
        $html = '';
        
        $html .=
            '<!DOCTYPE html>'."\n".
            '<html>'.
            '<head>'.
                $this->_node__get_head().
            '</head>'.
            '<body>'.
                $this->_node__get_body().
            '</body>'.
            '</html>';
        
        return $html;
    }
}

