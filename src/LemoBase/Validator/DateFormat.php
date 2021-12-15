<?php

namespace LemoBase\Validator;

use Laminas\Stdlib\ArrayUtils;
use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Exception;
use Traversable;

use function array_key_exists;
use function array_shift;
use function date;
use function func_get_args;
use function is_array;
use function preg_replace;
use function str_replace;
use function strtotime;

class DateFormat extends AbstractValidator
{
    public const INVALID_FORMAT = 'invalidFormat';

    protected array $messageTemplates = [
        self::INVALID_FORMAT => "Date '%value%' doesn`t match format '%format%'",
    ];

    protected array $messageVariables = [
        'format' => 'format',
    ];

    protected string $format;

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

    public function setFormat(string $format): self
    {
        $this->format = $format;
        return $this;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Returns true if and only if $value is greater than max option, inclusively
     * when the inclusive option is true
     *
     * @param mixed $value
     * @param mixed $context
     * @return bool
     */
    public function isValid($value, $context = null): bool
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
