<?php

namespace App\Http\Controllers\Api\Master;

use App\Faq;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FaqController extends Controller
{
    public function getFaq(){

        $faq = Faq::whereNull('deleted_at')->select('question', 'answer')->get();

        if ($faq->count() < 1) {
            return response()->json(
                [
                'status' => false,
                'message' => 'No Data Found',
                ],
                500
            );
        }
        return response()->json($faq);

    }
}
