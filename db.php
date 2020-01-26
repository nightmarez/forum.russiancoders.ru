<?php
	require_once('db-config.php');

	class PdoDb
	{
		private $db;

		function __construct()
		{
			try {
				$this->db = new PDO('mysql:host=localhost;port=3306;dbname=' . DB_NAME, DB_USER, DB_PASS);
				$this->db->exec('SET NAMES utf8;');
				$this->db->exec('SET SESSION time_zone = "+3:00"');
			} catch (PDOException $e) {
				echo 'Error: ' . $e->getMessage() . '<br>';
				echo 'Line: ' . $e->getLine() . '<br>';
			}
		}

		function exec($q)
		{
			try {
				$this->db->exec($q);
			} catch (PDOException $e) {
				echo 'Error: ' . $e->getMessage() . '<br>';
				echo 'Line: ' . $e->getLine() . '<br>';
			}
		}

		function query($q)
		{
			try {
				return $this->db->query($q);
			} catch (PDOException $e) {
				echo 'Error: ' . $e->getMessage() . '<br>';
				echo 'Line: ' . $e->getLine() . '<br>';
			}
		}

		function prepare($q)
		{
			try {
				return $this->db->prepare($q);
			} catch (PDOException $e) {
				echo 'Error: ' . $e->getMessage() . '<br>';
				echo 'Line: ' . $e->getLine() . '<br>';
			}
		}

		function beginTransaction()
		{
			try {
				$this->db->beginTransaction();
			} catch (PDOException $e) {
				echo 'Error: ' . $e->getMessage() . '<br>';
				echo 'Line: ' . $e->getLine() . '<br>';
			}
		}

		function commit()
		{
			try {
				$this->db->commit();
			} catch (PDOException $e) {
				$this->$db->rollBack();
				echo 'Error: ' . $e->getMessage() . '<br>';
				echo 'Line: ' . $e->getLine() . '<br>';
			}
		}
	}