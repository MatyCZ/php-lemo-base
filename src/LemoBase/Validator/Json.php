<?php

namespace LemoBase\Validator;

use Zend\Validator\AbstractValidator;

class Json extends AbstractValidator
{
    const INVALID = 'jsonInvalid';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::INVALID => "Json is invalid: %reason%",
    ];

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $messageVariables = [
        'reason' => 'reason',
    ];

    /**
     * @var string
     */
    protected $reason;

    /**
     * Returns true if and only if $value is valid JSON
     *
     * @param string $value
     * @return bool
     */
    public function isValid($value)
    {
        if (empty($value)) {
            return true;
        }

        json_decode($value);
        $errorMessage = json_last_error_msg();
        if (!empty($errorMessage)) {
            $this->reason = $errorMessage;
            $this->error(self::INVALID);
            return false;
        }

        return true;
    }
}