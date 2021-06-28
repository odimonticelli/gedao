/*
 * Funções de JS para uso geral
 */

//função que recebe dois parâmetros
//o ID da notícia e o título...
function delNot(idn, tit) 
{
	ok = window.confirm("Deseja excluir a notícia \n" + tit + "?");
	if (ok) {
		location.href = "noticias-exec.php?act=delete&idn="+idn;
	}
}

