<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'name',
        'role',
        'active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'password' => 'hashed',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the orders for the user.
     *
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope a query to only include active users.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to search by name or email.
     *
     * @param Builder $query
     * @param string|null $search
     * @return Builder
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    /**
     * Scope a query to sort by specified column.
     *
     * @param Builder $query
     * @param string $sortBy
     * @param string $direction
     * @return Builder
     */
    public function scopeSortBy(Builder $query, string $sortBy = 'created_at', string $direction = 'asc'): Builder
    {
        $allowedColumns = ['name', 'email', 'created_at'];

        if (!in_array($sortBy, $allowedColumns)) {
            $sortBy = 'created_at';
        }

        return $query->orderBy($sortBy, $direction);
    }

    /**
     * Scope a query to include orders count.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithOrdersCount(Builder $query): Builder
    {
        return $query->withCount('orders');
    }

    /*
    |--------------------------------------------------------------------------
    | Static Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get filtered and paginated users.
     *
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getFilteredUsers(array $filters = [])
    {
        return static::query()
            ->active()
            ->search($filters['search'] ?? null)
            ->sortBy($filters['sortBy'] ?? 'created_at')
            ->withOrdersCount()
            ->paginate(
                $filters['perPage'] ?? 15,
                ['*'],
                'page',
                $filters['page'] ?? 1
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if user can edit another user.
     *
     * @param User $targetUser
     * @return bool
     */
    public function canEdit(User $targetUser): bool
    {
        // Administrator can edit any user
        if ($this->role === 'administrator') {
            return true;
        }

        // Manager can only edit users with role 'user'
        if ($this->role === 'manager') {
            return $targetUser->role === 'user';
        }

        // User can only edit themselves
        return $this->id === $targetUser->id;
    }
}
