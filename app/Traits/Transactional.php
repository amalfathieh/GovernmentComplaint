<?php


namespace App\Traits;


use Illuminate\Support\Facades\DB;

trait Transactional
{
    public function runInTransaction(callable $callback)
    {
        return DB::transaction($callback);
    }
}
