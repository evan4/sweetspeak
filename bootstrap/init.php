<?php

//load env variables
require_once __DIR__.'/../config/_env.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;


// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$proxyDir = null;
$cache = null;
$useSimpleAnnotationReader = false;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/app"), $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);
// or if you prefer yaml or XML
//$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
//$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);

$dotenv = Dotenv\Dotenv::createImmutable(ROOT);
$dotenv->load();

// database configuration parameters
$conn = array(
    'driver' => 'pdo_mysql',
    'dbname' => $_ENV['DB_NAME'],
    'user' => $_ENV['DB_USERNAME'],
    'password' => $_ENV['DB_PASSWORD']
);

// obtaining the entity manager
$entityManager = EntityManager::create($conn, $config);

$app = \Mycms\App::getInstance(ROOT);

return $app;


