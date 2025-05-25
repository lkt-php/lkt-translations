#!/usr/bin/env php
<?php

ini_set('display_errors', 0);
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);

session_start();

require __DIR__ .'/vendor/autoload.php';

\Lkt\Commander\Commander::run();