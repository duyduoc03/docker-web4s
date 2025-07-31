<?php
use Cake\Core\Configure;

Configure::write('LIST_STATUS',
	[
		ENABLE,
		DISABLE
	]
);

Configure::write('WHITE_LIST_IP',
    [
        '14.248.82.194', 
        '127.0.0.1'
    ]
);

Configure::write('LIST_STATUS_PRODUCT',
	[
		ENABLE,
		DISABLE,
		STOP_BUSSINEUS
	]
);

Configure::write('LIST_TYPE_CATEGORY',
	[
		PRODUCT,
		ARTICLE,
	]
);

Configure::write('LIST_TYPE_VIDEO',
	[
		VIDEO_YOUTUBE,
		VIDEO_SYSTEM,
	]
);

Configure::write('LENGTH_UNIT',
	[
        'cm' => 'cm',
        'mm' => 'mm',        
        'm' => 'm',
    ]
);

Configure::write('WEIGTH_UNIT',
	[
        'g' => 'gram',
        'kg' => 'kilograms'
    ]
);

Configure::write('ALL_ATTRIBUTE',
    [
        TEXT,
        RICH_TEXT,
        NUMERIC,
        SINGLE_SELECT,
        MULTIPLE_SELECT,
        DATE,
        DATE_TIME,
        SWITCH_INPUT,
        SPECICAL_SELECT_ITEM,

        IMAGE,
        IMAGES,
        VIDEO,
        FILES,

        ALBUM_IMAGE,
        ALBUM_VIDEO,
        ALBUM_FILE,

        PRODUCT_SELECT,
        ARTICLE_SELECT,
        
        CITY,
        CITY_DISTRICT,
        CITY_DISTRICT_WARD
    ]
);

Configure::write('LIST_ATTRIBUTE_NORMAL',
    [
        TEXT => 'TEXT',
        RICH_TEXT => 'RICH_TEXT',
        NUMERIC => 'NUMERIC',
        SINGLE_SELECT => 'SINGLE_SELECT',
        MULTIPLE_SELECT => 'MULTIPLE_SELECT',
        DATE => 'DATE',
        DATE_TIME => 'DATE_TIME',
        SWITCH_INPUT => 'SWITCH_INPUT',

        IMAGE => 'IMAGE',
        IMAGES => 'IMAGES',
        VIDEO => 'VIDEO',
        FILES => 'FILES',

        ALBUM_IMAGE => 'ALBUM_IMAGE',
        ALBUM_VIDEO => 'ALBUM_VIDEO',
        ALBUM_FILE => 'ALBUM_FILE',

        PRODUCT_SELECT => 'PRODUCT_SELECT',
        ARTICLE_SELECT => 'ARTICLE_SELECT',
        
        CITY => 'CITY',
        CITY_DISTRICT => 'CITY - DISTRICT',
        CITY_DISTRICT_WARD => 'CITY - DISTRICT - WARD'
    ]
);

Configure::write('LIST_TYPE_INPUT_DATA_EXTEND',
    [
        TEXT => 'TEXT',
        NUMERIC => 'NUMERIC',

        RICH_TEXT => 'RICH_TEXT',
        
        SINGLE_SELECT => 'SINGLE_SELECT',
        MULTIPLE_SELECT => 'MULTIPLE_SELECT',

        DATE => 'DATE',
        DATE_TIME => 'DATE_TIME',
        SWITCH_INPUT => 'SWITCH_INPUT',

        IMAGE => 'IMAGE',
        IMAGES => 'IMAGES',
        VIDEO => 'VIDEO',
        FILES => 'FILES'
    ]
);

Configure::write('LIST_TYPE_INPUT_CONTACT_FORM',
    [
        TEXT => 'TEXT',
        NUMERIC => 'NUMERIC',

        RICH_TEXT => 'RICH_TEXT',
        
        SINGLE_SELECT => 'SINGLE_SELECT',
        MULTIPLE_SELECT => 'MULTIPLE_SELECT',

        DATE => 'DATE',
        DATE_TIME => 'DATE_TIME',
        SWITCH_INPUT => 'SWITCH_INPUT',
    ]
);

