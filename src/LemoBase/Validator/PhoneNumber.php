<?php

namespace LemoBase\Validator;

use Laminas\Validator\AbstractValidator;

use function array_key_exists;
use function array_keys;
use function is_array;
use function is_string;
use function preg_match;
use function substr;

class PhoneNumber extends AbstractValidator
{
    public const INVALID                = 'phoneNumberInvalid';
    public const NO_MATCH               = 'phoneNumberNoMatch';
    public const NO_MATCH_INTERNATIONAL = 'phoneNumberNoMatchInternational';
    public const UNSUPPORTED            = 'phoneNumberUnsupported';

    protected array $messageTemplates = [
        self::INVALID                => 'Invalid type given. String expected',
        self::NO_MATCH               => 'The input does not match a phone number format',
        self::NO_MATCH_INTERNATIONAL => 'The input does not match an international phone number format',
        self::UNSUPPORTED            => 'The country provided is currently unsupported',
    ];

    protected array $patterns = [
        'cs-CZ' => "/^(\+?420)? ?[1-9][0-9]{2} ?[0-9]{3} ?[0-9]{3}$/",
        'sk-SK' => "/^(\+?421)? ?[1-9][0-9]{2} ?[0-9]{3} ?[0-9]{3}$/",
    ];

    /**
     * @var array|string|null
     */
    protected $locale;

    /**
     * true = international format
     *
     * @var bool
     */
    protected bool $strict = false;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        if (array_key_exists('locale', $options)) {
            $this->setLocale($options['locale']);
        }
        if (array_key_exists('strict', $options)) {
            $this->setStrict($options['strict']);
        }

        parent::__construct($options);
    }

    /**
     * @param  array|null|string $locale
     * @return self
     */
    public function setLocale($locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @param  bool $strict
     * @return self
     */
    public function setStrict(bool $strict): self
    {
        $this->strict = $strict;

        return $this;
    }

    /**
     * @param  string $value
     * @return bool
     */
    public function isValid($value): bool
    {
        if (!is_string($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);

        // Is not strict
        if (true === $this->strict && '+' != substr($value, 0, 1)) {
            $this->error(self::NO_MATCH);
            return false;
        }

        if (is_array($this->locale)) {
            foreach ($this->locale as $locale) {
                if (!array_key_exists($locale, $this->patterns)) {
                    $this->error(self::UNSUPPORTED);
                    return false;
                }

                if (preg_match($this->patterns[$locale], $value)) {
                    return true;
                }
            }

            $this->error(self::NO_MATCH);
            return false;
        } elseif (null !== $this->locale) {
            if (!array_key_exists($this->locale, $this->patterns)) {
                $this->error(self::UNSUPPORTED);
                return false;
            }

            if (preg_match($this->patterns[$this->locale], $value)) {
                return true;
            }

            $this->error(self::NO_MATCH);
            return false;
        } else {
            foreach (array_keys($this->patterns) as $locale) {
                if (preg_match($this->patterns[$locale], $value)) {
                    return true;
                }
            }

            $this->error(self::NO_MATCH);
            return false;
        }
    }
}
