<?php

namespace koteeki\clearoutphone\exceptions;

use yii\base\Exception;

/**
 * Class ClearoutPhoneException
 * @package koteeki\clearoutphone\exceptions
 */
class ClearoutPhoneException extends Exception
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'ClearoutPhone Exception';
    }
}