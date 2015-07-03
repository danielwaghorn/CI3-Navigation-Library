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
         * @return int menu ID otherwise -1;
         */
        if (isset($menu_name) && ctype_alnum ($menu_name)) {
            $query = $this->db->query('SELECT `MenuID` FROM `CI-Nav-Menus` WHERE `MenuName` = ?',array($menu_name));
            $row = $query->row();
            return $row->MenuID;
        }

        return NULL;
    }

    public function getTopLevelNav_byName($menu_name) {
        /**
         * Returns the top level of a menu
         * @param $menu_name : String handle of menu e.g. 'public'
         *  as defined in CI-Nav-Menus
         * @return : Query with result, if invalid NULL
         */

        // Check $nav_name is not null
        if (isset($menu_name) && ctype_alnum($menu_name)) {
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
         * @return : Query with result
         */

        if (isset($menu_ID) && $menu_ID != -1 && ctype_digit($menu_ID)) {

            $query = $this->db->query('SELECT `ItemName`, `ItemHumanName`, `ItemLink`, C.`ItemID`
                                    FROM `CI-Nav-Items` I
                                    INNER JOIN `CI-Nav-InMenu` C
                                    ON C.`ItemID` = I.`ItemID`
                                    WHERE C.`MenuID` = ? ORDER BY `LinkWeight` ASC',array($menu_ID));

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
         * @return : Query with result of MenuItems
         */

        if (isset($item_ID) && ctype_digit($item_ID)) {

            $query = $this->db->query('SELECT `ItemName`, `ItemHumanName`, `ItemLink`
                                        FROM `CI-Nav-Items`
                                        WHERE `ParentItem` = ? ORDER BY `ItemName` ASC',array($item_ID));
            return $query;

        }
        return NULL;
    }

}