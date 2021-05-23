<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\EsetsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\EsetsTable Test Case
 */
class EsetsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\EsetsTable
     */
    public $Esets;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Esets'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Esets') ? [] : ['className' => EsetsTable::class];
        $this->Esets = TableRegistry::getTableLocator()->get('Esets', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Esets);

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
