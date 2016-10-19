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
  private $importsPath = '';
  private $exportsPath = '';

  private $importResult = null;
  private $exportResult = null;

  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct()
  {
    $this->importsPath = dirname(__FILE__).'/test_imports/';
    $this->exportsPath = dirname(__FILE__).'/test_exports/';
  }

  private function testExitCode($result, $expected = 0) {
    PHPUnit_Framework_Assert::assertEquals($expected, (int) $result['exitCode'], "Exit code does not match expected");
  }

  /**
   * @Given a WP install
   */
  public function aWpInstall()
  {
    $result = $this->run("core is-installed");
    $this->testExitCode($result);
  }

  /**
   * @When I run the wp-cli command to import the file :arg1
   */
  public function iRun($file)
  {
    $path = $this->importsPath.$file;

    $this->importResult = $this->run( "acf import --json_file='{$path}'" );
  }

  /**
   * @Then the import result code should be :arg1
   */
  public function theImportResultCodeShouldBe($expected_code)
  {
    $this->testExitCode($this->importResult, $expected_code);
  }

  /**
   * @Then the import result should not be empty
   */
  public function theImportResultShouldNotBeEmpty()
  {
    PHPUnit_Framework_Assert::assertNotEmpty($this->importResult['output']);
  }

  /**
   * @Then the import result string should start with :arg1
   */
  public function theImportResultStringShouldStartWith($expected_start)
  {
    PHPUnit_Framework_Assert::assertStringStartsWith($expected_start, $this->importResult['output_string']);
  }

  /**
   * @When I run the wp-cli command to export custom field :arg1
   */
  public function iRunTheWpCliCommandToExportCustomField($customField)
  {
    $this->exportResult = $this->run("acf export --field_group='{$customField}' --export_path='{$this->exportsPath}'");

    var_dump($this->exportResult);
  }

  /**
   * @Then the export result code should be :arg1
   */
  public function theExportResultCodeShouldBe($expected_code)
  {
    $this->testExitCode($this->exportResult, $expected_code);
  }

    /**
     * @Then the export result should not be empty
     */
    public function theExportResultShouldNotBeEmpty()
    {
        PHPUnit_Framework_Assert::assertNotEmpty($this->exportResult['output']);
    }

    /**
     * @Then the export result string should start with :arg1
     */
    public function theExportResultStringShouldStartWith($expected_start)
    {
      PHPUnit_Framework_Assert::assertStringStartsWith($expected_start, $this->exportResult['output_string']);
    }
}
