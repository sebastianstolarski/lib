<?php

/**
 * @author Sebastian Stolarski
 * @copyright 2015
 */

class DatabaseManager {
    
    static public function getConection() {
        
        
        try 
        {
            $conn = @new PDO('mysql:host='.DB_SERVER.';dbname='.DB_NAME, DB_USERNAME, DB_PASSWORD);
            $conn->exec('SET NAMES utf8');
            return $conn;
        } 
        catch (PDOException $e)
        {
            $error_message = "\n".date("d.m.y").", ".date("H:i:s").":\n Nastąpił błąd połączenia z bazą danych: ".$e->getMessage().", w lini: ".__LINE__.", w pliku: ".__FILE__."\n";
            error_log($error_message, 3, '_log/db_log.log');
            die(); exit();
        }
        
        
    }
    
    //wywołanie funkcji należy wykonać z dwoma parametrami, 
    //pierwszy z nich to zapytanie sql 
    //w formie pytajników w miejscu zmiennych,
    //drugi parametr to tablica ze zmiennymi wprowadzonymi 
    //w zapytaniu sql uporządkowanymi w kolejności występowania
    
    static public function selectBySql($sql, $att) {
        
        //clear SQL statment
        $conn = SELF::getConection();
        $sth = $conn->prepare($sql);
        $sth->execute($att);
        
        
        if (!$sth) {
            
            //obsługa błędów zapytania do bazy 
            $error_message = "\n".date("d.m.y").", ".date("H:i:s").":\n Nastąpił błąd w zapytaniu do bazy: ".$e->getMessage().", w lini: ".__LINE__.", w pliku: ".__FILE__."\n";
            error_log($error_message, 3, '_log/db_log.log');
            die(); exit();
            
        } else {
            
            $resultArray = $sth->fetchAll(PDO::FETCH_ASSOC);
            
        }
        //sprawdzenie czy wynik z bazy jest poprawny
        if (count($resultArray) > 0) {
            
            return $resultArray;
            
        } else {
            
            //obsługa błędów zapytania do bazy 
            $error_message = "\n".date("d.m.y").", ".date("H:i:s").":\n Zapytanie bazodanowe o treści: \"".$sql."\" o wartnościach: \"".implode(", ", $att)."\" zwróciło pusty wynik! W lini: ".__LINE__.", w pliku: ".__FILE__."\n";
            error_log($error_message, 3, '_log/db_log.log');
            die(); exit();
            
        }
        
        die(); exit();
        
    }
    
    //metoda wybierajaca z bazy dane
    static public function selectData($table, $colums = array("*"), $where = array(), $logic_oper = "=", $oper = "AND") {
        
        $conn = self::getConection();
        
        $sql = "SELECT ";
        
        if (count($colums) == 1) {
            $sql .= $colums[0];
        } else {
            foreach ($colums as $row_c) {
                $sql .= $row_c.",";
            }
        }
        
        $sql = rtrim($sql, ',');
        
        $sql .= " FROM {$table}";
        
        if (count($where) > 0) {
            $sql .= " WHERE ";
            
            foreach ($where as $key_w => $val_w) {
                $sql .= $key_w.$logic_oper."'".$val_w."' ".$oper." ";
            }
            
            $sql = substr($sql, 0, strlen($sql)-(strlen($oper)+2)); 
        }
        
        $sth = $conn->query($sql);
        
        if (!$sth) {
            
            //obsługa błędów zapytania do bazy 
            $error_message = "\n".date("d.m.y").", ".date("H:i:s").":\n Nastąpił błąd w zapytaniu do bazy: ".$e->getMessage().", w lini: ".__LINE__.", w pliku: ".__FILE__."\n";
            error_log($error_message, 3, '_log/db_log.log');
            die(); exit();
            
        } else {
            
            $resultArray = $sth->fetchAll(PDO::FETCH_ASSOC);
            
        }
        //sprawdzenie czy wynik z bazy jest poprawny
        if (count($resultArray) > 0) {
            
            return $resultArray;
            
        } else {
            
            //obsługa błędów zapytania do bazy 
            $error_message = "\n".date("d.m.y").", ".date("H:i:s").":\n Zapytanie bazodanowe o treści: \"".$sql."\" zwróciło pusty wynik! W lini: ".__LINE__.", w pliku: ".__FILE__."\n";
            error_log($error_message, 3, '_log/db_log.log');
            die(); exit();
            
        }
        
        die(); exit();
        
    }
    
