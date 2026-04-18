<?php
if (\BorlabsCookie\Cookie\Backend\License::getInstance()->isPluginUnlocked()) {
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?page=borlabs-cookie">Borlabs Cookie</a></li>
        <li class="breadcrumb-item"><a href="?page=borlabs-cookie-script-blocker"><?php _ex('Script Blocker', 'Backend / Script Blocker / Breadcrumb', 'borlabs-cookie'); ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php _ex('Wizard Step 1: Select page or enter URL', 'Backend / Script Blocker / Breadcrumb', 'borlabs-cookie'); ?></li>
    </ol>
</nav>

<?php echo \BorlabsCookie\Cookie\Backend\Messages::getInstance()->getAll(); ?>


<?php
if ($borlabsCookieStatus === true) {
?>
<form action="?page=borlabs-cookie-script-blocker&action=wizardStep-2" method="post" id="BorlabsCookieForm">
    <div class="row no-gutters mb-4">
        <div class="col-12 col-md-8 rounded-left bg-light shadow-sm">
            <div class="px-3 pt-3 pb-4">
                <h3 class="border-bottom mb-3"><?php _ex('Select a page or enter a URL', 'Backend / Script Blocker / Headline', 'borlabs-cookie'); ?></h3>
                <div class="row">
                    <div class="col">

                        <div class="alert alert-warning mt-2"><?php _ex('If you use caching and optimization plugins such as Autoptimize, please disable them before scanning.', 'Backend / Script Blocker / Text', 'borlabs-cookie'); ?></div>

                        <div class="form-group row">
                            <label for="scanPageId" class="col-sm-4 col-form-label"><?php _ex('Scan page for JavaScript', 'Backend / Script Blocker / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <?php
                                    $scanPageSelect = wp_dropdown_pages([
                                        'name' => 'scanPageId',
                                        'id' => 'scanPageId',
                                        'class' => 'form-control form-control form-control-sm d-inline-block w-75 mr-2 mb-2',
                                        'option_none_value' => 0,
                                        'selected' => $inputScanPageId,
                                        'show_option_none' => _x('-- Please select --', 'Backend / Global / Default Select Option', 'borlabs-cookie'),
                                        'sort_column' => 'post_title',
                                        'echo' => 0,
                                    ]);

                                    if (!empty($inputScanCustomURL)) {
                                        $scanPageSelect = str_replace('<select', '<select disabled', $scanPageSelect);
                                    }

                                    echo $scanPageSelect;
                                ?>

                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('Select a URL to be scanned for JavaScript. Alternatively you can enter a URL of your WordPress page in the second field.', 'Backend / Script Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>

                                <div class="input-group input-group-sm w-75">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <input type="checkbox" name="enableScanCustomURL" value="1" data-enable-target="scanCustomURL" data-disable-target="scanPageId"<?php echo !empty($inputScanCustomURL) ? ' checked' : ''; ?>>
                                        </span>
                                    </div>
                                    <input type="url" class="form-control form-control-sm d-inline-block" id="scanCustomURL" name="scanCustomURL" value="<?php echo $inputScanCustomURL; ?>" placeholder="https://"<?php echo empty($inputScanCustomURL) ? ' disabled' : ''; ?>>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="searchPhrases" class="col-sm-4 col-form-label"><?php _ex('Search phrases', 'Backend / Script Blocker / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="searchPhrases" name="searchPhrases" value="<?php echo $inputSearchPhrases; ?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('The string Borlabs Cookie is looking for. Separate multiple entries with a comma.', 'Backend / Script Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-8 offset-sm-4">
                                <button type="submit" class="btn btn-primary btn-sm"><?php _ex('Scan', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4 rounded-right shadow-sm bg-tips text-light">
            <div class="px-3 pt-3 pb-3 mb-4">
                <h3 class="border-bottom mb-3"><?php _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>

                <h4><?php _ex('How does it work?', 'Backend / Script Blocker / Tips / Headline', 'borlabs-cookie'); ?></h4>
                <p><?php _ex('Specify a page that you want Borlabs Cookie to scan for JavaScripts. You can use search phrases to fine-tune your search.', 'Backend / Script Blocker / Tips / Text', 'borlabs-cookie'); ?></p>
                <p><?php _ex('Borlabs Cookie then scans the file names, handles, and code of the JavaScripts. JavaScripts that contain the search phrases you specify are displayed separately.', 'Backend / Script Blocker / Tips / Text', 'borlabs-cookie'); ?></p>
            </div>
        </div>

    </div>
</form>
<?php
} else {
?>
<div class="alert alert-danger mt-2"><?php _ex('Please activate Borlabs Cookie under <strong>Settings &gt; Borlabs Cookie Status</strong> to use the <strong>Script Blocker</strong>.', 'Backend / Script Blocker / Text', 'borlabs-cookie'); ?></div>
<?php
}
?>
<?php
} else {
    echo \BorlabsCookie\Cookie\Backend\License::getInstance()->getLicenseMessageActivateKey();
}
?>
