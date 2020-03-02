# ApiAnswer
Универсальный формат для создания ответов от API.

Пример использования библиотеки:
```php
echo (new ApiAnswer(true,200,'Принято'))->addData(['key_1' => 'value_1', 'key_2' => 'value_2'])->toJson();
```

Структура ответа возвращается в формате JSON:
```json
{
    "ok":true,
    "code":200,
    "description":"Принято",
    "data": {
        "key_1":"value_1",
        "key_2":"value_2"
    }
}
```