    //metoda aktualizująca rekordy w bazie
    static public function updateTable($table, $set, $where = array(), $oper = "AND") {
        
        $conn = self::getConection();
        
        $sql = "UPDATE {$table} SET ";
        
        foreach ($set as $key => $val) {
            $sql .= $key."='".$val."',";
        }
        
        $sql = rtrim($sql, ',');
        
        if (count($where) > 0) {
            
            $sql .= " WHERE ";
            
            foreach ($where as $key => $val) {
                $sql .= $key."='".$val."' ".$oper." ";
            }
            
            $sql = substr($sql, 0, strlen($sql)-(strlen($oper)+2)); 
        }
        
        $result = $conn->query($sql);

        //sprawdzenie czy wynik z bazy jest poprawny
        if ($result) {
            
            return true;
            
        } else {
            
            //obsługa błędów zapytania do bazy 
            $error_message = "\nDnia: ".date("d.m.y").", o godzinie: ".date("H:i:s").":\n Zapytanie bazodanowe o treści: \"".$sql."\" zwróciło pusty wynik! W lini: ".__LINE__.", w pliku: ".__FILE__."\n";
            error_log($error_message, 3, '_log/db_log.log');
            return false;
            
        }
        
        die(); exit();
        
    }
    
    //metoda usuwająca z bazy rekord
    static public function deleteFrom($table, $where = array(), $oper = "AND") {
        
        $conn = self::getConection();
        
        $sql = "DELETE FROM {$table}";
        
        if (count($where) > 0) {
            
            $sql .= " WHERE ";
            
            foreach ($where as $key => $val) {
                
                $sql .= $key."='".$val."'  ".$oper;
                
            }
            
            $sql = substr($sql, 0, strlen($sql)-(strlen($oper)+2));
            
        }
        
        $result = $conn->query($sql);
        
        if (!($result)) {
            
            $error_message = "\nDnia: ".date("d.m.y").", o godzinie: ".date("H:i:s").":\n Zapytanie bazodanowe o treści: \"".$sql."\" zwróciło pusty wynik! W lini: ".__LINE__.", w pliku: ".__FILE__."\n";
            error_log($error_message, 3, '_log/db_log.log');
            return false;
            
        } else {
            
            return true;
            
        }
        
        die(); exit();
        
    }
    
    //metoda dodająca do bazy rekord
    static public function insertInto($table, $data) {
        
        $conn = self::getConection();
        
        $sql = "INSERT INTO {$table} ";
        $sql .= " (";
        
        foreach ($data as $key => $val) {
            
            $sql .= $key.",";
            
        }
        
        $sql = rtrim($sql, ',');
        
        $sql .= ") ";
        $sql .= "VALUES";
        $sql .= " (";
        
        foreach ($data as $val) {
            
            $sql .= "'".$val."',";
            
        }
        $sql = rtrim($sql, ',');
        
        $sql .= ")";
        
        $result = $conn->query($sql);
        
        if (!($result)) {
            
            $error_message = "\nDnia: ".date("d.m.y").", o godzinie: ".date("H:i:s").":\n Zapytanie bazodanowe o treści: \"".$sql."\" zwróciło pusty wynik! W lini: ".__LINE__.", w pliku: ".__FILE__."\n";
            error_log($error_message, 3, '_log/db_log.log');
            return false;
            
        } else {
            
            return true;
            
        }
        
        die(); exit();
    }
    
}


?>