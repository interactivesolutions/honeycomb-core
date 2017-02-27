<?php

namespace interactivesolutions\honeycombcore\errors;

use Log;

class HCLog
{
    /**
     * Create emergency response
     *
     * @param $id
     * @param $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function emergency($id, $message, $status = 200)
    {
        $response = [
            'success' => false,
            'id'      => $id,
            'message' => $message,
        ];

        Log::error($id . ' : ' . $message);

        return response()->json($response, $status);
    }

    /**
     * Create alert response
     *
     * @param $id
     * @param $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function alert($id, $message, $status = 200)
    {
        $response = [
            'success' => false,
            'id'      => $id,
            'message' => $message,
        ];

        Log::error($id . ' : ' . $message);

        return response()->json($response, $status);
    }

    /**
     * Create critical error response
     *
     * @param $id
     * @param $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function critical($id, $message, $status = 200)
    {
        $response = [
            'success' => false,
            'id'      => $id,
            'message' => $message,
        ];

        Log::critical($id . ' : ' . $message);

        return response()->json($response, $status);
    }

    /**
     * Create error response
     *
     * @param $id
     * @param $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function error($id, $message, $status = 200)
    {
        $response = [
            'success' => false,
            'id'      => $id,
            'message' => $message,
        ];

        Log::error($id . ' : ' . $message);

        return response()->json($response, $status);
    }

    /**
     * Create warning response
     *
     * @param $id
     * @param $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function warning($id, $message, $status = 200)
    {
        $response = [
            'success' => false,
            'id'      => $id,
            'message' => $message,
        ];

        Log::warning($id . ' : ' . $message);

        return response()->json($response, $status);
    }

    /**
     * Create notice response
     *
     * @param $id
     * @param $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function notice($id, $message, $status = 200)
    {
        $response = [
            'success' => false,
            'id'      => $id,
            'message' => $message,
        ];

        Log::info($id . ' : ' . $message);

        return response()->json($response, $status);
    }

    /**
     * Create info response
     *
     * @param $id
     * @param $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function info($id, $message, $status = 200)
    {
        $response = [
            'success' => false,
            'id'      => $id,
            'message' => $message,
        ];

        return response()->json($response, $status);
    }

    /**
     * Create debug response
     *
     * @param $id
     * @param $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function debug($id, $message, $status = 200)
    {
        $response = [
            'success' => false,
            'id'      => $id,
            'message' => $message,
        ];

        Log::error($id . ' : ' . $message);

        return response()->json($response, $status);
    }

    /**
     * Create success response
     *
     * @param $id
     * @param $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($id, $message, $status = 200)
    {
        $response = [
            'success' => true,
            'id'      => $id,
            'message' => $message,
        ];

        Log::info($id . ' : ' . $message);

        return response()->json($response, $status);
    }

    /**
     * Log the message and stop
     *
     * @param $message
     */
    public function stop($message)
    {
        Log::info($message);
        exit();
    }
}