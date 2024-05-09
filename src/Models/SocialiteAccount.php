<?php

declare(strict_types = 1);

namespace Wame\LaravelAuth\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
//use Spatie\Activitylog\LogOptions;
//use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * 
 *
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteAccount onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteAccount ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteAccount withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteAccount withoutTrashed()
 * @mixin \Eloquent
 */
class SocialiteAccount extends Model implements Sortable
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
        'sort_order' => 'integer',
        'user_id' => 'string',
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

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
