<?php

namespace App\Http\Controllers\BPJS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiBPJSController extends Controller
{
    public function sendResponse($message, $data, $code = 200)
    {
        $response = [
            'metadata' => [
                'code' => $code,
                'message' => $message,
            ],
            'response' => $data,
        ];
        return response()->json($response, $code);
    }
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'metadata' => [
                'code' => $code,
                'message' => $error,
            ],
        ];
        if (!empty($errorMessages)) {
            $response['response'] = $errorMessages;
        }
        return response()->json($response, $code);
    }
}
