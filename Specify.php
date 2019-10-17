<?php
/**
 * Created by PhpStorm.
 * User: bbonhomme
 * Date: 12.07.18
 * Time: 12:17
 */

namespace App\Tests;


use PHPUnit\Framework\TestResult;
use PHPUnit\Util\Printer;

trait Specify
{

    private $reset = "\e[0m";
    private $red   = "\e[0;31m";
    private $green = "\e[0;32m";
    private $blue  = "\e[0;34m";

    /**
     * @var \PHPUnit\Util\Printer
     */
    private $printer;

    use \Codeception\Specify {
        \Codeception\Specify::describe as baseDescribe;
        \Codeception\Specify::specify as baseSpecify;
    }


    public function describe($specification, \Closure $callable = null)
    {
        $this->getPrinter()->write("\n\n$this->blue$specification:$this->reset\n");
        $this->baseDescribe($specification, $callable);
    }

    public function specify($specification, \Closure $callable = null, $params = [])
    {
        if (null === $callable) {
            return;
        }

        /**
         * @var TestResult $testResult
         */
        $testResult = $this->getTestResultObject();

        if (strlen($this->specifyName) !== 0) {

            $failureCount = $testResult->failureCount();

            $this->baseSpecify($specification, $callable, $params);

            $this->getPrinter()->write("-> ");

            $testResult = $this->getTestResultObject();

            if ($testResult->failureCount() > $failureCount) {
                $this->getPrinter()->write("$this->red[FAILED]$this->reset");
            } else {
                $this->getPrinter()->write("$this->green[PASSED]$this->reset");
            }

            $this->getPrinter()->write(" it $specification\n");
        } else {
            $this->baseSpecify($specification, $callable, $params);
        }
    }

    /**
     * @return \PHPUnit\Util\Printer
     */
    public function getPrinter(): Printer
    {
        if (null === $this->printer) {
            $this->printer = new Printer();
        }

        return $this->printer;
    }
}