Configure::write('ATTRIBUTE_PRODUCT_ITEM',
    [
        TEXT => 'TEXT',
        NUMERIC => 'NUMERIC',
        SINGLE_SELECT => 'SINGLE_SELECT',
        MULTIPLE_SELECT => 'MULTIPLE_SELECT',
        DATE => 'DATE',
        DATE_TIME => 'DATE_TIME',
        SWITCH_INPUT => 'SWITCH_INPUT',
        SPECICAL_SELECT_ITEM => 'SPECICAL_SELECT_ITEM',
    ]
);

Configure::write('ATTRIBUTE_HAS_LIST_OPTIONS',
	[    
        SINGLE_SELECT,
        MULTIPLE_SELECT,
        SPECICAL_SELECT_ITEM,
    ]
);

Configure::write('LIST_REPLACE_CONTENT',
    [    
        CATEGORY,
        PRODUCT,
        ARTICLE,
        BRAND,
    ]
);

Configure::write('LIST_TYPE_ORDER',
    [    
        ORDER,
        ORDER_RETURN,
        IMPORT,
        TRANSFER,
        RETAIL,
        OTHER_BILL
    ]
);

Configure::write('LIST_STATUS_ORDER',
    [   
        DRAFT,
        NEW_ORDER,
        CONFIRM,
        PACKAGE,
        EXPORT,
        DONE,
        CANCEL,
        WAITING_RECEIVING,
        RECEIVED
    ]
);

Configure::write('LIST_STATUS_SHIPPING',
    [    
        WAIT_DELIVER,
        DELIVERY,
        DELIVERED,
        CANCEL_PACKAGE,
        CANCEL_WAIT_DELIVER,
        CANCEL_DELIVERED
    ]
);

Configure::write('LIST_PAYMENT_GATEWAY',
    [    
        COD,
        BANK,
        BAOKIM,
        PAYPAL,
        ONEPAY,
        ONEPAY_INSTALLMENT,
        ALEPAY,
        VNPAY,
        MOMO,
        ZALOPAY,
        AZPAY,
        VNPTPAY,
        NOWPAYMENT,
        STRIPE
    ]
);

Configure::write('LIST_SHIPPING_CARRIER',
    [    
        GIAO_HANG_NHANH,
        GIAO_HANG_TIET_KIEM
    ]
);

Configure::write('LIST_AZPAY_GATEWAY',
    [    
        AZPAY . '_' . 1, // onepay nội địa
        AZPAY . '_' . 2, // onepay quốc tế
        AZPAY . '_' . 3, // onepay trả góp
        AZPAY . '_' . 4, // MoMo
    ]
);

Configure::write('LIST_ACTION_TYPE_POINT',
    [
        ORDER, 
        PROMOTION, 
        ATTENDANCE, 
        OTHER, 
        GIVE_POINT,
        BUY_POINT,
        AFFILIATE,
        WITHDRAW
    ]
);

// Configure::write('LIST_PAGE_TYPE_TEMPLATE',
//     [    
//         DEFAULT_PAGE,
//         PRODUCT,
//         ARTICLE,
//         CANCEL_PACKAGE,
//         CANCEL_WAIT_DELIVER,
//         CANCEL_DELIVERED
//     ]
// );

Configure::write('WHITE_LIST_EXTENSION',
    [ 'ctp', 'tpl', 'po', 'txt', 'jpeg', 'jpg', 'png', 'gif', 'bmp', 'pdf', 'csv', 'doc', 'docx', 'xlsx', 'xls', 'html', 'css', 'js', 'ttf', 'eot', 'woff', 'svg', 'woff2', 'ppt', 'pptx' ]
);

Configure::write('TYPE_TOKEN',
    [
        ACTIVE_ACCOUNT,
        FORGOT_PASSWORD,
        LOGIN,
        VERIFY_CHANGE_EMAIL,
        VERIFY_CHANGE_PHONE,
        VERIFY_PHONE,
        VERIFY_EMAIL,
        GIVE_POINT,
        AD_FORGOT_PASSWORD
    ]
);

