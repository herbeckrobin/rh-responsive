<?php

declare(strict_types=1);

namespace RhResponsive;

use RhResponsive\Admin\ResponsiveGroup;
use WP_HTML_Tag_Processor;

/**
 * Responsive-Eingriffe: per-Block-Sichtbarkeit und Navigations-Breakpoint.
 *
 * Sichtbarkeit: ein per-Block-Attribut (im Editor wählbar) setzt Klassen
 * rh-hide-mobile/-tablet/-desktop aufs Block-Markup; das passende CSS wird aus den
 * konfigurierten Breakpoints serverseitig erzeugt (Media-Queries können kein var()).
 *
 * Navigation: WP klappt das Overlay-Menü hart bei 600px um. Wir erzwingen den
 * Hamburger bis zum konfigurierten Breakpoint, indem wir unterhalb davon den
 * Open-Button zeigen und den Inline-Container verstecken (versionsrobust, weil wir
 * core nur im Bereich UNTER dem Wunsch-Breakpoint überschreiben).
 *
 * CSS + Klassen nur im Frontend, nicht im Editor (sonst verschwinden Blöcke beim Pflegen).
 */
final class Responsive
{
    public const ATTR = 'rhResponsiveHide';

    /** @var array<string, string> */
    private const CLASS_MAP = [
        'mobile' => 'rh-hide-mobile',
        'tablet' => 'rh-hide-tablet',
        'desktop' => 'rh-hide-desktop',
    ];

    public function boot(): void
    {
        if ($this->visibilityEnabled()) {
            add_filter('render_block', [$this, 'addVisibilityClasses'], 10, 2);
            add_action('enqueue_block_editor_assets', [$this, 'enqueueEditor']);
        }

        add_action('wp_head', [$this, 'renderCss'], 20);
    }

    private function visibilityEnabled(): bool
    {
        return (bool) rhbp_setting(ResponsiveGroup::GROUP_ID, ResponsiveGroup::FIELD_VISIBILITY_ENABLED, true);
    }

    private function allBlocksAllowed(): bool
    {
        return (bool) rhbp_setting(ResponsiveGroup::GROUP_ID, ResponsiveGroup::FIELD_ALL_BLOCKS, true);
    }

    /**
     * Greift die Sichtbarkeits-Auswahl für diesen Block? Mit dem All-Blocks-
     * Schalter jeder Block, sonst nur die kuratierte Core-Whitelist.
     */
    private function matchesBlock(string $blockName): bool
    {
        if ($blockName === '') {
            return false;
        }

        return $this->allBlocksAllowed() || in_array($blockName, $this->blocks(), true);
    }

    private function bp(string $field, int $default): int
    {
        $value = (int) rhbp_setting(ResponsiveGroup::GROUP_ID, $field, (string) $default);

        return $value > 0 ? $value : $default;
    }

    /**
     * Blöcke, die die Sichtbarkeits-Auswahl bekommen. Pro Theme erweiterbar.
     *
     * @return array<int, string>
     */
    public function blocks(): array
    {
        $blocks = [
            'core/group',
            'core/columns',
            'core/column',
            'core/image',
            'core/cover',
            'core/heading',
            'core/paragraph',
            'core/buttons',
            'core/list',
            'core/spacer',
            'core/separator',
            'core/gallery',
            'core/media-text',
            'core/navigation',
            'core/site-logo',
        ];

        /** @var array<int, string> $filtered */
        $filtered = (array) apply_filters('rh-blueprint/responsive/blocks', $blocks);

        return $filtered;
    }

    /**
     * Sichtbarkeits-Klassen aufs erste Tag setzen.
     */
    public function addVisibilityClasses(string $blockContent, array $block): string
    {
        if (trim($blockContent) === '') {
            return $blockContent;
        }
        $blockName = $block['blockName'] ?? '';
        if (! is_string($blockName) || ! $this->matchesBlock($blockName)) {
            return $blockContent;
        }

        $hide = $block['attrs'][self::ATTR] ?? [];
        if (! is_array($hide) || $hide === []) {
            return $blockContent;
        }

        $classes = [];
        foreach ($hide as $key) {
            if (is_string($key) && isset(self::CLASS_MAP[$key])) {
                $classes[] = self::CLASS_MAP[$key];
            }
        }
        if ($classes === []) {
            return $blockContent;
        }

        $processor = new WP_HTML_Tag_Processor($blockContent);
        if (! $processor->next_tag()) {
            return $blockContent;
        }
        foreach ($classes as $class) {
            $processor->add_class($class);
        }

        return $processor->get_updated_html();
    }

    /**
     * Berechnetes Frontend-CSS (Sichtbarkeit + Nav-Override) inline ausgeben.
     */
    public function renderCss(): void
    {
        if (is_admin()) {
            return;
        }

        $css = '';

        if ($this->visibilityEnabled()) {
            $tablet = $this->bp(ResponsiveGroup::FIELD_BP_TABLET, 600);
            $desktop = $this->bp(ResponsiveGroup::FIELD_BP_DESKTOP, 1024);
            $mobileMax = $this->edge($tablet);
            $tabletMax = $this->edge($desktop);

            $css .= '@media (max-width:' . $mobileMax . '){.rh-hide-mobile{display:none !important}}';
            $css .= '@media (min-width:' . $tablet . 'px) and (max-width:' . $tabletMax . '){.rh-hide-tablet{display:none !important}}';
            $css .= '@media (min-width:' . $desktop . 'px){.rh-hide-desktop{display:none !important}}';
        }

        if ((bool) rhbp_setting(ResponsiveGroup::GROUP_ID, ResponsiveGroup::FIELD_NAV_ENABLED, false)) {
            $navBp = $this->bp(ResponsiveGroup::FIELD_NAV_BREAKPOINT, 782);
            // Unterhalb des Wunsch-Breakpoints den Hamburger erzwingen und den
            // Inline-Container verstecken, überschreibt core's 600px-Umschaltung.
            $css .= '@media (max-width:' . $this->edge($navBp) . '){';
            $css .= '.wp-block-navigation__responsive-container-open:not(.always-shown){display:flex !important}';
            $css .= '.wp-block-navigation__responsive-container:not(.hidden-by-default):not(.is-menu-open){display:none !important}';
            $css .= '}';
        }

        if ($css === '') {
            return;
        }

        echo "\n" . '<style id="rh-responsive">' . $css . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- nur generierte numerische Breakpoints + feste Selektoren
    }

    /**
     * Editor-JS für die Sichtbarkeits-Auswahl, Block-Whitelist gespiegelt.
     */
    public function enqueueEditor(): void
    {
        $jsRel = 'assets/js/visibility-editor.js';
        $jsAbs = RHRESP_PLUGIN_DIR . $jsRel;
        if (! file_exists($jsAbs)) {
            return;
        }

        wp_enqueue_script(
            'rh-responsive-editor',
            RHRESP_PLUGIN_URL . $jsRel,
            ['wp-hooks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-compose', 'wp-i18n'],
            (string) filemtime($jsAbs),
            true
        );

        wp_localize_script('rh-responsive-editor', 'rhResponsiveConfig', [
            'attr' => self::ATTR,
            'blocks' => array_values($this->blocks()),
            'allBlocks' => $this->allBlocksAllowed(),
        ]);
    }

    /**
     * Obere Kante einer max-width-Query (1px-Überlappung vermeiden).
     */
    private function edge(int $px): string
    {
        return ($px - 1) . '.98px';
    }
}
