# DigiCert Constellix DNS API PHP Client Library

This is an API client library for the [Constellix](https://www.constellix.com) API.

More information about the API may be found in the [official API documentation]().

 - [Installation](#installation)
 - [Usage](#usage)
 - [Examples](#examples)
 - [License](#license)

## Installation

### Standalone

The easiest way to install and use this client library is using Composer. The following command will add the library to your application and install it from Packagist.

```bash
composer require tiggee/constellix-v4-client
```

You will also need a [PSR-18 HTTP Client](https://www.php-fig.org/psr/psr-18/) implementation. We recommend Guzzle, you you can use any compatible client.

```bash
composer require guzzlehttp/guzzle
```

### Laravel

If you are using this package inside Laravel, then there is a Laravel-specific package you can include instead. This will add a service provider and use Laravel's own pagination methods. You can include this using:

```bash
composer require tiggee/constellix-v4-client-laravel
```

For more details, please see the [documentation for that package](https://github.com/Constellix/constellix-php-sdk-laravel).

## Getting Started

You will need a DigiCert Constellix DNS account to manage DNS records. You can sign up for one on the [DigiCert Constellix DNS](https://constellix.com) site.

If you are using Composer, you should be using Composer's autoload to load libraries:

```php
require_once 'vendor/_autoload.php';
```

With the libraries loaded, you just need to create the client and set the API key and secret key.

```php
$httpClient = new \GuzzleHttp\Client();

$client = new \Constellix\Client\Client($httpClient);
$client->setApiKey(API_KEY);
$client->setSecretKey(SECRET_KEY);
```

You may now use the client to query the API retrieve objects. Usage is documented in GitHub in the docs directory.


### Putting it all together

Putting this together, it's time for the API equivalent of Hello World. Let's get a list of your domains.

```php
<?php
// Load the library and dependencies
require_once 'vendor/_autoload.php';

// Create a new client and set our credentials
$httpClient = new \GuzzleHttp\Client();
$client = new \Constellix\Client\Client($client);
$client->setApiKey("Your API Key");
$client->setSecretKey("Your Secret Key");


// Create a new domain
$domain = $client->domains->create();
$domain->name = 'mydomain.example.com';
$domain->save();

// Print out our domain
echo json_encode($domain, JSON_PRETTY_PRINT);

// Now fetch a list of our domains
$domains = $client->domains->paginate();
foreach ($domains as $domain) {
    echo json_encode($domain, JSON_PRETTY_PRINT);
}
```

There's more examples further down of using the API client SDK.

## Configuration

There's additional configuration options you can use with the client as well, including logging, and specifying a separate API URL.

 ### Logging

You can specify a logger that implements the [PSR-3 Logger](https://www.php-fig.org/psr/psr-3/) specification such as MonoLog. The client is a `LoggerAwareInterface` and the logger can be specified either in the constructor or via a method call.

```php
$myLogger = new \Monolog\Logger('name');
$myLogger->pushHandler(new \Monolog\Handler\StreamHandler('php://stdout', \Psr\Log\LogLevel::DEBUG));

$client = new \Constellix\Client\Client(logger: $myLogger);
```

```php
$myLogger = new \Monolog\Logger('name');
$myLogger->pushHandler(new \Monolog\Handler\StreamHandler('php://stdout', \Psr\Log\LogLevel::DEBUG));

$client->setLogger($myLogger);
```

If no logger is specified then a null logger that does nothing will be used.

### Custom HTTP Client

If you need additional configuration for HTTP requests in your application, for example to specify a proxy server or if you want to use your own HTTP client matching the [PSR-18 HTTP Client](https://www.php-fig.org/psr/psr-18/) specification.

You can specify the client using either the constructor or via a method call.

```php
$myClient = new \GuzzleHttp\Client([
    'proxy' => 'http://localhost:8123'
]);
$client = new \Constellix\Client\Client(client: $myClient);
```

```php
$myClient = new \GuzzleHttp\Client([
    'proxy' => 'http://localhost:8123'
]);
$client->setHttpClient($myClient);
```

## Examples

Full documentation of the library methods are in the docs folder.

### Managers

Managers are used for managing your access to resources on the API, including creating new resources and fetching existing ones from the API. These can be accessed as properties on the client.

```php
// Fetch our manager
$domainsManager = $client->domains;
// Ask our manager for the domain
$domain = $domainsManager->get(1234);
```

Manages are also used to create new objects.

```php
// Create a new domain
$domain = $client->domains->create();
$domain->name = 'example.com';

// Save the domain
$domain->save();
```

The domain is not saved on the API until you call `$domain->save()`.

Multiple objects can be fetched using the `paginate()` method on the manager. You can specify the page number and the number of items per page.

```php
// Return the 4th page of results with the default page size
$client->domains->paginate(4);

// Return the first page of 50 results
$client->domains->paginate(1, 50);
```

### Models

The models themselves follow an Active Record pattern. Properties can be updated and `save()` called on the model to update the API.

```php
// Fetch an existing domain with the ID 1234
$domain = $client->domains->get(1234);
// Update the gtdEnabled property
$domain->gtdEnabled = true;
// Save the domain object on the API
$domain->save();
```

You can delete an object by calling `delete()` on it:

```php
$domain = $client->domains->get(1234);
$domain->delete();
```

### Creating a domain and records

This example creates a new domain and adds records to it.

```php
// Include composer libraries
require_once 'vendor/_autoload.php';

// Create the client
$client = new \Constellix\Client\Client;
$client->setApiKey(API_KEY);
$client->setSecretKey(SECRET_KEY);

// Create the domain
$domain = $client->domains->create();
$domain->name = 'example.com';
$domain->save();

// Create a record on the domain
$record = $domain->records->create();
$record->type = \Constellix\Client\Enums\RecordType::A();
$record->name = 'www';

// Create a value for the record and assign it to the record
$value = new \Constellix\Client\Models\Helpers\RecordValues\Standard();
$value->value = '192.0.2.1';
$record->value = $value;

// Save the record
$record->save();

// Get a list of all domains
$domains = $client->domains->paginate();
foreach ($domains as $domain) {
    print_r(json_encode($domain, JSON_PRETTY_PRINT));
}
```

### Adding multiple values to a record

Records can support multiple values for most record types. You can add these by either setting the values property to an array of values, or using the `addValue()` method.

```php
$domain = $client->domains->get(1234);
$record = $domain->records->create();
$record->type = \Constellix\Client\Enums\RecordType::A();
$record->name = 'www';

// Create some values
$newValue1 = new \Constellix\Client\Models\Helpers\RecordValues\Standard();
$newValue1->value = '192.0.2.1';

$newValue2 = new \Constellix\Client\Models\Helpers\RecordValues\Standard();
$newValue2->value = '192.0.2.2';

// Assign an array to the value property
$record->value = [
    $newValue1,
    $newValue2,
];

// Add a new value using the addValue() method
$newValue3 = new \Constellix\Client\Models\Helpers\RecordValues\Standard();
$newValue3->value = '192.0.2.3';

$record->addValue($newValue3);

// Save the record to persist these changes
$record->save();
```

Some record types (A, AAAA, CNAME and ANAME) have different types of value you can use, including Failover, RoundRobinFailover and Pools. You can assign these values by creating the correct type of value object. For more details on all these classes, see the docs folder.

### Searching for a Domain

You can search for domains matching wildcards like `*example.com` or `mydomain.*` by passing the name to the filter parameter on the domains pagination method, eg.

```php
$client->domains->paginate(filters: ['name' => '*example.com']);
```

This will return only domains matching your filter.

The domain objects retrieved are lazy loaded, so only the ID and name are fetched. If you attempt to access any other properties of the domain then it will fetch the full domain object from the API.

## Contributing

If you want to contribute to this SDK, we are open to pull requests. We use the following standards:

 - PHP 8.0 - We will continue supporting 8.0 until at least 26 Nov 2023
 - PSR12 Coding Standards - enforced using PHP CodeSniffer
 - Unit Tests - 100% Test Coverage using PHPUnit
 - Static Analysis - Level 8 PHPStan
 - Documentation - PHP Documentor

All pull requests must meet these requirements. 

## License

The MIT License (MIT)

Copyright (c) 2020 Constellix, a subsidiary of Tiggee LLC.

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.