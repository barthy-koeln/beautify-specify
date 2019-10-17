<?php

namespace App\Tests;

class ResultPrinter extends \PHPUnit\TextUI\ResultPrinter
{

    /**
     * @param string $progress
     */
    protected function writeProgress(string $progress): void
    {
        return;
    }
}