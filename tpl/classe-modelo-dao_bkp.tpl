
/**
 * Classe que representa os comandos de acesso aos dados
 * da tabela "{tab}" da base "{bas}"
 */
class {tab}DAO
{
	//objeto que representa o uso da classe "DbAdmin"
	//para manipula��o gen�rica do SGBD
	private $dba;
	
	//m�todo construtor que j� faz a conex�o com o BD
	public function {tab}DAO() {
		$dba = new DbAdmin();
		$dba->connectDefault();
		$this->dba = $dba;
	}
	
	//m�todo que faz a inser��o de um {tab} no BD
	public function insert($obj) {
		//pegar os dados do objeto
		<!-- START BLOCK : dados_ins -->
		${col} = $obj->get{col2}();
		<!-- END BLOCK : dados_ins -->
		
		//montar o comando SQL
		$sql = "insert into {tab2} 
				(
				<!-- START BLOCK : colunas1_ins -->
				{col}{vir}
				<!-- END BLOCK : colunas1_ins -->
				) 
				values 
				(
				<!-- START BLOCK : colunas2_ins -->
				'${col}'{vir}
				<!-- END BLOCK : colunas2_ins -->
				)";
		//executar o comando SQL 
		return $this->dba->query($sql);
	}
	
	
	//m�todo que faz a atualiza��o de um {tab} no BD
	public function update($obj) {
		//pegar os dados do objeto
		<!-- START BLOCK : dados_upd -->
		${col} = $obj->get{col2}();
		<!-- END BLOCK : dados_upd -->
		
        $campos = '';
        <!-- START BLOCK : colunas1_upd -->
        if (!empty(${col})) {
        	$campos .= "{col}='${col}', ";
        }
        <!-- END BLOCK : colunas1_upd -->
        $campos = substr($campos,0,strrpos($campos,','));
        
		//montar o comando SQL
        $sql = "update {tab2} set
				$campos
				where {colid} = ${colid}";
		//executar o comando SQL 
		return $this->dba->query($sql);
        
        //montar o comando SQL
		//$sql = "update {tab2} set
		//		<!--STARTBLOCK:colunas1_upd-->
		//		{col}='${col}'{vir} 
		//		<!--ENDBLOCK:colunas1_upd-->
		//		where {colid} = ${colid}";
        
	}
	
	
	//m�todo que faz a exclus�o de um {tab} no BD
	public function delete($obj) {
		//pegar os dados do objeto
		<!-- START BLOCK : dados_del -->
		${col} = $obj->get{col2}();
		<!-- END BLOCK : dados_del -->
		
		//montar o comando SQL
		$sql = "delete from {tab2} 
				where {colid} = ${colid}";
		//executar o comando SQL 
		return $this->dba->query($sql);
	}
	
	
	//m�todo que retorna os regsitros da tabela {tab} conforme o filtro informado
	// - filtro e ordem opcionais
	public function select($where='', $order='') {
		if (!empty($where)) {
			$where = 'where '.$where;
		}
		if (!empty($order)) {
			$order = 'order by '.$order;
		}
		$sql = "select * from {tab2} $where $order";
		$res = $this->dba->query($sql);
		$num = $this->dba->rows($res);
		$vet = array();
		for ($i=0; $i<$num; $i++) {
			<!-- START BLOCK : colunas1_selFil -->
			$vet[$i]['{col}'] = $this->dba->result($res, $i, '{col}');
			<!-- END BLOCK : colunas1_selFil -->			
		}
		//matriz com os dados (linhas e colunas)
		return $vet;
	}
    
    
    //m�todo que retorna o �ltimo id cadastrado de {tab}
	public function lastId() {
		return $this->dba->lastid();
	}
    
    
    //m�todo que permite a execu��o de SQL livre
    public function execSql($sql='') {
    	if (!empty($sql) && $sql!='') {
            $res = $this->dba->query($sql);
            $num = $this->dba->rows($res);
            $vet = array();
            if ($num > 0) {
                //retorna um vetor associativo
                for ($i=0; $i<$num; $i++) {
                	$vet[$i] = $this->dba->fetch($res);
				}
            }
        } else {
        	$vet = array(); //vazio
        }
		return $vet;
    }
    
    
    
    <!-- START BLOCK : temFK -->
    //----------------------------------------
    //m�todos gerados para jun��o (inner join) com as tabelas usam '{tab2}' como FK
    
        <!-- START BLOCK : fks -->
        //m�todo de jun��o (inner join) e retorno de dados da tabela '{tabFK}' 
        public function list{tabFK}($filtro) {
            if (!empty($filtro)) {
                $filtro = 'AND '.$filtro;
            }
            $sql = "select {tab2FK}.* from {tab2}, {tab2FK}
                    where {tab2}.{colpk} = {tab2FK}.{colfk} 
                    $filtro";
            $res = $this->dba->query($sql);
            $num = $this->dba->rows($res);
            $vet = array();
            for ($i=0; $i<$num; $i++) {
                <!-- START BLOCK : colunas1_selFks -->
                $vet[$i]['{col}'] = $this->dba->result($res, $i, '{col}');
                <!-- END BLOCK : colunas1_selFks -->			
            }
            //matriz com os dados (linhas e colunas)
            return $vet;
        }
        
        <!-- END BLOCK : fks -->
    <!-- END BLOCK : temFK -->
}
