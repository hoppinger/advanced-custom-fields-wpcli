<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given a WP install
     */
    public function aWpInstall()
    {
        throw new PendingException();
    }

    /**
     * @When I run :arg1
     */
    public function iRun($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then STDOUT should contain:
     */
    public function stdoutShouldContain(PyStringNode $string)
    {
        throw new PendingException();
    }
}
