<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Choice Entity
 *
 * @property int $id
 * @property int $qid
 * @property int|null $subseq
 * @property string $text
 * @property int $correct
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property \Cake\I18n\FrozenTime|null $created
 */
class Choice extends Entity
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
        'qid' => true,
        'subseq' => true,
        'text' => true,
        'correct' => true,
        'modified' => true,
        'created' => true
    ];
}
