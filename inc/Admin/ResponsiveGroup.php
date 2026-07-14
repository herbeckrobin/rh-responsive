<?php

declare(strict_types=1);

namespace RhResponsive\Admin;

use RhBlueprint\Core\Settings\GroupInterface;
use RhBlueprint\Core\Settings\SettingField;

/**
 * Settings-Gruppe für Responsive-Eingriffe.
 *
 * Zwei Features: per-Block-Sichtbarkeit (pro Block im Editor wählbar) und ein
 * konfigurierbarer Navigations-Breakpoint (WP klappt das Menü hart bei 600px auf
 * Hamburger um, dazwischen bricht dichte Navigation, Gutenberg-Issue #45040).
 */
final class ResponsiveGroup implements GroupInterface
{
    public const GROUP_ID = 'responsive';

    public const FIELD_VISIBILITY_ENABLED = 'visibility_enabled';
    public const FIELD_ALL_BLOCKS = 'all_blocks';
    public const FIELD_BP_TABLET = 'bp_tablet';
    public const FIELD_BP_DESKTOP = 'bp_desktop';
    public const FIELD_NAV_ENABLED = 'nav_enabled';
    public const FIELD_NAV_BREAKPOINT = 'nav_breakpoint';
    public const FIELD_REVERSE_ENABLED = 'reverse_enabled';
    public const FIELD_REVERSE_BREAKPOINT = 'reverse_breakpoint';

    public function id(): string
    {
        return self::GROUP_ID;
    }

    public function tab(): string
    {
        return 'responsive';
    }

    public function title(): string
    {
        return __('Responsive', 'rh-responsive');
    }

    public function description(): string
    {
        return __('Sichtbarkeit pro Block je Geräteklasse und ein frei wählbarer Umschaltpunkt fürs Navigationsmenü.', 'rh-responsive');
    }

    public function fields(): array
    {
        return [
            new SettingField(
                id: self::FIELD_VISIBILITY_ENABLED,
                type: SettingField::TYPE_BOOLEAN,
                label: __('Sichtbarkeit pro Block', 'rh-responsive'),
                description: __('Fügt jedem Block eine Auswahl hinzu, ihn auf Mobile, Tablet oder Desktop auszublenden.', 'rh-responsive'),
                default: true,
                keywords: ['sichtbarkeit', 'visibility', 'mobile', 'tablet', 'desktop', 'ausblenden'],
            ),
            new SettingField(
                id: self::FIELD_ALL_BLOCKS,
                type: SettingField::TYPE_BOOLEAN,
                label: __('Auf allen Blöcken', 'rh-responsive'),
                description: __('An (Standard) = die Sichtbarkeits-Auswahl erscheint an jedem Block, auch Theme- und Custom-Blöcken. Aus = nur ein kuratierter Satz gängiger Core-Blöcke, falls du die Auswahl bewusst eingrenzen willst.', 'rh-responsive'),
                default: true,
                keywords: ['custom', 'theme', 'bloecke', 'alle', 'whitelist'],
            ),
            new SettingField(
                id: self::FIELD_BP_TABLET,
                type: SettingField::TYPE_TEXT,
                label: __('Breakpoint Tablet (px)', 'rh-responsive'),
                description: __('Ab dieser Breite gilt Tablet. Darunter ist Mobile. Standard 600.', 'rh-responsive'),
                default: '600',
                keywords: ['breakpoint', 'tablet', 'mobile'],
            ),
            new SettingField(
                id: self::FIELD_BP_DESKTOP,
                type: SettingField::TYPE_TEXT,
                label: __('Breakpoint Desktop (px)', 'rh-responsive'),
                description: __('Ab dieser Breite gilt Desktop. Dazwischen ist Tablet. Standard 1024.', 'rh-responsive'),
                default: '1024',
                keywords: ['breakpoint', 'desktop'],
            ),
            new SettingField(
                id: self::FIELD_NAV_ENABLED,
                type: SettingField::TYPE_BOOLEAN,
                label: __('Navigations-Breakpoint überschreiben', 'rh-responsive'),
                description: __('Erzwingt das Hamburger-Menü bis zur eingestellten Breite, statt WordPress\' fixem Umschaltpunkt (600px).', 'rh-responsive'),
                default: false,
                keywords: ['navigation', 'menu', 'hamburger', 'breakpoint'],
            ),
            new SettingField(
                id: self::FIELD_NAV_BREAKPOINT,
                type: SettingField::TYPE_TEXT,
                label: __('Navigations-Breakpoint (px)', 'rh-responsive'),
                description: __('Bis zu dieser Breite bleibt das Menü als Hamburger. Sinnvoll z.B. 782 oder 1024. Muss größer als 600 sein.', 'rh-responsive'),
                default: '782',
                keywords: ['navigation', 'breakpoint', 'hamburger'],
            ),
            new SettingField(
                id: self::FIELD_REVERSE_ENABLED,
                type: SettingField::TYPE_BOOLEAN,
                label: __('Mobil: Reihenfolge umkehren', 'rh-responsive'),
                description: __('Fügt Spalten-, Gruppen-, Medien-Text- und Button-Blöcken eine Auswahl hinzu, die Reihenfolge der Kinder auf Mobile umzukehren. Nützlich für Zickzack-Layouts (Bild-Text, Text-Bild), die sonst gestapelt zwei Bilder untereinander zeigen.', 'rh-responsive'),
                default: true,
                keywords: ['reihenfolge', 'umkehren', 'reverse', 'mobile', 'zickzack', 'spalten', 'columns'],
            ),
            new SettingField(
                id: self::FIELD_REVERSE_BREAKPOINT,
                type: SettingField::TYPE_TEXT,
                label: __('Umkehr-Breakpoint (px)', 'rh-responsive'),
                description: __('Bis zu dieser Breite gilt die Umkehrung. Darunter werden die Kinder gestapelt und umgekehrt. Standard 781 (entspricht dem Punkt, an dem WordPress Spalten von selbst stapelt). Bei anderen Werten wird das Stapeln mit übernommen, damit es keinen Zwischenbereich gibt.', 'rh-responsive'),
                default: '781',
                keywords: ['breakpoint', 'reverse', 'umkehren', 'mobile', 'spalten'],
            ),
        ];
    }
}
