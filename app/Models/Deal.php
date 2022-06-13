<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deal extends Model implements HasMedia
{
    use HasFactory, UsesUuid, HasMediaTrait, Searchable;

    const ENABLED = 1;
    const DISABLED = 0;
    const DISABLE_BY_ADMIN = 3;

    protected $fillable = ['user_id', 'category_id', 'subcategory_id', 'title','description','tags', 'boosted','status', 'action'];

    protected $hidden = ['media'];

    protected $appends = ['deal_images', 'is_favourite'];
    /**
     * Get all of the types for the Deal
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function types(): HasMany
    {
        return $this->hasMany(DealType::class)->orderBy('position');

    }

    public function active_types()
    {
        return $this->types->where('status', true);
    }

    public function typeBasic(): HasOne
    {
        return $this->hasOne(DealType::class)->where('type', 'basic');
    }

    public function typeStandard(): HasOne
    {
        return $this->hasOne(DealType::class)->where('type', 'standard');
    }

    public function typePremium(): HasOne
    {
        return $this->hasOne(DealType::class)->where('type', 'premium');
    }

    /**
     * Get all of the requirements for the Deal
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requirements(): HasMany
    {
        return $this->hasMany(DealRequirement::class);
    }

   /**
    * Get the owner that owns the Deal
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
   public function owner(): BelongsTo
   {
       return $this->belongsTo(User::class, 'user_id');
   }

   /**
    * Get the category that owns the Deal
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
   public function category(): BelongsTo
   {
       return $this->belongsTo(Category::class);
   }

   /**
    * Get the subCategory that owns the Deal
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
   public function subCategory(): BelongsTo
   {
       return $this->belongsTo(Category::class, 'subcategory_id');
   }

   public function minimumPrice(){
       return $this->types->min('price');
   }

   public function getTagsAttribute($tags){
       return empty($tags) ? [] : explode(',', $tags);
   }

   public function getTagsList(){
       return implode('', $this->tags);
   }

   public function getIsNewAttribute(){
    return $this->created_at > now()->subDays(7);

   }

    public function toSearchableArray(){
        $array = $this;

        // Applies Scout Extended default transformations:
        $array = $this->toArray();

        // Add an extra attribute:
        $array['owner'] = $this->owner->name;
        $array['category'] = $this->category->name;
        $array['subcategory'] = $this->subCategory->name;

        return $array;
    }

    public function getDealImagesAttribute()
    {
        $data = [];
        foreach ($this->getMedia() as $media) {
            $data[] =  $media->getFullUrl();

        }
        return $data;
    }

    public function sellerOtherDeals(){
        return Deal::where('user_id', $this->user_id)->where('id', '!=', $this->id)->get();
    }

    public function categoryOtherDeals(){
        return Deal::where('category_id', $this->category_id)->where('id', '!=', $this->id)->get();
    }

    public function getIsFavouriteAttribute(){
        $isFavourite = false;
        if(auth()->check()){
             $isFavourite = DealFavourite::where(['user_id' => auth()->user()->id, 'deal_id' => $this->id])->exists();
        }

        return $isFavourite;
    }

}
