<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Navigation library for CI3-Navigation-Library
 * User: danielwaghorn
 * Date: 11/06/15
 * Time: 11:06
 */

class Navigation {
    /**
     * Class for rendering navigation.
     *
     * @var $_navigation_open:
     *      Contains the open tag e.g. <ul class="nav">
     * @var $_navigation_close:
     *      Contains the closing tag e.g. </ul>
     * @var $_item_open:
     *      Contains the open tag for a nav item e.g. <li>
     * @var $_item_open_active_class:
     *      Contains the class for an active item e.g. "active"
     * @var $_item_open_dropdown_class:
     *      Contains the class for an item which has subitems e.g. "dropdown"
     * @var $_item_close:
     *      Contains the close tag for an item e.g. </li>
     * @var $_anchor:
     *      Contains the template for an anchor in an item
     *      e.g. <a href="{$url}" {$extra}>{$text}</a>
     *      where $url is the link to the item,
     *      $extra is any additional attributes e.g. class="main"
     *      and $text is the text to be held in the anchor.
     * @var $_anchor_dropdown:
     *      Different template for dropdown parent links.
     * @var $_dropdown_open:
     *      Contains the open tag for a dropdown e.g. <ul class="dropdown">
     * @var $_dropdown_close:
     *      Contains the close tag for a dropdown e.g. </ul>
     */

    /* Markup variables */
    private $_navigation_open;
    private $_navigation_close;
    private $_item_open;
    private $_item_open_active_class;
    private $_item_open_dropdown_class;
    private $_item_close;
    private $_anchor;
    private $_anchor_dropdown;
    private $_dropdown_open;
    private $_dropdown_close;

    private $_current_url;
    private $_base_url;

    private $_output;

    protected $CI;

    public function __construct($params = array('config' => 'navigation'))
    {
        /**
         * Constructor for Navigation
         * @param $params : array containing arguments
         * In this instance argument can specify custom config to load,
         * otherwise the default config is used.
         * Usage: $params = array( 'config' => 'myconfig');
         * In $this->load->library('navigation',$params);
         * Where 'myconfig.php' exists in your application/config folder.
         */

        // Gets CI ref to use in this lib.
        $this->CI =& get_instance();
        $this->CI->load->helper('url');

        // Load configuration
        $this->CI->config->load($params['config'],TRUE);

        // Store configuration
        $this->_navigation_open = $this->CI->config->item('navigation_open',$params['config']);
        $this->_navigation_close = $this->CI->config->item('navigation_close',$params['config']);
        $this->_item_open = $this->CI->config->item('item_open',$params['config']);
        $this->_item_open_active_class = $this->CI->config->item('item_open_active_class',$params['config']);
        $this->_item_open_dropdown_class = $this->CI->config->item('item_open_dropdown_class',$params['config']);
        $this->_item_close = $this->CI->config->item('item_close',$params['config']);
        $this->_anchor = $this->CI->config->item('anchor',$params['config']);
        $this->_anchor_dropdown = $this->CI->config->item('anchor_dropdown',$params['config']);
        $this->_dropdown_open = $this->CI->config->item('dropdown_open',$params['config']);
        $this->_dropdown_close = $this->CI->config->item('dropdown_close',$params['config']);

        // Get current URL
        $this->_current_url = rtrim(current_url(),'/');

        // Get site URL
        $this->_base_url = base_url();

        // Load model
        $this->CI->load->model('nav_model','nav');
    }

    function isCurrentPage($url) {
        /**
         * Checks whether current page is = $url
         * @param url : relative url to site url e.g. 'login/forgot-password'
         * @return : boolean indicating result
         */

        // Remove site url
        $page_url = str_replace(rtrim($this->_base_url,"/"),"",$this->_current_url);
        if(empty($page_url)){
            $page_url = "/";
        }
        return strcmp("/" . $url,$page_url) == 0;
    }

    function bindAnchor($url, $text, $extra = '',$isDropdown = false) {
        /**
         * Takes parameters for an anchor and binds them to template.
         * @param url : url to put in href
         * @param text : text to put between anchor
         * @param OPTIONAL extra : extra attributes and data
         * @param OPTIONAL isDropdown : boolean indicating if dropdown or not,
         * changes url template.
         */

        $vars = array(
            '{$url}'       => $this->_base_url . $url,
            '{$text}'        => $text,
            '{$extra}' => $extra
        );

        if ($isDropdown) {
            return strtr($this->_anchor_dropdown,$vars);
        } else {
            return strtr($this->_anchor, $vars);
        }
    }

