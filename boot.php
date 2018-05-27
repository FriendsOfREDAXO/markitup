<?php
	if (rex::isBackend()) {

        // Wenn ein Profil erfolgreich gespeichert wurde (add|edit) oder gelöscht (delete)
        // werden die darauf basierenden DAteien markitup_profiles.[css|js] neu angelegt

        rex_extension::register( 'REX_FORM_SAVED', function( $ep ) {

            if( strcasecmp( $ep->getParams()['form']->getTableName(),rex::getTable('markitup_profiles') ) == 0 )
            {
                include_once( 'functions/cache_markitup_profiles.php');
                echo markitup_cache_update( );
            }

        } );

        rex_extension::register( 'REX_FORM_DELETED', function( $ep ) {

            if( strcasecmp( $ep->getParams()['form']->getTableName(),rex::getTable('markitup_profiles') ) == 0 )
            {
                include_once( 'functions/cache_markitup_profiles.php');
                echo markitup_cache_update( );
            }

        } );

        // Ressourcen einbinden

		rex_view::addJsFile($this->getAssetsUrl('jquery.markitup.js'));
		rex_view::addJsFile($this->getAssetsUrl('autosize.min.js'));
		rex_view::addJsFile($this->getAssetsUrl('scripts.js'));
		rex_view::addCssFile($this->getAssetsUrl('style.css'));
		if (file_exists($this->getAssetsPath('skin.css'))) {
			rex_view::addCssFile($this->getAssetsUrl('skin.css'));
		}
        rex_view::addCssFile($this->getAssetsUrl('cache/markitup_profiles.css'));
    	rex_view::addJsFile($this->getAssetsUrl('cache/markitup_profiles.js'));

    }
