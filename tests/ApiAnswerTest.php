<?php

namespace lShamanl\ApiAnswer;


use PHPUnit\Framework\TestCase;

class ApiAnswerTest extends TestCase
{

    /** @var ApiAnswer */
    protected $apiAnswer;

    public function setUp()
    {
        $this->apiAnswer = new ApiAnswer();
    }

    public function testSetCode()
    {
        $code = 200;
        $this->apiAnswer->setCode($code);

        $this->assertEquals($code,$this->apiAnswer->getCode());
    }

    public function testSetDescription()
    {
        $description = 'My description';
        $this->apiAnswer->setDescription($description);

        $this->assertEquals($description, $this->apiAnswer->getDescription());
    }

    /**
     * @dataProvider setOkProvider
     * @param $ok
     * @param bool $expected
     */
    public function testSetOk($ok, $expected)
    {
        $this->apiAnswer->setOk($ok);
        $this->assertEquals($expected, $this->apiAnswer->isOk());
    }

    public function testSetData()
    {
        $data = [
            'key_1' => 'value_1',
            'key_2' => 'value_2',
            'key_3' => 'value_3',
        ];

        $this->apiAnswer->setData($data);
        $this->assertEquals($data, $this->apiAnswer->getData());
    }

    public function testAddData()
    {
        $setDataArray = [
            'key_1' => 'value_1',
            'key_2' => 'value_2',
            'key_3' => 'value_3',
        ];
        $this->apiAnswer->setData($setDataArray);

        $addDataArray = [
            'key_4' => 'value_4',
            'key_5' => 'value_5',
        ];
        $this->apiAnswer->addData($addDataArray);

        $this->assertEquals(
            array_merge($setDataArray, $addDataArray),
            $this->apiAnswer->getData()
        );
    }

    /**
     * @param bool $ok
     * @param int $code
     * @param string $description
     * @param array $data
     * @dataProvider toJsonProvider
     */
    public function testToJson($ok, $code, $description, $data)
    {
        $this->apiAnswer->setOk($ok)->setCode($code)->setDescription($description)->setData($data);

        $structure = [
            'ok' => $ok,
            'code' => $code,
            'description' => $description,
            'data' => $data,
        ];

        foreach ($structure as $key => $value) {
            if (!isset($value)) {
                unset($structure[$key]);
            }
        }

        $this->assertEquals(
            json_encode($structure, JSON_UNESCAPED_UNICODE),
            $this->apiAnswer->toJson()
        );
    }

    /**
     * @param $json
     * @dataProvider toJsonProvider
     */
    public function testCreateFromJson($json)
    {
        $this->assertInstanceOf(ApiAnswer::class, ApiAnswer::createFromJson($json));
    }

    public function testGetDataField()
    {
        $this->apiAnswer->setData(['key_1' => 'value_1', 'key_2' => 'value_2']);

        $this->assertEquals('value_1', $this->apiAnswer->getDataField('key_1'));
        $this->assertEquals('value_2', $this->apiAnswer->getDataField('key_2'));
        $this->assertEquals(null, $this->apiAnswer->getDataField('key_3'));
    }

    /**
     * @return array
     */
    public function createFromJsonProvider()
    {
        return [
            ['{"ok":true,"code":200,"description":"This is OK","data":{"key_1":"value_1"}}'],
            ['{"ok":false,"code":400,"description":"Client Error","data":{"key_2":"value_2"}}'],
            ['{"ok":false}'],
            ['{"ok":false,"code":200}'],
        ];
    }

    /**
     * @return array
     */
    public function toJsonProvider()
    {
        return [
            [true, 200, 'This is OK', ['key_1' => 'value_1']],
            [false, 400, 'Client Error', ['key_2' => 'value_2']],
            [false, null, null, null],
            [false, 200, null, null],
        ];
    }

    /**
     * @return array
     */
    public function setOkProvider()
    {
        return [
            [true, true],
            [false, false],
            [null, false],
            [1, true],
            [0, false],
            [-1, true],
            ['', false],
            ['1', true],
        ];
    }
}
