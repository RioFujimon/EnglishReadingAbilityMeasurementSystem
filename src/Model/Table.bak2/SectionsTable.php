<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Sections Model
 *
 * @method \App\Model\Entity\Section get($primaryKey, $options = [])
 * @method \App\Model\Entity\Section newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Section[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Section|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Section saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Section patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Section[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Section findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SectionsTable extends Table
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

        $this->setTable('sections');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->hasMany('questions')
             ->setForeignKey('sid')
             ->setDependent(true)
             ->setCascadeCallbacks(true);

        $this->hasMany('rresults')
             ->setForeignKey('sid')
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
            ->allowEmptyString('eid');

        $validator
            ->integer('subseq')
            ->requirePresence('subseq', 'create')
            ->allowEmptyString('subseq', false);

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
            ->scalar('text')
            ->maxLength('text', 16384)
            ->requirePresence('text', 'create')
            ->allowEmptyString('text', false);

        $validator
            ->integer('tlimit')
            ->requirePresence('tlimit', 'create')
            ->allowEmptyString('tlimit', false);

        return $validator;
    }
}
