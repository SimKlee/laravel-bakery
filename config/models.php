<?php
return [
    'SampleModel' => [
        'table'      => '',
        'columns'    => [
            'id'                 => 'integer|unsigned|ai',
            'uuid'               => 'uuid|unique',
            'foreign_id'         => 'fk',
            'column_with_values' => 'varchar|length:10',
        ],
        'values'     => [
            'column_with_values' => [
                'string1',
                'string2',
            ],
        ],
        'label'      => false,
        'timestamps' => false,
        'softDelete' => false,
        'useUuid'    => true,
        'tags'       => [],
        'api'        => [
            'index'   => true,
            'create'  => true,
            'show'    => true,
            'store'   => true,
            'update'  => true,
            'destroy' => true,
        ],
        'frontend'   => [
            'index' => true,
        ],
    ],
];
