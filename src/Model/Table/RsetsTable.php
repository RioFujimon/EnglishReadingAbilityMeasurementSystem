<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Rsets Model
 *
 * @method \App\Model\Entity\Rset get($primaryKey, $options = [])
 * @method \App\Model\Entity\Rset newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Rset[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Rset|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Rset saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Rset patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Rset[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Rset findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RsetsTable extends Table
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

        $this->setTable('rsets');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->hasMany('rresults')
             ->setForeignKey('rid')
             ->setDependent(true)
             ->setCascadeCallbacks(true);

        $this->hasMany('aresults')
             ->setForeignKey('rid')
             ->setDependent(true)
             ->setCascadeCallbacks(true);
        
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
            ->integer('eid')
            ->requirePresence('eid', 'create')
            ->allowEmptyString('eid', false);

        $validator
            ->integer('uid')
            ->requirePresence('uid', 'create')
            ->allowEmptyString('uid', false);

        $validator
            ->dateTime('starttime')
            ->requirePresence('starttime', 'create')
            ->allowEmptyDateTime('starttime', false);

        $validator
            ->dateTime('endtime')
            ->requirePresence('endtime', 'create')
            ->allowEmptyDateTime('endtime', false);

        $validator
            ->integer('valid')
            ->requirePresence('valid', 'create')
            ->allowEmptyString('valid', false);

        return $validator;
    }
}
