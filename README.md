Metadata Browser (plugin for Omeka)
==================================

[Metadata Browser] is a plugin for [Omeka] that creates browseable Omeka
categories, initially built for the [digitalMETRO Project].

It is available for Omeka 1.3.2 ([Metadata Browser (1.x)]) and for Omeka 2 ([Metadata Browser (2.x)]).

Related plugin: SortBrowseResults

**Warning**
This plugin is no longer maintained. Some ideas of it have been included in [Reference].


Installation
------------

Uncompress files and rename plugin folder "MetadataBrowser".

Then install it like any other Omeka plugin.


Usage for [Metadata Browser (1.x)]
----------------------------------

See files inside "views" for [Metadata Browser (2.x)].

Covers how to use the MetadataBrowser plugin to create linking for selected
Omeka elements in "yourcurrenttheme/items/show.php" or "yourcurrenttheme/items/browse.php"
and add category browsing to your secondary theme navigation. At present these
two steps need to be done manually.

Refactors the Omeka Elements table to produce browsable views of element content
in both the Admin and Public Interface. The elements have been renamed
"categories" in the interest of using terms that will be familiar to general
users.

The MetadataBrowser_Category class enables the user to denote categories as
"public" as well as change the value of the "slug" and descriptive "label" that
is displayed for context in the interface when the category is displayed. Each
"category" is traced back to original element declaration by the "element_id"
value assigned to each category. This value is taken directly from the
"Elements" table by the "metadata_browser_generate_element_select" utility
method in plugin.php for the plugin.

See file *Linking Instructions for Themes* below on how to activate browsing in
theme "show" and "browse" views.

* Linking Instructions for Themes

In item/browse.php or item/show.php you currently need to go through each
element value one-by-one in order to activate browsing for selected elements
manually. It would be better to find a way to do this automatically via the
show_item_metadata() helper function but at present this is the way to do it.
These linking instructions also work in the browse.php file in an Omeka theme.
Once a plug-in is installed all you need to do is insert the following:

  ** Generic Model:

```
 <?php if ($element = item('My Element Set', 'My Element Name', array('all'=>'true'))): ?>
    <div id="my-element-name" class="element">
        <h3>My Element Name (Desired Public Value)</h3>
        <?php foreach($element as $value) {?>
            <div class="element-text"><?php echo metadata_browser_element_link("My Element Name", $value);?></div>
        <?php }?>
    </div>
<?php endif; ?>
```

  ** Example for the Dublin Core Element "Creator":

```
<?php if ($creator = item('Dublin Core', 'Creator', array('all'=>'true'))): ?>
    <div id="dublin-core-creator" class="element">
        <h3>Creator</h3>
        <?php foreach($creator as $value) {?>
            <div class="element-text"><?php echo metadata_browser_element_link("Creator", $value);?></div>
        <?php }?>
    </div>
<?php endif; ?>
```

* Add Category Browsing to your Theme's Navigation Options

Adding the top-level public "/category" view where all active categories
selected in the admin module are displayed to a secondary browsing option needs
to be done manually as well. You will need to execute something like the
following where you wish the secondary navigations options to appear in your
theme, such as the browse.php file.

  ** Example from browse.php:

```
<ul class="items-nav navigation" id="secondary-nav">
    <li>Browse by: </li>
    <?php echo nav(array('Title' => uri('items/browse', array('sortby'=>'dc.title')), 'Category' => uri('category'), 'Tag' => uri('items/tags'), 'Creator' => uri('items/browse', array('sortby'=>'dc.creator')), 'Most Recent' => uri('items/browse', array('sortby'=>'omeka.modified', 'sortorder'=>'desc')))); ?>
</ul>
```

The value 'Category' => uri('category') needs to be added to the array passed to
the nav() function.

Note: The above example expects you have the "SortBrowseResults" plugin
installed to enable sorting by field values like 'dc.title' and 'dc.creater'.


TODO
----

(7.31.2010)

1. Add a description field to class to enable user-supplied definition rather
than depend on element definition which can be very technical.
2. Routes for individual element values a la wordpress
3. Reliable value counting method for each value assigned to a given category.
4. Advanced search and database query for string production different values.
5. Automatic way to activate browsing by value for active categories in "show"
and "browse" pages. This now needs to be done manually. See "howtolink.txt" for
more information on how to currently do this.


Warning
-------

Use it at your own risk.

It's always recommended to backup your files and database regularly so you can
roll back if needed.


Troubleshooting
---------------

See online issues on the [plugin issues] page on GitHub.


License
-------

This plugin is published under [GNU/GPL v3].

This program is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT
ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
details.

You should have received a copy of the GNU General Public License along with
this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.


Contact
-------

Current maintainers:

* Kevin Reiss (mail: <kevin.reiss@gmail.com>, see [kevinreiss] on GitHub)
* Daniel Berthereau (see [Daniel-KM] on GitHub, release [Metadata Browser (2.x)])


Copyright
---------

* Copyright Kevin Reiss, 2010
* Copyright Daniel Berthereau, 2016


[Omeka]: https://omeka.org
[Metadata Browser]: https://github.com/kevinreiss/Omeka-MetadataBrowser
[digitalMETRO Project]: http://nycdigital.org/
[Metadata Browser (1.x)]: https://github.com/kevinreiss/Omeka-MetadataBrowser
[Metadata Browser (2.x)]: https://github.com/Daniel-KM/MetadataBrowser
[Reference]: https://github.com/Daniel-KM/Reference
[plugin issues (1.x)]: https://github.com/kevinreiss/Omeka-MetadataBrowser/issues
[plugin issues (2.x)]: https://github.com/Daniel-KM/MetadataBrowser/issues
[GNU/GPL v3]: https://www.gnu.org/licenses/gpl-3.0.html
[kevinreiss]: https://github.com/kevinreiss
[Daniel-KM]: https://github.com/Daniel-KM "Daniel Berthereau"
