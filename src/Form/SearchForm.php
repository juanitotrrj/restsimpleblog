<?php
namespace App\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

/**
 * Search Form.
 */
class SearchForm extends Form
{
    private $_result;

    /**
     * Builds the schema for the modelless form
     *
     * @param \Cake\Form\Schema $schema From schema
     * @return \Cake\Form\Schema
     */
    protected function _buildSchema(Schema $schema)
    {
        return $schema
            ->addField('published_date', ['type' => 'string'])
            ->addField('search', ['type' => 'string'])
            ->addField('user', ['type' => 'integer']);
    }

    /**
     * Form validation builder
     *
     * @param \Cake\Validation\Validator $validator to use against the form
     * @return \Cake\Validation\Validator
     */
    protected function _buildValidator(Validator $validator)
    {
        return $validator
            ->allowEmpty('published_date')
            ->allowEmpty('search')
            ->allowEmpty('user')
            ->add('published_date', 'custom', [
                'rule' => function ($value, $context) {
                    return (bool)preg_match("/^(1[1-2]{1}|0[1-9]{1})\/[0-3]{1}[0-9]{1}\/\d{4}$/", $value);
                }
            ])
            ->add('user', 'custom', [
                'rule' => function ($value, $context) {
                    return (bool)preg_match("/^[0-9]+$/", $value);
                }
            ]);
    }

    /**
     * Defines what to execute once the From is being processed
     *
     * @param array $data Form data.
     * @return bool
     */
    protected function _execute(array $data)
    {
        return true;
    }
}
