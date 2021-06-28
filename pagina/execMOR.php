<?php
set_time_limit(0);
require_once('../inc/class.TemplatePower.php');


// ---------- PROCESSAMENTO ------------
if ( isset($_REQUEST['sub']) && !empty($_REQUEST['sub']) ) 
{
	$host = $_REQUEST['host'];
	$user = $_REQUEST['user'];
	$pass = $_REQUEST['pass'];
	$base = $_REQUEST['base'];
	$link = "mysql:host=$host;dbname=$base";
	//conexao
	$cnx = new PDO($link, $user, $pass);
	
	$tab = $_REQUEST['tab'];
	if ($tab == '%') {
		$sql = "SHOW TABLES";
	} else {
		$sql = "SHOW TABLES WHERE Tables_in_$base LIKE '$tab'";
	}

	$restab = $cnx->query($sql);
	foreach ($restab as $rowtab) 
	{
		$txt = '';
		//layout "mestre"
		$tpl = new TemplatePower('../tpl/classe-modelo.tpl'); 
		$tpl->prepare();

		$tabela = $rowtab["Tables_in_$base"];
		
		$tpl->assign('bas', $base);
		$tpl->assign('tab', ucfirst(strtolower($tabela)));

		//ini - pegar os nomes das colunas da tabela
		$res = $cnx->query("SHOW COLUMNS FROM $tabela");
		foreach ($res as $row) {
			$col = $row['Field'];
			$col = strtolower($col);
			$col2 = ucfirst($col); //primeira letra Mai�scula
			
			$tpl->newBlock('atributos');
			$tpl->assign('col', $col);
			$tpl->assign('col2', $col2);
			
			$tpl->newBlock('metodos');
			$tpl->assign('col', $col);
			$tpl->assign('col2', $col2);
		}
		//fim - mesclar com o template para gerar 1 classe

		$txt = $tpl->getOutputContent();
		//cria um arquivo e envia ao solicitante
		$tabela = strtolower($tabela);
		$tabela = ucfirst($tabela);
		$dir = '../classes/'.$host.'-'.$user.'-'.$base.'/';
		if (is_dir($dir)) {
			$okd = true;
		} else {
			$okd = mkdir($dir); 
		}
		if ($okd) {
			$arq = 'class.'.$tabela.'.php';
			$ref = fopen($dir.$arq, 'w');
			$codigo = 
"<?php
	$txt 
?>";
			fwrite($ref, $codigo);
			fclose($ref);
		}

	}
	//fim do loop das tabelas
}
else 
{
	header('location: ../pagina/selecionar.php');
	exit;
}
// ---------- PROCESSAMENTO ------------



if (isset($_REQUEST['exp']) && $_REQUEST['exp'] == 'sim')
{
	//se for todos, pega do diret�rio e compacta
	if ($tab == '%') {
		$arquivo = 'ClassesMOR.zip';
		$zip = new ZipArchive();
		$zip->open($arquivo, ZIPARCHIVE::CREATE);
		$diretorio = dir('../classes/'.$host.'-'.$user.'-'.$base.'/');
		while($arqdir = $diretorio->read()) {
			$zip->addFile('../classes/'.$host.'-'.$user.'-'.$base.'/'.$arqdir);
		}
		$diretorio->close();
		$zip->close();
		header("Content-Type: application/zip"); // informa o tipo do arquivo ao navegador
		header("Content-Transfer-Encoding: Binary");
	}
	else {
	//se for apenas 1
		$arquivo = $dir.$arq;
		header("Content-Type: PHP"); // informa o tipo do arquivo ao navegador
	}

	header("Content-Length: ".filesize($arquivo)); //informa o tamanho do arquivo ao navegador
	header("Content-Disposition: attachment; filename=".basename($arquivo)); //informa ao navegador que � tipo anexo e faz abrir a janela de download, tambem informa o nome do arquivo
	readfile($arquivo); // l� o arquivo
	exit; // aborta p�s-a��es
}
else 
{
	//aviso na tela e link de retorno
	echo '<a href="javascript:history.go(-1);">&laquo; Voltar</a>';
	echo '<br />';
	echo '<hr />';
	echo '<br />';

	if ($tab != '%') {
		//mostra na tela
		echo '<pre>';
		echo $txt;
		echo '</pre>';
	}
	else {
		//mostra aviso para pegar no FTP
		echo 'Classes MOR geradas com sucesso. Baixe-as do FTP.';
	}
}
?>