<?php
return [
    'Author' => [
        'table'      => 'authors',
        'columns'    => [
            'id'   => 'integer|unsigned|ai',
            'name' => 'varchar|length:80',
        ],
        'timestamps' => true,
    ],
    'Book'   => [
        'table'      => 'books',
        'columns'    => [
            'id'        => 'integer|unsigned|ai',
            'author_id' => 'fk',
            'title'     => 'varchar|length:80',
        ],
        'timestamps' => true,
    ],
];
