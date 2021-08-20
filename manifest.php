<?php
//The main configuration file

return [

    'default-lang' => 'en',

    'supported-langs' => ['en'],

    'head' => [

        'link' => [
            [
                'type' => 'text/css',

                'href' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css',
                
                'rel' => 'stylesheet'
            ],
            [
                'type' => 'text/css',

                'href' => '/static/css/all',

                'rel' => 'stylesheet'
            ]
        ],
        'meta' => [
            [
                'charset' => 'UTF-8'
            ],
            
            [
                'name' => 'viewport',

                'content' => 'width=device-width, initial-scale=1.0'
            ]
        ],
        'script' => [
            [
                'type' => 'text/javascript',

                'src' => 'https://code.jquery.com/jquery-3.6.0.min.js'
            ],
            [
                'type' => 'text/javascript',

                'src' => 'https://code.jquery.com/ui/1.12.1/jquery-ui.js'
            ],
            [
                'type' => 'text/javascript',

                'src' => '/static/js/app'
            ],
            [
                'type' => 'text/javascript',

                'src' => '/static/js/general'
            ]
        ]

    ],

    'db' => [

        'mysql' => [

            'name' => 'test',

            'port' => 3306,

            'host' => 'localhost',
            
            'user' => 'root',

            'password' => ''
            
        ]

    ]

];