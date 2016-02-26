var currentLanguage = $('html').attr('lang');

var rex_markitupLanguagestrings = new Object();
rex_markitupLanguagestrings['de'] = new Object();
rex_markitupLanguagestrings['en'] = new Object();

rex_markitupLanguagestrings['de']['linktext'] = 'Linktext?';
rex_markitupLanguagestrings['en']['linktext'] = 'Linktext?';
rex_markitupLanguagestrings['de']['linkurl'] = 'URL?';
rex_markitupLanguagestrings['en']['linkurl'] = 'URL?';
rex_markitupLanguagestrings['de']['linkemailaddress'] = 'E-Mail Adresse?';
rex_markitupLanguagestrings['en']['linkemailaddress'] = 'Emailaddress?';
rex_markitupLanguagestrings['de']['tablecolumns'] = 'Wie viele Spalten?';
rex_markitupLanguagestrings['en']['tablecolumns'] = 'How many cols?';
rex_markitupLanguagestrings['de']['tablerows'] = 'Wie viele Zeilen?';
rex_markitupLanguagestrings['en']['tablerows'] = 'How many rows?';

//Start - temporary functions to prevent errors
	function btnImageCallbackInsert() {
		alert('Module-Input is misconfigured, please use the example!');
	}
	
	function btnLinkInternalCallbackInsert() {
		alert('Module-Input is misconfigured, please use the example');
	}
//End - temporary functions to prevent errors

//Start - functions for markdown
	function btnMarkdownImageCallback (h) {
		var markitupFieldID = h.textarea.id;
		newPoolWindow('index.php?page=mediapool/media&opener_input_field='+markitupFieldID);
	}
	
	function btnMarkdownImageCallbackInsert (id, url, linktext) {
		window.opener.$.markItUp({
			target:'#'+id,
			openWith:'['+linktext+'](index.php?rex_media_type=markitupImage&rex_media_file='+url+')'
		});
	}
	
	function btnMarkdownLinkExternalCallback (h) {
		var linktext = h.selection;
		if (linktext == '') {
			var linktext = prompt(rex_markitupLanguagestrings[currentLanguage]['linktext']);
		}
		
		var linkurl = prompt(rex_markitupLanguagestrings[currentLanguage]['linkurl']);
		
		return '['+linktext+']('+linkurl+')';
	}
	
	function btnMarkdownLinkInternalCallback (h) {
		var markitupFieldID = h.textarea.id;
		openLinkMap(markitupFieldID);
	}
	
	function btnMarkdownLinkInternalCallbackInsert (id, url, linktext) {
		window.opener.$.markItUp({
			target:'#'+id,
			openWith: '[',
			closeWith: ']('+url+')'
		});
	}
	
	function btnMarkdownLinkMailtoCallback (h) {
		var linktext = h.selection;
		if (linktext == '') {
			var linktext = prompt(rex_markitupLanguagestrings[currentLanguage]['linktext']);
		}
		var emailaddress = prompt(rex_markitupLanguagestrings[currentLanguage]['linkemailaddress']);
		
		return '['+linktext+'](mailto:'+emailaddress+')';
	}
	
	function btnMarkdownTableCallback (h) {
		cols = prompt(rex_markitupLanguagestrings[currentLanguage]['tablecolumns']);
		rows = prompt(rex_markitupLanguagestrings[currentLanguage]['tablerows']);
		html = '';
		
		for (r = 0; r < rows; r++) {
			for (c = 0; c < cols; c++) {
				html += '| ABC ';
			}
			html += '|\n';
		}
		
		return html;
	}
	
	function btnMarkdownOrderedlistCallback (h) {
		var selection = h.selection;
		
		var lines = selection.split(/\r?\n/);
		var r = "";
		for (var i=0; i < lines.length; i++) {
			line = lines[i];
			
			r = r + (i+1) + '. ' + line;
			
			if (i != lines.length - 1) {
				r += "\n";
			}
		}
		return r;
	}
	
	function btnMarkdownUnorderedlistCallback (h) {
		var selection = h.selection;
		
		var lines = selection.split(/\r?\n/);
		var r = "";
		for (var i=0; i < lines.length; i++) {
			line = lines[i];
			
			r = r + "- " + line;
			
			if (i != lines.length - 1) {
				r += "\n";
			}
		}
		return r;
	}
//End - functions for markdown


//Start - functions for textile
	function btnTextileImageCallback (h) {
		var markitupFieldID = h.textarea.id;
		newPoolWindow('index.php?page=mediapool/media&opener_input_field='+markitupFieldID);
	}
	
	function btnTextileImageCallbackInsert (id, url, linktext) {
		window.opener.$.markItUp({
			target:'#'+id,
			openWith:'!index.php?rex_media_type=markitupImage&rex_media_file='+url+'('+linktext+')!'
		});
	}
	
	function btnTextileLinkExternalCallback (h) {
		var linktext = h.selection;
		if (linktext == '') {
			var linktext = prompt(rex_markitupLanguagestrings[currentLanguage]['linktext']);
		}
		
		var linkurl = prompt(rex_markitupLanguagestrings[currentLanguage]['linkurl']); //todo
		
		return '"'+linktext+'":'+linkurl;
	}
	
	function btnTextileLinkInternalCallback (h) {
		var markitupFieldID = h.textarea.id;
		openLinkMap(markitupFieldID);
	}
	
	function btnTextileLinkInternalCallbackInsert (id, url, linktext) {
		window.opener.$.markItUp({
			target:'#'+id,
			openWith: '"',
			closeWith: '('+linktext+')":'+url
		});
	}
	
	function btnTextileLinkMailtoCallback (h) {
		var linktext = h.selection;
		if (linktext == '') {
			var linktext = prompt(rex_markitupLanguagestrings[currentLanguage]['linktext']);
		}
		var emailaddress = prompt(rex_markitupLanguagestrings[currentLanguage]['linkemailaddress']);
		
		return '"'+linktext+'":mailto:'+emailaddress;
	}

	function btnTextileTableCallback (h) {
		cols = prompt(rex_markitupLanguagestrings[currentLanguage]['tablecolumns']);
		rows = prompt(rex_markitupLanguagestrings[currentLanguage]['tablerows']);
		html = '';
		
		for (r = 0; r < rows; r++) {
			for (c = 0; c < cols; c++) {
				html += '|ABC';
			}
			html += '|\n';
		}
		
		return html;
	}
	
	function btnTextileOrderedlistCallback (h) {
		var selection = h.selection;
		
		var lines = selection.split(/\r?\n/);
		var r = "";
		for (var i=0; i < lines.length; i++) {
			line = lines[i];
			
			r = r + "# " + line;
			
			if (i != lines.length - 1) {
				r += "\n";
			}
		}
		return r;
	}
	
	function btnTextileUnorderedlistCallback (h) {
		var selection = h.selection;
		
		var lines = selection.split(/\r?\n/);
		var r = "";
		for (var i=0; i < lines.length; i++) {
			line = lines[i];
			
			r = r + "* " + line;
			
			if (i != lines.length - 1) {
				r += "\n";
			}
		}
		return r;
	}
//End - functions for textile