# MarkItUp Editor

Integriert den [MarkItUp-Editor](http://markitup.jaysalvat.com/) (Markdown und Textile) in REDAXO CMS.

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/markitup/assets/rex_markitup.png)

Textareas mit der Klasse `markitupEditor-%profileName%` werden automatisch von einem normalen Texteingabefeld in einen MarkItUp-Editor umgewandelt, zum Beispiel:

```html
<textarea id="markitup_full" class="markitupEditor-full"></textarea>
```

Im Backend können verschiedene Profile mit unterschiedlichen Konfigurationseinstellungen für den MarkItUp-Editor angelegt werden.

Es gibt die Möglichkeit, dem Editor eigene CSS-Styles zu geben. Dafür muss lediglich im Ordner `/assets/addons/markitup` eine Datei mit dem Namen `skin.css` angelegt werden.


## Markdown

Modul-Eingabe:

```html
<fieldset class="form-horizontal">
  <div class="form-group">
    <label class="col-sm-2 control-label" for="value-1">VALUE 1</label>
    <div class="col-sm-10">
      <textarea class="form-control markitupEditor-markdown_full" id="value-1" name="REX_INPUT_VALUE[1]">REX_VALUE[1]</textarea>
    </div>
  </div>
</fieldset>
```

Modul-Ausgabe:

```php
<?php
  echo markitup::parseOutput ('markdown', 'REX_VALUE[id=1 output="html"]');
?>
```

## Textile

Modul-Eingabe:

```html
<fieldset class="form-horizontal">
  <div class="form-group">
    <label class="col-sm-2 control-label" for="value-1">VALUE 1</label>
    <div class="col-sm-10">
      <textarea class="form-control markitupEditor-textile_full" id="value-1" name="REX_INPUT_VALUE[1]">REX_VALUE[1]</textarea>
    </div>
  </div>
</fieldset>
```

Modul-Ausgabe:

```php
<?php
  echo markitup::parseOutput ('textile', 'REX_VALUE[id=1 output="html"]');
?>
```

## Profil anlegen

Beispielcode zur Nutzung in Templates, Modulen oder AddOns:

```php
<?php
  if (!markitup::profileExists('simple')) {

    // name, description, type (markdown/textile), min height, max height, url type (relative/absolute), buttons
    markitup::insertProfile ('simple', 'Simple editor', 'textile', 300, 800, 'relative', 'bold,italic');
  }
?>
```
