<?php
// admin that shows current browing choices
$pageTitle = __('Metadata Browser Current Browsing Options');
echo head(array(
    'title' => $pageTitle,
    'bodyclass' => 'metadata-browser',
    'content_class' => 'horizontal-nav',
));
?>
<div id="primary">
    <?php echo flash(); ?>
    <h1><?php echo __('Available Metadata Browsing Options'); ?></h1>
    <form id="metadata-browser-browse" action="<?php echo html_escape(url('metadata-browser/index/power-edit')); ?>" method="post" accept-charset="utf-8">
        <table>
            <tr>
                <th><?php echo __('Display Name'); ?></th>
                <th><?php echo __('Slug'); ?></th>
                <th><?php echo __('Element Set'); ?></th>
                <th><?php echo __('Active'); ?></th>
                <th><?php echo __('Browse Values'); ?></th>
                <th><?php echo __('Edit'); ?></th>
            </tr>
            <?php echo metadata_browser_generate_element_select(); ?>
        </table>
        <fieldset>
            <input type="submit" class="submit submit-medium" id="save-changes" name="submit" value="Update Categories" />
        </fieldset>
    </form>
</div>
<?php echo foot();
