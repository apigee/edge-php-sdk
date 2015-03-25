# How to use the Apigee Edge PHP SDK
The Apigee Edge Management PHP SDK makes it easy to write PHP modules that use the Apigee Edge management API. By using the Management PHP SDK, you can communicate with Apigee Edge to manage developers, apps, roles, and permissions in the same way as the Apigee Developer Services portal.
## Prerequisites and Installation Requirements
### PHP Requirements
The Edge PHP SDK has the following prerequisites:
- PHP 5.3 or greater. This is required due to the SDK’s use of PHP namespaces and closures, which were not available in 5.2 or earlier.
- JSON support built into PHP. This is enabled in almost all default builds of PHP. Certain Linux distributions (such as Debian) may ship with JSON-C instead of the standard JSON, due to licensing restrictions. This is also acceptable.
- CURL support built into PHP.
- (Recommended) Composer, https://getcomposer.org/, should be installed and in your path.

</br>
### Installation of Dependencies
#### As part of a Composer-based project
If your project is itself based on Composer, simply add `“apigee/edge”` to your project’s composer.json file and run `composer install --no-dev`.  This should install the Edge PHP SDK as well as all its dependencies into your project’s `vendor` dir, and it will integrate them all with your project’s class autoloader. If you want to run the SDK’s unit tests, you should omit the `--no-dev` option.

</br>
#### Using Composer, but not as part of a Composer-based project
If you have installed Composer but choose not to use it to manage your project, you may register the Edge SDK and its dependencies as follows:
```
$ cd path/to/edge-sdk
$ composer install --no-dev
```

This will download and install all the dependencies. You will need to include `path/to/edge-sdk/vendor/autoload.php` in order to reap the benefits of having all your classes autoloaded.

</br>
#### Without Composer
If all of the Edge SDK’s dependencies are supplied by some other aspect of your project (for example, they are all supplied by a base install of Drupal 8), you may create a simple autoloader for the SDK classes from a Unix-like shell prompt as follows:
```
$ cd path/to/edge-sdk
$ sh make-autoloader.sh > autoload.php
```

This will generate `autoload.php`, which you should include in your code in order to autoload all the SDK classes.
In order to use the Edge SDK without Composer, you will need the following packages and their dependencies:
- guzzle/guzzle
- psr/log

</br>
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
