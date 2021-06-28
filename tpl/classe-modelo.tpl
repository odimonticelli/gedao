/**
 * Classe que representa a tabela "{tab}" da base "{bas}"
 */
class {tab}
{
	/**
	 * m�todo construtor
	 */ 
	public function __construct(){}

	/**
	 * metodo clone do tipo privado previne a clonagem dessa inst�ncia da classe
	 */
	private function __clone(){}

	/**
	 * metodo unserialize do tipo privado para prevenir a desserializa��o da inst�ncia dessa classe.
	 */
	private function __wakeup(){}

	/**
	 * destrutor do objeto da classe
	 */
	public function __destruct(){}


	/**
	 * atributos (vari�veis) relacionadas �s colunas da tabela
	 */

	<!-- START BLOCK : atributos -->
	private ${col};
	<!-- END BLOCK : atributos -->
	
	
	/**
	 * m�todos para obter e ajustar dados das vari�veis (get e set)
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