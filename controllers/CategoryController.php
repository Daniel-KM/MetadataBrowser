<?php
/**
 * Controller handles public theme views for plugin
 *
 * @package Omeka\Plugins\MetadataBrowser
 */
class MetadataBrowser_CategoryController extends Omeka_Controller_AbstractActionController
{
    /**
     * Controller-wide initialization. Sets the underlying model to use.
     */
    public function init()
    {
        $this->_helper->db->setDefaultModelName('MetadataBrowser_Category');
    }

    /**
     * Displays a list of all active browsing category.
     * It is routed to show up at WEB_ROOT/category.
     */
    public function indexAction()
    {
        $categories = $this->_helper->db->findActiveCategories();
        $this->view->categories = $categories;
    }

    /**
     * Depends on a valid route being defined for the slug of the category in
     * question.
     */
    public function browseAction()
    {
        $id = $this->_getParam('id');
        $category = $this->_helper->db->find($id);

        // Restrict access to the page when it is not published.
        // is this valid && !$this->isAllowed('show-unpublished')
        if (!$category->active) {
             throw new Omeka_Controller_Exception_403;
        }

        //$slug = $this->_getParam('slug');
        $this->view->category = $category;
    }
}
