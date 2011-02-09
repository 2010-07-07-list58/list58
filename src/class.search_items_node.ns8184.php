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
require_once dirname(__FILE__).'/class.node.ns21085.php';
require_once dirname(__FILE__).'/class.item_list_widget.ns28376.php';
require_once dirname(__FILE__).'/class.page_links_widget.ns22493.php';
require_once dirname(__FILE__).'/utils/class.msg_bus.ns1438.php';
require_once dirname(__FILE__).'/utils/class.parse_form.ns31025.php';
require_once dirname(__FILE__).'/utils/class.cached_time.ns29922.php';
require_once dirname(__FILE__).'/utils/class.mysql_tools.php';

class form_error__ns8184
        extends Exception {}

function split_str_to_words__ns8184($str, $kwargs=NULL) {
    $min_len = ($kwargs && array_key_exists('min_len', $kwargs))?
        $kwargs['min_len']:0;
    
    $words = array();
    
    if($str) {
        foreach(explode(' ', $str) as $raw_word) {
            $word = trim($raw_word);
            
            if($word && (!$min_len || mb_strlen($word, 'utf-8') >= $min_len)) {
                $words []= $word;
            }
        }
    }
    
    return $words;
}

function join_sqls__ns8184($op, $sqls, $kwargs=NULL) {
    $bkt = ($kwargs && array_key_exists('bkt', $kwargs))?
        $kwargs['bkt']:FALSE;
    
    $sql = join(sprintf(' %s ', $op), $sqls);
    
    if($bkt && $sql) {
        $sql = sprintf('(%s)', $sql);
    }
    
    return $sql;
}

class search_items_node__ns8184 extends node__ns21085 {
    protected $_base_node__need_db = TRUE;
    protected $_base_node__need_check_auth = TRUE;
    
    protected $_search_items_node__advanced_search_types = array(
        'Имя',
        'Фамилия',
        'Отчество',
        'Дата рождения',
        'Возраст от',
        'Возраст до',
        'Паспорт (серия, номер и кем выдан)',
        'Серия паспорта (строгий режим)',
        'Номер паспорта (строгий режим)',
        'Город',
        'Адрес',
        'Дополнительное описание',
        'Примечание',
        'Id',
    );
    protected $_search_items_node__show_form = TRUE;
    protected $_search_items_node__show_form_results = FALSE;
    protected $_search_items_node__mysql_microtime = NULL;
    protected $_search_items_node__message_html = '';
    
    protected $_search_items_node__general_search = array();
    protected $_search_items_node__sex_search = '';
    protected $_search_items_node__advanced_search_params = array();
    
    protected $_search_items_node__items_limit = 0;
    protected $_search_items_node__items_real_limit = 20;
    protected $_search_items_node__items_offset = 0;
    protected $_search_items_node__items_count;
    protected $_search_items_node__items;
    protected $_search_items_node__item_list_widget;
    protected $_search_items_node__page_links_widget;
    
    protected function _base_node__on_add_check_perms() {
        parent::_base_node__on_add_check_perms();
        
        $this->_base_node__add_check_perms(
            array(
                // требуется разрешение на поиск Элементов Данных:
                'search_items' => TRUE,
            )
        );
    }
    
    protected function _search_items_node__get_like_sql($sql_field, $str, $kwargs=NULL) {
        $min_len = ($kwargs && array_key_exists('min_len', $kwargs))?
            $kwargs['min_len']:0;
        $bkt = ($kwargs && array_key_exists('bkt', $kwargs))?
            $kwargs['bkt']:FALSE;
        $field_expr = ($kwargs && array_key_exists('field_expr', $kwargs))?
            $kwargs['field_expr']:FALSE;
        
        $sqls = array();
        
        if($field_expr) {
            $sql_field_sql = $sql_field;
        } else {
            $sql_field_sql = sprintf('`%s`', $sql_field);
        }
        
        foreach(split_str_to_words__ns8184($str, array('min_len' => $min_len)) as $word) {
           $sqls []= sprintf(
                '%s LIKE %s',
                $sql_field_sql,
                mysql_quote_like_expr_string($word, $this->_base_node__db_link)
            );
        }
        
        $sql = join_sqls__ns8184('OR', $sqls, array('bkt' => $bkt));
        
        return $sql;
    }
    
