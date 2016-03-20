<?php
/**
 * The MetadataBrowser_Category table.
 *
 * @package Omeka\Plugins\MetadataBrowser
 */
class Table_MetadataBrowser_Category extends Omeka_Db_Table
{
    public function findAllPagesOrderBySlug()
    {
        $select = $this->getSelect()->order('slug');
        return $this->fetchObjects($select);
    }

    public function findAllPagesOrderByDisplayName()
    {
        $select = $this->getSelect()->order('display_name');
        return $this->fetchObjects($select);
    }

    public function findByElementId($elementId)
    {
        $select = $this->getSelect()
            ->where('element_id = ?', (integer) $elementId);
        return $this->fetchObject($select);
    }

    public function findActiveCategories()
    {
        $select = $this->getSelect()
            ->where('active = 1')
            ->order('display_name');
        return $this->fetchObjects($select);
    }
}
