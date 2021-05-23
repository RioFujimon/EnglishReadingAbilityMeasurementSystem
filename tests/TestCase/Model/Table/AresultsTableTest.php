<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AresultsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AresultsTable Test Case
 */
class AresultsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\AresultsTable
     */
    public $Aresults;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Aresults'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Aresults') ? [] : ['className' => AresultsTable::class];
        $this->Aresults = TableRegistry::getTableLocator()->get('Aresults', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Aresults);

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