    protected function _search_items_node__get_where_sql() {
        $and_part_sqls = array();
        
        if($this->_search_items_node__general_search) {
            // критерий Обобщённый Поиск ('general_search')
            
            $and_part_general_search_sqls = array();
            foreach($this->_search_items_node__general_search as $general_search_word) {
                $or_part_general_search_sqls = array();
                
                if(is_numeric($general_search_word)) {
                    $or_part_general_search_sqls []= sprintf(
                        '`id` = \'%s\'',
                        mysql_real_escape_string($general_search_word, $this->_base_node__db_link)
                    );
                }
                
                $or_part_general_search_sqls []= sprintf(
                    '`given_name` LIKE %s',
                    mysql_quote_like_expr_string($general_search_word, $this->_base_node__db_link)
                );
                
                $or_part_general_search_sqls []= sprintf(
                    '`family_name` LIKE %s',
                    mysql_quote_like_expr_string($general_search_word, $this->_base_node__db_link)
                );
                
                $or_part_general_search_sqls []= sprintf(
                    '`patronymic_name` LIKE %s',
                    mysql_quote_like_expr_string($general_search_word, $this->_base_node__db_link)
                );
                
                try {
                    $general_search_word_day = parse_day__ns31025($general_search_word);
                } catch(parse_error__ns31025 $e) {
                    $general_search_word_day = NULL;
                }
                if($general_search_word_day) {
                    $or_part_general_search_sqls []= sprintf(
                        '`birth_year` = \'%s\' AND `birth_month` = \'%s\' AND `birth_day` = \'%s\'',
                        mysql_real_escape_string($general_search_word_day[0], $this->_base_node__db_link),
                        mysql_real_escape_string($general_search_word_day[1], $this->_base_node__db_link),
                        mysql_real_escape_string($general_search_word_day[2], $this->_base_node__db_link)
                    );
                }
                
                $or_part_general_search_sqls []= sprintf(
                    'CONCAT(`passport_ser`, `passport_no`) LIKE %s',
                    mysql_quote_like_expr_string($general_search_word, $this->_base_node__db_link)
                );
                
                $or_part_general_search_sqls []= sprintf(
                    '`residence_city` LIKE %s',
                    mysql_quote_like_expr_string($general_search_word, $this->_base_node__db_link)
                );
                
                $or_part_general_search_sqls []= sprintf(
                    '`residence` LIKE %s',
                    mysql_quote_like_expr_string($general_search_word, $this->_base_node__db_link)
                );
                
                $or_part_general_search_sqls []= sprintf(
                    '`phone` LIKE %s',
                    mysql_quote_like_expr_string($general_search_word, $this->_base_node__db_link)
                );
                
                $or_part_general_search_sqls []= sprintf(
                    '`phone2` LIKE %s',
                    mysql_quote_like_expr_string($general_search_word, $this->_base_node__db_link)
                );
                
                try {
                    $general_search_word_phone = normalize_phone__ns31025($general_search_word);
                } catch(parse_error__ns31025 $e) {
                    $general_search_word_phone = NULL;
                }
                if($general_search_word_phone) {
                    $or_part_general_search_sqls []= sprintf(
                        '`phone` = \'%s\'',
                        mysql_real_escape_string($general_search_word_phone, $this->_base_node__db_link)
                    );
                    $or_part_general_search_sqls []= sprintf(
                        '`phone2` = \'%s\'',
                        mysql_real_escape_string($general_search_word_phone, $this->_base_node__db_link)
                    );
                }
                
                $or_part_general_search_sqls []= sprintf(
                    '`about` LIKE %s',
                    mysql_quote_like_expr_string($general_search_word, $this->_base_node__db_link)
                );
                
                $or_part_general_search_sqls []= sprintf(
                    '`comments` LIKE %s',
                    mysql_quote_like_expr_string($general_search_word, $this->_base_node__db_link)
                );
                
                $and_part_general_search_sqls []= join_sqls__ns8184(
                        'OR', $or_part_general_search_sqls, array('bkt' => TRUE));
            }
            $and_part_sqls []= join_sqls__ns8184('AND', $and_part_general_search_sqls);
        }
        
        if($this->_search_items_node__sex_search) {
            // критерий Пол ('sex_search')
            
            if($this->_search_items_node__sex_search == 'Мужской') {
                $and_part_sqls []= '`sex` = 1';
            } elseif($this->_search_items_node__sex_search == 'Женский') {
                $and_part_sqls []= '`sex` = 2';
            }
        }
        
        foreach($this->_search_items_node__advanced_search_params as $advanced_search_param) {
            // критерии Дополнительных Параметров ('advanced_search_params')
            
            $search_type = $advanced_search_param['search_type'];
            $search_value = $advanced_search_param['search_value'];
            
            if($search_type == 'Имя') {
                $and_part_sqls []= sprintf(
                    '`given_name` LIKE %s',
                    mysql_quote_like_expr_string($search_value, $this->_base_node__db_link)
                );
            } elseif($search_type == 'Фамилия') {
                $and_part_sqls []= sprintf(
                    '`family_name` LIKE %s',
                    mysql_quote_like_expr_string($search_value, $this->_base_node__db_link)
                );
            } elseif($search_type == 'Отчество') {
                $and_part_sqls []= sprintf(
                    '`patronymic_name` LIKE %s',
                    mysql_quote_like_expr_string($search_value, $this->_base_node__db_link)
                );
            } elseif($search_type == 'Дата рождения') {
                try {
                    $search_value_day = parse_day__ns31025($search_value);
                } catch(parse_error__ns31025 $e) {
                    $search_value_day = NULL;
                }
                if($search_value_day) {
                    $and_part_sqls []= sprintf(
                        '`birth_year` = \'%s\' AND `birth_month` = \'%s\' AND `birth_day` = \'%s\'',
                        mysql_real_escape_string($search_value_day[0], $this->_base_node__db_link),
                        mysql_real_escape_string($search_value_day[1], $this->_base_node__db_link),
                        mysql_real_escape_string($search_value_day[2], $this->_base_node__db_link)
                    );
                } else {
                    $message = '\'Дата рождения\' указана неверно (параметр был игнорирован)';
                    
                    $this->_search_items_node__message_html .=
                            '<p class="ErrorColor TextAlignCenter">'.
                                htmlspecialchars($message).
                            '</p>';
                }
            } elseif($search_type == 'Возраст от') {
                $time = get_time__ns29922();
                $time_year = @date('Y', $time);
                $time_month = @date('n', $time);
                $time_day = @date('j', $time);
                
                $and_part_sqls []= sprintf(
                    '`birth_year` AND (\'%s\' - `birth_year` > \'%s\' OR \'%s\' - `birth_year` = \'%s\' AND ('.
                        '\'%s\' - `birth_month` > 0 OR '.
                        '\'%s\' - `birth_month` = 0 AND \'%s\' - `birth_day` >= 0'.
                    '))',
                    mysql_real_escape_string($time_year, $this->_base_node__db_link),
                    mysql_real_escape_string($search_value, $this->_base_node__db_link),
                    mysql_real_escape_string($time_year, $this->_base_node__db_link),
                    mysql_real_escape_string($search_value, $this->_base_node__db_link),
                    mysql_real_escape_string($time_month, $this->_base_node__db_link),
                    mysql_real_escape_string($time_month, $this->_base_node__db_link),
                    mysql_real_escape_string($time_day, $this->_base_node__db_link)
                );
            } elseif($search_type == 'Возраст до') {
                $time = get_time__ns29922();
                $time_year = @date('Y', $time);
                $time_month = @date('n', $time);
                $time_day = @date('j', $time);
                
                $and_part_sqls []= sprintf(
                    '`birth_year` AND (\'%s\' - `birth_year` < \'%s\' OR \'%s\' - `birth_year` = \'%s\' AND ('.
                        '\'%s\' - `birth_month` < 0 OR '.
                        '\'%s\' - `birth_month` = 0 AND \'%s\' - `birth_day` < 0'.
                    '))',
                    mysql_real_escape_string($time_year, $this->_base_node__db_link),
                    mysql_real_escape_string($search_value, $this->_base_node__db_link),
                    mysql_real_escape_string($time_year, $this->_base_node__db_link),
                    mysql_real_escape_string($search_value, $this->_base_node__db_link),
                    mysql_real_escape_string($time_month, $this->_base_node__db_link),
                    mysql_real_escape_string($time_month, $this->_base_node__db_link),
                    mysql_real_escape_string($time_day, $this->_base_node__db_link)
                );
            } elseif($search_type == 'Паспорт (серия, номер и кем выдан)') {
                $search_value_ser_no = str_replace(' ', '', $search_value);
                
                $and_part_sqls []= $this->_search_items_node__get_like_sql(
                        'CONCAT(`passport_ser`, \' \', `passport_no`, \' \', `passport_dep`, \' \', `passport_day`)',
                        $search_value, array('field_expr' => TRUE, 'bkt' => TRUE));
            } elseif($search_type == 'Серия паспорта (строгий режим)') {
                $and_part_sqls []= sprintf(
                    '`passport_ser` = \'%s\'',
                    mysql_real_escape_string($search_value, $this->_base_node__db_link)
                );
            } elseif($search_type == 'Номер паспорта (строгий режим)') {
                $and_part_sqls []= sprintf(
                    '`passport_no` = \'%s\'',
                    mysql_real_escape_string($search_value, $this->_base_node__db_link)
                );
            } elseif($search_type == 'Город') {
                $and_part_sqls []= sprintf(
                    '`residence_city` = \'%s\'',
                    mysql_real_escape_string($search_value, $this->_base_node__db_link)
                );
            } elseif($search_type == 'Адрес') {
                $and_part_sqls []= $this->_search_items_node__get_like_sql(
                        'residence', $search_value, array('bkt' => TRUE));
            } elseif($search_type == 'Адрес') {
                $and_part_sqls []= $this->_search_items_node__get_like_sql(
                        'residence', $search_value, array('bkt' => TRUE));
            } elseif($search_type == 'Дополнительное описание') {
                $and_part_sqls []= $this->_search_items_node__get_like_sql(
                        'about', $search_value, array('bkt' => TRUE));
            } elseif($search_type == 'Примечание') {
                $and_part_sqls []= $this->_search_items_node__get_like_sql(
                        'comments', $search_value, array('bkt' => TRUE));
            } elseif($search_type == 'Id') {
                $and_part_sqls []= sprintf(
                    '`id` = \'%s\'',
                    mysql_real_escape_string($search_value, $this->_base_node__db_link)
                );
            } else {
                $message = sprintf(
                    'Параметр \'%s\' был игнорирован',
                    addslashes($search_type)
                );
                
                $this->_search_items_node__message_html .=
                        '<p class="ErrorColor TextAlignCenter">'.
                            htmlspecialchars($message).
                        '</p>';
            }
        }
        
        $where_sql = join_sqls__ns8184('AND', $and_part_sqls);
        
        #$this->_search_items_node__message_html .=                   // this is for DEBUG ONLY
        #        '<p class="TextAlignCenter Width700Px">'.            // this is for DEBUG ONLY
        #            'DEBUG: +++'.htmlspecialchars($where_sql).'---'. // this is for DEBUG ONLY
        #        '</p>';                                              // this is for DEBUG ONLY
        
        return $where_sql;
    }
    
