<?php
/**
 * Metadata Browser Plugin
 *
 *
 * Developed by digitalMETRO Project [http://nycdigital.org/]
 *
 * @todo
 *  1. Add a description field to class to enable user-supplied definition rather than depend
 *     on element definition which can be very technical.
 *  2. Routes for individual element values a la wordpress
 *  3. Reliable value counting method for each value assigned to a given category.
 *     Advanced search and database query for string production different values.
 *  4. Automatic way to activate browsing by value for active categories in "show" and
 *     "browse" pages. This now needs to be done manually. See "howtolink.txt" for
 *     more information on how to currently do this.
 *
 * @copyright New York Metropolitan Library Council, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package MetadataBrowser
 * @author Kevin Reiss kevin.reiss@gmail.com
 */

// Require the record model for the metadata_browser_category table.
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'MetadataBrowser_Functions.php';

 /**
 * The Metadata Browser plugin.
 *
 * Refactors the Omeka Elements table to produce browsable views of element content
 * in both the Admin and Public Interface. The elements have been renamed "categories"
 * in the interest of using terms that will be familiar to general users.
 *
 * The MetadataBrowser_Category class enables the user to denote categories as "public"
 * as well as change the value of the "slug" and descriptive "label" that is displayed for
 * context in the interface when the category is displayed. Each "category" is traced back to
 * original element declaration by the "element_id" value assigned to each category. This
 * value is taken directly from the "Elements" table by the "metadata_browser_generate_element_select"
 * utility method in plugin.php for the plugin.
 *
 *  See file *readme.txt* on how to activate browsing in theme "show" and "browse" views.
 *
 * @package Omeka\Plugins\MetadataBrowser
 */
 class MetadataBrowserPlugin extends Omeka_Plugin_AbstractPlugin
{
    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array(
        'install',
        'uninstall',
        'config_form',
        'config',
        'define_routes',
        'admin_head',
        'public_head',
    );

    /**
     * @var array Filters for the plugin.
     */
    protected $_filters = array(
        'admin_navigation_main',
    );

    /**
     * @var array Options and their default values.
     */
    protected $_options = array(
        // Not sure if these are needed.
        'metadata_browser_configuration' => false,
        'metadata_browser_browse_points' => '',
    );

    /**
     * Install the plugin.
     */
    public function hookInstall()
    {
        // Create a Table to hold category information.
        $db = $this->_db;
        $sql = "
            CREATE TABLE IF NOT EXISTS `$db->MetadataBrowser_Category` (
                `id` int(10) unsigned NOT NULL auto_increment,
                `element_id` int(10) unsigned NOT NULL,
                `display_name` tinytext collate utf8_unicode_ci NOT NULL,
                `slug` tinytext collate utf8_unicode_ci NOT NULL,
                `active` tinyint(1) NOT NULL,
                PRIMARY KEY  (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ";
        $db->query($sql);

        // Populate table with all existing Elements as possible category choices.
        $setElements = $db->getTable('Element')->findall();

        // For ID value
        $num = 1;
        foreach($setElements as $element) {
            $element_id = $element->id;
            $display_name = $element->name;
            // Create a URL-friendly slug.
            $slug = metadata_browser_generate_slug($element->name);

            $sql = "
                INSERT INTO `$db->MetadataBrowser_Category`
                VALUES({$num}, {$element_id}, '{$display_name}', '{$slug}', 0);
            ";
            $db->query($sql);
            $num++;
        }

        $this->_installOptions();
    }

    /**
     * Uninstall the plugin.
     */
    public function hookUninstall()
    {
        $db = $this->_db;
        $sql = "DROP TABLE IF EXISTS `$db->MetadataBrowser_Category`";
        $db->query($sql);

        $this->_uninstallOptions();
    }

    /**
     * Shows plugin configuration page.
     */
    public function hookConfigForm($args)
    {
        $view = get_view();
        echo $view->partial(
            'plugins/metadata-browser-config-form.php'
        );
    }

    /**
     * Handle a submitted config form.
     *
     * @param array Options set in the config form.
     */
    public function hookConfig($args)
    {
        $post = $args['post'];
        foreach ($this->_options as $optionKey => $optionValue) {
            if (isset($post[$optionKey])) {
                set_option($optionKey, $post[$optionKey]);
            }
        }
    }

    /**
     * Define routes.
     *
     * @param Zend_Controller_Router_Rewrite $router
     */
    public function hookDefineRoutes($args)
    {
        $router = $args['router'];

        // The only parameters here are the id value of the element and the string to be searched.
        // route goal is convert the following style URL:
        //
        // items/browse?advanced[0][element_id]=51&advanced[0][type]=contains&advanced[0][terms]=photographs&submit_search=Search
        //
        // into something that looks like:
        //
        // items/browse/51/contains/photographs/Search
        // were 51 is the format element type ID and "photographs" is a format element values
        /*
        $route = new Zend_Controller_Router_Route(
            'items/browse/:advanced[0][element_id]/:advanced[0][type]/:advanced[0][terms]/:submit_action',
            array(
                'controller' => 'items',
                'action'     => 'browse',
                'advanced[0][element_id]' => '51',
                'advanced[0][type]' => 'contains',
                'advanced[0][terms]' => 'photographs',
                'submit_search' => 'Search',
            )
        );
        */

        // Route for public "category" index page displaying all current active
        // public categories.
        $router->addRoute(
            'matadata_browser_categories',
            new Zend_Controller_Router_Route(
                'category',
                array(
                    'module' => 'metadata-browser',
                    'controller' => 'category',
                    'action' => 'index',
        )));

        // Route to pass element_id to browsing routine.
        $router->addRoute(
            'metadata_browser_admin_list',
            new Zend_Controller_Router_Route(
                'metadata-browser/index/show/:element_id',
                array(
                    'module' => 'metadata-browser',
                    'controller' => 'index',
                    'action' => 'show',
                ),
                array(
                    'element_id' => '\d+',
                )
        ));

        // Create a route for each current active category using the slug given
        // to the category.
        // If you want to create route for every possible category, try the
        // findAll() method instead of findActiveCategories().
        $categories = $this->_db->getTable('MetadataBrowser_Category')
            ->findActiveCategories();
        foreach($categories as $category) {
            $router->addRoute(
                'metadata_browser_show_cat_' . $category->id,
                new Zend_Controller_Router_Route(
                    'category/' . $category->slug,
                    array(
                        'module' => 'metadata-browser',
                        'controller' => 'category',
                        'action' => 'browse',
                        'id' => $category->id,
            )));
        }
    }

    /**
     * Load the plugin css/javascript when admin section loads
     *
     * @return void
     */
    public function hookAdminHead($args)
    {
        queue_css_file('metadata-browser');
    }

    /**
     * Load the plugin css/javascript when public section loads
     *
     * @return void
     */
    public function hookPublicHead($args)
    {
        queue_css_file('metadata-browser');
    }

    /**
     * Add the plugin link to the admin main navigation.
     *
     * @param array Navigation array.
     * @return array Filtered navigation array.
    */
    public function filterAdminNavigationMain($nav)
    {
        $link = array(
            'label' => __('Metadata Browser'),
            'uri' => url(array(
                'module' => 'metadata-browser',
                'controller' => 'index',
                'action'=>'index',
            )),
            'privilege' => 'default',
        );
        $nav[] = $link;

        return $nav;
    }
}
