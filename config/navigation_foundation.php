<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * $config file for CI3-Navigation-Library and Zurb Foundation.
 * User: danielwaghorn
 * Date: 11/06/15
 * Time: 11:23
 *
 * @var navigation_open:
 *      Contains the open tag e.g. <ul class="nav">
 * @var navigation_close:
 *      Contains the closing tag e.g. </ul>
 * @var item_open:
 *      Contains the open tag for a nav item e.g. <li>
 * @var item_open_active_class:
 *      Contains class to be added to an open tag which is active link.
 *      E.G. 'active'
 * @var item_open_dropdown_class:
 *      Contains class to be added to a dropdown parent link open tag.
 *      E.G. 'dropdown'
 * @var item_close:
 *      Contains the close tag for an item e.g. </li>
 * @var anchor:
 *      Contains the template for an anchor in an item
 *      e.g. <a href="{$url}" {$extra}>{$text}</a>
 *      where $url is the link to the item,
 *      $extra is any additional attributes e.g. class="main"
 *      and $text is the text to be held in the anchor.
 * @var dropdown_open:
 *      Contains the open tag for a dropdown e.g. <ul class="dropdown">
 * @var dropdown_close:
 *      Contains the close tag for a dropdown e.g. </ul>
 */


$config['navigation_open'] = '<ul class="right">';

$config['navigation_close'] = '</ul>';

$config['item_open'] = '<li>';

$config['item_open_active_class'] = 'active';

$config['item_open_dropdown_class'] = '';

$config['item_close'] = '</li>';

$config['anchor'] = '<a href="{$url}" {$extra}>{$text}</a>';

$config['anchor'] = '<a href="{$url}" {$extra}>{$text}</a>';

$config['dropdown_open'] = '<ul class="has-dropdown">';

$config['dropdown_close'] = '</ul>';