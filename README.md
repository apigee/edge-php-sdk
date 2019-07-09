# The 2.x version has moved
The newer 2.x version of this library has been released and is now available as [Apigee Edge Client Library for PHP](https://github.com/apigee/apigee-client-php/releases).
This library is now in maintenance mode and is only accepting major bug fixes.

# How to use the Apigee Edge PHP SDK

The Apigee Edge Management PHP SDK makes it easy to write PHP modules that use
the Apigee Edge management API. By using the Management PHP SDK, you can
communicate with Apigee Edge to manage developers, apps, roles, and permissions
in the same way as the Apigee Developer Services portal.

## Prerequisites and Installation Requirements
### PHP Requirements

The Edge PHP SDK has the following prerequisites:

* PHP 7.1 or greater. This is required due to the SDK’s use of PHP namespaces
  and closures, and finally block.
* JSON support built into PHP. This is enabled in almost all default builds of
  PHP. Certain Linux distributions (such as Debian) may ship with JSON-C
  instead of the standard JSON, due to licensing restrictions; this is also
  acceptable.
* CURL support built into PHP.
* Composer, https://getcomposer.org/, should be installed and in your path.

### Installation of Dependencies
#### As part of a Composer-based project

If your project is itself based on Composer, simply add `“apigee/edge”`
to your project’s composer.json file and run `composer install --no-dev`.  This
should install the Edge PHP SDK as well as all its dependencies into your
project’s `vendor` dir, and it will integrate them all with your project’s class
autoloader. If you want to run the SDK’s unit tests, you should omit the
`--no-dev` option.

#### Using Composer, but not as part of a Composer-based project

If you have installed Composer but choose not to use it to manage your project,
you may register the Edge SDK and its dependencies as follows:
```
$ cd path/to/edge-sdk
$ composer install --no-dev
```

This will download and install all the dependencies. You will need to include
`path/to/edge-sdk/vendor/autoload.php` in order to reap the benefits of having
all your classes autoloaded.

#### Without Composer

Installation without Composer is no longer supported.

## Connecting to the Edge server

The core object used to configure your connection to Edge is Apigee\Util\OrgConfig.  You can create one as follows:
```php
<?php
// Your organization name
$org = 'my-org';
// API endpoint in Apigee’s cloud
$endpoint = 'https://api.enterprise.apigee.com/v1';
// Authenticated user for this organization.
// This user should have the ‘devadmin’ (or ‘orgadmin’) role on Edge.
$user = 'poweruser@example.com';
// Password for the above user
$pass = 'i<3apis';
// An array of other connection options
$options = array(
  'http_options' => array(
    'connection_timeout' => 4,
    'timeout' => 4
  )
);

$org_config = new Apigee\Util\OrgConfig($org, $endpoint, $user, $pass, $options);
```

Once you have created an OrgConfig object, you can pass it in the constructor to other objects:

```php
<?php
$developer = new Apigee\ManagementAPI\Developer($org_config);
try {
  $developer->load('user@example.com');
  $developer->setFirstName('John');
  $developer->setLastName('Doe');
  $developer->save();
  print "Developer updated!\n";
}
catch (Apigee\Exceptions\ResponseException $e) {
  print $e->getMessage();
}

$app = new Apigee\ManagementAPI\DeveloperApp($org_config, $developer->getEmail());
try {
  $app_list = $app->getListDetail();
  foreach ($app_list as $my_app) {
    print $my_app->getName() . "\n";
  }
}
catch (Apigee\Exceptions\ResponseException $e) {
  print $e->getMessage();
}
```
