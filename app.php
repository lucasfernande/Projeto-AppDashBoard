<?php  

	class Dashboard {
		private $dataInicio;
		private $dataFim;
		private $numeroVendas;
		private $totalVendas;

		public function __get($attr) {
			return $this->$attr;
		}

		public function __set($attr, $value) {
			$this->$attr = $value;
		}
	}

	# classe de conexão com a database
	class Conn {
		private $host = 'localhost';
		private $dbname = 'dashboard';
		private $user = 'root';
		private $pass = '';

		public function conectar() {
			try {
				$conn = new PDO("mysql:host=$this->host;dbname=$this->dbname", "$this->user", "$this->pass");
				$conn->exec('set charset set utf8'); // essa instrução faz a instância da conexão trabalhar com UTF-8 
				return $conn;
			} catch (PDOException $e) {
				echo '<p>'.$e->getMessage().'</p>';
			}	
		}
	}

	# classe para manipular o objeto no banco (service)
	class BD {
		private $conn;
		private $dashboard;

		public function __construct(Conn $conn, Dashboard $dashboard) {
			$this->conn = $conn->conectar();
			$this->dashboard = $dashboard;
		}

		public function getNumeroVendas() {
			$query = 'select count(*) as numeroVendas from tb_vendas where data_venda BETWEEN :dataInicio AND :dataFim' ;

			$statemt = $this->conn->prepare($query);
			$statemt->bindValue(':dataInicio', $this->dashboard->__get('dataInicio'));
			$statemt->bindValue(':dataFim',  $this->dashboard->__get('dataFim'));
			$statemt->execute();

			return $statemt->fetch(PDO::FETCH_OBJ)->numeroVendas; # retornando apenas o numeroVendas, e não o objeto;
		}

		public function getTotalVendas() {
			$query = 'select SUM(total) as totalVendas from tb_vendas where data_venda BETWEEN :dataInicio AND :dataFim' ;

			$statemt = $this->conn->prepare($query);
			$statemt->bindValue(':dataInicio', $this->dashboard->__get('dataInicio'));
			$statemt->bindValue(':dataFim',  $this->dashboard->__get('dataFim'));
			$statemt->execute();

			return $statemt->fetch(PDO::FETCH_OBJ)->totalVendas; # retornando apenas o totalVendas, e não o objeto;
		}
	}

	$conn = new Conn();
	$dashboard = new Dashboard();
	$bd = new BD($conn, $dashboard);

	$dashboard->__set('dataInicio', '2020-08-01'); // teste
	$dashboard->__set('dataFim', '2020-08-31'); // teste

	$dashboard->__set('numeroVendas', $bd->getNumeroVendas());
	$dashboard->__set('totalVendas', $bd->getTotalVendas());
	print_r($dashboard);
?>