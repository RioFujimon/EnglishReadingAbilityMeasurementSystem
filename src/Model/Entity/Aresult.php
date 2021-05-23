<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Aresult Entity
 *
 * @property int $id
 * @property int $rid
 * @property int $qid
 * @property int $correct
 * @property int $answer
 * @property int $iscorrect
 * @property \Cake\I18n\FrozenTime $starttime
 * @property \Cake\I18n\FrozenTime $endtime
 * @property int $thinktime
 * @property int $valid
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property \Cake\I18n\FrozenTime|null $created
 */
class Aresult extends Entity
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
        'qid' => true,
        'correct' => true,
        'answer' => true,
        'iscorrect' => true,
        'starttime' => true,
        'endtime' => true,
        'thinktime' => true,
        'valid' => true,
        'modified' => true,
        'created' => true
    ];
}
