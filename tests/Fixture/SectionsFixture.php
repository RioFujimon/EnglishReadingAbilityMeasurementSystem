<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SectionsFixture
 */
class SectionsFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 10, 'autoIncrement' => true, 'default' => null, 'null' => false, 'comment' => null, 'precision' => null, 'unsigned' => null],
        'eid' => ['type' => 'integer', 'length' => 10, 'default' => null, 'null' => false, 'comment' => null, 'precision' => null, 'unsigned' => null, 'autoIncrement' => null],
        'subseq' => ['type' => 'integer', 'length' => 10, 'default' => '1', 'null' => false, 'comment' => null, 'precision' => null, 'unsigned' => null, 'autoIncrement' => null],
        'title' => ['type' => 'string', 'length' => 256, 'default' => '', 'null' => false, 'collate' => null, 'comment' => null, 'precision' => null, 'fixed' => null],
        'property' => ['type' => 'string', 'length' => 4096, 'default' => '', 'null' => false, 'collate' => null, 'comment' => null, 'precision' => null, 'fixed' => null],
        'text' => ['type' => 'string', 'length' => 16384, 'default' => '', 'null' => false, 'collate' => null, 'comment' => null, 'precision' => null, 'fixed' => null],
        'tlimit' => ['type' => 'integer', 'length' => 10, 'default' => '10', 'null' => false, 'comment' => null, 'precision' => null, 'unsigned' => null, 'autoIncrement' => null],
        'modified' => ['type' => 'timestamp', 'length' => null, 'default' => null, 'null' => true, 'comment' => null, 'precision' => null],
        'created' => ['type' => 'timestamp', 'length' => null, 'default' => null, 'null' => true, 'comment' => null, 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'sections_eid_fkey' => ['type' => 'foreign', 'columns' => ['eid'], 'references' => ['esets', 'id'], 'update' => 'noAction', 'delete' => 'noAction', 'length' => []],
        ],
    ];
    // @codingStandardsIgnoreEnd
    /**
     * Init method
     *
     * @return void
     */
    public function init()
    {
        $this->records = [
            [
                'id' => 1,
                'eid' => 1,
                'subseq' => 1,
                'title' => 'Lorem ipsum dolor sit amet',
                'property' => 'Lorem ipsum dolor sit amet',
                'text' => 'Lorem ipsum dolor sit amet',
                'tlimit' => 1,
                'modified' => 1557327389,
                'created' => 1557327389
            ],
        ];
        parent::init();
    }
}
