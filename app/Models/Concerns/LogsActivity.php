<?php

namespace App\Models\Concerns;

use App\Models\ActivityLog;

trait LogsActivity
{
    protected static function bootLogsActivity(): void
    {
        static::created(function (self $model): void {
            $values = array_intersect_key($model->getAttributes(), array_flip($model->getFillable()));
            unset($values['password']);

            ActivityLog::create([
                'subject_type' => get_class($model),
                'subject_id'   => $model->getKey(),
                'actor_id'     => auth()->id(),
                'action'       => 'created',
                'new_values'   => $values ?: null,
            ]);
        });

        static::updated(function (self $model): void {
            $dirty = $model->getDirty();
            unset($dirty['password'], $dirty['updated_at']);

            if (empty($dirty)) {
                return;
            }

            $oldValues = array_map(fn ($key) => $model->getOriginal($key), array_keys($dirty));
            $oldValues = array_combine(array_keys($dirty), $oldValues);

            ActivityLog::create([
                'subject_type' => get_class($model),
                'subject_id'   => $model->getKey(),
                'actor_id'     => auth()->id(),
                'action'       => 'updated',
                'old_values'   => $oldValues,
                'new_values'   => $dirty,
            ]);
        });

        static::deleted(function (self $model): void {
            $values = array_intersect_key($model->getAttributes(), array_flip($model->getFillable()));
            unset($values['password']);

            ActivityLog::create([
                'subject_type' => get_class($model),
                'subject_id'   => $model->getKey(),
                'actor_id'     => auth()->id(),
                'action'       => 'deleted',
                'old_values'   => $values ?: null,
            ]);
        });
    }
}
