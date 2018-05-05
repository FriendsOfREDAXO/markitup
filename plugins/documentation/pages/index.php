<?php
function docsGlobRecursive($pattern, $flags = 0)
{
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern) . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
    {
        $files = array_merge($files, docsGlobRecursive($dir . DIRECTORY_SEPARATOR . basename($pattern), $flags));
    }
    return $files;
}

$navi = '';
$content = '';
$langselect = '';

$ajax = rex_request('ajax', 'string', '');
$doclang = rex_request('doclang', 'string', '');
$formsubmit = rex_request('formsubmit', 'string', '');
if ($formsubmit) {
    rex_set_session('addon_documentation[doclang]', $doclang);
}

// Addon/Plugin-Informationen
$addon = rex_be_controller::getCurrentPagePart(1);
$docplugin = rex_be_controller::getCurrentPagePart(2);
$plugin = rex_plugin::get($addon, $docplugin);

// Default Navigation aus package.yml
$default_navi = $plugin->getProperty('defaultnavi');
if (!$default_navi) {
    $default_navi = 'main_navi.md';
}

// Default Intro aus package.yml
$default_intro = $plugin->getProperty('defaultintro');
if (!$default_intro) {
    $default_intro = 'main_intro.md';
}

// User-Backend-Sprache
$lang = rex::getUser()->getLanguage();
// Feste Sprache der Dokumentation aus package.yml
if ($plugin->getProperty('documentationlang')) {
    $lang = $plugin->getProperty('documentationlang');
    if (!rex_session('addon_documentation[doclang]', 'string', '')) {
        rex_set_session('addon_documentation[doclang]', $lang);
    }
}

// Bei mehreren verfügbaren Sprachen Sprachwähler aufbauen
$path = rex_path::plugin($addon, $docplugin , 'docs/');
$docs = [];
foreach (glob($path . '*', GLOB_ONLYDIR) as $dir) {
    if (file_exists($path . basename($dir) . '/' . $default_navi)) {
        $docs[basename($dir)] = basename($dir);
    }
}

if (count($docs) > 1) {
    if ($doclang) {
        $lang = $doclang;
    }
    if (rex_session('addon_documentation[doclang]', 'string', '')) {
        $lang = rex_session('addon_documentation[doclang]', 'string', '');
    }
    $sel_lang = new rex_select();
    $sel_lang->setStyle('class="form-control"');
    $sel_lang->setName('doclang');
    $sel_lang->setId('doclang');
    $sel_lang->setSize(1);
    $sel_lang->setSelected($lang);
    foreach ($docs as $l) {
        $sel_lang->addOption($l, $l);
    }
    $langselect = '
    <form action="' . rex_url::currentBackendPage() . '" method="post">
    <input type="hidden" name="formsubmit" value="1" />
        ' . $sel_lang->get() . '
    </form>
    ';
}

// Pfad zusammenbauen aus Addon + Plugin + Sprache
$path = rex_path::plugin($addon, $docplugin , 'docs/' . $lang . '/');

// vorhandene Dateien ermitteln
if ($ajax <> 'true') {
    $files = [];
    $filetypes = ['*.md', '*.gif', '*.png', '*.jpg', '*.jpeg'];
    $search = [$path, "\\"];
    $replace = ['', '/'];
    foreach ($filetypes as $mask) {
	    foreach (docsGlobRecursive($path . $mask, GLOB_BRACE) as $filename) {
            $filename = str_replace($search, $replace, $filename);
            $files[$filename] = $filename;
        }
    }
    rex_set_session('addon_documentation[files]', $files);
} else {
    $files = rex_session('addon_documentation[files]', 'array', null);
}

