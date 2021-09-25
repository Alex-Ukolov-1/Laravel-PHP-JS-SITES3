<?php

namespace App\Repositories;

use App\Models\Transaction;

class TransactionTypeRepository
{
    public function list()
    {
        return Transaction::$types;
    }

    public function fullList()
    {
        return Transaction::$types;
    }

    static public function getById($id)
    {
        return Transaction::$types[$id];
    }

    static function getDefaultId()
    {
        return null;
    }
}
