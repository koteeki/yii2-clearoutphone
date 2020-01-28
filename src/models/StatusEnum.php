<?php

namespace koteeki\clearoutphone\models;

use yii2mod\enum\helpers\BaseEnum;

/**
 * Class Status
 * @package koteeki\clearoutphone\models
 */
class StatusEnum extends BaseEnum
{
    const VALID = 'valid';
    const INVALID = 'invalid';

    protected static $list = [
        self::VALID => 'Valid',
        self::INVALID => 'Invalid',
    ];
}