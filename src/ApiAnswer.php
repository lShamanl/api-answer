<?php


namespace lShamanl\ApiAnswer;

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