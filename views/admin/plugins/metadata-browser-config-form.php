<fieldset id="fieldset-metadata-browser-main"><legend><?php echo __('Metadata Browser'); ?></legend>
    <?php // create a form for metadata browsing configuration options ?>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('metadata_browser_configuration',
                __('Activate Menu')); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formCheckbox('metadata_browser_configuration', true,
                array('checked' => (boolean) get_option('metadata_browser_configuration'))); ?>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('metadata_browser_browse_points',
                __('List Elements to Be Browsed')); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formText('metadata_browser_browse_points', get_option('metadata_browser_browse_points'), null); ?>
            <p class="explanation">
                <?php echo __(' (Use Id values)'); ?>
            </p>
        </div>
    </div>
</fieldset>
