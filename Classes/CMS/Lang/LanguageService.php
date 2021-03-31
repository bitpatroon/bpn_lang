<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2018 Sjoerd Zonneveld <typo3@bitpatroon.nl>
 *  Date: 17-5-2018 12:54
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

namespace BPN\BpnLang\CMS\Lang;

/**
 * Class LangService
 *
 * Overrides the language service by showing the debug lang Tag when then tag is undefined.
 *
 * @author  : SZO
 * @fqn     : BPN\BpnLang\CMS\Lang\LangService
 */
class LanguageService extends \TYPO3\CMS\Core\Localization\LanguageService
{
    public const DISPLAY_LABEL_WHEN_NOT_SET = 2; // extension default
    public const DISPLAY_NO_LABEL_WHEN_NOT_SET = 1; // no debug
    public const DISPLAY_TEXT_AND_LABEL = 0;  // TYPO3 default

    /**
     * @var int
     */
    private $settings = -1;

    /**
     * splitLabel function
     *
     * All translations are based on $LOCAL_LANG variables.
     * 'language-splitted' labels can therefore refer to a local-lang file + index.
     * Refer to 'Inside TYPO3' for more details
     *
     * @param string $input Label key/reference
     * @return string
     */
    public function sL($input)
    {
        $sL = parent::sL($input);
        switch ($this->getLangDebugSetting()) {
            case self::DISPLAY_NO_LABEL_WHEN_NOT_SET:
            case self::DISPLAY_TEXT_AND_LABEL:
                return $sL;

            case self::DISPLAY_LABEL_WHEN_NOT_SET:
                return $this->showDebugKey($sL, $input);
        }
        return $sL;
    }

    /**
     * Returns the label with key $index from the $LOCAL_LANG array used as the second argument
     *
     * @param string $index         Label key
     * @param array  $localLanguage $LOCAL_LANG array to get label key from
     * @param bool   $hsc           If set, the return value is htmlspecialchar'ed
     * @return string
     */
    public function getLLL($index, $localLanguage)
    {
        $lll = parent::getLLL($index, $localLanguage);
        if (empty($lll)) {
            return $this->showDebugKey($lll, $index);
        }
        return $lll;
    }

    /**
     * Gets if the debug lang key should be shown or the original
     * @return int the setting
     */
    protected function getLangDebugSetting()
    {
        if ($this->settings < 0) {
            $this->settings = $this->getLabelSettings();
        }
        return $this->settings;
    }

    /**
     * @return int
     */
    protected function getLabelSettings()
    {
        $setting = self::DISPLAY_TEXT_AND_LABEL;
        if (isset($_COOKIE['tx_bpnlang'])) {
            $setting = (int)$_COOKIE['tx_bpnlang'];
        }

        if (isset($GLOBALS['TYPO3_CONF_VARS']['BE']['bpnlang']['debug'])) {
            $setting = (int)$GLOBALS['TYPO3_CONF_VARS']['BE']['bpnlang']['debug'];
        }

        // autocorrect
        if ($setting > self::DISPLAY_LABEL_WHEN_NOT_SET) {
            return self::DISPLAY_LABEL_WHEN_NOT_SET;
        }

        return $setting;
    }

    /**
     * @param string $sL
     * @param string $input
     * @return string
     */
    private function showDebugKey(string $sL, string $input = null)
    {
        if (empty($input)) {
            return '';
        }

        switch ($this->getLangDebugSetting()) {
            case self::DISPLAY_LABEL_WHEN_NOT_SET:
                if (!empty($sL)) {
                    return $sL;
                }
                return sprintf('[%s]', $this->getLanguageKey($input));

            case self::DISPLAY_TEXT_AND_LABEL:
                $label = sprintf('%s [%s]', $sL, $this->getLanguageKey($input));
                return trim($label);

            default:
                if ($this->debugKey) {
                    return parent::debugLL($input);
                }
                return $sL;
        }
    }


    private function getLanguageKey(string $input): string
    {
        if (empty($input)) {
            return '';
        }
        if (strpos($input, ':') !== false) {
            [, , , $key] = explode(':', $input);
            return $key;
        }
        return $input;
    }
}
