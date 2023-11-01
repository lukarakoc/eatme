<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait FileHandler
{
    public static function storeFile($file, $directory = 'unknown'): string
    {
        if (!is_null($file)) {
            if (env('OS') == 'macos' || env('OS') == 'linux') {
                $destinationPath = '/images/' . $directory . '/';
            } else {
                $destinationPath = '\\images' . $directory . '\\';
            }
            $fileName = $file->getClientOriginalName();
            $filenameCover = time() . $fileName;
            $file->move(public_path() . $destinationPath, $filenameCover);
            return $destinationPath . $filenameCover;
        } else {
            abort(404, 'File not found');
        }
    }

    public static function storeFileFromBase64($file, $directory = 'unknown'): string
    {
        if (!is_null($file)) {
            $safeName = Str::random(30) . '.' . 'png';
            if (env('OS') == 'macos' || env('OS') == 'linux') {
                $dir = '/images/' . $directory . '/' . $safeName;
            } else {
                $dir = '\\images\\' . $directory . '\\' . $safeName;
            }
            $fileStream = fopen(public_path() . $dir, "wb");
            fwrite($fileStream, base64_decode(explode(',', $file)[1]));
            fclose($fileStream);
            return $dir;
        } else {
            abort(404, 'File not found');
        }
    }
}
