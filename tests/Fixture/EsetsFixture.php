<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * EsetsFixture
 */
class EsetsFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 10, 'autoIncrement' => true, 'default' => null, 'null' => false, 'comment' => null, 'precision' => null, 'unsigned' => null],
        'title' => ['type' => 'string', 'length' => 256, 'default' => '', 'null' => false, 'collate' => null, 'comment' => null, 'precision' => null, 'fixed' => null],
        'property' => ['type' => 'string', 'length' => 4096, 'default' => '', 'null' => false, 'collate' => null, 'comment' => null, 'precision' => null, 'fixed' => null],
        'mode' => ['type' => 'integer', 'length' => 10, 'default' => '0', 'null' => false, 'comment' => null, 'precision' => null, 'unsigned' => null, 'autoIncrement' => null],
        'modified' => ['type' => 'timestamp', 'length' => null, 'default' => null, 'null' => true, 'comment' => null, 'precision' => null],
        'created' => ['type' => 'timestamp', 'length' => null, 'default' => null, 'null' => true, 'comment' => null, 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
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
                'title' => 'Lorem ipsum dolor sit amet',
                'property' => 'Lorem ipsum dolor sit amet',
                'mode' => 1,
                'modified' => 1557327291,
                'created' => 1557327291
            ],
        ];
        parent::init();
    }
}
