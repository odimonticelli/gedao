<?php

if (isset($_REQUEST['base']) && !empty($_REQUEST['base'])) {
	$base = $_REQUEST['base'];
} else {
	$base = '';
}

$host = 'mysql.servidor.com.br';
$base = 'base';
$user = 'user';
$pass = 'senha';
?>