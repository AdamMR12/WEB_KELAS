<?php
// Workaround: folder public/kas/ (aset CSS) menutupi route /kas di PHP built-in server.
// File ini meneruskan request /kas ke bootstrap Laravel (public/index.php).
require dirname(__DIR__) . '/index.php';
