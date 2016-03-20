<?php
/**
 * The MetadataBrowser_Category class.
 *
 * @package Omeka\Plugins\MetadataBrowser
 */
class MetadataBrowser_Category extends Omeka_Record_AbstractRecord
{
    // automatic ID
    // element id that category links up with
    public $element_id;
    public $display_name;
    // if this a parent
    // public $parent_id;
    // how will the slugs work out?
    public $slug;
    public $active;

/*
    public function getCategoryId()
    {
        return $this->id;
    }

    public function getElementId()
    {
        return $this->element_id;
    }

    public function getCatSlug()
    {
        return $this->cat_slug;
    }

    // let's you change the public display name of the categry for the end user
    public function getDisplayName()
    {
        return $this->display_name;
    }
*/

    public function isActive()
    {
        return $this->active
            ? $this->active
            : 0;
    }

    public function getAssignedValues()
    {
        $values = array();

        $db = get_db();
        $texttable = $db->getTable('ElementText');
        $select = $texttable->getSelect()
            ->where('element_texts.element_id = ?', (integer) $this->element_id)
            ->group('element_texts.text')
            ->order('element_texts.text');
        $element_texts = $texttable->fetchObjects($select);
        foreach ($element_texts as $text) {
            // Should see if an html version exists that can be processed.
            if (!$text->isHtml()) {
                $value = $text->getText();
                array_push($values, $value);
            }
        }
        return $values;
    }

    public function getCategoryCount()
    {
        $db = get_db();
        $texttable = $db->getTable('ElementText');
        $select = $texttable->getSelect()
            ->where('element_texts.element_id = ' . (integer) $this->element_id)
            ->group('element_texts.text')
            ->order('element_texts.text');
        // $rows = $texttable->fetchAll($select);

        /*
        $element_text_select = $texttable->getSelect()->where('ie.element_id = ?', (int)$this->element_id)->group('ie.text')->order('ie.text');

        $element_text_count = $texttable->fetchObjects($element_text_select)->count();
        */

        $totalRecords = $db->query($select)->rowCount();
        return $totalRecords;
    }

    /**
     * Returns the description assigned to an Omeka element for a selected category.
     *
     * @todo Could be rewritten to allow the user to include a more colloquiol definition.
     */
    public function getCategoryDescription()
    {
        $db = get_db();
        $element = $db->getTable('Element')->find($this->element_id);
        return $element->description;
    }
}
