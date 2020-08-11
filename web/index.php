<?php

require '../vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\GetFiles;

$loader = new FilesystemLoader('../app/templates');
$twig = new Environment($loader, ['debug' => TRUE]);

$twig->addExtension(new \Twig\Extension\DebugExtension());

$source = getFiles::files('files');

echo $twig->render('index.html.twig', [
  'site_name' => 'Gebruiker Centraal Beeldbank',
  'title' => 'Dit is de homepage :D',
  'source' => $source
]);


