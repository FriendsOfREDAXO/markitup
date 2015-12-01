function btnImageCallback (h) {
	var markitupFieldID = h.textarea.id;
	newPoolWindow('index.php?page=mediapool/media&opener_input_field='+markitupFieldID);
}

function btnLinkExternalCallback (h) {
	var linktext = h.selection;
	if (linktext == '') {
		var linktext = prompt('Linktext?');
	}
	
	var linkurl = prompt('URL?');
	
	return '"'+linktext+'":'+linkurl;
}


function btnLinkInternalCallback (h) {
	var markitupFieldID = h.textarea.id;
	openLinkMap(markitupFieldID);
}

function btnLinkInternalCallbackInsert (id, url, linktext) {
	$.markItUp({target:'#'+id, openWith:'url:linktext'});
}

function btnLinkMailtoCallback (h) {
	var linktext = h.selection;
	if (linktext == '') {
		var linktext = prompt('Linktext?');
	}
  var emailaddress = prompt('Emailaddress?');
  
  return '"'+linktext+'":mailto:'+emailaddress;
}

function btnTableCallback (h) {
	cols = prompt('How many cols?');
	rows = prompt('How many rows?');
	html = '';
	
	for (r = 0; r < rows; r++) {
		for (c = 0; c < cols; c++) {
			html += '|ABC';
		}
		html += '|\n';
	}
	
	return html;
}

function btnOrderedlistCallback (h) {
	var selection = h.selection;
	
	var lines = selection.split(/\r?\n/);
	var r = "";
	var start = "# ";
	for (var i=0; i < lines.length; i++) {
	  line = lines[i];
	
	  if (line.substr(0,1) == "*" || line.substr(0,1) == "#") {
	    start = "*";
	    if (i != lines.length - 1) {
	      line = line + "\n";
	    }
	  } else {
	    line = line + "\n";
	  }
	  r = r + start + line;
	}
	return r;
}

function btnUnorderedlistCallback (h) {
	var selection = h.selection;
	
	var lines = selection.split(/\r?\n/);
	var r = "";
	var start = "* ";
	for (var i=0; i < lines.length; i++) {
	  line = lines[i];
	  if (line.substr(0,1) == "*" || line.substr(0,1) == "#") {
	    start = "*";
	
	    if (i != lines.length - 1) {
	      line = line + "\n";
	    }
	  } else {
	    line = line + "\n";
	  }
	  r = r + start + line;
	}
	return r;
}