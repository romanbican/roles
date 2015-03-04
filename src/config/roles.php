<?php
    /*Config file for Bican/Roles package*/

return [
    /*pretend lets you simulate how roles package behave*/

    /*enabled (true|false) tells package to simulate behaviour according to options array*/
    'pretend' => ['enabled' => 'false',
                  'options' => ['hasPermission' => 'true', //hasPermission always returns assigned value (true|false)
                                'hasRole'       => 'true', //hasRole always returns assigned value (true|false)
                                'allowed'       => 'true', //allowed always returns assigned value (true|false)
                                'is'            => 'true'] //is always returns assigned value (true|false)
    ]
];

