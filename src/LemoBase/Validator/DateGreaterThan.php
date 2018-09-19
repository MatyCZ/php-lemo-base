<?php

namespace LemoBase\Validator;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\Validator\AbstractValidator;

class DateGreaterThan extends AbstractValidator
{
    const NOT_GREATER           = 'notDateGreaterThan';
    const NOT_GREATER_INCLUSIVE = 'notDateGreaterThanInclusive';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::NOT_GREATER           => "The input is not greater than date '%min%'",
        self::NOT_GREATER_INCLUSIVE => "The input is not greater or equal than date '%min%'",
    ];

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $messageVariables = [
        'min' => 'min',
    ];

    /**
     * Maximum value as date or field name
     *
     * @var mixed
     */
    protected $min;

    /**
     * Whether to do inclusive comparisons, allowing equivalence to min
     *
     * If false, then strict comparisons are done, and the value may equal
     * the max option
     *
     * @var boolean
     */
    protected $inclusive;

    /**
     * Sets validator options
     *
     * @param  array|Traversable $options
     * @throws \Zend\Validator\Exception\InvalidArgumentException
     */
    public function __construct($options = null)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }
        if (!is_array($options)) {
            $options = func_get_args();
            $temp['min'] = array_shift($options);

            if (!empty($options)) {
                $temp['inclusive'] = array_shift($options);
            }

            $options = $temp;
        }

        if (!array_key_exists('min', $options)) {
            throw new \Zend\Validator\Exception\InvalidArgumentException("Missing option 'min'");
        }

        if (!array_key_exists('inclusive', $options)) {
            $options['inclusive'] = false;
        }

        $this->setMin($options['min'])
            ->setInclusive($options['inclusive']);

        parent::__construct($options);
    }

    /**
     * Returns the max option
     *
     * @return mixed
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * Sets the max option
     *
     * @param  mixed $min
     * @return GreaterThan Provides a fluent interface
     */
    public function setMin($min)
    {
        $this->min = $min;
        return $this;
    }

    /**
     * Returns the inclusive option
     *
     * @return boolean
     */
    public function getInclusive()
    {
        return $this->inclusive;
    }

    /**
     * Sets the inclusive option
     *
     * @param  boolean $inclusive
     * @return GreaterThan Provides a fluent interface
     */
    public function setInclusive($inclusive)
    {
        $this->inclusive = $inclusive;
        return $this;
    }

    /**
     * Returns true if and only if $value is greater than min option, inclusively
     * when the inclusive option is true
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->setValue($value);

        if ($this->inclusive) {
            if (strtotime($value) < strtotime($this->min)) {
                $this->error(self::NOT_GREATER_INCLUSIVE);
                return false;
            }
        } else {
            if (strtotime($value) <= strtotime($this->min)) {
                $this->error(self::NOT_GREATER);
                return false;
            }
        }

        return true;
    }
}
