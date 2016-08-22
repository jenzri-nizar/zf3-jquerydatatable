<?php
/**
 * User: Jenzri
 * Date: 21/08/2016
 * Time: 15:09
 */
namespace Datatable;

return [
    'controller_plugins' => [
        'invokables' => [
            'DataTable' =>\Datatable\Controller\Plugin\DataTable::class,
        ]
    ]
];
?>