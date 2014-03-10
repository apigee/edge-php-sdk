<?php
/**
 * @file
 * Unit test for \Apigee\ManagementAPI\Developer.
 *
 * @author djohnson
 * @since 31-Jan-2014
 */

namespace Apigee\test\ManagementAPI;

use Apigee\ManagementAPI\Developer;

class DeveloperTest extends \Apigee\test\AbstractAPITest
{

    public function testDeveloperCRUD()
    {
        $developer = new Developer(self::$orgConfig);
        $mail = 'phpunit-' . $this->randomString() . '@example.com';

        // Begin creation
        $developer->blankValues();

        $developer->setEmail($mail);
        $developer->setFirstName($this->randomString());
        $developer->setLastName($this->randomString());
        $developer->setUserName($this->randomString());
        $developer->setAttribute('foo', 'bar');

        try {
            $developer->save();
        } catch (\Exception $e) {
            $this->fail('Cannot save developer at create time: [' . $e->getCode() . '] ' . $e->getMessage());
        }
        $this->assertNotEmpty($developer->getDeveloperId());
        $this->assertEquals($mail, $developer->getEmail());
        $this->assertEquals('bar', $developer->getAttribute('foo'));
        // End creation

        // Begin load
        $developer->blankValues();
        try {
            $developer->load($mail);
        } catch (\Exception $e) {
            $this->fail();
            return;
        }
        $this->assertNotEmpty($developer->getDeveloperId());
        $this->assertEquals($mail, $developer->getEmail());
        $this->assertEquals('bar', $developer->getAttribute('foo'));
        // End load

        // Begin update
        $developer->setAttribute('foo', 'baz');
        try {
            $developer->save(false);
        } catch (\Exception $e) {
            $this->fail('Cannot save developer at update time');
            return;
        }
        $developer->blankValues();
        try {
            $developer->load($mail);
        } catch (\Exception $e) {
            $this->fail('Cannot reload developer after update');
            return;
        }
        $this->assertEquals('baz', $developer->getAttribute('foo'));
        // End update

        // Begin delete
        try {
            $developer->delete();
        } catch (\Exception $e) {
            $this->fail('Cannot delete developer');
        }
        $developer->blankValues();
        try {
            $developer->load($mail);
            // If we succeed in the load, the developer was not deleted.
            $this->fail('Developer deletion failed.');
        } catch (\Exception $e) {
            $this->assertEquals(404, $e->getCode());
        }
        // End delete
    }

    public function testDeveloperList()
    {
        $developer = new Developer(self::$orgConfig);
        try {
            $list = $developer->listDevelopers();
        } catch (\Exception $e) {
            $this->fail('Error fetching developer list');
            return;
        }
        if (empty($list)) {
            $this->fail('Empty developer list');
            return;
        }
    }

    public function testDeveloperLoadAll()
    {
        $developer = new Developer(self::$orgConfig);
        try {
            $list = $developer->loadAllDevelopers();
        } catch (\Exception $e) {
            $this->fail('Error fetching all developers');
            return;
        }
        if (empty($list)) {
            $this->fail('Empty detailed developer list');
            return;
        }
    }
}