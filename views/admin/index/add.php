<?php
$pageTitle = html_escape(__('Metadata Browser | Category'));
echo head(array(
    'title' => $pageTitle,
    'bodyclass' => 'metadata-browser',
    'content_class' => 'horizontal-nav',
));
?>
<div id="primary">
    <?php echo flash(); ?>
    <h1><?php echo $pageTitle; ?></h1>
    <form method="post">
        <?php include 'form.php'; ?>
        <?php echo $this->formSubmit(
            'metadata-browser-add-submit',
            'Add Category',
            array(
                'id' => 'metadata-browser-add-submit',
                'class' => 'submit submit-medium',
        )); ?>
    </form>
</div>
<?php echo foot();
