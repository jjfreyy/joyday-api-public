<?php
function is_empty($str) {
    return !isset($str) || (empty($str) && $str != "0");
}

function is_empty_array($arr) {
    return empty($arr) || !is_array($arr);
}

function is_valid_alphanumeric($str, $object, $letter_prefix = FALSE, $length = 50, $can_empty = FALSE) {
    if ($can_empty && is_empty($str)) {
        return array(TRUE);
    }

    if (!preg_match("/^" .($letter_prefix ? "[a-z]" : ""). "([a-z1-9]+)?$/i", $str) || strlen($str) > $length || strlen($str) === 0) {
        return array(FALSE, "$object " .($letter_prefix ? "harus diawali dengan huruf & " : ""). "harus berupa alphanumeric & 
        tidak boleh lebih dari $length karakter. <br>");
    } else {
        return array(TRUE);
    }
}

function is_valid_code($code, $object, $can_empty = false) {
    if ($can_empty && is_empty(($code))) return [true];

    $code0 = explode("-", $code);
    if (count($code0) !== 2) return [false, "$object harus mengikuti format {1~3 huruf}-{angka}.<br>"];
    $code1 = $code0[0];
    $code2 = $code0[1];

    $is_valid_number = is_valid_number($code2, $object, 1, false);
    if (!$is_valid_number[0]) return [false, "$object harus mengikuti format {1~3 huruf}-{angka}.<br>"];

    if (strlen($code) > 30) return [false, "$object lebih dari 30 karakter.<br>"];
    
    return [true];
}

function is_valid_date($date, $object, $can_empty = FALSE) {
    if ($can_empty && is_empty($date)) {
        return array(TRUE);
    }

    $date_arr = explode("-", $date);
    if (count($date_arr) != 3) {
        $is_valid_date = FALSE;
    } else {
        if (!is_numeric($date_arr[0]) || !is_numeric($date_arr[1]) || !is_numeric($date_arr[2])) {
            $is_valid_date = FALSE;
        } else if (checkdate($date_arr[1], $date_arr[2], $date_arr[0])) {
            $is_valid_date = TRUE;
        } else {
            $is_valid_date = FALSE;
        }
    }

    if (!$is_valid_date) {
        return array(FALSE, "$object tidak valid. <br>"); 
    } else {
        return array(TRUE);
    }
}

function is_valid_email($email, $object, $can_empty = FALSE) {
    if ($can_empty && is_empty($email)) {
        return array(TRUE);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 50) {
        return array(FALSE, "$object tidak valid / panjang e-mail lebih dari 50 karakter. <br>");
    } else {
        return array(TRUE);
    }
}

function is_valid_mandarin_name($str, $object, $length = 50, $can_empty = false) {
    if ($can_empty && is_empty($str)) {
        return [true];
    }

    $is_valid_name = is_valid_name($str, $object);
    if ((!$is_valid_name[0] && !preg_match("/^[\p{L}\s\.\/\(\)]+$/u", $str)) || strlen($str) > $length || strlen($str) === 0) {
        return [false, "$object tidak valid / atau lebih dari $length karakter.<br>"];
    } else {
        return [true];
    }
}

function is_valid_name($str, $object, $length = 50, $can_empty = false) {
    if ($can_empty && is_empty($str)) {
        return [true];
    }

    if (!preg_match("/^[a-z\s\.]+$/i", $str) || strlen($str) > $length || strlen($str) === 0) {
        return [FALSE, "$object $str harus berupa huruf & tidak boleh lebih dari $length karakter. <br>"];
    } else {
        return [true];
    }
}

/** 
 * restrict 1: tidak boleh kurang dari 0
 * restrict 2: harus lebih dari 0 
 * */
function is_valid_number($number, $object, $restrict = 0, $allow_decimal = TRUE, $can_empty = FALSE, $length = 0) {
    if ($can_empty && is_empty($number)) {
        return [TRUE];
    }

    if ($length > 0 && strlen($number) > $length) {
        return [false, "Panjang " .strtolower($object). " tidak boleh lebih dari $length karakter."];
    }
    
    if (!is_numeric($number)) {
        return [FALSE, "$object harus berupa angka. <br>"];
    }

    if (!$allow_decimal && preg_match("/[\.]/", $number)) {
        return [FALSE, "$object tidak boleh memiliki angka desimal. <br>"];
    } 
    
    if ($restrict === 1 && $number < 0) {
        return [FALSE, "$object tidak boleh kurang dari 0. <br>"];
    } else if ($restrict === 2 && !($number > 0)) {
        return [FALSE, "$object harus lebih dari 0. <br>"];
    }
    
    return [true];
}

function is_valid_periode($periode, $can_empty = false) {
    if ($can_empty && is_empty($periode)) {
        return [true];
    }

    $periode = explode(" ", $periode);
    if (count($periode) !== 2) return [false, "Format periode tidak valid. <br>"];
    
    $is_valid_number = is_valid_number($periode[0], "Periode", 2, false);
    if (!$is_valid_number[0]) return [false, $is_valid_number[1]];
    else if ($periode[0] > 999) return [false, "Periode lebih dari 999. <br>"];

    $is_valid_kode_periode = $periode[1] == "H" || $periode[1] == "B" || $periode[1] == "T";
    if (!$is_valid_kode_periode) return [false, "Kode periode tidak valid. <br>"];
    
    return [true];
}

function is_valid_phone($phone, $object, $can_empty = false) {
    if ($can_empty && is_empty($phone)) {
        return [true];
    }

    if (!preg_match("/^(\+62|0)([\s\-])?[\d]{2,3}([\s\-])?[\d]{3,4}([\s\-])?[\d]{3,4}([\s\-])?(([\s\-])?[\d]{3,4})?(([\s\-])?[\d]{1,})?$/", $phone) ||
        strlen($phone) > 20) {
        return [false, "$object tidak valid / panjang nomor lebih dari 20 karakter. <br>"];
    } else {
        return [true];
    }
}

function is_valid_str($str, $object, $length = 50, $can_empty = FALSE) {
    if ($can_empty && is_empty($str)) {
        return array(TRUE);
    }

    if (preg_match("/^[\#\;\~\`\/]+$/", $str)) {
        return [false, "$object tidak boleh mengandung karakter '# ; ~ ` /'.<br>"];
    }

    if (strlen($str) === 0) {
        return [false, "$object wajib diisi.<br>"];
    }

    if (strlen($str) > $length) {
        return array(FALSE, "$object lebih dari $length karakter. <br>");
    } else {
        return array(TRUE);
    }
}

function is_valid_username($str, $object, $can_empty = false) {
    if ($can_empty && is_empty($str)) {
        return [true];
    }

    if (!preg_match("/^[a-z][\w]+$/i", $str) || strlen($str) < 3 || strlen($str) > 50) {
        return [FALSE, "$object harus berupa huruf atau angka dan harus diawali dengan huruf. Minimal 3 karakter. Maksimal 50 karakter. <br>"];
    } else {
        return [true];
    }
}
