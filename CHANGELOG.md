# MarkItUp Changelog

## [3.7.1](https://github.com/FriendsOfREDAXO/markitup/releases/tag/3.7.1) – 19.10.2021

### Bugfixes

- Fix invalid `package.yml` file


## [3.7.0](https://github.com/FriendsOfREDAXO/markitup/releases/tag/3.7.0) – 19.10.2021

### Features

- Dressed up for new dark mode (REDAXO 5.13) 🦇


## [3.6.1](https://github.com/FriendsOfREDAXO/markitup/releases/tag/v3.6.1) – 06.05.2021

### Bugfixes

- Update autosize


## [3.6.0](https://github.com/FriendsOfREDAXO/markitup/releases/tag/3.5.2) – 25.08.2020

### Features ([#111](https://github.com/FriendsOfREDAXO/markitup/pull/113))

- Enhanced `markitup::yformLinkInUse`
  limit reported inUse-Items to n items per table
- Support for own InUse-Queries: `markitup::yformInUseWhere`
  get a tailored where-clause 
  
See section »Editor integrieren« within documentation for infos on how to use the feature!
  
### Bugfixes

- Fehlerkorrekturen zu YForm-Support ([#111](https://github.com/FriendsOfREDAXO/markitup/pull/113))


## [3.5.1](https://github.com/FriendsOfREDAXO/markitup/releases/tag/3.5.1) – 16.08.2020

### Bugfixes

- Fehlerkorrekturen zu YForm-Support ([#112](https://github.com/FriendsOfREDAXO/markitup/pull/112))


## [3.5.0](https://github.com/FriendsOfREDAXO/markitup/releases/tag/3.5.0) – 15.08.2020

### Features

- Links to YForm tables ([#111](https://github.com/FriendsOfREDAXO/markitup/pull/111), [@christophboecker](https://github.com/christophboecker))  
  See section »Editor integrieren« within documentation for infos on how to use the feature!


## [3.4.0](https://github.com/FriendsOfREDAXO/markitup/releases/tag/3.4.0) – 15.08.2020

### Features

* Hilfeseiten aktualisiert ([#109](https://github.com/FriendsOfREDAXO/markitup/pull/109))
* Textile-Komponente auf 3.7.6 aktualisiert und neue Methode `parse()` verwendet ([#110](https://github.com/FriendsOfREDAXO/markitup/pull/110))
* Standardprofile auf Englisch
* Changelog angelegt


## [3.3.4](https://github.com/FriendsOfREDAXO/markitup/releases/tag/3.3.4) – 08.12.2019

Versionsnummer angepasst...


## [3.3.3](https://github.com/FriendsOfREDAXO/markitup/releases/tag/3.3.3) – 08.12.2019

Textile: keine automatisch generierten title Attribute für interne und media Links


## [3.3.2](https://github.com/FriendsOfREDAXO/markitup/releases/tag/3.3.2) – 25.10.2019

- mblock compatibility fix @alexplusde 
- images smaller @ImgBotApp 


## [3.3.0](https://github.com/FriendsOfREDAXO/markitup/releases/tag/3.3.0) – 01.07.2019

In den Editor-Profilen können schon immer individuelle Textbausteine (Snippets) hinterlegt werden. Um deren Verwaltung und auch komplexe Snippets zu vereinfachen, gibt es jetzt eine Snippet-Verwaltung. 

- Erfassen der Snippets in einer Datenbank-Tabelle
- Einbinden in Profile über den Snippet-Namen
- Mehrsprachingkeit mit automatischer Sprachauswahl und Fallback


## [3.2.0](https://github.com/FriendsOfREDAXO/markitup/releases/tag/3.2.0) – 27.10.2018

- Optimiert das Ladeverhalten, boot.php entschlackt @christophboecker
- Traducción en castellano
- Umstellung auf 'includeCurrentPageSubPath' @christophboecker
- translation to spanish


## [3.1.0](https://github.com/FriendsOfREDAXO/markitup/releases/tag/3.1.0) – 05.05.2018

## Features

- Dokumentation (#78 @madiko)

## Bugfixes

- Dropdown (#77 @christophboecker)


## [3.0.0](https://github.com/FriendsOfREDAXO/markitup/releases/tag/3.0.0) – 19.09.2016

Updates:

- Addon-Name: rex_-Prefix entfernt (#48)

---

⚠️ Achtung, das Addon wird unter der neuen Bezeichnung **`markitup`** weiter geführt. Solltest du Funktionen oder Dateien verwenden, die noch die alte Addonbezeichnung beinhalten, müsstest du diese bitte anpassen. Ein typisches Beispiel: `if (rex_addon::get('rex_markitup')->isAvailable()) { … }`.

Danke für deine Mithilfe!
