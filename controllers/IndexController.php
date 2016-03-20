<?php
/**
 * The MetadataBrowser index controller class.
 *
 * Actions for administrative theme views for plugin
 *
 * @package Omeka\Plugins\MetadataBrowser
 */
class MetadataBrowser_IndexController extends Omeka_Controller_AbstractActionController
{
    public function init()
    {
        // Set the model class so this controller can perform some functions,Ed
        // such as $this->_helper->db->findById()
        $this->_helper->db->setDefaultModelName('MetadataBrowser_Category');
    }

    // list top-level browing categories selected via plug-in menu
    public function indexAction()
    {
        $this->_forward('browse');
    }

    // show the results for an individual category
    public function showAction()
    {
        $elementId = $this->_getParam('element_id');
        $category = $this->_helper->db->findByElementId($elementId);
        if (empty($category)) {
            $msg = __('The id #%d is not an existing element.', $elementId);
            $this->_helper->_flashMessenger($msg, 'error');
            $this->_helper->redirector->goto('browse');
        }
        $this->view->category = $category;
    }

    public function browseAction()
    {
    }

    public function editAction()
    {
        $id = $this->_getParam('id');
        $category = $this->_helper->db->find($id);
        if (empty($category)) {
            $msg = __('The category id #%d is not an existing category.', $elementId);
            $this->_helper->_flashMessenger($msg, 'error');
            $this->_helper->redirector->goto('browse');
        }
        $this->_processCategoryForm($category, 'edit');
    }

    public function addAction()
    {
        $elementId = $this->_getParam('id');
        $element = $this->_helper->db->getTable('Element')->find($elementId);

        if (empty($element)) {
            $msg = __('The category id #%d is not an existing element id.', $elementId);
            $this->_helper->_flashMessenger($msg, 'error');
            $this->_helper->redirector->goto('browse');
        }

        $category = new MetadataBrowser_Category();
        $category->element_id = $element->id;
        $category->display_name = $element->name;
        $category->slug = metadata_browser_generate_slug($element->name);
        $this->_processCategoryForm($category, 'add');
    }

    public function powerEditAction()
    {
        /*
       POST in this format:
            categories[1][active],
            categories[1][id],
            categories[2]...etc
        */
        if (empty($_POST)) {
            $this->_helper->redirector->goto('browse');
        }
        try {
            $categoryArray = $this->_getParam('categories');
            if ($categoryArray) {
                // Loop through the IDs given and toggle
                foreach ($categoryArray as $k => $fields) {
                    if (!array_key_exists('id', $fields) || !array_key_exists('active', $fields)) {
                        throw new Exception(__('Power-edit request was mal-formed!'));
                    }

                    //$category = $this->findById($fields['id']);
                    $category = $this->_helper->db->find($fields['id']);
                    /*
                    * Following routine handles different cases of active/inactive values.
                    */
                    if ($fields['active'] == 1) {
                        if (!$category->active) {
                            $category->active = 1;
                            $category->save();
                        }
                    }
                    else {
                        if ($category->active) {
                            $category->active = 0;
                            $category->save();
                        }
                    }
                    //$category->active = 0;
                }
            }
            $msg = __('Categories were successfully updated!');
            $this->_helper->_flashMessenger($msg, 'success');
        } catch (Exception $e) {
            $this->_helper->_flashMessenger($e->getMessage(), 'error');
        }

        $this->_helper->redirector->gotoUrl($_SERVER['HTTP_REFERER']);
    }

    private function _processCategoryForm($category, $action)
    {
        if (!is_object($category)) {
            $msg = __('The category id is not an existing element id.');
            $this->_helper->_flashMessenger($msg, 'error');
            $this->_helper->redirector->goto('browse');
        }

        // Attempt to save the form if there is a valid POST. If the form
        // is successfully saved, set the flash message, unset the POST,
        // and redirect to the browse action.
        if ($this->getRequest()->isPost()) {
            try {
                $category->setPostData($_POST);
                if ($category->save()) {
                    if ($action == 'add') {
                        $msg = __('The category "%s" has been added.', $category->display_name);
                        $this->_helper->_flashMessenger($msg, 'success');
                    }
                    elseif ($action == 'edit') {
                        $msg = __('The category "%s" has been edited.', $category->display_name);
                        $this->_helper->_flashMessenger($msg, 'success');
                    }
                    unset($_POST);
                    $this->_helper->redirector->goto('browse');
                    return;
                }
            // Catch validation errors.
            } catch (Omeka_Validate_Exception $e) {
                $this->_helper->_flashMessenger($e, 'error');
            // Catch any other errors that may occur.
            } catch (Exception $e) {
                $this->_helper->_flashMessenger($e->getMessage(), 'error');
            }
        }
        // Set the page object to the view.
        $this->view->category = $category;
    }
}
