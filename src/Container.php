<?php
namespace App;

use Psr\Container\ContainerInterface;
use App\Exception\NotFoundException;
use App\Exception\ContainerException;

class Container implements ContainerInterface
{
    /** @var array<string, mixed> */
    private array $definitions = [];

    /** @var array<string, object> */
    private array $instances = [];

    /**
     * Реєстрація: приймаємо або готовий об’єкт, або фабрику-колбек.
     * @param string $id
     * @param object|callable $definition
     */
    public function register(string $id, $definition): void
    {
        $this->definitions[$id] = $definition;
    }

    public function has(string $id): bool
    {
        return isset($this->instances[$id]) || array_key_exists($id, $this->definitions);
    }

    public function get(string $id): mixed
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (!array_key_exists($id, $this->definitions)) {
            throw new NotFoundException("Service '{$id}' not found");
        }

        $def = $this->definitions[$id];

        // Готовий об’єкт
        if (is_object($def) && !is_callable($def)) {
            return $this->instances[$id] = $def;
        }

        // Фабрика
        if (is_callable($def)) {
            $obj = $def($this); // передаємо контейнер для залежностей
            if (!is_object($obj)) {
                throw new ContainerException("Factory for '{$id}' must return object");
            }
            return $this->instances[$id] = $obj;
        }

        throw new ContainerException("Invalid definition type for '{$id}'");
    }
}
