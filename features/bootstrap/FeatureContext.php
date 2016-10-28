<?php

require 'wordpress/wp-load.php';
//include('advanced-custom-fields-wpcli.php');

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;

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
        $this->result = $this->run('core is-installed');
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
        PHPUnit_Framework_Assert::assertEquals($expectedExitCode, (int) $this->result['exitCode'], 'Exit code does not match expected');
    }

    /**
     * @Then the result should not be empty
     */
    public function theResultShouldNotBeEmpty()
    {
        PHPUnit_Framework_Assert::assertNotEmpty($this->result['output']);
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
        $export = json_decode(file_get_contents($this->exportsPath.$arg1), true);

        PHPUnit_Framework_Assert::assertTrue(($original === $export), 'Original and export do not match');
    }

    /**
     * @Then acf fields in the database
     */
    public function acfFieldsInTheDatabase()
    {
        $fieldsToAdd = ['text', 'gallery', 'select'];

        foreach ($fieldsToAdd as $field) {
            $this->run("acf import --json_file='{$this->importsPath}{$field}-group.json'");
        }
    }

    /**
     * @Then the result should be empty
     */
    public function theResultShouldBeEmpty()
    {
        PHPUnit_Framework_Assert::assertEmpty($this->result['output']);
    }

    /**
     * @Given a :arg1 feature
     */
    public function aFeature($feature)
    {
        $this->run('acf clean');
        $this->run("acf import --json_file='{$this->importsPath}{$feature}-group.json'");
    }

    /**
     * @When I remove the fields from :arg1
     */
    public function iRemoveTheFieldsFrom($file)
    {
        $jsonString = file_get_contents($this->exportsPath.$file);
        $arr = json_decode($jsonString, true);

        $arr[0]['fields'] = [];

        $fp = fopen($this->exportsPath.$file, 'w');
        fwrite($fp, json_encode($arr));
        fclose($fp);
    }

    /**
     * @Then the :arg1 should not have been added to the local groups
     */
    public function theShouldNotHaveBeenAddedToTheLocalGroups($group)
    {
        $acfwpcli = new ACFWPCLI();
        $acfwpcli->add_runtime_fieldgroups();

        $added_groups = $acfwpcli->get_added_groups();

        PHPUnit_Framework_Assert::assertNotContains($group, $added_groups);
    }

    /**
     * @When I run the command :arg1 and answer :arg2
     */
    public function iRunTheCommandAndAnswer($command, $answer)
    {
        $this->result = $this->run($command, $answer);
    }
}
