<?php

// Legt die Dateien /addons/assets/addons/markitup/cache/markitup_profiles.[css|js] an.
// Einbinden und ausführen mit
//      include_once
//      $message = markitup_cache_update( )

function markitup_cache_update( )
{
    $message = '';

    $profiles = rex_sql::factory()->setQuery('SELECT `name`, `type`, `minheight`, `maxheight`, `markitup_buttons` FROM `'.rex::getTable('markitup_profiles').'` ORDER BY `name` ASC')->getArray();

    // Liste der Sprachen, die in "snippets" vorkommen plus '--' (=Fallback bzw. für alle anderen)
    $languages = rex_sql::factory()->setQuery('SELECT DISTINCT `lang` FROM `'.rex::getTable('markitup_snippets').'`')->getArray();
    $languages = array_unique( array_merge( ['--'], array_column($languages,'lang') ));
    $fallback = array_unique(
        array_map(
            function($l) { return substr($l,0,2); },
            rex::getProperty('lang_fallback', [])
        )
    );
    // alte Dateien löschen
    rex_dir::delete( rex_path::addonAssets('markitup', 'cache/' ), false );


    foreach( $languages as $language ) {

        $cssCode = [];
        $jsCode = [];

        if( $language == '--' ) {
            $languageSet = [ $language ];
        } else {
            $languageSet = array_unique( array_merge( [ $language, '--'], $fallback ) );
        }

        $jsCode[] = 'function markitupInit() {';
        foreach ($profiles as $profile) {
            $cssCode[] = '  textarea.markitupEditor-'.$profile['name'].' { min-height: '.$profile['minheight'].'px; max-height: '.$profile['maxheight'].'px; }';
            $jsCode[] = '  $(\'.markitupEditor-'.$profile['name'].'\').not(\'.markitupActive\').markItUp({';

            $jsCode[] = '    nameSpace: "markitup_'.$profile['type'].'",';
            switch ($profile['type']) {
                case 'textile':
                    $jsCode[] = '    onShiftEnter: {keepDefault:false, replaceWith:\'\n\n\'},';
                break;
            }

            $jsCode[] = '    markupSet: [';
            $jsCode[] = '      '.markitup_cache_defineButtons($profile['type'], $profile['markitup_buttons'], $languageSet);
            $jsCode[] = '    ]';
            $jsCode[] = '  }).addClass(\'markitupActive\');';
        }

        $jsCode[] = '}';
        $jsCode[] = '$(document).on(\'rex:ready pjax:success\',function() {';
        $jsCode[] = '  markitupInit();';
        $jsCode[] = '  autosize($("textarea[class*=\'markitupEditor-\']"));';
        $jsCode[] = '});';

        $languageDir = rex_path::addonAssets('markitup', "cache/$language/" );
        if (!rex_file::put($languageDir.'markitup_profiles.css', implode(PHP_EOL, $cssCode))) {
            $message .= rex_view::error( rex_i18n::msg('markitup_cache',true,"<i>/assets/markitup/cache/$language/markitup_profiles.css</i>") );
        }
        unset ( $cssCode );

        if (!rex_file::put($languageDir.'markitup_profiles.js', implode(PHP_EOL, $jsCode))) {
            $message .= rex_view::error( rex_i18n::msg('markitup_cache',true,"<i>/assets/markitup/cache/$language/markitup_profiles.js</i>" ) );
        }
        unset ( $jsCode );

    }

    if( $message ) rex_logger::logError( E_WARNING, $message, 'Function: '.__FUNCTION__, __LINE__);

    return $message;
}

