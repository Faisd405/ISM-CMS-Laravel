<?php

namespace App\Traits;

trait Helper
{
    function isImageLink($url)
    {
        // Check if the URL is a valid link
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // extension image in url
        $ext = pathinfo($url, PATHINFO_EXTENSION);

        // Check if the URL is a valid image
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            return false;
        }

        return true;
    }
}
