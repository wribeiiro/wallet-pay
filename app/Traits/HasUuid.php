<?php

namespace App\Traits;

trait HasUuid
{
    public static function bootHasUuid(): void
    {
        static::creating(fn($model) => $model->id = \Ramsey\Uuid\Uuid::uuid4()->toString());
    }
}
