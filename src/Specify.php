<?php

namespace BarthyKoeln\BeautifySpecify;

use Closure;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\PHPTAssertionFailedError;
use PHPUnit\Framework\TestFailure;
use PHPUnit\Framework\TestResult;
use PHPUnit\Util\Printer;
use ReflectionClass;
use ReflectionException;

trait Specify
{

    private string $reset = "\e[0m";
    private string $red = "\e[31m";
    private string $green = "\e[32m";
    private string $blue = "\e[34m";
    private string $bold = "\e[1m";
    private string $underline = "\e[4m";

    private ?Printer $printer = null;

    use \Codeception\Specify {
        \Codeception\Specify::describe as baseDescribe;
        \Codeception\Specify::runSpec as baseSpecify;
    }


    public function describe($specification, Closure $callable = null): void
    {
        $this->getPrinter()->write("\n\n{$this->bold}{$this->underline}{$this->blue}{$specification}:{$this->reset}\n");
        $this->baseDescribe($specification, $callable);
    }

    public function getPrinter(): Printer
    {
        if (null === $this->printer) {
            $this->printer = new Printer();
        }

        return $this->printer;
    }

    public function runSpec($specification, Closure $callable = null, $params = []): void
    {
        if (null === $callable) {
            return;
        }

        if (!method_exists($this, 'getTestResultObject')) {
            return;
        }

        $testResult = $this->getTestResultObject();

        if (!($testResult instanceof TestResult)) {
            return;
        }

        if (strlen($this->specifyName) !== 0) {
            $failureCount = $testResult->failureCount();

            $this->baseSpecify($specification, $callable, $params);

            if ($testResult->failureCount() > $failureCount) {
                $this->getPrinter()->write("  {$this->red}[FAILED]{$this->reset}");
                $failure = $testResult->failures()[$testResult->failureCount() - 1];

            } else {
                $this->getPrinter()->write("  {$this->green}[PASSED]{$this->reset}");
                $failure = null;
            }

            $this->getPrinter()->write(" … {$specification}\n");

            if ($failure instanceof TestFailure) {
                $exception = $failure->thrownException();
                $this->getPrinter()->write("    ↳ {$this->blue}[REASON]{$this->reset} {$exception->getMessage()}\n");

                if ($exception instanceof ExpectationFailedException && $exception->getComparisonFailure()) {
                    $this->getPrinter()->write($exception->getComparisonFailure()->getDiff());
                }

                if ($exception instanceof PHPTAssertionFailedError) {
                    $this->getPrinter()->write($exception->getDiff());
                }

                try {
                    $reflection = new ReflectionClass(get_class());
                    $classFileName = $reflection->getFileName();
                    $trace = $exception->getTrace();
                    foreach($trace as $crumb){
                        if($crumb['file'] === $classFileName){
                            $this->getPrinter()->write("    ↳ {$this->blue}[LOCATION]{$this->reset} {$crumb['file']} at line {$crumb['line']}\n");
                            return;
                        }
                    }
                } catch (ReflectionException $e) {
                    return;
                }
            }

            return;
        }

        $this->baseSpecify($specification, $callable, $params);
    }
}
