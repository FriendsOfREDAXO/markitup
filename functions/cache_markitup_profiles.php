<?php

/**
 * Mit Version 3.8 wird das Addon auf namespaces umgestellt. Für den Übergang werden die
 * alten Funktionen auf diesem Wege weiterhin bereitgestellt. Diese Datei und damit die
 * Funktionen fallen mit Version 4.0.0 weg, nachdem Redaxo auf Composer als Installer
 * umgestellt wurde.
 */

// Legt die Dateien /addons/assets/addons/markitup/cache/markitup_profiles.[css|js] an.
// Einbinden und ausführen mit
//      include_once
//      $message = markitup_cache_update( )

/**
 * @deprecated 4.0.0 Aufrufe auf "FriendsOfRedaxo\MarkItUp\Cache::update" (Namespace) umstellen
 * @see https://github.com/orgs/FriendsOfREDAXO/discussions/40
 */
function markitup_cache_update()
{
    return FriendsOfRedaxo\MarkItUp\Cache::update();
}

/**
 * @deprecated 4.0.0 Aufrufe auf "FriendsOfRedaxo\MarkItUp\Cache::defineButtons" (Namespace) umstellen
 * @see https://github.com/orgs/FriendsOfREDAXO/discussions/40
 */
function markitup_cache_defineButtons($type, $profileButtons, $languageSet)
{
    return FriendsOfRedaxo\MarkItUp\Cache::defineButtons($type, $profileButtons, $languageSet);
}
