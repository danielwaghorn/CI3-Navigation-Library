<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Model used by navigation library.
 * This facilitates reading navigation information stored in the
 * database. It does not perform any edits or additions.
 * User: danielwaghorn
 * Date: 11/06/15
 * Time: 09:05
 */

class Nav_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->load->database();
    }

    public function getMenuID($menu_name) {
        /**
         * Returns the id from associated menu handle
         * @param $menu_name : string name associated to menu defined in CI-Nav-Menus
         * @returns int menu ID otherwise -1;
         */

        $query = $this->db->query('SELECT `MenuName` FROM `CI-Nav-Menus` WHERE `MenuID` = ' . $menu_name);
        $row = $query->row();
        return $row->MenuName;
    }

    public function getTopLevelNav_byName($menu_name) {
        /**
         * Returns the top level of a menu
         * @param $menu_name : String handle of menu e.g. 'public'
         *  as defined in CI-Nav-Menus
         * @returns : Query with result, if invalid NULL
         */

        // Check $nav_name is not null
        if (isset($menu_name) && (strcmp(preg_replace("/[^a-zA-Z0-9]+/", "", $menu_name),$menu_name))) {
            // Get ID
            $menu_ID = $this->getMenuID($menu_name);
            // Return Menu
            return $this->getTopLevelNav_byID((int) $menu_ID);
        }

        return NULL;
    }

    public function getTopLevelNav_byID($menu_ID) {
        /**
         * Returns the top level of a menu
         * @param $menu_ID : int ID of menu as defined in CI-Nav-Menus
         * @returns : Query with result
         */

        if (isset($menu_ID) && $menu_ID != -1 && is_int($menu_ID)) {

            $query = $this->db->query('SELECT `ItemName`, `ItemHumanName`, `ItemLink`
                                    FROM `CI-Nav-Items` AS I
                                    INNER JOIN `CI-Nav-InMenu` AS C
                                    ON C.`ItemID` = I.`ItemID`
                                    WHERE C.`MenuID` = ' . $menu_ID);

            return $query;

        }
        return NULL;
    }

    public function getSubItems($item_ID) {
        /**
         * Returns sub items under a menu item where ParentItem
         * refs ItemID in CI-Nav-Items
         * @param $item_ID : int ID of menu item as defined in
         * CI-Nav-Items
         * @returns : Query with result of MenuItems
         */

        if (isset($item_ID) && is_int($item_ID)) {

            $query = $this->db->query('SELECT `ItemName`, `ItemHumanName`, `ItemLink`
                                        FROM `CI-Nav-Items`
                                        WHERE `ParentItem` = ' . $item_ID);
            return $query;

        }
        return NULL;
    }

}