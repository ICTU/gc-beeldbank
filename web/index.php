<?php

require '../vendor/autoload.php';

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use App\FileManager;

use App\Twig\ClassList;
use App\Twig\CleanClass;


$loader = new FilesystemLoader('../app/templates');
$twig = new Environment($loader, ['debug' => TRUE]);

$twig->addExtension(new ClassList);
$twig->addExtension(new CleanClass);
$twig->addExtension(new DebugExtension());

$source = FileManager::getAllFiles('files');

echo $twig->render('index.html.twig', [
  'site_name' => 'Gebruiker Centraal Beeldbank',
  'source' => $source
]);


