<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ArtistIdentity extends Model
{
    protected $table = 'artist_identity';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';
}
