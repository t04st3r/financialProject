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
            $this->conn = mysqli_connect($this->host, $this->user, $this->password, $this->schema);
            if (mysqli_connect_errno()) {
                die("Error while connecting to the database: ".mysqli_connect_error());
            }
        }
    }

    function __destruct() {
        if (isset($this->conn) || $this->conn != null) {
            mysqli_close($this->conn);
        }
    }

    private function error_message(){
        return "Database Error: " . mysqli_error($this->conn);
        
    }
    
    public function getAllUsersName() {
        $query = 'SELECT * FROM customer';
        $this->result = mysqli_query($this->conn, $query);
        if(!$this->result){
            die($this->error_message());
        }
        $result_array = mysqli_fetch_assoc($this->result);
        mysqli_free_result($this->result);
        return $result_array;  
    }
    
    public function checkLogin($user_name){
        
    }

}
