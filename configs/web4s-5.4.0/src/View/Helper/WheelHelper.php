<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\ORM\TableRegistry;

class WheelHelper extends Helper
{
    /** Lấy chi tiết thông tin liên hệ trong gửi Email
     * 
     * $contact_id (*): ID của bảng WheelFortuneLog (int)
     * 
     * {assign var = data value = $this->Wheel->getDetailWheelFortuneLog($id_record)}
     * 
    */
    public function getDetailWheelFortuneLog($contact_id = null)
    {
        if(empty($contact_id)) return [];

        $table = TableRegistry::get('WheelFortuneLog');

        $contact = $table->getDetailWheelFortuneLog($contact_id, LANGUAGE);
        return $table->formatDataWheelFortuneLog($contact, LANGUAGE);
    }

    public function createSlicePath($index = 0, $total_option = null, $color = null)
    {
        if(empty($total_option) || empty($color)) return '';

        $percent = (100/$total_option)/100;
        $coords = $this->getCoordinatesForPercent($percent, $index);
        $path = [
            'M '.$coords->start->x.' '.$coords->start->y,
            'A 1 1 0 0 1 '.$coords->end->x.' '.$coords->end->y,
            'L 0 0'
        ];

        return '<path stroke="'.$color.'" stroke-width="0.0025" class="wof-slice-bg" data-slice="'.($index+1).'" fill="#fff" d="'.join(' ',$path).'" style="fill: '. $this->_hexToRgb($color) .';"></path>';
    }

    private function getCoordinatesForPercent($percent = null, $index = 0) 
    {
        if(empty($percent)) return;

        $start_percent = $percent * $index;
        $end_percent = $percent * ($index+1);
        $start_x = cos(2 * M_PI * $start_percent);
        $start_y = sin(2 * M_PI * $start_percent);
        $end_x = cos(2 * M_PI * $end_percent);
        $end_y = sin(2 * M_PI * $end_percent);
        return (object) [
            'start' => (object) ['x' => $start_x,'y' => $start_y],
            'end' => (object) ['x' => $end_x,'y' => $end_y]
        ];
    }

    private function _hexToRgb($hex = '', $alpha = false) 
    {
        if(empty($hex)) return [];

        $hex = str_replace('#', '', $hex);
        $length = strlen($hex);

        $rgb = [];
        $rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
        $rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
        $rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
        if ($alpha) $rgb['a'] = $alpha;

        return 'rgb('.$rgb['r'].','.$rgb['g'].','.$rgb['b'].')';
    }
}
