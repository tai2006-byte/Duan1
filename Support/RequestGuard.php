<?php

class RequestGuard
{
    public static function positiveId($value) {
        if (!is_string($value) && !is_int($value)) return false;
        return filter_var($value, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 2147483647]
        ]);
    }

    public static function notFound($message, $backUrl) {
        http_response_code(404);
        include __DIR__ . '/../views/errors/404.php';
    }

    public static function findRecord($model, $value, $message, $backUrl) {
        $id = self::positiveId($value);
        $record = $id === false ? false : $model->getOne($id);
        if (!$record) self::notFound($message, $backUrl);
        return $record;
    }
}
