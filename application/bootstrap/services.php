<?php
/*
 * Service manager is basic dependency injection container
 * It can be used in to transfer Services as dependencies
 * Warning!!!
 * Service Manager uses lazy object instantiation - only if one use
 * $serviceManager->getService('request'); call
 */
$serviceManager = new \Amalgam\Services\ServiceManager();
$serviceManager->addService('request', [
        function ($config) {
            return new \Amalgam\Http\Request(new \Amalgam\Config\Config($config));
        },
        [
            'base_url' => 'http://mvc-amalgam',
        ]
    ]
);

/*
 * By default Service Manager returns only copy of the service
 */
$request1 = $serviceManager->getService('request');
$request2 = $serviceManager->getService('request');
$request3 = $serviceManager->getService('request');
$request4 = $serviceManager->getService('request');
var_dump($request1 === $request2);
var_dump($request1 === $request2);
var_dump($request2 === $request3);
var_dump($request3 === $request4);
var_dump($request4 === $request1);

/*
 * One can make it singleton instance to return same object
 * Warning!!!
 * This operation cannot be undone!!!
 */
$serviceManager->markAsShared('request');
$request1 = $serviceManager->getService('request');
$request2 = $serviceManager->getService('request');
$request3 = $serviceManager->getService('request');
$request4 = $serviceManager->getService('request');
var_dump($request1 === $request2);
var_dump($request1 === $request2);
var_dump($request2 === $request3);
var_dump($request3 === $request4);
var_dump($request4 === $request1);

/*
 * Same result will be if on will use ServiceManager in this way:
 * $serviceManager['request'] = [
        function ($config) {
            return new \Amalgam\Http\Request(new \Amalgam\Config\Config($config));
        },
        [
            'base_url' => 'http://mvc-amalgam',
        ]
    ];
 * But after that you need to use
 * $serviceManager->markAsShared('request');
 * to make service shared
 * So basically
 * $serviceManager->addService('request', [
            function ($config) {
                return new \Amalgam\Http\Request(new \Amalgam\Config\Config($config));
            },
            [
                'base_url' => 'http://mvc-amalgam',
            ]
        ],
        true
    );
 * is equal
 * $serviceManager['request'] = [
        function ($config) {
            return new \Amalgam\Http\Request(new \Amalgam\Config\Config($config));
        },
        [
            'base_url' => 'http://mvc-amalgam',
        ]
    ];
    $serviceManager->markAsShared('request');
 */