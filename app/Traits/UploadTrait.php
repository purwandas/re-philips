<?php

namespace App\Traits;

trait UploadTrait {

    /**
     * Trait for Image Upload
     *
     * @param $image, $imageFolder
     * @return string
     */
    public function imageUpload($image, $imageFolder)
    {    
        $image_new = time().'.'.$image->getClientOriginalExtension();
        $image->move(public_path('image/'.$imageFolder), $image_new);        

        // Back result url asset + filename (For insert/update data in model)
        return asset('image').'/'.$imageFolder.'/'.$image_new;
    }

}