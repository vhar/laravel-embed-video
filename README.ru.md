Русский | [English](README.md)

Embed Video для Laravel
=======================

Пакет предназначен для создания URL-адреса для встраиваемого видео и URL-адреса на картинку обложки с различных видеохостингов из URL-адреса видео в любом формате.  
Пакет можно использовать как для обработки введенных пользователем данных перед добавлением в базу данных, так и для отображении результата на странице.  
В пакет входят классы-обработчики для видео с YouTube, Rutube и VK Video.  
При необходимости можно добавить собственные классы обработчики для других видеохостингов.  


## Установка

Установите последнюю версию с помощью `composer`  
```bash
composer require vhar/laravel-embed-video
```

## Использование

Вызовите метод `handle` фасада `Vhar\LaravelEmbedVideo\Facades\EmbeVideo`, передав ему в качестве аргумента URL-адрес видео в любом формате.  
```php
try {
    return EmbedVideo::handle('https://www.youtube.com/watch?v=gpn_4tWz1w8');
} catch (\Exception $exception) {
    return ['error' => $exception->getMessage()];
}
```

Если переданный URL-адрес успешно обработан, метод `handle` вернет экземпляр класса `\Vhar\LaravelEmbedVideo\EmbedData`, содержащий идентификатор, URL-адрес для встраиваемого видео и URL-адрес на изображение обложки.   
```php
{
  id: "gpn_4tWz1w8"
  video: "https://www.youtube.com/embed/gpn_4tWz1w8"
  cover: "https://img.youtube.com/vi/gpn_4tWz1w8/0.jpg"
}

```
или исключение с сообщением об ошибке, например:  
```
The argument is not valid URL-address to a YouTube video.
```

Вы также можете вызвать определенный обработчик, используя метод `hosting` передав в качестве аргумента псевдонимом доступного обработчика.  
```php
try {
    return EmbedVideo::hosting('youtube')->handle('https://www.youtube.com/watch?v=gpn_4tWz1w8');
} catch (\Exception $exception) {
    return ['error' => $exception->getMessage()];
}
```

В настоящее время доступны следующие псевдонимы:  
* `youtube` для YouTube;  
* `rutube` для Rutube;
* `vkvideo` для VK Video;


## Тестирование

Чтобы использовать тесты, добавьте в файл `.env` для вашей тестовой среды ключи со URL-адресами любых доступных видео на соответствующих хостингах.  
```
YOUTUBE_VIDEO=''
RUTUBE_VIDEO=''
VK_VIDEO=''
``` 

Если ключ отсутствуют или содержат недопустимое значение, тест будет пропущен.  

На следующем шаге добавьте блок `testsuite` в файл `phpunit.xml`.
```xml
<testsuites>instance
    ...
    <testsuite name="Embed Video">
        <directory suffix="Test.php">./vendor/vhar/laravel-embed-video/tests/</directory>
    </testsuite>
</testsuites>
```

Тесты `Embedded video` теперь будут выполняться при запуске `php artisan test`.  


## Создание собственного класса-обработчика

Создайте класс реализующую интерфейс `Vhar\LaravelEmbedVideo\Contracts\EmbedVideoContract`.
В методе `handle` создайте код обработки строки URL. Этот метод должен возвращать экземпляр класса `\Vhar\LaravelEmbedVideo\EmbedData` содержащий свойсвта с идентификатором (`id`), URL-адресом для встраимаевого видео (`video`) и URL-адресом изображения обложки (`cover`).  
```php
public function handle(string $url): EmbedData
{
    ...    
    return EmbedData::create($id, $embedURL, $coverURL);
}
``` 

Метод `availableDomains` должен возвращать массив с именами доменов хостинга для вашего обработчика.  
```php
public function allowedDomains(): array
{
    return [
        'youtu.be',
        'youtube.com',
    ];
}
```

Добавьте в метод `register` провайдера `App\Providers\AppServiceProvider` регистрацию вашего обработчика.  
```php
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        ...
        \Vhar\LaravelEmbedVideo\Facades\VideoHosting::hosting('you_alias', \YouNamespace\YouClassHandlerService::class);
    }
}
```

Теперь вы можете вызвать обработку ссылок из вашего класса, как и для встроенных обработчиков, вызвав метод `handle` фасада `Vhar\LaravelEmbedVideo\Facades\EmbedVideo`.
