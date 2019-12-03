<?php

namespace LemoBase\Validator;

use Throwable;
use Zend\Json as ZendJson;
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

        try {
            ZendJson\Decoder::decode(
                $value,
                ZendJson\Json::TYPE_ARRAY
            );
        } catch (Throwable $throwable) {
            $this->reason = $throwable->getMessage();
            $this->error(self::INVALID);
            return false;
        }

        return true;
    }
}