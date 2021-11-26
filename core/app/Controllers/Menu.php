<?php
namespace App\Controllers;

class Menu extends BaseController {

    function get_menu() {
        try {
          $type = get_get("type");
          $id_user = get_get("id_user");
          if (is_empty($type) || !in_array($type, ["header", "aside"])) throw new \Exception("type value not found!");
          $html = "";
          if ($type === "header") $this->_init_header($html, $id_user);
          if ($type === "aside") $this->_init_aside($html, $id_user);
          $response = \Config\Services::response();
          $response->setStatusCode(200);
          $response->setBody($html);
          $response->send();
        } catch (\Exception $e) {
          \send_500_response(\format_exception($e));
        }
    }

    private function _init_header(&$html, $id_user, $kode_menu = "", $data = null) {
      $header = !isset($data) ? $this->tmenu->read(1, is_empty($kode_menu) ? 1 : strlen($kode_menu) + 1, $id_user, $kode_menu) : $data;
      if (is_empty($kode_menu)) {
        foreach ($header as $row) {
          $kode = $row->kode;
          $nama = $row->nama;
          $link = $row->link === "#" ? "#" : CLIENT_URL. $row->link;
          $html .= "<li class='submenu_title'><a href='$link' class='link_header'>$nama</a>";
          $this->_init_header($html, $id_user, $kode);
          $html .= "</li>";
        }
      } else {
        $length = strlen($kode_menu);
        if (!isset($data) && count($header) > 0) {
          $html .= "<ul class='submenu sub$length'>";
          $this->_init_header($html, $id_user, $kode_menu, $header);
          $html .= "</ul>";
        } else {
          foreach ($header as $row) {
            $kode = $row->kode;
            $nama = $row->nama;
            $link = $row->link === "#" ? "#" : CLIENT_URL. $row->link;
            $nav_icon = count($this->tmenu->read(1, strlen($kode) + 1, $id_user, $kode)) > 0 ? "<span class='nav_icon2' />" : "";
            $html .= "<li class='sub_list$length'><a href='$link'>$nama $nav_icon</a>";
            $this->_init_header($html, $id_user, $kode);
            $html .= "</li>";
          }
        }
      }
    }

    private function _init_aside(&$html, $id_user, $kode_menu = "", $data = null) {
      $aside = !isset($data) ? $this->tmenu->read(1, is_empty($kode_menu) ? 1 : strlen($kode_menu) + 1, $id_user, $kode_menu) : $data;
      if (is_empty($kode_menu)) {
        foreach ($aside as $row) {
          $kode = $row->kode;
          $nama = $row->nama;
          $link = $row->link === "#" ? "#" : CLIENT_URL. $row->link;
          $nav_icon = count($this->tmenu->read(1, strlen($kode) + 1, $id_user, $kode)) > 0 ? "<span class='nav_icon' />" : "";
          $html .= "<a href='$link' class='link_aside'>$nama $nav_icon</a>";
          $this->_init_aside($html, $id_user, $kode);
        }
      } else {
        $length = strlen($kode_menu);
        if (!isset($data) && count($aside) > 0) {
          $html .= "<ul class='submenu sub$length'>";
          $this->_init_aside($html, $id_user, $kode_menu, $aside);
          $html .= "</ul>";
        } else {
          foreach ($aside as $row) {
            $kode = $row->kode;
            $nama = $row->nama;
            $link = $row->link === "#" ? "#" : CLIENT_URL. $row->link;
            $nav_icon = count($this->tmenu->read(1, strlen($kode) + 1, $id_user, $kode)) > 0 ? "<span class='nav_icon' />" : "";
            $html .= "<li class='sub_list$length'><a href='$link' class='link_aside'>$nama $nav_icon</a>";
            $this->_init_aside($html, $id_user, $kode);
            $html .= "</li>";
          }
        }
      }
    }
    
}