    protected function _search_items_node__init_form_results() {
        try {
            $where_sql = $this->_search_items_node__get_where_sql();
            
            if($where_sql) {
                if(array_key_exists('items_offset', $_GET)) {
                    $items_offset = intval($this->get_arg('items_offset'));
                    
                    if($items_offset > 0) {
                        $this->_search_items_node__items_offset = $items_offset;
                    }
                }
                
                if(array_key_exists('items_limit', $_GET)) {
                    $items_limit = intval($this->get_arg('items_limit'));
                    
                    if($items_limit > 0 && $items_limit <= 200) {
                        $this->_search_items_node__items_limit = $items_limit;
                        $this->_search_items_node__items_real_limit = $items_limit;
                    }
                }
                
                $mysql_microtime = microtime(TRUE);
                
                $result = mysql_query_or_error(
                    sprintf(
                        'SELECT COUNT(*) FROM `items_base` '.
                        'WHERE %s',
                        $where_sql
                    ),
                    $this->_base_node__db_link
                );
                list($this->_search_items_node__items_count) = mysql_fetch_array($result);
                mysql_free_result($result);
                
                $result = mysql_query_or_error(
                    sprintf(
                        'SELECT * FROM `items_base` '.
                            'WHERE %s '.
                            'ORDER BY ABS(%s - `item_modified`) '.
                            'LIMIT %s OFFSET %s',
                        $where_sql,
                        intval(get_time__ns29922()),
                        intval($this->_search_items_node__items_real_limit),
                        intval($this->_search_items_node__items_offset)
                    ),
                    $this->_base_node__db_link
                );
                
                $this->_search_items_node__items = array();
                for(;;) {
                    $row = mysql_fetch_assoc($result);
                    if($row) {
                        $this->_search_items_node__items []= $row;
                    }
                    else {
                        break;
                    }
                }
                mysql_free_result($result);
                
                $mysql_microtime = abs(microtime(TRUE) - $mysql_microtime);
                
                $this->_search_items_node__item_list_widget =
                        new item_list_widget__ns28376($this->_search_items_node__items);
                $this->_search_items_node__page_links_widget = 
                        new page_links_widget__ns22493(
                            $this->_search_items_node__items_real_limit,
                            $this->_search_items_node__items_offset,
                            $this->_search_items_node__items_count,
                            array($this, '_search_items_node__page_links_widget__get_link_html'),
                            5
                        );
                
                $this->_search_items_node__show_form_results = TRUE;
                $this->_search_items_node__mysql_microtime = $mysql_microtime;
            }
        } catch(MysqlError $e) {
            throw new form_error__ns8184(
                sprintf(
                    'Ошибка при поиске данных внутри Базы Данных (%s)',
                    $e->mysql_error
                )
            );
        }
    }
    
