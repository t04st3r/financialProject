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
        return array('result' => true, 'id' => $id);
    }

    public function getUserNameSurname($id) {
        $query = "SELECT name, surname FROM customer WHERE customer_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $result_set = $result->fetch_array(MYSQLI_ASSOC);
        $name = $result_set['name'];
        $surname = $result_set['surname'];
        return $name . ' ' . $surname;
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

    public function getAccountCards($id) {
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

    public function getAccountsNumber($id) {
        $query = "SELECT account_number FROM account WHERE customer_id = ? ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $result_set = $result->fetch_all(MYSQLI_ASSOC);
        return $result_set;
    }

    
    public function transaction($cust_id, $cust_account, $amount, $ben_name, $ben_surname, $ben_account, $message, $ip) {

        //check whether customer account and beneficiary account are the same
        if (strcmp($cust_account, $ben_account) == 0) {
            $error = 'customer account cannot be equal to beneficiary account';
            $t_code = $this->writeTransactionLog($cust_account, $amount, $ben_account, $ip, $message, false, $error);
            return array('result' => false,
                'message' => 'Transaction Aborted:<br/>' . $error . '<br/>Transaction Code: ' . $t_code);
        }
        
        //check wheter the value of accounts numbers and amounts are numeric
        if (!is_numeric($amount) || !is_numeric($cust_account) || !is_numeric($ben_account)) {
            $error = 'bad data format';
            $t_code = $this->writeTransactionLog($cust_account, $amount, $ben_account, $ip, $message, false, $error);
            return array('result' => false,
                'message' => 'Transaction Aborted:<br/>' . $error . '<br/>Transaction Code: ' . $t_code);
        }
        
        //check whether the amount is higher the minimum required
        if ($amount < 100) {
            $error = 'minimun amount transferable is 100.00 $HK';
            $t_code = $this->writeTransactionLog($cust_account, $amount, $ben_account, $ip, $message, false, $error);
            return array('result' => false,
                'message' => 'Transaction Aborted:<br/>' . $error . '<br/>Transaction Code: ' . $t_code);
        }

        //check whether the amount is lover than the maximun funds transferable
        if ($amount > 500000) {
            $error = 'maximum amount transferable is 500,000.00 $HK, please contact your branch director for further information';
            $t_code = $this->writeTransactionLog($cust_account, $amount, $ben_account, $ip, $message, false, $error);
            return array('result' => false,
                'message' => 'Transaction Aborted:<br/>' . $error . '<br/>Transaction Code: ' . $t_code);
        }
        
        //check whether the customer account account exists
        $result_cust_account = $this->checkAccountExists($cust_account);
        if (!$result_cust_account['result']) {
            $error = 'unknown customer account';
            $t_code = $this->writeTransactionLog($cust_account, $amount, $ben_account, $ip, $message, false, $error);
            return array('result' => false,
                'message' => 'Transaction Aborted:<br/>' . $error . '<br/>Transaction Code: ' . $t_code);
        }

        //check whether the beneficiary account exists
        $result_account_exists = $this->checkAccountExists($ben_account);
        
        if (!$result_account_exists['result']) {
            $error = 'unknown beneficiary account';
            $t_code = $this->writeTransactionLog($cust_account, $amount, $ben_account, $ip, $message, false, $error);
            return array('result' => false,
                'message' => 'Transaction Aborted:<br/>' . $error . '<br/>Transaction Code: ' . $t_code);
        }
        
        
        //check whether the beneficiary name is exactly the same of the registered one
        $array_result = $result_account_exists['array'];
        
        if (strcasecmp($array_result['name'], $ben_name) != 0 || strcasecmp($array_result['surname'], $ben_surname) != 0 ) {
            $error = 'unknown beneficiary name';
            $t_code = $this->writeTransactionLog($cust_account, $amount, $ben_account, $ip, $message, false, $error);
            return array('result' => false,
                'message' => 'Transaction Aborted:<br/>' . $error . '<br/>Transaction Code: ' . $t_code);
        }
        
        //check whether there is suffucent funds to perform the transation
        $check_funds = $this->checkSufficientFunds($cust_id, $amount, $cust_account);
        
        if(!$check_funds){
           $error = 'insufficient funds to perform the transaction';
           $t_code = $this->writeTransactionLog($cust_account, $amount, $ben_account, $ip, $message, false, $error);
            return array('result' => false,
                'message' => 'Transaction Aborted:<br/>' . $error . '<br/>Transaction Code: ' . $t_code); 
        }
        
        $id_beneficiary = $array_result['id'];
        
        return array('result' => true, 'message' => 'ok');
    }

    private function checkAccountExists($account) {
        $query = "SELECT customer.customer_id AS id, name, surname FROM account, customer WHERE "
                . "account_number = ? AND customer.customer_id = account.customer_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $account);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $stmt->affected_rows;
        if ($rows != 1) {
            return array('result' => false);
        } else {
            $result_set = $result->fetch_array(MYSQLI_ASSOC);
            return array('result' => true, 'array' => $result_set);
        }
    }
    //write on the transaction table the successful or aborted transaction
    private function writeTransactionLog($cust_account, $amount, $dest_account, $ip, $message, $flag = false, $error = NULL) {
        $date = date('Y-m-d H:i:s');
        $characters_to_eliminate = array('-', ':', ' ');
        $characters_to_replace = array('', '', '');
        //primary key of the transaction table are a string composed by the date (stripped by non-number characters) and customer account number 
        $transaction_code = str_replace($characters_to_eliminate, $characters_to_replace, $date);
        $flag_mysql = $flag ? 'executed' : 'aborted';
        $query = "INSERT INTO transaction (transaction_code, operation_time_date, account, transaction_amount, flag, dest_account, ip_address, message, error) "
                . "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ssiisisss', $transaction_code, $date, $cust_account, $amount, $flag_mysql, $dest_account, $ip, $message, $error);
        $stmt->execute();
        return $transaction_code;
    }
    
    //check whether there is enough money on the customer account to perform the transaction
    private function checkSufficientFunds($id, $amount, $customer_account) {
        $query = "SELECT balance FROM account WHERE "
                . "account_number = ? AND customer_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $customer_account, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $stmt->affected_rows;
        if ($rows != 1) {
            return false;
        } else {
            $result_set = $result->fetch_array(MYSQLI_ASSOC);
            $cust_amount = $result_set['balance'];
            return $cust_amount >= $amount;
        }
    }

}
