<?php
/**
 * Helpers for MetadataBrowser.
 *
 * @package MetadataBrowser
 */

/**
 * Get the element name.
 */
function metadata_browser_get_category_name($id)
{
    $db = get_db();
    // Lookup via id value
    $element = $db->getTable('Element')->find($id);
    /*
    $select = $db->getTable('Element')->get_select()->where('id = ?', $id);
    $element = $db->getTable('Element')->fetchObjects($select);
    */
    return $element->name;
}

/**
 * Helper function to return the current ID value of an element in the omeka
 * element table by supplying the name of the element. Used in creating links to
 * different browsing categories.
 */
function metadata_browser_get_element_id($name)
{
    $db = get_db();
    $elementTable = $db->getTable('Element');
    $elementSelect = $elementTable->getSelect()
        ->where("name = '$name'");
    $element =  $elementTable->fetchObject($elementSelect);
    return $element->id;
}

/**
 *
 */
function metadata_browser_get_active_cat($name)
{
    $db = get_db();
    $element_id = metadata_browser_get_element_id($name);
    $category = $db->getTable('MetadataBrowser_Category')
        ->findByElementId($element_id);
    if($category->isActive()) {
        return $category;
    } else { return "Browsing Option is not Active"; }
}

/**
 * helper function that uses Omeka advanced search parameters
 * to pull together browsing options for a given
 * string used as an element value
 * $id is an omeka "element_id" value for the element that will be serched
 */
function metadata_browser_create_link($id, $value)
{
    // utility function to create browsing link for display in a theme
    // querystring based on current omeka advanced search linking
    $clean_value = trim($value); // remove whitespace at start or end of string
    $queryURL = url("items/browse?advanced[0][element_id]=" . $id . "&advanced[0][type]=contains&advanced[0][terms]=" . $clean_value);
    $resultsLink = '<a class="browse-link" href="' . $queryURL . '" title="Browse ' . $clean_value . '">' . $clean_value . "</a>";
    return $resultsLink;
}

/**
 * Create a link when display_name is passed instead of id value.
 *
 * Returns just a URL to browse to a certain element value string rather than
 * the entirely link.
 */
function metadata_browser_create_url($id, $value)
{
    $clean_value = trim($value); // remove whitespace at start or end of string
    $queryURL = htmlspecialchars("items/browse?advanced[0][element_id]=" . $id . "&advanced[0][type]=contains&advanced[0][terms]=" . $clean_value);
    return $queryURL;
}

/**
 *
 * Takes element name and value to build link for use primarily on "item/show"
 * page of theme.
 */
function metadata_browser_element_link($name,$value)
{
    $id = metadata_browser_get_element_id($name);
    $clean_value = trim($value); // remove whitespace at start or end of string
    $queryURL = url("items/browse?advanced[0][element_id]=" . $id . "&advanced[0][type]=contains&advanced[0][terms]=" . $clean_value);
    $resultsLink = '<a class="browse-link" href="' . $queryURL . '" title="Browse ' . $clean_value . '">' . $clean_value . "</a>";
    return $resultsLink;
}

/**
 * Generates a slug from a string (sanitize a string).
 *
 * @note Slug code taken "simplePagesPage plugin model class"
 *
 * @internal maybe this should be defined as a method for the MetadataBrowser_Category Class
 *
 * @param string $seed
 * @return string
 */
function metadata_browser_generate_slug($seed)
{
    $seed = trim($seed);
    $seed = strtolower($seed);
    // Replace spaces with dashes.
    $seed = str_replace(' ', '-', $seed);
    $seed = str_replace('/', '-', $seed);
    // Remove all but alphanumeric characters, underscores, and dashes.
    return preg_replace('/[^\w\/-]/i', '', $seed);
}

/**
 * Used by "browse" view in admin views directory
 *
 * This function awkwardly reads the "ElementSet" and
 * element tables in order to display all possible
 * browsing categories based on all available elements
 * including those that may been added to the set of
 * available elements since the the initial installation of
 * the plugin.
 *
 * @return string Html.
 */
function metadata_browser_generate_element_select()
{
    $db = get_db();
    $element_sets = $db->getTable('ElementSet')->findall();
    // alternate query to sort element element sets

    $setlist = '';
    foreach ($element_sets as $set) {
        // how do you do these queries?
        $setElements = $db->getTable('Element')->findBySet($set->name);
        foreach($setElements as $element) {
            // get a category object if one exits with the element id in question
            $category = $db->getTable('MetadataBrowser_Category')
                ->findByElementId($element->id);
            $setlist .= '<tr>';
            if ($category) {
                $setlist .= '<td>' . $category->display_name . '</td>';
                $setlist .= '<td>' . $category->slug . '</td>';
                $setlist .= '<td>' . $set->name . '</td>';
                $setlist .= '<td>' . metadata_browser_active_checkbox($category->id) . '</td>';
                $setlist .= '<td>[<a href="' . html_escape(url('metadata-browser/index/show/' . $category->element_id)) . '">' . __('View Assigned Values') . '</a>]</td>';
                $setlist .= '<td><a class="edit" href="' . html_escape(url('metadata-browser/index/edit/id/' . $category->id)) . '">' . __('Edit') . '</a></td>';
            }
             // if the category does not exist yet use element information to make a skeleton record
            else {
                $setlist .= '<td>' . $element->name . '</td>';
                $setlist .= '<td>' . metadata_browser_generate_slug($element->name) . '</td>';
                $setlist .= '<td>' . $set->name . '</td>';
                $setlist .= 'td>[<a href="' . html_escape(url('metadata-browser/index/show/' . $element->id)) . '">' . __('View Assigned Values') . '</a>]</td>';
                $setlist .= '<td><a class="add" href="' . html_escape(url('metadata-browser/index/add/id/' . $element->id)) . '">' . __('Activate') . '</a></td>';
                $setlist .= '<td></td>';
            }
            $setlist .= '</tr>';
        }
    }

    return $setlist;
}

/**
 *
 */
function metadata_browser_get_cat_description($element_id)
{
    $db = get_db();
    $element = $db->getTable('Element')->find($element_id);
    return $element->description;
}

/**
 *
 */
function metadata_browser_is_active($id)
{
    $db = get_db();
    $element = $db->getTable('MetadataBrowser_Category')->find($id);
    return $element->active;
}

/**
 * Generate a checkbox to activate public browsing for a given category
 * for use within the "browse" view of the admin pages for the plugin.
 * This is also used by the "powerEdit" method of the index controller
 * to process category activations in batch mode using the browse view.
*/
function metadata_browser_active_checkbox($id)
{
    $view = get_view();
    $checked = (boolean) metadata_browser_is_active($id);

    $checkbox = '';
    $checkbox .= $view->formHidden('categories[' . $id . '][active]', 0);
    $checkbox .= $view->formCheckbox('categories[' . $id . '][active]', true,
        array(
            'id' => 'categories-' . $id . '-active',
            'checked' => $checked,
            'class' => 'checkbox make-active'
    ));
    $checkbox .= $view->formHidden('categories[' . $id . '][id]', $id);

    return $checkbox;
}