    protected function _base_node__on_init() {
        parent::_base_node__on_init();
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $search_args = array();
            $raw_general_search = $this->post_arg('general_search');
            if($raw_general_search) {
                $general_search = split_str_to_words__ns8184($raw_general_search, array('min_len' => 3));
                
                $search_args['general_search'] = $general_search;
                    
            }
            $sex_search = $this->post_arg('sex_search');
            if($sex_search) {
                $search_args['sex_search'] = $sex_search;
            }
            
            $raw_advanced_search_params = array();
            foreach($_POST as $post_name => $raw_post_value) {
                if(strpos($post_name, 'search_type__') === 0) {
                    $name_postfix = substr($post_name, strlen('search_type__'));
                    $raw_advanced_search_params[$name_postfix]['search_type'] = $this->post_arg($post_name);
                } elseif(strpos($post_name, 'search_value__') === 0) {
                    $name_postfix = substr($post_name, strlen('search_value__'));
                    $raw_advanced_search_params[$name_postfix]['search_value'] = $this->post_arg($post_name);
                }
            }
            $advanced_search_params = array();
            foreach($raw_advanced_search_params as $search_param) {
                if(array_key_exists('search_type', $search_param) && $search_param['search_type'] &&
                        array_key_exists('search_value', $search_param) && $search_param['search_value']) {
                    $advanced_search_params []= $search_param;
                }
            }
            $search_args['advanced_search_params'] = $advanced_search_params;
            
            $msg_token = send_msg__ns1438('search_items_node__ns8184::search_args', $search_args);
            
            $this->_search_items_node__message_html .=
                    '<p class="TextAlignCenter">'.
                        'Поиск...'.
                    '</p>';
            
            @header(sprintf(
                'Refresh: 0.2;url=?%s',
                http_build_query(array(
                    'node' => $this->get_arg('node'),
                    'msg_token' => $msg_token,
                ))
            ));
            $this->_search_items_node__show_form = FALSE;
        } else {
            $msg_token = $this->get_arg('msg_token');
            $search_args = recv_msg__ns1438($msg_token, 'search_items_node__ns8184::search_args');
            
            if($search_args) {
                if(array_key_exists('general_search', $search_args)) {
                    $this->_search_items_node__general_search = $search_args['general_search'];
                }
                if(array_key_exists('sex_search', $search_args)) {
                    $this->_search_items_node__sex_search = $search_args['sex_search'];
                }
                if(array_key_exists('advanced_search_params', $search_args)) {
                    $this->_search_items_node__advanced_search_params = $search_args['advanced_search_params'];
                }
                
                try{
                    $this->_search_items_node__init_form_results();
                } catch(form_error__ns8184 $e) {
                    $message = $e->getMessage();
                    
                    $this->_search_items_node__message_html .=
                        '<p class="ErrorColor TextAlignCenter">'.
                            htmlspecialchars($message).
                        '</p>';
                }
            }
        }
    }
    
    protected function _node__get_title() {
        $parent_title = parent::_node__get_title();
        
        return 'Поиск - '.$parent_title;
    }
    
    protected function _node__get_head() {
        $parent_head = parent::_node__get_head();
        
        $html = '';
        
        $html .=
                $parent_head.
                '<link rel="stylesheet" href="/media/search_items_node/css/style.css" />'.
                '<script src="/media/share/js/func_tools.js"></script>'.
                '<script src="/media/share/js/meta.js"></script>'.
                '<script src="/media/search_items_node/js/dynamic_fields.js"></script>'.
                '<script src="/media/search_items_node/js/autofocus.js"></script>';
        
        $advanced_search_types_params_name =
                '/2010/07/07/List58/search_items_node/dynamic_fields/advanced_search_types_params';
        $html .= sprintf(
            '<meta name="%s" content="%s" />',
            htmlspecialchars($advanced_search_types_params_name),
            htmlspecialchars(
                json_encode($this->_search_items_node__advanced_search_types)
            )
        );
        
        $advanced_search_ids_params_name =
                '/2010/07/07/List58/search_items_node/dynamic_fields/advanced_search_ids_params';
        $advanced_search_ids_params = array();
        foreach($this->_search_items_node__advanced_search_params as $id => $param) {
            $name_postfix = 'last_'.$id;
            $advanced_search_ids_params []= $name_postfix;
        }
        $html .= sprintf(
            '<meta name="%s" content="%s" />',
            htmlspecialchars($advanced_search_ids_params_name),
            htmlspecialchars(
                json_encode($advanced_search_ids_params)
            )
        );
        
        return $html;
    }
    
    protected function _search_items_node__advanced_search_element($name_postfix, $search_type, $search_value) {
        $search_options_html = '';
        
        foreach($this->_search_items_node__advanced_search_types as $type) {
            $search_options_html .= sprintf(
                    '<option value="%s">%s</option>',
                    htmlspecialchars($type),
                    htmlspecialchars($type));
        }
        
        $html =
                '<div id="'.htmlspecialchars('_search_items_node__advanced_search_element__div__'.$name_postfix).'">'.
                    '<select class="FloatLeft Margin5Px Width200Px" '.
                            'name="'.htmlspecialchars('search_type__'.$name_postfix).'" '.
                            'id="'.htmlspecialchars('_search_items_node__advanced_search_element__search_type__'.$name_postfix).'">'.
                        ($search_type?
                            '<option value="'.
                                    htmlspecialchars($search_type).
                            '">'.
                                htmlspecialchars(
                                    sprintf('(Выбрано: %s)', $search_type)
                                ).
                            '</option>':
                            ''
                        ).
                        '<option></option>'.
                        $search_options_html.
                    '</select>'.
                    '<input class="FloatLeft Margin5Px Width300Px" '.
                        'type="text" '.
                        'name="'.htmlspecialchars('search_value__'.$name_postfix).'" '.
                        'id="'.htmlspecialchars('_search_items_node__advanced_search_element__search_value__'.$name_postfix).'" '.
                        'value="'.htmlspecialchars($search_value).'" />'.
                        '<div class="FloatRight Margin5Px" id="'.htmlspecialchars(
                                '_search_items_node__advanced_search_element__remove_noscript__'.$name_postfix).'"></div>'.
                    '<div class="ClearBoth"></div>'.
                '</div>';
        
        return $html;
    }
    
    protected function _search_items_node__get_search_widget() {
        $last_advanced_search_elements = '';
        
        foreach($this->_search_items_node__advanced_search_params as $id => $param) {
            $name_postfix = 'last_'.$id;
            $search_type = $param['search_type'];
            $search_value = $param['search_value'];
            
            $last_advanced_search_elements .=
                    $this->_search_items_node__advanced_search_element($name_postfix, $search_type, $search_value);
        }
        
        $html =
                '<div class="GroupFrame">'.
                    '<form action="'.htmlspecialchars('?node='.urlencode($this->get_arg('node'))).'" method="post">'.
                        '<div class="Margin5Px">'.
                            '<label for="_search_items_node__general_search">Введите одно или несколько ключевых слов:</label>'.
                        '</div>'.
                        '<div class="Margin5Px">'.
                            '<input class="MinWidth700Px Width100Per" '.
                                'type="text" '.
                                'name="general_search" '.
                                'id="_search_items_node__general_search" '.
                                'value="'.htmlspecialchars(join(' ', $this->_search_items_node__general_search)).'" />'.
                        '</div>'.
                        '<div>'.
                            '<select class="FloatRight Margin5Px Width300Px" '.
                                    'name="sex_search" '.
                                    'id="_search_items_node__sex_search">'.
                                ($this->_search_items_node__sex_search?
                                    '<option value="'.
                                            htmlspecialchars($this->_search_items_node__sex_search).
                                    '">'.
                                        htmlspecialchars(
                                            sprintf('(Выбрано: %s)', $this->_search_items_node__sex_search)
                                        ).
                                    '</option>':
                                    ''
                                ).
                                '<option></option>'.
                                '<option value="Мужской">Мужской</option>'.
                                '<option value="Женский">Женский</option>'.
                            '</select> '.
                            '<label class="FloatRight Margin5Px" '.
                                    'for="_search_items_node__sex_search" >'.
                                'Пол:'.
                            '</label>'.
                            '<div class="ClearBoth"></div>'.
                        '</div>'.
                        '<div>'.
                            '<input type="hidden" '.
                                'name="post_token" '.
                                'value="'.htmlspecialchars($_SESSION['post_token']).'" />'.
                            '<input class="FloatLeft Margin5Px" type="submit" value="Найти" />'.
                            '<input class="FloatLeft Margin5Px" type="reset" value="Сброс" />'.
                            '<div class="ClearBoth"></div>'.
                        '</div>'.
                        '<h4>Расширенные параметры:</h4>'.
                        $last_advanced_search_elements.
                        '<div id="_search_items_node__advanced_search_params_noscript">'.
                            $this->_search_items_node__advanced_search_element('noscript_0', '', '').
                            $this->_search_items_node__advanced_search_element('noscript_1', '', '').
                            $this->_search_items_node__advanced_search_element('noscript_2', '', '').
                            $this->_search_items_node__advanced_search_element('noscript_3', '', '').
                            $this->_search_items_node__advanced_search_element('noscript_4', '', '').
                        '</div>'.
                        '<input class="FloatLeft Margin5Px" type="submit" value="Найти" />'.
                        '<input class="FloatLeft Margin5Px" type="reset" value="Сброс" />'.
                        '<div class="ClearBoth"></div>'.
                    '</form>'.
                '</div>';
        
        return $html;
    }
    
    public function _search_items_node__page_links_widget__get_link_html($items_offset, $label) {
        $query_node = $this->get_arg('node');
        $query_msg_token = $this->get_arg('msg_token');
        
        $query_data = array();
        if($query_node) {
            $query_data['node'] = $query_node;
        }
        if($query_msg_token) {
            $query_data['msg_token'] = $query_msg_token;
        }
        if($this->_search_items_node__items_limit) {
            $query_data['items_limit'] = $this->_search_items_node__items_limit;
        }
        if($items_offset > 0) {
            $query_data['items_offset'] = $items_offset;
        }
        
        $html =
                '<a href="'.htmlspecialchars('?'.http_build_query($query_data)).'">'.
                    htmlspecialchars($label).
                '</a>';
        
        return $html;
    }
    
    protected function _search_items_node__get_result_widget() {
        $query_node = $this->get_arg('node');
        $query_msg_token = $this->get_arg('msg_token');
        $short_page_links_html = '';
        
        if($this->_search_items_node__items_offset > 0) {
            $query_items_offset = $this->_search_items_node__items_offset - $this->_search_items_node__items_real_limit;
            
            $query_data = array();
            if($query_node) {
                $query_data['node'] = $query_node;
            }
            if($query_msg_token) {
                $query_data['msg_token'] = $query_msg_token;
            }
            if($this->_search_items_node__items_limit) {
                $query_data['items_limit'] = $this->_search_items_node__items_limit;
            }
            if($query_items_offset > 0) {
                $query_data['items_offset'] = $query_items_offset;
            }
            
            $short_page_links_html .=
                    '<a class="FloatLeft" href="'.htmlspecialchars('?'.http_build_query($query_data)).'">'.
                        htmlspecialchars('<< Назад').
                    '</a>';
        }
        
        if(
            $this->_search_items_node__items_offset + $this->_search_items_node__items_real_limit <
            $this->_search_items_node__items_count
        ) {
            $query_items_offset = $this->_search_items_node__items_offset + $this->_search_items_node__items_real_limit;
            
            $query_data = array();
            if($query_node) {
                $query_data['node'] = $query_node;
            }
            if($query_msg_token) {
                $query_data['msg_token'] = $query_msg_token;
            }
            if($this->_search_items_node__items_limit) {
                $query_data['items_limit'] = $this->_search_items_node__items_limit;
            }
            if($query_items_offset > 0) {
                $query_data['items_offset'] = $query_items_offset;
            }
            
            $short_page_links_html .=
                    '<a class="FloatRight" href="'.htmlspecialchars('?'.http_build_query($query_data)).'">'.
                        htmlspecialchars('Ещё >>').
                    '</a>';
        }
        
        $html =
                '<div class="GroupFrame">'.
                     $this->_search_items_node__item_list_widget->get_widget().
                '</div>'.
                '<div class="Margin10Px TextAlignCenter">'.
                    $short_page_links_html.
                    'Стр.: '.$this->_search_items_node__page_links_widget->get_widget().
                    '<div class="ClearBoth"></div>'.
                '</div>';
        
        if($this->_search_items_node__mysql_microtime) {
            $mysql_time = ceil($this->_search_items_node__mysql_microtime * 1000.0) / 1000.0;
            
            $html .=
                '<div class="Margin10Px FloatRight FontSize07Em">'.
                    'Время поиска (секунд): '.$mysql_time.
                '</div>'.
                '<div class="ClearBoth"></div>';
        }
        
        return $html;
    }
    
    protected function _node__get_aside() {
        $form_html = '';
        
        if($this->_search_items_node__show_form) {
            $search_widget_html = $this->_search_items_node__get_search_widget();
            
            $form_html =
                    '<h2 class="TextAlignCenter">Поиск данных</h2>'.
                    $search_widget_html;
            
            if($this->_search_items_node__show_form_results) {
                $result_widget_html = $this->_search_items_node__get_result_widget();
                
                $form_html .=
                        '<h3>Найдено:</h3>'.
                        $result_widget_html;
            }
        }
        
        $html =
                '<div class="SmallFrame">'.
                    $this->_search_items_node__message_html.
                    $form_html.
                '</div>';
        
        return $html;
    }
}

