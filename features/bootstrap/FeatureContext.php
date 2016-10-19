<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends CommandFeature implements Context
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
        $result = $this->run("core is-installed");
        $this->testResultCode($result);
    }

    private function testResultCode($result, $expected = 0) {
      PHPUnit_Framework_Assert::assertEquals($expected, (int) $result['exitCode'], "Exit code does not match expected");
    }

    /**
     * @When I run the wp-cli command :arg1
     */
    public function iRun($command)
    {
      $result = $this->run($command);

      var_dump($result); 
    }

    /**
     * @Then STDOUT should contain:
     */
    public function stdoutShouldContain(PyStringNode $string)
    {
        throw new PendingException();
    }
}
