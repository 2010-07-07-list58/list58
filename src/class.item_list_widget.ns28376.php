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

require_once dirname(__FILE__).'/utils/class.msg_bus.ns1438.php';

class item_list_widget__ns28376 {
    protected $_items;
    protected $_mod_perm = FALSE;
    
    public function __construct($items, $kwargs=NULL) {
        if($kwargs && array_key_exists('mod_perm', $kwargs)) {
            $this->_mod_perm = $kwargs['mod_perm'];
        }
        $this->_items = $items;
    }
    
    protected function _item_list_widget__get_detail_href($item_id) {
        $msg_token = send_msg__ns1438('mod_item_node__ns16127::args', array(
            'next' => '?'.(array_key_exists('QUERY_STRING', $_SERVER)?$_SERVER['QUERY_STRING']:''),
        ));
        
        $href = '?'.http_build_query(array(
            'node' => 'item_detail_frame',
            'item_id' => $item_id,
            'msg_token' => $msg_token,
        ));
        
        return $href;
    }
    
    protected function _item_list_widget__get_mod_href($item_id) {
        $msg_token = send_msg__ns1438('mod_item_node__ns16127::args', array(
            'next' => '?'.(array_key_exists('QUERY_STRING', $_SERVER)?$_SERVER['QUERY_STRING']:''),
        ));
        
        $href = '?'.http_build_query(array(
            'node' => 'mod_item',
            'item_id' => $item_id,
            'msg_token' => $msg_token,
        ));
        
        return $href;
    }
    
    protected function _item_list_widget__get_del_href($item_id) {
        $msg_token = send_msg__ns1438('mod_item_node__ns16127::args', array(
            'next' => '?'.(array_key_exists('QUERY_STRING', $_SERVER)?$_SERVER['QUERY_STRING']:''),
        ));
        
        $href = '?'.http_build_query(array(
            'node' => 'del_item',
            'item_id' => $item_id,
            'msg_token' => $msg_token,
        ));
        
        return $href;
    }
    
    protected function _item_list_widget__get_detail_link($item_id, $label) {
        $href = $this->_item_list_widget__get_detail_href($item_id);
        
        $html = sprintf('<a href="%s" rel="big_frame_fancybox">%s</a>', htmlspecialchars($href), htmlspecialchars($label));
        
        return $html;
    }
    
    protected function _item_list_widget__get_actions_html($item_id) {
        $htmls = array(
            '<a rel="big_frame_fancybox" href="'.htmlspecialchars($this->_item_list_widget__get_detail_href($item_id)).'">'.
                '<img class="Ico" src="/media/share/img/item_detail_ico.png" alt="Просмотреть" title="Просмотреть" />'.
            '</a>',
        );
        
        if($this->_mod_perm) {
            $htmls []=
                    '<a href="'.htmlspecialchars($this->_item_list_widget__get_mod_href($item_id)).'">'.
                        '<img class="Ico" src="/media/share/img/item_edit_ico.png" alt="Изменить" title="Изменить" />'.
                    '</a>';
            $htmls []=
                    '<a href="'.htmlspecialchars($this->_item_list_widget__get_del_href($item_id)).'">'.
                        '<img class="Ico" src="/media/share/img/item_del_ico.png" alt="Удалить" title="Удалить" />'.
                    '</a>';
        }
        
        $html = join(' | ', $htmls);
        
        return $html;
    }
    
    public function get_widget() {
        $html = '';
        
        $html .= '<table class="ItemTable MinWidth700Px Width100Per">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Id</th>';
        $html .= '<th>Фамилия И О</th>';
        $html .= '<th>Дата Рождения</th>';
        $html .= '<th>Телефон</th>';
        $html .= '<th>Доп. Описание</th>';
        $html .= '<th>Действия</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        
        $html .= '<tbody>';
        
        if($this->_items) {
            foreach($this->_items as $item) {
                $item_id = $item['id'];
                
                $html .= '<tr>';
                $html .= sprintf('<td>%s</td>', $this->_item_list_widget__get_detail_link(
                    $item_id,
                    $item_id
                ));
                $html .= sprintf('<td>%s</td>', $this->_item_list_widget__get_detail_link(
                    $item_id,
                    ($item['family_name']?$item['family_name']:'••••••••••').' '.
                    ($item['given_name']?mb_substr($item['given_name'], 0, 1, 'utf-8'):'•').' '.
                    ($item['patronymic_name']?mb_substr($item['patronymic_name'], 0, 1, 'utf-8'):'•')
                ));
                $html .= sprintf('<td>%s</td>', $this->_item_list_widget__get_detail_link(
                    $item_id,
                    ($item['birth_year'] || $item['birth_month'] || $item['birth_day'])?
                    sprintf('%02s.%02s.%s', $item['birth_day'], $item['birth_month'], $item['birth_year']):''
                ));
                $html .= sprintf('<td>%s</td>', $this->_item_list_widget__get_detail_link(
                    $item_id,
                    $item['phone']?$item['phone']:$item['phone2']
                ));
                $html .= sprintf('<td>%s</td>', str_replace(
                    "\n", '<br />',
                    htmlspecialchars(
                        mb_strlen($item['about'], 'utf-8') < 100?$item['about']:mb_substr($item['about'], 0, 100, 'utf-8').'...'
                    )
                ));
                $html .=
                        '<td class="TextAlignCenter">'.
                            $this->_item_list_widget__get_actions_html($item_id).
                        '</td>';
                $html .= '</tr>';
            }
            
            # DEBUG:
            #$html .= '<tr>';
            #$html .= '<td colspan="6"><pre>'.htmlspecialchars(print_r($this->_items, TRUE)).'</pre></td>';
            #$html .= '</tr>';
        } else {
            $html .= '<tr>';
            $html .= '<td colspan="6"><p class="TextAlignCenter Padding20Px">(Пусто)</p></td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody>';
        $html .= '</table>';
        
        return $html;
    }
}

