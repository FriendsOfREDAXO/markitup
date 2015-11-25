<?php
	if (rex::isBackend()) {
		function markitupDefineButtons($type, $buttons, $that) {
			
			$markitupButtons = [];
			
			$buttons = explode(',', $buttons);
			foreach ($buttons as $button) {
				switch ($button) {
					case 'bold':
						switch($type) {
							case 'textile':
								$openWith = '*';
								$closeWith = '*';
							break;
						}
						
						$markitupButtons[] = "{name:'".$that->i18n('profiles_buttons_bold')."', key:'B', className:'bold', openWith:'".$openWith."', closeWith: '".$closeWith."'},";
					break;
					case 'deleted':
						switch($type) {
							case 'textile':
								$openWith = '-';
								$closeWith = '-';
							break;
						}
						
						$markitupButtons[] = "{name:'".$that->i18n('profiles_buttons_deleted')."', key:'S', className:'deleted', openWith:'".$openWith."', closeWith: '".$closeWith."'},";
					break;
					case 'italic':
						switch($type) {
							case 'textile':
								$openWith = '_';
								$closeWith = '_';
							break;
						}
						
						$markitupButtons[] = "{name:'".$that->i18n('profiles_buttons_italic')."', key:'I', className:'italic', openWith:'".$openWith."', closeWith: '".$closeWith."'},";
					break;
					case 'separator':
						$markitupButtons[] = "{separator:'---------------' },";
					break;
					case 'underline':
						switch($type) {
							case 'textile':
								$openWith = '+';
								$closeWith = '+';
							break;
						}
						
						$markitupButtons[] = "{name:'".$that->i18n('profiles_buttons_underline')."', key:'U', className:'underline', openWith:'".$openWith."', closeWith: '".$closeWith."'},";
					break;
				}
			}
			
			return implode(PHP_EOL, $markitupButtons);
		}
		
		rex_view::addJsFile($this->getAssetsUrl('jquery.markitup.js'));
		rex_view::addCssFile($this->getAssetsUrl('skins/custom/style.css'));
		
		//Start - get markitup-profiles
			$sql = rex_sql::factory();
			$profiles = $sql->setQuery("SELECT `name`, `language`, `type`, `markitup_buttons` FROM `".rex::getTablePrefix()."markitup_profiles` ORDER BY `name` ASC")->getArray();
			unset($sql);
			
			$jsCode = [];
			$jsCode[] = '$(document).on(\'ready pjax:success\',function() {';
			foreach ($profiles as $profile) {
				$jsCode[] = '  $(\'.markitupEditor-'.$profile['name'].'\').markItUp({';
				
				$jsCode[] = '    nameSpace: "markitup_'.$profile['type'].'",';
//				$jsCode[] = '    previewParserPath:   "~/sets/textile/preview.php",';
//				$jsCode[] = '    onShiftEnter: {keepDefault:false, replaceWith:\'\n\n\'},';
				$jsCode[] = '    markupSet: [,';
				$jsCode[] = '      '.markitupDefineButtons($profile['type'], $profile['markitup_buttons'], $this);
				$jsCode[] = '    ]';
				
				
    
    
    /*
    markupSet: [
        {name:'Heading 1', key:'1', openWith:'h1(!(([![Class]!]))!). ', placeHolder:'Your title here...' },
        {name:'Heading 2', key:'2', openWith:'h2(!(([![Class]!]))!). ', placeHolder:'Your title here...' },
        {name:'Heading 3', key:'3', openWith:'h3(!(([![Class]!]))!). ', placeHolder:'Your title here...' },
        {name:'Heading 4', key:'4', openWith:'h4(!(([![Class]!]))!). ', placeHolder:'Your title here...' },
        {name:'Heading 5', key:'5', openWith:'h5(!(([![Class]!]))!). ', placeHolder:'Your title here...' },
        {name:'Heading 6', key:'6', openWith:'h6(!(([![Class]!]))!). ', placeHolder:'Your title here...' },
        {name:'Paragraph', key:'P', openWith:'p(!(([![Class]!]))!). '}, 
        {separator:'---------------' },
        {name:'Bold', key:'B', closeWith:'*', openWith:'*'}, 
        {name:'Italic', key:'I', closeWith:'_', openWith:'_'}, 
        {name:'Stroke through', key:'S', closeWith:'-', openWith:'-'}, 
        {separator:'---------------' },
        {name:'Bulleted list', openWith:'(!(* |!|*)!)'}, 
        {name:'Numeric list', openWith:'(!(# |!|#)!)'}, 
        {separator:'---------------' },
        {name:'Picture', replaceWith:'![![Source:!:http://]!]([![Alternative text]!])!'}, 
        {name:'Link', openWith:'"', closeWith:'([![Title]!])":[![Link:!:http://]!]', placeHolder:'Your text to link here...' },
        {separator:'---------------' },
        {name:'Quotes', openWith:'bq(!(([![Class]!]))!). '}, 
        {name:'Code', openWith:'@', closeWith:'@'}, 
        {separator:'---------------' },       
        {name:'Preview', call:'preview', className:'preview'}
    ]
';*/
				
				$jsCode[] = '  });';
				
			}
			$jsCode[] = '});';
			
			if (!rex_file::put(rex_path::addonAssets('rex_markitup', 'cache/markitup_profiles.js').'', implode(PHP_EOL, $jsCode))) {
				echo 'js-file konnte nicht gespeichert werden';
			}
			
			rex_view::addJsFile($this->getAssetsUrl('cache/markitup_profiles.js'));
		//End - get markitup-profiles
		

	}
?>