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

    //perform login checking username password and matrix values
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

    //get the customer name and surname given his id
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

    //check if the login submitted matrix values are correct 
    private function checkMatrix($matrix_array, $id) {
        $query = "SELECT customer_id FROM matrix WHERE customer_id = $id";
        foreach ($matrix_array as $key => $value) {
            $query .= " AND " . $key . " = '" . $value . "'";
        }
        $this->conn->query($query);
        $row = $this->conn->affected_rows;
        return $row == 1;
    }

    //get the cards numbers belonging to the given customer id
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
    
    public function getAccountBalance($account_number){
        if ($account_number == '') {
            return '';
        }
        $query = 'SELECT balance FROM account WHERE account_number = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $account_number);
        $stmt->execute();
        $result = $stmt->get_result();
        $result_set = $result->fetch_array(MYSQLI_ASSOC);
        return $result_set['balance'];
    }

    //get the accounts numbers belonging to the given customer id
    public function getAccountsNumber($id) {
        $query = "SELECT account_number FROM account WHERE customer_id = ? ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $result_set = $result->fetch_all(MYSQLI_ASSOC);
        return $result_set;
    }

    //execute a transaction
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

        if (strcasecmp($array_result['name'], $ben_name) != 0 || strcasecmp($array_result['surname'], $ben_surname) != 0) {
            $error = 'unknown beneficiary name';
            $t_code = $this->writeTransactionLog($cust_account, $amount, $ben_account, $ip, $message, false, $error);
            return array('result' => false,
                'message' => 'Transaction Aborted:<br/>' . $error . '<br/>Transaction Code: ' . $t_code);
        }

        //check whether there is suffucent funds to perform the transation
        $check_funds = $this->checkSufficientFunds($cust_id, $amount, $cust_account);

        if (!$check_funds) {
            $error = 'insufficient funds to perform the transaction';
            $t_code = $this->writeTransactionLog($cust_account, $amount, $ben_account, $ip, $message, false, $error);
            return array('result' => false,
                'message' => 'Transaction Aborted:<br/>' . $error . '<br/>Transaction Code: ' . $t_code);
        }

        //transaction can proceed
        $result = $this->transferMoney($cust_account, $ben_account, $amount, $ip, $message);

        return $result;
    }

    //check whether an account is present on the database
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
            $cust_balance = $result_set['balance'];
            return $cust_balance >= $amount;
        }
    }

    private function transferMoney($cust_account, $ben_account, $amount, $ip, $message) {
        try {
            //begin transaction
            $this->conn->begin_transaction();
            //lock tables to avoid cuncurrency issues
            $this->conn->query('LOCK TABLES account WRITE');

            //get the balance from customer account
            $query = "SELECT balance FROM account WHERE "
                    . "account_number = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('i', $cust_account);
            $stmt->execute();
            $result = $stmt->get_result();
            $result_set = $result->fetch_array(MYSQLI_ASSOC);
            $cust_balance = $result_set['balance'];

            //get the balance from beneficiary account
            $query = "SELECT balance FROM account WHERE "
                    . "account_number = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('i', $ben_account);
            $stmt->execute();
            $result = $stmt->get_result();
            $result_set = $result->fetch_array(MYSQLI_ASSOC);
            $ben_balance = $result_set['balance'];

            //calcolate new balances bcadd bcsub
            $new_customer_balance = $cust_balance - $amount;
            $new_beneficiary_balance = $ben_balance + $amount;

            //update cusotmer account with new balance
            $query = 'UPDATE account SET balance = ? WHERE account_number = ?';
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('di', $new_customer_balance, $cust_account);
            $stmt->execute();

            //update beneficiary account with new balance
            $query = 'UPDATE account SET balance = ? WHERE account_number = ?';
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('di', $new_beneficiary_balance, $ben_account);
            $stmt->execute();

            //unlock tables
            $this->conn->query('UNLOCK TABLES');

            //write on transaction table transaction successfully completed
            $code = $this->writeTransactionLog($cust_account, $amount, $ben_account, $ip, $message, true, NULL);

            //commit transaction
            $this->conn->commit();

            return array('result' => true, 'message' => 'Transaction successfully executed</br></br>Transaction Code: ' . $code);
        } catch (Exception $e) {
            $error = 'Transaction Rolled Back: ' . $e->getMessage();
            $this->conn->rollback();
            $code = $this->writeTransactionLog($cust_account, $amount, $ben_account, $ip, $message, false, $error);
            return array('result' => false, 'message' => $error . '<br/>Transaction Code: ' . $code);
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

    //search on transaction table for the executed transaction that satisfy filtering criteria
    public function search($account, $startDate, $stopDate, $minAmount, $maxAmount) {

        //checking if account number is provided
        if (strcmp($account, '') == 0) {
            return array('result' => false, 'error' => 'Select an account number to display statements');
        }

        //checking if valid start and end dates are provided
        if (strcmp($startDate, '') != 0 && strcmp($stopDate, '') != 0) {
            if (strcmp($startDate, $stopDate) > 0) {
                return array('result' => false, 'error' => 'Start date must be lower than end date');
            }
        }
        //checking if valid max and min values are provided
        if (strcmp($minAmount, '') != 0 && strcmp($maxAmount, '') != 0) {
            if ($minAmount >= $maxAmount) {
                return array('result' => false, 'error' => 'Min amount must be lower than max amount');
            }
        }
        
        $query = "SELECT transaction_code AS code, operation_time_date AS time_date, transaction_amount AS amount,"
            ."account, dest_account, message FROM transaction WHERE flag = 'executed'" 
            ." AND (account = $account OR dest_account = $account) ";
        $query .=  strcmp($startDate, '') != 0 ? "AND operation_time_date >= '$startDate 23:59:59' " : '';
        $query .=  strcmp($stopDate, '') != 0 ? "AND operation_time_date <= '$stopDate 23:59:59' " : '';
        $query .=  strcmp($minAmount, '') != 0 ? "AND transaction_amount >= $minAmount " : '';
        $query .=  strcmp($maxAmount, '') != 0 ? "AND transaction_amount <= $maxAmount " : '';
        $result = $this->conn->query($query);
        $result_set = $result->fetch_all(MYSQLI_ASSOC);
        return array('result' => true, 'data' => $result_set);           
    }
    
    //get all the meaningful information about bank branches
    public function getBranches(){
        $query = "SELECT latitude, longitude, address, city, phone, open_time FROM branch";
        $result = $this->conn->query($query);
        $result_set = $result->fetch_all(MYSQLI_ASSOC);
        return $result_set; 
    }

}
