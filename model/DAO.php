<?php
// DAO Class
class DAO {
    protected $db_conn;
    private $stmt;

    protected function openDB() {
		try {
			$conn_str = 'mysql:dbname=' . EW_CONN_DB .
						';host=' . EW_CONN_HOST .
						';port=' . EW_CONN_PORT;
			$this->db_conn = new PDO($conn_str, EW_CONN_USER, EW_CONN_PASS);
			//echo "<pre>";
			//print_r($this->db_conn);
			//echo "</pre>";
			
		} catch (PDOException $e) {
			//echo "-1";
				 echo "<pre>";
				 print_r($e);
				 echo "</pre>";
				 print_r($this->db_conn->errorInfo());
		}
    }

    protected function closeDB() {
		$this->db_conn = null;
    }

    protected function prepareQuery($sql) {
		$this->stmt = $this->db_conn->prepare($sql);
    }

    protected function bindQueryParam($param, $value) {
			$this->stmt->bindParam($param, $value);
	}
	
	protected function bindQueryParam2($param, $value) {
			$this->stmt->bindParam($param, $value, PDO::PARAM_INT);
	}

    protected function executeQuery() {
		$this->stmt->execute();
		return $this->stmt->fetchAll();
    }

    protected function executeUpdate() {
		try{
			return $this->stmt->execute();
		}catch(PDOException $e) {
			print_r ($e);
		}
    }

    protected function beginTrans() {
		$this->db_conn->beginTransaction();
    }

    protected function rollbackTrans() {
		$this->db_conn->rollBack();
    }

    protected function commitTrans() {
		$this->db_conn->commit();
    }
}
?>
