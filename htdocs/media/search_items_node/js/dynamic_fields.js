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

(function() {
    "use strict"
    
    var html_ns = 'http://www.w3.org/1999/xhtml'
    var id_search_element_prefix = '_search_items_node__advanced_search_element__'
    
    var replace_noscript = function() {
        var noscript = document.getElementById('_search_items_node__advanced_search_params_noscript')
        
        if(noscript && noscript.parentNode) {
            var fragment = document.createDocumentFragment()
            fragment.appendChild(document.createTextNode('(тут будет кнопка "добавить")'))
            
            noscript.parentNode.replaceChild(fragment, noscript)
        }
    }
    
    var main = function(event) {
        replace_noscript()
        // TODO: ...
    }
    
    addEventListener('load', main, false)
})()

