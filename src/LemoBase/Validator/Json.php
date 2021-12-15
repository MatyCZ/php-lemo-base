<?php

namespace LemoBase\Validator;

use Laminas\Validator\AbstractValidator;

class Json extends AbstractValidator
{
    public const INVALID = 'jsonInvalid';

    protected array $messageTemplates = [
        self::INVALID => "Json is invalid: %reason%",
    ];

    protected array $messageVariables = [
        'reason' => 'reason',
    ];

    protected ?string $reason = null;

    /**
     * Returns true if and only if $value is valid JSON
     *
     * @param string $value
     * @return bool
     */
    public function isValid($value): bool
    {
        if (empty($value)) {
            return true;
        }

        json_decode($value);
        if (JSON_ERROR_NONE !== json_last_error()) {
            $this->error(self::INVALID);

            $errorMessage = json_last_error_msg();

            if (!empty($errorMessage)) {
                $this->reason = $errorMessage;
            } else {
                $this->reason = 'An Unexpected Error Occurred';
            }

            return false;
        }

        return true;
    }
}