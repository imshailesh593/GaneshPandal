<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable(['image_path', 'caption', 'sort_order'])]
class GalleryPhoto extends Model
{
    public function url(): string
    {
        return Storage::disk('public')->url($this->image_path);
    }
}
