<?php

namespace BarthyKoeln\BeautifySpecify;

use Closure;
use Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\PHPTAssertionFailedError;
use PHPUnit\Framework\TestFailure;
use PHPUnit\Framework\TestResult;
use PHPUnit\Util\Printer;
use ReflectionClass;

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
        $this->write("\n\n{$this->bold}{$this->underline}{$this->blue}{$specification}:{$this->reset}\n");
        $this->baseDescribe($specification, $callable);
    }

    public function getPrinter(): Printer
    {
        if (null === $this->printer) {
            $this->printer = new Printer();
        }

        return $this->printer;
    }

    private function write(string $buffer): void
    {
        $this->getPrinter()->write($buffer);
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

        if (!$testResult instanceof TestResult) {
            return;
        }

        if (strlen($this->specifyName) !== 0) {
            try {
                $this->handleSpecifyCase($testResult, $specification, $callable, $params);
            } catch (Exception $_) {
                return;
            }
        }

        $this->baseSpecify($specification, $callable, $params);
    }

    /**
     * @throws \Exception
     */
    private function handleSpecifyCase(TestResult $testResult, $specification, ?Closure $callable, array $params): void
    {
        $failureCountBefore = $testResult->failureCount();
        $this->baseSpecify($specification, $callable, $params);
        $failureCountAfter = $testResult->failureCount();

        $failure = $this->printAndGetResult($testResult, $failureCountAfter, $failureCountBefore);

        $this->write(" … {$specification}\n");

        if (!$failure instanceof TestFailure) {
            return;
        }

        $exception = $failure->thrownException();
        $this->write("    ↳ {$this->blue}[REASON]{$this->reset} {$exception->getMessage()}\n");

        if ($exception instanceof ExpectationFailedException && $exception->getComparisonFailure()) {
            $this->write($exception->getComparisonFailure()->getDiff());
        }

        if ($exception instanceof PHPTAssertionFailedError) {
            $this->write($exception->getDiff());
        }

        $reflection    = new ReflectionClass(get_class());
        $classFileName = $reflection->getFileName();
        $trace         = $exception->getTrace();

        foreach ($trace as $crumb) {
            if ($crumb['file'] !== $classFileName) {
                continue;
            }

            $this->write(
                "    ↳ {$this->blue}[LOCATION]{$this->reset} {$crumb['file']} at line {$crumb['line']}\n"
            );

            throw new Exception();
        }
    }

    private function printAndGetResult(
        TestResult $testResult,
        int $failureCountAfter,
        int $failureCountBefore
    ): ?TestFailure {
        if ($failureCountAfter > $failureCountBefore) {
            $this->write("  {$this->red}[FAILED]{$this->reset}");

            return $testResult->failures()[$testResult->failureCount() - 1];
        }

        $this->write("  {$this->green}[PASSED]{$this->reset}");

        return null;
    }
}
