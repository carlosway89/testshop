<?php
	/* --------------------------------------------------------------
	  StyleEdit v2.0
	  Gambio GmbH
	  http://www.gambio.de
	  Copyright (c) 2015 Gambio GmbH
	  --------------------------------------------------------------
	*/

	if(defined('_STYLE_EDIT_VALID_CALL') === false)
	{	
		die('');
	}

	define('SIDE_LAYER_EXPERT_HEADING',						'Expertenmodus');
	define('TABS_TITLE_STYLES',								'Styles');
	define('TABS_TITLE_BOXES',								'Boxen');
	define('TABS_TITLE_BACKUP',								'Option');

	define('ACCORDION_TITLE_FONT',							'Inhalt');
	define('ACCORDION_TITLE_BACKGROUND',					'Hintergrund');
	define('ACCORDION_TITLE_POSITION',						'Rand');
	define('ACCORDION_TITLE_DIMENSION',						'Position &amp; Gr&ouml;&szlig;e');
	define('ACCORDION_TITLE_MOUSE_ACTIONS',					'Maus-Effekte');
	
	define('TITLE_TOOLBOX',									'Position &amp; Gr&ouml;&szlig;e');
	define('TITLE_FILENAME',								'Dateiname: ');

	define('ACCORDION_TITLE_IMPORT',						'Upload');
	define('ACCORDION_TITLE_EXPORT',						'Sicherung');
	define('ACCORDION_TITLE_ARCHIVE',						'Archiv');
	define('ACCORDION_TITLE_BOXES',							'Men&uuml;boxen positionieren');
	define('ACCORDION_TITLE_OPTIONS',						'Weitere Einstellungen');

	define('ACCORDION_TITLE_AREA_EMPTY',					'F&uuml;r dieses Element gibt es in diesem Bereich keine Style-Eigenschafen.');

	define('FORM_TITLE_FONT_FACE',							'Schriftart');
	define('FORM_TITLE_FONT_COLOR',							'Farbe');
	define('FORM_TITLE_FONT_STYLES',						'Stil');
	define('FORM_TITLE_FONT_POSITION',						'Ausrichtung');
	define('FORM_TITLE_FONT_SIZE',							'Gr&ouml;&szlig;e');
	define('FORM_TITLE_IMPORT',								'Designeinstellungen hochladen');
	define('FORM_TITLE_EXPORT',								'Designeinstellungen sichern');
	define('FORM_TITLE_BACKGROUND_COLOR',					'Farbe');
	define('FORM_TITLE_BACKGROUND_IMAGE',					'Hintergrundbild');
	define('FORM_TITLE_BACKGROUND_REPEAT_NOT',				'Bild nicht kacheln');
	define('FORM_TITLE_BACKGROUND_REPEAT',					'Bild kacheln');
	define('FORM_TITLE_BACKGROUND_REPEAT_X',				'Bild nur horizontal kacheln');
	define('FORM_TITLE_BACKGROUND_REPEAT_Y',				'Bild nur vertikal kacheln');
	define('FORM_TITLE_BACKGROUND_TRANSPARENCY',			'Transparenz');
	define('FORM_TITLE_BACKGROUND_NO_TRANSPARENCY',			'Keine Transparenz');
	define('FORM_TITLE_BACKGROUND_GRADIENT',				'- Farbverlauf -');
	define('FORM_TITLE_BACKGROUND_NO_GRADIENT',				'keinen Farbverlauf');

	define('FORM_TITLE_EXPERT_POSITION',					'position:');
	define('FORM_TITLE_EXPERT_POSITION_TOP',				'top');
	define('FORM_TITLE_EXPERT_POSITION_RIGHT',				'right');
	define('FORM_TITLE_EXPERT_POSITION_BOTTOM',				'bottom');
	define('FORM_TITLE_EXPERT_POSITION_LEFT',				'left');
	define('FORM_TITLE_EXPERT_POSITION_STATIC',				'static');
	define('FORM_TITLE_EXPERT_POSITION_RELATIVE',			'relative');
	define('FORM_TITLE_EXPERT_POSITION_ABSOLUTE',			'absolute');
	define('FORM_TITLE_EXPERT_POSITION_FIXED',				'fixed');
	define('FORM_TITLE_EXPERT_BACKGROUND_POSITION',			'background-position:');
	define('FORM_TITLE_EXPERT_FLOAT',						'float:');
	define('FORM_TITLE_EXPERT_FLOAT_NONE',					'none');
	define('FORM_TITLE_EXPERT_FLOAT_LEFT',					'left');
	define('FORM_TITLE_EXPERT_FLOAT_RIGHT',					'right');
	define('FORM_TITLE_EXPERT_CLEAR',						'clear:');
	define('FORM_TITLE_EXPERT_CLEAR_LEFT',					'left');
	define('FORM_TITLE_EXPERT_CLEAR_RIGHT',					'right');
	define('FORM_TITLE_EXPERT_CLEAR_BOTH',					'both');
	define('FORM_TITLE_EXPERT_CLEAR_NONE',					'none');
	define('FORM_TITLE_EXPERT_OVERFLOW',					'overflow:');
	define('FORM_TITLE_EXPERT_OVERFLOW',					'visible');
	define('FORM_TITLE_EXPERT_OVERFLOW_HIDDEN',				'hidden');
	define('FORM_TITLE_EXPERT_OVERFLOW_VISIBLE',			'visible');
	define('FORM_TITLE_EXPERT_OVERFLOW_SCROLL',				'scroll');
	define('FORM_TITLE_EXPERT_OVERFLOW_AUTO',				'auto');
	define('FORM_TITLE_EXPERT_DISPLAY',						'display:');
	define('FORM_TITLE_EXPERT_DISPLAY_NONE',				'none');
	define('FORM_TITLE_EXPERT_DISPLAY_BLOCK',				'block');
	define('FORM_TITLE_EXPERT_DISPLAY_INLINE',				'inline');
	define('FORM_TITLE_EXPERT_DISPLAY_INLINE_BLOCK',		'inline-block');
	define('FORM_TITLE_EXPERT_DISPLAY_LIST_ITEM',			'list-item');
	define('FORM_TITLE_EXPERT_WHITE_SPACE',					'white-space:');
	define('FORM_TITLE_EXPERT_WHITE_SPACE_NORMAL',			'normal');
	define('FORM_TITLE_EXPERT_WHITE_SPACE_PRE',				'pre');
	define('FORM_TITLE_EXPERT_WHITE_SPACE_NOWRAP',			'nowrap');
	define('FORM_TITLE_EXPERT_WHITE_SPACE_PRE_WRAP',		'pre-wrap');
	define('FORM_TITLE_EXPERT_WHITE_SPACE_PRE_LINE',		'pre-line');
	define('FORM_TITLE_EXPERT_VERTICAL_ALIGN',				'vertical-align:');
	define('FORM_TITLE_EXPERT_VERTICAL_ALIGN_SUB',			'sub');
	define('FORM_TITLE_EXPERT_VERTICAL_ALIGN_SUPER',		'super');
	define('FORM_TITLE_EXPERT_VERTICAL_ALIGN_BASELINE',		'baseline');
	define('FORM_TITLE_EXPERT_VERTICAL_ALIGN_TOP',			'top');
	define('FORM_TITLE_EXPERT_VERTICAL_ALIGN_BOTTOM',		'bottom');
	define('FORM_TITLE_EXPERT_VERTICAL_ALIGN_TEXT_TOP',		'text-top');
	define('FORM_TITLE_EXPERT_VERTICAL_ALIGN_TEXT_BOTTOM',	'text-bottom');
	define('FORM_TITLE_EXPERT_LINE_HEIGHT',					'line-height:');
	define('FORM_TITLE_EXPERT_LIST_STYLE_POSITION',			'list-style-position:');
	define('FORM_TITLE_EXPERT_LIST_STYLE_POSITION_INSIDE',	'inside');
	define('FORM_TITLE_EXPERT_LIST_STYLE_POSITION_OUTSIDE',	'outside');
	define('FORM_TITLE_EXPERT_LIST_STYLE_TYPE',				'list-style-type:');
	define('FORM_TITLE_EXPERT_LIST_STYLE_TYPE_NONE',		'none');
	define('FORM_TITLE_EXPERT_LIST_STYLE_TYPE_CIRCLE',		'circle');
	define('FORM_TITLE_EXPERT_LIST_STYLE_TYPE_SQUARE',		'square');
	define('FORM_TITLE_EXPERT_LIST_STYLE_TYPE_DISC',		'disc');
	define('FORM_TITLE_EXPERT_LIST_STYLE_TYPE_DECIMAL',		'decimal');
	define('FORM_TITLE_EXPERT_LIST_STYLE_TYPE_LOWER_ROMAN',	'lower-roman');
	define('FORM_TITLE_EXPERT_LIST_STYLE_TYPE_UPPER_ROMAN',	'upper-roman');
	define('FORM_TITLE_EXPERT_CURSOR',						'cursor:');
	define('FORM_TITLE_EXPERT_CURSOR_AUTO',					'auto');
	define('FORM_TITLE_EXPERT_CURSOR_DEFAULT',				'default');
	define('FORM_TITLE_EXPERT_CURSOR_CROSSHAIR',			'crosshair');
	define('FORM_TITLE_EXPERT_CURSOR_POINTER',				'pointer');
	define('FORM_TITLE_EXPERT_CURSOR_MOVE',					'move');
	define('FORM_TITLE_EXPERT_CURSOR_TEXT',					'text');
	define('FORM_TITLE_EXPERT_CURSOR_WAIT',					'wait');
	define('FORM_TITLE_EXPERT_CURSOR_HELP',					'help');
	define('FORM_TITLE_EXPERT_CURSOR_PROGRESS',				'progress');
	define('FORM_TITLE_EXPERT_CURSOR_N_RESIZE',				'n-resize');
	define('FORM_TITLE_EXPERT_CURSOR_NE_RESIZE',			'ne-resize');
	define('FORM_TITLE_EXPERT_CURSOR_E_RESIZE',				'e-resize');
	define('FORM_TITLE_EXPERT_CURSOR_SE_RESIZE',			'se-resize');
	define('FORM_TITLE_EXPERT_CURSOR_S_RESIZE',				's-resize');
	define('FORM_TITLE_EXPERT_CURSOR_SW_RESIZE',			'sw-resize');
	define('FORM_TITLE_EXPERT_CURSOR_W_RESIZE',				'w-resize');
	define('FORM_TITLE_EXPERT_CURSOR_NW_RESIZE',			'nw-resize');

	define('FORM_TITLE_DIMENSIONS_WIDTH',					'Breite');
	define('FORM_TITLE_DIMENSIONS_HEIGHT',					'H&ouml;he');
	define('FORM_TITLE_DIMENSIONS_MARGIN',					'Au&szlig;enabstand');
	define('FORM_TITLE_DIMENSIONS_PADDING',					'Innenabstand');

	define('FORM_TITLE_BORDER_',							'Alle');
	define('FORM_TITLE_BORDER_TOP',							'Oben');
	define('FORM_TITLE_BORDER_RIGHT',						'Rechts');
	define('FORM_TITLE_BORDER_BOTTOM',						'Unten');
	define('FORM_TITLE_BORDER_LEFT',						'Links');
	define('FORM_TITLE_BORDER_COLOR',						'Farbe');
	define('FORM_TITLE_BORDER_WITDH',						'St&auml;rke');
	define('FORM_TITLE_BORDER_STYLE',						'Stil');
	
	define('FORM_TITLE_PSEUDO_CLASSES',						'Maus-Effekte');
	define('FORM_TITLE_PSEUDO_CLASSES_STANDARD',			'Element Standard');
	define('FORM_TITLE_PSEUDO_CLASSES_ACTIVE',				'Element aktiv (:active)');
	define('FORM_TITLE_PSEUDO_CLASSES_HOVER',				'Element "Mouseover" (:hover)');
	define('FORM_TITLE_PSEUDO_CLASSES_LINK',				'Element nicht besucht (:link)');
	define('FORM_TITLE_PSEUDO_CLASSES_VISITED',				'Element besucht (:visited)');
	define('FORM_TITLE_PSEUDO_CLASSES_FOCUS',				'Element aktiviert (:focus)');


	define('FORM_TITLE_BORDER_STYLE_NONE',					'kein');
	define('FORM_TITLE_BORDER_STYLE_HIDDEN',				'unsichtbar');
	define('FORM_TITLE_BORDER_STYLE_DOTTED',				'gepunktet');
	define('FORM_TITLE_BORDER_STYLE_DASHED',				'gestrichelt');
	define('FORM_TITLE_BORDER_STYLE_SOLID',					'Linie');
	define('FORM_TITLE_BORDER_STYLE_DOUBLE',				'doppelt');
	define('FORM_TITLE_BORDER_STYLE_GROOVE',				'3D-Effekt 1');
	define('FORM_TITLE_BORDER_STYLE_RIDGE',					'3D-Effekt 2');
	define('FORM_TITLE_BORDER_STYLE_INSET',					'3D-Effekt 3');
	define('FORM_TITLE_BORDER_STYLE_OUTSET',				'3D-Effekt 4');

	define('FORM_TITLE_UNIT',								'Einheit');
	define('FORM_TITLE_UNIT_AUTO',							'Automatisch');
	define('FORM_TITLE_UNIT_EM',							'em');
	define('FORM_TITLE_UNIT_PX',							'px');
	define('FORM_TITLE_UNIT_PT',							'pt');
	define('FORM_TITLE_UNIT_PERCENT',						'%');

	define('FORM_TITLE_DIMENSIONS',							'Alle');
	define('FORM_TITLE_DIMENSIONS_TOP',						'Oben');
	define('FORM_TITLE_DIMENSIONS_RIGHT',					'Rechts');
	define('FORM_TITLE_DIMENSIONS_BOTTOM',					'Unten');
	define('FORM_TITLE_DIMENSIONS_LEFT',					'Links');

	define('BUTTON_TITLE_LOAD',								'Laden');
	define('BUTTON_TITLE_DELETE',							'L&ouml;schen');
	define('BUTTON_TITLE_SAVE',								'Download');
	define('BUTTON_TITLE_START',							'Start');
	define('BUTTON_TITLE_PAUSE',							'Pause');
	define('BUTTON_TITLE_UPLOAD',							'Hochladen');
	define('BUTTON_TITLE_PREVIEW',							'Vorschau');

	define('BUTTON_TITLE_TEMPLATE_CONFIGURATION',			'Template Konfiguration');
	define('BUTTON_TITLE_CSS_EDITOR',						'CSS Editor');

	define('INFO_UPLOAD',									'Klicken Sie auf den folgenden Button, um die gew&uuml;nschten Designeinstellungen von Ihrer Festplatte zu laden.');
	define('INFO_EXPORT',									'Klicken Sie auf den folgenden Button, um die aktuellen Designeinstellungen im Archiv zu sichern.');
	define('INFO_START_STYLE_EDIT',							'Klicken Sie auf den gr&uuml;nen Button, um den Bearbeitungsmodus zu starten.');
	define('INFO_START_STYLE_EDITING',						'Klicken Sie auf ein Element im Shop, um es zu bearbeiten.');
	define('INFO_START_BOXES_EDIT',							'Klicken Sie auf den gr&uuml;nen Button, um die Positionierung der Men&uuml;boxen zu aktivieren.');
	define('INFO_START_BOXES_EDITING',						'Klicken Sie auf den roten Button, um die Positionierung der Men&uuml;boxen zu deaktivieren.');
	define('INFO_CLOSE',									'Wollen Sie den Bearbeitungsmodus wirklich beenden?');

	define('GMSE_ERROR_FILE_EXISTS',						'Datei {#FILE#} existiert bereits.');
	define('GMSE_ERROR_FILE_NOT_EXIST',						'Datei existiert nicht.');
	define('GMSE_ERROR_WRONG_DIR_PERM',						'Inkorrekte Zugriffsrechte des Verzeichnisses');
	define('GMSE_ERROR_WRONG_FILE_PERM',					'');
	define('GMSE_ERROR_CANNOT_OPEN_FILE',					'');
	define('GMSE_ERROR_WRONG_FILE_TYP',						'Inkorrekter Dateityp');
	define('GMSE_ERROR_UPLOAD_FAILED',						'');
	define('GMSE_ERROR_NO_ERROR',							'');
	define('GMSE_ERROR_DELETE_SUCCESSFUL',					'Datei entfernt.');
	
	// do not remove line breaks
	define('GMSE_ERROR_DELETE_CONFIRM',						'Möchten Sie die Datei {#FILE#} und ALLE damit verknüpften Hintergrundbild-Styles im gesamten Shop wirklich löschen?

Nicht gespeicherte Änderungen am aktuell ausgewählten Element gehen verloren, da die Seite neu geladen wird.');

	define('GMSE_ERROR_IMPORT_SUCCESSFUL',					'Styles importiert. Browser aktualisieren.');
	define('GMSE_ERROR_EXPORT_SUCCESSFUL',					'Styles in die Datei {#FILE#} exportiert.');
	define('GMSE_ERROR_UPLOAD_SUCCESSFUL',					'Datei {#FILE#} hochgeladen.');
	define('GMSE_ERROR_ARCHIVE_EMPTY',						'Noch keine Dateien vorhanden.');
	define('GMSE_ERROR_WRONG_FILENAME',						'Inkorrekter Dateiname');
	define('GMSE_ERROR_DELETE_FAILED',						'Datei konnte nicht entfernt werden.');
	define('GMSE_ERROR_ARCHIVE_EMPTY',						'Noch keine Dateien vorhanden.');
	define('GMSE_ERROR_STYLES_UPDATED',						'Styles aktualisiert');
	define('GMSE_ERROR_STYLES_EMPTY',						'Noch keine Aenderungen zum Speichern vorgenommen.');
	define('GMSE_ERROR_NO_IMAGE',							'Kein Bild vorhanden.');
	define('GMSE_ERROR_LOGOFF',								'Die Bearbeitungssitzung wurde automatisch beendet. Melden Sie sich bitte erneut an.');

?>