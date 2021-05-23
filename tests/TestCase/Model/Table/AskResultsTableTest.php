<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AskResultsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AskResultsTable Test Case
 */
class AskResultsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\AskResultsTable
     */
    public $AskResults;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.AskResults'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('AskResults') ? [] : ['className' => AskResultsTable::class];
        $this->AskResults = TableRegistry::getTableLocator()->get('AskResults', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->AskResults);

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
