<?php

namespace interactivesolutions\honeycombcore\errors;

use Log;

class HCLog
{
    /**
     * Create emergency response
     *
     * @param string $id
     * @param string $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function emergency(string $id, string $message, int $status = 200)
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
     * @param string $id
     * @param string $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function alert(string $id, string $message, int $status = 200)
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
     * @param string $id
     * @param string $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function critical(string $id, string $message, int $status = 200)
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
     * @param string $id
     * @param string $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function error(string $id, string $message, int $status = 200)
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
     * @param string $id
     * @param string $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function warning(string $id, string $message, int $status = 200)
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
     * @param string $id
     * @param string $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function notice(string $id, string $message, int $status = 200)
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
     * @param string $id
     * @param string $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function info(string $id, string $message, int $status = 200)
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
     * @param string $id
     * @param string $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function debug(string $id, string $message, int $status = 200)
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
     * @param string $id
     * @param string $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function success(string $id, string $message, int $status = 200)
    {
        $response = [
            'success' => true,
            'id'      => $id,
            'message' => $message,
        ];

        Log::info($id . ' : ' . $message);

        return response()->json($response, $status);
    }
}