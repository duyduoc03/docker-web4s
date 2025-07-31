<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class TagHelper extends Helper
{

    /** Lấy danh sách tag trên page
     * 
     * {assign var = data value = $this->Tag->getTagsOfPage()}
     * 
    */
    public function getTagsOfPage()
    {
        $language = null;
        if(!defined('PAGE_TYPE') || !defined('PAGE_RECORD_ID')) return [];
        if(empty(PAGE_TYPE) || empty(PAGE_RECORD_ID)) return [];
        if(!in_array(PAGE_TYPE, [PRODUCT_DETAIL, ARTICLE_DETAIL])) return [];

        $tags_id = TableRegistry::get('TagsRelation')->find()->where([
            'type' => PAGE_TYPE,
            'foreign_id' => PAGE_RECORD_ID
        ])->select(['tag_id'])->toArray();
        $tags_id = Hash::extract($tags_id, '{n}.tag_id');
        
        
        $result = [];
        if(!empty($tags_id)){
            $result = TableRegistry::get('Tags')->queryListTags([
                FILTER => [
                    'ids' => $tags_id
                ]
            ])->toArray();
        }

        return $result;
    }


    /** Lấy chi tiết thẻ tag thông qua id
     * 
     * $id (*): ID tag (int) - ví dụ: {PAGE_TAG_ID}
     * $params['lang'] (*): Mã ngôn ngữ(string)  - ví dụ: 'en'| 'vi'
     * 
     * {assign var = data value = $this->Tag->getInfoTag({PAGE_TAG_ID}, [
     *      {LANG} => LANGUAGE
     * ])}
     * 
    */
    public function getInfoTag($id = null, $params = [])
    {
        if(empty($id)) return [];
        $table = TableRegistry::get('Tags');
        $lang = !empty($params[LANG]) ? $params[LANG] : TableRegistry::get('Languages')->getDefaultLanguage();
    
        $tag_info = $table->getDetailTag($id, $lang);

        return $tag_info;
    }
}
