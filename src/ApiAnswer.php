<?php


namespace lShamanl\ApiAnswer;

use Exception;

/**
 * Class ApiAnswer
 * @package lShamanl\ApiAnswer
 */
class ApiAnswer
{

    /** @var bool */
    protected $ok;

    /** @var int */
    protected $code;

    /** @var string */
    protected $description;

    /** @var array */
    protected $data;

    /**
     * ApiAnswer constructor.
     * @param bool $ok
     * @param int $code
     * @param null $description
     * @param array|null $data
     */
    public function __construct($ok = false, $code = StatusCode::HTTP_ACCEPTED, $description = null, array $data = null)
    {
        $this->setOk($ok);
        $this->setCode($code);
        $this->setDescription($description);
        $this->setData($data);
    }

    /**
     * @param bool $ok
     * @return ApiAnswer
     */
    public function setOk($ok)
    {
        $this->ok = (bool)$ok;
        return $this;
    }

    /**
     * @param int $code
     * @return ApiAnswer
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @param string $description
     * @return ApiAnswer
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     * Добавить произвольную информацию в поле ответа "data"
     */
    public function addData(array $data)
    {
        foreach ($data as $key => $value) {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * @param array $data
     * @return ApiAnswer
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return bool
     */
    public function isOk()
    {
        return $this->ok;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $fieldName
     * @return mixed
     */
    public function getDataField($fieldName)
    {
        if (!isset($this->data[$fieldName])) {
            return null;
//            throw new FieldNotSetException('This field is not set');
        }

        return $this->data[$fieldName];
    }

    /**
     * @return string
     * Зашифровать тело запроса в JSON и вернуть его в виде json-строки
     */
    public function toJson()
    {
        $answer = [
            'ok' => $this->isOk(),
            'code' => $this->getCode(),
            'description' => $this->getDescription(),
            'data' => $this->getData()
        ];

        $this->unsetEmpty($answer);
        return json_encode($answer, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param string $json
     * @return ApiAnswer
     * Создать новый объект текущего класса из json-строки
     */
    public static function createFromJson($json)
    {
        $apiAnswer = new ApiAnswer();
        $data = json_decode($json, true);

        $apiAnswer
            ->setOk($data['ok'])
            ->setCode($data['code'])
            ->setDescription($data['description'])
            ->setData($data['data'])
        ;

        return $apiAnswer;
    }

    /**
     * @param string $description
     * @param int $code
     * @param bool $setResponseCode
     * @return string
     */
    public static function responseOk($description = null, $code = StatusCode::HTTP_OK, $setResponseCode = false)
    {
        /** @var ApiAnswer $apiAnswer */
        $apiAnswer = (new self(true, $code));
        if (isset($description)) {
            $apiAnswer->setDescription($description);
        } else {
            $apiAnswer->setDescription(StatusCode::getDescription($code));
        }

        if ($setResponseCode) { http_response_code($apiAnswer->getCode()); }

        return $apiAnswer->toJson();
    }

    /**
     * @param string $description
     * @param int $code
     * @param bool $setResponseCode
     * @return string
     */
    public static function responseRejected($description = null, $code = StatusCode::HTTP_BAD_REQUEST, $setResponseCode = false)
    {
        /** @var ApiAnswer $apiAnswer */
        $apiAnswer = (new self(false, $code));
        if (isset($description)) {
            $apiAnswer->setDescription($description);
        } else {
            $apiAnswer->setDescription(StatusCode::getDescription($code));
        }

        if ($setResponseCode) { http_response_code($apiAnswer->getCode()); }

        return $apiAnswer->toJson();
    }

    /**
     * @param Exception $exception
     * @param bool $setResponseCode
     * @return string
     */
    public static function responseException(Exception $exception, $setResponseCode = false)
    {
        /** @var ApiAnswer $apiAnswer */
        $apiAnswer = (new self(false, $exception->getCode()));
        if (!empty($message = $exception->getMessage())) {
            $apiAnswer->setDescription($message);
        } else {
            $apiAnswer->setDescription(StatusCode::getDescription($exception->getCode()));
        }

        if ($setResponseCode) { http_response_code($apiAnswer->getCode()); }

        return $apiAnswer->toJson();
    }

    /**
     * @param null $description
     * @param int $code
     * @param bool $setResponseCode
     * @return string
     */
    public static function responseError($description = null, $code = StatusCode::HTTP_INTERNAL_SERVER_ERROR, $setResponseCode = false)
    {
        /** @var ApiAnswer $apiAnswer */
        $apiAnswer = (new self(false, $code));
        if (isset($description)) {
            $apiAnswer->setDescription($description);
        } else {
            $apiAnswer->setDescription(StatusCode::getDescription($code));
        }

        if ($setResponseCode) { http_response_code($apiAnswer->getCode()); }

        return $apiAnswer->toJson();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Удалить пустые элементы массива
     * @param array $array
     */
    protected function unsetEmpty(array &$array)
    {
        foreach ($array as $key => $value) {
            if (!isset($value)) {
                unset($array[$key]);
            }
        }
    }
}