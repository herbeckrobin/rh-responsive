# RH Responsive

Schließt zwei WordPress-Responsive-Lücken. Teil der rh-blueprint Kollektion.

## Was es macht

- **Sichtbarkeit pro Block**: jeder gängige Block bekommt im Inspector ein Panel „Sichtbarkeit" und lässt sich auf Mobile, Tablet oder Desktop ausblenden. Die Breakpoints sind konfigurierbar, das CSS wird daraus erzeugt. Die Klassen wirken nur im Frontend, im Editor bleiben die Blöcke sichtbar.
- **Navigations-Breakpoint**: WordPress klappt das Navigations-Menü hart bei 600px auf Hamburger um, ohne Einstellung (Gutenberg-Issue #45040). Dazwischen bricht dichtere Navigation. Dieses Modul erzwingt den Hamburger bis zu einer frei wählbaren Breite, indem es den Core nur unterhalb des Wunsch-Breakpoints überschreibt (versionsrobust).

## Einstellungen

Im Backend unter **RH Blueprint → Responsive**:

- Sichtbarkeit pro Block an/aus, Breakpoint Tablet (Standard 600) und Desktop (Standard 1024).
- Navigations-Breakpoint überschreiben an/aus, Breakpoint in px (z.B. 782 oder 1024, muss größer als 600 sein).

## Für Entwickler

Filter `rh-blueprint/responsive/blocks` (array) erweitert die Blöcke, die die Sichtbarkeits-Auswahl bekommen. Die Klassen `rh-hide-mobile`, `rh-hide-tablet`, `rh-hide-desktop` werden serverseitig ans Block-Markup gesetzt und per generiertem Media-Query-CSS aktiv.

## Installation

ZIP hochladen und aktivieren. Der geteilte Core ist gebündelt.

## Voraussetzungen

WordPress 6.5+, PHP 8.1+.
