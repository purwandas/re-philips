<?php

namespace App\Traits;

trait StringTrait {

    /**
     * Replace single quote ' to #39; in text => for escaping character
     *
     * @param $text
     * @return string
     */
    public function replaceSingleQuote($text)
    {    
        return str_replace("'", "&#39;", $text);
    }

    /**
     * Replace space for uploading images
     *
     * @param $text
     * @return string
     */
    public function replaceSpace($text)
    {    
        return str_replace(' ', '', $text);
    }

    /**
     * Replace space for uploading images
     *
     * @param $text
     * @return string
     */
    public function replaceDash($text)
    {    
        return str_replace('-', '', $text);
    }

    /**
     * Give random path
     *
     * @return string
     */
    public function getRandomPath()
    {    
        return str_random(30).time().str_random(30);
    }

    /**
     * Convert Date Time
     *
     * @param $text
     * @return string
     */
    public function convertDateTime($text)
    {    
        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $arrayDateTime = explode(' ', $text);
        $arrayDate = explode('-', $arrayDateTime[0]);        

        return $arrayDate[2]." ".$months[(int)$arrayDate[1]-1]." ".$arrayDate[0]." - ".$arrayDateTime[1];
    }

}