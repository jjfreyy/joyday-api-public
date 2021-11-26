<?php 

function check_assets_file($file = "") {
    return file_exists("d://web/wamp64/www/src/$file") || file_exists("e://web/wamp64/www/src/$file");
}

function check_file($file) {
    return !empty(glob($file));
}

function convert_currency_tonumber($number) {
    $number = str_replace(".", "", $number);
    return str_replace(",", ".", $number);
}

function convert_number_tocurrency($number) {
    if (is_empty($number) || $number == "-") return "-";
    $number_arr = explode(".", $number);
    if (!isset($number_arr[1]) || (isset($number_arr[1]) && $number_arr[1] == 0)) $decimal = 0;
    else $decimal = 2;

    return number_format($number, $decimal, ",", ".");
}

function debug($text = "test") {
    echo "<script>console.log('$text')</script>";
}

function debug_exception($e) {
    debug(env("CI_ENVIRONMENT") === "production" ? "Terjadi kesalahan internal server." : format_exception($e, true));
}

function format_exception($e, $addslashes = false) {
    return get_class($e). " at line " .$e->getLine(). ": " .(!$addslashes ? $e->getMessage() : \addslashes($e->getMessage()));
}

function format_date($date, $separator = "-", $include_time = FALSE) {
    return strftime("%d$separator%m$separator%Y" .($include_time ? " %T" : ""), strtotime($date));
}

function generate_code($str) {
    $str = explode(" ", strtoupper($str));
    if (count($str) > 1) {
        return $str[0][0].$str[1][0].(isset($str[2]) ? $str[2][0] : "");
    } else if (is_empty($str[0])) {
        return NULL;
    } else {
        return substr($str[0], 0, 3);
    }
}

function get_abbreviation($str) {
    $str1 = explode(" ", $str);
    if (count($str1) > 1) {
        return strtoupper(substr($str1[0], 0, 1).substr($str1[1], 0, 1).(isset($str1[2]) ? substr($str1[2], 0, 1) : ""));
    } else {
        return strtoupper(substr($str, 0, 3));
    }
}

function get_company_info() {
    return [
        "logo" => "logo.png", 
        "company" => "PT. Giokindo Indah Lestari", 
        "address" => "Jl. MP Mangkunegara Komp. Prabu Indah Blok B No. 16-18 Palembang 30114",
        "phone" => "(0811) 721701",
        "longitude" => "104.7689883",
        "latitude" => "-2.9487067",
    ];
}

function get_period($tgl1, $tgl2) {
    $tgl1_arr = explode("-", $tgl1);
    $tgl2_arr = explode("-", $tgl2);

    if ($tgl1_arr[0] == $tgl2_arr[0] && $tgl1_arr[1] == $tgl2_arr[1]) {
        $periode = get_month_name($tgl1_arr[1]). " " .$tgl1_arr[0];
    } else if ($tgl1_arr[0] == $tgl2_arr[0] && substr("0$tgl1_arr[1]", -2) == "01" && substr("0$tgl2_arr[1]", -2) == "12" && 
        $tgl1_arr[2] == "01" && $tgl2_arr[2] == "31") {
        $periode = $tgl1_arr[0];
    } else {
        $periode = $tgl1_arr[2]. " " .get_month_name($tgl1_arr[1]). " " .$tgl1_arr[0]. " Sampai Dengan " .$tgl2_arr[2]. " " .
        get_month_name($tgl2_arr[1]). " " .$tgl2_arr[0];
    }

    return "Periode $periode";
}

function get_days_in_month($tahun, $bulan) {
    return cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
}

function get_month_name($kode) {
    switch ($kode) {
        case "1": case "01": return "Januari";
        case "2": case "02": return "Februari";
        case "3": case "03": return "Maret";
        case "4": case "04": return "April";
        case "5": case "05": return "Mei";
        case "6": case "06": return "Juni";
        case "7": case "07": return "Juli";
        case "8": case "08": return "Agustus";
        case "9": case "09": return "September";
        case "10": return "Oktober";
        case "11": return "November";
        case "12": return "Desember";
    }
}

function get_error_page() {
    return "unknown";
}

function get_form_report($type, $contents) {
    if (is_array($contents)) {
        $contents = implode("", $contents);
    } else if ($type === "success") {
        $contents = "Data $contents berhasil ditambahkan / diubah. <br>";
    } else if ($type === "error") {
        $contents = "Gagal menambahkan data $contents. Silakan coba kembali. <br>";
    }

    return "<p id='report' class='$type'>$contents</p>";
}

function get_get($name, $sanitize = true) {
    $request = \Config\Services::request();
    $value = $request->getGet($name);
    if ($sanitize) $value = sanitize($value);
    return $value;
}

function get_json_response($type, $status, $message) {
    $json["status"] = $status;
    switch ($type) {
        case "delete":
            switch ($status) {
                case "error":
                    $json["message"] = "Gagal menghapus $message. Silakan coba kembali.";
                    break;
                case "success":
                    $json["message"] = "Berhasil menghapus $message.";
            }
            break;
        case "custom":
            $json["message"] = $message;
            break;
    }

    return json_encode($json);
}

function get_post($name, $sanitize = true) {
    $request = \Config\Services::request();
    $value = $request->getPost($name);
    if ($sanitize) $value = sanitize($value);
    return $value;
}

function if_empty_then($value, $assign = "-", $allow_zero = true) {
    if (is_empty($value)) return $assign;
    if (!$allow_zero && ($value === 0 || $value === "0")) return $assign;
    return $value;
}

function prepare_flashdata($data) {
    $session = \Config\Services::session();
    foreach ($data as $k => $v) {
        $session->setFlashdata($k, $v);
    }
}

function sanitize($data) {
    return strip_tags(addslashes(trim($data)));
}

function trim_phone_number($phone_number) {
    return preg_replace("/[\s\-]/", "", str_replace("+62", "0", $phone_number));
}
