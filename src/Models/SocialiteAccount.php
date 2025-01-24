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
 * @property string $id
 * @property int|null $sort_order
 * @property string $user_id
 * @property string $socialite_provider_id
 * @property string $provider_user_id
 * @property string|null $provider_user_token
 * @property string|null $last_login_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteAccount whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteAccount whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteAccount whereProviderUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteAccount whereProviderUserToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteAccount whereSocialiteProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteAccount whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteAccount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteAccount whereUserId($value)
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
