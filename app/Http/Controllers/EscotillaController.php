<?php

namespace App\Http\Controllers;

class EscotillaController extends Controller
{
    protected function successResponse($data)
    {
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    protected function errorResponse($message, $code)
    {
        return response()
            ->json([
                'success' => false,
                'message' => $message,
                'code' => $code
            ]);
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //
}
