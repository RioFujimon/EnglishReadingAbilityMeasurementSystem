<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Section Entity
 *
 * @property int $id
 * @property int $eid
 * @property int $subseq
 * @property string $title
 * @property string $property
 * @property string $text
 * @property int $tlimit
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property \Cake\I18n\FrozenTime|null $created
 */
class Section extends Entity
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
        'eid' => true,
        'subseq' => true,
        'title' => true,
        'property' => true,
        'text' => true,
        'tlimit' => true,
        'modified' => true,
        'created' => true
    ];
}
