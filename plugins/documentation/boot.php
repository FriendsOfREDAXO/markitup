<?php
// CSS und JavaScript für Dokumentation einbinden
if (rex::isBackend() && is_object(rex::getUser())) {
    if (rex_be_controller::getCurrentPage() == $this->getProperty('package')) {
        rex_view::addCssFile($this->getAssetsUrl('documentation.css'));
        rex_view::addJsFile($this->getAssetsUrl('documentation.js'));
    }
}
