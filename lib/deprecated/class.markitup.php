<?php

/**
 * Mit Version 3.8 wird das Addon auf namespaces umgestellt. Für den Übergang wird die alte Klasse
 * auf diesem Wege weiterhin bereitgestellt. Diese Klasse fällt mit Version 4.0.0 weg, nachdem
 * Redaxo auf Composer als Installer umgestellt wurde.
 *
 * @deprecated 4.0.0 Aufrufe auf "FriendsOfRedaxo\MarkItUp\MarkItUp" (Namespace) umstellen
 * @see https://github.com/orgs/FriendsOfREDAXO/discussions/40
 */

class markitup extends FriendsOfRedaxo\MarkItUp\Markitup
{
}
