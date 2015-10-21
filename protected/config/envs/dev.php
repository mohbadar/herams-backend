<?php
return [
    'bootstrap' => ['gii'],
    'modules' => [
        'gii' => yii\gii\Module::class
    ]
];
<?php
return [
    'components' => [
        'db' => [
            'class' => \yii\db\Connection::class,
            'charset' => 'utf8',
            'dsn' => 'mysql:host=localhost;dbname=who;',
            'password' => 'z2P6NUSj3YfcfVH4',
            'username' => 'who',
            'enableSchemaCache' => true,
            'schemaCache' => 'cache',
            'enableQueryCache' => true,
            'queryCache' => 'cache',
			'tablePrefix' => 'prime2_'
        ]
    ]
];


