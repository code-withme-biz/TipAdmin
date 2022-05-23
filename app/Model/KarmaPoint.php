<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class KarmaPoint extends Model
{
    protected $table = 'user_kps';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';
}
