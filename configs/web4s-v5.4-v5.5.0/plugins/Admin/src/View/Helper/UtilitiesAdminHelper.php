<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Exception\Exception;
use Cake\I18n\Date;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class UtilitiesAdminHelper extends Helper
{    

    public function convertIntgerToDateString($int = null)
    {
        if(empty($int)) return null;

        try{
            $result = date('d/m/Y', intval($int));
        }catch (Exception $e) {
            return null;
        }

        return $result;
    }

    public function convertIntgerToDateTimeString($int = null, $format = 'H:i - d/m/Y')
    {
        if(empty($int)) return null;
        if(empty($format)) $format = 'H:i - d/m/Y';

        try{
            $result = date(strval($format), intval($int));
        }catch (Exception $e) {
            return null;
        }

        return $result;
    }

    public function isJson($json_str = null)
    {
        return is_string($json_str) && is_array(json_decode($json_str, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    public function getTypeFileByUrl($url_file = null)
    {
        if(empty($url_file) || !is_string($url_file)) return '';
        $ext = pathinfo($url_file, PATHINFO_EXTENSION);

        $type = '';
        switch($ext){
            case 'jpg':
            case 'png':
            case 'gif':
            case 'jpeg':
            case 'svg':
            case 'bmp':
                $type = 'image';
            break;

            case 'xlsx':
            case 'xlsm':
            case 'xls':
                $type = 'excel';
            break;

            case 'doc':
            case 'docx':
                $type = 'word';
            break;

            case 'pdf':
                $type = 'pdf';
            break;

            case 'mp3':
            case 'flac':
            case 'm4a':
                $type = 'audio';
            break;

            case 'mp4':
            case 'swf':
            case 'avi':
            case '3gp':
            case 'mov':
            case 'wmv':
            case 'webm':
                $type = 'video';
            break;
        }

        return $type;
    }

    public function getArrayKeys($list = [])
    {
        if(!is_array($list)) return [];

        return array_keys($list);
    }

    public function getUrlWebsite()
    {
        $request = $this->getView()->getRequest();
        return $request->scheme() . '://' . $request->host();
    }

    public function parseFileSize($bytes = null, $decimals = 2)
    {
        return TableRegistry::get('Utilities')->parseFileSize($bytes, $decimals);
    }

    public function hashCombineData($data = [], $key = '', $key_value = '')
    {
        if(empty($data)) return [];
        if(empty($key) || gettype($key) != 'string') return [];

        $value = '{n}';
        if(!empty($key_value)) $value = $value . ".$key_value";
        
        return Hash::combine($data, "{n}.$key", $value);
    }

    public function getThumbs($url = null, $size = null, $source = null)
    {
        $result = [];
        if(empty($url) || empty($size) || !in_array($size, [50, 150, 250, 350, 500, 720])) return $result;
        
        $url = ltrim($url, '/');
        $url = '/' . $url;
        
        $path_info = pathinfo($url);
        $extension = !empty($path_info['extension']) ? $path_info['extension'] : '';
        $filename = !empty($path_info['filename']) ? $path_info['filename'] : '';
        
        if(empty($extension)) return $result;

        switch ($source) {
            case 'template':
                $url = $this->str_replace_first('media', 'media_thumbs', $url);
                $path = explode('/', $url);
                break;
            
            default:
                $path = explode('/', $url);
                $path[1] = 'thumbs';
                break;
        }

        $num_last =  count($path) - 1;
        $path[$num_last] = $filename . '_thumb_'. $size . '.'. $extension;
        return implode('/', $path);
    }

    public function hashExtractData($data = [], $field = 'name')
    {
        if(empty($data)) return [];
        if(empty($field) || gettype($field) != 'string') return [];

        return Hash::extract($data, '{n}.'. $field);
    }
}
