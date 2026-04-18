<?php
if (\BorlabsCookie\Cookie\Backend\License::getInstance()->isPluginUnlocked()) {
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?page=borlabs-cookie">Borlabs Cookie</a></li>
        <li class="breadcrumb-item"><a href="?page=borlabs-cookie-cookies"><?php _ex('Cookies', 'Backend / Cookies / Breadcrumb', 'borlabs-cookie'); ?></a></li>
        <?php
        if (!empty($cookieData->id)) {
            ?><li class="breadcrumb-item active" aria-current="page"><?php echo sprintf(_x('Edit: %s', 'Backend / Cookies / Breadcrumb', 'borlabs-cookie'), $inputName); ?></li><?php
        } else {
            ?>
            <li class="breadcrumb-item"><a href="?page=borlabs-cookie-cookies&action=cookieServices&id=<?php echo $cookieGroupData->id; ?>"><?php _ex('Step 1: Select a Service', 'Backend / Cookies / Breadcrumb', 'borlabs-cookie'); ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo _x('Step 2: Setup', 'Backend / Cookies / Breadcrumb', 'borlabs-cookie'); ?></li>
            <?php
        }
        ?>
    </ol>
</nav>

<?php echo \BorlabsCookie\Cookie\Backend\Messages::getInstance()->getAll(); ?>

<form action="?page=borlabs-cookie-cookies" method="post" id="BorlabsCookieForm" class="needs-validation" novalidate>
    <div class="row no-gutters mb-4">
        <div class="col-12 col-md-8 rounded-left bg-light shadow-sm">
            <div class="px-3 pt-3 pb-4">
                <h3 class="border-bottom mb-3"><?php _ex('Cookie Settings', 'Backend / Cookies / Headline', 'borlabs-cookie'); ?></h3>
                <div class="row">
                    <div class="col-12">

                        <div class="form-group row">
                            <label for="cookieId" class="col-sm-4 col-form-label"><?php _ex('ID', 'Backend / Cookies / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="cookieId" name="cookieId" value="<?php echo $inputCookieId; ?>" <?php echo empty($cookieData->id) ? 'required' : 'disabled'; ?> pattern="[a-z-_]{3,}">
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('<strong>ID</strong> must be set. The <strong>ID</strong> must be at least 3 characters long and may only contain: <strong><em>a-z - _</em></strong>', 'Backend / Cookies / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                                <div class="invalid-feedback"><?php _ex('Invalid <strong>ID</strong> name. Only use <strong><em>a-z - _</em></strong>', 'Backend / Global / Validation Message', 'borlabs-cookie'); ?></div>
                            </div>
                        </div>

                        <div class="form-group row align-items-center">
                            <label class="col-sm-4 col-form-label"><?php _ex('Cookie Group', 'Backend / Cookies / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <?php echo esc_html($cookieGroupData->name); ?>
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('The <strong>Cookie Group</strong> the <strong>Cookie</strong> is part of.', 'Backend / Cookies / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>

                        <?php
                        if (!empty($cookieData->id)) {
                        ?>
                        <div class="form-group row align-items-center">
                            <label for="shortcode" class="col-sm-4 col-form-label"><?php _ex('Shortcode', 'Backend / Cookies / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <input id="shortcode" type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" value="[borlabs-cookie id=&quot;<?php echo $inputCookieId; ?>&quot; type=&quot;cookie&quot;]<?php _ex('...block this...', 'Backend / Cookies / Input Placeholder', 'borlabs-cookie'); ?>[/borlabs-cookie]" disabled>
                                <span data-clipboard="shortcode" data-toggle="tooltip" title="<?php echo esc_attr_x('Click to copy to clipboard.', 'Backend / Global / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-clone"></i></span>
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('Use this shortcode to unblock JavaScript or content when user opted-in for this <strong>Cookie</strong>.', 'Backend / Cookies / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>
                        <?php
                        }
                        ?>

                        <?php
                        if (!empty($cookieData->id) && empty($cookieData->undeletable)) {
                        ?>
                        <div class="form-group row align-items-center">
                            <label for="opt-out-shortcode" class="col-sm-4 col-form-label"><?php _ex('Opt-out Shortcode', 'Backend / Cookies / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <input id="opt-out-shortcode" type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" value="[borlabs-cookie id=&quot;<?php echo $inputCookieId; ?>&quot; type=&quot;btn-switch-consent&quot;/]" disabled>
                                <span data-clipboard="opt-out-shortcode" data-toggle="tooltip" title="<?php echo esc_attr_x('Click to copy to clipboard.', 'Backend / Global / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-clone"></i></span>
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('Use this shortcode to display an opt-out option for this <strong>Cookie</strong>.', 'Backend / Cookies / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>
                        <?php
                        }
                        ?>

                        <?php
                        if (!empty($cookieData->id) && !empty($languageFlag)) {
                        ?>
                        <div class="form-group row align-items-center">
                            <label class="col-sm-4 col-form-label"><?php _ex('Language', 'Backend / Cookies / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <img src="<?php echo $languageFlag; ?>" alt=""> <?php echo $languageName; ?>
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('Your entry is stored for this language.', 'Backend / Cookies / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>
                        <?php
                        }
                        ?>

                        <?php
                        if (empty($cookieData->id) || (!empty($cookieData->id) && empty($cookieData->undeletable))) {
                        ?>
                        <div class="form-group row align-items-center">
                            <label class="col-sm-4 col-form-label"><?php _ex('Status', 'Backend / Cookies / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <button type="button" class="btn btn-sm btn-toggle mr-2<?php echo $switchStatus; ?>" data-toggle="button" data-switch-target="status" aria-pressed="<?php echo $inputStatus ? 'true' : 'false'; ?>" autocomplete="off"><span class="handle"></span></button>
                                <input type="hidden" name="status" id="status" value="<?php echo $inputStatus; ?>">
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('The status of this <strong>Cookie</strong>. If active (Status: ON) it is displayed to the visitor in the <strong>Cookie Box</strong>.', 'Backend / Cookies / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>
                        <?php
                        } else {
                            ?>
                            <input type="hidden" name="status" id="status" value="1">
                            <?php
                        }
                        ?>

                        <div class="form-group row">
                            <label for="position" class="col-sm-4 col-form-label"><?php _ex('Position', 'Backend / Cookies / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control form-control-sm d-inline-block w-75 mr-2" id="position" name="position" value="<?php echo $inputPosition; ?>" min="1" step="1">
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('Determine the position where this <strong>Cookie</strong> is displayed in its <strong>Cookie Group</strong>. Order follows natural numbers.', 'Backend / Cookies / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-8 offset-sm-4">
                        <button type="submit" class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                        <a class="btn btn-secondary btn-sm" href="?page=borlabs-cookie-cookies"><?php _ex('Go back without saving', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4 rounded-right shadow-sm bg-tips text-light">
            <div class="px-3 pt-3 pb-3 mb-4">
                <h3 class="border-bottom mb-3"><?php _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>
                <h4><?php _ex('Shortcode explained', 'Backend / Cookies / Tips / Headline', 'borlabs-cookie'); ?></h4>
                <p><?php _ex('The shortcode can be used to execute custom code associated with this type of <strong>Cookie</strong> once the visitor has given consent to the <strong>Cookie</strong>. This can be used, for example, to block a conversion pixel code.', 'Backend / Cookies / Tips / Text', 'borlabs-cookie'); ?> </p>
                <p><?php _ex('The shortcode for example can be used in the <strong>Meta Box</strong> of Borlabs Cookie. You can find it for example in <strong>Posts &gt; <em>Your Post</em> &gt; Borlabs Cookie &gt; Custom Code</strong>.', 'Backend / Cookies / Tips / Text', 'borlabs-cookie'); ?> </p>
            </div>
        </div>
    </div>

    <div class="row no-gutters mb-4">
        <div class="col-12 col-md-8 rounded-left bg-light shadow-sm">
            <div class="px-3 pt-3 pb-4">
                <h3 class="border-bottom mb-3"><?php _ex('Cookie Information', 'Backend / Cookies / Headline', 'borlabs-cookie'); ?></h3>
                <div class="row">
                    <div class="col-12">

                        <div class="form-group row">
                            <label for="name" class="col-sm-4 col-form-label"><?php _ex('Name', 'Backend / Cookies / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="name" name="name" value="<?php echo $inputName; ?>" required>
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('Insert a name for this <strong>Cookie</strong>.', 'Backend / Cookies / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                                <div class="invalid-feedback"><?php _ex('This is a required field and cannot be empty.', 'Backend / Global / Validation Message', 'borlabs-cookie'); ?></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="provider" class="col-sm-4 col-form-label"><?php _ex('Provider', 'Backend / Cookies / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="provider" name="provider" value="<?php echo $inputProvider; ?>" required>
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('Insert the provider of this <strong>Cookie</strong>.', 'Backend / Cookies / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                                <div class="invalid-feedback"><?php _ex('This is a required field and cannot be empty.', 'Backend / Global / Validation Message', 'borlabs-cookie'); ?></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="purpose" class="col-sm-4 col-form-label"><?php _ex('Purpose', 'Backend / Cookies / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <textarea class="form-control d-inline-block align-top w-75 mr-2" name="purpose" id="purpose" rows="5"><?php echo $textareaPurpose; ?></textarea>
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('Explain the purpose of this <strong>Cookie</strong> to your visitors.', 'Backend / Cookies / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="privacyPolicyURL" class="col-sm-4 col-form-label"><?php _ex('Privacy Policy URL', 'Backend / Cookies / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <input type="url" class="form-control form-control-sm d-inline-block w-75 mr-2" id="privacyPolicyURL" name="privacyPolicyURL" value="<?php echo $inputPrivacyPolicyURL; ?>">
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('Provide a URL to the privacy policy of the provider of the <strong>Cookie</strong>.', 'Backend / Cookies / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="hosts" class="col-sm-4 col-form-label"><?php _ex('Host(s)', 'Backend / Cookies / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <textarea class="form-control d-inline-block align-top w-75 mr-2" name="hosts" id="hosts" rows="5" autocapitalize="off" autocomplete="off" autocorrect="off" spellcheck="false"><?php echo $textareaHosts; ?></textarea>
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('Insert the host(s) of this <strong>Cookie</strong>.', 'Backend / Cookies / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="cookieName" class="col-sm-4 col-form-label"><?php _ex('Cookie Name', 'Backend / Cookies / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="cookieName" name="cookieName" value="<?php echo $inputCookieName; ?>">
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('Provide the technical name of the <strong>Cookie</strong>. Multiple entries possible.', 'Backend / Cookies / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                                <div class="invalid-feedback"><?php _ex('This is a required field and cannot be empty.', 'Backend / Global / Validation Message', 'borlabs-cookie'); ?></div>
                            </div>
                        </div>

                        <?php
                        if (empty($cookieData->id) || (!empty($cookieData->cookie_id) && $cookieData->cookie_id !== 'borlabs-cookie')) {
                        ?>
                        <div class="form-group row align-items-center">
                            <label class="col-sm-4 col-form-label"><?php _ex('Block Cookies before Consent', 'Backend / Cookies / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <button type="button" class="btn btn-sm btn-toggle mr-2<?php echo $switchBlockCookiesBeforeConsent; ?>" data-toggle="button" data-switch-target="blockCookiesBeforeConsent" aria-pressed="<?php echo $inputBlockCookiesBeforeConsent ? 'true' : 'false'; ?>" autocomplete="off"><span class="handle"></span></button>
                                <input type="hidden" name="settings[blockCookiesBeforeConsent]" id="blockCookiesBeforeConsent" value="<?php echo $inputBlockCookiesBeforeConsent; ?>">
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('If active (Status: ON) Borlabs Cookie tries to block cookies with the names from <strong>Cookie Name</strong> until consent is given.', 'Backend / Cookies / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>

                        <?php
                            if ($inputBlockCookiesBeforeConsent) {
                            ?>
                            <div class="form-group row align-items-center">
                                <div class="col-sm-8 offset-4">
                                    <div class="alert alert-warning mt-2"><?php _ex('Read the information in the <strong>Tips</strong> section.', 'Backend / Cookies / Alert Message', 'borlabs-cookie'); ?></div>
                                </div>
                            </div>
                            <?php
                            }
                        ?>
                        <?php
                        }
                        ?>

                        <div class="form-group row">
                            <label for="cookieExpiry" class="col-sm-4 col-form-label"><?php _ex('Cookie Expiry', 'Backend / Cookies / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="cookieExpiry" name="cookieExpiry" value="<?php echo $inputCookieExpiry; ?>">
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('Provide the expiry date of the <strong>Cookie</strong>.', 'Backend / Cookies / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                                <div class="invalid-feedback"><?php _ex('This is a required field and cannot be empty.', 'Backend / Global / Validation Message', 'borlabs-cookie'); ?></div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-8 offset-sm-4">
                        <button type="submit" class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                        <a class="btn btn-secondary btn-sm" href="?page=borlabs-cookie-cookies"><?php _ex('Go back without saving', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4 rounded-right shadow-sm bg-tips text-light">
            <div class="px-3 pt-3 pb-3 mb-4">
                <h3 class="border-bottom mb-3"><?php _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>
                <h4><?php _ex('Cookie Information', 'Backend / Cookies / Tips / Headline', 'borlabs-cookie'); ?></h4>
                <p><?php _ex('The information stored here has no technical effects and serves exclusively to inform the visitor, unless you activate <strong>Block Cookies before Consent</strong>.', 'Backend / Cookies / Tips / Text', 'borlabs-cookie'); ?> </p>
                <p><?php _ex('The information of the <strong>Services</strong> provided by us may not be complete and should be checked before use.', 'Backend / Cookies / Tips / Text', 'borlabs-cookie'); ?> </p>
                <h4><?php _ex('What is a Host?', 'Backend / Cookies / Tips / Headline', 'borlabs-cookie'); ?></h4>
                <p><?php _ex('The host is a part of a URL, often the domain. For example, if the URL is <strong><em>https://www.example.com/index.html</em></strong> the host would be <strong><em>www.example.com</em></strong>.', 'Backend / Cookies / Tips / Text', 'borlabs-cookie'); ?> </p>
                <h4><?php _ex('Block Cookies before Consent', 'Backend / Cookies / Tips / Headline', 'borlabs-cookie'); ?></h4>
                <p><?php _ex('If you activate this option Borlabs Cookie searches and deletes cookies that match the <strong>Cookie Name</strong>. You can also use <strong>*</strong> to search for multiple names, e.g. if you enter the Name <strong><em>example_*</em></strong> Borlabs Cookie will search and delete the cookies <strong><em>example_abc</em></strong> and <strong><em>example_xyz</em></strong>. Separate multiple cookie names with a comma.', 'Backend / Cookies / Tips / Text', 'borlabs-cookie'); ?> </p>
                <p><?php _ex('This function is mainly intended to delete session cookies set by third-party plugins via PHP. JavaScript cookies can also be deleted, but only if they belong to the same domain.', 'Backend / Cookies / Tips / Text', 'borlabs-cookie'); ?> </p>
                <p><?php _ex('Example: if the website runs under <strong><em>www.example.com</em></strong>, no cookie created for <strong><em>example.com</em></strong> can be deleted.', 'Backend / Cookies / Tips / Text', 'borlabs-cookie'); ?> </p>
                <p><?php _ex('Named cookies will be only set after consent by the visitor was given and the website was reloaded.', 'Backend / Cookies / Tips / Text', 'borlabs-cookie'); ?> </p>
            </div>
        </div>
    </div>

    <div class="row no-gutters mb-4">
        <div class="col-12 col-md-8 rounded-left bg-light shadow-sm">
            <div class="px-3 pt-3 pb-4">
                <h3 class="border-bottom mb-3"><?php _ex('Additional Settings', 'Backend / Cookies / Headline', 'borlabs-cookie'); ?></h3>
                <div class="row">
                    <div class="col-12">

                        <div class="form-group row align-items-center">
                            <label class="col-sm-4 col-form-label"><?php _ex('Prioritize', 'Backend / Cookies / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <button type="button" class="btn btn-sm btn-toggle mr-2<?php echo $switchSettingsPrioritize; ?>" data-toggle="button" data-switch-target="settingsPrioritize" aria-pressed="<?php echo $inputSettingsPrioritize ? 'true' : 'false'; ?>" autocomplete="off"><span class="handle"></span></button>
                                <input type="hidden" name="settings[prioritize]" id="settingsPrioritize" value="<?php echo $inputSettingsPrioritize; ?>">
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('The <strong>Opt-in Code</strong> is loaded in &amp;lt;head&amp;gt; and is executed before the page is fully loaded.', 'Backend / Cookies / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>

                        <div class="form-group row align-items-center">
                            <label class="col-sm-4 col-form-label"><?php _ex('Asynchronous Opt-Out Code', 'Backend / Cookies / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <button type="button" class="btn btn-sm btn-toggle mr-2<?php echo $switchSettingsAsyncOptOutCode; ?>" data-toggle="button" data-switch-target="settingsAsyncOptOutCode" aria-pressed="<?php echo $inputSettingsAsyncOptOutCode ? 'true' : 'false'; ?>" autocomplete="off"><span class="handle"></span></button>
                                <input type="hidden" name="settings[asyncOptOutCode]" id="settingsAsyncOptOutCode" value="<?php echo $inputSettingsAsyncOptOutCode; ?>">
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('The <strong>Opt-Out Code</strong> contains asynchronous JavaScript code that needs to executed to finish the Opt-Out.', 'Backend / Cookies / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>

                        <?php
                        if (!empty($cookieData->service) && has_action('borlabsCookie/cookie/edit/template/settings/'.$cookieData->service)) {
                            do_action('borlabsCookie/cookie/edit/template/settings/'.$cookieData->service, $cookieData);
                        }
                        ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-8 offset-sm-4">
                        <button type="submit" class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                        <a class="btn btn-secondary btn-sm" href="?page=borlabs-cookie-cookies"><?php _ex('Go back without saving', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></a>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if (!empty($cookieData->service) && has_action('borlabsCookie/cookie/edit/template/settings/'.$cookieData->service)) {
            do_action('borlabsCookie/cookie/edit/template/settings/help/'.$cookieData->service, $cookieData);
        }
        ?>
    </div>

    <div class="row no-gutters mb-4">
        <div class="col-12 col-md-8 rounded bg-light shadow-sm">
            <div class="px-3 pt-3 pb-4">
                <h3 class="border-bottom mb-3"><?php _ex('HTML &amp; JavaScript', 'Backend / Cookies / Headline', 'borlabs-cookie'); ?></h3>
                <div class="row">
                    <div class="col-12">

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="optInJS"><?php _ex('Opt-in Code', 'Backend / Cookies / Label', 'borlabs-cookie'); ?> <span data-toggle="tooltip" title="<?php echo esc_attr_x('This code will be executed after the visitor gives their consent.', 'Backend / Cookies / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span></label>
                                <div class="code-editor"><textarea data-borlabs-html-editor name="optInJS" id="optInJS" rows="5"><?php echo $textareaOptInJS; ?></textarea></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="optOutJS"><?php _ex('Opt-out Code', 'Backend / Cookies / Label', 'borlabs-cookie'); ?> <span data-toggle="tooltip" title="<?php echo esc_attr_x('This code will be executed only if the visitor did opt-in previously and chooses to opt-out. It is executed once.', 'Backend / Cookies / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span></label>
                                <div class="code-editor"><textarea data-borlabs-html-editor name="optOutJS" id="optOutJS" rows="5"><?php echo $textareaOptOutJS; ?></textarea></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="fallbackJS"><?php _ex('Fallback Code', 'Backend / Cookies / Label', 'borlabs-cookie'); ?> <span data-toggle="tooltip" title="<?php echo esc_attr_x('This code will always be executed.', 'Backend / Cookies / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span></label>
                                <div class="code-editor"><textarea data-borlabs-html-editor name="fallbackJS" id="fallbackJS" rows="5"><?php echo $textareaFallbackJS; ?></textarea></div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-8 offset-sm-4">
                        <?php wp_nonce_field('borlabs_cookie_cookies_save'); ?>
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="cookieGroupId" value="<?php echo $inputCookieGroupId; ?>">
                        <input type="hidden" name="service" value="<?php echo $inputService; ?>">
                        <input type="hidden" name="id" value="<?php echo $inputId; ?>">
                        <button type="submit" class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                        <a class="btn btn-secondary btn-sm" href="?page=borlabs-cookie-cookies"><?php _ex('Go back without saving', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></a>
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
