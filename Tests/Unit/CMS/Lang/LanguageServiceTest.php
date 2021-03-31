<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020 Sjoerd Zonneveld  <typo3@bitpatroon.nl>
 *  Date: 5-6-2020 12:53
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

namespace BPN\BpnLang\Tests\Unit\CMS\Lang;

use BPN\BpnLang\CMS\Lang\LanguageService;
use PHPUnit\Framework\Assert;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Localization\LanguageStore;
use TYPO3\CMS\Core\Localization\Locales;
use TYPO3\CMS\Core\Localization\LocalizationFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class LanguageServiceTest extends UnitTestCase
{
    protected $languageFilePath = 'EXT:bpn_lang/Tests/Unit/CMS/Lang/Fixtures/locallang.xlf';
    protected $labelWithTransLation = 'testLabel';
    protected $labelWithoutTranslation = 'someNonExistingLabel';


    /**
     * Instance of configurationManagerInterface, injected to subject
     *
     * @var ConfigurationManagerInterface
     */
    protected $configurationManagerInterfaceProphecy;

    /**
     * LOCAL_LANG array fixture
     *
     * @var array
     */
    protected $LOCAL_LANG = [];


    /**
     * Prepare class mocking some dependencies
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->LOCAL_LANG = [
            $this->languageFilePath => [
                'default' => [
                    'testLabel' => [
                        [
                            'source' => 'Lorem ipsum dolor sit amet',
                            'target' => 'Lorem ipsum dolor sit amet'
                        ]
                    ],
                ],
                'nl'      => [
                    'testLabel' => [
                        [
                            'source' => 'Lorem ipsum dolor sit amet',
                            'target' => 'Dutch label for testLabel',
                        ]
                    ],
                ],
            ],
        ];

        $reflectionClass = new \ReflectionClass(LocalizationUtility::class);

        /** @var ConfigurationManagerInterface|ObjectProphecy $configurationManagerInterfaceProphecy */
        $this->configurationManagerInterfaceProphecy = $this->prophesize(ConfigurationManagerInterface::class);
        $this->configurationManagerInterfaceProphecy
            ->getConfiguration('Framework', 'core', null)
            ->willReturn([]);
        $property = $reflectionClass->getProperty('LOCAL_LANG');
        $property->setAccessible(true);
        $property->setValue($this->LOCAL_LANG);

        $property = $reflectionClass->getProperty('configurationManager');
        $property->setAccessible(true);
        $property->setValue($this->configurationManagerInterfaceProphecy->reveal());
    }

    /**
     * Reset static properties
     */
    protected function tearDown(): void
    {
        $reflectionClass = new \ReflectionClass(LocalizationUtility::class);

        $property = $reflectionClass->getProperty('configurationManager');
        $property->setAccessible(true);
        $property->setValue(null);

        $property = $reflectionClass->getProperty('LOCAL_LANG');
        $property->setAccessible(true);
        $property->setValue([]);

        GeneralUtility::purgeInstances();

        parent::tearDown();
    }

    /**
     * @test
     * @dataProvider dataProviderSLWorksAsExpected
     * @param string $lll
     * @param int    $variant
     * @param string $expectedTranslations
     * @param string $languageKey
     * @throws \ReflectionException
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     */
    public function sLWorksAsExpected(string $lll, int $variant, string $expectedTranslation, string $languageKey)
    {
        /** @var CacheManager|ObjectProphecy $cacheManagerProphecy */
        $cacheManagerProphecy = $this->prophesize(CacheManager::class);
        $cacheFrontendProphecy = $this->prophesize(FrontendInterface::class);
        $cacheManagerProphecy->getCache('l10n')->willReturn($cacheFrontendProphecy->reveal());
        $cacheFrontendProphecy->get(Argument::cetera())->willReturn(false);
        $cacheFrontendProphecy->set(Argument::cetera())->willReturn(null);

        $GLOBALS['TYPO3_CONF_VARS'] = [
            'BE' => [
                'bpnlang'       => [],
                'languageDebug' => 0
            ]
        ];

        /** @var LanguageService $languageService */
        $languageService = GeneralUtility::makeInstance(
            LanguageService::class,
            new Locales(),
            new LocalizationFactory(new LanguageStore(), $cacheManagerProphecy->reveal())
        );
        $reflectionClass = new \ReflectionClass(LanguageService::class);
        $property = $reflectionClass->getProperty('LL_files_cache');
        $property->setAccessible(true);
        $property->setValue($languageService, $this->LOCAL_LANG);

        $languageService->init($languageKey);

        $GLOBALS['TYPO3_CONF_VARS']['BE']['bpnlang']['debug'] = $variant;
        $actualTranslation = $languageService->sL($lll);
        Assert::assertEquals($expectedTranslation, $actualTranslation);
    }

    public function dataProviderSLWorksAsExpected()
    {
        return [
            [
                $this->trans($this->labelWithTransLation),
                LanguageService::DISPLAY_TEXT_AND_LABEL,
                'Lorem ipsum dolor sit amet',
                'dk'
            ],
            [
                $this->trans($this->labelWithTransLation),
                LanguageService::DISPLAY_NO_LABEL_WHEN_NOT_SET,
                'Lorem ipsum dolor sit amet',
                'dk'
            ],
            [
                $this->trans($this->labelWithTransLation),
                LanguageService::DISPLAY_LABEL_WHEN_NOT_SET,
                'Lorem ipsum dolor sit amet',
                'dk'
            ],
            [
                $this->trans($this->labelWithTransLation),
                LanguageService::DISPLAY_TEXT_AND_LABEL,
                'Dutch label for testLabel',
                'nl'
            ],
            [
                $this->trans($this->labelWithTransLation),
                LanguageService::DISPLAY_NO_LABEL_WHEN_NOT_SET,
                'Dutch label for testLabel',
                'nl'
            ],
            [
                $this->trans($this->labelWithTransLation),
                LanguageService::DISPLAY_LABEL_WHEN_NOT_SET,
                'Dutch label for testLabel',
                'nl'
            ],
            [
                $this->trans($this->labelWithoutTranslation),
                LanguageService::DISPLAY_TEXT_AND_LABEL,
                '[someNonExistingLabel]',
                ''
            ],
            [
                $this->trans($this->labelWithoutTranslation),
                LanguageService::DISPLAY_NO_LABEL_WHEN_NOT_SET,
                '',
                ''
            ],
            [
                $this->trans($this->labelWithoutTranslation),
                LanguageService::DISPLAY_LABEL_WHEN_NOT_SET,
                '[someNonExistingLabel]',
                ''
            ],
            [
                $this->trans($this->labelWithoutTranslation),
                LanguageService::DISPLAY_TEXT_AND_LABEL,
                '[someNonExistingLabel]',
                'nl'
            ],
            [
                $this->trans($this->labelWithoutTranslation),
                LanguageService::DISPLAY_NO_LABEL_WHEN_NOT_SET,
                '',
                'nl'
            ],
            [
                $this->trans($this->labelWithoutTranslation),
                LanguageService::DISPLAY_LABEL_WHEN_NOT_SET,
                '[someNonExistingLabel]',
                'nl'
            ],
        ];
    }

    private function trans(string $label)
    {
        return 'LLL:' . $this->languageFilePath . ':' . $label;
    }
}
