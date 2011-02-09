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
    'use strict'
    
    var top_window_marker =
            '/2010/07/07/List58/node/frame_exit/top_window_marker'
    
    function main(event) {
        // помечаем что это окно является "первым" ("top"), относительно этой системы
        window[top_window_marker] = true
        
        try {
            for(var curr_window = window;
                    curr_window.parent != curr_window;
                    curr_window = curr_window.parent) {
                // если вдруг оказывается что мы находимся внутри другого родительского окна...
                
                if(curr_window.parent[top_window_marker]) {
                    // и это другое окно тоже является таким же "первым" (относительно этой системы)...
                    //
                    // ...то обновляем его адрес на наш адрес
                    
                    curr_window.parent.location = location
                    // таким образом мы избавились от лишнего frame
                    
                    break
                }
                
                // но суть в том, что нет смысла просто-так избавляться от frame,
                // в случае если мы ничего не знаем о том кто является родительским окном
            }
        } catch(e) {}
    }
    
    addEventListener('load', main, false)
})()

