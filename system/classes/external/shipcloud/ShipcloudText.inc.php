<?php
/* --------------------------------------------------------------
	ShipcloudText.inc.php 2015-10-15
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Utility class providing text replacements using the 'shipcloud' section.
 */
class ShipcloudText {
	/**
	 * @var LanguageTextManager used to retrieve phrases
	 */
	protected $languageTextManager;

	/**
	 * initializes for the given languages_id or session language.
	 * Uses session language as a default if no languages_id is given.
	 * @param int|null $languages_id a Gambio languages_id
	 */
	public function __construct($languages_id = null)
	{
		$languages_id = is_null($languages_id) ? $_SESSION['languages_id'] : (int)$languages_id;
		$this->languageTextManager = MainFactory::create_object('LanguageTextManager', array('shipcloud', $languages_id));
	}

	/**
	 * returns a single phrase
	 * @param string $placeholder phrase name
	 * @return string phrase value
	 */
	public function get_text($placeholder)
	{
		return $this->languageTextManager->get_text($placeholder);
	}

	/**
	 * replaces phrases denoted by phrase tags ('##phrase_name') in the content.
	 * @param string $content text containing phrase tags
	 * @return string the content with tags replaced by phrases
	 */
	public function replaceLanguagePlaceholders($content)
	{
		while(preg_match('/##(\w+)\b/', $content, $matches) == 1)
		{
			$replacement = $this->get_text($matches[1]);
			if(empty($replacement))
			{
				$replacement = $matches[1];
			}
			$replacement = str_replace('»', '&shy;', $replacement);
			$content = preg_replace('/##'.$matches[1].'/', $replacement.'$1', $content, 1);
		}
		return $content;
	}

}
