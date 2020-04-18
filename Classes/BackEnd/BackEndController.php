<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Sjoerd Zonneveld <typo3@bitpatroon.nl>
 *  Date: 5-1-2017 12:37
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

namespace BPN\BpnLang\BackEnd;

use TYPO3\CMS\Core\Page\PageRenderer;

/**
 * Class BackEndController
 * @author  : SZO
 * @fqn     : BPN\BpnLang\BackEnd\BackEndController
 */
class BackEndController
{

    /**
     * Hook
     * @param array                                           $params The parameter Array
     * @param \TYPO3\CMS\Backend\Controller\BackendController $ref    The parent object
     */
    public function renderPreProcess(&$params, $ref)
    {
        if (!($ref instanceof \TYPO3\CMS\Backend\Controller\BackendController)) {
            return;
        }

        $this->addLangToggle($params, $ref);
    }

    /**
     * Hook
     * @param array                                           $params The parameter Array
     * @param \TYPO3\CMS\Backend\Controller\BackendController $ref    The parent object
     */
    private function addLangToggle(
        /** @noinspection PhpUnusedParameterInspection */
        &$params,
        $ref
    ) {
        $folder = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('bpn_lang');

        /** @var PageRenderer $pageRenderer */
        $pageRenderer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/BpnLang/Backend/BpnLang');
        $pageRenderer->addCssFile($folder . 'Resources/Public/CSS/BackEnd/Backend.css');
    }
}
