# Библиотека Config

Базовый интерфейс для работы с конфигами.

Конфиг может быть изменяемым (`MutableConfigInterface`) или нет (`ConfigInterface`).

## ConfigContainer

Содержит в себе конфиги и позволяет получать значения как у конкретной реализации, так и опрашивая всех (в соответствии с приоритетами).

Приоритеты настраиваются при инициализации. Чем больше число указанное при инициализации, тем выше приоритет.

### Инициализация и использование

```php
$config = new ConfigContainer();

$config->setConfig('app', new SomeAppConfig(), 0);
$config->setConfig('env', new SomeEnvConfig(), 1);

// опрос всех по приоритетам
$config->getValue('some.node');

// значение в конкретном конфиге
$config->getConfig('app')->getValue('some.node');
$config->getConfig('env')->getValue('some.node');
```

## JsonConfig

Реализация конфига в виде json-файла
