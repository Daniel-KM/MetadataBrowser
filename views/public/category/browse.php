<?php
// Display all values in a given browsing category
$pageTitle = __('Browse by ' . $category->display_name);
echo head(array(
    'title' => $pageTitle,
    'bodyclass' => 'metadata-browser',
));
?>
<div id="primary">
    <h1><?php echo $pageTitle; ?></h1>
	<p class="explanation">
       <?php echo html_escape($category->getCategoryDescription()); ?>
   </p>
	<?php // you will need to make sure this works with your selected theme ?>
	<ul class="navigation item-tags" id="secondary-nav">
		<li><?php echo __('Browse by:'); ?></li>
        <?php echo nav(array(
            array(
                'label' => __('Title'),
                'uri' => url('items/browse', array('sort_field' => 'Dublin Core,Title')),
            ),
            array(
                'label' => __('Category'),
                'uri' => url('category'),
            ),
            array(
                'label' => __('Tag'),
                'uri' => url('items/tags'),
            ),
            array(
                'label' => __('Creator'),
                'uri' => url('items/browse', array('sort_field' => 'Dublin Core,Creator')),
            ),
            array(
                'label' => __('Most Recent'),
                'uri' => url('items/browse', array('sort_field' => 'added', 'sort_dir' => 'd')),
            ),
        )); ?>
	</ul>
	<div class='metadataBrowserDisplay'>
		<ul>
		<?php $categoryValues = $category->getAssignedValues();
		foreach($categoryValues as $value):
		?>
			<li class="category"><?php echo metadata_browser_create_link($category->element_id, $value)?></li>
		<?php endforeach; ?>
		</ul>
	</div>
	<?php if ($category->getCategoryCount() >= 10): ?>
	<ul class="navigation item-tags" id="secondary-nav">
		<li><?php echo __('Browse by:'); ?></li>
        <?php echo nav(array(
            array(
                'label' => __('Title'),
                'uri' => url('items/browse', array('sort_field' => 'Dublin Core,Title')),
            ),
            array(
                'label' => __('Category'),
                'uri' => url('category'),
            ),
            array(
                'label' => __('Tag'),
                'uri' => url('items/tags'),
            ),
            array(
                'label' => __('Creator'),
                'uri' => url('items/browse', array('sort_field' => 'Dublin Core,Creator')),
            ),
            array(
                'label' => __('Most Recent'),
                'uri' => url('items/browse', array('sort_field' => 'added', 'sort_dir' => 'd')),
            ),
        )); ?>
	</ul>
	<?php endif; ?>
</div>
<?php echo foot();
