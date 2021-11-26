<?php
namespace app\Models;

use CodeIgniter\Model;

class M_Utils extends Model {
    protected $db;

    function __construct() {
        $this->db = db_connect();
    }

    function test() {
        $this->db->query("SET @p1 = 'hhh'");
        $this->db->query("CALL save_test(@p1, @p2)");
        $dt = $this->db->query("SELECT @p1 AS p1, @p2 AS p2")->getResult();
        return $dt;
    }

    function get_insert_id() {
        return $this->db->insertID();
    }
    
    function commit() {
        $this->db->transCommit();
    }

    function escape_string($value) {
        return $this->db->escapeString($value);
    }

    function escape_like($value) {
        return $this->db->escapeLikeString($value);
    }

    function read() {
        switch (\func_get_arg(0)) {
            case "check_data":
                $table = \func_get_arg(1);
                $col_name = \func_get_arg(2);
                $col_val = \func_get_arg(3);
                return $this->db->table($table)->
                select($col_name)->
                where([$col_name => $col_val, "sta !=" => 0])->
                get()->getResult();
        }
    }

    function start() {
        $this->db->transBegin();
    }

    function rollback($table = "") {
        $this->db->transRollback();
        if (!is_array($table)) $this->db->query("ALTER TABLE $table AUTO_INCREMENT = 0");
        else {
            foreach ($table as $t) {
                $this->db->query("ALTER TABLE $t AUTO_INCREMENT = 0");
            }
        }
    }

}
