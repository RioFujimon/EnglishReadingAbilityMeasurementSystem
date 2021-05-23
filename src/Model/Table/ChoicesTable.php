<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Choices Model
 *
 * @method \App\Model\Entity\Choice get($primaryKey, $options = [])
 * @method \App\Model\Entity\Choice newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Choice[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Choice|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Choice saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Choice patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Choice[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Choice findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ChoicesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('choices');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', 'create');

        $validator
            ->integer('qid')
            ->requirePresence('qid', 'create')
            ->allowEmptyString('qid', false);

        $validator
            ->integer('subseq')
            ->allowEmptyString('subseq');

        $validator
            ->scalar('text')
            ->maxLength('text', 1024)
            ->requirePresence('text', 'create')
            ->allowEmptyString('text', false);

        $validator
            ->integer('correct')
            ->requirePresence('correct', 'create')
            ->allowEmptyString('correct', false);

        return $validator;
    }
}
