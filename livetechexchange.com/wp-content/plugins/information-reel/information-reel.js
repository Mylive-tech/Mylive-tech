/**
 *     Information Reel
 *     Copyright (C) 2011 - 2014 www.gopiplus.com
 * 
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 * 
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 * 
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */	

function scrollIR() {
	objIR.scrollTop = objIR.scrollTop + 1;
	IR_scrollPos++;
	if ((IR_scrollPos%IR_heightOfElm) == 0) {
		IR_numScrolls--;
		if (IR_numScrolls == 0) {
			objIR.scrollTop = '0';
			IRContent();
		} else {
			if (IR_scrollOn == 'true') {
				IRContent();
			}
		}
	} else {
		/* Speed values: 10 slow, 50 fast */
		var speed = 60 - ( IR_speed * 10 );
		setTimeout("scrollIR();", speed);
	}
}

var IRNum = 0;
/*
Creates amount to show + 1 for the scrolling ability to work
scrollTop is set to top position after each creation
Otherwise the scrolling cannot happen
*/
function IRContent() {
	var tmp_IR = '';

	w_IR = IRNum - parseInt(IR_numberOfElm);
	if (w_IR < 0) {
		w_IR = 0;
	} else {
		w_IR = w_IR%IR.length;
	}
	
	// Show amount of IR
	var elementsTmp_IR = parseInt(IR_numberOfElm) + 1;
	for (i_IR = 0; i_IR < elementsTmp_IR; i_IR++) {
		
		tmp_IR += IR[w_IR%IR.length];
		w_IR++;
	}

	objIR.innerHTML 	= tmp_IR;
	
	IRNum 			= w_IR;
	IR_numScrolls 	= IR.length;
	objIR.scrollTop 	= '0';
	// start scrolling
	setTimeout("scrollIR();", IR_waitseconds * 2000);
}