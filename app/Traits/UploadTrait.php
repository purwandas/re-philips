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

    /**
     * Trait for Image Upload With Name
     *
     * @param $image, $imageFolder
     * @return string
     */
    public function imageUploadName($image, $imageFolder, $imageName)
    {
        $image_new = $imageName.'-'.time().'.'.$image->getClientOriginalExtension();
        $image->move(public_path('image/'.$imageFolder), $image_new);

        // Back result url asset + filename (For insert/update data in model)
        return asset('image').'/'.$imageFolder.'/'.$image_new;
    }

    /**
     * Trait for Image Upload With Name (For POSM upload multi images)
     *
     * @param $image, $imageFolder
     * @return string
     */
    public function posmUploadName($image, $imageFolder, $imageName)
    {
        $image_new = $imageName.'-'.str_random(8).'.'.$image->getClientOriginalExtension();
        $image->move(public_path('image/'.$imageFolder), $image_new);

        return true;
    }

    /**
     * Trait for File Upload
     *
     * @param $image, $imageFolder
     * @return string
     */
    public function fileUpload($file, $fileFolder)
    {
        $file_new = time().'.'.$file->getClientOriginalExtension();
        $file->move(public_path('file/'.$fileFolder), $file_new);

        // Back result url asset + filename (For insert/update data in model)
        return asset('file').'/'.$fileFolder.'/'.$file_new;
    }

    /**
     * Trait for Image Upload
     */
    public function getUploadPath($image, $imageFolder)
    {
        $image_new = time().'.'.$image->getClientOriginalExtension();
//        $image->move(public_path('image/'.$imageFolder), $image_new);

        // Back result url asset + filename (For insert/update data in model)
        return asset('image').'/'.$imageFolder.'/'.$image_new;
    }

    public function upload($image, $imageFolder, $imageName)
    {
        $image->move(public_path('image/'.$imageFolder), $imageName);

        return true;
    }

    public function getUploadPathName($image, $imageFolder, $imageName)
    {
        $image_new = $imageName.'-'.time().'.'.$image->getClientOriginalExtension();

        // Back result url asset + filename (For insert/update data in model)
        return asset('image').'/'.$imageFolder.'/'.$image_new;
    }

    public function getUploadPathNameFile($file, $fileFolder, $fileName)
    {
        $file_new = $fileName.'-'.time().'.'.$file->getClientOriginalExtension();

        // Back result url asset + filename (For insert/update data in model)
        return asset('file').'/'.$fileFolder.'/'.$file_new;
    }

    public function uploadFile($file, $fileFolder, $fileName)
    {
        $file->move(public_path('file/'.$fileFolder), $fileName);

        return true;
    }

}