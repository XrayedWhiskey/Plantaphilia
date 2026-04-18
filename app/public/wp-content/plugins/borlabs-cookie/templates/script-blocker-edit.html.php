<?php
if (\BorlabsCookie\Cookie\Backend\License::getInstance()->isPluginUnlocked()) {
    ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?page=borlabs-cookie">Borlabs Cookie</a></li>
            <li class="breadcrumb-item"><a href="?page=borlabs-cookie-script-blocker"><?php _ex('Script Blocker', 'Backend / Script Blocker / Breadcrumb', 'borlabs-cookie'); ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo sprintf(_x('Edit: %s', 'Backend / Script Blocker / Breadcrumb', 'borlabs-cookie'), $inputName); ?></li>
        </ol>
    </nav>

    <?php echo \BorlabsCookie\Cookie\Backend\Messages::getInstance()->getAll(); ?>

    <form action="?page=borlabs-cookie-script-blocker" method="post" id="BorlabsCookieForm" class="needs-validation" novalidate>
        <div class="row no-gutters mb-4">
            <div class="col-12 col-md-8 rounded-left bg-light shadow-sm">
                <div class="px-3 pt-3 pb-4">
                    <div class="row">
                        <div class="col">
                            <h3 class="border-bottom mb-3"><?php _ex('Script Blocker Settings', 'Backend / Script Blocker / Headline', 'borlabs-cookie'); ?></h3>

                            <div class="form-group row">
                                <label for="scriptBlockerId" class="col-sm-4 col-form-label"><?php _ex('ID', 'Backend / Script Blocker / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="scriptBlockerId" name="scriptBlockerId" value="<?php echo $inputScriptBlockerId; ?>" disabled>
                                    <span data-toggle="tooltip" title="<?php echo esc_attr_x('<strong>ID</strong> must be set. The <strong>ID</strong> must be at least 3 characters long and may only contain: <strong><em>a-z - _</em></strong>', 'Backend / Script Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="name" class="col-sm-4 col-form-label"><?php _ex('Name', 'Backend / Script Blocker / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="name" name="name" value="<?php echo $inputName; ?>" required>
                                    <span data-toggle="tooltip" title="<?php echo esc_attr_x('Choose a name for this Script Blocker.', 'Backend / Script Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                                    <div class="invalid-feedback"><?php _ex('This is a required field and cannot be empty.', 'Backend / Global / Validation Message', 'borlabs-cookie'); ?></div>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label class="col-sm-4 col-form-label"><?php _ex('Status', 'Backend / Script Blocker / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <button type="button" class="btn btn-sm btn-toggle mr-2<?php echo $switchStatus; ?>" data-toggle="button" data-switch-target="status" aria-pressed="<?php echo $inputStatus ? 'true' : 'false'; ?>" autocomplete="off"><span class="handle"></span></button>
                                    <input type="hidden" name="status" id="status" value="<?php echo $inputStatus; ?>">
                                    <span data-toggle="tooltip" title="<?php echo esc_attr_x('The status of this <strong>Script Blocker</strong>. If active (Status: ON) it will block the configured JavaScript.', 'Backend / Script Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <div class="form-group row align-items-top">
                                <div class="col-sm-4"><?php _ex('Blocked JavaScript Handles', 'Backend / Script Blocker / Text', 'borlabs-cookie'); ?></div>
                                <div class="col-sm-8">
                                    <?php
                                    if (!empty($blockedHandles)) {
                                        ?>
                                        <ul class="unordered-list">
                                            <?php
                                            foreach ($blockedHandles as $blockedHandle) {
                                                ?><li><?php echo esc_html($blockedHandle); ?></li><?php
                                            }
                                            ?></ul>
                                        <?php
                                    } else {
                                        ?><em><?php _ex('No JavaScript handle configured.', 'Backend / Script Blocker / Text', 'borlabs-cookie'); ?></em><?php
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="form-group row align-items-top">
                                <div class="col-sm-4"><?php _ex('Blocked Phrases', 'Backend / Script Blocker / Text', 'borlabs-cookie'); ?></div>
                                <div class="col-sm-8">
                                    <?php
                                    if (!empty($blockedPhrases)) {
                                        ?>
                                        <ul class="unordered-list">
                                            <?php
                                            foreach ($blockedPhrases as $blockedPhrase) {
                                                ?><li><?php echo esc_html($blockedPhrase); ?></li><?php
                                            }
                                            ?></ul>
                                        <?php
                                    } else {
                                        ?><em><?php _ex('No phrase configured.', 'Backend / Script Blocker / Text', 'borlabs-cookie'); ?></em><?php
                                    }
                                    ?>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-8 offset-sm-4">
                            <?php wp_nonce_field('borlabs_cookie_script_blocker_save'); ?>
                            <input type="hidden" name="action" value="save">
                            <input type="hidden" name="id" value="<?php echo $inputId; ?>">
                            <button type="submit" class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                            <a class="btn btn-secondary btn-sm" href="?page=borlabs-cookie-script-blocker"><?php _ex('Go back without saving', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4 rounded-right shadow-sm bg-tips text-light">
                <div class="px-3 pt-3 pb-3 mb-4">
                    <h3 class="border-bottom mb-3"><?php _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>

                    <div class="accordion" id="accordionScriptBlocker">
                        <button type="button" data-toggle="collapse" data-target="#accordionScriptBlockerOne" aria-expanded="true">
                            <?php _ex('How to add/remove handles/phrases', 'Backend / Script Blocker / Tips / Headline', 'borlabs-cookie'); ?>
                        </button>

                        <div id="accordionScriptBlockerOne" class="collapse show" data-parent="#accordionScriptBlocker">
                            <p><?php _ex('Handles and phrases of an existing <strong>Script Blocker</strong> cannot be changed. If you want to make changes, you must create a new <strong>Script Blocker</strong> and delete or deactivate the old one.', 'Backend / Script Blocker / Tips / Text', 'borlabs-cookie'); ?></p>
                        </div>

                        <button type="button" data-toggle="collapse" data-target="#accordionScriptBlockerTwo" aria-expanded="false">
                            <?php _ex('Blocked JavaScript Handles explained', 'Backend / Script Blocker / Tips / Headline', 'borlabs-cookie'); ?>
                        </button>

                        <div id="accordionScriptBlockerTwo" class="collapse" data-parent="#accordionScriptBlocker">
                            <p><?php _ex('JavaScript handles that the <strong>Script Blocker</strong> searches for and blocks. A handle is a developer-defined ID used to register a JavaScript file within the WordPress system.', 'Backend / Script Blocker / Tips / Text', 'borlabs-cookie'); ?></p>
                        </div>

                        <button type="button" data-toggle="collapse" data-target="#accordionScriptBlockerThree" aria-expanded="false">
                            <?php _ex('Blocked Phrases explained', 'Backend / Script Blocker / Tips / Headline', 'borlabs-cookie'); ?>
                        </button>

                        <div id="accordionScriptBlockerThree" class="collapse" data-parent="#accordionScriptBlocker">
                            <p><?php _ex('These are strings that the <strong>Script Blocker</strong> searches for in inline JavaScript. Any inline JavaScript that contains one of these phrases is blocked.', 'Backend / Script Blocker / Tips / Text', 'borlabs-cookie'); ?></p>
                            <p><?php _ex('This is necessary because there is no unique identifier for inline JavaScript.', 'Backend / Script Blocker / Tips / Text', 'borlabs-cookie'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="row no-gutters mb-4">
        <div class="col-12 col-md-8 rounded-left bg-light shadow-sm">
            <div class="px-3 pt-3 pb-4">
                <h3 class="border-bottom mb-3"><?php _ex('Unblock Code', 'Backend / Script Blocker / Headline', 'borlabs-cookie'); ?></h3>
                <div class="row">
                    <div class="col-12">

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="unblockScriptCookieCode"><?php _ex('For Cookies', 'Backend / Script Blocker / Headline', 'borlabs-cookie'); ?> <span data-toggle="tooltip" title="<?php echo esc_attr_x('Paste the unblock code in a <strong>Cookie</strong> into the <strong>Opt-in Code</strong> box.', 'Backend / Script Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span></label>
                                <span data-clipboard="unblockScriptCookieCode" data-toggle="tooltip" title="<?php echo esc_attr_x('Click to copy to clipboard.', 'Backend / Global / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-clone"></i></span>
                                <div class="code-editor"><textarea data-borlabs-html-editor name="unblockScriptCookieCode" id="unblockScriptCookieCode" rows="5"><?php echo $textareaUnblockScriptCookieCode; ?></textarea></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="unblockScriptContentBlockerCode"><?php _ex('For Content Blocker', 'Backend / Script Blocker / Headline', 'borlabs-cookie'); ?> <span data-toggle="tooltip" title="<?php echo esc_attr_x('Paste the unblock code in a <strong>Content Blocker</strong> into the <strong>Global</strong> field.', 'Backend / Script Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span></label>
                                <span data-clipboard="unblockScriptContentBlockerCode" data-toggle="tooltip" title="<?php echo esc_attr_x('Click to copy to clipboard.', 'Backend / Global / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-clone"></i></span>
                                <div class="code-editor"><textarea data-borlabs-html-editor name="unblockScriptContentBlockerCode" id="unblockScriptContentBlockerCode" rows="5"><?php echo $textareaUnblockScriptContentBlockerCode; ?></textarea></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4 rounded-right shadow-sm bg-tips text-light">
            <div class="px-3 pt-3 pb-3 mb-4">
                <h3 class="border-bottom mb-3"><?php _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>

                <h4><?php _ex('Unblock Code for Cookies', 'Backend / Script Blocker / Tips / Headline', 'borlabs-cookie'); ?></h4>
                <p><?php _ex('Paste the code in <strong>Cookies &gt; <em>Your Cookie</em></strong> in the <strong>Opt-in Code</strong> box. If a visitor gives their consent to the <strong>Cookie</strong>, the code will be executed.', 'Backend / Script Blocker / Tips / Text', 'borlabs-cookie'); ?></p>
                <p><?php _ex('This allows you to specifically unblock JavaScripts only after consent.', 'Backend / Script Blocker / Tips / Text', 'borlabs-cookie'); ?></p>

                <h4><?php _ex('Unblock Code for Content Blocker', 'Backend / Script Blocker / Tips / Headline', 'borlabs-cookie'); ?></h4>
                <p><?php _ex('Paste the code in a <strong>Content Blocker &gt; <em>Your Content Blocker</em></strong> in the <strong>Global</strong> field. A visitor can then click on the blocked content to give their consent and the <strong>Content Blocker</strong> executes the code.', 'Backend / Script Blocker / Tips / Text', 'borlabs-cookie'); ?></p>
                <p><?php _ex('With this you can e.g. integrate Google Maps via third-party plugins. The map is blocked by the <strong>Content Blocker</strong> and only loaded after the visitor has given their consent.', 'Backend / Script Blocker / Tips / Text', 'borlabs-cookie'); ?></p>
            </div>
        </div>
    </div>
    <?php
} else {
    echo \BorlabsCookie\Cookie\Backend\License::getInstance()->getLicenseMessageActivateKey();
}
?>
