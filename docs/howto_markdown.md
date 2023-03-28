# markitUp! - Markdown

Hier findest Du die snippets für die Auszeichnungssprache markdown - jeweils mit Ausgabe (= Vorschau) und dem einzugebendem Code:

- [Überschriften](#ueberschriften)
- [Texte formatieren](#texte)
	- [Font-Formate](#formate)
	- [Hoch-/Tief-Stellen von Texten](#sub)
- [Links](#links)
- [Anker](#anker)
	- [Anker definieren](#anker-definieren)
	- [Text auf Anker verlinken](#anker-link)
	- [Inhaltsverzeichnis erstellen](#inhalt)
- [Listen](#listen)
- [Tabellen](#tabellen)
- [Code](#code)
- [Zitieren](#zitieren) (in REX auch Hinweise)
- [Bilder](#images)

---

Quellen für diese Anleitung:

[Daring Fireball: Markdown Syntax Documentation](https://daringfireball.net/projects/markdown/syntax)
[markdown.de · Markdown Syntax-Dokumentation](http://markdown.de/)
[Mastering Markdown · GitHub Guides](https://guides.github.com/features/mastering-markdown/)



<a name="ueberschriften"></a>
## Überschriften

| Ausgabe       | Eingabe          |
| --------------| -----------------|
| Überschrift 1 | `# Überschrift`  |
| Überschrift 2 | `## Überschrift` |
| Überschrift 3 | `### Überschrift`|

> Anker für die Überschriften definieren: siehe [Anker definieren](#anker)

&uarr; [zurück zur Übersicht](#top)



<a name="texte"></a>
## Texte 

<a name="formate"></a>
### Formatieren

| Ausgabe       | Eingabe          |
| --------------| -----------------|
| Das ist ein Standard-Text und soll als Referenz dienen. | `Das ist ein Standard-Text und soll als Referenz dienen.`|
| Dieser Text enthält ein **fettgedrucktes** Wort. | `Dieser Text enthält ein **fettgedrucktes** Wort.`|
| Warum nicht auch mal ein Wort _kursiv_ stellen? | `Warum nicht auch mal ein Wort _kursiv_ stellen?` |
| Fettgedruckt und kursiv? Na klar, auch **_das geht natürlich_** mit markdown. | `Fettgedruckt und kursiv? Na klar, auch **_das geht natürlich_** mit markdown.` | 
| <small>Bleibt noch, etwas klein zu setzen. Ideal z. B. für Quellen-Angaben oder Fußnoten.</small> | `<small>Bleibt noch, etwas klein zu setzen. Ideal z. B. für Quellen-Angaben oder Fußnoten.</small>` |


<a name="sup"></a>
### Hochstellen

Anwendung zum Beispiel zum Kennzeichnen von offenen Punkten bzw. in Arbeit

| Ausgabe       | Eingabe          |
| --------------| -----------------|
| Das ist ein ganz normaler Text mit einer hochgestellten Anmerkung<sup>1</sup>. | `Das ist ein ganz normaler Text mit einer hochgestellten Anmerkung<sup>1</sup>.` |
| [Wo finde ich Hilfe?](help_where.md) <sup><b>(in Arbeit)</b></sup> | `[Wo finde ich Hilfe?](help_where.md) <sup><b>(in Arbeit)</b></sup>` |

> Das funktioniert auch mit Aufzählungen im Fließtext (nur nicht innerhalb von Tabellen).

<a name="sub"></a>
### Tiefstellen

| Ausgabe       | Eingabe          |
| --------------| -----------------|
| Willst Du Zeichen tiefstellen, so geht das natürlich auch: H<sub>2</sub>O. | `Willst Du Zeichen tiefstellen, so geht das natürlich auch: H<sub>2</sub>O.`|


&uarr; [zurück zur Übersicht](#top)



<a name="links"></a>
## Links

Der verlinkte Text wird in eckige Klammern gesetzt, der Link dahinter in runden Klammern:

| Ausgabe       | Eingabe          |
| --------------| -----------------|
| [Linktext](markdown-datei.md) | `[Linktext](markdown-datei.md)`|


&uarr; [zurück zur Übersicht](#top)



<a name="anker"></a>
## Anker-Links

<a name="anker-definieren"></a>
### Definieren des Ankers 

= Zuordnen eines Namens und Links z. B. zur Überschrift
Achtung: das erste Zeichen _muss_ ein Kleinbuchstabe sein. Keine Sonderzeichen oder Leerzeichen! Groß- und Kleinschreibung ist relevant.


#### Ausgabe: Sprunganker (Zielpunkt für den Link)

<a name="anker-zur-ueberschrift"></a>
##### Überschrift


#### Eingabe: Sprunganker definieren

    <a name="anker-zur-ueberschrift"></a>
    ##### Überschrift



<a name="anker-link"></a>
### Sprunganker als Link setzen.

| Ausgabe       | Eingabe          |
| --------------| -----------------|
| [Text von dem aus zum Anker gelinkt wird](#anker) | `[Text von dem aus zum Anker gelinkt wird](#anker)` |




<a name="inhalt"></a>
### Inhaltsverzeichnis erstellen

> Definieren, wohin der Link springen soll: siehe [Anker definieren](#anker)

#### Ausgabe

- [Einleitung](#einleitung)
- [Hauptteil](#hauptteil)
	- [Thema 1](#thema1)
	- [Thema 2](#thema2)
	- [Thema 3](#thema3)	
- [Schluß](#schluss)


#### Eingabe

	- [Einleitung](#einleitung)
	- [Hauptteil](#hauptteil)
		- [Thema 1](#thema1)
		- [Thema 2](#thema2)
		- [Thema 3](#thema3)	
	- [Schluß](#schluss)

&uarr; [zurück zur Übersicht](#top)


<a name="listen"></a>
## Listen

### Unnumerierte Listen

#### Ausgabe 

- Listenpunkt 1
- Listenpunkt 2
- Listenpunkt 3
- Listenpunkt 4

#### Eingabe

    - Listenpunkt 1
    - Listenpunkt 2
    - Listenpunkt 3
    - Listenpunkt 4


### Numerierte Listen 

> Wichtig ist die Zahl mit Punkt, es kann aber auch ordentlich durchnumeriert werden.

#### Ausgabe 

1. Listenpunkt 1
2. Listenpunkt 2
3. Listenpunkt 3
4. Listenpunkt 4


#### Eingabe

    1. Listenpunkt 1
    1. Listenpunkt 2
    1. Listenpunkt 3
    1. Listenpunkt 4

    oder

    1. Listenpunkt 1
    2. Listenpunkt 2
    3. Listenpunkt 3
    4. Listenpunkt 4


&uarr; [zurück zur Übersicht](#top)



<a name="tabellen"></a>
## Tabellen

 #### Ausgabe 


| Alt                  | Neu                    |
| -------------------- | ---------------------- |
| `$REX['SERVERNAME']` | `rex::getServername()` |


 #### Eingabe

```
| Alt                  | Neu                    |
| -------------------- | ---------------------- |
| `$REX['SERVERNAME']` | `rex::getServername()` |
```

&uarr; [zurück zur Übersicht](#top)


<a name="code"></a>
## Code

### Code Inline

#### Ausgabe 

Code innerhalb eines Text wird `ganz normal` mit Backticks ausgezeichnet.


#### Eingabe

	Code innerhalb eines Text wird `ganz normal` mit Backticks ausgezeichnet.
	

### Code Block


#### Ausgabe = Eingabe

    <?php
		// Code wird einfach nur mit Tabs eingerückt.
		$article = rex_article::get();
	?>
	



 

&uarr; [zurück zur Übersicht](#top)



<a name="zitieren"></a>
## Zitieren

### Zitat
 
#### Ausgabe

> **"Weit hinten, hinter den Wortbergen, fern der Länder Vokalien und Konsonantien leben die Blindtexte. Abgeschieden wohnen sie in Buchstabhausen an der Küste des Semantik, eines großen Sprachozeans. Ein kleines Bächlein namens Duden fließt durch ihren Ort und versorgt sie mit den nötigen Regelialien."**
> Blindtextgenerator Wortberge 
> <small>Quelle: [www.blindtextgenerator.de](http://www.blindtextgenerator.de)</small>


#### Eingabe

	> **"Weit hinten, hinter den Wortbergen, fern der Länder Vokalien und Konsonantien leben die Blindtexte. Abgeschieden wohnen sie in Buchstabhausen an der Küste des Semantik, eines großen Sprachozeans. Ein kleines Bächlein namens Duden fließt durch ihren Ort und versorgt sie mit den nötigen Regelialien."**
	> Blindtextgenerator Wortberge 
	> <small>Quelle: [www.blindtextgenerator.de](http://www.blindtextgenerator.de)</small>



### Hinweis

Im Rahmen der REDAXO-Doku wird das markdown für Zitate auch für Hinweise empfohlen.

#### Ausgabe 

> **Hinweis:** Aliquam arcu lectus, imperdiet sollicitudin vehicula ultricies, pellentesque at nunc. Pellentesque ut consectetur nisl. In finibus efficitur turpis, posuere facilisis dui tristique ac.


#### Eingabe

    > **Hinweis:** Aliquam arcu lectus, imperdiet sollicitudin vehicula ultricies, pellentesque at nunc. Pellentesque ut consectetur nisl. In finibus efficitur turpis, posuere facilisis dui tristique ac.



&uarr; [zurück zur Übersicht](#top)



<a name="images"></a>
## Bilder

 #### Ausgabe

![FriendsOfREDAXO](assets/for.png)


 #### Eingabe

	&#33;&#91;FriendsOfREDAXO&#93;&#40;assets/for.png&#41;
	
	![FriendsOfREDAXO](assets/for.png)



&uarr; [zurück zur Übersicht](#top)
