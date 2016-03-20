<?php
// admin that shows current browing choices
$pageTitle = __('Browse Current Values for %s', $category->display_name);
echo head(array(
    'title' => $pageTitle,
    'bodyclass' => 'metadata-browser',
    'content_class' => 'horizontal-nav',
));
?>
<div id="primary">
    <?php echo flash(); ?>
    <h1><?php echo $pageTitle; ?></h1>
	<h2>
	    <a class="edit" href="<?php echo html_escape(url('metadata-browser/index/edit/id/' . $category->id)); ?>"><?php echo __('Edit Category Options for %s', $category->display_name); ?></a>
    </h2>
   <h3><?php echo __('Total Number of Values Assigned to this Category: %d', $category->getCategoryCount()); ?></h3>
	<ul>
		<?php $categoryValues = $category->getAssignedValues(); ?>
		<?php foreach($categoryValues as $value): ?>
			<li class='category'><?php echo metadata_browser_create_link($category->element_id, $value); ?></li>
		<?php endforeach; ?>
	</ul>
</div>
<?php echo foot();
