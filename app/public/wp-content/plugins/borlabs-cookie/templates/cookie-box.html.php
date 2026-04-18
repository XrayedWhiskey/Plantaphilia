<?php
if (\BorlabsCookie\Cookie\Backend\License::getInstance()->isPluginUnlocked()) {
    ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?page=borlabs-cookie">Borlabs Cookie</a></li>
            <li class="breadcrumb-item active"
                aria-current="page"><?php _ex('Cookie Box', 'Backend / Cookie Box / Breadcrumb', 'borlabs-cookie'); ?></li>
        </ol>
    </nav>

    <?php echo \BorlabsCookie\Cookie\Backend\Messages::getInstance()->getAll(); ?>

    <form action="?page=borlabs-cookie-cookie-box" method="post" id="BorlabsCookieForm" class="needs-validation"
          novalidate>
        <div class="row no-gutters mb-4">
            <div class="col-12 col-md-8 rounded-left bg-light shadow-sm">
                <div class="px-3 pt-3 pb-4">
                    <h3 class="border-bottom mb-3"><?php _ex('General Settings', 'Backend / Cookie Box / Headline', 'borlabs-cookie'); ?></h3>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row align-items-center">
                                <label for="showCookieBox"
                                       class="col-sm-4 col-form-label"><?php _ex('Show Cookie Box', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <button type="button"
                                            class="btn btn-sm btn-toggle mr-2<?php echo $switchShowCookieBox; ?>"
                                            data-toggle="button" data-switch-target="showCookieBox"
                                            aria-pressed="<?php echo $inputShowCookieBox ? 'true' : 'false'; ?>">
                                        <span class="handle"></span>
                                    </button>
                                    <input type="hidden" id="showCookieBox" name="showCookieBox"
                                           value="<?php echo $inputShowCookieBox; ?>">
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('If activated the <strong>Cookie Box</strong> is shown, in which the visitor can choose their cookie preferences.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="showCookieBoxOnLoginPage"
                                       class="col-sm-4 col-form-label"><?php _ex('Show Cookie Box on Login Page', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <button type="button"
                                            class="btn btn-sm btn-toggle mr-2<?php echo $switchShowCookieBoxOnLoginPage; ?>"
                                            data-toggle="button" data-switch-target="showCookieBoxOnLoginPage"
                                            aria-pressed="<?php echo $inputShowCookieBoxOnLoginPage ? 'true' : 'false'; ?>"
                                            autocomplete="off">
                                        <span class="handle"></span>
                                    </button>
                                    <input type="hidden" id="showCookieBoxOnLoginPage" name="showCookieBoxOnLoginPage"
                                           value="<?php echo $inputShowCookieBoxOnLoginPage; ?>">
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('If activated the <strong>Cookie Box</strong> is shown on the login page. The option <strong>Show Cookie Box</strong> must be active.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="cookieBoxIntegration"
                                       class="col-sm-4 col-form-label"><?php _ex('Integration', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <select class="form-control form-control form-control-sm d-inline-block w-75 mr-2"
                                            id="cookieBoxIntegration" name="cookieBoxIntegration">
                                        <option<?php echo $optionCookieBoxIntegrationHTML; ?>
                                            value="html"><?php _ex('HTML', 'Backend / Cookie Box / Select Option', 'borlabs-cookie'); ?></option>
                                        <option<?php echo $optionCookieBoxIntegrationJavaScript; ?>
                                            value="javascript"><?php _ex('JavaScript', 'Backend / Cookie Box / Select Option', 'borlabs-cookie'); ?></option>
                                    </select>
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('Choose the method of integration. We recommend using the <strong>JavaScript</strong> option.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="cookieBoxBlocksContent"
                                       class="col-sm-4 col-form-label"><?php _ex('Block Content', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <button type="button"
                                            class="btn btn-sm btn-toggle mr-2<?php echo $switchCookieBoxBlocksContent; ?>"
                                            data-toggle="button" data-switch-target="cookieBoxBlocksContent"
                                            aria-pressed="<?php echo $inputCookieBoxBlocksContent ? 'true' : 'false'; ?>"
                                            autocomplete="off">
                                        <span class="handle"></span>
                                    </button>
                                    <input type="hidden" id="cookieBoxBlocksContent" name="cookieBoxBlocksContent"
                                           value="<?php echo $inputCookieBoxBlocksContent; ?>">
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('The content below the <strong>Cookie Box</strong> will be inaccessible until the visitor has selected an option.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="cookieBoxManageOptionType"
                                       class="col-sm-4 col-form-label"><?php _ex('Individual Cookie Preferences Option - Cookie Box', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <select class="form-control form-control form-control-sm d-inline-block w-75 mr-2"
                                            id="cookieBoxManageOptionType" name="cookieBoxManageOptionType">
                                        <option<?php echo $optionCookieBoxManageOptionTypeButton; ?>
                                            value="button"><?php _ex('Button', 'Backend / Cookie Box / Select Option', 'borlabs-cookie'); ?></option>
                                        <option<?php echo $optionCookieBoxManageOptionTypeLink; ?>
                                            value="link"><?php _ex('Link', 'Backend / Cookie Box / Select Option', 'borlabs-cookie'); ?></option>
                                    </select>
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('Choose the display option of the <strong>Individual Cookie Preferences</strong> option.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="cookieBoxRefuseOptionType"
                                       class="col-sm-4 col-form-label"><?php _ex('Refuse Option - Cookie Box', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <select class="form-control form-control form-control-sm d-inline-block w-75 mr-2"
                                            id="cookieBoxRefuseOptionType" name="cookieBoxRefuseOptionType">
                                        <option<?php echo $optionCookieBoxRefuseOptionTypeButton; ?>
                                            value="button"><?php _ex('Button', 'Backend / Cookie Box / Select Option', 'borlabs-cookie'); ?></option>
                                        <option<?php echo $optionCookieBoxRefuseOptionTypeLink; ?>
                                            value="link"><?php _ex('Link', 'Backend / Cookie Box / Select Option', 'borlabs-cookie'); ?></option>
                                    </select>
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('Choose the display option of the <strong>Refuse</strong> option. We recommend using the <strong>Button</strong> option.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="cookieBoxPreferenceRefuseOptionType"
                                       class="col-sm-4 col-form-label"><?php _ex('Refuse Option - Cookie Preferences', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <select class="form-control form-control form-control-sm d-inline-block w-75 mr-2"
                                            id="cookieBoxPreferenceRefuseOptionType" name="cookieBoxPreferenceRefuseOptionType">
                                        <option<?php echo $optionCookieBoxPreferenceRefuseOptionTypeButton; ?>
                                            value="button"><?php _ex('Button', 'Backend / Cookie Box / Select Option', 'borlabs-cookie'); ?></option>
                                        <option<?php echo $optionCookieBoxPreferenceRefuseOptionTypeLink; ?>
                                            value="link"><?php _ex('Link', 'Backend / Cookie Box / Select Option', 'borlabs-cookie'); ?></option>
                                    </select>
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('Choose the display option of the <strong>Refuse</strong> option. We recommend using the <strong>Button</strong> option.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="cookieBoxHideRefuseOption"
                                       class="col-sm-4 col-form-label"><?php _ex('Hide Refuse Option', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <button type="button"
                                            class="btn btn-sm btn-toggle mr-2<?php echo $switchCookieBoxHideRefuseOption; ?>"
                                            data-toggle="button" data-switch-target="cookieBoxHideRefuseOption"
                                            aria-pressed="<?php echo $inputCookieBoxHideRefuseOption ? 'true' : 'false'; ?>"
                                            autocomplete="off">
                                        <span class="handle"></span>
                                    </button>
                                    <input type="hidden" id="cookieBoxHideRefuseOption" name="cookieBoxHideRefuseOption"
                                           value="<?php echo $inputCookieBoxHideRefuseOption; ?>">
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('If activated the <strong>Refuse Link</strong> will not be shown to the visitor.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <?php
                            if (
                                !empty($inputCookieBoxHideRefuseOption)
                                && (
                                    empty($inputCookieBoxShowAcceptAllButton)
                                    ||
                                    empty($inputCookieBoxIgnorePreSelectStatus)
                                    ||
                                    (empty($optionCookieBoxLayoutBarAdvanced) && empty($optionCookieBoxLayoutBoxAdvanced))
                                )
                            ) {
                                ?>
                                <div class="form-group row align-items-center">
                                    <div class="col-sm-8 offset-4">
                                        <div
                                            class="alert alert-danger mt-2"><?php _ex('Depending on applicable law, this option may not be allowed to be turned on.', 'Backend / Cookie Box / Alert Message', 'borlabs-cookie'); ?></div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>

                            <div class="form-group row">
                                <label for="privacyPageId"
                                       class="col-sm-4 col-form-label"><?php _ex('Privacy Page', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <?php
                                    $privacyPageSelect = wp_dropdown_pages([
                                        'name' => 'privacyPageId',
                                        'id' => 'privacyPageId',
                                        'class' => 'form-control form-control form-control-sm d-inline-block w-75 mr-2 mb-2',
                                        'option_none_value' => 0,
                                        'selected' => $privacyPageId,
                                        'show_option_none' => _x('-- Please select --', 'Backend / Global / Default Select Option', 'borlabs-cookie'),
                                        'sort_column' => 'post_title',
                                        'echo' => 0,
                                    ]);

                                    if (!empty($inputPrivacyPageCustomURL)) {
                                        $privacyPageSelect = str_replace('<select', '<select disabled', $privacyPageSelect);
                                    }

                                    echo $privacyPageSelect;
                                    ?>
                                    <span data-add-page-source="privacyPageId"
                                          data-add-page-target="hideCookieBoxOnPages" data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('Click to add page to <strong>Hide Cookie Box on Page</strong> list.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-eye-slash"></i></span>
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('Choose your privacy page or add a custom URL.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>

                                    <div class="input-group input-group-sm w-75">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <input type="checkbox" name="enablePrivacyPageCustomURL" value="1"
                                                   data-enable-target="privacyPageCustomURL"
                                                   data-disable-target="privacyPageId"<?php echo !empty($inputPrivacyPageCustomURL) ? ' checked' : ''; ?>>
                                        </span>
                                        </div>
                                        <input type="url" class="form-control form-control-sm d-inline-block"
                                               id="privacyPageCustomURL" name="privacyPageCustomURL"
                                               value="<?php echo $inputPrivacyPageCustomURL; ?>"
                                               placeholder="https://"<?php echo empty($inputPrivacyPageCustomURL) ? ' disabled' : ''; ?>>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="imprintPageId"
                                       class="col-sm-4 col-form-label"><?php _ex('Imprint Page', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <?php
                                    $imprintPageSelect = wp_dropdown_pages([
                                        'name' => 'imprintPageId',
                                        'id' => 'imprintPageId',
                                        'class' => 'form-control form-control form-control-sm d-inline-block w-75 mr-2 mb-2',
                                        'option_none_value' => 0,
                                        'selected' => $imprintPageId,
                                        'show_option_none' => _x('-- Please select --', 'Backend / Global / Default Select Option', 'borlabs-cookie'),
                                        'sort_column' => 'post_title',
                                        'echo' => 0,
                                    ]);

                                    if (!empty($inputImprintPageCustomURL)) {
                                        $imprintPageSelect = str_replace('<select', '<select disabled', $imprintPageSelect);
                                    }

                                    echo $imprintPageSelect;
                                    ?>
                                    <span data-add-page-source="imprintPageId"
                                          data-add-page-target="hideCookieBoxOnPages" data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('Click to add page to <strong>Hide Cookie Box on Page</strong> list.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-eye-slash"></i></span>
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('Choose your imprint page or add a custom URL.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>

                                    <div class="input-group input-group-sm w-75">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <input type="checkbox" name="enableImprintPageCustomURL" value="1"
                                                   data-enable-target="imprintPageCustomURL"
                                                   data-disable-target="imprintPageId"<?php echo !empty($inputImprintPageCustomURL) ? ' checked' : ''; ?>>
                                        </span>
                                        </div>
                                        <input type="url" class="form-control form-control-sm d-inline-block"
                                               id="imprintPageCustomURL" name="imprintPageCustomURL"
                                               value="<?php echo $inputImprintPageCustomURL; ?>"
                                               placeholder="https://"<?php echo empty($inputImprintPageCustomURL) ? ' disabled' : ''; ?>>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="hideCookieBoxOnPages"
                                       class="col-sm-4 col-form-label"><?php _ex('Hide Cookie Box on Page', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <textarea class="form-control d-inline-block align-top w-75 mr-2"
                                              id="hideCookieBoxOnPages" name="hideCookieBoxOnPages" rows="5"
                                              autocapitalize="off" autocomplete="off" autocorrect="off"
                                              spellcheck="false"><?php echo $textareaHideCookieBoxOnPages; ?></textarea>
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('Add one URL per line. The <strong>Cookie Box</strong> will not be shown on these pages.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="showCookieBox"
                                       class="col-sm-4 col-form-label"><?php _ex('Support Borlabs Cookie', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <button type="button"
                                            class="btn btn-sm btn-toggle mr-2<?php echo $switchSupportBorlabsCookie; ?>"
                                            data-toggle="button" data-switch-target="supportBorlabsCookie"
                                            aria-pressed="<?php echo $inputSupportBorlabsCookie ? 'true' : 'false'; ?>"
                                            autocomplete="off">
                                        <span class="handle"></span>
                                    </button>
                                    <input type="hidden" id="supportBorlabsCookie" name="supportBorlabsCookie"
                                           value="<?php echo $inputSupportBorlabsCookie; ?>">
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('The <strong>Cookie Box</strong> contains a reference to Borlabs Cookie. Activate this option to support this plugin.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-8 offset-sm-4">
                            <button type="submit"
                                    class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4 rounded-right shadow-sm bg-tips text-light">
                <div class="px-3 pt-3 pb-3 mb-4">
                    <h3 class="border-bottom mb-3"><?php _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>

                    <div class="accordion" id="accordionGeneralSettings">

                        <button type="button" class="collapsed" data-toggle="collapse"
                                data-target="#accordionGeneralSettingsOne" aria-expanded="true">
                            <?php _ex('How can the visitor change selections?', 'Backend / Cookie Box / Tips / Headline', 'borlabs-cookie'); ?>
                        </button>

                        <div id="accordionGeneralSettingsOne" class="collapse show"
                             data-parent="#accordionGeneralSettings">
                            <p><?php _ex('To give him this option, enter the following shortcode to your privacy page. This will create a button which reopens the <strong>Cookie Box</strong>.', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?>
                            </p>
                            <p>
                                <strong><?php _ex('Button', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></strong>
                                <span class="code-example">[borlabs-cookie type="btn-cookie-preference" title="Lorem ipsum"/]</span>
                            </p>
                            <p>
                                <strong><?php _ex('Link', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></strong>
                                <span class="code-example">[borlabs-cookie type="btn-cookie-preference" title="Lorem ipsum" element="link"/]</span>
                            </p>
                        </div>

                        <button type="button" class="collapsed" data-toggle="collapse"
                                data-target="#accordionGeneralSettingTwo">
                            <?php _ex('How can the visitor perform an opt-out?', 'Backend / Cookie Box / Tips / Headline', 'borlabs-cookie'); ?>
                        </button>

                        <div id="accordionGeneralSettingTwo" class="collapse" data-parent="#accordionGeneralSettings">
                            <p><?php
                                printf(
                                    _x('Insert the following shortcode to your privacy page. This will create an opt-out button that allows the visitor to opt-out. %s', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'),
                                    '<span class="code-example">[borlabs-cookie type="btn-switch-consent" id="ID of the Cookie"/]</span>'
                                );
                                ?>
                            </p>
                        </div>

                        <button type="button" class="collapsed" data-toggle="collapse"
                                data-target="#accordionGeneralSettingThree">
                            <?php _ex('How to display User ID, Consent History and Cookie & Cookie Groups Overview.', 'Backend / Cookie Box / Tips / Headline', 'borlabs-cookie'); ?>
                        </button>

                        <div id="accordionGeneralSettingThree" class="collapse" data-parent="#accordionGeneralSettings">
                            <p><?php
                                printf(
                                    _x('To display the <strong>User ID</strong> use the following shortcode. This allows the visitor to view the personal <strong>User ID</strong>. %s', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'),
                                    '<span class="code-example">[borlabs-cookie type="uid"/]</span>'
                                );
                                ?>
                            </p>
                            <p><?php
                                printf(
                                    _x('To display the <strong>Consent History</strong> use the following shortcode. This allows the visitor to view the personal <strong>Consent History</strong>. %s', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'),
                                    '<span class="code-example">[borlabs-cookie type="consent-history"/]</span>'
                                );
                                ?>
                            </p>
                            <p><?php
                                printf(
                                    _x('To display the <strong>Cookie & Cookie Groups Overview</strong> use the following shortcode. This creates an overview of all active <strong>Cookies</strong> and <strong>Cookie Groups</strong>. %s', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'),
                                    '<span class="code-example">[borlabs-cookie type="cookie-list"/]</span>'
                                );
                                ?>
                            </p>
                        </div>

                        <button type="button" class="collapsed" data-toggle="collapse"
                                data-target="#accordionGeneralSettingFour">
                            <?php _ex('Why does a Password prompt appear after the Cookie Box?', 'Backend / Cookie Box / Tips / Headline', 'borlabs-cookie'); ?>
                        </button>

                        <div id="accordionGeneralSettingFour" class="collapse" data-parent="#accordionGeneralSettings">
                            <p>
                                <?php _ex('If a password prompt appears after sending the <strong>Cookie Box</strong>, check whether you have blocked the <strong><em>/wp-admin</em></strong> folder via http-auth (htaccess directory protection). The <strong><em>admin-ajax.php</em></strong> is essential for the communication between front- and backend via AJAX.', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?>
                            </p>
                        </div>

                        <button type="button" class="collapsed" data-toggle="collapse"
                                data-target="#accordionGeneralSettingFive">
                            <?php _ex('What method of Integration should I use?', 'Backend / Cookie Box / Tips / Headline', 'borlabs-cookie'); ?>
                        </button>

                        <div id="accordionGeneralSettingFive" class="collapse" data-parent="#accordionGeneralSettings">
                            <p>
                                <?php _ex('We recommend the integration via JavaScript, as this avoids effects on the SEO. Note: Users of W3 Total Cache who have JavaScript minification enabled must select HTML integration because there is a bug in W3 Total Cache.', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?>
                            </p>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <div class="row no-gutters mb-4">
            <div class="col-12 col-md-8 rounded-left bg-light shadow-sm">
                <div class="px-3 pt-3 pb-4">
                    <h3 class="border-bottom mb-3"><?php _ex('Additional Settings', 'Backend / Cookie Box / Headline', 'borlabs-cookie'); ?></h3>
                    <div class="row">
                        <div class="col-12">

                            <div class="form-group row align-items-center">
                                <label for="cookieBoxShowAcceptAllButton"
                                       class="col-sm-4 col-form-label"><?php _ex('Show Accept all Button', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <button type="button"
                                            class="btn btn-sm btn-toggle mr-2<?php echo $switchCookieBoxShowAcceptAllButton; ?>"
                                            data-toggle="button" data-switch-target="cookieBoxShowAcceptAllButton"
                                            aria-pressed="<?php echo $inputCookieBoxShowAcceptAllButton ? 'true' : 'false'; ?>"
                                            autocomplete="off">
                                        <span class="handle"></span>
                                    </button>
                                    <input type="hidden" id="cookieBoxShowAcceptAllButton"
                                           name="cookieBoxShowAcceptAllButton"
                                           value="<?php echo $inputCookieBoxShowAcceptAllButton; ?>">
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('Shows a second button in the <strong>Cookie Preferences</strong>, with which the visitor can accept all <strong>Cookies</strong>.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="cookieBoxIgnorePreSelectStatus"
                                       class="col-sm-4 col-form-label"><?php _ex('Ignore Pre-selected Status', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <button type="button"
                                            class="btn btn-sm btn-toggle mr-2<?php echo $switchCookieBoxIgnorePreSelectStatus; ?>"
                                            data-toggle="button" data-switch-target="cookieBoxIgnorePreSelectStatus"
                                            aria-pressed="<?php echo $inputCookieBoxIgnorePreSelectStatus ? 'true' : 'false'; ?>"
                                            autocomplete="off">
                                        <span class="handle"></span>
                                    </button>
                                    <input type="hidden" id="cookieBoxIgnorePreSelectStatus"
                                           name="cookieBoxIgnorePreSelectStatus"
                                           value="<?php echo $inputCookieBoxIgnorePreSelectStatus; ?>">
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('If enabled, no <strong>Cookie Group</strong> is pre-selected in the <strong>Cookie Preferences</strong>. Does not apply to the <strong>Cookie Box</strong>.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-8 offset-sm-4">
                            <button type="submit"
                                    class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4 rounded-right shadow-sm bg-tips text-light">
                <div class="px-3 pt-3 pb-3 mb-4">
                    <h3 class="border-bottom mb-3"><?php _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>

                    <p><?php _ex('These settings affect the <strong>Cookie Settings</strong> and <strong>Layouts</strong> with the word <strong>Advanced</strong> in the name.', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></p>

                    <div class="accordion" id="accordionSettingsCookieSettings">

                        <button type="button" class="collapsed" data-toggle="collapse"
                                data-target="#accordionSettingsCookieSettingsOne">
                            <?php _ex('Show Accept all Button', 'Backend / Cookie Box / Tips / Headline', 'borlabs-cookie'); ?>
                        </button>

                        <div id="accordionSettingsCookieSettingsOne" class="collapse"
                             data-parent="#accordionSettingsCookieSettings">
                            <p><?php _ex('Borlabs Cookie shows a second button in the <strong>Cookie Preferences</strong> that allows the visitor to accept all <strong>Cookies</strong>. The <strong>Save</strong> button is retained.', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></p>
                            <p><?php _ex('If the visitor clicks on the <strong>Accept all</strong> button, all <strong>Cookie Groups</strong> are selected, the selection is saved and the window is closed.', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></p>
                        </div>

                        <button type="button" class="collapsed" data-toggle="collapse"
                                data-target="#accordionSettingsCookieSettingTwo">
                            <?php _ex('Ignore Pre-selected Status', 'Backend / Cookie Box / Tips / Headline', 'borlabs-cookie'); ?>
                        </button>

                        <div id="accordionSettingsCookieSettingTwo" class="collapse"
                             data-parent="#accordionSettingsCookieSettings">
                            <p><?php _ex('If this option is enabled, no <strong>Cookie Group</strong> will be pre-selected in the <strong>Cookie Preferences</strong>. This does not apply to the <strong>Cookie Box</strong>: If the visitor clicks directly on the <strong>Accept Cookie</strong> button, the <strong>Cookie Groups</strong> activated under <strong>Cookie Groups &gt; <em>Your Cookie Group</em> &gt; Pre-selected</strong> are set.', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></p>
                            <p><?php _ex('If the visitor instead clicks on the individual <strong>Cookie Preferences</strong>, these will be displayed. There are no <strong>Cookie Groups</strong> pre-selected. If he now clicks on the <strong>Back</strong> link, his selection will also be used for the <strong>Cookie Box</strong>.', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="row no-gutters mb-4">
            <div class="col-12 col-md-8 rounded-left bg-light shadow-sm">
                <div class="px-3 pt-3 pb-4">
                    <h3 class="border-bottom mb-3"><?php _ex('Layout Settings', 'Backend / Cookie Box / Headline', 'borlabs-cookie'); ?></h3>
                    <div class="row">
                        <div class="col-12">

                            <div class="form-group row align-items-center">
                                <label for="cookieBoxLayout"
                                       class="col-sm-4 col-form-label"><?php _ex('Layout', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <select class="form-control form-control form-control-sm d-inline-block w-75 mr-2"
                                            id="cookieBoxLayout" name="cookieBoxLayout">
                                        <option<?php echo $optionCookieBoxLayoutBar; ?>
                                            value="bar"><?php _ex('Bar', 'Backend / Cookie Box / Select Option', 'borlabs-cookie'); ?></option>
                                        <option<?php echo $optionCookieBoxLayoutBarAdvanced; ?>
                                            value="bar-advanced"><?php _ex('Bar - Advanced', 'Backend / Cookie Box / Select Option', 'borlabs-cookie'); ?></option>
                                        <option<?php echo $optionCookieBoxLayoutBarSlim; ?>
                                            value="bar-slim"><?php _ex('Bar - Slim', 'Backend / Cookie Box / Select Option', 'borlabs-cookie'); ?></option>
                                        <option<?php echo $optionCookieBoxLayoutBox; ?>
                                            value="box"><?php _ex('Box', 'Backend / Cookie Box / Select Option', 'borlabs-cookie'); ?></option>
                                        <option<?php echo $optionCookieBoxLayoutBoxAdvanced; ?>
                                            value="box-advanced"><?php _ex('Box - Advanced', 'Backend / Cookie Box / Select Option', 'borlabs-cookie'); ?></option>
                                        <option<?php echo $optionCookieBoxLayoutBoxPlus; ?>
                                            value="box-plus"><?php _ex('Box - Plus', 'Backend / Cookie Box / Select Option', 'borlabs-cookie'); ?></option>
                                        <option<?php echo $optionCookieBoxLayoutBoxSlim; ?>
                                            value="box-slim"><?php _ex('Box - Slim', 'Backend / Cookie Box / Select Option', 'borlabs-cookie'); ?></option>
                                    </select>
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('Choose the layout of the <strong>Cookie Box</strong>.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <?php
                            if (
                                empty($optionCookieBoxLayoutBarAdvanced) && empty($optionCookieBoxLayoutBoxAdvanced) && empty($optionCookieBoxLayoutBoxPlus)
                            ) {
                                ?>
                                <div class="form-group row align-items-center">
                                    <div class="col-sm-8 offset-4">
                                        <div
                                            class="alert alert-danger mt-2"><?php _ex('Depending on applicable law, this layout may not be allowed to be used.', 'Backend / Cookie Box / Alert Message', 'borlabs-cookie'); ?></div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>

                            <div class="form-group row align-items-center">
                                <label for="cookieBoxPosition"
                                       class="col-sm-4 col-form-label"><?php _ex('Position', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <select class="form-control form-control form-control-sm d-inline-block w-75 mr-2"
                                            id="cookieBoxPosition" name="cookieBoxPosition">
                                        <option<?php echo $optionCookieBoxPositionTL; ?>
                                            value="top-left"><?php _ex('Top Left', 'Backend / Cookie Box / Select Option', 'borlabs-cookie'); ?></option>
                                        <option<?php echo $optionCookieBoxPositionTC; ?>
                                            value="top-center"><?php _ex('Top Center', 'Backend / Cookie Box / Select Option', 'borlabs-cookie'); ?></option>
                                        <option<?php echo $optionCookieBoxPositionTR; ?>
                                            value="top-right"><?php _ex('Top Right', 'Backend / Cookie Box / Select Option', 'borlabs-cookie'); ?></option>
                                        <option<?php echo $optionCookieBoxPositionML; ?>
                                            value="middle-left"><?php _ex('Middle Left', 'Backend / Cookie Box / Select Option', 'borlabs-cookie'); ?></option>
                                        <option<?php echo $optionCookieBoxPositionMC; ?>
                                            value="middle-center"><?php _ex('Middle Center', 'Backend / Cookie Box / Select Option', 'borlabs-cookie'); ?></option>
                                        <option<?php echo $optionCookieBoxPositionMR; ?>
                                            value="middle-right"><?php _ex('Middle Right', 'Backend / Cookie Box / Select Option', 'borlabs-cookie'); ?></option>
                                        <option<?php echo $optionCookieBoxPositionBL; ?>
                                            value="bottom-left"><?php _ex('Bottom Left', 'Backend / Cookie Box / Select Option', 'borlabs-cookie'); ?></option>
                                        <option<?php echo $optionCookieBoxPositionBC; ?>
                                            value="bottom-center"><?php _ex('Bottom Center', 'Backend / Cookie Box / Select Option', 'borlabs-cookie'); ?></option>
                                        <option<?php echo $optionCookieBoxPositionBR; ?>
                                            value="bottom-right"><?php _ex('Bottom Right', 'Backend / Cookie Box / Select Option', 'borlabs-cookie'); ?></option>
                                    </select>
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('Choose the position in which the <strong>Cookie Box</strong> appears.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="cookieBoxCookieGroupJustification"
                                       class="col-sm-4 col-form-label"><?php _ex('Cookie Groups Justification', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <select class="form-control form-control form-control-sm d-inline-block w-75 mr-2"
                                            id="cookieBoxCookieGroupJustification"
                                            name="cookieBoxCookieGroupJustification">
                                        <option<?php echo $optionCookieBoxCookieGroupJustificationSA; ?>
                                            value="space-around">space-around
                                        </option>
                                        <option<?php echo $optionCookieBoxCookieGroupJustificationSB; ?>
                                            value="space-between">space-between
                                        </option>
                                    </select>
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('Choose the justification of the <strong>Cookie Group</strong> items (list items or checkboxes) in the <strong>Cookie Box</strong>.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-8 offset-sm-4">
                            <button type="submit"
                                    class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4 rounded-right shadow-sm bg-tips text-light">
                <div class="px-3 pt-3 pb-3 mb-4">
                    <h3 class="border-bottom mb-3"><?php _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>
                    <h4><?php _ex('Cookie Groups Justification', 'Backend / Cookie Box / Tips / Headline', 'borlabs-cookie'); ?></h4>
                    <p><?php _ex('<strong>space-around</strong>: The spacing between each pair of adjacent items is the same. The empty space before the first and after the last item equals half of the space between each pair of adjacent items.', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?> </p>
                    <p><?php _ex('<strong>space-between</strong>: The spacing between each pair of adjacent items is the same. The first item is flush with the main-start edge, and the last item is flush with the main-end edge.', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?> </p>

                </div>
            </div>
        </div>

        <div class="row no-gutters mb-4">
            <div class="col-12 col-md-8 rounded bg-light shadow-sm">
                <div class="px-3 pt-3 pb-4">
                    <h3 class="border-bottom mb-3"><?php _ex('Animation Settings', 'Backend / Cookie Box / Headline', 'borlabs-cookie'); ?></h3>
                    <div class="row">
                        <div class="col-12">

                            <div class="form-group row align-items-center">
                                <label for="cookieBoxAnimation"
                                       class="col-sm-4 col-form-label"><?php _ex('Animation', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <button type="button"
                                            class="btn btn-sm btn-toggle mr-2<?php echo $switchCookieBoxAnimation; ?>"
                                            data-toggle="button" data-switch-target="cookieBoxAnimation"
                                            aria-pressed="<?php echo $inputCookieBoxAnimation ? 'true' : 'false'; ?>"
                                            autocomplete="off">
                                        <span class="handle"></span>
                                    </button>
                                    <input type="hidden" id="cookieBoxAnimation" name="cookieBoxAnimation"
                                           value="<?php echo $inputCookieBoxAnimation; ?>">
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('If activated the <strong>Cookie Box</strong> is animated.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="cookieBoxAnimationDelay"
                                       class="col-sm-4 col-form-label"><?php _ex('Animation Delay', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <button type="button"
                                            class="btn btn-sm btn-toggle mr-2<?php echo $switchCookieBoxAnimationDelay; ?>"
                                            data-toggle="button" data-switch-target="cookieBoxAnimationDelay"
                                            aria-pressed="<?php echo $inputCookieBoxAnimationDelay ? 'true' : 'false'; ?>"
                                            autocomplete="off">
                                        <span class="handle"></span>
                                    </button>
                                    <input type="hidden" id="cookieBoxAnimationDelay" name="cookieBoxAnimationDelay"
                                           value="<?php echo $inputCookieBoxAnimationDelay; ?>">
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('If activated the animation and appearance of the <strong>Cookie Box</strong> is delayed for one second.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="cookieBoxAnimationIn"
                                       class="col-sm-4 col-form-label"><?php _ex('Animation In', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <select class="form-control form-control form-control-sm d-inline-block w-75 mr-2"
                                            id="cookieBoxAnimationIn" name="cookieBoxAnimationIn"
                                            data-animation="animationPreview">
                                        <optgroup
                                            label="<?php _ex('Attention Seekers', 'Backend / Cookie Box / Select Option Group', 'borlabs-cookie'); ?>">
                                            <option<?php echo $optionCookieBoxAnimationInBounce; ?> value="bounce">
                                                bounce
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInFlash; ?> value="flash">flash
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInPulse; ?> value="pulse">pulse
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInRubberBand; ?>
                                                value="rubberBand">rubberBand
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInShake; ?> value="shake">shake
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInSwing; ?> value="swing">swing
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInTada; ?> value="tada">tada
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInWobble; ?> value="wobble">
                                                wobble
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInJello; ?> value="jello">jello
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInHeartBeat; ?> value="heartBeat">
                                            heartBeat
                                            </option>
                                        </optgroup>

                                        <optgroup
                                            label="<?php _ex('Bouncing Entrances', 'Backend / Cookie Box / Select Option Group', 'borlabs-cookie'); ?>">
                                            <option<?php echo $optionCookieBoxAnimationInBounceIn; ?> value="bounceIn">
                                                bounceIn
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInBounceInDown; ?>
                                                value="bounceInDown">bounceInDown
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInBounceInLeft; ?>
                                                value="bounceInLeft">bounceInLeft
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInBounceInRight; ?>
                                                value="bounceInRight">bounceInRight
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInBounceInUp; ?>
                                                value="bounceInUp">bounceInUp
                                            </option>
                                        </optgroup>

                                        <optgroup
                                            label="<?php _ex('Fading Entrances', 'Backend / Cookie Box / Select Option Group', 'borlabs-cookie'); ?>">
                                            <option<?php echo $optionCookieBoxAnimationInFadeIn; ?> value="fadeIn">
                                                fadeIn
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInFadeInDown; ?>
                                                value="fadeInDown">fadeInDown
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInFadeInDownBig; ?>
                                                value="fadeInDownBig">fadeInDownBig
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInFadeInLeft; ?>
                                                value="fadeInLeft">fadeInLeft
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInFadeInLeftBig; ?>
                                                value="fadeInLeftBig">fadeInLeftBig
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInFadeInRight; ?>
                                                value="fadeInRight">fadeInRight
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInFadeInRightBig; ?>
                                                value="fadeInRightBig">fadeInRightBig
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInFadeInUp; ?> value="fadeInUp">
                                                fadeInUp
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInFadeInUpBig; ?>
                                                value="fadeInUpBig">fadeInUpBig
                                            </option>
                                        </optgroup>

                                        <optgroup
                                            label="<?php _ex('Flippers', 'Backend / Cookie Box / Select Option Group', 'borlabs-cookie'); ?>">
                                            <option<?php echo $optionCookieBoxAnimationInFlip; ?> value="flip">flip
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInFlipInX; ?> value="flipInX">
                                                flipInX
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInFlipInY; ?> value="flipInY">
                                                flipInY
                                            </option>
                                        </optgroup>

                                        <optgroup
                                            label="<?php _ex('Lightspeed', 'Backend / Cookie Box / Select Option Group', 'borlabs-cookie'); ?>">
                                            <option<?php echo $optionCookieBoxAnimationInLightSpeedIn; ?>
                                                value="lightSpeedIn">lightSpeedIn
                                            </option>
                                        </optgroup>

                                        <optgroup
                                            label="<?php _ex('Rotating Entrances', 'Backend / Cookie Box / Select Option Group', 'borlabs-cookie'); ?>">
                                            <option<?php echo $optionCookieBoxAnimationInRotateIn; ?> value="rotateIn">
                                                rotateIn
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInRotateInDownLeft; ?>
                                                value="rotateInDownLeft">rotateInDownLeft
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInRotateInDownRight; ?>
                                                value="rotateInDownRight">rotateInDownRight
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInRotateInUpLeft; ?>
                                                value="rotateInUpLeft">rotateInUpLeft
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInRotateInUpRight; ?>
                                                value="rotateInUpRight">rotateInUpRight
                                            </option>
                                        </optgroup>

                                        <optgroup
                                            label="<?php _ex('Sliding Entrances', 'Backend / Cookie Box / Select Option Group', 'borlabs-cookie'); ?>">
                                            <option<?php echo $optionCookieBoxAnimationInSlideInUp; ?> value="slideInUp">
                                            slideInUp
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInSlideInDown; ?>
                                                value="slideInDown">slideInDown
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInSlideInLeft; ?>
                                                value="slideInLeft">slideInLeft
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInSlideInRight; ?>
                                                value="slideInRight">slideInRight
                                            </option>
                                        </optgroup>

                                        <optgroup
                                            label="<?php _ex('Zoom Entrances', 'Backend / Cookie Box / Select Option Group', 'borlabs-cookie'); ?>">
                                            <option<?php echo $optionCookieBoxAnimationInZoomIn; ?> value="zoomIn">
                                                zoomIn
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInZoomInDown; ?>
                                                value="zoomInDown">zoomInDown
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInZoomInLeft; ?>
                                                value="zoomInLeft">zoomInLeft
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInZoomInRight; ?>
                                                value="zoomInRight">zoomInRight
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInZoomInUp; ?> value="zoomInUp">
                                                zoomInUp
                                            </option>
                                        </optgroup>

                                        <optgroup
                                            label="<?php _ex('Specials', 'Backend / Cookie Box / Select Option Group', 'borlabs-cookie'); ?>">
                                            <option<?php echo $optionCookieBoxAnimationInJackInTheBox; ?>
                                                value="jackInTheBox">jackInTheBox
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationInRollIn; ?> value="rollIn">
                                                rollIn
                                            </option>
                                        </optgroup>
                                    </select>
                                    <span data-repeat-animation="cookieBoxAnimationIn" data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('Repeat animation.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-play-circle text-dark"></i></span>
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('Choose the animation with which the <strong>Cookie Box</strong> appears.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="cookieBoxAnimationOut"
                                       class="col-sm-4 col-form-label"><?php _ex('Animation Out', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <select class="form-control form-control form-control-sm d-inline-block w-75 mr-2"
                                            id="cookieBoxAnimationOut" name="cookieBoxAnimationOut"
                                            data-animation="animationPreview">

                                        <optgroup
                                            label="<?php _ex('Bouncing Exits', 'Backend / Cookie Box / Select Option Group', 'borlabs-cookie'); ?>">
                                            <option<?php echo $optionCookieBoxAnimationOnBounceOut; ?> value="bounceOut">
                                            bounceOut
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationOnBounceOutDown; ?>
                                                value="bounceOutDown">bounceOutDown
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationOnBounceOutLeft; ?>
                                                value="bounceOutLeft">bounceOutLeft
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationOnBounceOutRight; ?>
                                                value="bounceOutRight">bounceOutRight
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationOnBounceOutUp; ?>
                                                value="bounceOutUp">bounceOutUp
                                            </option>
                                        </optgroup>

                                        <optgroup
                                            label="<?php _ex('Fading Exits', 'Backend / Cookie Box / Select Option Group', 'borlabs-cookie'); ?>">
                                            <option<?php echo $optionCookieBoxAnimationOnFadeOut; ?> value="fadeOut">
                                                fadeOut
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationOnFadeOutDown; ?>
                                                value="fadeOutDown">fadeOutDown
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationOnFadeOutDownBig; ?>
                                                value="fadeOutDownBig">fadeOutDownBig
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationOnFadeOutLeft; ?>
                                                value="fadeOutLeft">fadeOutLeft
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationOnFadeOutLeftBig; ?>
                                                value="fadeOutLeftBig">fadeOutLeftBig
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationOnFadeOutRight; ?>
                                                value="fadeOutRight">fadeOutRight
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationOnFadeOutRightBig; ?>
                                                value="fadeOutRightBig">fadeOutRightBig
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationOnFadeOutUp; ?> value="fadeOutUp">
                                            fadeOutUp
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationOnFadeOutUpBig; ?>
                                                value="fadeOutUpBig">fadeOutUpBig
                                            </option>
                                        </optgroup>

                                        <optgroup
                                            label="<?php _ex('Flippers', 'Backend / Cookie Box / Select Option Group', 'borlabs-cookie'); ?>">
                                            <option<?php echo $optionCookieBoxAnimationOnFlipOutX; ?> value="flipOutX">
                                                flipOutX
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationOnFlipOutY; ?> value="flipOutY">
                                                flipOutY
                                            </option>
                                        </optgroup>

                                        <optgroup
                                            label="<?php _ex('Lightspeed', 'Backend / Cookie Box / Select Option Group', 'borlabs-cookie'); ?>">
                                            <option<?php echo $optionCookieBoxAnimationOnLightSpeedOut; ?>
                                                value="lightSpeedOut">lightSpeedOut
                                            </option>
                                        </optgroup>

                                        <optgroup
                                            label="<?php _ex('Rotating Exits', 'Backend / Cookie Box / Select Option Group', 'borlabs-cookie'); ?>">
                                            <option<?php echo $optionCookieBoxAnimationOnRotateOut; ?> value="rotateOut">
                                            rotateOut
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationOnRotateOutDownLeft; ?>
                                                value="rotateOutDownLeft">rotateOutDownLeft
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationOnRotateOutDownRight; ?>
                                                value="rotateOutDownRight">rotateOutDownRight
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationOnRotateOutUpLeft; ?>
                                                value="rotateOutUpLeft">rotateOutUpLeft
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationOnRotateOutUpRight; ?>
                                                value="rotateOutUpRight">rotateOutUpRight
                                            </option>
                                        </optgroup>

                                        <optgroup
                                            label="<?php _ex('Sliding Exits', 'Backend / Cookie Box / Select Option Group', 'borlabs-cookie'); ?>">
                                            <option<?php echo $optionCookieBoxAnimationOnSlideOutUp; ?>
                                                value="slideOutUp">slideOutUp
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationOnSlideOutDown; ?>
                                                value="slideOutDown">slideOutDown
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationOnSlideOutLeft; ?>
                                                value="slideOutLeft">slideOutLeft
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationOnSlideOutRight; ?>
                                                value="slideOutRight">slideOutRight
                                            </option>
                                        </optgroup>

                                        <optgroup
                                            label="<?php _ex('Zoom Exits', 'Backend / Cookie Box / Select Option Group', 'borlabs-cookie'); ?>">
                                            <option<?php echo $optionCookieBoxAnimationOnZoomOut; ?> value="zoomOut">
                                                zoomOut
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationOnZoomOutDown; ?>
                                                value="zoomOutDown">zoomOutDown
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationOnZoomOutLeft; ?>
                                                value="zoomOutLeft">zoomOutLeft
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationOnZoomOutRight; ?>
                                                value="zoomOutRight">zoomOutRight
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationOnZoomOutUp; ?> value"zoomOutUp">
                                            zoomOutUp
                                            </option>
                                        </optgroup>

                                        <optgroup
                                            label="<?php _ex('Specials', 'Backend / Cookie Box / Select Option Group', 'borlabs-cookie'); ?>">
                                            <option<?php echo $optionCookieBoxAnimationOnHinge; ?> value="hinge">hinge
                                            </option>
                                            <option<?php echo $optionCookieBoxAnimationOnRollOut; ?> value="rollOut">
                                                rollOut
                                            </option>
                                        </optgroup>
                                    </select>
                                    <span data-repeat-animation="cookieBoxAnimationOut" data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('Repeat animation.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-play-circle text-dark"></i></span>
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('Choose the animation with which the <strong>Cookie Box</strong> disappears.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label
                                    class="col-sm-4 col-form-label"><?php _ex('Animation Preview', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">

                                    <div id="animationPreview">
                                        <div class="cookie-box">
                                            <div class="row no-gutters">
                                                <div class="col-xs-4 col-2">
                                                    <img src="<?php echo $animationPreviewImage; ?>" alt=""
                                                         style="margin: 8px;">
                                                </div>
                                                <div class="col-xs-8 col-10">
                                                    <span class="demo-headline" style="width: 50%;"></span>
                                                    <span style="width: 80%;"></span>
                                                    <span style="width: 80%;"></span>
                                                    <span class="demo-btn" style="width: 70px;"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-8 offset-sm-4">
                            <button type="submit"
                                    class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row no-gutters mb-4">
            <div class="col-12 col-md-8 rounded bg-light shadow-sm">
                <div class="px-3 pt-3 pb-4">
                    <h3 class="border-bottom mb-3"><?php _ex('Widget Settings', 'Backend / Cookie Box / Headline', 'borlabs-cookie'); ?></h3>
                    <div class="row">
                        <div class="col-12">

                            <div class="form-group row align-items-center">
                                <label for="cookieBoxShowWidget"
                                       class="col-sm-4 col-form-label"><?php _ex('Show Widget', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <button type="button"
                                            class="btn btn-sm btn-toggle mr-2<?php echo $switchCookieBoxShowWidget; ?>"
                                            data-toggle="button" data-switch-target="cookieBoxShowWidget"
                                            aria-pressed="<?php echo $inputCookieBoxShowWidget ? 'true' : 'false'; ?>"
                                            autocomplete="off">
                                        <span class="handle"></span>
                                    </button>
                                    <input type="hidden" id="cookieBoxShowWidget" name="cookieBoxShowWidget"
                                           value="<?php echo $inputCookieBoxShowWidget; ?>">
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('If activated a widget is displayed which can be used open the <strong>Privacy Preferences</strong>.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>


                            <div class="form-group row align-items-center">
                                <label for="cookieBoxWidgetPosition"
                                       class="col-sm-4 col-form-label"><?php _ex('Position', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <select class="form-control form-control form-control-sm d-inline-block w-75 mr-2"
                                            id="cookieBoxWidgetPosition" name="cookieBoxWidgetPosition">
                                        <option<?php echo $optionCookieBoxWidgetPositionBL; ?>
                                            value="bottom-left"><?php _ex('Bottom Left', 'Backend / Cookie Box / Select Option', 'borlabs-cookie'); ?></option>
                                        <option<?php echo $optionCookieBoxWidgetPositionBR; ?>
                                            value="bottom-right"><?php _ex('Bottom Right', 'Backend / Cookie Box / Select Option', 'borlabs-cookie'); ?></option>
                                    </select>
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('Choose the position in which the <strong>Widget</strong> appears.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <div class="form-group row row align-items-center">
                                <label for="cookieBoxWidgetColor"
                                       class="col-sm-4 col-form-label"><?php _ex('Widget Color', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="color-field" id="cookieBoxWidgetColor"
                                           name="cookieBoxWidgetColor"
                                           value="<?php echo $inputCookieBoxWidgetColor; ?>">

                                </div>
                            </div>




                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-8 offset-sm-4">
                            <button type="submit"
                                    class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row no-gutters mb-4">
            <div class="col-12 col-md-8 rounded bg-light shadow-sm">
                <div class="px-3 pt-3 pb-4">
                    <h3 class="border-bottom mb-3"><?php _ex('Logo Settings', 'Backend / Cookie Box / Headline', 'borlabs-cookie'); ?></h3>
                    <div class="row">
                        <div class="col-12">

                            <div class="form-group row align-items-center">
                                <label for="cookieBoxShowLogo"
                                       class="col-sm-4 col-form-label"><?php _ex('Show Logo', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <button type="button"
                                            class="btn btn-sm btn-toggle mr-2<?php echo $switchCookieBoxShowLogo; ?>"
                                            data-toggle="button" data-switch-target="cookieBoxShowLogo"
                                            aria-pressed="<?php echo $inputCookieBoxShowLogo ? 'true' : 'false'; ?>"
                                            autocomplete="off">
                                        <span class="handle"></span>
                                    </button>
                                    <input type="hidden" id="cookieBoxShowLogo" name="cookieBoxShowLogo"
                                           value="<?php echo $inputCookieBoxShowLogo; ?>">
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('If activated the selected Logo is displayed in the <strong>Cookie Box</strong>.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieBoxLogo" class="col-sm-4 col-form-label">
                                    <?php _ex('Logo', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?>
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('Choose a logo you want to appear in the <strong>Cookie Box</strong>.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control form-control-sm d-inline-block w-75"
                                               id="cookieBoxLogo" name="cookieBoxLogo"
                                               value="<?php echo $inputCookieBoxLogo; ?>">
                                        <div class="input-group-append">
                                            <button data-media-picker="cookieBoxLogo" data-media-preview="logoPreview"
                                                    data-media-title="<?php echo esc_attr_x('Select or Upload Logo', 'Backend / Cookie Box / Button Title', 'borlabs-cookie'); ?>"
                                                    data-media-button="<?php _ex('Use this media', 'Backend / Global / Button Title', 'borlabs-cookie'); ?>"
                                                    type="button" class="btn btn-secondary btn-sm"><i
                                                    class="fas fa-image"></i></button>
                                            <button data-media-clear="cookieBoxLogo" data-media-preview="logoPreview"
                                                    type="button" class="btn btn-danger btn-sm"><i
                                                    class="fas fa-trash-alt"></i></button>
                                        </div>
                                    </div>
                                    <div id="logoPreview" class="media-preview">
                                        <?php
                                        if (!empty($inputCookieBoxLogo)) {
                                            ?>
                                            <img src="<?php echo $inputCookieBoxLogo; ?>" alt="">
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieBoxLogoHD" class="col-sm-4 col-form-label">
                                    <?php _ex('Logo - HD', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?>
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('Choose the HD version of the logo you want to appear in the <strong>Cookie Box</strong>. It will be used for high resolution displays.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control form-control-sm d-inline-block w-75"
                                               id="cookieBoxLogoHD" name="cookieBoxLogoHD"
                                               value="<?php echo $inputCookieBoxLogoHD; ?>">
                                        <div class="input-group-append">
                                            <button data-media-picker="cookieBoxLogoHD"
                                                    data-media-preview="logoHDPreview"
                                                    data-media-title="<?php echo esc_attr_x('Select or Upload Logo', 'Backend / Cookie Box / Button Title', 'borlabs-cookie'); ?>"
                                                    data-media-button="<?php _ex('Use this media', 'Backend / Global / Button Title', 'borlabs-cookie'); ?>"
                                                    type="button" class="btn btn-secondary btn-sm"><i
                                                    class="fas fa-image"></i></button>
                                            <button data-media-clear="cookieBoxLogoHD"
                                                    data-media-preview="logoHDPreview" type="button"
                                                    class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div id="logoHDPreview" class="media-preview">
                                        <?php
                                        if (!empty($inputCookieBoxLogoHD)) {
                                            ?>
                                            <img src="<?php echo $inputCookieBoxLogoHD; ?>" alt="">
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-8 offset-sm-4">
                            <button type="submit"
                                    class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row no-gutters mb-4">
            <div class="col-12 col-md-8 rounded-left bg-light shadow-sm">
                <div class="px-3 pt-3 pb-4">
                    <h3 class="border-bottom mb-3"><?php _ex('Appearance Settings', 'Backend / Cookie Box / Headline', 'borlabs-cookie'); ?></h3>
                    <div class="row">
                        <div class="col-12">

                            <div class="form-group row">
                                <label for="cookieBoxFontFamily" class="col-sm-4 col-form-label">
                                    <?php _ex('Font Family', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('Usually the theme font is used. To use a custom font family select the checkbox and enter custom font.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="input-group input-group-sm w-75">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <input type="checkbox" name="enableCookieBoxFontFamily" value="1"
                                                   data-enable-target="cookieBoxFontFamily"<?php echo !empty($inputCookieBoxFontFamily) ? ' checked' : ''; ?>>
                                        </span>
                                        </div>
                                        <input type="text" class="form-control form-control-sm d-inline-block"
                                               id="cookieBoxFontFamily" name="cookieBoxFontFamily"
                                               value="<?php echo $inputCookieBoxFontFamily; ?>"
                                               placeholder="<?php echo esc_attr_x('Enter custom font family', 'Backend / Cookie Box / Input Placeholder', 'borlabs-cookie'); ?>"<?php echo empty($inputCookieBoxFontFamily) ? ' disabled' : ''; ?>>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieBoxFontSize" class="col-sm-4 col-form-label">
                                    <?php _ex('Font Size (Base)', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('Based on the base font size, the font sizes of all elements are automatically adjusted proportionally.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="input-group input-group-sm w-50">
                                        <div class="input-group input-group-sm mb-2">
                                            <input type="number"
                                                   class="form-control form-control-sm d-inline-block w-75"
                                                   id="cookieBoxFontSize" name="cookieBoxFontSize" min="0"
                                                   value="<?php echo $inputCookieBoxFontSize; ?>">
                                            <div class="input-group-append">
                                                <span class="input-group-text">px</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label
                                    class="col-sm-4 col-form-label"><?php _ex('Cookie Box', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxBgColor"
                                                   name="cookieBoxBgColor"
                                                   value="<?php echo $inputCookieBoxBgColor; ?>">
                                            <div>
                                                <?php _ex('Background Color', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxTxtColor"
                                                   name="cookieBoxTxtColor"
                                                   value="<?php echo $inputCookieBoxTxtColor; ?>">
                                            <div>
                                                <?php _ex('Text Color', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label
                                    class="col-sm-4 col-form-label"><?php _ex('Accordion', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxAccordionBgColor"
                                                   name="cookieBoxAccordionBgColor"
                                                   value="<?php echo $inputCookieBoxAccordionBgColor; ?>">
                                            <div>
                                                <?php _ex('Background Color', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxAccordionTxtColor"
                                                   name="cookieBoxAccordionTxtColor"
                                                   value="<?php echo $inputCookieBoxAccordionTxtColor; ?>">
                                            <div>
                                                <?php _ex('Text Color', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label
                                    class="col-sm-4 col-form-label"><?php _ex('Table', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxTableBgColor"
                                                   name="cookieBoxTableBgColor"
                                                   value="<?php echo $inputCookieBoxTableBgColor; ?>">
                                            <div>
                                                <?php _ex('Background Color', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxTableTxtColor"
                                                   name="cookieBoxTableTxtColor"
                                                   value="<?php echo $inputCookieBoxTableTxtColor; ?>">
                                            <div>
                                                <?php _ex('Text Color', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-8 offset-sm-4">
                            <button type="submit"
                                    class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4 rounded-right shadow-sm bg-tips text-light">
                <div class="px-3 pt-3 pb-3 mb-4">
                    <h3 class="border-bottom mb-3"><?php _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>

                    <div class="accordion" id="accordionAppearanceSettings">

                        <button type="button" class="collapsed" data-toggle="collapse"
                                data-target="#accordionAppearanceSettingsOne" aria-expanded="true">
                            <?php _ex('Where\'s what? Overview of all elements in the <strong>Cookie Box</strong>.', 'Backend / Cookie Box / Tips / Headline', 'borlabs-cookie'); ?>
                        </button>

                        <div id="accordionAppearanceSettingsOne" class="collapse show"
                             data-parent="#accordionAppearanceSettings">
                            <p class="text-center"><a href="#" data-toggle="modal"
                                                      data-target="#BorlabsCookieTipCookieBoxModal"><img
                                        src="<?php echo $tipsImageCookieBox; ?>" alt="" style="width: 70%;"></a></p>
                            <p class="text-center"><a href="#" data-toggle="modal"
                                                      data-target="#BorlabsCookieTipCookieBoxModal"
                                                      class="text-light"><i
                                        class="fas fa-info-circle"></i> <?php _ex('Click for more information', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?>
                                </a></p>
                        </div>

                        <button type="button" class="collapsed" data-toggle="collapse"
                                data-target="#accordionAppearanceSettingsTwo">
                            <?php _ex('Where\'s what? Overview of all elements in the Cookie Preferences.', 'Backend / Cookie Box / Tips / Headline', 'borlabs-cookie'); ?>
                        </button>

                        <div id="accordionAppearanceSettingsTwo" class="collapse"
                             data-parent="#accordionAppearanceSettings">
                            <p class="text-center"><a href="#" data-toggle="modal"
                                                      data-target="#BorlabsCookieTipCookiePreferenceModal"><img
                                        src="<?php echo $tipsImageCookiePreference; ?>" alt="" style="width: 70%;"></a>
                            </p>
                            <p class="text-center"><a href="#" data-toggle="modal"
                                                      data-target="#BorlabsCookieTipCookiePreferenceModal"
                                                      class="text-light"><i
                                        class="fas fa-info-circle"></i> <?php _ex('Click for more information', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?>
                                </a></p>
                            <p><?php _ex('The <strong>Cookie Preferences</strong> is displayed when the visitor clicks on the link for the individual <strong>Cookie Preferences</strong> in the <strong>Cookie Box</strong>.', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row no-gutters mb-4">
            <div class="col-12 col-md-8 rounded bg-light shadow-sm">
                <div class="px-3 pt-3 pb-4">
                    <h3 class="border-bottom mb-3"><?php _ex('Border Settings', 'Backend / Cookie Box / Headline', 'borlabs-cookie'); ?></h3>
                    <div class="row">
                        <div class="col-12">

                            <div class="form-group row row align-items-center">
                                <label for="cookieBoxTableBorderColor"
                                       class="col-sm-4 col-form-label"><?php _ex('Table Border Color', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="color-field" id="cookieBoxTableBorderColor"
                                           name="cookieBoxTableBorderColor"
                                           value="<?php echo $inputCookieBoxTableBorderColor; ?>">

                                </div>
                            </div>

                            <div class="form-group row">
                                <label
                                    class="col-sm-4 col-form-label"><?php _ex('Border Radius', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="input-group input-group-sm mb-2">
                                                <input type="number"
                                                       class="form-control form-control-sm d-inline-block w-50"
                                                       id="cookieBoxBorderRadius" name="cookieBoxBorderRadius" min="0"
                                                       value="<?php echo $inputCookieBoxBorderRadius; ?>">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">px</span>
                                                </div>
                                            </div>
                                            <label for="cookieBoxBorderRadius">
                                                <?php _ex('Box', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </label>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="input-group input-group-sm mb-2">
                                                <input type="number"
                                                       class="form-control form-control-sm d-inline-block w-50"
                                                       id="cookieBoxBtnBorderRadius" name="cookieBoxBtnBorderRadius"
                                                       min="0" value="<?php echo $inputCookieBoxBtnBorderRadius; ?>">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">px</span>
                                                </div>
                                            </div>
                                            <label for="cookieBoxBtnBorderRadius">
                                                <?php _ex('Button', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </label>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="input-group input-group-sm mb-2">
                                                <input type="number"
                                                       class="form-control form-control-sm d-inline-block w-50"
                                                       id="cookieBoxCheckboxBorderRadius"
                                                       name="cookieBoxCheckboxBorderRadius" min="0"
                                                       value="<?php echo $inputCookieBoxCheckboxBorderRadius; ?>">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">px</span>
                                                </div>
                                            </div>
                                            <label for="cookieBoxCheckboxBorderRadius">
                                                <?php _ex('Checkbox', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </label>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="input-group input-group-sm mb-2">
                                                <input type="number"
                                                       class="form-control form-control-sm d-inline-block w-50"
                                                       id="cookieBoxAccordionBorderRadius"
                                                       name="cookieBoxAccordionBorderRadius" min="0"
                                                       value="<?php echo $inputCookieBoxAccordionBorderRadius; ?>">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">px</span>
                                                </div>
                                            </div>
                                            <label for="cookieBoxAccordionBorderRadius">
                                                <?php _ex('Accordion', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="input-group input-group-sm mb-2">
                                                <input type="number"
                                                       class="form-control form-control-sm d-inline-block w-50"
                                                       id="cookieBoxTableBorderRadius" name="cookieBoxTableBorderRadius"
                                                       min="0" value="<?php echo $inputCookieBoxTableBorderRadius; ?>">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">px</span>
                                                </div>
                                            </div>
                                            <label for="cookieBoxTableBorderRadius">
                                                <?php _ex('Table', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-8 offset-sm-4">
                            <button type="submit"
                                    class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row no-gutters mb-4">
            <div class="col-12 col-md-8 rounded bg-light shadow-sm">
                <div class="px-3 pt-3 pb-4">
                    <h3 class="border-bottom mb-3"><?php _ex('Button Settings', 'Backend / Cookie Box / Headline', 'borlabs-cookie'); ?></h3>
                    <div class="row">
                        <div class="col-12">

                            <div class="form-group row align-items-center">
                                <label for="cookieBoxBtnFullWidth"
                                       class="col-sm-4 col-form-label"><?php _ex('Button Full Width', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <button type="button"
                                            class="btn btn-sm btn-toggle mr-2<?php echo $switchCookieBoxBtnFullWidth; ?>"
                                            data-toggle="button" data-switch-target="cookieBoxBtnFullWidth"
                                            aria-pressed="<?php echo $inputCookieBoxBtnFullWidth ? 'true' : 'false'; ?>"
                                            autocomplete="off">
                                        <span class="handle"></span>
                                    </button>
                                    <input type="hidden" id="cookieBoxBtnFullWidth" name="cookieBoxBtnFullWidth"
                                           value="<?php echo $inputCookieBoxBtnFullWidth; ?>">
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('Choose if the button is full width (Status: ON) or not (Status: OFF). Only applies to the buttons in the <strong>Cookie Box</strong>, not <strong>Cookie Preferences</strong>.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label
                                    class="col-sm-4 col-form-label"><?php _ex('Button Color', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxBtnColor"
                                                   name="cookieBoxBtnColor"
                                                   value="<?php echo $inputCookieBoxBtnColor; ?>">
                                            <div>
                                                <?php _ex('Default', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxBtnHoverColor"
                                                   name="cookieBoxBtnHoverColor"
                                                   value="<?php echo $inputCookieBoxBtnHoverColor; ?>">
                                            <div>
                                                <?php _ex('Hover', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label
                                    class="col-sm-4 col-form-label"><?php _ex('Button Text Color', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxBtnTxtColor"
                                                   name="cookieBoxBtnTxtColor"
                                                   value="<?php echo $inputCookieBoxBtnTxtColor; ?>">
                                            <div>
                                                <?php _ex('Default', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxBtnHoverTxtColor"
                                                   name="cookieBoxBtnHoverTxtColor"
                                                   value="<?php echo $inputCookieBoxBtnTxtHoverColor; ?>">
                                            <div>
                                                <?php _ex('Hover', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label
                                    class="col-sm-4 col-form-label"><?php _ex('Refuse Button Color', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxRefuseBtnColor"
                                                   name="cookieBoxRefuseBtnColor"
                                                   value="<?php echo $inputCookieBoxRefuseBtnColor; ?>">
                                            <div>
                                                <?php _ex('Default', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxRefuseBtnHoverColor"
                                                   name="cookieBoxRefuseBtnHoverColor"
                                                   value="<?php echo $inputCookieBoxRefuseBtnHoverColor; ?>">
                                            <div>
                                                <?php _ex('Hover', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label
                                    class="col-sm-4 col-form-label"><?php _ex('Refuse Button Text Color', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxRefuseBtnTxtColor"
                                                   name="cookieBoxRefuseBtnTxtColor"
                                                   value="<?php echo $inputCookieBoxRefuseBtnTxtColor; ?>">
                                            <div>
                                                <?php _ex('Default', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxRefuseBtnHoverTxtColor"
                                                   name="cookieBoxRefuseBtnHoverTxtColor"
                                                   value="<?php echo $inputCookieBoxRefuseBtnTxtHoverColor; ?>">
                                            <div>
                                                <?php _ex('Hover', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label
                                    class="col-sm-4 col-form-label"><?php _ex('Accept all Button Color', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxAcceptAllBtnColor"
                                                   name="cookieBoxAcceptAllBtnColor"
                                                   value="<?php echo $inputCookieBoxAcceptAllBtnColor; ?>">
                                            <div>
                                                <?php _ex('Default', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxAcceptAllBtnHoverColor"
                                                   name="cookieBoxAcceptAllBtnHoverColor"
                                                   value="<?php echo $inputCookieBoxAcceptAllBtnHoverColor; ?>">
                                            <div>
                                                <?php _ex('Hover', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label
                                    class="col-sm-4 col-form-label"><?php _ex('Accept all Button Text Color', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxAcceptAllBtnTxtColor"
                                                   name="cookieBoxAcceptAllBtnTxtColor"
                                                   value="<?php echo $inputCookieBoxAcceptAllBtnTxtColor; ?>">
                                            <div>
                                                <?php _ex('Default', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field"
                                                   id="cookieBoxAcceptAllBtnHoverTxtColor"
                                                   name="cookieBoxAcceptAllBtnHoverTxtColor"
                                                   value="<?php echo $inputCookieBoxAcceptAllBtnTxtHoverColor; ?>">
                                            <div>
                                                <?php _ex('Hover', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label
                                    class="col-sm-4 col-form-label"><?php _ex('Individual Settings Button Color', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxIndividualSettingsBtnColor"
                                                   name="cookieBoxIndividualSettingsBtnColor"
                                                   value="<?php echo $inputCookieBoxIndividualSettingsBtnColor; ?>">
                                            <div>
                                                <?php _ex('Default', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxIndividualSettingsBtnHoverColor"
                                                   name="cookieBoxIndividualSettingsBtnHoverColor"
                                                   value="<?php echo $inputCookieBoxIndividualSettingsBtnHoverColor; ?>">
                                            <div>
                                                <?php _ex('Hover', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label
                                    class="col-sm-4 col-form-label"><?php _ex('Individual Settings Button Text Color', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxIndividualSettingsBtnTxtColor"
                                                   name="cookieBoxIndividualSettingsBtnTxtColor"
                                                   value="<?php echo $inputCookieBoxIndividualSettingsBtnTxtColor; ?>">
                                            <div>
                                                <?php _ex('Default', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field"
                                                   id="cookieBoxIndividualSettingsBtnHoverTxtColor"
                                                   name="cookieBoxIndividualSettingsBtnHoverTxtColor"
                                                   value="<?php echo $inputCookieBoxIndividualSettingsBtnTxtHoverColor; ?>">
                                            <div>
                                                <?php _ex('Hover', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label
                                    class="col-sm-4 col-form-label"><?php _ex('Switch Button Background Color', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxBtnSwitchActiveBgColor"
                                                   name="cookieBoxBtnSwitchActiveBgColor"
                                                   value="<?php echo $inputCookieBoxBtnSwitchActiveBgColor; ?>">
                                            <div>
                                                <?php _ex('Active', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field"
                                                   id="cookieBoxBtnSwitchInactiveBgColor"
                                                   name="cookieBoxBtnSwitchInactiveBgColor"
                                                   value="<?php echo $inputCookieBoxBtnSwitchInactiveBgColor; ?>">
                                            <div>
                                                <?php _ex('Inactive', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label
                                    class="col-sm-4 col-form-label"><?php _ex('Switch Button Color', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxBtnSwitchActiveColor"
                                                   name="cookieBoxBtnSwitchActiveColor"
                                                   value="<?php echo $inputCookieBoxBtnSwitchActiveColor; ?>">
                                            <div>
                                                <?php _ex('Active', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxBtnSwitchInactiveColor"
                                                   name="cookieBoxBtnSwitchInactiveColor"
                                                   value="<?php echo $inputCookieBoxBtnSwitchInactiveColor; ?>">
                                            <div>
                                                <?php _ex('Inactive', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="cookieBoxBtnSwitchRound"
                                       class="col-sm-4 col-form-label"><?php _ex('Switch Button Round', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <button type="button"
                                            class="btn btn-sm btn-toggle mr-2<?php echo $switchCookieBoxBtnSwitchRound; ?>"
                                            data-toggle="button" data-switch-target="cookieBoxBtnSwitchRound"
                                            aria-pressed="<?php echo $inputCookieBoxBtnSwitchRound ? 'true' : 'false'; ?>"
                                            autocomplete="off">
                                        <span class="handle"></span>
                                    </button>
                                    <input type="hidden" id="cookieBoxBtnSwitchRound" name="cookieBoxBtnSwitchRound"
                                           value="<?php echo $inputCookieBoxBtnSwitchRound; ?>">
                                    <span data-toggle="tooltip"
                                          title="<?php echo esc_attr_x('Choose if the button is round (Status: ON) or squared (Status: OFF).', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-8 offset-sm-4">
                            <button type="submit"
                                    class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row no-gutters mb-4">
            <div class="col-12 col-md-8 rounded bg-light shadow-sm">
                <div class="px-3 pt-3 pb-4">
                    <h3 class="border-bottom mb-3"><?php _ex('Checkbox Settings', 'Backend / Cookie Box / Headline', 'borlabs-cookie'); ?></h3>
                    <div class="row">
                        <div class="col-12">

                            <div class="form-group row">
                                <label
                                    class="col-sm-4 col-form-label"><?php _ex('Checkbox Active', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxCheckboxActiveBgColor"
                                                   name="cookieBoxCheckboxActiveBgColor"
                                                   value="<?php echo $inputCookieBoxCheckboxActiveBgColor; ?>">
                                            <div>
                                                <?php _ex('Background Color', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field"
                                                   id="cookieBoxCheckboxActiveBorderColor"
                                                   name="cookieBoxCheckboxActiveBorderColor"
                                                   value="<?php echo $inputCookieBoxCheckboxActiveBorderColor; ?>">
                                            <div>
                                                <?php _ex('Border Color', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label
                                    class="col-sm-4 col-form-label"><?php _ex('Checkbox Inactive', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxCheckboxInactiveBgColor"
                                                   name="cookieBoxCheckboxInactiveBgColor"
                                                   value="<?php echo $inputCookieBoxCheckboxInactiveBgColor; ?>">
                                            <div>
                                                <?php _ex('Background Color', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field"
                                                   id="cookieBoxCheckboxInactiveBorderColor"
                                                   name="cookieBoxCheckboxInactiveBorderColor"
                                                   value="<?php echo $inputCookieBoxCheckboxInactiveBorderColor; ?>">
                                            <div>
                                                <?php _ex('Border Color', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label
                                    class="col-sm-4 col-form-label"><?php _ex('Checkbox Disabled', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxCheckboxDisabledBgColor"
                                                   name="cookieBoxCheckboxDisabledBgColor"
                                                   value="<?php echo $inputCookieBoxCheckboxDisabledBgColor; ?>">
                                            <div>
                                                <?php _ex('Background Color', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field"
                                                   id="cookieBoxCheckboxDisabledBorderColor"
                                                   name="cookieBoxCheckboxDisabledBorderColor"
                                                   value="<?php echo $inputCookieBoxCheckboxDisabledBorderColor; ?>">
                                            <div>
                                                <?php _ex('Border Color', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label
                                    class="col-sm-4 col-form-label"><?php _ex('Check Mark', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field"
                                                   id="cookieBoxCheckboxCheckMarkActiveColor"
                                                   name="cookieBoxCheckboxCheckMarkActiveColor"
                                                   value="<?php echo $inputCookieBoxCheckboxCheckMarkActiveColor; ?>">
                                            <div>
                                                <?php _ex('Active', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field"
                                                   id="cookieBoxCheckboxCheckMarkDisabledColor"
                                                   name="cookieBoxCheckboxCheckMarkDisabledColor"
                                                   value="<?php echo $inputCookieBoxCheckboxCheckMarkDisabledColor; ?>">
                                            <div>
                                                <?php _ex('Disabled', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-8 offset-sm-4">
                            <button type="submit"
                                    class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row no-gutters mb-4">
            <div class="col-12 col-md-8 rounded bg-light shadow-sm">
                <div class="px-3 pt-3 pb-4">
                    <h3 class="border-bottom mb-3"><?php _ex('Link Settings', 'Backend / Cookie Box / Headline', 'borlabs-cookie'); ?></h3>
                    <div class="row">
                        <div class="col-12">

                            <div class="form-group row">
                                <label
                                    class="col-sm-4 col-form-label"><?php _ex('Primary Link Color', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxPrimaryLinkColor"
                                                   name="cookieBoxPrimaryLinkColor"
                                                   value="<?php echo $inputCookieBoxPrimaryLinkColor; ?>">
                                            <div>
                                                <?php _ex('Default', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxPrimaryLinkHoverColor"
                                                   name="cookieBoxPrimaryLinkHoverColor"
                                                   value="<?php echo $inputCookieBoxPrimaryLinkHoverColor; ?>">
                                            <div>
                                                <?php _ex('Hover', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label
                                    class="col-sm-4 col-form-label"><?php _ex('Secondary Link Color', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxSecondaryLinkColor"
                                                   name="cookieBoxSecondaryLinkColor"
                                                   value="<?php echo $inputCookieBoxSecondaryLinkColor; ?>">
                                            <div>
                                                <?php _ex('Default', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxSecondaryLinkHoverColor"
                                                   name="cookieBoxSecondaryLinkHoverColor"
                                                   value="<?php echo $inputCookieBoxSecondaryLinkHoverColor; ?>">
                                            <div>
                                                <?php _ex('Hover', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label
                                    class="col-sm-4 col-form-label"><?php _ex('Refuse Link Color', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxRejectionLinkColor"
                                                   name="cookieBoxRejectionLinkColor"
                                                   value="<?php echo $inputCookieBoxRejectionLinkColor; ?>">
                                            <div>
                                                <?php _ex('Default', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" class="color-field" id="cookieBoxRejectionLinkHoverColor"
                                                   name="cookieBoxRejectionLinkHoverColor"
                                                   value="<?php echo $inputCookieBoxRejectionLinkHoverColor; ?>">
                                            <div>
                                                <?php _ex('Hover', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-8 offset-sm-4">
                            <button type="submit"
                                    class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row no-gutters mb-4">
            <div class="col-12 col-md-8 rounded-left bg-light shadow-sm">
                <div class="px-3 pt-3 pb-4">
                    <h3 class="border-bottom mb-3"><?php _ex('Text Settings - Legal Information', 'Backend / Cookie Box / Headline', 'borlabs-cookie'); ?></h3>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row">
                                <label for="cookieBoxTextDescriptionConfirmAge"
                                       class="col-sm-4 col-form-label"><?php _ex('Age', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="input-group input-group-sm w-75">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <input type="checkbox" name="cookieBoxShowTextDescriptionConfirmAge" value="1"
                                                   data-enable-target="cookieBoxTextDescriptionConfirmAge"
                                                <?php echo !empty($inputCookieBoxShowTextDescriptionConfirmAge) ? ' checked' : ''; ?>>
                                        </span>
                                        </div>
                                        <textarea class="form-control d-inline-block align-top w-75 mr-2"
                                                  id="cookieBoxTextDescriptionConfirmAge" name="cookieBoxTextDescriptionConfirmAge"
                                                  rows="5"<?php echo empty($inputCookieBoxShowTextDescriptionConfirmAge) ? ' disabled' : ''; ?>><?php echo $textareaCookieBoxTextDescriptionConfirmAge; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="cookieBoxTextDescriptionTechnology"
                                       class="col-sm-4 col-form-label"><?php _ex('Technology', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="input-group input-group-sm w-75">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <input type="checkbox" name="cookieBoxShowTextDescriptionTechnology" value="1"
                                                   data-enable-target="cookieBoxTextDescriptionTechnology"
                                                <?php echo !empty($inputCookieBoxShowTextDescriptionTechnology) ? ' checked' : ''; ?>>
                                        </span>
                                        </div>
                                        <textarea class="form-control d-inline-block align-top w-75 mr-2"
                                                  id="cookieBoxTextDescriptionTechnology" name="cookieBoxTextDescriptionTechnology"
                                                  rows="5"<?php echo empty($inputCookieBoxShowTextDescriptionTechnology) ? ' disabled' : ''; ?>><?php echo $textareaCookieBoxTextDescriptionTechnology; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="cookieBoxTextDescriptionPersonalData"
                                       class="col-sm-4 col-form-label"><?php _ex('Personal Data', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="input-group input-group-sm w-75">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <input type="checkbox" name="cookieBoxShowTextDescriptionPersonalData" value="1"
                                                   data-enable-target="cookieBoxTextDescriptionPersonalData"
                                                <?php echo !empty($inputCookieBoxShowTextDescriptionPersonalData) ? ' checked' : ''; ?>>
                                        </span>
                                        </div>
                                        <textarea class="form-control d-inline-block align-top w-75 mr-2"
                                                  id="cookieBoxTextDescriptionPersonalData" name="cookieBoxTextDescriptionPersonalData"
                                                  rows="5"<?php echo empty($inputCookieBoxShowTextDescriptionPersonalData) ? ' disabled' : ''; ?>><?php echo $textareaCookieBoxTextDescriptionPersonalData; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="cookieBoxTextDescriptionMoreInformation"
                                       class="col-sm-4 col-form-label"><?php _ex('More Information', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="input-group input-group-sm w-75">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <input type="checkbox" name="cookieBoxShowDescriptionMoreInformation" value="1"
                                                   data-enable-target="cookieBoxTextDescriptionMoreInformation"
                                                <?php echo !empty($inputCookieBoxShowDescriptionMoreInformation) ? ' checked' : ''; ?>>
                                        </span>
                                        </div>
                                        <textarea class="form-control d-inline-block align-top w-75 mr-2"
                                                  id="cookieBoxTextDescriptionMoreInformation" name="cookieBoxTextDescriptionMoreInformation"
                                                  rows="5"<?php echo empty($inputCookieBoxShowDescriptionMoreInformation) ? ' disabled' : ''; ?>><?php echo $textareaCookieBoxTextDescriptionMoreInformation; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="cookieBoxTextDescriptionNoObligation"
                                       class="col-sm-4 col-form-label"><?php _ex('No Obligation', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="input-group input-group-sm w-75">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <input type="checkbox" name="cookieBoxShowTextDescriptionNoObligation" value="1"
                                                   data-enable-target="cookieBoxTextDescriptionNoObligation"
                                                <?php echo !empty($inputCookieBoxShowTextDescriptionNoObligation) ? ' checked' : ''; ?>>
                                        </span>
                                        </div>
                                        <textarea class="form-control d-inline-block align-top w-75 mr-2"
                                                  id="cookieBoxTextDescriptionNoObligation" name="cookieBoxTextDescriptionNoObligation"
                                                  rows="5"<?php echo empty($inputCookieBoxShowTextDescriptionNoObligation) ? ' disabled' : ''; ?>><?php echo $textareaCookieBoxTextDescriptionNoObligation; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="cookieBoxTextDescriptionRevoke"
                                       class="col-sm-4 col-form-label"><?php _ex('Revoke', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="input-group input-group-sm w-75">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <input type="checkbox" name="cookieBoxShowTextDescriptionRevoke" value="1"
                                                   data-enable-target="cookieBoxTextDescriptionRevoke"
                                                <?php echo !empty($inputCookieBoxShowTextDescriptionRevoke) ? ' checked' : ''; ?>>
                                        </span>
                                        </div>
                                        <textarea class="form-control d-inline-block align-top w-75 mr-2"
                                                  id="cookieBoxTextDescriptionRevoke" name="cookieBoxTextDescriptionRevoke"
                                                  rows="5"<?php echo empty($inputCookieBoxShowTextDescriptionRevoke) ? ' disabled' : ''; ?>><?php echo $textareaCookieBoxTextDescriptionRevoke; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="cookieBoxTextDescriptionIndividualSettings"
                                       class="col-sm-4 col-form-label"><?php _ex('Individual Settings', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="input-group input-group-sm w-75">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <input type="checkbox" name="cookieBoxShowTextDescriptionIndividualSettings" value="1"
                                                   data-enable-target="cookieBoxTextDescriptionIndividualSettings"
                                                <?php echo !empty($inputCookieBoxShowTextDescriptionIndividualSettings) ? ' checked' : ''; ?>>
                                        </span>
                                        </div>
                                        <textarea class="form-control d-inline-block align-top w-75 mr-2"
                                                  id="cookieBoxTextDescriptionIndividualSettings" name="cookieBoxTextDescriptionIndividualSettings"
                                                  rows="5"<?php echo empty($inputCookieBoxShowTextDescriptionIndividualSettings) ? ' disabled' : ''; ?>><?php echo $textareaCookieBoxTextDescriptionIndividualSettings; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="cookieBoxTextDescriptionNonEUDataTransfer"
                                       class="col-sm-4 col-form-label"><?php _ex('Non-EU Data Transfer', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="input-group input-group-sm w-75">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <input type="checkbox" name="cookieBoxShowTextDescriptionNonEUDataTransfer" value="1"
                                                   data-enable-target="cookieBoxTextDescriptionNonEUDataTransfer"
                                                <?php echo !empty($inputCookieBoxShowTextDescriptionNonEUDataTransfer) ? ' checked' : ''; ?>>
                                        </span>
                                        </div>
                                        <textarea class="form-control d-inline-block align-top w-75 mr-2"
                                                  id="cookieBoxTextDescriptionNonEUDataTransfer" name="cookieBoxTextDescriptionNonEUDataTransfer"
                                                  rows="5"<?php echo empty($inputCookieBoxShowTextDescriptionNonEUDataTransfer) ? ' disabled' : ''; ?>><?php echo $textareaCookieBoxTextDescriptionNonEUDataTransfer; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-8 offset-sm-4">
                            <button type="submit"
                                    class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 rounded-right shadow-sm bg-tips text-light">
                <div class="px-3 pt-3 pb-3 mb-4">
                    <h3 class="border-bottom mb-3"><?php _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>
                    <h4><?php _ex('Legal Information', 'Backend / Cookie Box / Tips / Headline', 'borlabs-cookie'); ?></h4>
                    <p>
                        <?php _ex('You can select which legal information topics you want to show to a visitor.', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?>
                        <?php _ex('If you disable a text, make sure that its message appears or is part of another text displayed to a visitor.', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?>
                    </p>
                    <p>
                        <?php _ex('You have to inform a visitor about the following topics:', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?>
                    </p>
                    <ul class="unordered-list">
                        <li><?php _ex('A minimum age of 16 is required to consent to non-optional services.', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></li>
                        <li><?php _ex('The used technologies, for example: Cookies.', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></li>
                        <li><?php _ex('What type of personal data is used and for what purpose.', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></li>
                        <li><?php _ex('Where more information about each service can be found.', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></li>
                        <li><?php _ex('<sup>1</sup> That no consent is required to visit your website.', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></li>
                        <li><?php _ex('The visitor can revoke the consent any time.', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></li>
                        <li><?php _ex('<sup>1</sup> What happens when individual settings are applied.', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></li>
                        <li><?php _ex('<sup>2</sup> That personal data can be transferred to non-eu countries with insufficient data protection.', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></li>
                    </ul>
                    <p>
                        <?php _ex('<sup>1</sup> It is recommended to display this information.', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?>
                        <br>
                        <?php _ex('<sup>2</sup> You must display the &quot;Non-EU Data Transfer&quot; information if you use a service whose company is based in the USA.', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="row no-gutters mb-4">
            <div class="col-12 col-md-8 rounded bg-light shadow-sm">
                <div class="px-3 pt-3 pb-4">
                    <h3 class="border-bottom mb-3"><?php _ex('Text Settings - Cookie Box', 'Backend / Cookie Box / Headline', 'borlabs-cookie'); ?></h3>
                    <div class="row">
                        <div class="col-12">

                            <div class="form-group row">
                                <label for="cookieBoxTextHeadline"
                                       class="col-sm-4 col-form-label"><?php _ex('Headline', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieBoxTextHeadline" name="cookieBoxTextHeadline"
                                           value="<?php echo $inputCookieBoxTextHeadline; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieBoxTextDescription"
                                       class="col-sm-4 col-form-label"><?php _ex('Description', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <textarea class="form-control d-inline-block align-top w-75 mr-2"
                                              id="cookieBoxTextDescription" name="cookieBoxTextDescription"
                                              rows="5"><?php echo $textareaCookieBoxTextDescription; ?></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieBoxTextAcceptButton"
                                       class="col-sm-4 col-form-label"><?php _ex('Accept Cookie Button Text', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieBoxTextAcceptButton" name="cookieBoxTextAcceptButton"
                                           value="<?php echo $inputCookieBoxTextAcceptButton; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieBoxTextManageLink"
                                       class="col-sm-4 col-form-label"><?php _ex('Individual Cookie Preferences Link Text', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieBoxTextManageLink" name="cookieBoxTextManageLink"
                                           value="<?php echo $inputCookieBoxTextManageLink; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieBoxTextRefuseLink"
                                       class="col-sm-4 col-form-label"><?php _ex('Refuse Link Text', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieBoxTextRefuseLink" name="cookieBoxTextRefuseLink"
                                           value="<?php echo $inputCookieBoxTextRefuseLink; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieBoxTextCookieDetailsLink"
                                       class="col-sm-4 col-form-label"><?php _ex('Cookie Details Link Text', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieBoxTextCookieDetailsLink" name="cookieBoxTextCookieDetailsLink"
                                           value="<?php echo $inputCookieBoxTextCookieDetailsLink; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieBoxTextPrivacyLink"
                                       class="col-sm-4 col-form-label"><?php _ex('Privacy Link Text', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieBoxTextPrivacyLink" name="cookieBoxTextPrivacyLink"
                                           value="<?php echo $inputCookieBoxTextPrivacyLink; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieBoxTextImprintLink"
                                       class="col-sm-4 col-form-label"><?php _ex('Imprint Link Text', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieBoxTextImprintLink" name="cookieBoxTextImprintLink"
                                           value="<?php echo $inputCookieBoxTextImprintLink; ?>">
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-8 offset-sm-4">
                            <button type="submit"
                                    class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row no-gutters mb-4">
            <div class="col-12 col-md-8 rounded bg-light shadow-sm">
                <div class="px-3 pt-3 pb-4">
                    <h3 class="border-bottom mb-3"><?php _ex('Text Settings - Cookie Preferences', 'Backend / Cookie Box / Headline', 'borlabs-cookie'); ?></h3>
                    <div class="row">
                        <div class="col-12">

                            <div class="form-group row">
                                <label for="cookieBoxTextHeadline"
                                       class="col-sm-4 col-form-label"><?php _ex('Headline', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieBoxPreferenceTextHeadline" name="cookieBoxPreferenceTextHeadline"
                                           value="<?php echo $inputCookieBoxPreferenceTextHeadline; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieBoxTextDescription"
                                       class="col-sm-4 col-form-label"><?php _ex('Description', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <textarea class="form-control d-inline-block align-top w-75 mr-2"
                                              id="cookieBoxPreferenceTextDescription"
                                              name="cookieBoxPreferenceTextDescription"
                                              rows="5"><?php echo $textareaCookieBoxPreferenceTextDescription; ?></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieBoxPreferenceTextSaveButton"
                                       class="col-sm-4 col-form-label"><?php _ex('Save Preference Button Text', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieBoxPreferenceTextSaveButton"
                                           name="cookieBoxPreferenceTextSaveButton"
                                           value="<?php echo $inputCookieBoxPreferenceTextSaveButton; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieBoxPreferenceTextAcceptAllButton"
                                       class="col-sm-4 col-form-label"><?php _ex('Accept all Button Text', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieBoxPreferenceTextAcceptAllButton"
                                           name="cookieBoxPreferenceTextAcceptAllButton"
                                           value="<?php echo $inputCookieBoxPreferenceTextAcceptAllButton; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieBoxTextRefuseLink"
                                       class="col-sm-4 col-form-label"><?php _ex('Refuse Link Text', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieBoxPreferenceTextRefuseLink"
                                           name="cookieBoxPreferenceTextRefuseLink"
                                           value="<?php echo $inputCookieBoxPreferenceTextRefuseLink; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieBoxPreferenceTextBackLink"
                                       class="col-sm-4 col-form-label"><?php _ex('Back Link Text', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieBoxPreferenceTextBackLink" name="cookieBoxPreferenceTextBackLink"
                                           value="<?php echo $inputCookieBoxPreferenceTextBackLink; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieBoxPreferenceTextSwitchStatusActive"
                                       class="col-sm-4 col-form-label"><?php _ex('Switch Button Status Active', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieBoxPreferenceTextSwitchStatusActive"
                                           name="cookieBoxPreferenceTextSwitchStatusActive"
                                           value="<?php echo $inputCookieBoxPreferenceTextSwitchStatusActive; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieBoxPreferenceTextSwitchStatusInactive"
                                       class="col-sm-4 col-form-label"><?php _ex('Switch Button Status Inactive', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieBoxPreferenceTextSwitchStatusInactive"
                                           name="cookieBoxPreferenceTextSwitchStatusInactive"
                                           value="<?php echo $inputCookieBoxPreferenceTextSwitchStatusInactive; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieBoxTextRefuseLink"
                                       class="col-sm-4 col-form-label"><?php _ex('Show Cookie Information', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieBoxPreferenceTextShowCookieLink"
                                           name="cookieBoxPreferenceTextShowCookieLink"
                                           value="<?php echo $inputCookieBoxPreferenceTextShowCookieLink; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieBoxTextRefuseLink"
                                       class="col-sm-4 col-form-label"><?php _ex('Hide Cookie Information', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieBoxPreferenceTextHideCookieLink"
                                           name="cookieBoxPreferenceTextHideCookieLink"
                                           value="<?php echo $inputCookieBoxPreferenceTextHideCookieLink; ?>">
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-8 offset-sm-4">
                            <button type="submit"
                                    class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row no-gutters mb-4">
            <div class="col-12 col-md-8 rounded bg-light shadow-sm">
                <div class="px-3 pt-3 pb-4">
                    <h3 class="border-bottom mb-3"><?php _ex('Text Settings - Cookie Details Table', 'Backend / Cookie Box / Headline', 'borlabs-cookie'); ?></h3>
                    <div class="row">
                        <div class="col-12">

                            <div class="form-group row">
                                <label for="cookieBoxCookieDetailsTableAccept"
                                       class="col-sm-4 col-form-label"><?php _ex('Accept', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieBoxCookieDetailsTableAccept"
                                           name="cookieBoxCookieDetailsTableAccept"
                                           value="<?php echo $inputCookieBoxCookieDetailsTableAccept; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieBoxCookieDetailsTableName"
                                       class="col-sm-4 col-form-label"><?php _ex('Name', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieBoxCookieDetailsTableName" name="cookieBoxCookieDetailsTableName"
                                           value="<?php echo $inputCookieBoxCookieDetailsTableName; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieBoxCookieDetailsTableProvider"
                                       class="col-sm-4 col-form-label"><?php _ex('Provider', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieBoxCookieDetailsTableProvider"
                                           name="cookieBoxCookieDetailsTableProvider"
                                           value="<?php echo $inputCookieBoxCookieDetailsTableProvider; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieBoxCookieDetailsTablePurpose"
                                       class="col-sm-4 col-form-label"><?php _ex('Purpose', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieBoxCookieDetailsTablePurpose"
                                           name="cookieBoxCookieDetailsTablePurpose"
                                           value="<?php echo $inputCookieBoxCookieDetailsTablePurpose; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieBoxCookieDetailsTablePrivacyPolicy"
                                       class="col-sm-4 col-form-label"><?php _ex('Privacy Policy', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieBoxCookieDetailsTablePrivacyPolicy"
                                           name="cookieBoxCookieDetailsTablePrivacyPolicy"
                                           value="<?php echo $inputCookieBoxCookieDetailsTablePrivacyPolicy; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieBoxCookieDetailsTableHosts"
                                       class="col-sm-4 col-form-label"><?php _ex('Host(s)', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieBoxCookieDetailsTableHosts" name="cookieBoxCookieDetailsTableHosts"
                                           value="<?php echo $inputCookieBoxCookieDetailsTableHosts; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieBoxCookieDetailsTableCookieName"
                                       class="col-sm-4 col-form-label"><?php _ex('Cookie Name', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieBoxCookieDetailsTableCookieName"
                                           name="cookieBoxCookieDetailsTableCookieName"
                                           value="<?php echo $inputCookieBoxCookieDetailsTableCookieName; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieBoxCookieDetailsTableCookieExpiry"
                                       class="col-sm-4 col-form-label"><?php _ex('Cookie Expiry', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieBoxCookieDetailsTableCookieExpiry"
                                           name="cookieBoxCookieDetailsTableCookieExpiry"
                                           value="<?php echo $inputCookieBoxCookieDetailsTableCookieExpiry; ?>">
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-8 offset-sm-4">
                            <button type="submit"
                                    class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row no-gutters mb-4">
            <div class="col-12 col-md-8 rounded bg-light shadow-sm">
                <div class="px-3 pt-3 pb-4">
                    <h3 class="border-bottom mb-3"><?php _ex('Text Settings - Consent History Table', 'Backend / Cookie Box / Headline', 'borlabs-cookie'); ?></h3>
                    <div class="row">
                        <div class="col-12">

                            <div class="form-group row">
                                <label for="cookieBoxConsentHistoryTableDate"
                                       class="col-sm-4 col-form-label"><?php _ex('Date', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieBoxConsentHistoryTableDate" name="cookieBoxConsentHistoryTableDate"
                                           value="<?php echo $inputCookieBoxConsentHistoryTableDate; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieBoxConsentHistoryTableVersion"
                                       class="col-sm-4 col-form-label"><?php _ex('Version', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieBoxConsentHistoryTableVersion"
                                           name="cookieBoxConsentHistoryTableVersion"
                                           value="<?php echo $inputCookieBoxConsentHistoryTableVersion; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieBoxConsentHistoryTableConsents"
                                       class="col-sm-4 col-form-label"><?php _ex('Consents', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieBoxConsentHistoryTableConsents"
                                           name="cookieBoxConsentHistoryTableConsents"
                                           value="<?php echo $inputCookieBoxConsentHistoryTableConsents; ?>">
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-8 offset-sm-4">
                            <button type="submit"
                                    class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row no-gutters mb-4">
            <div class="col-12 col-md-8 rounded bg-light shadow-sm">
                <div class="px-3 pt-3 pb-4">
                    <h3 class="border-bottom mb-3"><?php _ex('Custom CSS', 'Backend / Cookie Box / Headline', 'borlabs-cookie'); ?></h3>
                    <div class="row">
                        <div class="col-12">

                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <label
                                        for="cookieBoxCustomCSS"><?php _ex('CSS', 'Backend / Cookie Box / Label', 'borlabs-cookie'); ?>
                                        <span data-toggle="tooltip"
                                              title="<?php echo esc_attr_x('Add your custom CSS to customize the <strong>Cookie Box</strong>.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i
                                                class="fas fa-lg fa-question-circle text-dark"></i></span></label>
                                    <div class="code-editor"><textarea data-borlabs-css-editor name="cookieBoxCustomCSS"
                                                                       id="cookieBoxCustomCSS"
                                                                       rows="5"><?php echo $textareaCookieBoxCustomCSS; ?></textarea>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-8 offset-sm-4">
                            <?php wp_nonce_field('borlabs_cookie_cookie_box_save'); ?>
                            <input type="hidden" name="action" value="save">
                            <button type="submit"
                                    class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Tip Modal -->
    <div class="modal fade" id="BorlabsCookieTipCookieBoxModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content shadow">
                <div class="modal-header bg-tips text-light">
                    <h5 class="modal-title"><?php _ex('Cookie Box', 'Backend / Cookie Box / Tips / Headline', 'borlabs-cookie'); ?></h5>
                    <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-12 col-md-8">
                                <img src="<?php echo $tipsImageCookieBox; ?>" alt="" style="width:100%;">
                            </div>
                            <div class="col-sm-12 col-md-4 pt-3">
                                <ol class="tips-list">
                                    <li value="1">
                                        <span><?php _ex('Cookie Box - Background Color', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></span>
                                    </li>
                                    <li value="2">
                                        <span><?php _ex('Cookie Box - Text Color', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></span>
                                    </li>
                                    <li value="3">
                                        <span><?php _ex('Border Radius - Box', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></span>
                                    </li>
                                    <li value="4">
                                        <span><?php _ex('Border Radius - Button', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></span>
                                    </li>
                                    <li value="5">
                                        <span><?php _ex('Button Background Color - Default / Hover', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></span>
                                    </li>
                                    <li value="6">
                                        <span><?php _ex('Button Text Color - Default / Hover', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></span>
                                    </li>
                                    <li value="7">
                                        <span><?php _ex('Primary Link Color - Default / Hover', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></span>
                                    </li>
                                    <li value="8">
                                        <span><?php _ex('Refuse Link Color - Default / Hover', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></span>
                                    </li>
                                    <li value="9">
                                        <span><?php _ex('Secondary Link Color - Default / Hover', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></span>
                                    </li>
                                    <li value="15">
                                        <span><?php _ex('Logo / Logo - HD', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></span>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="BorlabsCookieTipCookiePreferenceModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content shadow">
                <div class="modal-header bg-tips text-light">
                    <h5 class="modal-title"><?php _ex('Cookie Preferences', 'Backend / Cookie Box / Tips / Headline', 'borlabs-cookie'); ?></h5>
                    <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-12 col-md-8">
                                <img src="<?php echo $tipsImageCookiePreference; ?>" alt="" style="width:100%;">
                            </div>
                            <div class="col-sm-12 col-md-4 pt-3">
                                <ol class="tips-list">
                                    <li value="2">
                                        <span><?php _ex('Cookie Box - Text Color', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></span>
                                    </li>
                                    <li value="5">
                                        <span><?php _ex('Background Text Color - Default / Hover', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></span>
                                    </li>
                                    <li value="6">
                                        <span><?php _ex('Button Text Color - Default / Hover', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></span>
                                    </li>
                                    <li value="7">
                                        <span><?php _ex('Primary Link Color - Default / Hover', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></span>
                                    </li>
                                    <li value="10">
                                        <span><?php _ex('Accordion - Background Color', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></span>
                                    </li>
                                    <li value="11">
                                        <span><?php _ex('Accordion - Text Color', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></span>
                                    </li>
                                    <li value="12">
                                        <span><?php _ex('Border Radius - Accordion', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></span>
                                    </li>
                                    <li value="13">
                                        <span><?php _ex('Border Radius - Table', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></span>
                                    </li>
                                    <li value="14">
                                        <span><?php _ex('Switch Button Background Color - Active / Inactive', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></span>
                                    </li>
                                    <li value="14">
                                        <span><?php _ex('Switch Button Color - Active / Inactive', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></span>
                                    </li>
                                    <li value="15">
                                        <span><?php _ex('Logo / Logo - HD', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></span>
                                    </li>
                                    <li value="16">
                                        <span><?php _ex('Table - Background Color', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></span>
                                    </li>
                                    <li value="17">
                                        <span><?php _ex('Table - Text Color', 'Backend / Cookie Box / Tips / Text', 'borlabs-cookie'); ?></span>
                                    </li>
                                </ol>
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
