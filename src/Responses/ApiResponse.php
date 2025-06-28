<?php

declare(strict_types=1);

namespace Larafly\Apidoc\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Larafly\Apidoc\Attributes\Prop;
use ReflectionClass;
use ReflectionNamedType;

class ApiResponse implements Responsable
{
    public const SUCCESS = 200;

    public const ERROR = 0;

    public function isOk(): bool
    {
        return $this->code === self::SUCCESS;
    }

    final public function __construct(
        protected mixed $data = null,
        protected int $code = self::SUCCESS,
        protected mixed $message = 'success'
    ) {}

    public static function error(mixed $message, int $code = self::ERROR): static
    {
        return new static(null, $code, $message);
    }

    public static function success(mixed $data): static
    {
        return new static(data: $data);
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getMessage(): mixed
    {
        return $this->message;
    }

    public static function fromModel(?Model $model): array
    {
        if (! $model) {
            return [];
        }
        $class = new ReflectionClass(static::class);
        $props = $class->getProperties();
        $args = [];

        foreach ($props as $prop) {
            if ($prop->getDeclaringClass()->getName() !== $class->getName()) {
                continue;
            }
            $name = $prop->getName();
            $type = $prop->getType();

            if (! $type instanceof ReflectionNamedType) {
                continue;
            }

            $typeName = $type->getName();
            $value = $model->$name ?? null;
            if ($value instanceof \DateTimeInterface) {
                $args[$name] = $value->format(config('larafly-apidoc.datetime_format'));
            } elseif (is_subclass_of($typeName, ApiResponse::class)) {
                $args[$name] = $typeName::fromModel($model->$name);
            } elseif ($typeName === 'array') {
                $attrs = $prop->getAttributes(Prop::class);
                if (empty($attrs)) {
                    continue;
                }
                $meta = $attrs[0]->newInstance();
                $type = $meta->type;
                if (class_exists($type) && is_subclass_of($type, __CLASS__)) {
                    if ($model->$name instanceof Collection) {
                        // get current type collection reflect models
                        $args[$name] = $model->$name->map(fn (Model $item) => $type::fromModel($item));
                    }
                } elseif (is_array($type)) {
                    $args[$name] = $type;
                }
            } else {
                $args[$name] = $model->$name;
            }
        }

        return $args;
    }

    public function toResponse($request): JsonResponse
    {
        return new JsonResponse([
            'code' => $this->code,
            'message' => $this->message,
            'data' => $this->transform($this->data),
        ]);
    }

    protected function transform(mixed $data): mixed
    {
        if ($data instanceof Model && is_subclass_of(static::class, __CLASS__)) {
            return static::fromModel($data);
        }

        if (is_array($data)) {
            return array_map(fn ($item) => $this->transform($item), $data);
        }

        return $data;
    }
}
