# beautify-specify
A simple wrapper to add some color and information to the phpunit/specify console output.

## Usage

Add the following to your `phpunit.xml`:

```xml
<phpunit
  printerClass="App\Tests\ResultPrinter"
/>
```

For each test case, replace any `Specify` trait with the `BeautifySpecify` trait:

```php
use App\Entity\Client;
use App\Tests\Specify;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
  
  use Specify;

  // […]
}
```

Then, use the Specify framework as always and as [described in the documentation](https://github.com/Codeception/Specify).
