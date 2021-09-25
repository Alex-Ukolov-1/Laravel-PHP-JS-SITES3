<?php

namespace App\Repositories;

class StatusRepository
{
    static private $data = ['1' => 'Активно', '0' => 'Неактивно'];

    public function list()
    {
        return $this::$data;
    }

    public function fullList()
    {
        return $this::$data;
    }

    static public function getById($id)
    {
        return self::$data[$id];
    }

    static function getDefaultId()
    {
        return null;
    }
}
