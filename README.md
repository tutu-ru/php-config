# Библиотека Config

Конфиг делится на 2 составляющие:
* Конфиг приложения (`ApplicationConfigInterface`): поставляется вместе с приложением
* Конфиг среды (`EnvironmentConfigInterface`): загружается из среды запуска

Вся работа ведется через объект `ConfigContainer`. 
Он содержит в себе конфиги среды и приложения и позволяет получать значения как у конкретной реализации, 
так и опрашивая всех (в соответствии с приоритетами).

Приоритеты настраиваются при инициализации. По умолчанию у кофига среды приоритет выше.

## Инициализация и использование

```php
$config = new ConfigContainer();

$config->setApplicationConfig(new SomeAppConfig());
$config->setEnvironmentConfig(new SomeEnvConfig());

// опрос всех по приоритетам
$config->getValue('some.node');

// значение в конкретном конфиге
$config->getApplicationValue('some.node');
$config->getEnvironmanetValue('some.node');

// значение из инфраструктурного конфига
$config->getEnvironmentServiceValue('some.node');
$config->getEnvironmentBusinessValue('some.node');
$config->getEnvironmentInfrastructureValue('some.node');
```

При добавлении можно изменить приоритеты:
```php
$config->setApplicationConfig(new SomeAppConfig(), 1);
$config->setEnvironmentConfig(new SomeEnvConfig(), 0);
// в этом случае у конфига приложения будет более высокий приоритет
```

Можно инициализировать только один из конфигов (приложения или среды).
В этом случае `getValue` будет работать корректно и исключение `InvalidConfigExceptionInterface` будет выброшено 
только при попытке прочитать значение напрямую у конфига, который не был добавлен.

## Конфиг приложения

Поставляется вместе с приложением.

Конфиг может быть изменяемым (`MutableApplicationConfigInterface`) или нет (`ApplicationConfigInterface`).
В первом случае значение можно изменить в рантайме:
```php
$config->setApplicationValue('some.node', 'value');
```
Если попытаться изменить значение у неизменяемого конфига, то будет выброшено исключение `ConfigUpdateForbiddenExceptionInterface`.

## Конфиг среды

Запрашивается в текущей среде запуска.

Конфиг среды делится на три составляющие:
* Сервисный — конфиг сервиса в данной среде (управляется техническими специалистами)
* Бизнес — конфиг сервиса в данной среде (управляется мэнеджерами/аналитиками)
* Инфраструктурный — общий для всех приложений данной среды

Значения бизнес-конфига можно изменять в рантайме:
```php
$config->updateEnvironmentBusinessValue('some.node', 'value');
```
Если значения нет, то оно не создается (и выбрасывается `ConfigUpdateForbiddenExceptionInterface`).