function markitup_cache_defineButtons($type, $profileButtons, $languageSet) {

    static $markItUpButtons = null;

    if (!$markItUpButtons) {
        $markItUpButtons = rex_file::getConfig(rex_path::addon('markitup', 'config.yml'));
    }

    $buttonString = '';
    $profileButtons = explode(',', $profileButtons);
    foreach ($profileButtons as $profileButton) {

        $profileButton = trim($profileButton);
        $options = [];

        if ($profileButton == '|') {
            $buttonString .= '{separator:\'&nbsp;\'},';
            continue;
        }

        if (preg_match('/(.*)\[(.*)\]/', $profileButton, $matches)) {
            $profileButton = $matches[1];

            //Start - explode parameters
            $parameters = explode('|', $matches[2]);
            $parameterString = '';

            if( $profileButton == 'clips' ) {
                foreach ($parameters as $parameter) {
                    if (strpos($parameter, '=') !== false) {
                        list($key, $value) = explode('=',$parameter);
                        $label = strtolower($value);
                        $snippets = rex_sql::factory()->getArray( 'SELECT lang, content FROM '.rex::getTable('markitup_snippets').' WHERE name like :name',[':name'=>$label], PDO::FETCH_KEY_PAIR );
                        if( $snippets ) {
                            foreach( $languageSet as $language ) {
                                if( isset( $snippets[$language] ) ) {
                                    $value = str_replace(["\r\n","\n","\r"],'\\\n',$snippets[$language]);
                                    break;
                                }
                            }
                        }
                        $options[] = ['name' => addslashes($key), 'openWith' => addslashes($value)];
                    } else {
                        $options[] = $parameter;
                    }
                }
            } elseif( $profileButton == 'yform' ) {
                // yform[tablename|...]
                $data = [];
                foreach ($parameters as $table) {
                    $options[] = $table;
                    $data[$table] = [
                        'name' => 'profiles_buttons_yform_option_'.$table,
                        'replaceWith' => [ $type => 'function(h) {return btn'.ucfirst($type).'YformCallback(h,"'.rex::getTable($table).'");}']
                    ];
                    if( !rex_i18n::hasMsg('markitup_'.$data[$table]['name']) ) rex_i18n::addMsg('markitup_'.$data[$table]['name'],$table);
                }
                $markItUpButtons[$profileButton]['children'] = $data;
            } else {
                foreach ($parameters as $parameter) {
                    if (strpos($parameter, '=') !== false) {
                        list($key, $value) = explode('=',$parameter);
                        $options[] = ['name' => addslashes($key), 'openWith' => addslashes($value)];
                    } else {
                        $options[] = $parameter;
                    }
                }
            }


            //End - explode parameters

        }

        $buttonString .= "{";

        foreach (['name', 'key', 'openWith', 'closeWith', 'className', 'replaceWith'] as $property) {
            if (!empty($markItUpButtons[$profileButton][$property])) {
                if (in_array($property, ['openWith', 'closeWith'])) {
                    $buttonString .= "  ".$property.":'".$markItUpButtons[$profileButton][$property][$type]."',".PHP_EOL;
                } else if ($property == 'replaceWith') {
                    $buttonString .= "  ".$property.":".$markItUpButtons[$profileButton][$property][$type].",".PHP_EOL;
                } else if ($property == 'name') {
                    $buttonString .= "  ".$property.":'".rex_i18n::msg('markitup_'.$markItUpButtons[$profileButton][$property])."',".PHP_EOL;
                } else {
                    $buttonString .= "  ".$property.":'".$markItUpButtons[$profileButton][$property]."',".PHP_EOL;
                }
            }
        }

        //Start - dropdown
            if (!empty($options)) {
                $buttonString .= "  dropMenu: [";

                foreach ($options as $option) {
                    $buttonString .= "{";

                    if (is_array($option)) {
                        foreach ($option as $property => $value) {
                            $buttonString .= "  ".$property.":'".$value."',";
                        }
                    } else {
                        foreach (['name', 'key', 'openWith', 'closeWith', 'replaceWith'] as $property) {
                            if (!empty($markItUpButtons[$profileButton]['children'][$option][$property])) {
                                if (in_array($property, ['openWith', 'closeWith'])) {
                                    $buttonString .= "  ".$property.":'".$markItUpButtons[$profileButton]['children'][$option][$property][$type]."',".PHP_EOL;
                                } else {
                                    if ($property == 'name') {
                                        $buttonString .= "  ".$property.":'".rex_i18n::msg('markitup_'.$markItUpButtons[$profileButton]['children'][$option][$property])."',".PHP_EOL;
                                    } else if ($property == 'replaceWith') {
                                        $buttonString .= "  ".$property.":".$markItUpButtons[$profileButton]['children'][$option][$property][$type].",".PHP_EOL;
                                    } else {
                                        $buttonString .= "  ".$property.":'".$markItUpButtons[$profileButton]['children'][$option][$property]."',".PHP_EOL;
                                    }
                                }
                            }
                        }
                    }

                    $buttonString .= "},";
                }

                $buttonString .= "  ]";
            }
        //End - dropdown

        $buttonString .= "},";
    }

    return $buttonString;
}
