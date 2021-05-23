<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Aresults Model
 *
 * @method \App\Model\Entity\Aresult get($primaryKey, $options = [])
 * @method \App\Model\Entity\Aresult newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Aresult[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Aresult|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Aresult saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Aresult patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Aresult[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Aresult findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AresultsTable extends Table
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

        $this->setTable('aresults');
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
            ->integer('rid')
            ->allowEmptyString('rid');

        $validator
            ->integer('qid')
            ->allowEmptyString('qid');

        $validator
            ->integer('correct')
            ->requirePresence('correct', 'create')
            ->allowEmptyString('correct', false);

        $validator
            ->integer('answer')
            ->requirePresence('answer', 'create')
            ->allowEmptyString('answer', false);

        $validator
            ->integer('iscorrect')
            ->requirePresence('iscorrect', 'create')
            ->allowEmptyString('iscorrect', false);

        $validator
            ->dateTime('starttime')
            ->requirePresence('starttime', 'create')
            ->allowEmptyDateTime('starttime', false);

        $validator
            ->dateTime('endtime')
            ->requirePresence('endtime', 'create')
            ->allowEmptyDateTime('endtime', false);

        $validator
            ->integer('thinktime')
            ->requirePresence('thinktime', 'create')
            ->allowEmptyString('thinktime', false);

        $validator
            ->integer('valid')
            ->requirePresence('valid', 'create')
            ->allowEmptyString('valid', false);

        return $validator;
    }
}
