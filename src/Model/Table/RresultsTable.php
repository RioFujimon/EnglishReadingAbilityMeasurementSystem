<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Rresults Model
 *
 * @method \App\Model\Entity\Rresult get($primaryKey, $options = [])
 * @method \App\Model\Entity\Rresult newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Rresult[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Rresult|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Rresult saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Rresult patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Rresult[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Rresult findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RresultsTable extends Table
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

        $this->setTable('rresults');
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
            ->requirePresence('rid', 'create')
            ->allowEmptyString('rid', false);

        $validator
            ->integer('sid')
            ->requirePresence('sid', 'create')
            ->allowEmptyString('sid', false);

        $validator
            ->dateTime('starttime')
            ->requirePresence('starttime', 'create')
            ->allowEmptyDateTime('starttime', false);

        $validator
            ->dateTime('endtime')
            ->requirePresence('endtime', 'create')
            ->allowEmptyDateTime('endtime', false);

        $validator
            ->integer('readtime')
            ->requirePresence('readtime', 'create')
            ->allowEmptyString('readtime', false);

        $validator
            ->integer('valid')
            ->requirePresence('valid', 'create')
            ->allowEmptyString('valid', false);

        return $validator;
    }
}
