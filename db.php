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
                $this->db->exec('SET SESSION time_zone = "+8:00"');
            } catch (PDOException $e) {
                //if (strpos(gethostname(), 'russiancoders') !== false) {
                    // for local testing
                    echo 'Error: ' . $e->getMessage() . '<br>';
                    echo 'Line: ' . $e->getLine() . '<br>';
                    die();
                //}
            }
        }

        function exec($q)
        {
            try {
                $this->db->exec($q);
            } catch (PDOException $e) {
                //if (strpos(gethostname(), 'russiancoders') !== false) {
                    // for local testing
                    echo 'Error: ' . $e->getMessage() . '<br>';
                    echo 'Line: ' . $e->getLine() . '<br>';
                    die();
                //}
            }
        }

        function query($q)
        {
            try {
                return $this->db->query($q);
            } catch (PDOException $e) {
                //if (strpos(gethostname(), 'russiancoders') !== false) {
                    // for local testing
                    echo 'Error: ' . $e->getMessage() . '<br>';
                    echo 'Line: ' . $e->getLine() . '<br>';
                    die();
                //}
            }
        }

        function prepare($q)
        {
            try {
                return $this->db->prepare($q);
            } catch (PDOException $e) {
                //if (strpos(gethostname(), 'russiancoders') !== false) {
                    // for local testing
                    echo 'Error: ' . $e->getMessage() . '<br>';
                    echo 'Line: ' . $e->getLine() . '<br>';
                    die();
                //}
            }
        }

        function beginTransaction()
        {
            try {
                $this->db->beginTransaction();
            } catch (PDOException $e) {
                //if (strpos(gethostname(), 'russiancoders') !== false) {
                    // for local testing
                    echo 'Error: ' . $e->getMessage() . '<br>';
                    echo 'Line: ' . $e->getLine() . '<br>';
                    die();
                //}
            }
        }

        function commit()
        {
            try {
                $this->db->commit();
            } catch (PDOException $e) {
                $this->$db->rollBack();

                //if (strpos(gethostname(), 'russiancoders') !== false) {
                    // for local testing
                    echo 'Error: ' . $e->getMessage() . '<br>';
                    echo 'Line: ' . $e->getLine() . '<br>';
                    die();
                //}
            }
        }
    }
?>
