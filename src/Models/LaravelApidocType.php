<?php

namespace Larafly\Apidoc\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 *
 * @property int $id
 * @property string $name name
 * @property string $alias alias
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaravelApidocType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaravelApidocType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaravelApidocType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaravelApidocType whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaravelApidocType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaravelApidocType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaravelApidocType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaravelApidocType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LaravelApidocType extends Model
{

    public function laravel_api_doc():HasMany
    {
        return $this->hasMany(LaravelApiDoc::class);
    }
}
