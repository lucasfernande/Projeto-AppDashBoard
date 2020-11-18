<?php  

	class Dashboard {
		private $dataInicio;
		private $dataFim;
		private $numeroVendas;
		private $totalVendas;
		private $clientesAtivos;
		private $clientesInativos;
		private $totalDespesas;

		public function __get($attr) {
			return $this->$attr;
		}

		public function __set($attr, $value) {
			$this->$attr = $value;
		}

		# método para criar um json no retorno da requisição mesmo com os atributos sendo private
		public function criarJson() {
            $jsonDashboard = json_encode(get_object_vars($this)); 
            return $jsonDashboard;
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

		public function getClientesAtivos() {
			$query = 'select count(*) as clientesAtivos from tb_clientes where cliente_ativo = 1';

			$statemt = $this->conn->prepare($query);
			$statemt->execute();

			return $statemt->fetch(PDO::FETCH_OBJ)->clientesAtivos;
		}

		public function getClientesInativos() {
			$query = 'select count(*) as clientesInativos from tb_clientes where cliente_ativo = 0';

			$statemt = $this->conn->prepare($query);
			$statemt->execute();

			return $statemt->fetch(PDO::FETCH_OBJ)->clientesInativos;
		}

		public function getTotalDespesas() {
			$query = 'select SUM(total) as totalDespesas from tb_despesas where data_despesa BETWEEN :dataInicio AND :dataFim';

			$statemt = $this->conn->prepare($query);
			$statemt->bindValue(':dataInicio', $this->dashboard->__get('dataInicio'));
			$statemt->bindValue(':dataFim', $this->dashboard->__get('dataFim'));
			$statemt->execute();

			return $statemt->fetch(PDO::FETCH_OBJ)->totalDespesas;
		}
	}

	$conn = new Conn();
	$dashboard = new Dashboard();
	$bd = new BD($conn, $dashboard);

	$competencia = explode('-', $_GET['competencia']); // separando ano e mes para calcular o ultimo dia deste mes 

	$ano = $competencia[0];
	$mes = $competencia[1];

	$diasMes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano); // calculando quantos dias tem determinado mês (28, 30 ou 31)

	$dashboard->__set('dataInicio',  $_GET['competencia'].'-01');
	$dashboard->__set('dataFim', $_GET['competencia'].'-'.$diasMes); //	formando a data final com o ultimo dia do mes
	$dashboard->__set('numeroVendas', $bd->getNumeroVendas());
	$dashboard->__set('totalVendas', $bd->getTotalVendas());
	$dashboard->__set('clientesAtivos', $bd->getClientesAtivos());
	$dashboard->__set('clientesInativos', $bd->getClientesInativos());
	$dashboard->__set('totalDespesas', $bd->getTotalDespesas());
	echo $dashboard->criarJson();
?>