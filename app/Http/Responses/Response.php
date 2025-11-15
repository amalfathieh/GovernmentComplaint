<?php


namespace App\Http\Responses;


use Illuminate\Http\JsonResponse;

class Response
{
    public static function Success( $data, $message , $statusCode=200) :JsonResponse
    {
        return response()->json([
            'data' =>$data,
            'success' => true,
            'message' => $message,
        ], $statusCode);
    }

    public static function Error( $message , $statusCode=500) :JsonResponse
    {
        return response()->json([
            'data' =>null,
            'success' => false,
            'message' => $message,
        ], $statusCode);
    }
}
