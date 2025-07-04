<?php

namespace Larafly\Apidoc\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name name
 * @property string $module module
 * @property string $parent_id parent_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaraflyApidocType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaraflyApidocType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaraflyApidocType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaraflyApidocType whereModule($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaraflyApidocType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaraflyApidocType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaraflyApidocType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaraflyApidocType whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaraflyApidocType whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class LaraflyApidocType extends Model
{
    protected $fillable = ['name', 'module', 'parent_id'];

    public function larafly_apidocs(): HasMany
    {
        return $this->hasMany(LaraflyApidoc::class);
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
