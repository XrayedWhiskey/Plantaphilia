<?php
if (\BorlabsCookie\Cookie\Backend\License::getInstance()->isPluginUnlocked()) {
?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?page=borlabs-cookie">Borlabs Cookie</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php _ex('Import &amp; Export', 'Backend / Import Export / Breadcrumb', 'borlabs-cookie'); ?></li>
    </ol>
</nav>

<?php echo \BorlabsCookie\Cookie\Backend\Messages::getInstance()->getAll(); ?>

<form action="?page=borlabs-cookie-import-export" method="post" id="BorlabsCookieForm" class="needs-validation" novalidate>
    <div class="row no-gutters mb-4">
        <div class="col-12 col-md-8 rounded-left bg-light shadow-sm">
            <div class="px-3 pt-3 pb-4">
                <h3 class="border-bottom mb-3"><?php _ex('Import', 'Backend / Import Export / Headline', 'borlabs-cookie'); ?></h3>
                <div class="row">
                    <div class="col-12">

                        <div class="form-group row">
                            <label for="importConfig" class="col-sm-4 col-form-label"><?php _ex('General &amp; Appearance Settings', 'Backend / Import Export / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <textarea class="form-control d-inline-block align-top w-75 mr-2" name="importConfig" id="importConfig" rows="5" autocapitalize="off" autocomplete="off" autocorrect="off" spellcheck="false"></textarea>
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('Insert the corresponding code from the <strong>Export</strong> field to import the <strong>General Settings and Appearance Settings</strong>.', 'Backend / Import Export / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="importCookiesAndGroups" class="col-sm-4 col-form-label"><?php _ex('Cookies &amp; Cookie Groups', 'Backend / Import Export / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <textarea class="form-control d-inline-block align-top w-75 mr-2" name="importCookiesAndGroups" id="importCookiesAndGroups" rows="5" autocapitalize="off" autocomplete="off" autocorrect="off" spellcheck="false"></textarea>
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('Insert the corresponding code from the <strong>Export</strong> field to import the <strong>Cookies and Cookie Groups</strong> data.', 'Backend / Import Export / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="importContentBlocker" class="col-sm-4 col-form-label"><?php _ex('Content Blocker', 'Backend / Import Export / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <textarea class="form-control d-inline-block align-top w-75 mr-2" name="importContentBlocker" id="importContentBlocker" rows="5" autocapitalize="off" autocomplete="off" autocorrect="off" spellcheck="false"></textarea>
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('Insert the corresponding code from the <strong>Export</strong> field to import the <strong>Content Blocker</strong> data.', 'Backend / Import Export / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="importScriptBlocker" class="col-sm-4 col-form-label"><?php _ex('Script Blocker', 'Backend / Import Export / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <textarea class="form-control d-inline-block align-top w-75 mr-2" name="importScriptBlocker" id="importScriptBlocker" rows="5" autocapitalize="off" autocomplete="off" autocorrect="off" spellcheck="false"></textarea>
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('Insert the corresponding code from the <strong>Export</strong> field to import the <strong>Script Blocker</strong> data.', 'Backend / Import Export / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-8 offset-sm-4">
                        <?php wp_nonce_field('borlabs_cookie_import'); ?>
                        <input type="hidden" name="action" value="import">
                        <button type="submit" class="btn btn-primary btn-sm"><?php _ex('Import', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4 rounded-right shadow-sm bg-tips text-light">
            <div class="px-3 pt-3 pb-3 mb-4">
                <h3 class="border-bottom mb-3"><?php _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>
                <h4><?php _ex('Language Settings', 'Backend / Import Export / Tips / Headline','borlabs-cookie'); ?></h4>
                <p><?php _ex('When importing, the language affiliation of the entries is ignored and all Settings, Cookies, Cookie Groups and Content Blockers are imported for the current language.', 'Backend / Import Export / Tips / Text', 'borlabs-cookie'); ?></p>
                <p><?php _ex('The current language is displayed in the Borlabs Cookie menu bar on the right, if WPML or Polylang is used.', 'Backend / Import Export / Tips / Text', 'borlabs-cookie'); ?></p>
            </div>
        </div>
    </div>
</form>

<div class="row no-gutters mb-4">
    <div class="col-12 col-md-8 rounded bg-light shadow-sm">
        <div class="px-3 pt-3 pb-4">
            <h3 class="border-bottom mb-3"><?php _ex('Export', 'Backend / Import Export / Headline', 'borlabs-cookie'); ?></h3>
            <div class="row">
                <div class="col-12">

                    <div class="form-group row">
                        <label for="config" class="col-sm-4 col-form-label"><?php _ex('General &amp; Appearance Settings', 'Backend / Import Export / Label', 'borlabs-cookie'); ?></label>
                        <div class="col-sm-8">
                            <textarea class="form-control d-inline-block align-top w-75 mr-2" name="config" id="config" rows="5" autocapitalize="off" autocomplete="off" autocorrect="off" spellcheck="false" disabled><?php echo $textareaConfig; ?></textarea>
                            <span data-clipboard="config" data-toggle="tooltip" title="<?php echo esc_attr_x('Click to copy to clipboard.', 'Backend / Global / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-clone"></i></span>
                            <span data-toggle="tooltip" title="<?php echo esc_attr_x('Copy this to export your <strong>General and Appearance Settings</strong>.', 'Backend / Import Export / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="cookiesAndGroups" class="col-sm-4 col-form-label"><?php _ex('Cookies &amp; Cookie Groups', 'Backend / Import Export / Label', 'borlabs-cookie'); ?></label>
                        <div class="col-sm-8">
                            <textarea class="form-control d-inline-block align-top w-75 mr-2" name="cookiesAndGroups" id="cookiesAndGroups" rows="5" autocapitalize="off" autocomplete="off" autocorrect="off" spellcheck="false" disabled><?php echo $textareaCookiesAndGroups; ?></textarea>
                            <span data-clipboard="cookiesAndGroups" data-toggle="tooltip" title="<?php echo esc_attr_x('Click to copy to clipboard.', 'Backend / Global / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-clone"></i></span>
                            <span data-toggle="tooltip" title="<?php echo esc_attr_x('Copy this to export your <strong>Cookies and Cookie Groups</strong> data.', 'Backend / Import Export / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="contentBlocker" class="col-sm-4 col-form-label"><?php _ex('Content Blocker', 'Backend / Import Export / Label', 'borlabs-cookie'); ?></label>
                        <div class="col-sm-8">
                            <textarea class="form-control d-inline-block align-top w-75 mr-2" name="contentBlocker" id="contentBlocker" rows="5" autocapitalize="off" autocomplete="off" autocorrect="off" spellcheck="false" disabled><?php echo $textareaContentBlocker; ?></textarea>
                            <span data-clipboard="contentBlocker" data-toggle="tooltip" title="<?php echo esc_attr_x('Click to copy to clipboard.', 'Backend / Global / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-clone"></i></span>
                            <span data-toggle="tooltip" title="<?php echo esc_attr_x('Copy this to export your <strong>Content Blocker</strong> data.', 'Backend / Import Export / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="scriptBlocker" class="col-sm-4 col-form-label"><?php _ex('Script Blocker', 'Backend / Import Export / Label', 'borlabs-cookie'); ?></label>
                        <div class="col-sm-8">
                            <textarea class="form-control d-inline-block align-top w-75 mr-2" name="scriptBlocker" id="scriptBlocker" rows="5" autocapitalize="off" autocomplete="off" autocorrect="off" spellcheck="false" disabled><?php echo $textareaScriptBlocker; ?></textarea>
                            <span data-clipboard="scriptBlocker" data-toggle="tooltip" title="<?php echo esc_attr_x('Click to copy to clipboard.', 'Backend / Global / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-clone"></i></span>
                            <span data-toggle="tooltip" title="<?php echo esc_attr_x('Copy this to export your <strong>Script Blocker</strong> data.', 'Backend / Import Export / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<?php
} else {
    echo \BorlabsCookie\Cookie\Backend\License::getInstance()->getLicenseMessageActivateKey();
}
?>
