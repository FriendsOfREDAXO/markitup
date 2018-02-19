MarkItUp Editor
================================

Addon für REDAXO 5: Bindet den [MarkItUp-Editor](http://markitup.jaysalvat.com/) im Backend ein, um Texte in Textile oder Markdown zu schreiben.

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/markitup/assets/rex_markitup.png)

# Allgemein

Textareas mit der Klasse `markitupEditor-%profileName%` werden automatisch von einem normalen Texteingabefeld in einen MarkItUp-Editor umgewandelt, z.B.: `<textarea id="markitup_full" class="form-control markitupEditor-full"></textarea>`

Im Backend können verschiedene Profile mit unterschiedlichen Konfigurationseinstellungen für den MarkItUp-Editor angelegt werden.

Die Klasse `form-control` wird im REDAXO-Backend benötigt. Es gibt die Möglichkeit, dem Editor eigene CSS-Styles zu geben. Dafür muss lediglich im Ordner `/assets/addons/markitup` eine Datei mit dem Namen `skin.css` angelegt werden.

# Beispiel 1: Markdown-Editor

## Modul-Eingabe
```
<fieldset class="form-horizontal">
  <div class="form-group">
    <label class="col-sm-2 control-label" for="value-1">VALUE 1</label>
    <div class="col-sm-10">
      <textarea class="form-control markitupEditor-markdown_full" id="value-1" name="REX_INPUT_VALUE[1]">REX_VALUE[1]</textarea>
    </div>
  </div>
</fieldset>
```

## Modul-Ausgabe
```
<?php
  echo markitup::parseOutput ('markdown', 'REX_VALUE[id=1 output="html"]');
?>
```


# Beispiel 2: Textile-Editor

## Modul-Eingabe
```
<fieldset class="form-horizontal">
  <div class="form-group">
    <label class="col-sm-2 control-label" for="value-1">VALUE 1</label>
    <div class="col-sm-10">
      <textarea class="form-control markitupEditor-textile_full" id="value-1" name="REX_INPUT_VALUE[1]">REX_VALUE[1]</textarea>
    </div>
  </div>
</fieldset>
```

## Modul-Ausgabe
```
<?php
  echo markitup::parseOutput ('textile', 'REX_VALUE[id=1 output="html"]');
?>
```

# Beispiel 3: Profil automatisch anlegen

```
<?php
  if (!markitup::profileExists('simple')) {
    //Name, Beschreibung, Typ (markdown oder textile), Mindesthöhe, Maximalhöhe, URL-Art (relativ oder absolut), Buttons
    markitup::insertProfile ('simple', 'Lorem Ipsum', 'textile', 300, 800, 'relative', 'bold,italic');
  }
?>
```
