<?php
	if (rex::isBackend()) {

        // Ressourcen einbinden

		rex_view::addJsFile($this->getAssetsUrl('jquery.markitup.js'));
		rex_view::addJsFile($this->getAssetsUrl('autosize.min.js'));
		rex_view::addJsFile($this->getAssetsUrl('scripts.js'));
		rex_view::addCssFile($this->getAssetsUrl('style.css'));
		if (file_exists($this->getAssetsPath('skin.css'))) {
			rex_view::addCssFile($this->getAssetsUrl('skin.css'));
		}

        $language = array_unique(
            array_map(
                function($l) { return substr($l,0,2); },
                array_merge( [rex_i18n::getLocale()], rex::getProperty('lang_fallback', []), ['--'] )
            )
        );
        foreach( $language as $lang ) {
            $langPath = $this->getAssetsUrl( "cache/$lang" );
            if( is_dir($langPath) ) {
                rex_view::addCssFile("$langPath/markitup_profiles.css");
            	rex_view::addJsFile("$langPath/markitup_profiles.js");
                break;
            }
        }
    }
