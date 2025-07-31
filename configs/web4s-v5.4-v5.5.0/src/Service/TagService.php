<?php
namespace App\Service;

use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Exception\Exception;

class TagService extends AppService
{
    public function getIdOrCreateNewTag($tag = '', $lang = '')
    {
        $utilities = TableRegistry::get('Utilities');

        if(empty($tag)) return $utilities->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        if(empty($lang)) $lang = TableRegistry::get('Languages')->getDefaultLanguage();

        $table = TableRegistry::get('Tags');

        $tag_info = $table->find()->where([
            'name' => $tag
        ])->select(['id', 'name'])->first();

        $tag_id = !empty($tag_info['id']) ? intval($tag_info['id']) : null;

        // tạo tag mới
        if(empty($tag_info)){
            $create_result = $this->saveTag([
                'name' => $tag, 
                'lang' => $lang
            ]);
            $tag_id = !empty($create_result[DATA]['id']) ? intval($create_result[DATA]['id']) : null;
        }
        
        return $utilities->getResponse([
            CODE => SUCCESS, 
            DATA => [
                'id' => $tag_id
            ]
        ]);
    }

    public function saveTag($data = [], $tag_id = null)
    {
        $utilities = TableRegistry::get('Utilities');
        $table = TableRegistry::get('Tags');

        $name = !empty($data['name']) ? trim(strip_tags($data['name'])) : '';
        if(empty($name)) {
            return $utilities->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $lang = !empty($data['lang']) && is_string($data['lang']) ? $data['lang'] : TableRegistry::get('Languages')->getDefaultLanguage();
        $link = !empty($data['link']) ? $utilities->formatToUrl(trim($data['link'])) : $utilities->formatToUrl($name);        
        $link = $table->getUrlUnique($link);

        $content = !empty($data['content']) ? $data['content'] : '';
        $seo_title = !empty($data['seo_title']) ? $data['seo_title'] : $name;
        $seo_description = !empty($data['seo_description']) ? $data['seo_description'] : $name;
        $seo_keywords = '';
        if(!empty($data['seo_keywords']) && is_array($data['seo_keywords'])){
            $seo_keywords = implode(',', array_filter(json_decode($data['seo_keywords'])));
        }
        
        if(empty($link)) {
            return $utilities->getResponse([MESSAGE => __d('admin', 'vui_long_nhap_ten_the')]);
        }
        
        if($table->checkTagExist($name, $tag_id)){
            return $utilities->getResponse([MESSAGE => __d('admin', 'ten_the_da_ton_tai_tren_he_thong')]);
        }

        if(!empty($tag_id)){
            $tag_info = $table->find()->where(['Tags.id' => $tag_id])->first();
            if(empty($tag_info)){
                return $utilities->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
            }
        }

        if(empty($tag_id)){
            $settings = TableRegistry::get('Settings')->getSettingByGroup(TAG);
            
            $prefix_seo_title = !empty($settings['prefix_seo_title']) ? $settings['prefix_seo_title'] : null;
            $suffixes_seo_title = !empty($settings['suffixes_seo_title']) ? $settings['suffixes_seo_title'] : null;
            $seo_title = $prefix_seo_title . $seo_title . $suffixes_seo_title;
        }

        $data_save = [
            'name' => $name,
            'url' => $link,
            'content' => $content,
            'seo_title' => $seo_title,
            'seo_description' => $seo_description,
            'seo_keyword' => $seo_keywords,
            'lang' => $lang,
            'search_unicode' => strtolower($utilities->formatSearchUnicode([$name]))
        ];
      
        if(empty($tag_id)){
            $entity = $table->newEntity($data_save);
        }else{            
            $entity = $table->patchEntity($tag_info, $data_save);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            return $utilities->getResponse([
                CODE => SUCCESS, 
                DATA => [
                    'id' => $save->id
                ]
            ]);

        }catch (Exception $e) {
            $conn->rollback();
            return $utilities->getResponse([MESSAGE => $e->getMessage()]);
        }
    }
    
}
