<?php
require __DIR__.'/../vendor/autoload.php';

use App\Container;
use App\Exception\NotFoundException;
use App\Exception\ContainerException;

// Створюємо контейнер
$container = new Container();

// Реєструємо готовий об'єкт
$container->register('config', (object)['db' => 'mysql', 'user' => 'root']);

// Реєструємо фабрику для PDO
$container->register('pdo', function () {
    return new PDO('sqlite::memory:'); // для прикладу SQLite в пам’яті
});

// Реєструємо фабрику з залежністю
$container->register('repo', function ($c) {
    $pdo = $c->get('pdo');
    return new class($pdo) {
        public function __construct(private PDO $pdo)
        {
        }

        public function hello()
        {
            return "Repo працює!";
        }
    };
});

// Використовуємо сервіси
try {
    $config = $container->get('config');
    var_dump($config);

    $pdo = $container->get('pdo');
    echo "Отримали PDO ✔\n";

    $repo = $container->get('repo');
    echo $repo->hello();
} catch (NotFoundException $e) {
    echo "Сервіс не знайдено: " . $e->getMessage();
} catch (ContainerException $e) {
    echo "Помилка контейнера: " . $e->getMessage();
}



//use App\Container;
//
//
//class Logger
//{
//    public function __construct(public string $file)
//    {
//    }
//}
//
//class UserService
//{
//    public function __construct(public \PDO $pdo, public Logger $logger)
//    {
//    }
//}
//
//// створюємо контейнер
//$dic = new Container();
//
//// простий сервіс
//$dic->register('logger', fn() => new Logger(__DIR__ . '/../var/app.log'));
//
//// PDO (просто для прикладу, sqlite в пам’яті)
//$dic->register('pdo', fn() => new PDO('sqlite::memory:'));
//
//// сервіс із залежностями
//$dic->register('userService', fn($c) => new UserService(
//    $c->get('pdo'),
//    $c->get('logger')
//));
//
//// ✅ перевірка singleton
//$u1 = $dic->get('userService');
//$u2 = $dic->get('userService');
//
//echo "<h1>DI Container works </h1>";
//echo "<p>userService #1 id: " . spl_object_id($u1) . "</p>";
//echo "<p>userService #2 id: " . spl_object_id($u2) . "</p>";
//echo "<p>Are they the same? " . ($u1 === $u2 ? "YES ✅" : "NO ❌") . "</p>";
