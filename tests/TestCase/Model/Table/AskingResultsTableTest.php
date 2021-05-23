<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AskingResultsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AskingResultsTable Test Case
 */
class AskingResultsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\AskingResultsTable
     */
    public $AskingResults;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.AskingResults'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('AskingResults') ? [] : ['className' => AskingResultsTable::class];
        $this->AskingResults = TableRegistry::getTableLocator()->get('AskingResults', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->AskingResults);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
