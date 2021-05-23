<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Esets Model
 *
 * @method \App\Model\Entity\Eset get($primaryKey, $options = [])
 * @method \App\Model\Entity\Eset newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Eset[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Eset|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Eset saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Eset patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Eset[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Eset findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class EsetsTable extends Table
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

        $this->setTable('esets');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->hasMany('sections')
             ->setForeignKey('eid')
             ->setDependent(true)
             ->setCascadeCallbacks(true);
        
        $this->hasMany('rsets')
             ->setForeignKey('eid')
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
            ->scalar('title')
            ->maxLength('title', 256)
            ->requirePresence('title', 'create')
            ->allowEmptyString('title', false);

        $validator
            ->scalar('property')
            ->maxLength('property', 4096)
            ->requirePresence('property', 'create')
            ->allowEmptyString('property', false);

        $validator
            ->integer('mode')
            ->requirePresence('mode', 'create')
            ->allowEmptyString('mode', false);

        return $validator;
    }
}
