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
