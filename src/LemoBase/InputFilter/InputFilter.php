<?php

namespace LemoBase\InputFilter;

use Laminas\InputFilter\BaseInputFilter;

class InputFilter extends \Laminas\InputFilter\InputFilter
{
    /**
     * @inheritdoc
     */
    public function setData($data)
    {
        foreach ($this->getInputs() as $inputName => $input) {
            if ($input instanceof BaseInputFilter && isset($data[$inputName])) {
                $input->setData($data[$inputName]);
            }
        }

        if (null === $data) {
            $data = [];
        }

        return parent::setData($data);
    }
}