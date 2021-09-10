<?php

namespace App\Models;

use App\Models\Category;
use App\Models\Proposal;
use App\Traits\UsesUuid;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model implements HasMedia
{
    use HasFactory, UsesUuid, HasMediaTrait,Searchable;

    // const ENABLED = 1;
    // const DISABLED = 0;

    const CLOSE = 0;
    const OPEN = 1;
    const INPROGRESS = 2;
    const COMPLETED = 3;
    const ABANDONED = 4;
    const IN_CREATION_MODE = 5;
    const IN_EDITING_MODE = 6;


    protected $guarded = [];
    protected $cast = ['active_until' => 'timestamp'];
    protected $hidden = ['media', 'category'];
//    protected $appends = [ 'status_display','project_images', 'UserProfileImage' ,'is_favourite'];
    protected $appends = [ 'UserProfileImage' ,'is_favourite', 'project_images', 'status_display'];

    protected function statusDisplay(){
        return  [
            $this::CLOSE => 'closed',
            $this::OPEN => 'open',
            $this::INPROGRESS => 'in_progress',
            $this::COMPLETED => 'completed',
            $this::ABANDONED => 'abandon',
        ];
    }


    public function setTitleAttribute($value){
        $this->attributes['title'] = ucwords($value);
    }

    public function getTagsAttribute($tags){
       return explode(',', $tags);
    }

     /**
    * Get the category that owns the Project
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
   public function category(): BelongsTo
   {
       return $this->belongsTo(Category::class);
   }

   /**
    * Get the subCategory that owns the Project
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
   public function subCategory(): BelongsTo
   {
       return $this->belongsTo(Category::class, 'subcategory_id');
   }

    /**
    * Get the owner that owns the Project
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
   public function owner(): BelongsTo
   {
       return $this->belongsTo(User::class,'user_id', 'id');
   }

    public function toSearchableArray(){
        $array = $this;

        // Applies Scout Extended default transformations:
        $array = $this->toArray();

        // Add an extra attribute:
        $array['owner'] = $this->owner->name;
        $array['category'] = $this->category->name;
        $array['subcategory'] = $this->subCategory->name ?? 'error';

        return $array;
    }

    public function getProjectImagesAttribute()
    {
        $data = [];
        foreach ($this->getMedia() as $media) {
            $data[] =  $media->getFullUrl();

        }
        return $data;
    }

    public function getUserProfileImageAttribute()
    {
        // return $this->owner->id;
        // return $medias = $this->owner();
        // return $medias->getFirstMediaUrl();

    }

    public function getIsFavouriteAttribute(){
        if(!auth()->check()){
            return false;
        }
        return ProjectFavourite::where(['user_id' => auth()->user()->id, 'project_id' => $this->id])->exists();
    }

    public function getStatusDisplayAttribute(){
        return  $this->statusDisplay()[$this->status];
    }

    public function getServiceFeeAndVatAttribute(){
        return $this->price * 20/100;
    }

    public function getTotalAttribute(){
        return $this->price + $this->serviceFeeAndVat;
    }

    /**
     * Get all of the proposals for the Project
     *
     * @return \Illumin            ate\Database\Eloquent\Relations\HasMany
     */
    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }




}
