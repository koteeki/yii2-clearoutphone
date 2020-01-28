<?php

namespace koteeki\clearoutphone;

use koteeki\clearoutphone\exceptions\BadRequestException;
use koteeki\clearoutphone\exceptions\ClearoutPhoneException;
use koteeki\clearoutphone\exceptions\PaymentRequiredException;
use koteeki\clearoutphone\exceptions\ServiceUnavailable;
use koteeki\clearoutphone\exceptions\TimeoutException;
use koteeki\clearoutphone\exceptions\UnauthorizedException;
use koteeki\clearoutphone\models\PhoneNumberDetails;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\httpclient\Client;
use yii\httpclient\RequestEvent;
use yii\httpclient\Response;

/**
 * Class ClearoutPhone
 * @package koteeki\clearoutphone
 */
class ClearoutPhone extends Component
{
    const BASE_URL = 'https://api.clearoutphone.io/v1/phonenumber/';
    const TIMEOUT_DEFAULT = 5000;

    /** @var string */
    public $token;

    /** @var int */
    public $timeout;

    /**
     * {@inheritDoc}
     */
    public function __construct($config = [])
    {
        $this->timeout = self::TIMEOUT_DEFAULT;

        parent::__construct($config);
    }

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();

        if (!$this->token) {
            throw new InvalidConfigException('ClearoutPhone is not configured.');
        }
    }

    /**
     * @param string $method
     * @param string $api
     * @param array $data
     * @return Response
     * @throws
     */
    protected function sendRequest(string $method, string $api, array $data = [])
    {
        $request = (new Client())
            ->createRequest()
            ->setMethod($method)
            ->setUrl(self::BASE_URL . $api)
            ->addHeaders([
                'Authorization' => 'Bearer:' . $this->token,
                'Accept' => 'application/json',
                'Content-type' => 'application/json',
            ])
            ->setContent(Json::encode($data));

        $request->on(Client::EVENT_AFTER_SEND, [$this, 'onAfterSend']);

        return $request->send();
    }

    /**
     * @param RequestEvent $event
     * @throws
     */
    protected function onAfterSend($event)
    {
        if ($event->response->isOk) {
            return;
        }

        switch ($event->response->statusCode) {
            case 400:
                $message = ArrayHelper::getValue($event->response->getData(), 'error.reasons.0.messages.0');
                throw new BadRequestException($message);

            case 401:
                $message = ArrayHelper::getValue($event->response->getData(), 'error.message');
                throw new UnauthorizedException($message);

            case 402:
                $message = ArrayHelper::getValue($event->response->getData(), 'error.message');
                throw new PaymentRequiredException($message);

            case 503:
                throw new ServiceUnavailable();

            case 524:
                throw new TimeoutException();

            default:
                throw new ClearoutPhoneException();
        }
    }

    /**
     * @return int
     * @throws
     */
    public function getCredits()
    {
        $response = $this->sendRequest('GET', 'getcredits');

        return ArrayHelper::getValue($response->getData(), 'data.available_credits');
    }

    /**
     * @param string $number
     * @param string $countryCode
     * @return PhoneNumberDetails
     * @throws
     */
    public function getPhoneDetails(string $number, string $countryCode = null)
    {
        $parameters = [
            'number' => $number,
            'timeout' => $this->timeout,
        ];

        if ($countryCode) {
            $parameters['country_code'] = $countryCode;
        }

        $response = $this->sendRequest('POST', 'validate', $parameters);

        return new PhoneNumberDetails(ArrayHelper::getValue($response->getData(), 'data'));
    }
}