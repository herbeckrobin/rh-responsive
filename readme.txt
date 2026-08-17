=== RH Responsive ===
Contributors: robinherbeck
Tags: responsive, visibility, navigation, breakpoint, mobile
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 8.1
Stable tag: 0.3.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Closes WordPress responsive gaps: a configurable navigation breakpoint, per-block visibility (hide on mobile, tablet or desktop), and a per-block toggle to reverse child order on mobile.

== Description ==

= Per-block visibility =
Every common block gets a "Visibility" panel in the editor to hide it on mobile, tablet or desktop. The breakpoints (tablet and desktop) are configurable; the CSS is generated from them. The classes only apply on the frontend, so blocks stay visible while editing.

= Navigation breakpoint =
WordPress collapses the navigation block into the hamburger overlay at a fixed 600px and offers no setting for it (Gutenberg issue #45040). Between 600px and roughly 1100px a denser menu breaks. This forces the hamburger up to a breakpoint you choose, by overriding core only below that width (version-robust).

= Reverse order on mobile =
Gutenberg core has no per-breakpoint ordering (Gutenberg issue #55619). Zigzag layouts (row 1 image|text, row 2 text|image) stack in DOM order on mobile, so two images or two texts end up next to each other. Columns, Group (row/stack), Media & Text and Buttons get a "Reverse order on mobile" toggle. Below the reverse breakpoint the module forces stacking and reverses the children in the same media query, so there is no in-between range where a block is reversed but not yet stacked. The default breakpoint is 781px, matching the point at which WordPress stacks columns on its own; at that value it is a no-op for columns and only reverses.

The block list for the visibility control is extensible via the rh-blueprint/responsive/blocks filter.

Part of the rh-blueprint collection. Settings live under RH Blueprint > Responsive.

== Changelog ==

= 0.3.1 =
* Internal: shared building blocks from core 2.6.0. The update check no longer loads on regular front-end requests.

= 0.3.0 =
* New: per-block "Reverse order on mobile" toggle for Columns, Group (row/stack), Media & Text and Buttons. Fixes zigzag layouts that collapse into two adjacent images/texts on mobile. Configurable reverse breakpoint (default 781); stacking is taken over in the same media query to avoid an in-between range.

= 0.2.1 =
* Bundle core 2.4.1 (loader fix for mixed bundled versions).

= 0.2.0 =
* Per-block visibility now available on all blocks by default (not just the curated whitelist).

= 0.1.0 =
* Initial release: per-block visibility with configurable breakpoints, configurable navigation breakpoint.
