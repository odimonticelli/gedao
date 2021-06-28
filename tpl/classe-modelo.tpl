/**
 * Classe que representa a tabela "{tab}" da base "{bas}"
 */
class {tab}
{
	/**
	 * método construtor
	 */ 
	public function __construct(){}

	/**
	 * metodo clone do tipo privado previne a clonagem dessa instância da classe
	 */
	private function __clone(){}

	/**
	 * metodo unserialize do tipo privado para prevenir a desserialização da instância dessa classe.
	 */
	private function __wakeup(){}

	/**
	 * destrutor do objeto da classe
	 */
	public function __destruct(){}


	/**
	 * atributos (variáveis) relacionadas às colunas da tabela
	 */

	<!-- START BLOCK : atributos -->
	private ${col};
	<!-- END BLOCK : atributos -->
	
	
	/**
	 * métodos para obter e ajustar dados das variáveis (get e set)
	 */
	
	<!-- START BLOCK : metodos -->
	// -- {col}
	public function set{col2}(${col}) {
		$this->{col} = ${col};
	}
	public function get{col2}() {
		return $this->{col};
	}
	<!-- END BLOCK : metodos -->
	
}