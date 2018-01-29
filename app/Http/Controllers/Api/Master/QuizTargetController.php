<?php

namespace App\Http\Controllers\Api\Master;

use App\QuizTarget;
use App\Filters\QuizTargetFilters;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;

class QuizTargetController extends Controller
{

    public function getData($param)
    {
        $target = QuizTarget::get();

        return response()->json($target);
    }

    public function getDataWithFilters(QuizTargetFilters $filters)
    {
        $data = QuizTarget::filter($filters)->get();

        return response()->json($data);
    }

}
