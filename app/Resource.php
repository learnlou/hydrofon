<?php

namespace App;

use App\Scopes\GroupPolicyScope;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'is_facility',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_facility' => 'boolean',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new GroupPolicyScope());
    }

    /**
     * Bookings of resource.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookings()
    {
        return $this->hasMany(\App\Booking::class);
    }

    /**
     * Categories the resource belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(\App\Category::class)
                    ->orderBy('name');
    }

    /**
     * Buckets the resource belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function buckets()
    {
        return $this->belongsToMany(\App\Bucket::class);
    }

    /**
     * Get all identifiers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function identifiers()
    {
        return $this->morphMany(\App\Identifier::class, 'identifiable');
    }

    /**
     * Groups the resource belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups()
    {
        return $this->belongsToMany(\App\Group::class);
    }
}
