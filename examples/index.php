<?php
/**
 * @author EgorKluch (EgorKluch@gmail.com)
 * @date: 10.06.2014
 */

require '../src/AccessManager.php';

use EgorKluch\AccessManager;

$accessManager = new AccessManager();

if (!$accessManager->hasAccess('someAction')) {
  throw new \Exception('Actions without custom handlers should be allowed');
}

$accessManager->handle('someAction', function ($action, $args) {
  return array_key_exists('arg1', $args) and array_key_exists('arg2', $args);
});
if ($accessManager->hasAccess('someAction', array('arg1' => true))) {
  throw new \Exception('SomeAction not allowed. Arg2 is false.');
}

$accessManager->prepareHandle('someAction', function ($action, $args) {
  $args['arg2'] = true;
  return $args;
});
if (!$accessManager->hasAccess('someAction', array('arg1' => true))) {
  throw new \Exception('SomeAction should be allowed. Arg1 and arg2 is true.');
}

echo 'Success!';
