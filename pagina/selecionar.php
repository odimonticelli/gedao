<?php
require_once('../inc/class.TemplatePower.php');

//layout "mestre"
$tpl = new TemplatePower('../tpl/_master.htm'); 
$tpl->assignInclude('content', '../tpl/selecionar.htm');
$tpl->prepare();

// ---------- PROCESSAMENTO ------------

//da base padrao, no momento FarmaSIS
include('../inc/inc.dbconfig.php');
$tpl->assign('host', $host);
$tpl->assign('user', $user);
$tpl->assign('pass', $pass);
$tpl->assign('base', $base);


//depois que faz a conex�o, para escolher a base
if ( isset($_REQUEST['sub1']) && !empty($_REQUEST['sub1']) ) 
{
	//pega os dados enviados do form
	$host = $_REQUEST['host'];
	$user = $_REQUEST['user'];
	$pass = $_REQUEST['pass'];
	$base = $_REQUEST['base'];
	$link = "mysql:host=$host;dbname=$base";
	$opts = array(PDO::ATTR_AUTOCOMMIT=>TRUE);


	//mostra na tela
	$tpl->assign('host', $host);
	$tpl->assign('user', $user);
	$tpl->assign('pass', $pass);
	$tpl->assign('base', $base);
	//conexao
	$cnx = new PDO($link, $user, $pass, $opts);

	//lista as bases disponiveis
	$sql = "SHOW DATABASES";
	$res = $cnx->query($sql);
	foreach ($res as $vet) {
		$bas = $vet['Database'];
		$tpl->newBlock('bases');
		$tpl->assign('bas', $bas);
		if ($bas == $base) {
			$tpl->assign('sel', 'selected="selected"');
		} else {
			$tpl->assign('sel', '');
		}
	}

	//para exibir as tabelas da base selecionada
	$tpl->newBlock('selecionar');
	
	//seleciona as tabelas para exibir no select
	//obter os nomes das tabelas
	$sql = "SHOW TABLES"; //die($sql);
	$res = $cnx->query($sql);
	foreach ($res as $vet) {
		$tab = $vet[0];

		$tpl->newBlock('tabelas1');
		$tpl->assign('tab', $tab);
		
		$tpl->newBlock('tabelas2');
		$tpl->assign('tab', $tab);
	}

	//para mostrar nos campos ocultos que v�o enviar ao exec.php
	$tpl->gotoBlock('selecionar');
	$tpl->assign('host', $host);
	$tpl->assign('user', $user);
	$tpl->assign('pass', $pass);
	$tpl->assign('base', $base);
}

// ---------- PROCESSAMENTO ------------

$tpl->printToScreen();
?>