<?php /*
 * Dynmic_menu.php
 */

class Dynamic_menu
{
// para CodeIgniter Super Global Referencias o variables globales
  private $ci;
  private $ul_class           = 'class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false"';
  private $li_parent          = 'class="nav-item has-treeview "';
  private $li_nonparent       = 'class="nav-item"';
  private $link_class         = 'class="nav-link"';
  // --------------------------------------------------------------------
  /**
   * PHP5        Constructor
   *
   */
  function __construct()
  {
    $this->ci = &get_instance();    // get a reference to CodeIgniter.
  }
  // --------------------------------------------------------------------
  /**
   * build_menu($table, $type)
   *
   * Description:
   *
   * builds the Dynaminc dropdown menu
   * $table allows for passing in a MySQL table name for different menu tables.
   * $type is for the type of menu to display ie; topmenu, mainmenu, sidebar menu
   * or a footer menu.
   *
   * @param    string    the MySQL database table name.
   * @param    string    the type of menu to display.
   * @return    string    $html_out using CodeIgniter achor tags.
   */

  function build_menu()
  {
    $menu = array();
    $role_id          = $this->ci->session->userdata['logged_in']['role_id'];
    $employee_id      = $this->ci->session->userdata['logged_in']['id'];
    $userrights       = $this->ci->db->query("select * from user_rights where role_id=$role_id");
    $userData         = $userrights->result();
    $userRightsIds2   = [];
    $userRightsIds2   = explode(',', $userData[0]->menu_ids);
    $userrights1      = $this->ci->db->query("select * from user_rights where employee_id=$employee_id");
    $userData1        = $userrights1->result();
    $userRightsIds1   = [];

    if (!empty($userData1)) {
      $userRightsIds1 = explode(',', $userData1[0]->menu_ids);
    }

    $userRightsIds = array_merge($userRightsIds2, $userRightsIds1);
    $query = $this->ci->db->query("select * from menus where show_menu='Y' ");
    $html_out = '';
    $html_out .= "\t\t" . '<ul ' . $this->ul_class . '>' . "\n";


    // me despliega del query los rows de la base de datos que deseo utilizar
    //print_r($query->result());exit;
    foreach ($query->result() as $row) {
      $id = $row->id;
      $title = $row->menu_name;
      $link_type = $row->link_type;
      $page_id = $row->page_id;
      $module_name = $row->controller;
      $action = $row->action;
      $url = $row->url . 'index.php/' . $module_name . '/' . $action;
      $dyn_group_id = $row->dyn_group_id;
      $position = $row->position;
      $icon_class = '"' . $row->icon_class . '"';
      $target = $row->target;
      $parent_id = $row->parent_id;
      $is_parent = $row->is_parent;
      $show_menu = $row->show_menu;

      if (in_array($id, $userRightsIds)) { {
          // are we allowed to see this menu?
          if ($show_menu == 'Y' && $parent_id == 0) {

            if ($is_parent == 'N') {
              $html_out .= '<li ' . $this->li_nonparent . '>
                                <a href=' . '"' . $url . '"' . $this->link_class . ' target=' . $target . '>
                                    <i class=' . $icon_class . '></i> &nbsp;' . $title . '
                                </a> ';
            } else {
              $html_out .= '<li ' . $this->li_parent . '>
                                <a ' . $this->link_class . ' target=' . $target . '>
                                    <i class=' . $icon_class . '></i> &nbsp;' . $title . '
                                </a> ';
            }
            $html_out .= $this->get_childs($id);
          }
        }
      }
    }
    // loop through and build all the child submenus.

    $html_out .= '</li>' . "\n";
    $html_out .= "\t\t" . '</ul>' . "\n";
    //$html_out .= "\t".'</nav>' . "\n";

    return $html_out;
  }
  /**
   * get_childs($menu, $parent_id) - SEE Above Method.
   *
   * Description:
   *
   * Builds all child submenus using a recurse method call.
   *
   * @param    mixed    $id
   * @param    string    $id usuario
   * @return    mixed    $html_out if has subcats else FALSE
   */
  function get_childs($id)
  {
    $has_subcats = FALSE;

    $html_out  = '';
    $html_out .= "\t\t\t\t\t" . '<ul class="nav">' . "\n";
    $role_id = $this->ci->session->userdata['logged_in']['role_id'];
    $employee_id = $this->ci->session->userdata['logged_in']['id'];
    $userrights = $this->ci->db->query("select * from user_rights where role_id=$role_id");
    $userData = $userrights->result();
    $userRightsIds2 = [];
    $userRightsIds2 = explode(',', $userData[0]->menu_ids);
    $userrights1 = $this->ci->db->query("select * from user_rights where employee_id=$employee_id");
    $userData1 = $userrights1->result();
    $userRightsIds1 = [];
    if (!empty($userData1)) {
      $userRightsIds1 = explode(',', $userData1[0]->menu_ids);
    }

    $userRightsIds = array_merge($userRightsIds2, $userRightsIds1);
    $query = $this->ci->db->query("select * from menus where parent_id = $id && show_menu='Y' ");
    foreach ($query->result() as $row) {
      $id = $row->id;
      $title = $row->menu_name;
      $link_type = $row->link_type;
      $page_id = $row->page_id;
      $module_name = $row->controller;
      $action = $row->action;
      $url = $row->url . 'index.php/' . $module_name . '/' . $action;
      $dyn_group_id = $row->dyn_group_id;
      $position = $row->position;
      $icon_class = '"' . $row->icon_class . '"';
      $target = $row->target;
      $parent_id = $row->parent_id;
      $is_parent = $row->is_parent;
      $show_menu = $row->show_menu;

      $has_subcats = TRUE;

      if (in_array($id, $userRightsIds)) {
        if ($is_parent == 'Y') {
          $html_out .= '<li ' . $this->li_parent . '>
                                            <a ' . $this->link_class . ' target=' . $target . '>
                                                <i class=' . $icon_class . '></i> &nbsp;&nbsp;' . $title . '
                                            </a> ';
        } else {
          $html_out .= '<li ' . $this->li_nonparent . '>
                                            <a href=' . '"' . $url . '"' . $this->link_class . ' target=' . $target. '>
                                                <i class=' . $icon_class . '></i> &nbsp; ' . $title . '
                                            </a> ';
        }
        // Recurse call to get more child submenuus.
        $html_out .= $this->get_childs($id);
      }
    }

    $html_out .= '</li>' . "\n";
    $html_out .= "\t\t\t\t\t" . '</ul>' . "\n";
    return ($has_subcats) ? $html_out : FALSE;
  }
}
