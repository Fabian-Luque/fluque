<?php

return [
    'success' => [
        'code'   => '200',
        'status' => 'ok',
    ],
    'failure' => [
        'status' => 'error',
        'codes'  => [
            'not_founded' => '404',
            'bad_request' => '400',
            'forbidden'   => '403',
        ],
        'bad'    => 'Solicitud incorrecta.',
    ],
];
