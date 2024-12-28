<?php

if (!function_exists('success_response')) {
    function success_response($message, $data): array
    {
        return [
            'message' => $message,
            'status' => 'success',
            'data' => $data,
        ];
    }
}

if (!function_exists('warning_response')) {
    function warning_response($message, $data): array
    {
        return [
            'message' => $message,
            'status' => 'warning',
            'data' => $data,
        ];
    }
}

if (!function_exists('failed_response')) {
    function failed_response($message): array
    {
        return [
            'message' => $message,
            'status' => 'danger',
        ];
    }
}

if (!function_exists('api_status_ok')) {
    function api_status_ok($data, $message = "data successfully retrieved", $code = 200)
    {
        $response = [
            'url' => url()->full(),
            'method' => request()->getMethod(),
            'code' => $code,
            'message' => $message,
            'payload' => $data
        ];

        return response($response, $code);
    }
}

if (!function_exists('api_status_warning')) {
    function api_status_warning($message = "Something went wrong", $code = 400)
    {
        $response = [
            'url' => url()->full(),
            'method' => request()->getMethod(),
            'request' => request()->except(['password', 'client']),
            'code' => $code,
            'message' => $message,
        ];

        return response($response, $code);
    }
}

if (!function_exists('api_status_error')) {
    function api_status_error(\Exception $exception)
    {
        throw_custom_exception($exception);

        $response = [
            'url' => url()->full(),
            'method' => request()->getMethod(),
            'request' => request()->except(['password', 'file', 'client']),
            'code' => $exception->getCode()
        ];

        if (config('app.debug')) {
            $response = array_merge($response, [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);
        } else {
            $acceptedMessage = ['Invalid ID'];

            $response['message'] = in_array($exception->getMessage(), $acceptedMessage) ? $exception->getMessage() : 'Internal Server Error';
        }

        return response($response, 500);
    }
}