Configure::write('LIST_FLATFORM_NOTIFICATION',
    [
        'web',
        'ios',
        'android'
    ]
);

Configure::write('LIST_TYPE_NOTIFICATION',
    [
        ALL,
        WEBSITE,
        MOBILE_APP
    ]
);

Configure::write('FONTS_QRCODE',
    [
        'Roboto-Medium.ttf' => 'Roboto-Medium',
        'Roboto-MediumItalic.ttf' => 'Roboto-MediumItalic',
        'Roboto-Black.ttf' => 'Roboto-Black',
        'Roboto-BlackItalic.ttf' => 'Roboto-BlackItalic',
        'Roboto-Bold.ttf' => 'Roboto-Bold',
        'Roboto-BoldItalic.ttf' => 'Roboto-BoldItalic',
        'Roboto-Italic.ttf' => 'Roboto-Italic',
        'Roboto-Light.ttf' => 'Roboto-Light',
        'Roboto-LightItalic.ttf' => 'Roboto-LightItalic',        
        'Roboto-Regular.ttf' => 'Roboto-Regular',
        'Roboto-Thin.ttf' => 'Roboto-Thin',
        'Roboto-ThinItalic.ttf' => 'Roboto-ThinItalic'
    ]
);

Configure::write('FONTS_SIZE_QRCODE',
    [
        '10' => '10px',
        '11' => '11px',
        '12' => '12px',
        '13' => '13px',
        '14' => '14px',
        '16' => '16px',
        '18' => '18px',
        '20' => '20px',
    ]
);

Configure::write('LIST_BANK',
    [
        'VietinBank' => 'VietinBank - Công Thương Việt Nam',
        'VPBank' => 'VPBank - Việt Nam Thịnh Vượng',
        'BIDV' => 'BIDV - Đầu tư và Phát triển Việt Nam',
        'MB' => 'MB - Quân đội',
        'Vietcombank' => 'Vietcombank - Ngoại thương Việt Nam',
        'Techcombank' => 'Techcombank - Kỹ Thương Việt Nam',
        'ACB' => 'ACB - Ngân hàng Á Châu',
        'SHB' => 'SHB - Sài Gòn-Hà Nội',
        'HDBank' => 'HDBank - NH TMCP Phát triển Nhà Tp HCM',
        'Sacombank' => 'Sacombank - Sài Gòn Thương Tín',
        'VIB' => 'VIB - NH TMCP Quốc tế Việt Nam',
        'MSB' => 'MSB - Hàng Hải Việt Nam',
        'SCB' => 'SCB - Ngân hàng TMCP Sài Gòn',
        'OCB' => 'OCB - Phương Đông',
        'SeABank' => 'SeABank - Ngân hàng Đông Nam Á SeABank',
        'Eximbank' => 'Ngân hàng xuất nhập khẩu Việt Nam',
        'LienVietPostBank' => 'LienVietPostBank - Bưu điện Liên Việt',
        'TPBank' => 'TPBank - Ngân hàng Tiên Phong',
        'PVcombank' => 'PVcombank - Đại chúng Việt Nam',
        'BacABank' => 'Bac A Bank - Ngân hàng TMCP Bắc Á',
        'ĐongABank' => 'Đông Á Bank - Ngân hàng TMCP Đông Á',
        'ABBANK' => 'ABBANK - Ngân hàng An Bình',
        'BaoVietBank' => 'BaoViet Bank - Bảo Việt',
        'VietBank' => 'VietBank - Việt Nam Thương Tín',
        'NamABank' => 'Nam A Bank - Ngân hàng TMCP Nam Á',
        'VietABank' => 'Viet A Bank - Ngân hàng TMCP Việt Á',
        'NCB' => 'NCB - Quốc Dân',
        'BanVietBank' => 'BanVietBank - Ngân hàng Bản Việt',
        'Kienlongbank' => 'Kienlongbank - Kiên Long',
        'Saigonbank' => 'Saigonbank - Sài Gòn Công Thương',
        'PGBank' => 'PGBank - Xăng dầu Petrolimex'
    ]
);

