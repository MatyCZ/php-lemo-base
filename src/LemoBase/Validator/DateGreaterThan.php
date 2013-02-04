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
	protected $messageTemplates = array(
		self::NOT_GREATER           => "The input is not greater than date '%token%'",
		self::NOT_GREATER_INCLUSIVE => "The input is not greater or equal than date '%token%'"
	);

	/**
	 * Additional variables available for validation failure messages
	 *
	 * @var array
	 */
	protected $messageVariables = array(
		'token' => 'token'
	);

	/**
	 * Maximum value as date or field name
	 *
	 * @var mixed
	 */
	protected $token;

	/**
	 * Whether to do inclusive comparisons, allowing equivalence to max
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
			$temp['token'] = array_shift($options);

			if (!empty($options)) {
				$temp['inclusive'] = array_shift($options);
			}

			$options = $temp;
		}

		if (!array_key_exists('token', $options)) {
			throw new \Zend\Validator\Exception\InvalidArgumentException("Missing option 'token'");
		}

		if (!array_key_exists('inclusive', $options)) {
			$options['inclusive'] = false;
		}

		$this->setToken($options['token'])
			->setInclusive($options['inclusive']);

		parent::__construct($options);
	}

	/**
	 * Returns the max option
	 *
	 * @return mixed
	 */
	public function getToken()
	{
		return $this->token;
	}

	/**
	 * Sets the max option
	 *
	 * @param  mixed $max
	 * @return GreaterThan Provides a fluent interface
	 */
	public function setToken($max)
	{
		$this->token = $max;
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
	 * Returns true if and only if $value is greater than max option, inclusively
	 * when the inclusive option is true
	 *
	 * @param  mixed $value
	 * @return boolean
	 */
	public function isValid($value, $context = null)
	{
		$this->setValue($value);

		if (null !== $context && !strtotime($this->token) && array_key_exists($this->token, $context)) {
			$this->token = $context[$this->token];
		}

		if (empty($this->token)) {
			return true;
		}

		if ($this->inclusive) {
			if (strtotime($value) < strtotime($this->token)) {
				$this->error(self::NOT_GREATER_INCLUSIVE);
				return false;
			}
		} else {
			if (strtotime($value) <= strtotime($this->token)) {
				$this->error(self::NOT_GREATER);
				return false;
			}
		}

		return true;
	}
}
