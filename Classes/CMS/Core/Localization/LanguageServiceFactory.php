<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020 Sjoerd Zonneveld  <typo3@bitpatroon.nl>
 *  Date: 5-6-2020 11:41
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

namespace BPN\BpnLang\CMS\Core\Localization;

use BPN\BpnLang\CMS\Lang\LanguageService as BpnLanguage;
use TYPO3\CMS\Core\Localization\LanguageService;

class LanguageServiceFactory extends \TYPO3\CMS\Core\Localization\LanguageServiceFactory
{
    /**
     * Factory method to create a language service object.
     *
     * @param string $locale the locale (= the TYPO3-internal locale given)
     * @return LanguageService
     */
    public function create(string $locale): LanguageService
    {
        $obj = new BpnLanguage($this->locales, $this->localizationFactory);
        $obj->init($locale);
        return $obj;
    }
}
