<?php

namespace LemoBase\Form;

use Zend\Form\FormInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Form extends \Zend\Form\Form
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * Set service manager instance
     *
     * @param ServiceManager $locator
     * @return void
     */
    public function setServiceManager(ServiceLocatorInterface $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * @see Zend\Form\Form::$data
     * @return Form
     */
    public function setData($data)
    {
        parent::setData($data);

        $this->populateOptions();

        return $this;
    }

    /**
     * Bind an object to the form
     *
     * Ensures the object is populated with validated values.
     *
     * @param  object $object
     * @return void
     */
    public function bind($object, $flags = FormInterface::VALUES_NORMALIZED)
    {
        parent::bind($object, $flags);

        $this->populateOptions();

        return $this;
    }

    /**
     * Set the object used by the hydrator
     *
     * @param  object $object
     * @return Form
     */
    public function setObject($object)
    {
        parent::setObject($object);

        $this->populateOptions();

        return $this;
    }

    /**
     * Populate select with options
     *
     * @abstract
     * @return Form
     */
    public function populateOptions()
    {

    }

    /**
     * Validate field via ajax request
     *
     * @param  string $fieldName
     * @param  string $fieldValue
     * @return array
     */
    public function isValidAjax($fieldName, $fieldValue)
    {
        $inputFilter = $this->getInputFilter();
        $valid = true;
        $messages = null;

        if($inputFilter->has($fieldName)) {
            $inputFilter->get($fieldName)->setValue($fieldValue);

            if('select' == $this->get($fieldName)->getAttribute('type')) {
                $validatorChain = new \Zend\Validator\ValidatorChain();
                foreach($inputFilter->get($fieldName)->getValidatorChain()->getValidators() as $validator) {
                    if($validator instanceof \Zend\Validator\InArray) {
                        $validatorChain->addValidator($validator);
                    }
                }
                $inputFilter->get($fieldName)->setValidatorChain($validatorChain);
            }

            if(!$inputFilter->get($fieldName)->isValid()) {
                $mes = $inputFilter->get($fieldName)->getMessages();
                if(!isset($mes['notSame']) || isset($mes['notSame']) && count($mes) > 1) {
                    $valid = false;
                    $messages = array_values($inputFilter->get($fieldName)->getMessages());
                }
            }
        }

        return array(
            'v' => $valid,
            'm' => $messages,
        );
    }
}
