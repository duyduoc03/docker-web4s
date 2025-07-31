<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class UserAdminHelper extends Helper
{   

    public function getListUser()
    {
        $list_user = Hash::combine(TableRegistry::get('Users')->queryListUsers()->toArray(), '{n}.id', '{n}.full_name');
        return $list_user;
    }

    public function getDetailUser($user_id = null, $param = [])
    {
        if(empty($user_id)) return [];
        $user = TableRegistry::get('Users')->getDetailUsers($user_id, $param);
        
        return TableRegistry::get('Users')->formatDataUserDetail($user);
    }

    public function getSettingForUser($path_menu = null, $type = null)
    {
        if(empty($path_menu)) return [];
        if(empty($type) || !in_array($type, ['field', 'filter'])) $type = 'field';
        $auth = $this->getView()->get('auth_user');        
        $config = !empty($auth['config']) ? $auth['config'] : [];
        if(empty($config)) return [];
        
        $result = !empty($config['list_view'][$path_menu][$type]) ? $config['list_view'][$path_menu][$type] : [];
        
        return $result;
    }

    public function getSettingForListView($path_menu = null, $type = null)
    {
        if(empty($path_menu)) return [];
        if(empty($type) || !in_array($type, ['field', 'filter'])) $type = 'field';

        $settings = Configure::read('SETTING_FOR_USER');
        
        $result = !empty($settings['list_view'][$path_menu][$type]) ? $settings['list_view'][$path_menu][$type] : [];

        return $result;
    }

    public function sortColumnForListView($fields = [], $user_fields = [])
    {
        if(empty($fields) && empty($user_fields)) return [];

        foreach($fields as $field_code => $item){     
                $item['sort'] = !empty($user_fields[$field_code]['sort']) ? $user_fields[$field_code]['sort'] : $item['sort'];      
                $fields[$field_code] = $item;            
        }
        $result = Hash::sort($fields, '{s}.sort', 'asc');
        return $result;
    }

    public function getLocaleSettingForUser()
    {
        $result = Configure::read('LIST_LOCALE_FOR_SETTING_USER');
        return $result;
    }
}
