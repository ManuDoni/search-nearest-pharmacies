<?php
namespace Tests;

class JsonRpcRequestFactory
{
    public static function create($method, array $params, $id = 1)
    {
        $request = [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params
        ];
        //if id is null then it means the request is a notification and the id MUST not be present
        if ($id !== null) {
            $request['id'] = $id;
        }

        return $request;
    }
}