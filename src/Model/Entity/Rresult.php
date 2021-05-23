<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Rresult Entity
 *
 * @property int $id
 * @property int $rid
 * @property int $sid
 * @property \Cake\I18n\FrozenTime $starttime
 * @property \Cake\I18n\FrozenTime $endtime
 * @property int $readtime
 * @property int $valid
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property \Cake\I18n\FrozenTime|null $created
 */
class Rresult extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'rid' => true,
        'sid' => true,
        'starttime' => true,
        'endtime' => true,
        'readtime' => true,
        'valid' => true,
        'modified' => true,
        'created' => true
    ];
}
