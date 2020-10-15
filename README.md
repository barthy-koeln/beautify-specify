# beautify-specify
A simple wrapper to add some color and information to the phpunit/specify console output.

## Usage

Add the following to your `phpunit.xml`:

```xml
<phpunit
  printerClass="Barthy\BeautifySpecify\ResultPrinter"
/>
```

For each test case, replace any `Specify` trait with the trait provided by this library:

```php
use App\Entity\Client;
use BarthyKoeln\BeautifySpecify\Specify;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
  
  use Specify;

  // […]
}
```

Then, use the Specify framework as always and as [described in the documentation](https://github.com/Codeception/Specify).
