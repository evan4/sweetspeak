<?php

namespace Mycms;

use PDO;
use Exception;

class Model
{
	private
		$table,
		$columns = [],
		$pdo;
 
	public function __construct($table) 
	{
		try {
			$this->pdo = new PDO($_ENV['DB_DRIVER'].":host=localhost;charset=utf8;
				dbname=".$_ENV['DB_NAME'],
				$_ENV['DB_USERNAME'], 
				$_ENV['DB_PASSWORD'], 
			[
				PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING
			]);
		} catch (PDOException $e) {
			echo 'Connection failed: ' . $e->getMessage();
			exit;
		}
		$this->table = $table;
		$this->columnsmeta();
		
	}

	public function select(array $params = null, array $data = null)
	{
		
		$endQuery = '';

		if (array_key_exists('endQuery', $data)) {
			$endQuery = array_pop($data);
		}
		
		$sum =  '';

		if (array_key_exists('sum', $data)) {
			$sum = array_pop($data);
		}

		$params = $params ? implode(', ', array_values($params)) 
			: implode(', ', array_keys($this->columns));
		
		$sql = "SELECT " . $params . $sum . " FROM ".$this->table;

		$join = '';

		if (array_key_exists('join', $data)) {
			$join = array_pop($data);
		}
		
		$sql .= $join;

		if($data){
			$sql .=  " WHERE ";
			foreach ($data as $key => $value) {
				//last element
				if (!next($data)) {
					$sql .= "$key = ? ";
				}else{
					$sql .= "$key = ? AND ";
				}
			}
		}
		
		$stmt = $this->pdo->prepare($sql);

		if($data){
			$index = 1;
			foreach ($data as $key => $value) {
				//last element
				$stmt->bindValue($index, $value, $this->type($this->columns[$key]) );
				$index+=1;
			}
		}

		$this->pdo->beginTransaction();

		$stmt->execute();
		
		$this->pdo->commit();

		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function selectAll(array $params = null, array $data = null)
	{
		$endQuery = '';

		if (array_key_exists('endQuery', $data)) {
			$endQuery = array_pop($data);
		}

		$condition = [];

		if (array_key_exists('condition', $data)) {
			$condition = array_pop($data);
		}
		
		$sum =  '';

		if (array_key_exists('sum', $data)) {
			$sum = array_pop($data);
		}

		$params = $params ? implode(', ', array_values($params)) 
			: implode(', ', array_keys($this->columns));

		$sql = "SELECT " . $params . $sum ." FROM ".$this->table;

		$join = '';

		if (array_key_exists('join', $data)) {
			$join = array_pop($data);
		}
		
		$sql .= $join;
		
		if(count($data) > 0){
			$sql .=  " WHERE ";
			$lastKey = array_key_last($data);
			foreach ($data as $key => $value) {
				$cond = "=";
				if(count($condition) > 0){
					if(array_key_exists($key, $condition)){
						$cond = $condition[$key];
					}
				}
				
				//last element
				if ($key === $lastKey) {
					$sql .= "$key $cond ? ";
				}else{
					$sql .= "$key $cond ? AND ";
				}
			}
		}

		$sql .= $endQuery;
		
		//var_dump($sql);
		$stmt = $this->pdo->prepare($sql);
	
		if(count($data) > 0){
			$index = 1;
			foreach ($data as $key => $value) {
				//last element
				$stmt->bindValue($index, $value, $this->type($this->columns[$key]) );
				$index+=1;
			}
		}
		$this->pdo->beginTransaction();

		$stmt->execute();
		
		$this->pdo->commit();

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function count( $field, array $data)
	{
		$condition = [];

		if (array_key_exists('condition', $data)) {
			$condition = array_pop($data);
		}

		$endQuery = '';

		if (array_key_exists('endQuery', $data)) {
			$endQuery = array_pop($data);
		}

		$sql = "SELECT count(" . $field . ") as total FROM ".$this->table;
		if(count($data)){
			$sql .= " WHERE ";
			foreach ($data as $key => $value) {
				$cond = "=";
				if(count($condition) > 0){
					if(array_key_exists($key, $condition)){
						$cond = $condition[$key];
					}
				}
				
				//last element
				if (!next($data)) {
					$sql .= "$key $cond ? ";
				}else{
					$sql .= "$key $cond ? AND ";
				}
			}
		}
		$sql .= $endQuery;

		$stmt = $this->pdo->prepare($sql);

		if($data){
			$index = 1;
			foreach ($data as $key => $value) {
				//last element
				$stmt->bindValue($index, $value, $this->type($this->columns[$key]) );
				$index+=1;
			}
		}
		
		$this->pdo->beginTransaction();

		$stmt->execute();
		
		$this->pdo->commit();

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function insert(array $data)
	{
		$fields = implode(', ', array_keys($data));
		
		$sql = "INSERT INTO " .$this->table. " (". $fields . ") VALUES (";
		
		$lastKey = array_key_last($data);
		foreach ($data as $key => $value) {
			//last element
			if ($key === $lastKey) {
				$sql .= "?)";
			}else{
				$sql .= "?, ";
			}
		}
		
		$stmt = $this->pdo->prepare($sql);

		try {
			$this->pdo->beginTransaction();

			$index = 1;
			foreach ($data as $key => $value) {
				//last element
				$stmt->bindValue($index, $value, $this->type($this->columns[$key]) );
				$index+=1;
			}

			$res = $stmt->execute();
			$id = $this->pdo->lastInsertId();

			$this->pdo->commit();

			return [
				'success' => $res,
				'id' => $id
			];

		} catch (Exception $e) {
			$this->pdo->rollBack();
			echo "Failed: " . $e->getMessage();
		}
		
	}	

	public function update(array $data)
	{
		$id = array_shift($data);
		
		$fields = implode(', ', array_keys($data));
		
		$sql = "UPDATE " .$this->table. " SET ";

		foreach ($data as $key => $value) {
			$sql .= $this->table.".".$key ."=?, ";
		}

		$sql = substr($sql, 0, -2).' ';
		$sql .= " WHERE ".$this->table.".id=".$id;
		
		$stmt = $this->pdo->prepare($sql);
		
		try {
			$this->pdo->beginTransaction();

			$index = 1;
			foreach ($data as $key => $value) {
				//last element
				$stmt->bindValue($index, $value, $this->type($this->columns[$this->table.".".$key]) );
				$index+=1;
			}
			
			$res = $stmt->execute();

			$this->pdo->commit();

			return $res;

		} catch (Exception $e) {
			$this->pdo->rollBack();
			echo "Failed: " . $e->getMessage();
		}
		
	}	

	public function delete(array $data)
	{
		$fields = implode(', ', array_keys($data));
		
		$sql = "DELETE FROM " .$this->table. " WHERE ";

		foreach ($data as $key => $value) {
			//last element
			if (!next($data)) {
				$sql .= "$key = ? ";
			}else{
				$sql .= "$key = ? AND ";
			}
		}
		
		$stmt = $this->pdo->prepare($sql);

		try {
			$this->pdo->beginTransaction();

			$index = 1;
			foreach ($data as $key => $value) {
				//last element
				$stmt->bindValue($index, $value, $this->type($this->columns[$key]) );
				$index+=1;
			}
			$stmt->execute();
			$res = $stmt->rowCount();
			
			$this->pdo->commit();

			return $res;

		} catch (Exception $e) {
			$this->pdo->rollBack();
			echo "Failed: " . $e->getMessage();
		}
	}

	private function columnsmeta()
	{
		$q = $this->pdo->query("DESCRIBE $this->table");
		
		$result = $q->fetchAll(PDO::FETCH_ASSOC);
		
		foreach($result as $column){
			$str=strpos($column['Type'], "(");
			$row=substr($column['Type'], 0, $str);

			$this->columns[$this->table.'.'.$column['Field']] = $row;;
		}
	}

	/**
	*	Map data type of argument to a PDO constant
	**/
	private function type($val) 
	{
		switch (gettype($val)) {
			case 'NULL':
				return PDO::PARAM_NULL;
			case 'boolean':
				return PDO::PARAM_BOOL;
			case 'int':
				return PDO::PARAM_INT;
			case 'resource':
				return PDO::PARAM_LOB;
			case 'float':
				return self::PARAM_FLOAT;
			default:
				return PDO::PARAM_STR;
		}
	}

}
