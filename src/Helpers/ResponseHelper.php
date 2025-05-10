<?php
namespace Androsyz\MetaforgeGql\Helpers;

use Workerman\Protocols\Http\Response;

class ResponseHelper
{
    public static function json(int $status = 200, array $data = []): Response
    {
        return new Response($status, ['Content-Type' => 'application/json'], json_encode($data));
    }

    public static function success(array $data = [], int $status = 200): Response
    {
        return self::json($status, [
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public static function error(string $message, int $status = 400, array $errors = []): Response
    {
        return self::json($status, [
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ]);
    }
}
