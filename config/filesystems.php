<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3", "rackspace"
    |
    */

    'disks' => array_merge([

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
        ],

        "bannanTools" => [
            "driver" => "ftp",
            "host" => "ftp.bannantools.co.uk",
            "username" => "process@bannantools.co.uk",
            "password" => "admin@123",
            'cache' => [
                'store' => 'database',
                'expire' => 600,
                'prefix' => "_bannanTools",
            ],
        ]
    ],
        ediDrives()),

];

function ediDrives(){
    $usernames = [
        "5C8M0"=>   ["down"=>"1LPDDCBNLFI4M",   "up"=>"MMNBGPQEIGAD"],
        "VU7KE"=>   ["down"=>"3TLD7WA66TE11",   "up"=>"C2IBUXVDBLBO"],
        "M195T"=>   ["down"=>"BU2UPN379E7W",    "up"=>"1VHJOABCMCDWC"],
        "L4R3I"=>   ["down"=>"22GD3UHNPYK22",    "up"=>"POEBQ97B3ZNU"],
        "umtest"=>  ["down"=>"2JZPUTC8GCHI1",   "up"=>"4I4RSZXMURIS"],
        "UNN42"=>   ["down"=>"3LB1U3A6XTQXT",   "up"=>"13PPL05B90JQC"],
        "UNNIP"=>   ["down"=>"24QJDNFQRSA7D",   "up"=>"2CSHK6Y1EMS9F"]
    ];

    $drives = [];
    foreach ($usernames as $drive => $names) {
        $drives[$drive."_down"] = [
            "driver" => "sftp",
            "host" => "sftp-eu.amazonsedi.com",
            "port" => 2222,
            "username" => $names["down"],
            "privateKey" =>  __DIR__."/../keys/{$drive}/down/private.ppk",
            "password" => "bramshot",
            "root" => "download",
            'cache' => [
                'store' => 'database',
                'expire' => 600,
                'prefix' => "_{$drive}_down",
            ],
        ];
        $drives[$drive."_up"] = [
            "driver" => "sftp",
            "host" => "sftp-eu.amazonsedi.com",
            "port" => 2222,
            "username" => $names["up"],
            "privateKey" =>  __DIR__."/../keys/{$drive}/up/private.ppk",
            "password" => "bramshot",
            "root" => "upload",
            'cache' => [
                'store' => 'database',
                'expire' => 600,
                'prefix' => "_{$drive}_up",
            ],
        ];
    }
    return $drives;
}