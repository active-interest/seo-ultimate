function reset_textbox(id, d, m, e) {
	if (confirm(m+"\n\n"+d)) {
		document.getElementById(id).value=d;
		e.className='hidden';
	}
}

function textbox_value_changed(e, d, l) {
	if (e.value==d)
		document.getElementById(l).className='hidden';
	else
		document.getElementById(l).className='';
}

function textbox_char_count(textbox, charcount) {
	document.getElementById(charcount).innerHTML = document.getElementById(textbox).value.length;
}