<?php

namespace App\Models;

use App\Models\Deal;
use App\Models\Order;
use App\Traits\UsesUuid;
use App\Models\DealFavourite;
use Laravel\Scout\Searchable;
use Ramsey\Uuid\Type\Integer;
// use App\Notifications\VerifyEmailNotification;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
// use App\Notifications\SendPasswordResetNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use App\Models\CategoryNotificationSubscription;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements HasMedia, MustVerifyEmail
{
    use HasFactory, Notifiable, Searchable;
    use UsesUuid, HasMediaTrait, HasApiTokens;

    const DISABLE = 0;
    const ACTIVE = 1;
    const VACATION = 2;
    const SUSPENDED = 3;
    const ROLE_TYPE_USER = 1;
    const ROLE_TYPE_ADMIN = 2;
    const ROLE_TYPE_SUPERADMIN = 3;

    protected $fillable = [
        'name', 'username', 'phone', 'email', 'password', 'email_verified_at',
        'pid', 'country_id', 'gender', 'dob', 'language',
        'status', 'registration_completed','short_description',
        'full_description', 'skillsets', 'languages'

    ];

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_seen' => 'datetime',
        'dob' => 'date',
        'languages' => 'array'
    ];

    public function setSkillsetsAttribute($skillset){
        try {
            $this->attributes['skillsets'] = implode(',', $skillset);
        } catch (\Throwable $th) {
            $this->attributes['skillsets'] = implode($skillset, ',');
        }
    }

    public function getSkillsetsAttribute($skillset){
        try {
            return empty($skillset) ? [] : explode(',', $skillset);
        } catch (\Throwable $th) {
            return empty($skillset) ? [] : explode($skillset, ',');
        }
    }

    public function setLanguagesAttribute($languages){
        $this->attributes['languages'] = json_encode($languages);
    }

    public function getLanguagesAttribute($languages){
        return empty($languages) ? []: json_decode($languages, true);
    }

    // public function sendPasswordResetNotification($token)
    // {
    //     $this->notify(new sendPasswordResetNotification($token));
    // }

    // public function sendEmailVerificationNotification(){
    //     $this->notify(new VerifyEmailNotification($this));
    // }


    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class)->with('project');
    }

    public function activeDeals(): HasMany
    {
        return $this->deals()->where('status', true);
    }

    public function dealFavourites(): HasMany
    {
        return $this->hasMany(DealFavourite::class);
    }

    public function projectFavourites(): HasMany
    {
        return $this->hasMany(ProjectFavourite::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function activeProjects(): HasMany
    {
        return $this->projects()->where('status', true);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('profile')
            ->singleFile();
    }

    public function isFavouriteDeal($deal_id){
        return in_array($deal_id, $this->dealFavourites->pluck('id'));
    }

    public function getUserProfileImageAttribute()
    {
        return  auth()->user()->getMedia();

    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'freelancer_id');
    }

    public function ordersOwner(): HasMany
    {
        return $this->hasMany(Order::class, 'owner_id');
    }

    public function earnings(): HasMany
    {
        return $this->hasMany(OrderTransaction::class, 'freelancer_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(OrderTransaction::class, 'owner_id');
    }

    public function totalEarnings()
    {
        return $this->hasMany(OrderTransaction::class, 'freelancer_id')->sum('amount_expected');
    }

    /**
     * Get all of the categorySubscription for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categoryNotificationSubscription(): HasMany
    {
        return $this->hasMany(CategoryNotificationSubscription::class);
    }


    public function categoryNotificationSubscriptionId()
    {
        return $this->categoryNotificationSubscription->pluck('category_id');
    }
}
