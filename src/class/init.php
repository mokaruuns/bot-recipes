<?php

namespace Bot\class;

require_once realpath(dirname(__FILE__)) . '/Database.php';
$db = new Database();
$db->fillDatabase();