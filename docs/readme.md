# Kleine Hinweise zu den Handbuchseiten:

Die Seiten sind so wie sie jetzt sind aus der Auflösung des **documentation**-Plugins
entstanden. Die neue Struktur ermöglicht grundsätzlich ebenfalls Mehrsprachigkeit.
Zusätzlich kann das Handbuch über die Berechtigung `markitup[manual]` auch anderen
Benutzerrollen zugewiesen werden.

## Verzeichnisse und Dateien

Basis ist das Verzeichnis `/redaxo/src/addons/markitup/docs`. 

Je gewünschter Backend-Sprache muss ein Unterverzeichnis angelegt werden. Der
Verzeichnisname ist der jeweilige Sprach-Code (`de`, `es`, `en`, ...).

Beispiel:

- `/redaxo/src/addons/markitup/docs/de`


Sofern in die Markdown-Dateien Bilder eingebunden sind, müssen diese in einem
ähnliche Verzeichnis unter den Assets liegen. Konkret sind das

- `/redaxo/src/addons/markitup/assets/docs` im Addon
- `/assets/addons/markitup/docs` nach Installation.

Für sprachbezogene Bilder werden hier wiederum Unterverzeichnisse je Sprache angelegt.
Die Verweise aus den Markdown-Dateien auf die Bilder erfolgen als

```markdown
![snippets](../assets/addons/markitup//docs/de/snippet.jpg)
```

Nicht sprachspezifische Bilder, die für alle Sprachvarianten genutzt werden,
sollen direkt im docs-Verzeichnis liegen:

```markdown
![logo](../assets/addons/markitup//docs/logo.jpg)
```

## package.yml

Die Seiten selbst werden als normale Addon-Seiten in der `package.yml`
definiert. 
```yml
page:
    ...
    subpages:
        ...
        manual: 
            title: 'translate:manual'
            icon: rex-icon fa-book
            perm: 'markitup[manual]'
            subpages:
                intro:
                    title: 'translate:markitup_manual_intro'
                    icon: rex-icon rex-icon-info
                    subPath: main_intro.md
                textile:
                    title: 'translate:markitup_manual_textile'
                    icon: rex-icon rex-icon-article
                    subPath: howto_textile.md
                markdown:
                    title: 'translate:markitup_manual_markdown'
                    icon: rex-icon rex-icon-article
                    subPath: howto_markdown.md
                developer:
                    title: 'translate:markitup_manual_developer'
                    icon: rex-icon fa-code
                    subPath: howto_integration.md
```

Unter `subPath` wird nur der Dateiname angegeben!

Die `pages/index.php` des Addons hat zusätzlichen Code, der aus der aktuellen Sprache
und ggf. den Fallback-Sprachen ermittelt, welches Sprachverzeichnis herangezogen wird.

Beispiel:
- aktuelle Sprache: `de`
- Verzeichnis `docs/de` gefunden
- Bilde die Pfadnamen als `...../redaxo/src/addons/markitup/docs/de/«subPath»`

Beispiel:
- aktuelle Sprache: `se`
- Verzeichnis `docs/se` nicht gefunden
- suche eines der Fallback-Sprachen-Verzeichnisse; fündig bei `docs/de`
- Bilde die Pfadnamen als `...../redaxo/src/addons/markitup/docs/de/«subPath»`

