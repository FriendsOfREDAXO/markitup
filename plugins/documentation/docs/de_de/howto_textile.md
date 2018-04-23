# markitUp! - textile

Hier findest Du die snippets für die Auszeichnungssprache textile - jeweils mit Ausgabe (= Vorschau) und dem einzugebendem Code:

- [Überschriften](#ueberschriften)
- [Texte formatieren](#texte)
	- [Font-Formate](#formate)
	- [Ausrichten in der Zeile](#ausrichten)
	- [Hoch-/Tief-Stellen von Texten](#sub)
- [Listen](#listen)
	- [ungeordnete Listen](#ungeordnet)
	- [geordnete Listen](#geordnet)
- [Links](#links)
- [Anker](#anker)
	- [Anker definieren](#anker-definieren)
	- [Text auf Anker verlinken](#anker-link)
	- [Inhaltsverzeichnis erstellen](#inhalt)	
- [Zitieren](#zitieren) (in REX auch Hinweise)
- [Bilder](#images)
- [Tabellen](#tabellen)
- [Code](#code)

---

Quelle für diese Anleitung und viele weitere Tipps & Tricks:
[www.rexdev.de/cheatsheets/textile](http://rexdev.de/cheatsheets/textile.html)
[www.txstyle.org](http://txstyle.org)


<a name="ueberschriften"></a>
## Überschriften

| Ausgabe                  | Eingabe                    |
| ------------------------ | ----------------------     |
| Überschrift 1            | `h1. Überschrift`          |
| Überschrift 1 mit spezifischer Klasse    | `h1(class). Überschrift` |
| Überschrift 1 mit ID | `h1(#id). Überschrift` | 
| Überschrift 1 mit Klasse und ID | `h1(class#id). Überschrift` |
| Überschrift 2            | `h2. Überschrift`          |
| Überschrift 3            | `h3. Überschrift`          |
| Überschrift 1 zentrieren | `h1=. Überschrift`         |

> **Nach jeder Überschrift _muss_ eine Leerzeile eingefügt werden!**

> Anker-Links auf Überschriften setzen: siehe [Anker definieren](#anker)

&uarr; [zurück zur Übersicht](#top)


<a name="texte"></a>
## Texte 

<a name="formate"></a>
### Formatieren

| Ausgabe               | Eingabe                    |
| --------------------- | ----------------------     |
| Das ist Dein Text     | `Das ist Dein Text`        |
| Du kannst dem Text auch eine Klasse mitgeben. | `p(class). Dann steht der darauffolgende Text in einem p-Tag mit dieser Klasse.` |
| Dieser Text enthält ein **fettgedrucktes** Wort. | `Dieser Text enthält ein *fettgedrucktes* Wort.` |
| Warum nicht auch mal ein Wort _kursiv_ stellen? | `Warum nicht auch mal ein Wort _kursiv_ stellen?` | 
| Fettgedruckt und kursiv? Na klar, auch **_das geht natürlich_** mit textile. | `Fettgedruckt und kursiv? Na klar, auch *_das geht natürlich_* mit textile.` |
| Auch ein formatierter span lässt sich einfügen. | `Auch ein formatierter %(class)span% lässt sich einfügen.` |
| Pfiffig programmiert, ist REDAXO ~~nicht schwer~~ leicht für den Redakteur zu bedienen. | `Pfiffig programmiert, ist REDAXO -nicht schwer- leicht für den Redakteur zu bedienen.` |
| REDAXO ist ein CMS (Akronyme einfügen). | `REDAXO ist ein CMS(Content Management System) (Akronyme einfügen).` |
| <small>Bleibt noch, etwas klein zu setzen. Ideal z. B. für Quellen-Angaben oder Fußnoten.</small> | `<small>Bleibt noch, etwas klein zu setzen. Ideal z. B. für Quellen-Angaben oder Fußnoten.</small>` |

&uarr; [zurück zur Übersicht](#top)

<a name="ausrichten"></a>
### Texte ausrichten in der Zeile

| Ausgabe               | Eingabe                    |
| --------------------- | ----------------------     |
| Text linksbündig | `p<. Dein Text (default).` |
| Text rechtsbündig | `p>. Dein Text.` |
| Text zentriert | `p=. Dein Text.` |
| Text im justify | `p<>. Dein Text.` |
| Text um 1em einrücken | `p(. Dein Text.` |
| Text um 2em einrücken | `p((. Dein Text.` |
| Text um 3em einrücken | `p(((. Dein Text.` |
| Text ohne p-Tag | ` Für Text ohne p-Tag setzt Du an den Anfang der Zeile ein Leerzeichen.` |

&uarr; [zurück zur Übersicht](#top)

<a name="sub"></a>
### Texte hoch- und runterstellen

| Ausgabe               | Eingabe                    |
| --------------------- | ----------------------     |
| Text<sup>hochstellen</sup> geht auch einfach. | `Text ^hochstellen^ geht auch einfach.` (Achtung: vor dem hochgestellten Text muss ein Leerzeichen eingefügt werden) | 
| alternative Schreibweise<sup>1</sup> dafür | `alternative Schreibweise<sup>1</sup> dafür.` (Dann brauchst Du kein Leerzeichen dazwischen.) |
| weitere Schreibweise<sup>1</sup> mit [breitenlosem Leerzeichen](https://infothek.rotkel.de/tastaturkuerzel/schriftzeichen/leerzeichen.html) | `weitere Schreibweise&#65279;^1^ mit "breitenlosem Leerzeichen (Breitenloses Leerzeichen im Thesaurus via Rotkel)":https://infothek.rotkel.de/tastaturkuerzel/schriftzeichen/leerzeichen.html` |
| Den Text<sup>**_hochgestellt_**</sup> kannst Du formatieren. | `Den Text ^*_hochgestellt_*^ kannst Du formatieren.` | 
| Willst Du Zeichen tiefstellen, so geht das natürlich auch: H<sub>2</sub>O. | `Willst Du Zeichen tiefstellen, so geht das natürlich auch: H<sub>2</sub>O.` | 


&uarr; [zurück zur Übersicht](#top)


<a name="listen"></a>
## Listen

Beachte: Nach jeder Liste eine Leerzeile einfügen.

<a name="ungeordnet"></a>
### Ungeordnete Listen

#### Ausgabe

* Bavaria ipsum dolor sit amet Breihaus anbandeln.
	* Mim i sog ja nix, i red ja bloß Weiznglasl Freibia Deandlgwand etza. 
	* Woaß i griaß God beinand Brodzeid mogsd a Bussal aasgem Gschicht no Buam. 
* Sauba ma a, ma i a Maß und no a Maß Zwedschgndadschi. 
* I moan oiwei i sog ja nix, i red ja bloß i bin a woschechta Bayer, Habedehre.

#### Eingabe

	* Bavaria ipsum dolor sit amet Breihaus anbandeln.
	** Mim i sog ja nix, i red ja bloß Weiznglasl Freibia Deandlgwand etza. 
	** Woaß i griaß God beinand Brodzeid mogsd a Bussal aasgem Gschicht no Buam. 
	* Sauba ma a, ma i a Maß und no a Maß Zwedschgndadschi. 
	* I moan oiwei i sog ja nix, i red ja bloß i bin a woschechta Bayer, Habedehre.
	

Ebenso möglich ist ein bewusster Zeilenumbruch (Achtung: ohne Leerzeile), um beispielsweise bei längeren Aufzählungen die Lesbarkeit zu erhöhen. Alle Zeilen werden dann entsprechend automatisch eingerückt.

	* Bavaria ipsum dolor 
	(z. B. sit amet Breihaus anbandeln.)
	* Mim i sog ja nix, i red ja bloß 
	(z. B. Weiznglasl Freibia Deandlgwand etza.) 
	* Woaß i griaß God beinand Brodzeid 
	(z. B. mogsd a Bussal aasgem Gschicht no Buam.)
	* Sauba ma a, ma i a Maß und no a Maß Zwedschgndadschi 
	(z. B. I moan oiwei i sog ja nix, i red ja bloß i bin a woschechta Bayer, Habedehre.)


&uarr; [zurück zur Übersicht](#top)


<a name="geordnet"></a>
### Geordnete Listen

#### Ausgabe 

1. Bavaria ipsum dolor sit amet Breihaus anbandeln.
	1. Mim i sog ja nix, i red ja bloß Weiznglasl Freibia Deandlgwand etza. 
	1. Woaß i griaß God beinand Brodzeid mogsd a Bussal aasgem Gschicht no Buam. 
1. Sauba ma a, ma i a Maß und no a Maß Zwedschgndadschi. 
1. I moan oiwei i sog ja nix, i red ja bloß i bin a woschechta Bayer, Habedehre.

#### Eingabe

	# Bavaria ipsum dolor sit amet Breihaus anbandeln.
	## Mim i sog ja nix, i red ja bloß Weiznglasl Freibia Deandlgwand etza. 
	## Woaß i griaß God beinand Brodzeid mogsd a Bussal aasgem Gschicht no Buam. 
	# Sauba ma a, ma i a Maß und no a Maß Zwedschgndadschi. 
	# I moan oiwei i sog ja nix, i red ja bloß i bin a woschechta Bayer, Habedehre.

&uarr; [zurück zur Übersicht](#top)



<a name="links"></a>
## Links

| Ausgabe               | Eingabe                    |
| --------------------- | ----------------------     |
| [Text extern verlinkt](http://domain.tld) | `"Text extern verlinkt":http://domain.tld` |
| [Text intern verlinkt](#links) | `"Text intern verlinkt":redaxo://0815` (statt 0815 die ID des article) |
| [Text lokal verlinkt](/lokal) | `"Dein Text":/example` |
| Link mit title-tag einfügen | `"zu verlinkender Text (link title)":http://domain.tld` |
| Link eine class mitgeben | `"(classname) zu verlinkender text (title tooltip)":http://domain.tld` |
| Link auf einen Anker | `"zu verlinkender Text (title)":#anker` | 
| E-Mails verlinken (inklusive title) | `"(classname)link text(title tooltip)":mailto:someone@example.com?subject=Wie%20es%20Dir%20gef&#xE4;llt` |


&uarr; [zurück zur Übersicht](#top)


<a name="anker"></a>
## Anker


<a name="anker-definieren"></a>
### Anker definieren

= Zuordnen eines Namens und Links z. B. zur Überschrift
Achtung: das erste Zeichen **_muss_** ein Kleinbuchstabe sein. 
Keine Sonderzeichen oder Leerzeichen! 
Groß- und Kleinschreibung ist relevant.

#### Ausgabe

##### Überschrift

#### Eingabe 

	h1(#id). Überschrift

	Hier dann Dein Text. Da die Überschrift eine Leerzeile danach braucht.

	h1(class#id). Überschrift

	Hier dann Dein Text. Neben der ID vergibst Du so auch noch eine Klasse.


&uarr; [zurück zur Übersicht](#top)



<a name="anker-links"></a>
### Text auf Anker verlinken

| Ausgabe              | Eingabe                                 |
| -------------------- | ----------------------                  |
| Link auf einen Anker | `"zu verlinkender Text (title)":#anker` | 


&uarr; [zurück zur Übersicht](#top)



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

	* "Einleitung":#einleitung
	* "Hauptteil":#hauptteil
	** "Thema 1":#thema1
	** "Thema 2":#thema2
	** "Thema 3":#thema3
	* "Schluß":#schluss


&uarr; [zurück zur Übersicht](#top)



<a name="zitieren"></a>
## Zitieren

### Das Zitat ist 1 Absatz lang

#### Ausgabe

> **"Weit hinten, hinter den Wortbergen, fern der Länder Vokalien und Konsonantien leben die Blindtexte. Abgeschieden wohnen sie in Buchstabhausen an der Küste des Semantik, eines großen Sprachozeans. Ein kleines Bächlein namens Duden fließt durch ihren Ort und versorgt sie mit den nötigen Regelialien."**
> Blindtextgenerator Wortberge 
> <small>Quelle: [www.blindtextgenerator.de](http://www.blindtextgenerator.de)</small>


#### Eingabe

	*"Weit hinten, hinter den Wortbergen, fern der Länder Vokalien und Konsonantien leben die Blindtexte. Abgeschieden wohnen sie in Buchstabhausen an der Küste des Semantik, eines großen Sprachozeans. Ein kleines Bächlein namens Duden fließt durch ihren Ort und versorgt sie mit den nötigen Regelialien."*
	Blindtextgenerator Wortberge
	<small>Quelle: "www.blindtextgenerator.de":http://www.blindtextgenerator.de</small>


### Das Zitat umfasst mehrere Absätze

#### Ausgabe

Fließtext vor dem Zitat

> "Überall dieselbe alte Leier. Das Layout ist fertig, der Text lässt auf sich warten. Damit das Layout nun nicht nackt im Raume steht und sich klein und leer vorkommt, springe ich ein: der Blindtext. Genau zu diesem Zwecke erschaffen, immer im Schatten meines großen Bruders »Lorem Ipsum«, freue ich mich jedes Mal, wenn Sie ein paar Zeilen lesen. 
> Denn esse est percipi - Sein ist wahrgenommen werden. Und weil Sie nun schon die Güte haben, mich ein paar weitere Sätze lang zu begleiten, möchte ich diese Gelegenheit nutzen, Ihnen nicht nur als Lückenfüller zu dienen, sondern auf etwas hinzuweisen, das es ebenso verdient wahrgenommen zu werden: Webstandards nämlich. 
> Sehen Sie, Webstandards sind das Regelwerk, auf dem Webseiten aufbauen. So gibt es Regeln für HTML, CSS, JavaScript oder auch XML; Worte, die Sie vielleicht schon einmal von Ihrem Entwickler gehört haben. Diese Standards sorgen dafür, dass alle Beteiligten aus einer Webseite den größten Nutzen ziehen."
> <small>Quelle: [www.blindtextgenerator.de](http://www.blindtextgenerator.de)</small>

Fließtext nach dem Zitat


#### Eingabe

	Fließtext vor dem Zitat

	bq.. "Überall dieselbe alte Leier. Das Layout ist fertig, der Text lässt auf sich warten. Damit das Layout nun nicht nackt im Raume steht und sich klein und leer vorkommt, springe ich ein: der Blindtext. Genau zu diesem Zwecke erschaffen, immer im Schatten meines großen Bruders »Lorem Ipsum«, freue ich mich jedes Mal, wenn Sie ein paar Zeilen lesen. 

	Denn esse est percipi - Sein ist wahrgenommen werden. Und weil Sie nun schon die Güte haben, mich ein paar weitere Sätze lang zu begleiten, möchte ich diese Gelegenheit nutzen, Ihnen nicht nur als Lückenfüller zu dienen, sondern auf etwas hinzuweisen, das es ebenso verdient wahrgenommen zu werden: Webstandards nämlich. 

	Sehen Sie, Webstandards sind das Regelwerk, auf dem Webseiten aufbauen. So gibt es Regeln für HTML, CSS, JavaScript oder auch XML; Worte, die Sie vielleicht schon einmal von Ihrem Entwickler gehört haben. Diese Standards sorgen dafür, dass alle Beteiligten aus einer Webseite den größten Nutzen ziehen."
	
	<footer>Quelle: [www.blindtextgenerator.de](http://www.blindtextgenerator.de)</footer>

	p. Fließtext nach dem Zitat

Mit dem `p.` für den ersten Absatz nach dem Zitat signalisierst Du das Ende des `bq..`.


&uarr; [zurück zur Übersicht](#top)


<a name="images"></a>
## Bilder 


![FriendsOfREDAXO](assets/for.png)



| Ausgabe               | Eingabe                    |
| --------------------- | ----------------------     |
| Bild (einfach) | `!imageurl!` |
| Bild mit Bildbeschreibung (alt text) | `!imageurl(alt text)!` |
| Bild mit umschließendem Link | `!imageurl!:linkurl` |
| Bild mit umschließendem Link und Bildbeschreibung (alt text) | `!imageurl(alt text)!:linkurl` |


&uarr; [zurück zur Übersicht](#top)


<a name="tabellen"></a>
## Tabellen 

### Einfache Tabelle

	|=. Tabellen-Überschrift
	|^.
	|_. Spalte1                  |_. Spalte 2 |_. Spalte 3 |_. Spalte 4 |
	|-.
	| Text                       | Text       | Text       | Text       |
	| Text (nächste Spalte leer) |            | Text       | Text       |


### Sortierbare Tabelle

Insbesondere Auswahlhilfen, die sich sortieren lassen, sind für Nutzer von höherem Nutzen. 

Ein Weg (von vielen): Um eine Tabelle sortierbar zu machen, lade Dir via github das JavaScript [sorttable.js](https://github.com/stuartlangridge/sorttable) von [Stuart Langridge](https://kryogenix.org) herunter.

Dann kannst Du mit der class "sortable" jede Tabelle sortierbar gestalten:


	table(sortable).
	|^.
	|                        |_. Vorname  |_. Nachname |
	|-.
	| Chef vom Dienst        | Anton      | Zacharias  |
	| Ressort Wirtschaft     | Zora       | Rot        |
	| Ressort Wissenschaften | Roberta    | Humboldt   |
	| Ressort Mobilität      | Karl-Maria | Draisler   | 

Willst Du Dein eigenes Script schreiben, empfiehlt sich ein Anfang mit der Vorlage von w3schools für das [Sortieren von Tabellen](https://www.w3schools.com/howto/howto_js_sort_table.asp).


### Attribute für den Text in den einzelnen Tabellen-Zellen


| Ausgabe              | Eingabe          |
| -------------------- | ---------------- |
| links                | `|<. Dein Text`  |
| rechts               | `|>. Dein Text`  | 
| zentriert            | `|=. Dein Text`  | 
| justify              | `|<>. Dein Text` | 
| Zeilen-Anfang (top)  | `|^. Dein Text`  | 
| Zeilen-Ende (bottom) | `|~. Dein Text`  | 


&uarr; [zurück zur Übersicht](#top)



<a name="code"></a>
## Code 

Einzelne Code-Fragmente und Zeilen umschließt Du mit zwei Klammeraffen: `@code@`.

Für ganze Code-Blöcke verwende `bc..`. Beachte: Das Ende des Code-Blocks signalisierst Du textile mit einer Leerzeile sowie dem Öffnen eines neuen Tags (p / h2 / ...).

	Fließtext so wie Du es zur Erläuterung Deines Code-Snippets brauchst.
	
	bc.. <?php
	echo "<h1>Open Source Code rocks!</h1>";
	echo "Thank you for sharing your insights.";
	?>
	
	p. Hier geht es weiter im Text...


&uarr; [zurück zur Übersicht](#top)

