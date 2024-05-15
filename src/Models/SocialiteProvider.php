<?php

namespace Wame\LaravelAuth\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
//use Spatie\Activitylog\LogOptions;
//use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteProvider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteProvider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteProvider onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteProvider ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteProvider query()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteProvider withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteProvider withoutTrashed()
 * @property string $id
 * @property int|null $sort_order
 * @property string $title
 * @property string $name
 * @property string $class
 * @property array $credentials
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteProvider whereClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteProvider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteProvider whereCredentials($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteProvider whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteProvider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteProvider whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteProvider whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteProvider whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteProvider whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SocialiteProvider extends Model implements Sortable
{
    use HasUlids;
    //use LogsActivity;
    use SoftDeletes;
    use SortableTrait;

    /**
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * @var string[]
     */
    protected $casts = [
        'credentials' => 'array',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    ///**
    // * @return LogOptions
    // */
    //public function getActivitylogOptions(): LogOptions
    //{
    //    return LogOptions::defaults()->logAll();
    //}
}
