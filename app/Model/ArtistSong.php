<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ArtistSong extends Model
{
    protected $table = 'artist_songs';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';
}