<?php
$pageTitle = __('Category Display Choices: %s', $category->display_name);
echo head(array(
    'title' => $pageTitle,
    'bodyclass' => 'metadata-browser',
    'content_class' => 'horizontal-nav',
));
?>
<div id="primary">
    <?php echo flash(); ?>
    <h1><?php echo $pageTitle; ?></h1>
    <p>[<a class="category" href="<?php echo html_escape(url('metadata-browser/index/show/' . $category->element_id)); ?>"><?php echo __('View Current Assigned Values'); ?></a>]</p>
	<form method="post">
	<?php
        include 'form.php';
        echo $this->formSubmit('metadata-browser-edit-submit',
             'Save Category',
             array(
                'id' => 'metadata-browser-edit-submit',
                'class' => 'submit submit-medium',
        ));
    ?>
	</form>
    <p id="metadata-browser-delete">
        <a class="delete" href="<?php echo html_escape(url("metadata-browser/index/delete/id/$category->id")); ?>"><?php echo __('Delete This Category'); ?></a>
    </p>
</div>
<?php echo foot();
