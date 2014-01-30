<?php

namespace Apigee\test\ManagementAPI;

use Apigee\ManagementAPI\APIProduct;

class APIProductTest extends \Apigee\test\AbstractAPITest {

  private $apiProductObject;

  public function setUp() {
    parent::setUp();
    $this->apiProductObject = new APIProduct(self::$orgConfig);
  }

  public function testApiProductList() {
    $loaded = FALSE;
    try {
      $list = $this->apiProductObject->listProducts();
      $loaded = TRUE;
    }
    catch (\Exception $e) {}

    $this->assertTrue($loaded);

    // Can't load an API Product if none are set up yet.
    if (empty($list)) {
      return;
    }
    shuffle($list);
    $item = reset($list);

    $loaded = FALSE;
    try {
      $this->apiProductObject->load($item);
      $loaded = TRUE;
    }
    catch (\Exception $e) {}

    $this->assertTrue($loaded);
  }
}