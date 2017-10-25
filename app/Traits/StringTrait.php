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

    /**
     * Convert Date From Picker
     *
     * @param $text
     * @return string
     */
    public function convertSearchDateTime($text)
    {
        $arrayDate = explode(' ', $text);

        $month = $arrayDate[1];
        if($month == 'Januari' || $month == 'January'){
            $month = 1;
        }else if($month == 'Februari' || $month == 'January'){
            $month = 2;
        }else if($month == 'Maret' || $month == 'March'){
            $month = 3;
        }else if($month == 'April' || $month == 'April'){
            $month = 4;
        }else if($month == 'Mei' || $month == 'May'){
            $month = 5;
        }else if($month == 'Juni' || $month == 'June'){
            $month = 6;
        }else if($month == 'Juli' || $month == 'July'){
            $month = 7;
        }else if($month == 'Agustus' || $month == 'August'){
            $month = 8;
        }else if($month == 'September' || $month == 'September'){
            $month = 9;
        }else if($month == 'Oktober' || $month == 'October'){
            $month = 10;
        }else if($month == 'November' || $month == 'November'){
            $month = 11;
        }else if($month == 'Desember' || $month == 'December'){
            $month = 12;
        }

        $day = $arrayDate[0];
        $year = $arrayDate[2];

        return $month.'/'.$day.'/'.$year;
    }

}