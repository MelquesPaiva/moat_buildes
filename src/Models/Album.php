<?php

namespace Source\Models;

use Source\Core\Model;

/**
 * @package Source\Models
 */
class Album extends Model
{
    /**
     * Album Constructor
     */
    public function __construct()
    {
        parent::__construct(
            "albums",
            [],
            ["artist", "album_name", "year"]
        );
    }
}
