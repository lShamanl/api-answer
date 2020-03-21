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

    public function __construct($ok = null, $code = null, $description = null, array $data = null)
    {
        $this->ok = $ok;
        $this->code = $code;
        $this->description = $description;
        $this->data = $data;
    }

    /**
     * @return bool
     */
    public function isOk()
    {
        return $this->ok;
    }

    /**
     * @param bool $ok
     * @return ApiAnswer
     */
    public function setOk($ok)
    {
        $this->ok = $ok;
        return $this;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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
     * @return array
     */
    public function getData()
    {
        return $this->data;
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

        foreach ($answer as $key => $item) {
            if (!isset($item)) {
                unset($answer[$key]);
            }
        }

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
     * @param string $fieldName
     * @return mixed
     * @throws Exception
     */
    public function getDataField($fieldName)
    {
        if (!isset($this->data[$fieldName])) {
            throw new Exception('Попытка получить несуществующее поля в ответе от API');
        }
        return $this->data[$fieldName];
    }

    /**
     * @param string $description
     * @param int $code
     * @param bool $isNeedSetResponseCode
     * @return string
     */
    public static function responseOk($description = null, $code = StatusCode::HTTP_OK, $isNeedSetResponseCode = false)
    {
        $apiAnswer = (new self(true, $code));
        if (isset($description)) { $apiAnswer->setDescription($description); }
        if ($isNeedSetResponseCode) { http_response_code($apiAnswer->getCode()); }
        return $apiAnswer->toJson();
    }

    /**
     * @param string $description
     * @param int $code
     * @param bool $isNeedSetResponseCode
     * @return string
     */
    public static function responseRejected($description = null, $code = StatusCode::HTTP_BAD_REQUEST, $isNeedSetResponseCode = false)
    {
        $apiAnswer = (new self(true, $code));
        if (isset($description)) { $apiAnswer->setDescription($description); }
        if ($isNeedSetResponseCode) { http_response_code($apiAnswer->getCode()); }
        return $apiAnswer->toJson();
    }

    /**
     * @param Exception $e
     * @param bool $isNeedSetResponseCode
     * @return string
     */
    public static function responseError(Exception $e, $isNeedSetResponseCode = false)
    {
        $apiAnswer = (new self())->setOk(false)->setCode($e->getCode())->setDescription($e->getMessage());
        if ($isNeedSetResponseCode) { http_response_code($apiAnswer->getCode()); }
        return $apiAnswer->toJson();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }
}