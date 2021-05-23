<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\RresultsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\RresultsTable Test Case
 */
class RresultsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\RresultsTable
     */
    public $Rresults;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Rresults'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Rresults') ? [] : ['className' => RresultsTable::class];
        $this->Rresults = TableRegistry::getTableLocator()->get('Rresults', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Rresults);

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
