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
		$tpl = new TemplatePower('../tpl/classe-modelo-dao.tpl'); 
		$tpl->prepare();
		
		$tabela = $rowtab["Tables_in_$base"];
		
		$tpl->assign('bas', $base);
		$tpl->assign('tab', ucfirst(strtolower($tabela)));
		$tpl->assign('tab2', $tabela); 
		
		//$dba->connect($host, $user, $pass, $base);
		new PDO($link, $user, $pass);
		
		//ini - pegar os nomes das colunas da tabela
		$res = $cnx->query("SHOW COLUMNS FROM $tabela") or die(mysql_error());
		$i = 0;
		foreach ($res as $row) {
			$col = $row['Field'];
			$col2 = ucfirst(strtolower($col)); //primeira letra Mai�scula
			if ($row['Key'] == 'PRI') {
				$colid = $col;
			}
			
			//para insert
			if ($col != $colid) {
				$tpl->newBlock('dados_ins');
				$tpl->assign('col', $col);
				$tpl->assign('col2', $col2);
			}
			
			if ($col != $colid) {
				$tpl->newBlock('colunas1_ins');
				$tpl->assign('col', $col);
				$tpl->assign('col2', $col2);
				if ($i+1 < $num)
					$tpl->assign('vir', ',');
			}
			
			if ($col != $colid) {
				$tpl->newBlock('colunas2_ins');
				$tpl->assign('col', $col);
				$tpl->assign('col2', $col2);
				if ($i+1 < $num)
					$tpl->assign('vir', ',');
			}
			//-----------
			
			//para update
			$tpl->newBlock('dados_upd');
			$tpl->assign('col', $col);
			$tpl->assign('col2', $col2);
			
			if ($col != $colid) {
				$tpl->newBlock('colunas1_upd');
				$tpl->assign('col', $col);
				$tpl->assign('col2', $col2);
				if ($i+1 < $num)
					$tpl->assign('vir', ',');
				$tpl->gotoBlock('_ROOT');
				$tpl->assign('colid', $colid);
			}
			//-----------
			
			//para delete
			//$tpl->newBlock('dados_del');
			$tpl->assign('col', $col);
			$tpl->assign('col2', $col2);
			
			if ($col != $colid) {
				$tpl->gotoBlock('_ROOT');
				$tpl->assign('colid', $colid);
			}
			//-----------
			
			//para selecionar todos
			// $tpl->newBlock('colunas1_selAll');
			// $tpl->assign('col', strtolower($col));
			//$tpl->assign('col2', $col2);
			//-----------
			
			//para selecionar coforme filtro
			$tpl->newBlock('colunas1_selFil');
			$tpl->assign('col', $col);
			//$tpl->assign('col2', $col2);
			//-----------

			$i++;
		}
		
		$tpl->gotoBlock('_ROOT');

		
		//PARA GERAR OS M�TODOS COM OS INNER JOIN
		$base2 = 'information_schema';
		$link2 = "mysql:host=$host;dbname=$base2";
		//$cnx1 = $dba->connect($host, $user, $pass, $base2);
		$cnx1 = new PDO($link2, $user, $pass);
		$sql1 = "SELECT TABLE_NAME, COLUMN_NAME 
				 FROM $base2.KEY_COLUMN_USAGE 
				 WHERE REFERENCED_TABLE_NAME = '$tabela' 
				 AND REFERENCED_COLUMN_NAME = '$colid'
				 AND TABLE_SCHEMA = '$base2';"; //die($sql1);
		$res1 = $cnx1->query($sql1);
		$num1 = $cnx1->num_rows;
		if ($num1 > 0) {
			$tpl->newBlock('temFK');
			$tpl->assign('tab2', strtolower($tabela));
			
			$vet = array();
			foreach ($res1 as $linha) {
				$tabFK = $linha['TABLE_NAME'];
				$colFK = $linha['COLUMN_NAME'];
				
				//criar um array para gravar os nomes dos m�todos
				//se j� tem o m�todo criado n�o cria novamente
				if (!in_array($tabFK, $vet)) 
				{
					$tpl->newBlock('fks');
					$tpl->assign('tab', ucfirst(strtolower($tabela)));
					$tpl->assign('tabFK', ucfirst(strtolower($tabFK)));
					$tpl->assign('tab2', strtolower($tabela));
					$tpl->assign('tab2FK', strtolower($tabFK));
					$tpl->assign('colpk', $colid);
					$tpl->assign('colfk', $colFK);
					
					//$cnx2 = $dba->connect($host, $user, $pass, $base);
					$cnx2 = new PDO($link, $user, $pass);
					$sql2 = "SHOW COLUMNS FROM $tabFK;";
					$res2 = $cnx2->query($sql2);
					$num2 = $cnx2->num_rows;
					foreach ($res2 as $linha2) {
						$col = $linha2['Field'];
						$tpl->newBlock('colunas1_selFks');
						$tpl->assign('col', $col);
					}

					array_push($vet, $tabFK);
				}
				
			}
		}



		//PARA GERAR OS M�TODOS COM OS INNER JOIN - 2 ("baixo pra cima")
		$base2 = 'information_schema';
		$link2 = "mysql:host=$host;dbname=$base2";
		//$cnx1 = $dba->connect($host, $user, $pass, $base2);
		$cnx1 = new PDO($link2, $user, $pass);
		$sql1 = "SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME	
				FROM $base2.KEY_COLUMN_USAGE
				WHERE TABLE_NAME = '$tabela' and REFERENCED_TABLE_NAME is not null and TABLE_SCHEMA = '$base2';";
		$res1 = $cnx1->query($sql1);
		$num1 = $cnx1->num_rows;
		if ($num1 > 0) {
			$tpl->newBlock('temFK2');
			$tpl->assign('tab2', strtolower($tabela));
			
			$vet = array();
			foreach ($res1 as $linha) {
				$tabPK = $linha['REFERENCED_TABLE_NAME'];
				$colPK = $linha['REFERENCED_COLUMN_NAME'];
				$colFK = $linha['COLUMN_NAME'];
				
				//criar um array para gravar os nomes dos m�todos
				//se j� tem o m�todo criado n�o cria novamente
				if (!in_array($tabPK, $vet)) 
				{
					// $tabPK = strtolower($tabPK);
					// $colPK = strtolower($colPK);
					// $colFK = strtolower($colFK);

					$tpl->newBlock('fks2');
					$tpl->assign('tabPK', ucfirst(strtolower($tabPK)));
					$tpl->assign('tab2PK', strtolower($tabPK));
					$tpl->assign('colpk', strtolower($colPK));
					$tpl->assign('tab2', strtolower($tabela));
					$tpl->assign('colfk', strtolower($colFK));
					
					//$cnx2 = $dba->connect($host, $user, $pass, $base);
					$cnx2 = new PDO($link2, $user, $pass);
					$sql2 = "SHOW COLUMNS FROM $tabPK;";
					$res2 = $cnx2->query($sql2);
					$num2 = $cnx2->num_rows;
					foreach ($res2 as $linha2) {
						$col = $linha2['Field'];
						$tpl->newBlock('colunas1_selFks2');
						$tpl->assign('col', strtolower($col));
					}
					
					array_push($vet, $tabPK);
				}
				
			}
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
			$arq = 'class.'.$tabela.'DAO.php';
			$ref = fopen($dir.$arq, 'w');
			$codigo = 
"<?php
	$txt 
?>";
			fwrite($ref, $codigo);
			fclose($ref);
		}

	}
	//fim while de cada tabela
	
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
		$arquivo = 'ClassesDAO.zip';
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
		echo utf8_encode($txt);
		echo '</pre>';
	}
	else {
		//mostra aviso para pegar no FTP
		echo 'Classes DAO geradas com sucesso. Baixe-as do FTP.';
	}
}
?>