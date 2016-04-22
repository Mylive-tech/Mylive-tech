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
 
function IR_submit()
{
	if(document.IR_form.IR_path.value=="")
	{
		alert("Please enter the image path.")
		document.IR_form.IR_path.focus();
		return false;
	}
	else if(document.IR_form.IR_link.value=="")
	{
		alert("Please enter the target link.")
		document.IR_form.IR_link.focus();
		return false;
	}
	else if(document.IR_form.IR_target.value=="")
	{
		alert("Please enter the target status.")
		document.IR_form.IR_target.focus();
		return false;
	}
	else if(document.IR_form.IR_title.value=="")
	{
		alert("Please enter the image title.")
		document.IR_form.IR_title.focus();
		return false;
	}
	else if(document.IR_form.IR_desc.value=="")
	{
		alert("Please enter the image description.")
		document.IR_form.IR_desc.focus();
		return false;
	}
	else if(document.IR_form.IR_type.value == "Select")
	{
		alert("Please select the content group/type.")
		document.IR_form.IR_type.focus();
		return false;
	}
	else if(document.IR_form.IR_type.value=="")
	{
		alert("Please enter the gallery type.")
		document.IR_form.IR_type.focus();
		return false;
	}
	else if(document.IR_form.IR_status.value=="")
	{
		alert("Please select the display status.")
		document.IR_form.IR_status.focus();
		return false;
	}
	else if(document.IR_form.IR_order.value=="")
	{
		alert("Please enter the display order, only number.")
		document.IR_form.IR_order.focus();
		return false;
	}
	else if(isNaN(document.IR_form.IR_order.value))
	{
		alert("Please enter the display order, only number.")
		document.IR_form.IR_order.focus();
		return false;
	}
}

function IR_delete(id)
{
	if(confirm("Do you want to delete this record?"))
	{
		document.frm_IR_display.action="options-general.php?page=information-reel&ac=del&did="+id;
		document.frm_IR_display.submit();
	}
}	

function IR_redirect()
{
	window.location = "options-general.php?page=information-reel";
}

function IR_help()
{
	window.open("http://www.gopiplus.com/work/2011/04/16/wordpress-plugin-information-reel/");
}