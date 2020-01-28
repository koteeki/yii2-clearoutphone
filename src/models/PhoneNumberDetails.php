<?php

namespace koteeki\clearoutphone\models;

use yii\base\BaseObject;

/**
 * Class PhoneNumber
 * @package koteeki\clearoutphone\models
 * @property bool $isValid
 */
class PhoneNumberDetails extends BaseObject
{
    public $status;

    public $line_type;

    public $carrier;

    public $location;

    public $country_name;

    public $country_timezone;

    public $country_code;

    public $international_format;

    public $local_format;

    public $e164_format;

    public $can_be_internationally_dialled;

    /**
     * @return bool
     */
    public function getIsValid()
    {
        return $this->status === StatusEnum::VALID;
    }
}