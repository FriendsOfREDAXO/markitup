<?php

/** @var rex_addon $this */

/**
 * Wenn die Handbuch-Seite aktiviert ist, dann die 
 * Erklärung-Seite ausblenden
 * Für die Handbuchseiten den richtigen Ländercode setzen:
 * 
 * Für User, die das Handbuch anzeigen dürfen, wird die
 * Overview-Seite ausgeblendet. Gilt auch für Admins.
 */

$manualPage = rex_be_controller::getPageObject('markitup/manual');
if( null !== $manualPage) {
    /**
     * Gibt es die Handbuchseiten in der aktuellen Sprache?
     * Verzeichnis: markitup/docs/«lang»
     * Wenn nicht werden auch die Fallback-Sprachen durchprobiert.
     */
    // 
    $language = rex_clang::getCurrent()->getCode();
    if(!is_dir($this->getPath('docs/'.$language))) {
        $language = '';
        foreach (rex::getProperty('lang_fallback',[]) as $lang) {
            if(is_dir($this->getPath('docs/'.$language))) {
                $language = $lang;
                break;
            }
        }
    }
    /**
     * Je Seite ist nur der Name der Handbuchdatei abgegeben
     * Der SubPath der Handbuchseiten wird entsprechend des
     * Language-Codes auf das Prachverzeichnis gelegt
     * Achtung: keine Überprüfung mehr, ob die Dateien existieren.
     */
    if( '' < $language) {
        foreach( $manualPage->getSubpages() as $page) {
            $subPath = sprintf('docs/%s/%s',$language,$page->getSubPath());
            $page->setSubPath($this->getPath($subPath));
        }
        $page = rex_be_controller::getPageObject('markitup/overview');
        $page->setHidden(true);
    } else {
        // mangels Handbuchseitenverzeichnis die Seite ausblenden
        $manualPage->setHidden(true);
    }
}
// Die Subpages müssen dem Titel nicht mehr übergeben werden
echo rex_view::title($this->i18n('title'));

// Falls die aufgerufene (HAndbuch-)Seite nicht existiert ...
$currentPage = rex_be_controller::getCurrentPageObject();
if( !is_file($currentPage->getSubPath())) {
    echo rex_view::error('Sorry, Seite nicht gefunden!');
    return;
}

// Subpages können über diese Methode eingebunden werden. So ist sichergestellt, dass auch Subpages funktionieren,
// die von anderen Addons/Plugins hinzugefügt wurden
rex_be_controller::includeCurrentPageSubPath();
