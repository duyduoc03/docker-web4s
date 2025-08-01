<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class AuthorAdminHelper extends Helper
{   
    public function getDetailAuthor($author_id = null, $params = [])
    {
        $table = TableRegistry::get('Authors');
        $author_info = $table->getDetailAuthor($author_id, $params);

        $result = [];
        if(!empty($author_info)){
        	$result = $table->formatDataAuthorDetail($author_info);
        }
        
        return $result;
    }


    public function getListAuthorsForDropdown($lang = null)
    {    
        $result = TableRegistry::get('Authors')->getAuthorsSimple($lang);
        $result = !empty($result) ? Hash::combine($result, '{n}.id', '{n}.full_name') : [];
        return $result;
    }
}
