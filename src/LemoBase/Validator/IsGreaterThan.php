<?php

namespace LemoBase\Validator;

use Laminas\Stdlib\ArrayUtils;
use Laminas\Validator\AbstractValidator;
use Traversable;

class IsGreaterThan extends AbstractValidator
{
    const MISSING_TOKEN = 'missingToken';
    const NOT_GREATER = 'notGreaterThan';
    const NOT_GREATER_INCLUSIVE = 'notGreaterThanInclusive';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::MISSING_TOKEN         => 'No token was provided to match against',
        self::NOT_GREATER           => "The input is not greater than '%token%'",
        self::NOT_GREATER_INCLUSIVE => "The input is not greater or equal than '%token%'",
    ];

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $messageVariables = [
        'token' => 'tokenString',
    ];

    /**
     * Maximum value as date or field name
     *
     * @var mixed
     */
    protected $token;

    /**
     * @var string
     */
    protected $tokenString;

    /**
     * Whether to do inclusive comparisons, allowing equivalence to max
     *
     * @var bool
     */
    protected $inclusive = false;

    /**
     * Sets validator options
     *
     * @param  mixed $token
     */
    public function __construct($token = null)
    {
        if ($token instanceof Traversable) {
            $token = ArrayUtils::iteratorToArray($token);
        }

        if (is_array($token) && array_key_exists('token', $token)) {
            if (array_key_exists('inclusive', $token)) {
                $this->setInclusive($token['inclusive']);
            }

            $this->setToken($token['token']);
        } elseif (null !== $token) {
            $this->setToken($token);
        }

        parent::__construct(is_array($token) ? $token : null);
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
     * Retrieve token
     *
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set token against which to compare
     *
     * @param  mixed $token
     * @return Identical
     */
    public function setToken($token)
    {
        $this->tokenString = (is_array($token) ? var_export($token, true) : (string) $token);
        $this->token       = $token;
        return $this;
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

        $token = $this->getToken();

        if (null !== $context) {
            if (! is_array($context) && ! ($context instanceof ArrayAccess)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Context passed to %s must be array, ArrayObject or null; received "%s"',
                    __METHOD__,
                    is_object($context) ? get_class($context) : gettype($context)
                ));
            }

            if (is_array($token)) {
                while (is_array($token)) {
                    $key = key($token);
                    if (! isset($context[$key])) {
                        break;
                    }
                    $context = $context[$key];
                    $token   = $token[$key];
                }
            }

            // if $token is an array it means the above loop didn't went all the way down to the leaf,
            // so the $token structure doesn't match the $context structure
            if (is_array($token) || ! isset($context[$token])) {
                $token = $this->getToken();
            } else {
                $token = $context[$token];
            }
        }

        if ($token === null) {
            $this->error(self::MISSING_TOKEN);
            return false;
        }

        if (true === $this->getInclusive()) {
            if ($value < $token) {
                $this->error(self::NOT_GREATER_INCLUSIVE);
                return false;
            }
        } else {
            if ($value <= $token) {
                $this->error(self::NOT_GREATER);
                return false;
            }
        }

        return true;
    }
}
