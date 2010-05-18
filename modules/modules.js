function su_reset_textbox(id, d, m, e) {
	if (confirm(m+"\n\n"+d)) {
		document.getElementById(id).value=d;
		e.className='hidden';
	}
}

function su_textbox_value_changed(e, d, l) {
	if (e.value==d)
		document.getElementById(l).className='hidden';
	else
		document.getElementById(l).className='';
}

function su_toggle_blind(id) {
	if (document.getElementById(id)) {
		if (document.getElementById(id).style.display=='none')
			Effect.BlindDown(id);
		else
			Effect.BlindUp(id);
	}
	
	return false;
}