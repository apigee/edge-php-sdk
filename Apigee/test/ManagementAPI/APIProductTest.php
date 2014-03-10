<?php
/**
 * @file
 * Unit test for \Apigee\ManagementAPI\APIProduct.
 *
 * @author djohnson
 * @since 30-Jan-2014
 */

namespace Apigee\test\ManagementAPI;

use Apigee\ManagementAPI\APIProduct;

class APIProductTest extends \Apigee\test\AbstractAPITest
{
    public function testApiProductList()
    {
        $api_product = new APIProduct(self::$orgConfig);
        try {
            $product_list = $api_product->listProducts();
        } catch (\Exception $e) {
            $this->fail();
            return;
        }
        $this->assertNotEmpty($product_list);
    }

    /**
     * @depends testApiProductList
     */
    public function testApiProductLoad()
    {
        $api_product = new APIProduct(self::$orgConfig);
        try {
            $list = $api_product->listProducts();
        } catch (\Exception $e) {
            $this->fail();
            return;
        }
        $this->assertNotEmpty($list);
        // Pick a random item from the list. No need to shuffle if list has
        // only 1 member.
        if (count($list) > 1) {
            shuffle($list);
        }
        $item = reset($list);

        try {
            $api_product->load($item->getName());
        } catch (\Exception $e) {
            $this->fail($e->getCode() . ': ' . $e->getMessage());
        }
        $this->assertNotEmpty($api_product->getName());
    }
}