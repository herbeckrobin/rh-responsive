# RH Responsive

Schließt zwei WordPress-Responsive-Lücken. Teil der rh-blueprint Kollektion.

## Was es macht

- **Sichtbarkeit pro Block**: jeder gängige Block bekommt im Inspector ein Panel „Sichtbarkeit" und lässt sich auf Mobile, Tablet oder Desktop ausblenden. Die Breakpoints sind konfigurierbar, das CSS wird daraus erzeugt. Die Klassen wirken nur im Frontend, im Editor bleiben die Blöcke sichtbar.
- **Navigations-Breakpoint**: WordPress klappt das Navigations-Menü hart bei 600px auf Hamburger um, ohne Einstellung (Gutenberg-Issue #45040). Dazwischen bricht dichtere Navigation. Dieses Modul erzwingt den Hamburger bis zu einer frei wählbaren Breite, indem es den Core nur unterhalb des Wunsch-Breakpoints überschreibt (versionsrobust).
- **Mobil: Reihenfolge umkehren**: Gutenberg-Core kann Reihenfolge nicht per Breakpoint steuern (Gutenberg-Issue #55619). Zickzack-Layouts (Reihe 1 Bild-Text, Reihe 2 Text-Bild) stapeln auf Mobile in DOM-Reihenfolge, dadurch treffen zwei Bilder bzw. zwei Texte aufeinander. Spalten-, Gruppen- (Reihe/Stapel), Medien-Text- und Button-Blöcke bekommen einen Schalter „Mobil: Reihenfolge umkehren". Unterhalb des Umkehr-Breakpoints erzwingt das Modul das Stapeln und kehrt die Reihenfolge in derselben Media-Query um, damit es keinen Zwischenbereich gibt (umgekehrt, aber noch nicht gestapelt). Standard-Breakpoint 781, das ist der Punkt, an dem WordPress Spalten von selbst stapelt.

## Einstellungen

Im Backend unter **RH Blueprint → Responsive**:

- Sichtbarkeit pro Block an/aus, Breakpoint Tablet (Standard 600) und Desktop (Standard 1024).
- Navigations-Breakpoint überschreiben an/aus, Breakpoint in px (z.B. 782 oder 1024, muss größer als 600 sein).
- Mobil: Reihenfolge umkehren an/aus, Umkehr-Breakpoint in px (Standard 781).

## Für Entwickler

Filter `rh-blueprint/responsive/blocks` (array) erweitert die Blöcke, die die Sichtbarkeits-Auswahl bekommen. Die Klassen `rh-hide-mobile`, `rh-hide-tablet`, `rh-hide-desktop` werden serverseitig ans Block-Markup gesetzt und per generiertem Media-Query-CSS aktiv.

Filter `rh-blueprint/responsive/reverse-blocks` (array) erweitert die Blöcke, die den Umkehr-Schalter bekommen (Standard: Spalten, Gruppe, Medien-Text, Buttons). Die Klasse `is-rh-reverse-mobile` wird serverseitig gesetzt; das CSS erzwingt unterhalb des Umkehr-Breakpoints `flex-direction: column-reverse` (plus Stapel-Zwang) bzw. die getauschte Grid-Reihenfolge bei Medien-Text.

## Installation

ZIP hochladen und aktivieren. Der geteilte Core ist gebündelt.

## Voraussetzungen

WordPress 6.5+, PHP 8.1+.
