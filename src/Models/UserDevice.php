<?php

namespace Wame\LaravelAuth\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use Wame\LaravelAuth\Database\Factories\UserDeviceFactory;
use Wame\User\Models\User;

/**
 *
 *
 * @property string $id
 * @property string $user_id
 * @property string|null $name
 * @property array|null $data
 * @property string|null $device_token
 * @property string|null $version
 * @property Carbon|null $last_login
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read User $user
 * @method static Builder|UserDevice newModelQuery()
 * @method static Builder|UserDevice newQuery()
 * @method static Builder|UserDevice onlyTrashed()
 * @method static Builder|UserDevice query()
 * @method static Builder|UserDevice whereCreatedAt($value)
 * @method static Builder|UserDevice whereData($value)
 * @method static Builder|UserDevice whereDeletedAt($value)
 * @method static Builder|UserDevice whereDeviceToken($value)
 * @method static Builder|UserDevice whereId($value)
 * @method static Builder|UserDevice whereLastLogin($value)
 * @method static Builder|UserDevice whereName($value)
 * @method static Builder|UserDevice whereUpdatedAt($value)
 * @method static Builder|UserDevice whereUserId($value)
 * @method static Builder|UserDevice whereVersion($value)
 * @method static Builder|UserDevice withTrashed()
 * @method static Builder|UserDevice withoutTrashed()
 * @method static UserDeviceFactory factory($count = null, $state = [])
 * @mixin \Eloquent
 */
class UserDevice extends Model
{
    use HasApiTokens;
    use HasUlids;
    use SoftDeletes;
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'data' => 'array',
        'last_login' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function newFactory(): Factory|UserDeviceFactory
    {
        return UserDeviceFactory::new();
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('wame-auth.model'), 'user_id');
    }
}
