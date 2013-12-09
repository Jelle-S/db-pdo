<?php
include_once './class.db.php';
//change these to your database settings
define("DB_HOST", "localhost");
define("DB_NAME", "db_example");
define("DB_USER", "root");
define("DB_PASSWORD", "root");
$db = new db("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);

$menu = $db->select('menu')->fields(array('label', 'page'))->orderby('weight')->run()->fetchAllAssoc();

print "Initial data: <br /><pre>";
print_r($menu);
print "</pre>";

$fields = array(
    'label' => 'This is the menu label.',
    'page' => 'home',//e.g relative link to the page'
    'weight' => 6,
);
$db->insert('menu')->fields($fields)->run();

$menu = $db->select('menu')->fields(array('label', 'page'))->orderby('weight')->run()->fetchAllAssoc();

print "Data after insert: <br /><pre>";
print_r($menu);
print "</pre>";

$updatefields = array(
    'label' => 'This is the new label.',
    'page' => 'contact',
    'weight' => 5
);

$db->update('menu')->fields($updatefields)->where('id', $db->lastInsertId())->run();

$menu = $db->select('menu')->fields(array('label', 'page'))->orderby('weight')->run()->fetchAllAssoc();

print "Data after update: <br /><pre>";
print_r($menu);
print "</pre>";
?>