<?php

namespace Larafly\Apidoc\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $laravel_apidoc_type_id apidoc type id
 * @property string $name name
 * @property string $request_type request type
 * @property string $url url
 * @property string $request_data request data
 * @property string $response_data response data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaraflyApidoc newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaraflyApidoc newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaraflyApidoc query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaraflyApidoc whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaraflyApidoc whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaraflyApidoc whereLaravelApidocTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaraflyApidoc whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaraflyApidoc whereRequestData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaraflyApidoc whereRequestType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaraflyApidoc whereResponseData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaraflyApidoc whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaraflyApidoc whereUrl($value)
 *
 * @mixin \Eloquent
 */
class LaraflyApidoc extends Model
{
    protected $fillable = [
        'larafly_api_doc_id',
        'name',
        'desc',
        'request_type',
        'url',
        'request_data',
        'response_data',
    ];

    public function laravel_api_doc_type(): BelongsTo
    {
        return $this->belongsTo(LaraflyApidocType::class);
    }
}