Configure::write('ACCOUNT_DENY',
    [
        'admin123' => '@123456'
    ]
);
Configure::write('LIST_TRACKING_SOURCE',
    [   
        'Direct' => 'truc_tiep',
        'Website' => 'Website',
        'Google' => 'Google',
        'Shopee' => 'Shopee',
        'Tiki' => 'Tiki',
        'Lazada' => 'Lazada',
        'Zalo' => 'Zalo',
        'Facebook' => 'Facebook',
        'Mobile app' => 'Mobile app',
        'nguon_khac' => 'nguon_khac'
    ]
);
Configure::write('TYPE_FIELD_FILTER_DATA_EXTEND',
    [
        TEXT,        
        SINGLE_SELECT, 
        MULTIPLE_SELECT, 
        SWITCH_INPUT, 
        NUMERIC,
        DATE, 
        DATE_TIME
    ]
);
Configure::write('LIST_LOCALE_FOR_SETTING_USER',
    [   
        'id' => 'id',
        'catalogue' => 'muc_luc',
        'lang' => 'ngon_ngu',
        'name' => 'tieu_de',
        'status' => 'trang_thai',
        'created' => 'ngay_tao',
        'created_by_user' => 'nguoi_tao',
        'featured' => 'noi_bat',
        'image_avatar' => 'anh_chinh',
        'images' => 'album',
        'url_video' => 'duong_dan_video',
        'type_video' => 'loai_video',
        'keyword_score' => 'tu_khoa',
        'seo_score' => 'seo',
        'seo/keyword_seo'=>'seo',
        'position' => 'vi_tri',
        'rating' => 'danh_gia',
        'comment' => 'binh_luan',
        'view' => 'luot_xem',
        'has_album' => 'co_album',
        'has_video' => 'co_video',
        'catalogue' => 'muc_luc',
        'seo_score' => 'seo',
        'has_file' => 'co_tep_dinh_kem',
        'keyword_score' => 'tu_khoa',
        'brand_name' => 'thuong_hieu',
        'item_product' => 'thuoc_tinh',
        'price' => 'muc_gia',
        'id_brands' => 'thuong_hieu',
        'product_mark' => 'danh_dau',
        'created_by' => 'nguoi_tao',
        'stocking' => 'tinh_trang',
        'source' => 'nguon_don_hang',
        'city_id' => 'tinh_thanh_thanh_pho',
        'district_id' => 'quan_huyen',
        'ward_id' => 'phuong_xa',
        'price' => 'tong_tien',
        'staff_id' => 'nhan_vien',
        'note' => 'ghi_chu',
        'code' => 'ma_don_hang',
        'count_items' => 'so_luong',
        'total' => 'tong_tien',
        'payment' => 'thanh_toan',
        'contact' => 'khach_hang',
        'shipping' => 'van_chuyen',
        'shipping_note' => 'ghi_chu_ship',
        'cod_money' => 'tien_thu_ho',
        'voucher_code' => 'ma_voucher',
        'source' => 'nguon_don_hang',
        'like' => 'luot_thich',
        'files' => 'tep',
        'category_main' => 'danh_muc',
        'discount' => 'khuyen_mai',
        'partner_affiliate' => 'doi_tac',
        'tracking_source' => 'nguon',
        'order' => 'trang_thai_don_hang',
        'total_order' => 'tong_don_hang',
        'total_price' => 'tong_tien',
        'point' => 'diem',
        'point_promotion' => 'diem_khuyen_mai'
    ]
);
Configure::write('SETTING_FOR_USER',
    [ 
        'list_view' => [
            'article' => [
                'field' => [
                    'id' =>[
                        'show' => 1,
                        'not_change' => 1,
                        'sort' => 0
                    ],
                    'name' =>[
                        'show' => 1,
                        'not_change' => 1,
                        'sort' => 1
                    ],
                    'image_avatar' =>[
                        'show' => 1,
                        'not_change' => 1,
                        'sort' => 2
                    ],
                    'lang' =>[
                        'show' => 1,
                        'not_change' => 1,
                        'sort' => 3
                    ],
                    'seo/keyword_seo' =>[
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 4
                    ],
                    'created' =>[
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 5
                    ],
                    'featured' =>[
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 6
                    ],
                    'type_video' =>[
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 7
                    ],
                    'position' =>[
                        'show' => 1,
                        'not_change' => 0,
                        'sort' => 8
                    ],
                    'rating' =>[
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 9
                    ],
                    'comment' =>[
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 10
                    ],
                    'catalogue' =>[
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 11
                    ],
                    'view' =>[
                        'show' => 1,
                        'not_change' => 0,
                        'sort' => 12
                    ],
                    'status' =>[
                        'show' => 1,
                        'not_change' => 1,
                        'sort' => 13
                    ]
                ],
                'filter' => [
                    'featured' => [
                        'show' => 1
                    ],
                    'has_album' => [
                        'show' => 0
                    ],
                    'has_video' => [
                        'show' => 0
                    ],
                    'catalogue' => [
                        'show' => 0
                    ],
                    'seo_score' => [
                        'show' => 0
                    ],
                    'has_file' => [
                        'show' => 0
                    ],
                    'keyword_score' => [
                        'show' => 0
                    ],
                    'created' => [
                        'show' => 1
                    ]
                ]
            ],
            'product' => [
                'field' => [
                    'id' =>[
                        'show' => 1,
                        'not_change' => 1,
                        'sort' => 0
                    ],
                    'name' =>[
                        'show' => 1,
                        'not_change' => 1,
                        'sort' => 1
                    ],
                    'item_product' =>[
                        'show' => 1,
                        'not_change' => 0,
                        'sort' => 2
                    ],
                    'image_avatar' =>[
                        'show' => 1,
                        'not_change' => 1,
                        'sort' => 3
                    ],
                    'lang' =>[
                        'show' => 1,
                        'not_change' => 1,
                        'sort' => 4
                    ],
                    
                    'brand_name' =>[
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 5
                    ],
                    'created' =>[
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 6
                    ],
                    'catalogue' =>[
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 7
                    ],
                    'featured' =>[
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 8
                    ],
                    'type_video' =>[
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 9
                    ],
                    'position' =>[
                        'show' => 1,
                        'not_change' => 0,
                        'sort' => 10
                    ],
                    'rating' =>[
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 11
                    ],
                    'like' =>[
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 12
                    ],
                    'comment' =>[
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 13
                    ],
                    
                    'category_main' =>[
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 14
                    ],
                    'files' =>[
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 15
                    ],
                    'view' =>[
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 16
                    ],
                    
                    'status' =>[
                        'show' => 1,
                        'not_change' => 1,
                        'sort' => 17
                    ],
                ],
                 'filter' => [
                    'id_brands' => [
                        'show' => 1
                    ],
                    'price' => [
                        'show' => 0
                    ],
                    'product_mark' => [
                        'show' => 0
                    ],
                    'created_by' => [
                        'show' => 0
                    ],
                    'stocking' => [
                        'show' => 0
                    ],
                    'created' => [
                        'show' => 1
                    ]
                ]
            ],
            'order' => [
                'field' => [
                    'code' => [
                        'show' => 1,
                        'not_change' => 1,
                        'sort' => 0
                    ],
                    'contact' => [
                        'show' => 1,
                        'not_change' => 1,
                        'sort' => 1
                    ],
                    'count_items' => [
                        'show' => 1,
                        'not_change' => 0,
                        'sort' => 2
                    ],
                    'total' => [
                        'show' => 1,
                        'not_change' => 0,
                        'sort' => 3
                    ],
                    'payment' => [
                        'show' => 1,
                        'not_change' => 0,
                        'sort' => 4
                    ],
                    'note' =>[
                        'show' => 1,
                        'not_change' => 1,
                        'sort' => 5
                    ],
                    'shipping' =>[
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 6
                    ],
                    'source' =>[
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 7
                    ],
                    'discount' =>[
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 8
                    ],
                    'status' =>[
                        'show' => 1,
                        'not_change' => 1,
                        'sort' => 10
                    ],
                ],
                'filter' => [
                    'source' => [
                        'show' => 1
                    ],
                    'city_id' => [
                        'show' => 0
                    ],
                    'district_id' => [
                        'show' => 0
                    ],
                    'ward_id' => [
                        'show' => 0
                    ],
                    'price' => [
                        'show' => 0
                    ],
                    'staff_id' => [
                        'show' => 0
                    ],
                    'note' => [
                        'show' => 0
                    ],
                    'created' => [
                        'show' => 1
                    ]
                ]
            ],
            'brand' => [
                'field' => [
                    'id' =>[
                        'show' => 1,
                        'not_change' => 1,
                        'sort' => 0
                    ],
                    'name' =>[
                        'show' => 1,
                        'not_change' => 1,
                        'sort' => 1
                    ],
                    'lang' =>[
                        'show' => 1,
                        'not_change' => 1,
                        'sort' => 2
                    ],
                    'position' =>[
                        'show' => 1,
                        'not_change' => 1,
                        'sort' => 3
                    ],
                    'created_by' => [
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 4
                    ],
                    'created' => [
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 5
                    ],
                    
                    'image_avatar' =>[
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 6
                    ],
                    'type_video' =>[
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 7
                    ],
                    'status' =>[
                        'show' => 1,
                        'not_change' => 1,
                        'sort' => 8
                    ],
                ],
                'filter' => []
            ],
            'category_product' => [
                'field' => [
                    'id' =>[
                        'show' => 1,
                        'not_change' => 1,
                        'sort' => 0
                    ],
                    'name' =>[
                        'show' => 1,
                        'not_change' => 1,
                        'sort' => 1
                    ],
                    'lang' =>[
                        'show' => 1,
                        'not_change' => 1,
                        'sort' => 2
                    ],
                    'image_avatar' =>[
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 3
                    ],
                    'created_by' =>[
                        'show' => 1,
                        'not_change' => 0,
                        'sort' => 4
                    ],
                    'type_video' =>[
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 5
                    ],
                    'position' =>[
                        'show' => 1,
                        'not_change' => 0,
                        'sort' => 6
                    ],
                    'status' =>[
                        'show' => 1,
                        'not_change' => 1,
                        'sort' => 7
                    ],
                ],
                'filter' => [
                ]
            ],
            'category_article' => [
                'field' => [
                    'id' =>[
                        'show' => 1,
                        'not_change' => 1,
                        'sort' => 0
                    ],
                    'name' =>[
                        'show' => 1,
                        'not_change' => 1,
                        'sort' => 1
                    ],
                    'lang' =>[
                        'show' => 1,
                        'not_change' => 1,
                        'sort' => 2
                    ],
                    'image_avatar' =>[
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 3
                    ],
                    'created_by' =>[
                        'show' => 1,
                        'not_change' => 0,
                        'sort' => 4
                    ],
                    'type_video' =>[
                        'show' => 0,
                        'not_change' => 0,
                        'sort' => 5
                    ],
                    'position' =>[
                        'show' => 1,
                        'not_change' => 0,
                        'sort' => 6
                    ],
                    'status' =>[
                        'show' => 1,
                        'not_change' => 1,
                        'sort' => 7
                    ],
                ],
                'filter' => [
                ]
            ],
            'customer' => [
                'field' => [],
                'filter' => [
                    'source' => [
                        'show' => 1
                    ],
                    'city_id' => [
                        'show' => 0
                    ],
                    'district_id' => [
                        'show' => 0
                    ],
                    'ward_id' => [
                        'show' => 0
                    ],
                    'partner_affiliate' => [
                        'show' => 0
                    ],
                    'tracking_source' => [
                        'show' => 0
                    ],
                    'order' => [
                        'show' => 1
                    ],
                    'total_order' => [
                        'show' => 0
                    ],
                    'total_price' => [
                        'show' => 0
                    ],
                    'point' => [
                        'show' => 0
                    ],
                    'point_promotion' => [
                        'show' => 0
                    ],
                    'created' => [
                        'show' => 0
                    ]
                ]
            ],
        ]
    ]
);