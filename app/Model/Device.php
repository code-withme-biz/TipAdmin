<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $table = 'device';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';
}
