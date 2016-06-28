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
	function btnMarkdownMediaCallback () {
		var mediapool = openMediaPool('markitup_media');
		$(mediapool).on('rex:selectMedia', function (event, filename) {
			event.preventDefault();
			mediapool.close();
			
			$.markItUp({
				openWith: '['+filename+'](index.php?rex_media_type=markitupImage&rex_media_file='+filename+')'
			});
		});
	}
	
	function btnMarkdownLinkExternalCallback (h) {
		var linktext = h.selection;
		if (linktext == '') {
			if ((linktext = prompt(rex_markitupLanguagestrings[currentLanguage]['linktext'])) == null) {
				return;
			}
		}
		
		if ((linkurl = prompt(rex_markitupLanguagestrings[currentLanguage]['linkurl'], 'http://')) == null) {
			return;
		}
		
		return '['+linktext+']('+linkurl+')';
	}
	
	function btnMarkdownLinkFileCallback () {
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
			if ((linktext = prompt(rex_markitupLanguagestrings[currentLanguage]['linktext'])) == null) {
				return;
			}
		}
		
		if ((emailaddress = prompt(rex_markitupLanguagestrings[currentLanguage]['linkemailaddress'])) == null) {
			return;
		}
		
		return '['+linktext+'](mailto:'+emailaddress+')';
	}
	
	function btnMarkdownTableCallback (h) {
		if ((cols = prompt(rex_markitupLanguagestrings[currentLanguage]['tablecolumns'])) == null) {
			return;
		}
		
		if ((rows = prompt(rex_markitupLanguagestrings[currentLanguage]['tablerows'])) == null) {
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
			if ((linktext = prompt(rex_markitupLanguagestrings[currentLanguage]['linktext'])) == null) {
				return;
			}
		}
		
		if ((linkurl = prompt(rex_markitupLanguagestrings[currentLanguage]['linkurl'], 'http://')) == null) {
			return;
		}
		
		return '"'+linktext+'":'+linkurl;
	}
	
	function btnTextileLinkFileCallback () {
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
			if ((linktext = prompt(rex_markitupLanguagestrings[currentLanguage]['linktext'])) == null) {
				return;
			}
		}
		
		if ((emailaddress = prompt(rex_markitupLanguagestrings[currentLanguage]['linkemailaddress'])) == null) {
			return;
		}
		
		return '"'+linktext+'":mailto:'+emailaddress;
	}

	function btnTextileTableCallback (h) {
		if ((cols = prompt(rex_markitupLanguagestrings[currentLanguage]['tablecolumns'])) == null) {
			return;
		}
		
		if ((rows = prompt(rex_markitupLanguagestrings[currentLanguage]['tablerows'])) == null) {
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