    function outputItem($item) {
        /**
         * Outputs the markup for a single item.
         * @param item : query row for a single nav item.
         * @return output : string composed of HTML to be rendered
         */

        $output = '';

        $classes = '';

        $output .= $this->_item_open;

        // Check for sub items.
        $subItems = $this->CI->nav->getSubItems($item->ItemID);

        if ($this->isCurrentPage($item->ItemLink)) {
            $classes .= $this->_item_open_active_class . ' ';
        }

        if (!is_null($subItems) && count($subItems->result()) > 0){
            // See if we have dropdown
            $classes .= $this->_item_open_dropdown_class . ' ';
        }

        if(!strcmp($classes,'') == 0) {
            // If classes to add them append to open tag
            $output = str_replace('>',' class="' . $classes . '">',$output);
        }

        // Output link
        if (!is_null($subItems) && count($subItems->result()) > 0) {
            $output .= $this->bindAnchor($item->ItemLink, $item->ItemHumanName, '', $this->_anchor_dropdown);
        } else {
            $output .= $this->bindAnchor($item->ItemLink, $item->ItemHumanName);
        }

        if (!is_null($subItems)){
            if (count($subItems->result()) > 0) {
                $output .= $this->renderDropdown($subItems);
            }
        }

        $output .= $this->_item_close;

        return $output;
    }

    function renderDropdown($subItems) {
        /**
         * Takes subitems and returns markup.
         * @param subItems : Query result containing nav items
         * @return output : string composed of HTML to be rendered
         */

        $output = $this->_dropdown_open;

        foreach ($subItems->result() as $item) {
            $subOutput = $this->_item_open;
            $classes = '';

            // Check if current page and open item
            if ($this->isCurrentPage($item->ItemLink)) {
                $classes .= $this->_item_open_active_class . ' ';
            }

            if (!is_null($subItems) && count($subItems->result()) > 0){
                // See if we have dropdown
                $classes .= $this->_item_open_dropdown_class . ' ';
            }

            if(!strcmp($classes,'') == 0) {
                // If classes to add them append to open tag
                $subOutput = str_replace('>',' class="' . $classes . '">',$subOutput);
            }

            $output .= $subOutput;

            // Output link
            $output .= $this->bindAnchor($item->ItemLink, $item->ItemHumanName);

            // Close item
            $output .= $this->_item_close;
        }

        $output .= $this->_dropdown_close;

        return $output;
    }

    public function generateNav_fromName($menu_name)
    {
        /**
         * Resolves a menu name to ID then returns the menu output.
         * @param menu_name : string identifier of the menu as in CI-Nav-Menus
         * @return output : string composed of HTML to be rendered.
         */
        $menu_id = $this->CI->nav->getMenuID($menu_name);
        return $this->generateNav_fromID($menu_id);
    }

    public function generateNav_fromID($menu_id) {
        /**
         * Generates output for menu from a menu ID as specified in
         * CI-Nav-Menus.
         * @param menu_id : int ID of the menu to be generate
         * @return output : string composed of HTML to be rendered.
         */

        // Open Container
        $this->_output = $this->_navigation_open;

        $top_level = $this->CI->nav->getTopLevelNav_byID($menu_id);

        if (count($top_level->result()) > 0)
        {
            foreach ($top_level->result() as $item)
            {
                // Output each nav item
                $this->_output .= $this->outputItem($item);
            }
        }

        $this->_output .= $this->_navigation_close;

        return $this->_output;
    }

    public function generateRoleBasedNav() {
        /**
         * Outputs navigation selectively based on user authentication
         * groups.
         * N.B. Required Ion Auth library.
         * http://benedmunds.com/ion_auth/
         *
         * @return HTML markup for navigation
         */

        if (!$this->CI->ion_auth->logged_in()){
            return $this->generateNav_fromName('public');
        } else {
            // User Group
            if ($this->CI->ion_auth->in_group('user')){
                return $this->generateNav_fromName('user');
            }

            // Admins
            if ($this->CI->ion_auth->is_admin()){
                return $this->generateNav_fromName('admin');
            }
        }
    }


}