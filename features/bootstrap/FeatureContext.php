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

  private $filename = '';

  private $importResult = null;
  private $exportResult = null;

  private $cleanResult = null;

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
    $this->filename = $file;
    $path = $this->importsPath.$this->filename;

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
    if (empty($this->importResult['output'])) {
      var_dump($this->importResult);
    }
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

  /**
   * @Then the exported file should match the original import file
   */
  public function theExportedFileShouldMatchTheOriginalImportFile()
  {
    $original = json_decode(file_get_contents($this->importsPath.$this->filename), true);
    $export   = json_decode(file_get_contents($this->exportsPath.$this->filename), true);

    PHPUnit_Framework_Assert::assertTrue(($original === $export), "Original and export do not match" );
  }

  /**
   * @Given a database with custom fields
   */
  public function aDatabaseWithCustomFields()
  {
    $imports = ['file', 'text', 'radio'];

    foreach($imports as $import) {
      $path = $this->importsPath.$import.'-group.json';
      $this->run( "acf import --json_file='{$path}'" );
    }
  }

  /**
   * @When I run the wp-cli command clean
   */
  public function iRunTheWpCliCommandClean()
  {
    $this->cleanResult = $this->run('acf clean');
  }

  /**
   * @Then I expect the result code to be :arg1
   */
  public function iExpectTheResultCodeToBe($expected_code)
  {
    $this->testExitCode($this->cleanResult, $expected_code);
  }

  /**
   * @Then I expect the database to no longer contain ACF records
   */
  public function iExpectTheDatabaseToNoLongerContainAcfRecords()
  {
    $newResult = $this->run('acf clean');
    var_dump($newResult);
  }
}
