<?php

namespace LemoBase\Validator;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;

class DateFormat extends AbstractValidator
{
    const INVALID_FORMAT = 'invalidFormat';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::INVALID_FORMAT => "Date '%value%' doesn`t match format '%format%'",
    ];

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $messageVariables = [
        'format' => 'format',
    ];

    /**
     * Date firnat
     *
     * @var mixed
     */
    protected $format;

    /**
     * Sets validator options
     *
     * @param  array|Traversable $options
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($options = null)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }
        if (!is_array($options)) {
            $options = func_get_args();
            $temp['format'] = array_shift($options);

            $options = $temp;
        }

        if (!array_key_exists('format', $options)) {
            throw new Exception\InvalidArgumentException("Missing option 'format'");
        }

        $this->setFormat($options['format']);

        parent::__construct($options);
    }

    /**
     * Set format
     *
     * @param  mixed $forma
     * @return $this
     */
    public function setFormat($forma)
    {
        $this->format = $forma;
        return $this;
    }

    /**
     * Returns format
     *
     * @return mixed
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Returns true if and only if $value is greater than max option, inclusively
     * when the inclusive option is true
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $value = preg_replace('/(^[0])/', '', $value);
        $value = str_replace('.0', '.', $value);

        $formated = date($this->getFormat(), strtotime($value));
        $formated = preg_replace('/(^[0])/', '', $formated);
        $formated = str_replace('.0', '.', $formated);

        if ($value != $formated) {
            $this->error(self::INVALID_FORMAT);
            return false;
        }

        return true;
    }
}
