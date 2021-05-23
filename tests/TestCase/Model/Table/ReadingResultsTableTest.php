<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ReadingResultsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ReadingResultsTable Test Case
 */
class ReadingResultsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ReadingResultsTable
     */
    public $ReadingResults;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.ReadingResults'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('ReadingResults') ? [] : ['className' => ReadingResultsTable::class];
        $this->ReadingResults = TableRegistry::getTableLocator()->get('ReadingResults', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ReadingResults);

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
