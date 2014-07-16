<?php
/**
 * @file
 * Unit test for \Apigee\ManagementAPI\DeveloperApp.
 *
 * @author djohnson
 * @since 31-Jan-2014
 */

namespace Apigee\test\ManagementAPI;

use Apigee\ManagementAPI\Developer;
use Apigee\ManagementAPI\DeveloperApp;
use Apigee\ManagementAPI\APIProduct;

class DeveloperAppTest extends \Apigee\test\AbstractAPITest
{

    public function testAppCRUD()
    {
        $developer = new Developer(self::$orgConfig);
        $mail = 'phpunit-' . $this->randomString() . '@example.com';

        // Create a developer to test on
        $developer->blankValues();

        $developer->setEmail($mail);
        $developer->setFirstName($this->randomString());
        $developer->setLastName($this->randomString());
        $developer->setUserName($this->randomString());

        try {
            $developer->save();
        } catch (\Exception $e) {
            $this->fail('Cannot save developer at create time: [' . $e->getCode() . '] ' . $e->getMessage());
        }

        $apiproduct = new APIProduct(self::$orgConfig);
        $products = $apiproduct->listProducts();
        shuffle($products);
        $product = array_shift($products);
        $new_product = array_shift($products);

        // Begin create
        $app = new DeveloperApp(self::$orgConfig, $mail);

        $app->setName('phpunit test');
        $app->setAccessType('read');
        $app->setCallbackUrl('http://example.com/');
        $app->setApiProducts(array($product->getName()));
        $app->setAttribute('foo', 'bar');

        try {
            $app->save();
        } catch (\Exception $e) {
            $this->fail('Cannot save app at creation time');
        }
        $this->assertNotEmpty($app->getConsumerKey());
        $this->assertNotEmpty($app->getAppId());
        $this->assertEquals('bar', $app->getAttribute('foo'));
        // End create

        // Begin load
        $app->blankValues();
        try {
            $app->load('phpunit test');
        } catch (\Exception $e) {
            $this->fail('Cannot load app.');
            return;
        }
        $this->assertNotEmpty($app->getConsumerKey());
        $this->assertEquals($app->getDeveloperId(), $developer->getDeveloperId());
        // End load

        // Begin update
        $app->setAttribute('foo', 'baz');
        try {
            $app->save(FALSE);
        } catch (\Exception $e) {
            $this->fail('Cannot save app at update time');
            return;
        }
        $app->blankValues();
        try {
            $app->load('phpunit test');
        } catch (\Exception $e) {
            $this->fail('Cannot reload app after update');
            return;
        }
        $this->assertEquals('baz', $app->getAttribute('foo'));
        // End update

        // Update key
        $key = $app->getConsumerKey();
        $new_product_name = $new_product->getName();
        $app->setApiProducts(array($new_product_name));
        try {
            $app->save();
        } catch (\Exception $e) {
            $this->fail('Cannot save app when updating API Products.');
        }
        $api_products = $app->getApiProducts();
        $cred_api_products = $app->getCredentialApiProducts();
        if (count($api_products) != 1 || $api_products[0] != $new_product_name) {
            $this->fail('Failed to update API Products list');
        }
        if (count($cred_api_products) != 1 || $cred_api_products[0]['apiproduct'] != $new_product_name) {
            $this->fail('Failed to update Credential API Products list');
        }
        $this->assertEquals($key, $app->getConsumerKey(), 'Consumer Key changed when API Product changed.');
        // End update key

        // Create key
        $key = $this->randomString(16);
        $secret = $this->randomString(16);
        try {
            $app->createKey($key, $secret);
        } catch (\Exception $e) {
            $this->fail('Cannot create key: ' . $e->getMessage());
            return;
        }
        $app->blankValues();
        $app->load('phpunit test');
        $this->assertEquals($key, $app->getConsumerKey(), 'Consumer Key changed to our custom value.');
        // End create key

        // Begin delete
        try {
            $app->delete();
        } catch (\Exception $e) {
            $this->fail('Cannot delete app');
        }
        $app->blankValues();
        try {
            $app->load($mail);
            // If we succeed in the load, the developer was not deleted.
            $this->fail('App deletion failed.');
        } catch (\Exception $e) {
            $this->assertEquals(404, $e->getCode());
        }
        // End delete

        // Clean up
        try {
            $developer->delete();
        } catch (\Exception $e) {
        }
    }

    public function testAppListAll()
    {
        $app = new DeveloperApp(self::$orgConfig, '');
        $list = NULL;
        try {
            $list = $app->listAllApps();
        } catch (\Exception $e) {
            $this->fail('Could not list all org apps: ' . get_class($e) . ' [' . $e->getCode() . '] ' . $e->getMessage() . "\n" . $e->getTraceAsString());
        }
        $this->assertNotEmpty($list);
    }

    /**
     * @depends testAppListAll
     */
    public function testAppListByDeveloper()
    {
        $app = new DeveloperApp(self::$orgConfig, '');
        $list = $app->listAllApps();
        shuffle($list);
        $item = reset($list);

        try {
            $list = $item->getList();
        } catch (\Exception $e) {
            $this->fail('Could not fetch app list for developer ' . $item->getDeveloperMail());
        }
        $this->assertNotEmpty($list);
    }

}