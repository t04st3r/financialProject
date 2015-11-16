<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of db
 *
 * @author raffaele
 */
class db {

    private $schema = 'financial';
    private $user = 'fin_user';
    private $password = 'Fin::Account::314';
    private $host = 'localhost';
    private $conn = null;
    private $result;

    function __construct() {
        if (!isset($this->conn) || $this->conn == null) {
            $this->conn = new mysqli($this->host, $this->user, $this->password, $this->schema);
            if (mysqli_connect_errno()) {
                die("Error while connecting to the database: " . mysqli_connect_error());
            }
        }
    }

    function __destruct() {
        if (isset($this->conn) || $this->conn != null) {
            unset($this->conn);
        }
    }

    private function error_message() {
        return "Database Error: " . mysqli_error($this->conn);
    }

    public function checkLogin($user_name, $password, $matrix_array) {
        $stmt = $this->conn->prepare("SELECT customer_id FROM customer WHERE user_name = ? AND password = ?");
        $stmt->bind_param('ss', $user_name, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $stmt->affected_rows;
        if ($rows != 1) {
            return array('result' => false, 'matrix' => false);
        }
        $result_set = $result->fetch_array(MYSQLI_ASSOC);
        $id = $result_set['customer_id'];
        $matrix_result = $this->checkMatrix($matrix_array, $id);
        if (!$matrix_result) {
            return array('result' => false, 'matrix' => true);
        }
        return array('result' => true , 'id' => $id);
    }
    
    public function getUserNameSurname($id){
        $query = "SELECT name, surname FROM customer WHERE customer_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $result_set = $result->fetch_array(MYSQLI_ASSOC);
        $name = $result_set['name'];
        $surname = $result_set['surname'];
        return $name.' '.$surname;
    }

    private function checkMatrix($matrix_array, $id) {
        $query = "SELECT customer_id FROM matrix WHERE customer_id = $id";
        foreach ($matrix_array as $key => $value) {
            $query .= " AND " . $key . " = '" . $value . "'";
        }
        $this->conn->query($query);
        $row = $this->conn->affected_rows;
        return $row == 1;
    }
    
    public function getAccountCards($id){
        $query = "SELECT account_number, balance, currency, account.card_number, type_name, card_circuit AS circuit "
                . "FROM account, account_type, card WHERE customer_id = ? "
                . "AND account_type = id_type "
                . "AND account.card_number = card.card_number;";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $result_set = $result->fetch_all(MYSQLI_ASSOC);
        return $result_set;
    }

}
