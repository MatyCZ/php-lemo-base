<?php

namespace LemoBase\Validator;

use Laminas\Stdlib\ArrayUtils;
use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Exception;
use Traversable;

class DateLessThan extends AbstractValidator
{
    public const NOT_LESS           = 'notDateLessThan';
    public const NOT_LESS_INCLUSIVE = 'notDateLessThanInclusive';

    protected array $messageTemplates = [
        self::NOT_LESS           => "The input is not less than date '%max%'",
        self::NOT_LESS_INCLUSIVE => "The input is not less or equal than date '%max%'",
    ];

    protected array $messageVariables = [
        'max' => 'max',
    ];

    /**
     * Maximum value as date or field name
     *
     * @var string
     */
    protected string $max;

    /**
     * Whether to do inclusive comparisons, allowing equivalence to max
     *
     * If false, then strict comparisons are done, and the value may equal
     * the max option
     *
     * @var bool
     */
    protected bool $inclusive = false;

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
            $temp['max'] = array_shift($options);

            if (!empty($options)) {
                $temp['inclusive'] = array_shift($options);
            }

            $options = $temp;
        }

        if (!array_key_exists('max', $options)) {
            throw new Exception\InvalidArgumentException("Missing option 'max'");
        }

        if (!array_key_exists('inclusive', $options)) {
            $options['inclusive'] = false;
        }

        $this->setMax($options['max'])
            ->setInclusive($options['inclusive']);

        parent::__construct($options);
    }

    /**
     * Sets the max option
     *
     * @param  string $max
     * @return self
     */
    public function setMax(string $max): self
    {
        $this->max = $max;
        return $this;
    }

    /**
     * Returns the max option
     *
     * @return string
     */
    public function getMax(): string
    {
        return $this->max;
    }

    /**
     * Sets the inclusive option
     *
     * @param  bool $inclusive
     * @return self
     */
    public function setInclusive(bool $inclusive): self
    {
        $this->inclusive = $inclusive;
        return $this;
    }

    /**
     * Returns the inclusive option
     *
     * @return bool
     */
    public function getInclusive(): bool
    {
        return $this->inclusive;
    }

    /**
     * Returns true if and only if $value is less than max option, inclusively
     * when the inclusive option is true
     *
     * @param  mixed $value
     * @return bool
     */
    public function isValid($value): bool
    {
        $this->setValue($value);

        if ($this->inclusive) {
            if (strtotime($value) > strtotime($this->max)) {
                $this->error(self::NOT_LESS_INCLUSIVE);
                return false;
            }
        } else {
            if (strtotime($value) >= strtotime($this->max)) {
                $this->error(self::NOT_LESS);
                return false;
            }
        }

        return true;
    }
}
