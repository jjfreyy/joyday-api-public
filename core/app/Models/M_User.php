<?php 
namespace app\Models;

use CodeIgniter\Model;

class M_User extends Model {
    protected $db;

    function __construct() {
        $this->db = db_connect();
    }

    function put($data_user) {
        return $this->query("CALL save_user(?, ?, ?, ?, ?, ?, ?, ?, ?)", $data_user);
    }

    function read() {
        switch (\func_get_arg(0)) {
            case 1: // authentication/login, sistem/edit_akun/save
                $username = $this->db->escapeString(\func_get_arg(1));
                return $this->db->query("
                    SELECT tu.id_user, tu.nama_user, tu.username, tu.password, tu.no_hp, tu.email, tu.id_level, tu1.nama_level
                    FROM tuser tu
                    JOIN tuser1 tu1 ON tu.id_level = tu1.id_level
                    WHERE (tu.username = '$username' OR REGEXP_REPLACE(REPLACE(tu.no_hp, '+62', '0'), '[\\\s\-]', '') = '" .$this->db->escapeString(trim_phone_number($username)). "' OR tu.email = '$username') AND tu.id_level IN(1, 2) AND tu.sta = 1
                ")->getResult();
            case 2: // authentication/check_session
                $id_user = \func_get_arg(1);
                return $this->db->table("tuser")->select("id_user")->where(["id_user" => $id_user, "sta" => 1])->get()->getResult();
            case 3: // libraries/c_user
                $type = func_get_arg(1);
                switch ($type) {
                    case "username":
                        return $this->db->table("tuser")->
                        select("id_user")->
                        where(["username" => func_get_arg(2), "id_user !=" =>func_get_arg(3)])->
                        get()->getResult();
                    case "no_hp":
                        $phone_number = \trim_phone_number(func_get_arg(2));
                        $phone_number = \is_empty($phone_number) ? $phone_number : $this->db->escapeString($phone_number);
                        $id_user = func_get_arg(3);
                        $id_user = \is_empty($id_user) ? $id_user : $this->db->escapeString($id_user);
                        return $this->db->query("
                            SELECT id_user FROM tuser
                            WHERE 
                                REGEXP_REPLACE(REPLACE(no_hp, '+62', '0'), '[\\\s\-]', '') = '$phone_number' AND
                                id_user != '$id_user'
                        ")->getResult();
                    case "email":
                        return $this->db->table("tuser")->
                        select("id_user")->
                        where(["email" => func_get_arg(2), "id_user !=" => func_get_arg(3)])->
                        get()->getResult();
                    case "id_level":
                        return $this->db->table("tuser1")->
                        select("id_level")->
                        where("id_level", func_get_arg(2))->
                        get()->getResult();
                }
            case 4: // sistem/edit_akun/fetch
                $id_user = \func_get_arg(1);
                return $this->db->table("tuser")->
                select("id_user, nama_user, username, no_hp, email, keterangan")->
                where(["id_user" => $id_user, "sta" => 1])->
                get()->getResult();
            break;
        }
    }

}