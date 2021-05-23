<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Eset Entity
 *
 * @property int $id
 * @property string $title
 * @property string $property
 * @property int $mode
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property \Cake\I18n\FrozenTime|null $created
 */
class Eset extends Entity
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
        'title' => true,
        'property' => true,
        'mode' => true,
        'modified' => true,
        'created' => true
    ];
}
