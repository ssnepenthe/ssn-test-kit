# ssn-test-kit
Utilities for working with BrowserKit and WP-CLI within my PHPUnit tests.

This repo is just one big experiment... It is designed to meet my needs/wants on the testing front.

You probably don't want to use this.

Instead, look to more established testing tools like:

* Plain ol' PHPUnit + WordPress Unit Test Suite
* Codeception + WP-Browser
* Behat + WordHat
* Etc.

## Considerations
There are loads - here are some of the most notable:

```php
// Even though all response objects created by a given browser instance are unique, the underlying state (client) may be the same.
$browser = new Browser();

$response1 = $browser->get('https://www.google.com');
$response2 = $browser->get('https://www.bing.com');

echo $response1->crawler()->filter('title')->text(); // "Bing"
echo $response2->crawler()->filter('title')->text(); // "Bing"

var_dump($response1 == $response2); // bool(true)
var_dump($response1 === $response2); // bool(false)

// In other words, this is bad:
$response1 = $browser->get('https://www.google.com');
$response2 = $browser->get('https://www.bing.com');

$response1->assertSee('Google'); // Throws an assertion exception.

// But this is alright:
$browser->get('https://www.google.com')->assertSee('Google');
$browser->get('https://www.bing.com')->assertSee('Bing');

// The exception is when toggling between panther and goutte:
$response1 = $browser->get('https://www.google.com');
$response2 = $browser->enableJavascript()->get('https://www.bing.com');

var_dump($response1 == $response2); // bool(false)
var_dump($response1 === $response2); // bool(false)
```
