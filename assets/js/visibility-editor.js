/**
 * RH Responsive, Editor-Script (buildless).
 *
 * Hängt ein Array-Attribut an die erlaubten Blöcke und zeigt im Inspector drei
 * Schalter (Mobile/Tablet/Desktop ausblenden). Block-Whitelist + Attribut-Name
 * kommen aus PHP (window.rhResponsiveConfig). Frontend macht das CSS (Klassen
 * rh-hide-*), hier wird nur die Auswahl gepflegt.
 *
 * Nutzt window.wp.* UMD-Globals.
 */
(function (wp, config) {
	'use strict';

	if (!wp || !wp.hooks || !wp.element || !wp.blockEditor || !wp.components || !wp.compose) {
		return;
	}
	if (!config || !Array.isArray(config.blocks)) {
		return;
	}

	var addFilter = wp.hooks.addFilter;
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var ToggleControl = wp.components.ToggleControl;
	var createHigherOrderComponent = wp.compose.createHigherOrderComponent;
	var __ = (wp.i18n && wp.i18n.__) ? wp.i18n.__ : function (s) { return s; };

	var BLOCKS = config.blocks;
	var ALL_BLOCKS = !!config.allBlocks;
	var ATTR = config.attr;
	var DEVICES = [
		{ key: 'mobile', label: __('Auf Mobile ausblenden', 'rh-responsive') },
		{ key: 'tablet', label: __('Auf Tablet ausblenden', 'rh-responsive') },
		{ key: 'desktop', label: __('Auf Desktop ausblenden', 'rh-responsive') }
	];

	// Greift die Sichtbarkeits-Auswahl für diesen Block? Mit allBlocks jeder Block, sonst die Whitelist.
	function isAllowed(name) {
		return !!name && (ALL_BLOCKS || BLOCKS.indexOf(name) !== -1);
	}

	addFilter(
		'blocks.registerBlockType',
		'rh-responsive/add-attribute',
		function (settings, name) {
			if (!isAllowed(name)) {
				return settings;
			}
			var added = {};
			added[ATTR] = { type: 'array', default: [] };
			settings.attributes = Object.assign({}, settings.attributes, added);
			return settings;
		}
	);

	var withVisibilityPanel = createHigherOrderComponent(
		function (BlockEdit) {
			return function (props) {
				if (!isAllowed(props.name)) {
					return el(BlockEdit, props);
				}
				var current = (props.attributes && props.attributes[ATTR]) || [];

				var toggles = DEVICES.map(function (d) {
					return el(ToggleControl, {
						key: d.key,
						label: d.label,
						checked: current.indexOf(d.key) !== -1,
						onChange: function () {
							var next = current.slice();
							var i = next.indexOf(d.key);
							if (i === -1) { next.push(d.key); } else { next.splice(i, 1); }
							var update = {};
							update[ATTR] = next;
							props.setAttributes(update);
						}
					});
				});

				return el(
					Fragment,
					null,
					el(BlockEdit, props),
					el(
						InspectorControls,
						null,
						el(
							PanelBody,
							{ title: __('Sichtbarkeit', 'rh-responsive'), initialOpen: false },
							toggles
						)
					)
				);
			};
		},
		'withRhResponsivePanel'
	);

	addFilter('editor.BlockEdit', 'rh-responsive/inspector', withVisibilityPanel);
})(window.wp, window.rhResponsiveConfig);
