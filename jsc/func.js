/*
 * Fun��es de JS para uso geral
 */

//fun��o que recebe dois par�metros
//o ID da not�cia e o t�tulo...
function delNot(idn, tit) 
{
	ok = window.confirm("Deseja excluir a not�cia \n" + tit + "?");
	if (ok) {
		location.href = "noticias-exec.php?act=delete&idn="+idn;
	}
}

