<?php

namespace App\Models;

use App\Models\Category;
use App\Traits\UsesUuid;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model implements HasMedia
{
    const ACTIVE=1;
    const INACTIVE=0;

    use HasFactory, UsesUuid, HasMediaTrait,Searchable;

    protected $guarded = [];
    protected $cast = ['active_until' => 'timestamp'];
    protected $hidden = ['media', 'category'];
    protected $appends = [ 'project_images', 'UserProfileImage' ];


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
        $array = $this->toArray();

        // Applies Scout Extended default transformations:
        $array = $this->transform($array);

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




}
