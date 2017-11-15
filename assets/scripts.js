var currentLanguage = $('html').attr('lang');

var markitupLanguagestrings = new Object();
markitupLanguagestrings['de'] = new Object();
markitupLanguagestrings['en'] = new Object();

markitupLanguagestrings['de']['linktext'] = 'Linktext?';
markitupLanguagestrings['en']['linktext'] = 'Linktext?';
markitupLanguagestrings['de']['linkurl'] = 'URL?';
markitupLanguagestrings['en']['linkurl'] = 'URL?';
markitupLanguagestrings['de']['linkemailaddress'] = 'E-Mail Adresse?';
markitupLanguagestrings['en']['linkemailaddress'] = 'Emailaddress?';
markitupLanguagestrings['de']['tablecolumns'] = 'Wie viele Spalten?';
markitupLanguagestrings['en']['tablecolumns'] = 'How many cols?';
markitupLanguagestrings['de']['tablerows'] = 'Wie viele Zeilen?';
markitupLanguagestrings['en']['tablerows'] = 'How many rows?';

//Start - temporary functions to prevent errors
	function btnImageCallbackInsert() {
		alert('Module-Input is misconfigured, please use the example!');
	}
	
	function btnLinkInternalCallbackInsert() {
		alert('Module-Input is misconfigured, please use the example');
	}
//End - temporary functions to prevent errors

//Start - functions for markdown
	function btnMarkdownMediaCallback () {
		var mediapool = openMediaPool('markitup_media');
		$(mediapool).on('rex:selectMedia', function (event, filename) {
			event.preventDefault();
			mediapool.close();
			
			$.markItUp({
				openWith: '!['+filename+'](index.php?rex_media_type=markitupImage&rex_media_file='+filename+')'
			});
		});
	}
	
	function btnMarkdownLinkExternalCallback (h) {
		var linktext = h.selection;
		if (linktext == '') {
			if (!(linktext = prompt(markitupLanguagestrings[currentLanguage]['linktext']))) {
				return;
			}
		}
		
		if (!(linkurl = prompt(markitupLanguagestrings[currentLanguage]['linkurl'], 'http://'))) {
			return;
		}
		
		return '['+linktext+']('+linkurl+')';
	}
	
	function btnMarkdownLinkMediaCallback () {
		var mediapool = openMediaPool('markitup_link');
		$(mediapool).on('rex:selectMedia', function (event, filename) {
			event.preventDefault();
			mediapool.close();
			
			$.markItUp({
				openWith: '['+filename+'](/media/'+filename+')'
			});
		});
	}
	
	function btnMarkdownLinkInternalCallback () {
		var linkMap = openLinkMap();
		$(linkMap).on('rex:selectLink', function (event, linkurl, linktext) {
			event.preventDefault();
			linkMap.close();
			
			$.markItUp({
				openWith: '['+linktext+']('+linkurl+')'
			});
		});
	}
	
	function btnMarkdownLinkMailtoCallback (h) {
		var linktext = h.selection;
		if (linktext == '') {
			if (!(linktext = prompt(markitupLanguagestrings[currentLanguage]['linktext']))) {
				return;
			}
		}
		
		if (!(emailaddress = prompt(markitupLanguagestrings[currentLanguage]['linkemailaddress']))) {
			return;
		}
		
		return '['+linktext+'](mailto:'+emailaddress+')';
	}
	
	function btnMarkdownTableCallback (h) {
		if (!(cols = prompt(markitupLanguagestrings[currentLanguage]['tablecolumns']))) {
			return;
		}
		
		if (!(rows = prompt(markitupLanguagestrings[currentLanguage]['tablerows']))) {
			return;
		}
		
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
		
		if (selection != '') {
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
		} else {
			return;
		}
	}
	
	function btnMarkdownUnorderedlistCallback (h) {
		var selection = h.selection;
		
		if (selection != '') {
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
		} else {
			return;
		}
	}
//End - functions for markdown

//Start - functions for textile
	function btnTextileMediaCallback (h) {
		var mediapool = openMediaPool('markitup_media');
		$(mediapool).on('rex:selectMedia', function (event, filename) {
			event.preventDefault();
			mediapool.close();
			
			$.markItUp({
				openWith: '!index.php?rex_media_type=markitupImage&rex_media_file='+filename+'('+filename+')!'
			});
		});
	}
	
	function btnTextileLinkExternalCallback (h) {
		var linktext = h.selection;
		if (linktext == '') {
			if (!(linktext = prompt(markitupLanguagestrings[currentLanguage]['linktext']))) {
				return;
			}
		}
		
		if (!(linkurl = prompt(markitupLanguagestrings[currentLanguage]['linkurl'], 'http://'))) {
			return;
		}
		
		return '"'+linktext+'":'+linkurl;
	}
	
	function btnTextileLinkMediaCallback () {
		var mediapool = openMediaPool('markitup_link');
		$(mediapool).on('rex:selectMedia', function (event, filename) {
			event.preventDefault();
			mediapool.close();
			
			$.markItUp({
				openWith: '"',
				closeWith: ' ('+filename+')":/media/'+filename
			});
		});
	}
	
	function btnTextileLinkInternalCallback () {
		var linkMap = openLinkMap();
		$(linkMap).on('rex:selectLink', function (event, linkurl, linktext) {
			event.preventDefault();
			linkMap.close();
			
			$.markItUp({
				openWith: '"',
				closeWith: ' ('+linktext+')":'+linkurl
			});
		});
	}
	
	function btnTextileLinkMailtoCallback (h) {
		var linktext = h.selection;
		if (linktext == '') {
			if (!(linktext = prompt(markitupLanguagestrings[currentLanguage]['linktext']))) {
				return;
			}
		}
		
		if (!(emailaddress = prompt(markitupLanguagestrings[currentLanguage]['linkemailaddress']))) {
			return;
		}
		
		return '"'+linktext+'":mailto:'+emailaddress;
	}

	function btnTextileTableCallback (h) {
		if (!(cols = prompt(markitupLanguagestrings[currentLanguage]['tablecolumns']))) {
			return;
		}
		
		if (!(rows = prompt(markitupLanguagestrings[currentLanguage]['tablerows']))) {
			return;
		}
		
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
		
		if (selection != '') {
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
		} else {
			return;
		}
	}
	
	function btnTextileUnorderedlistCallback (h) {
		var selection = h.selection;
		
		if (selection != '') {
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
		} else {
			return;
		}
	}
//End - functions for textile