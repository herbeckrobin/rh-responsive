/**
 * RH Responsive, Editor-Script (buildless).
 *
 * Ein Inspector-Panel "Responsive" mit zwei Features, jeweils per PHP schaltbar:
 *  - Sichtbarkeit: Array-Attribut (Mobile/Tablet/Desktop ausblenden) an der Block-Whitelist.
 *  - Mobil umkehren: Boolean-Attribut an Flex-/Grid-Blöcken (Spalten/Gruppe/Medien-Text/Buttons).
 * Attribut-Namen, Block-Listen und Enable-Flags kommen aus PHP (window.rhResponsiveConfig).
 *
 * Canvas-Ansicht: Im Frontend setzt PHP die Klassen rh-hide-* und is-rh-reverse-mobile
 * per render_block. Der Editor rendert nicht über PHP, deshalb setzt dieses Script
 * dieselben Klassen an die Block-Wrapper im Canvas und spielt dasselbe CSS ein
 * (config.canvasCss, aus Responsive::css(true)). Die Geräte-Vorschau zeigt damit, was
 * das Frontend bei dieser Breite zeigt. Ausgewählte Blöcke bleiben sichtbar, markiert.
 *
 * Nutzt window.wp.* UMD-Globals.
 */
(function (wp, config) {
	'use strict';

	if (!wp || !wp.hooks || !wp.element || !wp.blockEditor || !wp.components || !wp.compose) {
		return;
	}
	if (!config) {
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

	var VIS_ENABLED = config.visibilityEnabled !== false && Array.isArray(config.blocks);
	var VIS_BLOCKS = Array.isArray(config.blocks) ? config.blocks : [];
	var ALL_BLOCKS = !!config.allBlocks;
	var VIS_ATTR = config.attr;

	var REV_ENABLED = !!config.reverseEnabled && Array.isArray(config.reverseBlocks);
	var REV_BLOCKS = Array.isArray(config.reverseBlocks) ? config.reverseBlocks : [];
	var REV_ATTR = config.reverseAttr;

	var DEVICES = [
		{ key: 'mobile', label: __('Auf Mobile ausblenden', 'rh-responsive') },
		{ key: 'tablet', label: __('Auf Tablet ausblenden', 'rh-responsive') },
		{ key: 'desktop', label: __('Auf Desktop ausblenden', 'rh-responsive') }
	];

	// Greift die Sichtbarkeits-Auswahl für diesen Block? Mit allBlocks jeder Block, sonst die Whitelist.
	function visAllowed(name) {
		return VIS_ENABLED && !!name && (ALL_BLOCKS || VIS_BLOCKS.indexOf(name) !== -1);
	}

	// Greift der Umkehr-Schalter für diesen Block? Nur die feste Flex-/Grid-Zielliste.
	function revAllowed(name) {
		return REV_ENABLED && !!name && REV_BLOCKS.indexOf(name) !== -1;
	}

	addFilter(
		'blocks.registerBlockType',
		'rh-responsive/add-attribute',
		function (settings, name) {
			var added = {};
			if (visAllowed(name)) {
				added[VIS_ATTR] = { type: 'array', default: [] };
			}
			if (revAllowed(name)) {
				added[REV_ATTR] = { type: 'boolean', default: false };
			}
			if (Object.keys(added).length === 0) {
				return settings;
			}
			settings.attributes = Object.assign({}, settings.attributes, added);
			return settings;
		}
	);

	var withResponsivePanel = createHigherOrderComponent(
		function (BlockEdit) {
			return function (props) {
				var showVis = visAllowed(props.name);
				var showRev = revAllowed(props.name);
				if (!showVis && !showRev) {
					return el(BlockEdit, props);
				}

				var attrs = props.attributes || {};
				var controls = [];

				if (showVis) {
					var current = attrs[VIS_ATTR] || [];
					DEVICES.forEach(function (d) {
						controls.push(el(ToggleControl, {
							key: 'vis-' + d.key,
							label: d.label,
							checked: current.indexOf(d.key) !== -1,
							onChange: function () {
								var next = current.slice();
								var i = next.indexOf(d.key);
								if (i === -1) { next.push(d.key); } else { next.splice(i, 1); }
								var update = {};
								update[VIS_ATTR] = next;
								props.setAttributes(update);
							}
						}));
					});
				}

				if (showRev) {
					controls.push(el(ToggleControl, {
						key: 'reverse',
						label: __('Mobil: Reihenfolge umkehren', 'rh-responsive'),
						help: __('Kehrt die Reihenfolge der Kinder auf Mobile um (z.B. für Zickzack-Layouts).', 'rh-responsive'),
						checked: !!attrs[REV_ATTR],
						onChange: function (value) {
							var update = {};
							update[REV_ATTR] = !!value;
							props.setAttributes(update);
						}
					}));
				}

				return el(
					Fragment,
					null,
					el(BlockEdit, props),
					el(
						InspectorControls,
						null,
						el(
							PanelBody,
							{ title: __('Responsive', 'rh-responsive'), initialOpen: false },
							controls
						)
					)
				);
			};
		},
		'withRhResponsivePanel'
	);

	addFilter('editor.BlockEdit', 'rh-responsive/inspector', withResponsivePanel);

	// ------------------------------------------------------------------
	// Canvas-Ansicht
	// ------------------------------------------------------------------

	var CLASS_MAP = config.classMap || {};
	var REV_CLASS = config.reverseClass || '';
	var CANVAS_CSS = config.canvasCss || '';
	var STYLE_ID = 'rh-responsive-canvas';

	if (!wp.data || !CANVAS_CSS) {
		return;
	}

	// Der Canvas ist ein Iframe (editor-canvas). Ohne Iframe, etwa bei alten
	// Blöcken ohne apiVersion 3, rendert der Editor direkt ins Dokument.
	function canvasDocument() {
		var frame = document.querySelector('iframe[name="editor-canvas"]');
		if (frame && frame.contentDocument && frame.contentDocument.body) {
			return frame.contentDocument;
		}
		return document.querySelector('.editor-styles-wrapper') ? document : null;
	}

	function ensureStyle(doc) {
		if (doc.getElementById(STYLE_ID)) {
			return;
		}
		var style = doc.createElement('style');
		style.id = STYLE_ID;
		style.textContent = CANVAS_CSS;
		(doc.head || doc.body).appendChild(style);
	}

	function wantedClasses(name, attrs) {
		var classes = [];
		if (visAllowed(name)) {
			(attrs[VIS_ATTR] || []).forEach(function (key) {
				if (CLASS_MAP[key]) {
					classes.push(CLASS_MAP[key]);
				}
			});
		}
		if (REV_CLASS && revAllowed(name) && attrs[REV_ATTR]) {
			classes.push(REV_CLASS);
		}
		return classes;
	}

	var ALL_CLASSES = Object.keys(CLASS_MAP).map(function (k) { return CLASS_MAP[k]; }).concat(REV_CLASS ? [REV_CLASS] : []);

	function sync() {
		var doc = canvasDocument();
		if (!doc) {
			return;
		}
		ensureStyle(doc);

		var store = wp.data.select('core/block-editor');
		if (!store || !store.getClientIdsWithDescendants) {
			return;
		}
		store.getClientIdsWithDescendants().forEach(function (clientId) {
			var node = doc.querySelector('[data-block="' + clientId + '"]');
			if (!node) {
				return;
			}
			var wanted = wantedClasses(store.getBlockName(clientId), store.getBlockAttributes(clientId) || {});
			ALL_CLASSES.forEach(function (cls) {
				var soll = wanted.indexOf(cls) !== -1;
				if (node.classList.contains(cls) !== soll) {
					node.classList.toggle(cls, soll);
				}
			});
		});
	}

	// Gebündelt pro Frame: der Store meldet sehr oft, der Canvas baut Blöcke
	// nachträglich auf, und ein Wechsel der Geräte-Vorschau ersetzt das Iframe.
	var geplant = false;
	function schedule() {
		if (geplant) {
			return;
		}
		geplant = true;
		window.requestAnimationFrame(function () {
			geplant = false;
			sync();
			watchCanvas();
		});
	}

	var beobachtet = null;
	var observer = new MutationObserver(schedule);
	function watchCanvas() {
		var doc = canvasDocument();
		if (!doc || doc === beobachtet) {
			return;
		}
		observer.disconnect();
		observer.observe(doc.body, { childList: true, subtree: true });
		beobachtet = doc;
	}

	wp.data.subscribe(schedule);
	schedule();
})(window.wp, window.rhResponsiveConfig);
