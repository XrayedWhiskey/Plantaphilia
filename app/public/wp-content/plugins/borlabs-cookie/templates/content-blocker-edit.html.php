<?php
if (\BorlabsCookie\Cookie\Backend\License::getInstance()->isPluginUnlocked()) {
?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?page=borlabs-cookie">Borlabs Cookie</a></li>
        <li class="breadcrumb-item"><a href="?page=borlabs-cookie-content-blocker"><?php _ex('Content Blocker', 'Backend / Content Blocker / Breadcrumb', 'borlabs-cookie'); ?></a></li>
        <?php
        if (!empty($contentBlockerData->id)) {
            ?><li class="breadcrumb-item active" aria-current="page"><?php echo sprintf(_x('Edit: %s', 'Backend / Content Blocker / Breadcrumb', 'borlabs-cookie'), $inputName); ?></li><?php
        } else {
            ?><li class="breadcrumb-item active" aria-current="page"><?php _ex('New', 'Backend / Content Blocker / Breadcrumb', 'borlabs-cookie'); ?></li><?php
        }
        ?>
    </ol>
</nav>

<?php echo \BorlabsCookie\Cookie\Backend\Messages::getInstance()->getAll(); ?>

<?php echo !empty($contentBlockerData->description) ? '<div class="alert alert-info">'.$contentBlockerData->description.'</div>' : ''; ?>
<form action="?page=borlabs-cookie-content-blocker" method="post" id="BorlabsCookieForm" class="needs-validation" novalidate>
    <div class="row no-gutters mb-4">
        <div class="col-12 col-md-8 rounded-left bg-light shadow-sm">
            <div class="px-3 pt-3 pb-4">
                <h3 class="border-bottom mb-3"><?php _ex('Content Blocker Settings', 'Backend / Content Blocker / Headline', 'borlabs-cookie'); ?></h3>
                <div class="row">
                    <div class="col-12">

                        <div class="form-group row">
                            <label for="contentBlockerId" class="col-sm-4 col-form-label"><?php _ex('ID', 'Backend / Content Blocker / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="contentBlockerId" name="contentBlockerId" value="<?php echo $inputContentBlockerId; ?>" <?php echo empty($contentBlockerData->id) ? 'required' : 'disabled'; ?> pattern="[a-z-_]{3,}">
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('<strong>ID</strong> must be set. The <strong>ID</strong> must be at least 3 characters long and may only contain: <strong><em>a-z - _</em></strong>', 'Backend / Content Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                                <div class="invalid-feedback"><?php _ex('Invalid <strong>ID</strong> name. Only use <strong><em>a-z - _</em></strong>', 'Backend / Global / Validation Message', 'borlabs-cookie'); ?></div>
                            </div>
                        </div>

                        <?php
                        if (!empty($contentBlockerData->id)) {
                        ?>
                        <div class="form-group row align-items-center">
                            <label for="shortcode" class="col-sm-4 col-form-label"><?php _ex('Shortcode', 'Backend / Content Blocker / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <input id="shortcode" type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" value="[borlabs-cookie id=&quot;<?php echo $inputContentBlockerId; ?>&quot; type=&quot;content-blocker&quot;]<?php _ex('...block this...', 'Backend / Content Blocker / Input Placeholder', 'borlabs-cookie'); ?>[/borlabs-cookie]" disabled>
                                <span data-clipboard="shortcode" data-toggle="tooltip" title="<?php echo esc_attr_x('Click to copy to clipboard.', 'Backend / Global / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-clone"></i></span>
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('Use this shortcode if automatic detection does not work.', 'Backend / Content Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>
                        <?php
                        }
                        ?>

                        <?php
                        if (!empty($contentBlockerData->id) && !empty($languageFlag)) {
                        ?>
                        <div class="form-group row align-items-center">
                            <label class="col-sm-4 col-form-label"><?php _ex('Language', 'Backend / Content Blocker / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <img src="<?php echo $languageFlag; ?>" alt=""> <?php echo $languageName; ?>
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('Your entry is stored for this language.', 'Backend / Content Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>
                        <?php
                        }
                        ?>

                        <div class="form-group row align-items-center">
                            <label class="col-sm-4 col-form-label"><?php _ex('Status', 'Backend / Content Blocker / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <button type="button" class="btn btn-sm btn-toggle mr-2<?php echo $switchStatus; ?>" data-toggle="button" data-switch-target="status" aria-pressed="<?php echo $inputStatus ? 'true' : 'false'; ?>" autocomplete="off"><span class="handle"></span></button>
                                <input type="hidden" name="status" id="status" value="<?php echo $inputStatus; ?>">
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('The status of this <strong>Content Blocker</strong>. If active (Status: ON) it does block content (iframes) of the configured hosts.', 'Backend / Content Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-sm-4 col-form-label"><?php _ex('Name', 'Backend / Content Blocker / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="name" name="name" value="<?php echo $inputName; ?>" required>
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('The name of the <strong>Content Blocker</strong> which can be used within the <strong>Preview Blocked Content</strong> code by using the variable <strong><em>%%name%%</em></strong>.', 'Backend / Content Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                                <div class="invalid-feedback"><?php _ex('This is a required field and cannot be empty.', 'Backend / Global / Validation Message', 'borlabs-cookie'); ?></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="privacyPolicyURL" class="col-sm-4 col-form-label"><?php _ex('Privacy Policy URL', 'Backend / Content Blocker / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <input type="url" class="form-control form-control-sm d-inline-block w-75 mr-2" id="privacyPolicyURL" name="privacyPolicyURL" value="<?php echo $inputPrivacyPolicyURL; ?>">
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('The URL of the <strong>Content Blocker</strong> which can be used within the <strong>Preview Blocked Content</strong> code by using the variable <strong><em>%%privacy_policy_url%%</em></strong>.', 'Backend / Content Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="hosts" class="col-sm-4 col-form-label"><?php _ex('Host(s)', 'Backend / Content Blocker / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <textarea class="form-control d-inline-block align-top w-75 mr-2" name="hosts" id="hosts" rows="5" autocapitalize="off" autocomplete="off" autocorrect="off" spellcheck="false"><?php echo $textareaHosts; ?></textarea>
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('One host per line. When one host is recognized (for example within the src-attribute of an iframe) this <strong>Content Blocker</strong> is used.', 'Backend / Content Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-8 offset-sm-4">
                        <button type="submit" class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                        <a class="btn btn-secondary btn-sm" href="?page=borlabs-cookie-content-blocker"><?php _ex('Go back without saving', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4 rounded-right shadow-sm bg-tips text-light">
            <div class="px-3 pt-3 pb-3 mb-4">
                <h3 class="border-bottom mb-3"><?php _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>

                <div class="accordion" id="accordionContentBlockerSettings">

                    <button type="button" class="collapsed" data-toggle="collapse" data-target="#accordionContentBlockerSettingsOne" aria-expanded="true">
                        <?php _ex('Shortcode explained', 'Backend / Content Blocker / Tips / Headline', 'borlabs-cookie'); ?>
                    </button>

                    <div id="accordionContentBlockerSettingsOne" class="collapse show" data-parent="#accordionContentBlockerSettings">
                        <p><?php
                            printf(
                                _x('Use the shortcode to block content that is not automatically blocked. If contents use the oEmbed format, for example Facebook links, use the following alternate shortcode to avoid display errors and block the content. %s', 'Backend / Content Blocker / Tips / Text', 'borlabs-cookie'),
                                '<span class="code-example">[borlabs-cookie' . (!empty($inputContentBlockerId) ? ' id="' . $inputContentBlockerId . '"' : '') . ' type="content-blocker"]URL[/borlabs-cookie]</span>'
                            );
                            ?>
                        </p>
                    </div>

                    <button type="button" class="collapsed" data-toggle="collapse" data-target="#accordionContentBlockerSettingsTwo">
                        <?php _ex('How can I use cookie consent for automatic content unblocking?', 'Backend / Content Blocker / Tips / Headline', 'borlabs-cookie'); ?>
                    </button>

                    <div id="accordionContentBlockerSettingsTwo" class="collapse" data-parent="#accordionContentBlockerSettings">
                        <p>
                            <?php _ex('It is possible to create a link between <strong>Cookies</strong> and <strong>Content Blocker</strong>. This automatically removes the blocking of certain content if the visitor has given their consent to certain <strong>Cookies</strong>. This is already the case with all <strong>External Media Cookies</strong> provided.', 'Backend / Content Blocker / Tips / Text', 'borlabs-cookie'); ?>
                        </p>
                        <p><?php
                            printf(
                                _x('To create a link, <strong>Cookie</strong> and <strong>Content Blocker IDs</strong> must be identical. Insert this code into the JavaScript box of the <strong>Cookie</strong>: %s', 'Backend / Content Blocker / Tips / Text', 'borlabs-cookie'),
                                '<span class="code-example">&lt;script&gt;window.BorlabsCookie.unblockContentId("' . (!empty($inputContentBlockerId) ? $inputContentBlockerId : '...your ID...') . '");&lt;/script&gt;</span>'
                            );
                            ?>
                        </p>
                    </div>

                    <button type="button" class="collapsed" data-toggle="collapse" data-target="#accordionContentBlockerSettingsThree">
                        <?php _ex('What is a Host?', 'Backend / Content Blocker / Tips / Headline', 'borlabs-cookie'); ?>
                    </button>

                    <div id="accordionContentBlockerSettingsThree" class="collapse" data-parent="#accordionContentBlockerSettings">
                        <p>
                            <?php _ex('The host is a part of a URL, often the domain. For example, if the URL is <strong><em>https://www.example.com/index.html</em></strong> the host would be <strong><em>www.example.com</em></strong>.', 'Backend / Content Blocker / Tips / Text', 'borlabs-cookie'); ?>
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <div class="row no-gutters mb-4">
        <div class="col-12 col-md-8 rounded-left bg-light shadow-sm">
            <div class="px-3 pt-3 pb-4">
                <h3 class="border-bottom mb-3"><?php _ex('Additional Settings', 'Backend / Content Blocker / Headline', 'borlabs-cookie'); ?></h3>
                <div class="row">
                    <div class="col-12">

                        <div class="form-group row align-items-center">
                            <label class="col-sm-4 col-form-label"><?php _ex('Unblock all', 'Backend / Content Blocker / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <?php
                                if (!empty($contentBlockerData->content_blocker_id) && $contentBlockerData->content_blocker_id === 'default') {
                                ?>
                                    <div class="alert alert-info">
                                        <?php _ex('It is not possible to unblock all content of an unspecific content type.', 'Backend / Content Blocker / Alert Message', 'borlabs-cookie'); ?>
                                    </div>
                                <?php
                                } else {
                                    ?>
                                    <button type="button" class="btn btn-sm btn-toggle mr-2<?php echo $switchSettingsUnblockAll; ?>" data-toggle="button" data-switch-target="settingsUnblockAll" aria-pressed="<?php echo $inputSettingsUnblockAll ? 'true' : 'false'; ?>" autocomplete="off"><span class="handle"></span></button>
                                    <input type="hidden" name="settings[unblockAll]" id="settingsUnblockAll" value="<?php echo $inputSettingsUnblockAll; ?>">
                                    <span data-toggle="tooltip" title="<?php echo esc_attr_x('Once the visitor unblocks content blocked by this <strong>Content Blocker</strong>, all content blocked by it will be automatically unblocked too. Please see the <strong>Tips</strong> section on how to link this setting with a <strong>Cookie</strong>.', 'Backend / Content Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                                <?php
                                }
                                ?>
                            </div>
                        </div>

                        <?php
                        if (!empty($contentBlockerData->content_blocker_id)) {
                            do_action('borlabsCookie/contentBlocker/edit/template/settings/'.$contentBlockerData->content_blocker_id, $contentBlockerData);
                        }
                        ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-8 offset-sm-4">
                        <button type="submit" class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                        <a class="btn btn-secondary btn-sm" href="?page=borlabs-cookie-content-blocker"><?php _ex('Go back without saving', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></a>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if (!empty($contentBlockerData->content_blocker_id)) {
            do_action('borlabsCookie/contentBlocker/edit/template/settings/help/'.$contentBlockerData->content_blocker_id, $contentBlockerData);
        }
        ?>
    </div>

   <div class="row no-gutters mb-4">
        <div class="col-12 col-md-8 rounded-left bg-light shadow-sm">
            <div class="px-3 pt-3 pb-4">
                <h3 class="border-bottom mb-3"><?php _ex('Preview Blocked Content', 'Backend / Content Blocker / Headline', 'borlabs-cookie'); ?></h3>
                <div class="row">
                    <div class="col-12">

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="previewHTML"><?php _ex('HTML', 'Backend / Content Blocker / Label', 'borlabs-cookie'); ?> <span data-toggle="tooltip" title="<?php echo esc_attr_x('This HTML will be used as the preview of the blocked content. You can use the variable <strong><em>%%name%%</em></strong> to display the name of the <strong>Content Blocker</strong>. You can also use the variable <strong><em>%%privacy_policy_url%%</em></strong> to display the <strong>Privacy Policy URL</strong> of the <strong>Content Blocker</strong>.', 'Backend / Content Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span></label>
                                <div class="code-editor"><textarea data-borlabs-html-editor name="previewHTML" id="previewHTML" rows="5"><?php echo $textareaPreviewHTML; ?></textarea></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="previewCSS"><?php _ex('CSS', 'Backend / Content Blocker / Label', 'borlabs-cookie'); ?> <span data-toggle="tooltip" title="<?php echo esc_attr_x('This CSS will be used for the preview of the blocked content.', 'Backend / Content Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span></label>
                                <div class="code-editor"><textarea data-borlabs-css-editor name="previewCSS" id="previewCSS" rows="5"><?php echo $textareaPreviewCSS; ?></textarea></div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-8 offset-sm-4">
                        <button type="submit" class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                        <a class="btn btn-secondary btn-sm" href="?page=borlabs-cookie-content-blocker"><?php _ex('Go back without saving', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="row no-gutters mb-4">
        <div class="col-12 col-md-8 rounded-left bg-light shadow-sm">
            <div class="px-3 pt-3 pb-4">
                <h3 class="border-bottom mb-3"><?php _ex('JavaScript', 'Backend / Content Blocker / Headline', 'borlabs-cookie'); ?></h3>
                <div class="row">
                    <div class="col-12">

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="globalJS"><?php _ex('Global', 'Backend / Content Blocker / Label', 'borlabs-cookie'); ?> <span data-toggle="tooltip" title="<?php echo esc_attr_x('Only use JavaScript, do not use <strong><em>&amp;lt;script&amp;gt;</em></strong>-tags!', 'Backend / Content Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span></label>
                                <div class="code-editor"><textarea data-borlabs-js-editor name="globalJS" id="globalJS" rows="5"><?php echo $textareaGlobalJS; ?></textarea></div>
                            </div>
                        </div>

                        <div class="form-group row align-items-center">
                            <label class="col-sm-4 col-form-label"><?php _ex('Execute Global code first', 'Backend / Content Blocker / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <button type="button" class="btn btn-sm btn-toggle mr-2<?php echo $switchSettingsExecuteGlobalCodeBeforeUnblocking; ?>" data-toggle="button" data-switch-target="executeGlobalCodeBeforeUnblocking" aria-pressed="<?php echo $inputSettingsExecuteGlobalCodeBeforeUnblocking ? 'true' : 'false'; ?>" autocomplete="off"><span class="handle"></span></button>
                                <input type="hidden" name="settings[executeGlobalCodeBeforeUnblocking]" id="executeGlobalCodeBeforeUnblocking" value="<?php echo $inputSettingsExecuteGlobalCodeBeforeUnblocking; ?>">
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('If this option is enabled (Status: ON) and a visitor unblocks the content, the JavaScript in the <strong>Global</strong> field will be executed before the blocked content is loaded.', 'Backend / Content Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="initJS"><?php _ex('Initialization', 'Backend / Content Blocker / Label', 'borlabs-cookie'); ?> <span data-toggle="tooltip" title="<?php echo esc_attr_x('Only use JavaScript, do not use <strong><em>&amp;lt;script&amp;gt;</em></strong>-tags!', 'Backend / Content Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span></label>
                                <div class="code-editor"><textarea data-borlabs-js-editor name="initJS" id="initJS" rows="5"><?php echo $textareaInitJS; ?></textarea></div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-8 offset-sm-4">
                        <?php wp_nonce_field('borlabs_cookie_content_blocker_save'); ?>
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="id" value="<?php echo $inputId; ?>">
                        <button type="submit" class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                        <a class="btn btn-secondary btn-sm" href="?page=borlabs-cookie-content-blocker"><?php _ex('Go back without saving', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4 rounded-right shadow-sm bg-tips text-light">
            <div class="px-3 pt-3 pb-3 mb-4">
                <h3 class="border-bottom mb-3"><?php _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>

                <div class="accordion" id="accordionContentBlockerJavaScript">

                    <button type="button" class="collapsed" data-toggle="collapse" data-target="#accordionContentBlockerJavaScriptOne" aria-expanded="true">
                        <?php _ex('Global JavaScript', 'Backend / Content Blocker / Tips / Headline', 'borlabs-cookie'); ?>
                    </button>

                    <div id="accordionContentBlockerJavaScriptOne" class="collapse show" data-parent="#accordionContentBlockerJavaScript">
                        <p>
                            <?php _ex('JavaScript stored in the <strong>Global</strong> field is executed once a blocked content is unblocked by the visitor (meaning only once per page). Use this, for example, to load an external JavaScript library.', 'Backend / Content Blocker / Tips / Text', 'borlabs-cookie'); ?>
                        </p>
                        <p>
                            <?php _ex('To execute the JavaScript stored in the <strong>Global</strong> field before the blocked content is loaded, activate the option <strong>Execute Global code first</strong>.', 'Backend / Content Blocker / Tips / Text', 'borlabs-cookie'); ?>
                        </p>
                        <p>
                            <?php _ex('If this option is enabled and a visitor unblocks the content, the JavaScript from the <strong>Global</strong> field will be executed before the blocked content is loaded.', 'Backend / Content Blocker / Tips / Text', 'borlabs-cookie'); ?>
                        </p>
                        <p><span class="code-example"><?php _ex('function (contentBlockerData) { /* Here is your global code */ }', 'Backend / Content Blocker / Tips / Code Example', 'borlabs-cookie'); ?></span></p>
                    </div>

                    <button type="button" class="collapsed" data-toggle="collapse" data-target="#accordionContentBlockerJavaScriptTwo">
                        <?php _ex('Initialization JavaScript', 'Backend / Content Blocker / Tips / Headline', 'borlabs-cookie'); ?>
                    </button>

                    <div id="accordionContentBlockerJavaScriptTwo" class="collapse" data-parent="#accordionContentBlockerJavaScript">
                        <p>
                            <?php _ex('JavaScript stored in the <strong>Initialization</strong> field is executed with every unblock of blocked content (meaning as many times as the visitor unblocks content). It is executed after the JavaScript from the <strong>Global</strong> field.', 'Backend / Content Blocker / Tips / Text', 'borlabs-cookie'); ?>
                        </p>

                        <h4><?php _ex('For Developers', 'Backend / Content Blocker / Tips / Headline', 'borlabs-cookie'); ?></h4>
                        <p>
                            <?php _ex('The code is executed in a function that uses the variable <strong><em>el</em></strong> as a parameter. <strong><em>el</em></strong> contains the unlocked object.', 'Backend / Content Blocker / Tips / Text', 'borlabs-cookie'); ?>
                        </p>
                        <p><span class="code-example"><?php _ex('function (el, contentBlockerData) { /* Here is your initialization code */ }', 'Backend / Content Blocker / Tips / Code Example', 'borlabs-cookie'); ?></span></p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</form>
<?php
} else {
    echo \BorlabsCookie\Cookie\Backend\License::getInstance()->getLicenseMessageActivateKey();
}
?>
