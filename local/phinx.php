<?php

define('NOT_CHECK_PERMISSIONS', true);
define('NO_AGENT_CHECK', true);
$GLOBALS['DBType'] = 'mysql';
$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '/..');
include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

// manual saving of DB resource
global $DB;
$application = \Bitrix\Main\Application::getInstance();
$connection = $application->getConnection();
$DB->db_Conn = $connection->getResource();

// 'authorizing' as admin
$_SESSION['SESS_AUTH']['USER_ID'] = 1;


$config = include realpath(__DIR__.'/../bitrix/.settings.php');

return array(
    'paths' => array(
        'migrations' => 'migrations'
    ),
    'environments' => array(
        'default_migration_table' => 'phinxlog',
        'default_database' => 'dev',
        'dev' => array(
            'adapter' => 'mysql',
            'host' => $config['connections']['value']['default']['host'],
            'name' => $config['connections']['value']['default']['database'],
            'user' => $config['connections']['value']['default']['login'],
            'pass' => $config['connections']['value']['default']['password']
        )
    )
);
