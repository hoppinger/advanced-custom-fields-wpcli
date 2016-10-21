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
    private $result = [];

    private $importsPath = '';
    private $exportsPath = '';

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
      parent::__construct();

      $this->importsPath = dirname(__FILE__).'/test_imports/';
      $this->exportsPath = dirname(__FILE__).'/test_exports/';
    }

    /**
     * @Given a WP install
     */
    public function aWpInstall()
    {
        $this->result = $this->run("core is-installed");
    }

    /**
     * @When I run the command :arg1
     */
    public function iRunTheCommand($command)
    {
        $this->result = $this->run($command);
    }

    /**
     * @Then the exit code should be :arg1
     */
    public function theExitCodeShouldBe($expectedExitCode)
    {
        PHPUnit_Framework_Assert::assertEquals($expectedExitCode, (int) $this->result['exitCode'], "Exit code does not match expected");
    }

    /**
     * @Then the result should not be empty
     */
    public function theResultShouldNotBeEmpty()
    {
        PHPUnit_Framework_Assert::assertNotEmpty($this->result['output']);
    }

    /**
     * @Then the exported file should match the original import file
     */
    public function theExportedFileShouldMatchTheOriginalImportFile()
    {
        throw new PendingException();
    }

    /**
     * @Then the result string should start with :arg1
     */
    public function theResultStringShouldStartWith($expectedStart)
    {
        PHPUnit_Framework_Assert::assertStringStartsWith($expectedStart, $this->result['output_string']);
    }

    /**
     * @Then the imported and exported :arg1 files should match
     */
    public function theImportedAndExportedFilesShouldMatch($arg1)
    {
      $original = json_decode(file_get_contents($this->importsPath.$arg1), true);
      $export   = json_decode(file_get_contents($this->exportsPath.$arg1), true);

      PHPUnit_Framework_Assert::assertTrue(($original === $export), "Original and export do not match" );
    }
}
