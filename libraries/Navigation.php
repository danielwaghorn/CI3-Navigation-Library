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
     * @var $_item_open_active:
     *      Contains the open tag for an active item e.g.
     *      <li class="active">
     * @var $_item_close:
     *      Contains the close tag for an item e.g. </li>
     * @var $_anchor:
     *      Contains the template for an anchor in an item
     *      e.g. <a href="{$url}" {$extra}>{$text}</a>
     *      where $url is the link to the item,
     *      $extra is any additional attributes e.g. class="main"
     *      and $text is the text to be held in the anchor.
     * @var $_dropdown_open:
     *      Contains the open tag for a dropdown e.g. <ul class="dropdown">
     * @var $_dropdown_close:
     *      Contains the close tag for a dropdown e.g. </ul>
     */

    /* Markup variables */
    private $_navigation_open;
    private $_navigation_close;
    private $_item_open;
    private $_item_open_active;
    private $_item_close;
    private $_anchor;
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
        $this->_item_open_active = $this->CI->config->item('item_open_active',$params['config']);
        $this->_item_close = $this->CI->config->item('item_close',$params['config']);
        $this->_anchor = $this->CI->config->item('anchor',$params['config']);
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
        $page_url = str_replace($this->_current_url,$this->_base_url,'');
        return strcmp($url,$page_url);
    }

    function bindAnchor($url, $text, $extra = '') {
        /**
         * Takes parameters for an anchor and binds them to template.
         * @param url : url to put in href
         * @param text : text to put between anchor
         * @param OPTIONAL extra : extra attributes and data
         */

        $vars = array(
            '{$url}'       => $url,
            '{$text}'        => $text,
            '{$extra}' => $extra
        );

        return strtr($this->_anchor, $vars);
    }

    function outputItem($item) {
        /**
         * Outputs the markup for a single item.
         * @param item : query row for a single nav item.
         * @returns output : string composed of HTML to be rendered
         */

        $output = '';

        if ($this->isCurrentPage($item->ItemLink)) {
            $output .= $this->_item_open_active;
        } else {
            $output .= $this->_item_open;
        }

        // Output link
        $output .= $this->bindAnchor($item->ItemLink, $item->ItemHumanName);

        // Check for sub items.
        $subItems = $this->CI->nav->getSubItems($item->ItemID);

        if (count($subItems->result_array()) > 0) {
            $this->renderDropdown($subItems);
        }

        $output .= $this->_item_close;

        return $output;
    }

    function renderDropdown($subItems) {
        /**
         * Takes subitems and returns markup.
         * @param subItems : Query result containing nav items
         * @returns output : string composed of HTML to be rendered
         */

        $output = $this->_dropdown_open;

        foreach ($subItems->result_array() as $item) {

            // Check if current page and open item
            if ($this->isCurrentPage($item->ItemLink)) {
                $output .= $this->_item_open_active;
            } else {
                $output .= $this->_item_open;
            }

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
         * @returns output : string composed of HTML to be rendered.
         */
        $menu_id = $this->CI->nav->getMenuID($menu_name);
        return $this->generateNav_fromID($menu_id);
    }

    public function generateNav_fromID($menu_id) {
        /**
         * Generates output for menu from a menu ID as specified in
         * CI-Nav-Menus.
         * @param menu_id : int ID of the menu to be generate
         * @returns output : string composed of HTML to be rendered.
         */

        // Open Container
        $this->_output = $this->_navigation_open;

        $top_level = $this->CI->nav->getTopLevelNav_byID($menu_id);

        // if ($top_level->num_rows() > 0)
       //  {
            foreach ($top_level->result() as $item)
            {
                // Output each nav item
                $this->_output .= $this->outputItem($item);
            }
        // }

        $this->_output .= $this->_navigation_close;

        return $this->_output;
    }




}