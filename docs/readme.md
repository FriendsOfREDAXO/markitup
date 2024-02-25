# Kleine Hinweise zu den Handbuchseiten:

Die Seiten sind so wie sie jetzt sind aus der Auflösung des **documentation**-Plugins
entstanden. Die neue Struktur ermöglicht grundsätzlich ebenfalls Mehrsprachigkeit.
Zusätzlich kann das Handbuch über die Berechtigungsverwaltung auch anderen
Benutzerrollen als dem Admin zugewiesen werden.

## Verzeichnisse und Dateien

Basis ist das Verzeichnis `..../redaxo/src/addons/markitup/docs`. 

Dort werden die Dateien (Markdown, 'xxx.md') unter dem Namen abgelegt, mit dem die
Seite in der `package.yml`eingetragen wird. 

Beispiel:

- Datei: `..../redaxo/src/addons/markitup/docs/main_intro.md`
- package.yml: `subPath: docs/main_intro.md`

Sollten die Teste auch zukünftig in anderen Backend-Sprachen angeboten werden,
müssen die sprachbezogenen Versionen zusätzlich vor dem Suffix den Sprachcode aufweisen:

Beispiel:

- Datei: `..../redaxo/src/addons/markitup/docs/main_intro.en.md`
- package.yml: `subPath: docs/main_intro.md`

REDAXO sucht zunächst nach der Datei zum aktuellen Sprach-Code (`main_intro.en.md`)
und dann nach der Datei ohne Sprachcode (`main_intro.md`).

Sofern in die Markdown-Dateien Bilder eingebunden sind, müssen diese in einem
ähnliche Verzeichnis unter den Assets liegen. Konkret sind das

Beispiel:

- `..../redaxo/src/addons/markitup/assets/docs` im Addon
- `..../assets/addons/markitup/docs` nach Installation.

Sprachbezogene Bilddateien müssen wiederum je Sprache passende Dateinamen haben,
aus denen die Sprachzugehörigkeit hervor geht. Hier greift kein Fallback wie bei
MD-Dateien. 

Beispiel:

- für deutschsprachige Texte: `snippet.de.jpg`
- im Addon; `..../redaxo/src/addons/markitup/assets/docs/snippet.de.jpg`
- Assets: `..../assets/addons/markitup/docs/snippet.de.jpg` (nach Installation)

Die Verweise aus den Markdown-Dateien auf die Bilder erfolgen als

```markdown
![snippets](../assets/addons/markitup//docs/snippet.de.jpg)
```

Nicht sprachspezifische Bilder, die für alle Sprachvarianten genutzt werden,
sollten ebenfalls als "für alle" gekennzeichnet sein.

Beispiel:

```markdown
![logo](../assets/addons/markitup/docs/logo.__.jpg)
```

## package.yml

Die Seiten selbst werden als normale Addon-Seiten in der `package.yml`
definiert. Die übergeordnete Seite ist `manual`.
```yml
page:
    ...
    subpages:
        ...
        manual: 
            title: 'translate:manual_a'
            icon: rex-icon fa-book
            perm: 'markitup[manual]'
            subpages:
                intro:
                    title: 'translate:markitup_manual_intro'
                    icon: rex-icon rex-icon-info
                    subPath: docs/main_intro.md
                textile:
                    title: 'translate:markitup_manual_textile'
                    icon: rex-icon rex-icon-article
                    subPath: docs/howto_textile.md
                markdown:
                    title: 'translate:markitup_manual_markdown'
                    icon: rex-icon rex-icon-article
                    subPath: docs/howto_markdown.md
        developer: 
            title: 'translate:manual_b'
            icon: rex-icon fa-book
            perm: 'markitup[developer]'
            subpages:
                developer:
                    title: 'translate:markitup_manual_developer'
                    icon: rex-icon fa-code
                    subPath: docs/howto_integration.md
                faq:
                    title: 'translate:markitup_manual_faq'
                    icon: rex-icon fa-exclamation-circle
                    subPath: docs/faq.md
        overview: 
            title: 'translate:description'
            itemClass: pull-right
            subPath: README.md
```

## Berechtigungen

Die bisherigen Berechtigungen `snippets` und `profiles` werden um zwei Handbuch-Berechtigungen ergänzt:

- `markitup[manual]`: die Handbuchseiten, die sich an Autoren / Redakteure (Texte erfassen)
- `markitup[developer]`: die Handbuchseiten, die sich an Entwickler richtet (Module)

