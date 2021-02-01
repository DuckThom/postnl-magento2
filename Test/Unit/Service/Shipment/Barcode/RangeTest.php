<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Test\Unit\Service\Shipment\Barcode;

use TIG\PostNL\Config\Provider\AccountConfiguration;
use TIG\PostNL\Config\Provider\DefaultConfiguration;
use TIG\PostNL\Config\Provider\Globalpack;
use TIG\PostNL\Config\Source\Options\ProductOptions;
use TIG\PostNL\Service\Shipment\Barcode\Range;
use TIG\PostNL\Test\TestCase;

class RangeTest extends TestCase
{
    public $instanceClass = Range::class;

    public function testGetNL()
    {
        /** @var Range $instance */
        $instance = $this->getInstance();
        $result = $instance->get('NL');

        $expected = [
            'type'  => '3S',
            'range' => '123456',
            'serie' => '000000000-999999999',
        ];

        $this->assertEquals($expected, $result);
    }

    public function testGetEU()
    {
        /** @var Range $instance */
        $instance = $this->getInstance();
        $result = $instance->get('EU');

        $expected = [
            'type'  => '3S',
            'range' => '123456',
            'serie' => '0000000-9999999',
        ];

        $this->assertEquals($expected, $result);
    }

    public function testGetGlobal()
    {
        /** @var Range $instance */
        $instance = $this->getInstance();
        $result = $instance->get('global');

        $expected = [
            'type'  => 'CD',
            'range' => '1660',
            'serie' => '0000-9999',
        ];

        $this->assertEquals($expected, $result);
    }

    public function getByProductProvider()
    {
        return [
            'Netherlands' => ['3085', ['type' => '3S', 'range' => '123456', 'serie' => '000000000-999999999']],
            'EPS Country' => ['4952', ['type' => '3S', 'range' => '123456', 'serie' => '0000000-9999999']],
            'Globalpack' => ['4945', ['type' => 'CD', 'range' => '1660', 'serie' => '0000-9999']],
        ];
    }

    /**
     * @dataProvider getByProductProvider
     *
     * @param $productCode
     * @param $expected
     */
    public function testGetByProductCode($productCode, $expected)
    {
        /** @var Range $instance */
        $instance = $this->getInstance();
        $result = $instance->getByProductCode($productCode);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return mixed
     */
    public function getInstance(array $args = [])
    {
        $accountConfigurationMock = $this->getFakeMock(AccountConfiguration::class, true);
        $this->mockFunction($accountConfigurationMock, 'getCustomerCode', '123456');

        $defaultConfigurationMock = $this->getFakeMock(Globalpack::class, true);
        $this->mockFunction($defaultConfigurationMock, 'getBarcodeType', 'CD');
        $this->mockFunction($defaultConfigurationMock, 'getBarcodeRange', '1660');

        $optionsConfigurationMock = $this->getFakeMock(ProductOptions::class);
        $optionsConfigurationMock->setMethods(null);

        $instance = parent::getInstance($args + [
            'accountConfiguration' => $accountConfigurationMock,
            'globalpack' => $defaultConfigurationMock,
            'options' => $optionsConfigurationMock->getMock()
        ]);

        return $instance;
    }
}
