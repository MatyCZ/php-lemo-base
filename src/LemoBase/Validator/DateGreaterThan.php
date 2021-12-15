<?php

namespace LemoBase\Validator;

use Laminas\Stdlib\ArrayUtils;
use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Exception;
use Traversable;

use function array_key_exists;
use function array_shift;
use function func_get_args;
use function is_array;
use function strtotime;

class DateGreaterThan extends AbstractValidator
{
    public const NOT_GREATER           = 'notDateGreaterThan';
    public const NOT_GREATER_INCLUSIVE = 'notDateGreaterThanInclusive';

    protected array $messageTemplates = [
        self::NOT_GREATER           => "The input is not greater than date '%min%'",
        self::NOT_GREATER_INCLUSIVE => "The input is not greater or equal than date '%min%'",
    ];

    protected array $messageVariables = [
        'min' => 'min',
    ];

    /**
     * Maximum value as date or field name
     *
     * @var string
     */
    protected string $min;

    /**
     * Whether to do inclusive comparisons, allowing equivalence to min
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
            $temp['min'] = array_shift($options);

            if (!empty($options)) {
                $temp['inclusive'] = array_shift($options);
            }

            $options = $temp;
        }

        if (!array_key_exists('min', $options)) {
            throw new Exception\InvalidArgumentException("Missing option 'min'");
        }

        if (!array_key_exists('inclusive', $options)) {
            $options['inclusive'] = false;
        }

        $this->setMin($options['min'])
            ->setInclusive($options['inclusive']);

        parent::__construct($options);
    }

    /**
     * Sets the min option
     *
     * @param  string $min
     * @return self
     */
    public function setMin(string $min): self
    {
        $this->min = $min;
        return $this;
    }

    /**
     * Returns the min option
     *
     * @return string
     */
    public function getMin(): string
    {
        return $this->min;
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
     * Returns true if and only if $value is greater than min option, inclusively
     * when the inclusive option is true
     *
     * @param  mixed $value
     * @return bool
     */
    public function isValid($value): bool
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
