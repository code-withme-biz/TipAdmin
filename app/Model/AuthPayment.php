<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AuthPayment extends Model
{
    protected $table = 'payment_authorised';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';
}
