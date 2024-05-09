<?php

declare(strict_types = 1);

namespace Wame\LaravelAuth\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
//use Spatie\Activitylog\LogOptions;
//use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * 
 *
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserPasswordReset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPasswordReset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPasswordReset onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPasswordReset ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|UserPasswordReset query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPasswordReset withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPasswordReset withoutTrashed()
 * @mixin \Eloquent
 */
class UserPasswordReset extends Model implements Sortable
{
    use HasFactory;
    use HasUlids;
    //use LogsActivity;
    use Notifiable;
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
        'reset_method' => 'integer',
        'value' => 'string',
        'expired_at' => 'datetime',
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
