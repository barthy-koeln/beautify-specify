<?php

namespace BarthyKoeln\BeautifySpecify;

use PHPUnit\Framework\TestResult;
use PHPUnit\TextUI\DefaultResultPrinter;

class ResultPrinter extends DefaultResultPrinter
{

    /**
     * @param string $progress
     */
    protected function writeProgress(string $progress): void
    {
        // Do nothing
    }

    protected function printFailures(TestResult $result): void
    {
        // Do nothing
    }
}
