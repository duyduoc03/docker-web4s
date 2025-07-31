<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Cache\Cache;

class AuthorHelper extends Helper
{
    public function getAuthors($params = [], $lang = null) 
    {
        $result = [];

        $params[FILTER][STATUS] = 1;
        $params[FIELD] = FULL_INFO;
        if(empty($lang) || empty($languages[$lang])) $lang = LANGUAGE;
        
        $params[FILTER][LANG] = $lang;

        $authors = TableRegistry::get('Authors')->queryListAuthors($params)->toArray();

        if(!empty($authors)){
            foreach($authors as $k => $author){
                $result[$k] = TableRegistry::get('Authors')->formatDataAuthorDetail($author, $lang);
            }
        }
        
        return $result;
    }

    public function getAuthorsSimple($lang = null) 
    {
        $languages = TableRegistry::get('Languages')->getList();
        if(empty($lang) || empty($languages[$lang])) $lang = LANGUAGE;

        $result = TableRegistry::get('Authors')->getAuthorsSimple($lang);
        
        return $result;
    }

    public function getListAuthorsForDropdown($lang = null) 
    {
        $result = TableRegistry::get('Authors')->getAuthorsSimple($lang);
        $result = !empty($result) ? Hash::combine($result, '{n}.id', '{n}.full_name') : [];

        return $result;
    }

    public function getDetailAuthor($author_id = null, $lang = null)
    {
        if(empty($author_id)) return [];

        $languages = TableRegistry::get('Languages')->getList();
        if(empty($lang) || empty($languages[$lang])) $lang = LANGUAGE;

        $table = TableRegistry::get('Authors');

        $result = $table->getDetailAuthor($author_id, $lang , [
            FILTER => [
                STATUS => 1
            ]
        ]);        
        $result = !empty($result) ? $table->formatDataAuthorDetail($result, $lang) : [];

        return $result;
    }
}
