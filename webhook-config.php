<?php

return [
    // secret
    'secret' => 'password',
    
    // 各サイトの設置パスの設定
    'repo' => [
        // レポジトリ名
        'webhook-site' => [
            // 本番環境のパス
            'prd' => '/home/ubuntu/workspace/master',
            // 開発環境のパス
            'dev' => '/home/ubuntu/workspace/develop',
        ],
    ],
];
