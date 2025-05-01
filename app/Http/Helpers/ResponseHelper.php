<?php
namespace App\http\Helpers;

class ResponseHelper
{
    public static function success($message, $data = [], $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'errors' => null,
            'code' => $code
        ], $code);
    }

    public static function error($message, $errors = [], $code = 400)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => null,
            'errors' => $errors,
            'code' => $code
        ], $code);
    }
}
