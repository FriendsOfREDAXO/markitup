# MarkItUp Editor

Integrates the [MarkItUp Editor](http://markitup.jaysalvat.com/) (Markdown and Textile) into REDAXO CMS.

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/markitup/assets/rex_markitup.png)

Textarea fields with the class `markitupEditor-%profileName%` will automatically be turned into a proper MarkItUp editor field, for example:

```html
<textarea id="markitup_full" class="markitupEditor-full"></textarea>
```

You can setup multiple profiles with different configurations to use with the MarkItUp editor in the backend.

You can even define your own skin for the editor using CSS. To do so, add a file called `skin.css` in the folder `/assets/addons/markitup`.


## Markdown

Module input:

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

Module output:

```php
<?php
  use FriendsOfRedaxo\MarkItUp\MarkItUp;
  echo MarkItUp::parseOutput ('markdown', 'REX_VALUE[id=1 output="html"]');
?>
```

or

```php
<?php
  echo FriendsOfRedaxo\MarkItUp\MarkItUp::parseOutput ('markdown', 'REX_VALUE[id=1 output="html"]');
?>
```


## Textile

Module input:

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

Module output:

```php
<?php
  use FriendsOfRedaxo\MarkItUp\MarkItUp;
  echo MarkItUp::parseOutput ('textile', 'REX_VALUE[id=1 output="html"]');
?>
```

or

```php
<?php
  echo FriendsOfRedaxo\MarkItUp\MarkItUp::parseOutput ('textile', 'REX_VALUE[id=1 output="html"]');
?>
```

## Create new profile

Example code for use in templates, modules or AddOns:

```php
<?php
  use FriendsOfRedaxo\MarkItUp\MarkItUp;
  if (!MarkItUp::profileExists('simple')) {

    // name, description, type (markdown/textile), min height, max height, url type (relative/absolute), buttons
    MarkItUp::insertProfile ('simple', 'Simple editor', 'textile', 300, 800, 'relative', 'bold,italic');
  }
?>
```
