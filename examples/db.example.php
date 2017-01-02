<?php

require_once '../vendor/autoload.php';
//change these to your database settings
define("DB_HOST", "localhost");
define("DB_NAME", "db_example");
define("DB_USER", "root");
define("DB_PASSWORD", "root");
$db = new Jelle_S\DataBase\Connection("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);

$menu = $db->select('menu')->fields(array('label', 'page'))->orderby('weight')->run()->fetchAllAssoc();

print "Initial data: <br /><pre>";
print_r($menu);
print "</pre>";

$fields = array(
  'label' => 'This is the menu label.',
  'page' => 'home', //e.g relative link to the page'
  'weight' => 6,
);
$db->insert('menu')->fields($fields)->run();
$last_insert_id = $db->lastInsertId();

$menu = $db->select('menu')->fields(array('label', 'page'))->orderby('weight')->run()->fetchAllAssoc();

print "Data after insert: <br /><pre>";
print_r($menu);
print "</pre>";

$updatefields = array(
  'label' => 'This is the new label.',
  'page' => 'contact',
  'weight' => 5
);

$db->update('menu')->fields($updatefields)->where('id', $last_insert_id)->run();

$menu = $db->select('menu')->fields(array('label', 'page'))->orderby('weight')->run()->fetchAllAssoc();

print "Data after update: <br /><pre>";
print_r($menu);
print "</pre>";

$db->delete('menu')->where('id', $last_insert_id)->run();

$menu = $db->select('menu')->fields(array('label', 'page'))->orderby('weight')->run()->fetchAllAssoc();

print "Data after delete: <br /><pre>";
print_r($menu);
print "</pre>";

