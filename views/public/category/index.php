<?php
// List all top level browsing categories and provide links to the them
$pageTitle = __('Browse by Category');
echo head(array(
    'title' => $pageTitle,
    'bodyclass' => 'metadata-browser',
));
?>
<div id="primary">
    <h1><?php echo $pageTitle; ?></h1>
	<ul class="navigation item-tags" id="secondary-nav">
		<li><?php echo __('Browse by:'); ?></li>
		<?php echo nav(array(
            array(
                'label' => __('Title'),
                'uri' => url('items/browse', null, array('sort_field' => 'Dublin Core,Title')),
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
                'uri' => url('items/browse', null, array('sort_field' => 'Dublin Core,Creator')),
            ),
            array(
                'label' => __('Most Recent'),
                'uri' => url('items/browse', null, array('sort_field' => 'added', 'sort_dir' => 'd')),
            ),
        )); ?>
	</ul>
	<div class='metadataBrowserDisplay'>
		<ul class='categories'>
		<?php foreach ($categories as $category): ?>
			<li class="top-category">
			    <?php echo sprintf('<a alt="%s" href="%s" title="%s">%s (%d)</a>',
				    html_escape($category->getCategoryDescription()),
				    url('category/' . $category->slug),
                    __('Browse %s', html_escape($category->display_name)),
                    html_escape($category->display_name),
                    $category->getCategoryCount()); ?>
			</li>
		<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php echo foot();
