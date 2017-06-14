<?php
/**
 * 各种服务器配置
 */

return [
    // 'solr' => [
    //     'common' => ['url' => 'http://10.105.248.26:8983/solr/', 'core' => 'test'],
    //     'top' => ['url' => 'http://10.105.248.26:8983/solr/', 'core' => 'top'],
    // ],
    'solr'            => [
        'vronline'     => ['url' => 'http://search.vronline.com:' . env('SOLR_PORT', 8983) . '/solr/', 'core' => 'vronline'],
        'vronline_top' => ['url' => 'http://search.vronline.com:' . env('SOLR_PORT', 8983) . '/solr/', 'core' => 'vronline_top'],
        'common'       => ['url' => 'http://search.vronline.com:' . env('SOLR_PORT', 8983) . '/solr/', 'core' => 'common'],
        'top'          => ['url' => 'http://search.vronline.com:' . env('SOLR_PORT', 8983) . '/solr/', 'core' => 'top'],
        'tob'          => ['url' => 'http://search.vronline.com:' . env('SOLR_PORT', 8983) . '/solr/', 'core' => 'tob'],
    ],
    /**
     * 图片服务器
     */
    'imageserver'     => [
        'ip' => env('IMAGE_SERVER_HOST', ""),
    ],

    'cosimg'          =>
    [
        'appid'  => '10005081',
        'sid'    => 'AKIDXOsCaaJ0O7bRvvpQxz1y7ul8nnPE6UFw',
        'skey'   => 'Oi4bR9CkrGW4SUIFX06Ebv5e2URADYgc',
        'bucket' => 'vronline1',
        'url'    => 'http://web.image.myqcloud.com/photos/v2/',
    ],
    'cosv4'           =>
    [
        'appid'   => '10005081',
        'sid'     => 'AKIDXOsCaaJ0O7bRvvpQxz1y7ul8nnPE6UFw',
        'skey'    => 'Oi4bR9CkrGW4SUIFX06Ebv5e2URADYgc',
        'bucket'  => 'vronlinegame',
        'url'     => 'http://sh.file.myqcloud.com/files/v2/',
        'downurl' => 'http://vronlinegame-10005081.file.myqcloud.com/',
    ],
    'netcenter'       => [
        'ak'     => 'c91f234f4050e303f74c30e0d056f11f21241d93',
        'sk'     => 'bedcf5564aeb498fa87a7bc7c9d5acab54a0dd70',
        'bucket' => 'vronline-video',
        'url'    => 'http://vronline.mgr9.v1.wcsapi.com/',
    ],
    'cos_img_key'     => 'cos_img_',
    'img_request_url' => 'http://web.image.myqcloud.com/photos/v2/',
    'img'             => [
        'webgameimg'  => ['url' => '//image.vronline.com/webgameimg/', 'bucket' => 'vronline'],
        'vrgameimg'   => ['url' => '//image.vronline.com/vrgameimg/', 'bucket' => 'vronline'],
        'openuser'    => ['url' => '//open.vronline.com/upload/open/user/', 'dev' => '../upload/user/dev/', 'pub' => '../upload/user/pub/', 'bucket' => 'vronline'],
        'openapp'     => ['url' => '//open.vronline.com/upload/open/app/', 'dev' => '../upload/app/dev/', 'pub' => '../upload/app/pub/', 'bucket' => 'vronline'],
        'tob_idcard'  => ['url' => '//open.vronline.com/upload/open/tob/', 'dev' => '../upload/tob/dev/', 'pub' => '../upload/tob/pub/', 'bucket' => 'vronline'],
        'tob_license' => ['url' => '//open.vronline.com/upload/open/tob/', 'dev' => '../upload/tob/dev/', 'pub' => '../upload/tob/pub/', 'bucket' => 'vronline'],
        'service'     => ['url' => '//open.vronline.com/upload/service/', 'bucket' => 'vronline'],
        'wwwimg'      => ['url' => 'http://image.vronline.com', 'bucket' => 'vronline1'],
        'videoimg'    => ['dev' => 'videoimg/', 'pub' => 'videoimg/', 'bucket' => 'vronline1'],
        'newsimg'     => ['dev' => 'newsimg/', 'pub' => 'newsimg/', 'bucket' => 'vronline1'],
        'faqimg'      => ['dev' => 'faqimg/', 'pub' => 'faqimg/', 'bucket' => 'vronline1'],
        'bannerimg'   => ['dev' => 'bannerimg/', 'pub' => 'bannerimg/', 'bucket' => 'vronline1'],
        'video'       => ['dev' => 'dev/', 'pub' => 'dev/', 'bucket' => 'vronlinevideo'],
        'userimg'     => ['url' => 'http://image.vronline.com/', 'dev' => 'userimg/dev/', 'pub' => 'userimg/pub/', 'bucket' => 'vronline1'],
        'vrgame'      => ['url' => '//image.vronline.com/', 'dev' => 'vrgameimg/dev/', 'pub' => 'vrgameimg/pub/', 'bucket' => 'vronline1'],
        'webgame'     => ['url' => '//image.vronline.com/', 'dev' => 'webgameimg/dev/', 'pub' => 'webgameimg/pub/', 'bucket' => 'vronline1'],
    ],
];
