<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ArtistWallet extends Model
{
    protected $table = 'artist_wallet';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';
}
