<?php

if (\BorlabsCookie\Cookie\Backend\License::getInstance()->isPluginUnlocked()) {
    ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?page=borlabs-cookie">Borlabs Cookie</a></li>
            <li class="breadcrumb-item active"
                aria-current="page"><?php
                _ex('Settings', 'Backend / Settings / Breadcrumb', 'borlabs-cookie'); ?></li>
        </ol>
    </nav>

    <?php
    echo \BorlabsCookie\Cookie\Backend\Messages::getInstance()->getAll(); ?>

    <form action="?page=borlabs-cookie-settings" method="post" id="BorlabsCookieForm" class="needs-validation"
          novalidate>
        <div class="row no-gutters mb-4">
            <div class="col-12 col-md-8 rounded-left bg-light shadow-sm">
                <div class="px-3 pt-3 pb-4">
                    <h3 class="border-bottom mb-3"><?php
                        _ex('General Settings', 'Backend / Settings / Headline', 'borlabs-cookie'); ?></h3>
                    <div class="row">
                        <div class="col-12">

                            <div class="form-group row align-items-center">
                                <label for="cookieStatus"
                                       class="col-sm-4 col-form-label"><?php
                                    _ex(
                                        'Borlabs Cookie Status',
                                        'Backend / Settings / Label',
                                        'borlabs-cookie'
                                    ); ?></label>
                                <div class="col-sm-8">
                                    <button type="button"
                                            class="btn btn-sm btn-toggle mr-2<?php
                                            echo $switchCookieStatus; ?>"
                                            data-toggle="button" data-switch-target="cookieStatus" aria-pressed="<?php
                                    echo $inputCookieStatus ? 'true' : 'false'; ?>">
                                        <span class="handle"></span>
                                    </button>
                                    <input type="hidden" name="cookieStatus" id="cookieStatus"
                                           value="<?php
                                           echo $inputCookieStatus; ?>">
                                    <span data-toggle="tooltip"
                                          title="<?php
                                          echo esc_attr_x(
                                              'Activates Borlabs Cookie on your website. Displays the <strong>Cookie Box</strong> and blocks iframes and other external media.',
                                              'Backend / Settings / Tooltip',
                                              'borlabs-cookie'
                                          ); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>
                            <div class="form-group row align-items-center">
                                <label for="setupMode"
                                       class="col-sm-4 col-form-label"><?php
                                    _ex('Setup Mode', 'Backend / Settings / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <button type="button"
                                            class="btn btn-sm btn-toggle mr-2<?php
                                            echo $switchSetupModeStatus; ?>"
                                            data-toggle="button" data-switch-target="setupMode" aria-pressed="<?php
                                    echo $inputSetupModeStatus ? 'true' : 'false'; ?>"
                                            autocomplete="off">
                                        <span class="handle"></span>
                                    </button>
                                    <input type="hidden" name="setupMode" id="setupMode"
                                           value="<?php
                                           echo $inputSetupModeStatus; ?>">
                                    <span data-toggle="tooltip"
                                          title="<?php
                                          echo esc_attr_x(
                                              'With Setup Mode enabled, you can test your setup without having to enable <strong>Borlabs Cookie Status</strong>. Only you will see the cookie box on your website.',
                                              'Backend / Settings / Tooltip',
                                              'borlabs-cookie'
                                          ); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label
                                    class="col-sm-4 col-form-label"><?php
                                    _ex('Cookie Version', 'Backend / Settings / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <?php
                                    _ex('Version', 'Backend / Settings / Text', 'borlabs-cookie'); ?> <?php
                                    echo $cookieVersion; ?>
                                    <span data-toggle="tooltip"
                                          title="<?php
                                          echo esc_attr_x(
                                              'Shows the version of the cookie of Borlabs Cookie. Increases with activating <strong>Update Cookie Version & Force Re-Selection</strong>.',
                                              'Backend / Settings / Tooltip',
                                              'borlabs-cookie'
                                          ); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="updateCookieVersion"
                                       class="col-sm-4 col-form-label"><?php
                                    _ex(
                                        'Update Cookie Version &amp; Force Re-Selection',
                                        'Backend / Settings / Label',
                                        'borlabs-cookie'
                                    ); ?></label>
                                <div class="col-sm-8">
                                    <button type="button" class="btn btn-sm btn-toggle mr-2" data-toggle="button"
                                            data-switch-target="updateCookieVersion" aria-pressed="false"
                                            autocomplete="off">
                                        <span class="handle"></span>
                                    </button>
                                    <input type="hidden" name="updateCookieVersion" id="updateCookieVersion" value="0">
                                    <span data-toggle="tooltip"
                                          title="<?php
                                          echo esc_attr_x(
                                              'Updates the version of the cookie of Borlabs Cookie. This will cause the <strong>Cookie Box</strong> to reappear for visitors who have already selected an option.',
                                              'Backend / Settings / Tooltip',
                                              'borlabs-cookie'
                                          ); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="cookieBeforeConsent"
                                       class="col-sm-4 col-form-label"><?php
                                    _ex(
                                        'Cookie before Consent',
                                        'Backend / Settings / Label',
                                        'borlabs-cookie'
                                    ); ?></label>
                                <div class="col-sm-8">
                                    <button type="button"
                                            class="btn btn-sm btn-toggle mr-2<?php
                                            echo $switchCookieBeforeConsent; ?>"
                                            data-toggle="button" data-switch-target="cookieBeforeConsent"
                                            aria-pressed="<?php
                                            echo $inputCookieBeforeConsent ? 'true' : 'false'; ?>">
                                        <span class="handle"></span>
                                    </button>
                                    <input type="hidden" name="cookieBeforeConsent" id="cookieBeforeConsent"
                                           value="<?php
                                           echo $inputCookieBeforeConsent; ?>">
                                    <span data-toggle="tooltip"
                                          title="<?php
                                          echo esc_attr_x(
                                              'A cookie is set before the visitor\'s consent. Thus even if a visitor does not select an option in the <strong>Cookie Box</strong> their visit is logged and and their later selections are assigned to him in the consent history.',
                                              'Backend / Settings / Tooltip',
                                              'borlabs-cookie'
                                          ); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <?php
                            if (!empty($inputCookieBeforeConsent)) {
                                ?>
                                <div class="form-group row align-items-center">
                                    <div class="col-sm-8 offset-4">
                                        <div
                                            class="alert alert-danger mt-2"><?php _ex('Depending on applicable law, this option may not be allowed to be turned on.', 'Backend / Settings / Alert Message', 'borlabs-cookie'); ?></div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>

                            <div class="form-group row align-items-center">
                                <label for="aggregateCookieConsent"
                                       class="col-sm-4 col-form-label"><?php
                                    _ex(
                                        'Aggregate Cookie Consent',
                                        'Backend / Settings / Label',
                                        'borlabs-cookie'
                                    ); ?></label>
                                <div class="col-sm-8">
                                    <button type="button"
                                            class="btn btn-sm btn-toggle mr-2<?php
                                            echo $switchAggregateCookieConsent; ?>"
                                            data-toggle="button" data-switch-target="aggregateCookieConsent"
                                            aria-pressed="<?php
                                            echo $inputAggregateCookieConsent ? 'true' : 'false'; ?>">
                                        <span class="handle"></span>
                                    </button>
                                    <input type="hidden" name="aggregateCookieConsent" id="aggregateCookieConsent"
                                           value="<?php
                                           echo $inputAggregateCookieConsent; ?>">
                                    <span data-toggle="tooltip"
                                          title="<?php
                                          echo esc_attr_x(
                                              'Aggregate the cookie consents of all WordPress sites in one table.',
                                              'Backend / Settings / Tooltip',
                                              'borlabs-cookie'
                                          ); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="cookiesForBots"
                                       class="col-sm-4 col-form-label"><?php
                                    _ex(
                                        'Cookies for Bots/Crawlers',
                                        'Backend / Settings / Label',
                                        'borlabs-cookie'
                                    ); ?></label>
                                <div class="col-sm-8">
                                    <button type="button"
                                            class="btn btn-sm btn-toggle mr-2<?php
                                            echo $switchCookiesForBots; ?>"
                                            data-toggle="button" data-switch-target="cookiesForBots"
                                            aria-pressed="<?php
                                            echo $inputCookiesForBots ? 'true' : 'false'; ?>">
                                        <span class="handle"></span>
                                    </button>
                                    <input type="hidden" name="cookiesForBots" id="cookiesForBots"
                                           value="<?php
                                           echo $inputCookiesForBots; ?>">
                                    <span data-toggle="tooltip"
                                          title="<?php
                                          echo esc_attr_x(
                                              'A bot/crawler is treated like a visitor who accepted all cookies.',
                                              'Backend / Settings / Tooltip',
                                              'borlabs-cookie'
                                          ); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="respectDoNotTrack"
                                       class="col-sm-4 col-form-label"><?php
                                    _ex(
                                        'Respect "Do Not Track"',
                                        'Backend / Settings / Label',
                                        'borlabs-cookie'
                                    ); ?></label>
                                <div class="col-sm-8">
                                    <button type="button"
                                            class="btn btn-sm btn-toggle mr-2<?php
                                            echo $switchRespectDoNotTrack; ?>"
                                            data-toggle="button" data-switch-target="respectDoNotTrack"
                                            aria-pressed="<?php
                                            echo $inputRespectDoNotTrack ? 'true' : 'false'; ?>">
                                        <span class="handle"></span>
                                    </button>
                                    <input type="hidden" name="respectDoNotTrack" id="respectDoNotTrack"
                                           value="<?php
                                           echo $inputRespectDoNotTrack; ?>">
                                    <span data-toggle="tooltip"
                                          title="<?php
                                          echo esc_attr_x(
                                              'A visitor with active <strong>"Do Not Track"</strong> setting will not see the <strong>Cookie Box</strong> and Borlabs Cookie automatically selects the <strong>Refuse Link</strong> option.',
                                              'Backend / Settings / Tooltip',
                                              'borlabs-cookie'
                                          ); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <?php
                            if ($doNotTrackIsActive === true) {
                                ?>
                                <div class="form-group row align-items-center">
                                    <div class="col-sm-8 offset-4">
                                        <div
                                            class="alert alert-danger mt-2"><?php
                                            _ex(
                                                'You have enabled <strong>Do Not Track</strong> in your browser therefore you will not see the <strong>Cookie Box</strong> on your website.',
                                                'Backend / Settings / Alert Message',
                                                'borlabs-cookie'
                                            ); ?></div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>

                            <div class="form-group row align-items-center">
                                <label for="reloadAfterConsent"
                                       class="col-sm-4 col-form-label"><?php
                                    _ex(
                                        'Reload After Consent',
                                        'Backend / Settings / Label',
                                        'borlabs-cookie'
                                    ); ?></label>
                                <div class="col-sm-8">
                                    <button type="button"
                                            class="btn btn-sm btn-toggle mr-2<?php
                                            echo $switchReloadAfterConsent; ?>"
                                            data-toggle="button" data-switch-target="reloadAfterConsent"
                                            aria-pressed="<?php
                                            echo $inputReloadAfterConsent ? 'true' : 'false'; ?>">
                                        <span class="handle"></span>
                                    </button>
                                    <input type="hidden" id="reloadAfterConsent" name="reloadAfterConsent"
                                           value="<?php
                                           echo $inputReloadAfterConsent; ?>">
                                    <span data-toggle="tooltip"
                                          title="<?php
                                          echo esc_attr_x(
                                              'If activated the website will be reloaded after the visitor saves their consent.',
                                              'Backend / Settings / Tooltip',
                                              'borlabs-cookie'
                                          ); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <?php
                            if ($inputReloadAfterConsent === 1) {
                                ?>
                                <div class="form-group row align-items-center">
                                    <div class="col-sm-8 offset-4">
                                        <div
                                            class="alert alert-danger mt-2"><?php
                                            _ex(
                                                'If this option is active, most likely all visits will be counted as "Direct visits" and the origin will be lost. We therefore recommend not to activate this option!',
                                                'Backend / Settings / Alert Message',
                                                'borlabs-cookie'
                                            ); ?></div>
                                        <div
                                            class="alert alert-warning mt-2"><?php
                                            _ex(
                                                'Only activate this option if you need to reload your page after consent. Less than 1&#37; of all Borlabs Cookie customers need this option enabled. If you don\'t know if you need this option, you won\'t need it.',
                                                'Backend / Settings / Alert Message',
                                                'borlabs-cookie'
                                            ); ?></div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>

                            <div class="form-group row align-items-center">
                                <label for="reloadAfterOptOut"
                                       class="col-sm-4 col-form-label"><?php
                                    _ex(
                                        'Reload After Opt-Out',
                                        'Backend / Settings / Label',
                                        'borlabs-cookie'
                                    ); ?></label>
                                <div class="col-sm-8">
                                    <button type="button"
                                            class="btn btn-sm btn-toggle mr-2<?php
                                            echo $switchReloadAfterOptOut; ?>"
                                            data-toggle="button" data-switch-target="reloadAfterOptOut"
                                            aria-pressed="<?php
                                            echo $inputReloadAfterOptOut ? 'true' : 'false'; ?>">
                                        <span class="handle"></span>
                                    </button>
                                    <input type="hidden" id="reloadAfterOptOut" name="reloadAfterOptOut"
                                           value="<?php
                                           echo $inputReloadAfterOptOut; ?>">
                                    <span data-toggle="tooltip"
                                          title="<?php
                                          echo esc_attr_x(
                                              'If activated the website will be reloaded after the visitor opts out of a service.',
                                              'Backend / Settings / Tooltip',
                                              'borlabs-cookie'
                                          ); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="jQueryHandle"
                                       class="col-sm-4 col-form-label"><?php
                                    _ex('jQuery Handle', 'Backend / Settings / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="jQueryHandle" name="jQueryHandle"
                                           value="<?php
                                           echo $inputJqueryHandle; ?>" autocapitalize="off"
                                           autocomplete="off" autocorrect="off" spellcheck="false" required>
                                    <span data-toggle="tooltip"
                                          title="<?php
                                          echo esc_attr_x(
                                              'If you are not using the default WordPress jQuery library, define the jQuery library that Borlabs Cookie should use by specifying the jQuery handle.',
                                              'Backend / Settings / Tooltip',
                                              'borlabs-cookie'
                                          ); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                    <div
                                        class="invalid-feedback"><?php
                                        _ex(
                                            'This is a required field and cannot be empty.',
                                            'Backend / Global / Validation Message',
                                            'borlabs-cookie'
                                        ); ?></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">
                                    <?php
                                    _ex('Display Meta Box', 'Backend / Settings / Label', 'borlabs-cookie'); ?>
                                    <span data-toggle="tooltip"
                                          title="<?php
                                          echo esc_attr_x(
                                              'Display the Borlabs Cookie <strong>Meta Box</strong> on the selected post types. The <strong>Meta Box</strong> allows you to add custom JavaScript on specific pages.',
                                              'Backend / Settings / Tooltip',
                                              'borlabs-cookie'
                                          ); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </label>
                                <div class="col-sm-8">
                                    <?php
                                    if (! empty($postTypes)) {
                                        foreach ($postTypes as $postType) {
                                            ?>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input"
                                                       id="metaBox-<?php
                                                       echo esc_attr($postType->name); ?>"
                                                       name="metaBox[<?php
                                                       echo esc_attr($postType->name); ?>]"
                                                       value="1"<?php
                                                echo ! empty($enabledPostTypes[$postType->name]) ? ' checked' : ''; ?>>
                                                <label class="custom-control-label mr-2"
                                                       for="metaBox-<?php
                                                       echo esc_attr($postType->name); ?>"><?php
                                                    echo esc_html($postType->label); ?>
                                                    <em>(<?php
                                                        echo esc_attr($postType->name); ?>)</em></label>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-8 offset-sm-4">
                            <button type="submit"
                                    class="btn btn-primary btn-sm"><?php
                                _ex(
                                    'Save all settings',
                                    'Backend / Global / Button Title',
                                    'borlabs-cookie'
                                ); ?></button>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-12 col-md-4 rounded-right shadow-sm bg-tips text-light">
                <div class="px-3 pt-3 pb-3 mb-4">
                    <h3 class="border-bottom mb-3"><?php
                        _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>

                    <div class="accordion" id="accordionGeneralSettings">

                        <button type="button" class="collapsed" data-toggle="collapse"
                                data-target="#accordionGeneralSettingsOne" aria-expanded="true">
                            <?php
                            _ex(
                                'What is the Cookie Version?',
                                'Backend / Settings / Tips / Headline',
                                'borlabs-cookie'
                            ); ?>
                        </button>

                        <div id="accordionGeneralSettingsOne" class="collapse show"
                             data-parent="#accordionGeneralSettings">
                            <p>
                                <?php
                                _ex(
                                    'The cookie of Borlabs Cookie is assigned a version number. It is used to ask the visitor again for their consent if changes have been made to the cookies. If the version number in the cookie differs from the current version number, the <strong>Cookie Box</strong> appears to the visitor.',
                                    'Backend / Settings / Tips / Text',
                                    'borlabs-cookie'
                                ); ?>
                            </p>
                        </div>

                        <button type="button" class="collapsed" data-toggle="collapse"
                                data-target="#accordionGeneralSettingsTwo">
                            <?php
                            _ex('What is the Meta Box?', 'Backend / Settings / Tips / Headline', 'borlabs-cookie'); ?>
                        </button>

                        <div id="accordionGeneralSettingsTwo" class="collapse" data-parent="#accordionGeneralSettings">
                            <p>
                                <?php
                                _ex(
                                    'If active the <strong>Meta Box</strong> is displayed in the selected post types. This allows you to execute code (JavaScript, HTML, shortcodes) on the page and e.g. trigger a conversion pixel.',
                                    'Backend / Settings / Tips / Text',
                                    'borlabs-cookie'
                                ); ?>
                            </p>
                        </div>

                        <button type="button" class="collapsed" data-toggle="collapse"
                                data-target="#accordionGeneralSettingsThree">
                            <?php
                            _ex(
                                'Aggregate Cookie Consent',
                                'Backend / Settings / Tips / Headline',
                                'borlabs-cookie'
                            ); ?>
                        </button>

                        <div id="accordionGeneralSettingsThree" class="collapse"
                             data-parent="#accordionGeneralSettings">
                            <?php
                            if (! defined('WP_ALLOW_MULTISITE') || WP_ALLOW_MULTISITE === false) {
                                ?>
                                <div class="alert alert-info">
                                    <?php
                                    _ex(
                                        'Your WordPress is not a Multisite Network, therefore you do not have do modify this setting in most cases.',
                                        'Backend / Settings / Tips / Alert Message',
                                        'borlabs-cookie'
                                    ); ?>
                                </div>
                                <?php
                            }
                            ?>
                            <p>
                                <?php
                                _ex(
                                    'Depending on your Multisite Network settings you can separate the cookie consents, or have to aggregate them to get a complete cookie setting history.',
                                    'Backend / Settings / Tips / Text',
                                    'borlabs-cookie'
                                ); ?>
                            </p>
                            <p>
                                <strong><?php
                                    _ex(
                                        'When you have to aggregate the cookie consent:',
                                        'Backend / Settings / Tips / Text',
                                        'borlabs-cookie'
                                    ); ?></strong>
                                <br>
                                <?php
                                _ex(
                                    '- if one site is using only the domain (e.g. <strong><em>example.com</em></strong>) and the other site a subdomain (e.g. <strong><em>shop.example.com</em></strong>)',
                                    'Backend / Settings / Tips / Text',
                                    'borlabs-cookie'
                                ); ?>
                                <br>
                                <?php
                                _ex(
                                    '- if one site is using the root folder <strong>/</strong> and the other site a subfolder (e.g. <strong>/shop</strong>)',
                                    'Backend / Settings / Tips / Text',
                                    'borlabs-cookie'
                                ); ?>
                            </p>
                            <p>
                                <strong><?php
                                    _ex(
                                        'When you can seperate the cookie consent:',
                                        'Backend / Settings / Tips / Text',
                                        'borlabs-cookie'
                                    ); ?></strong>
                                <br>
                                <?php
                                _ex(
                                    '- if all sites are using different domains (e.g. <strong><em>example.com</em></strong> and <strong><em>my-example.com</em></strong>) or different subdomains (e.g. <strong><em>www.example.com</em></strong> and <strong><em>shop.example.com</em></strong>)',
                                    'Backend / Settings / Tips / Text',
                                    'borlabs-cookie'
                                ); ?>
                                <br>
                                <?php
                                _ex(
                                    '- if all sites are using different subfolders (e.g. <strong>/en</strong> and <strong>/de</strong>)',
                                    'Backend / Settings / Tips / Text',
                                    'borlabs-cookie'
                                ); ?>
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="row no-gutters mb-4">
            <div class="col-12 col-md-8 rounded-left bg-light shadow-sm">
                <div class="px-3 pt-3 pb-4">
                    <h3 class="border-bottom mb-3"><?php
                        _ex('Cookie Settings', 'Backend / Settings / Headline', 'borlabs-cookie'); ?></h3>
                    <div class="row">
                        <div class="col-12">

                            <div class="form-group row align-items-center">
                                <label for="automaticCookieDomainAndPath"
                                       class="col-sm-4 col-form-label"><?php
                                    _ex(
                                        'Automatic Domain and Path Detection',
                                        'Backend / Settings / Label',
                                        'borlabs-cookie'
                                    ); ?></label>
                                <div class="col-sm-8">
                                    <button type="button"
                                            class="btn btn-sm btn-toggle mr-2<?php
                                            echo $switchAutomaticCookieDomainAndPath; ?>"
                                            data-toggle="button" data-switch-target="automaticCookieDomainAndPath"
                                            data-disable="cookieDomain,cookiePath" data-disable-on="true"
                                            aria-pressed="<?php
                                            echo $inputAutomaticCookieDomainAndPath ? 'true' : 'false'; ?>">
                                        <span class="handle"></span>
                                    </button>
                                    <input type="hidden" name="automaticCookieDomainAndPath"
                                           id="automaticCookieDomainAndPath"
                                           value="<?php
                                           echo $inputAutomaticCookieDomainAndPath; ?>">
                                    <span data-toggle="tooltip"
                                          title="<?php
                                          echo esc_attr_x(
                                              'Borlabs Cookie tries to automatically detect domain and path.',
                                              'Backend / Settings / Tooltip',
                                              'borlabs-cookie'
                                          ); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieDomain"
                                       class="col-sm-4 col-form-label"><?php
                                    _ex('Domain', 'Backend / Settings / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieDomain" name="cookieDomain"
                                           value="<?php
                                           echo $inputCookieDomain; ?>"
                                           required<?php
                                    echo ! empty($inputAutomaticCookieDomainAndPath) ? ' disabled' : ''; ?>>
                                    <span data-toggle="tooltip"
                                          title="<?php
                                          echo esc_attr_x(
                                              'Specify the domain scheme for which the cookie is valid. Example: If you enter <strong><em>example.com</em></strong> the cookie is also valid for subdomains like <strong><em>shop.example.com</em></strong>.',
                                              'Backend / Settings / Tooltip',
                                              'borlabs-cookie'
                                          ); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                    <div
                                        class="invalid-feedback"><?php
                                        _ex(
                                            'This is a required field and cannot be empty.',
                                            'Backend / Global / Validation Message',
                                            'borlabs-cookie'
                                        ); ?></div>
                                    <?php
                                    if ($cookieDomainIsDifferent === true) {
                                        ?>
                                        <div
                                            class="alert alert-danger mt-2"><?php
                                            _ex(
                                                'Your configured domain is different from the website domain. The setting may be incorrect and will cause the Cookie Box to reappear on each page.',
                                                'Backend / Settings / Alert Message',
                                                'borlabs-cookie'
                                            ); ?></div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookiePath"
                                       class="col-sm-4 col-form-label"><?php
                                    _ex('Path', 'Backend / Settings / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookiePath" name="cookiePath" value="<?php
                                    echo $inputCookiePath; ?>"
                                           required<?php
                                    echo ! empty($inputAutomaticCookieDomainAndPath) ? ' disabled' : ''; ?>>
                                    <span data-toggle="tooltip"
                                          title="<?php
                                          echo sprintf(
                                              _x(
                                                  'The path for which the cookie is valid. Default path: <strong>%s</strong>',
                                                  'Backend / Settings / Tooltip',
                                                  'borlabs-cookie'
                                              ),
                                              $networkPath
                                          ); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                    <div
                                        class="invalid-feedback"><?php
                                        _ex(
                                            'This is a required field and cannot be empty.',
                                            'Backend / Global / Validation Message',
                                            'borlabs-cookie'
                                        ); ?></div>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="cookieSameSite" class="col-sm-4 col-form-label"><?php
                                    _ex(
                                        'SameSite Attribute',
                                        'Backend / Settings / Label',
                                        'borlabs-cookie'
                                    ); ?></label>
                                <div class="col-sm-8">
                                    <select class="form-control form-control form-control-sm d-inline-block w-75 mr-2"
                                            id="cookieSameSite" name="cookieSameSite">
                                        <option<?php echo $optionCookieSameSiteLax; ?>
                                            value="Lax">Lax</option>
                                        <option<?php echo $optionCookieSameSiteNone; ?>
                                            value="None">None</option>
                                    </select>
                                    <span data-toggle="tooltip" title="<?php echo esc_attr_x('Choose the cookie restriction. We recommend using the <strong>Lax</strong> option.', 'Backend / Cookie Box / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <?php
                            if ($sameSiteError === true) {
                                ?>
                                <div class="form-group row align-items-center">
                                    <div class="col-sm-8 offset-4">
                                        <div
                                            class="alert alert-danger mt-2"><?php
                                            _ex(
                                                'The setting <strong>SameSite: None</strong> requires that the <strong>Secure Attribute</strong> setting is enabled.',
                                                'Backend / Settings / Alert Message',
                                                'borlabs-cookie'
                                            ); ?></div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>

                            <div class="form-group row align-items-center">
                                <label for="cookieSecure"
                                       class="col-sm-4 col-form-label"><?php
                                    _ex(
                                        'Secure Attribute',
                                        'Backend / Settings / Label',
                                        'borlabs-cookie'
                                    ); ?></label>
                                <div class="col-sm-8">
                                    <button type="button"
                                            class="btn btn-sm btn-toggle mr-2<?php
                                            echo $switchCookieSecure; ?>"
                                            data-toggle="button" data-switch-target="cookieSecure"
                                            aria-pressed="<?php
                                            echo $inputCookieSecure ? 'true' : 'false'; ?>">
                                        <span class="handle"></span>
                                    </button>
                                    <input type="hidden" name="cookieSecure"
                                           id="cookieSecure"
                                           value="<?php
                                           echo $inputCookieSecure; ?>">
                                    <span data-toggle="tooltip"
                                          title="<?php
                                          echo sprintf(
                                              _x(
                                                  'A cookie with the Secure attribute is sent to the server only in case of an encrypted request via the HTTPS protocol.',
                                                  'Backend / Settings / Tooltip',
                                                  'borlabs-cookie'
                                              ),
                                              $networkPath
                                          ); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                            <?php
                            if ($secureAttributError === true) {
                                ?>
                                <div class="form-group row align-items-center">
                                    <div class="col-sm-8 offset-4">
                                        <div
                                            class="alert alert-danger mt-2"><?php
                                            _ex(
                                                'Your website is not using a SSL certification, so you need to disable the <strong>Secure Attribute</strong> setting.',
                                                'Backend / Settings / Alert Message',
                                                'borlabs-cookie'
                                            ); ?></div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>

                            <div class="form-group row">
                                <label for="cookieLifetime"
                                       class="col-sm-4 col-form-label"><?php
                                    _ex(
                                        'Cookie Lifetime in Days',
                                        'Backend / Settings / Label',
                                        'borlabs-cookie'
                                    ); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieLifetime" name="cookieLifetime"
                                           value="<?php
                                           echo $inputCookieLifetime; ?>" required>
                                    <span data-toggle="tooltip"
                                          title="<?php
                                          echo esc_attr_x(
                                              'Number of days until the visitor will be asked again to choose their cookie perference. Remember to adjust the <strong>Cookie Expiry</strong> information of the Borlabs Cookie under <strong>Cookies &gt; Borlabs Cookie</strong>.',
                                              'Backend / Settings / Tooltip',
                                              'borlabs-cookie'
                                          ); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                    <div
                                        class="invalid-feedback"><?php
                                        _ex(
                                            'This is a required field and cannot be empty.',
                                            'Backend / Global / Validation Message',
                                            'borlabs-cookie'
                                        ); ?></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cookieLifetimeEssentialOnly"
                                       class="col-sm-4 col-form-label"><?php
                                    _ex(
                                        'Cookie Lifetime in Days - Essential Only',
                                        'Backend / Settings / Label',
                                        'borlabs-cookie'
                                    ); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2"
                                           id="cookieLifetimeEssentialOnly" name="cookieLifetimeEssentialOnly"
                                           value="<?php
                                           echo $inputCookieLifetimeEssentialOnly; ?>" required>
                                    <span data-toggle="tooltip"
                                          title="<?php
                                          echo esc_attr_x(
                                              'Number of days until the visitor will be asked again to choose their cookie preference, if the user has only given consent to essential cookies. Remember to adjust the <strong>Cookie Expiry</strong> information of the Borlabs Cookie under <strong>Cookies &gt; Borlabs Cookie</strong>.',
                                              'Backend / Settings / Tooltip',
                                              'borlabs-cookie'
                                          ); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                    <div
                                        class="invalid-feedback"><?php
                                        _ex(
                                            'This is a required field and cannot be empty.',
                                            'Backend / Global / Validation Message',
                                            'borlabs-cookie'
                                        ); ?></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="crossDomainCookie"
                                       class="col-sm-4 col-form-label"><?php
                                    _ex(
                                        'Cross Domain Cookie',
                                        'Backend / Settings / Label',
                                        'borlabs-cookie'
                                    ); ?></label>
                                <div class="col-sm-8">
                                    <textarea class="form-control d-inline-block align-top w-75 mr-2"
                                              name="crossDomainCookie" id="crossDomainCookie" rows="3"
                                              autocapitalize="off" autocomplete="off" autocorrect="off"
                                              spellcheck="false"><?php
                                        echo $textareaCrossDomainCookie; ?></textarea>
                                    <span data-toggle="tooltip"
                                          title="<?php
                                          echo esc_attr_x(
                                              'Add one URL per line. Insert WordPress Address (URL). URL must end with <strong>/</strong>. Cookie selections will be shared between sites.',
                                              'Backend / Settings / Tooltip',
                                              'borlabs-cookie'
                                          ); ?>"><i
                                            class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-8 offset-sm-4">
                            <?php
                            wp_nonce_field('borlabs_cookie_settings_save'); ?>
                            <input type="hidden" name="action" value="save">
                            <button type="submit"
                                    class="btn btn-primary btn-sm"><?php
                                _ex(
                                    'Save all settings',
                                    'Backend / Global / Button Title',
                                    'borlabs-cookie'
                                ); ?></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4 rounded-right shadow-sm bg-tips text-light">
                <div class="px-3 pt-3 pb-3 mb-4">
                    <h3 class="border-bottom mb-3"><?php
                        _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>

                    <h4><?php
                        _ex('Cross Domain Cookie', 'Backend / Settings / Tips / Headline', 'borlabs-cookie'); ?></h4>
                    <p><?php
                        _ex(
                            'The visitor\'s selection is transferred to all specified domains and WordPress installations, provided the websites are set up in the same way (<strong>Cookie Version</strong>, <strong>Cookie</strong> and <strong>Cookie Group</strong> IDs must match). Any consent or modification by the visitor will be shared with the specified domains.',
                            'Backend / Settings / Tips / Text',
                            'borlabs-cookie'
                        ); ?></p>
                    <p class="text-center"><a
                            href="<?php
                            _ex(
                                'https://borlabs.io/kb/set-up-cross-domain-cookie/?utm_source=Borlabs+Cookie&utm_medium=Cross+Domain+Cookie+Link&utm_campaign=Analysis',
                                'Backend / Settings / Tips / URL',
                                'borlabs-cookie'
                            ); ?>"
                            rel="nofollow noopener noreferrer" target="_blank"
                            class="text-light"><?php
                            _ex(
                                'More information about Cross Domain Cookie',
                                'Backend / Settings / Tips / Text',
                                'borlabs-cookie'
                            ); ?>
                            <i class="fas fa-external-link-alt"></i></a></p>
                </div>
            </div>
        </div>

    </form>
    <?php
} else {
    echo \BorlabsCookie\Cookie\Backend\License::getInstance()->getLicenseMessageActivateKey();
}
?>
