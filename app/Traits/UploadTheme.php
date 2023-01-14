<?php

namespace App\Traits;

trait UploadTheme
{


    function recurse_copy($src, $dst)
    {
        try {
            $dir = opendir($src);
            @mkdir($dst);
            while (false !== ($file = readdir($dir))) {
                if (($file != '.') && ($file != '..')) {
                    if (is_dir($src . '/' . $file)) {
                        $this->recurse_copy($src . '/' . $file, $dst . '/' . $file);
                    } else {
                        copy($src . '/' . $file, $dst . '/' . $file);
                    }
                }
            }
            closedir($dir);
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    function delete_directory($dirname)
    {
        try {
            if (is_dir($dirname)) {
                $dir_handle = opendir($dirname);
                if (!$dir_handle)
                    return false;
                while ($file = readdir($dir_handle)) {
                    if ($file != "." && $file != "..") {
                        if (!is_dir($dirname . "/" . $file))
                            unlink($dirname . "/" . $file);
                        else
                            $this->delete_directory($dirname . '/' . $file);
                    }
                }
                closedir($dir_handle);
                rmdir($dirname);
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

    }
}
