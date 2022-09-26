<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

/**
 * -----------------------------------------------------------------------
 * Collection of general helper functions
 * -----------------------------------------------------------------------
 */
function storeFile($file, String $parent = '')
{
    return Storage::disk('public')
        ->put($parent, new File($file));
}
