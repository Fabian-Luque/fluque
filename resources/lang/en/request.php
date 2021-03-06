<?php

return [
    'success' => [
        'status' => 'false',
        'code'   => '200',
    ],
    'failure' => [
        'status' => 'true',
        'code'  => [
            'not_founded' => '404', // no encontrado
            'bad_request' => '400', // peticion mala
            'forbidden'   => '403', // prohibido
            'unauthorized' => '401', // no atorizado
        ],
        'bad'    => 'Bad Request.',
    ],
];