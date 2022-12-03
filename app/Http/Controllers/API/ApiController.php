<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function sendResponse($message, $data, $code = 200)
    {
        $response = [
            'code' => $code,
            'message' => $message,
            'data'    => $data,
        ];
        return response()->json($response, $code);
    }
    public function sendError($errorMessages, $errorData = [], $code = 404)
    {
        $response = [
            'code' => $code,
            'message' => $errorMessages,
        ];
        if (!empty($errorData)) {
            $response['data'] = $errorData;
        }
        return response()->json($response, $code);
    }
}
