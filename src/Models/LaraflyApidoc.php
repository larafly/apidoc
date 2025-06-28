<?php

namespace Larafly\Apidoc\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $larafly_apidoc_type_id type id
 * @property string $name name
 * @property string $creator creator
 * @property string $updater updater
 * @property string $desc desc
 * @property string $request_type request type
 * @property string $url url
 * @property string $request_data request data
 * @property string $response_data response data
 * @property string $response_demo response demo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaraflyApidoc newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaraflyApidoc newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaraflyApidoc query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaraflyApidoc whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaraflyApidoc whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaraflyApidoc whereLaraflyApidocTypeId($value)
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
        'larafly_apidoc_type_id',
        'name',
        'desc',
        'creator',
        'updater',
        'request_type',
        'url',
        'request_data',
        'response_data',
        'response_demo',
    ];

    protected $casts = [
        'request_data' => 'json',
        'response_data' => 'json',
        'response_demo' => 'json',
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('larafly-apidoc.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
