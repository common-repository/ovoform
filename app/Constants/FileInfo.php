<?php

namespace Ovoform\Constants;

class FileInfo
{

    /*
    |--------------------------------------------------------------------------
    | File Information
    |--------------------------------------------------------------------------
    |
    | This trait basically contain the path of files and size of images.
    | All information are stored as an array. Developer will be able to access
    | this info as method and property using FileManager class.
    |
    */

    public function fileInfo()
    {
        $data['verify'] = [
            'path'      => 'verify'
        ];
        $data['default'] = [
            'path'      => 'images/default.png',
        ];
        $data['logoIcon'] = [
            'path'      => 'images/logoIcon',
        ];
        $data['favicon'] = [
            'size'      => '128x128',
        ];
        $data['extensions'] = [
            'path'      => 'admin/images/extensions',
        ];
        return $data;
    }
}
