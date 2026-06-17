=== RH Responsive ===
Contributors: robinherbeck
Tags: responsive, visibility, navigation, breakpoint, mobile
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 8.1
Stable tag: 0.1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Closes two WordPress responsive gaps: a configurable navigation breakpoint and per-block visibility (hide on mobile, tablet or desktop).

== Description ==

= Per-block visibility =
Every common block gets a "Visibility" panel in the editor to hide it on mobile, tablet or desktop. The breakpoints (tablet and desktop) are configurable; the CSS is generated from them. The classes only apply on the frontend, so blocks stay visible while editing.

= Navigation breakpoint =
WordPress collapses the navigation block into the hamburger overlay at a fixed 600px and offers no setting for it (Gutenberg issue #45040). Between 600px and roughly 1100px a denser menu breaks. This forces the hamburger up to a breakpoint you choose, by overriding core only below that width (version-robust).

The block list for the visibility control is extensible via the rh-blueprint/responsive/blocks filter.

Part of the rh-blueprint collection. Settings live under RH Blueprint > Responsive.

== Changelog ==

= 0.1.0 =
* Initial release: per-block visibility with configurable breakpoints, configurable navigation breakpoint.