// Bild ausgeben wenn Parameter document_image gesetzt ist und die Datei existiert
$docimage = rex_request('document_image', 'string', '');
if ($docimage != '' && isset($files[$docimage])) {
    while (ob_get_length()) {
        ob_end_clean();
    }

    $filename = rex_request('document_image', 'string');
    $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    $ctype = '';
    switch( $file_extension ) {
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpeg"; break;
        default:
    }
    if ($ctype) {
        header('Content-type: ' . $ctype);
    }

    rex_response::sendfile($path . rex_request('document_image', 'string'), $ctype);
    exit;
}

// Navigation aus $default_navi
if ($ajax <> 'true') {
    $navi = trim(rex_file::get($path . $default_navi));
    if ($navi == '') {
        $navi = rex_view::error(rex_i18n::rawMsg('documentation_navinotfound', $lang . '/' . $default_navi));
    }
}

// Content aus Parameter document_file, sonst aus $default_intro
$file = rex_request('document_file', 'string', $default_intro);
$content = trim(rex_file::get($path . basename($file)));
if ($content == '') {
    $content = rex_view::warning(rex_i18n::rawMsg('documentation_filenotfound', $lang . '/' . $file, $this->getProperty('supportpage')));
}

// Images im Inhalt ersetzen
// ![Alt-Text](bildname.png)
// ![Ein Screenshot](screenshot.png)
foreach ($files as $i_file) {
    $search = '#\!\[(.*)\]\((' . $i_file . ')\)#';
    $replace = '<img src="index.php?page='. $addon . '/' . $docplugin . '&document_image=$2" alt="$1" title="$1" style="max-width:100%" />';
    $content = preg_replace($search, $replace, $content);
    $navi = preg_replace($search, $replace, $navi);
}

// Parse Navigation & Content
if (class_exists('rex_markdown')) {
    $parser = rex_markdown::factory();
    $navi = $parser->parse($navi);
    $content = $parser->parse($content);
} else if (class_exists('Parsedown')) {
    $parser = new Parsedown();
    $navi = $parser->text($navi);
    $content = $parser->text($content);
} else {
    $navi = '';
    $content = rex_view::error(rex_i18n::rawMsg('documentation_noparser'));
}

// Links in Navigation ersetzen
if ($ajax <> 'true') {
    foreach ($files as $i_file) {
        $file = rex_request('document_file', 'string', $default_intro);
        $current = ($i_file == $file) ? ' current' : '';
        $search = '#href="(' . $i_file . ')"#';
        $replace = 'href="index.php?page=' . $addon . '/' . $docplugin . '&document_file=$1"';
        $navi = preg_replace($search, $replace, $navi);
        $navi = str_replace('document_file=' . $i_file .'"', 'document_file=' . $i_file .'" class="doclink' . $current . '"', $navi);
    }
}

// Links im Inhalt ersetzen
foreach ($files as $i_file) {
    $search = '#href="(' . $i_file . ')"#';
    $replace = 'href="index.php?page=' . $addon . '/' . $docplugin . '&document_file=$1"';
    $content = preg_replace($search, $replace, $content);
    $content = str_replace('document_file=' . $i_file .'"', 'document_file=' . $i_file .'" class="doclink"', $content);
}

// Bei Parameter ajax=true nur den Inhalt ausgeben
if ($ajax == 'true') {
    while (ob_get_length()) {
        ob_end_clean();
    }
    echo $content;
    exit;
}

// Navigation
$fragment = new rex_fragment();
$fragment->setVar('title', rex_i18n::rawMsg('documentation_navigation_title'), false);
$fragment->setVar('body', $navi, false);
$navi = $fragment->parse('core/page/section.php');

// Inhalt
$fragment = new rex_fragment();
$fragment->setVar('title', rex_i18n::rawMsg('documentation_content_title', $file), false);
$fragment->setVar('body', $content, false);
$content = $fragment->parse('core/page/section.php');

// Navigation und Inhalt ausgeben
echo '
<section class="addon_documentation">
    <div class="row">
        <div class="col-md-4 addon_documentation-navi">' . $langselect . $navi . '
        </div>
        <div class="col-md-8 addon_documentation-content">' . $content . '
        </div>
    </div>
</section>

';
