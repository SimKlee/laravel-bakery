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
        'useUuid'    => true,
        'tags'       => [],
    ],
];
