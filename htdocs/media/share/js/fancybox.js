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
    
    jQuery(function() {
        jQuery('a[rel=sm_frame_fancybox]').fancybox({
            type: 'iframe',
            showNavArrows: false,
            width: '50%',
            height: '40%',
        })
        
        jQuery('a[rel=med_frame_fancybox]').fancybox({
            type: 'iframe',
            showNavArrows: false,
            width: '75%',
            height: '70%',
        })
        
        jQuery('a[rel=big_frame_fancybox]').fancybox({
            type: 'iframe',
            showNavArrows: false,
            width: '95%',
            height: '95%',
        })
    })
})()

