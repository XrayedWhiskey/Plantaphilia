<div
    class="cookie-preference"
    aria-hidden="true"
    role="dialog"
    aria-describedby="CookiePrefDescription"
    aria-modal="true"
>
    <div class="container not-visible">
        <div class="row no-gutters">
            <div class="col-12">
                <div class="row no-gutters align-items-top">
                    <div class="col-12">
                        <div class="_brlbs-flex-center">
                        <?php
                        if ($cookieBoxShowLogo) { ?>
                            <img
                                width="32"
                                height="32"
                                class="cookie-logo"
                                src="<?php
                                echo $cookieBoxLogo; ?>"
                                srcset="<?php
                                echo implode(', ', $cookieBoxLogoSrcSet); ?>"
                                alt="<?php
                                echo esc_attr($cookieBoxPreferenceTextHeadline); ?>"
                            >
                        <?php } ?>
                            <span role="heading" aria-level="3" class="_brlbs-h3"><?php
                                echo $cookieBoxPreferenceTextHeadline; ?></span>
                        </div>

                        <p id="CookiePrefDescription">
                            <?php
                            echo do_shortcode($cookieBoxPreferenceTextDescription); ?>
                        </p>

                        <div class="row no-gutters align-items-center">
                            <div class="col-12 <?php echo ($cookieBoxHideRefuseOption === false && $cookieBoxPreferenceRefuseOptionType === 'button') ? 'col-sm-10' : 'col-sm-7'; ?>">
                                <p class="_brlbs-accept">
                                    <?php
                                    if ($cookieBoxShowAcceptAllButton) { ?>
                                        <a
                                            href="#"
                                            class="_brlbs-btn _brlbs-btn-accept-all _brlbs-cursor"
                                            tabindex="0"
                                            role="button"
                                            data-cookie-accept-all
                                        >
                                            <?php
                                            echo $cookieBoxPreferenceTextAcceptAllButton; ?>
                                        </a>
                                        <?php
                                    } ?>

                                    <a
                                        href="#"
                                        id="CookiePrefSave"
                                        tabindex="0"
                                        role="button"
                                        class="_brlbs-btn _brlbs-cursor"
                                        data-cookie-accept
                                    >
                                        <?php
                                        echo $cookieBoxPreferenceTextSaveButton; ?>
                                    </a>

                                    <?php
                                    if ($cookieBoxHideRefuseOption === false && $cookieBoxPreferenceRefuseOptionType === 'button') { ?>
                                        <a
                                            href="#"
                                            class="_brlbs-btn _brlbs-refuse-btn _brlbs-cursor"
                                            tabindex="0"
                                            role="button"
                                            data-cookie-refuse
                                        >
                                            <?php
                                            echo $cookieBoxPreferenceTextRefuseLink; ?>
                                        </a>
                                    <?php
                                    }
                                    ?>
                                </p>
                            </div>

                            <div class="col-12 <?php echo ($cookieBoxHideRefuseOption === false && $cookieBoxPreferenceRefuseOptionType === 'button') ? 'col-sm-2' : 'col-sm-5'; ?>">
                                <p class="_brlbs-refuse">
                                    <a
                                        href="#"
                                        class="_brlbs-cursor"
                                        tabindex="0"
                                        role="button"
                                        data-cookie-back
                                    >
                                        <?php
                                        echo $cookieBoxPreferenceTextBackLink; ?>
                                    </a>

                                    <?php
                                    if ($cookieBoxHideRefuseOption === false && $cookieBoxPreferenceRefuseOptionType === 'link') { ?>
                                        <span class="_brlbs-separator"></span>
                                        <a
                                            href="#"
                                            class="_brlbs-cursor"
                                            tabindex="0"
                                            role="button"
                                            data-cookie-refuse
                                        >
                                            <?php
                                            echo $cookieBoxPreferenceTextRefuseLink; ?>
                                        </a>
                                        <?php
                                    } ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div data-cookie-accordion>
                    <?php
                    if (! empty($cookieGroups)) { ?>
                        <fieldset>
                            <legend class="sr-only"><?php echo esc_attr($cookieBoxPreferenceTextHeadline); ?></legend>

                            <?php
                            foreach ($cookieGroups as $groupData) { ?>
                                <?php
                                if (! empty($groupData->cookies)) { ?>
                                    <div class="bcac-item">
                                        <div class="d-flex flex-row">
                                            <label class="w-75">
                                                <span role="heading" aria-level="4" class="_brlbs-h4"><?php
                                                    echo esc_html($groupData->name); ?> (<?php
                                                    echo count($groupData->cookies); ?>)</span>
                                            </label>

                                            <div class="w-25 text-right">
                                                <?php
                                                if ($groupData->group_id !== 'essential') { ?>
                                                    <label class="_brlbs-btn-switch">
                                                        <span class="sr-only"><?php
                                                            echo esc_html($groupData->name); ?></span>
                                                        <input
                                                            tabindex="0"
                                                            id="borlabs-cookie-group-<?php
                                                            echo $groupData->group_id; ?>"
                                                            type="checkbox"
                                                            name="cookieGroup[]"
                                                            value="<?php
                                                            echo $groupData->group_id; ?>"
                                                            <?php
                                                            echo ! empty($groupData->pre_selected) ? ' checked' : ''; ?>
                                                            data-borlabs-cookie-switch
                                                        />
                                                        <span class="_brlbs-slider"></span>
                                                        <span
                                                            class="_brlbs-btn-switch-status"
                                                            data-active="<?php
                                                            echo $cookieBoxPreferenceTextSwitchStatusActive; ?>"
                                                            data-inactive="<?php
                                                            echo $cookieBoxPreferenceTextSwitchStatusInactive; ?>">
                                                        </span>
                                                    </label>
                                                    <?php
                                                } ?>
                                            </div>
                                        </div>

                                        <div class="d-block">
                                            <p><?php
                                                echo $groupData->description; ?></p>

                                            <p class="text-center">
                                                <a
                                                    href="#"
                                                    class="_brlbs-cursor d-block"
                                                    tabindex="0"
                                                    role="button"
                                                    data-cookie-accordion-target="<?php
                                                    echo $groupData->group_id; ?>"
                                                >
                                                    <span data-cookie-accordion-status="show">
                                                        <?php
                                                        echo $cookieBoxPreferenceTextShowCookieLink; ?>
                                                    </span>

                                                    <span data-cookie-accordion-status="hide" class="borlabs-hide">
                                                        <?php
                                                        echo $cookieBoxPreferenceTextHideCookieLink; ?>
                                                    </span>
                                                </a>
                                            </p>
                                        </div>

                                        <div
                                            class="borlabs-hide"
                                            data-cookie-accordion-parent="<?php
                                            echo $groupData->group_id; ?>"
                                        >
                                            <?php
                                            foreach ($groupData->cookies as $cookieData) { ?>
                                                <table>
                                                    <?php
                                                    if ($groupData->group_id !== 'essential') { ?>
                                                        <tr>
                                                            <th scope="row"><?php
                                                                echo $cookieBoxCookieDetailsTableAccept; ?></th>
                                                            <td>
                                                                <label class="_brlbs-btn-switch _brlbs-btn-switch--textRight">
                                                                    <span class="sr-only"><?php
                                                                        echo esc_html($cookieData->name); ?></span>
                                                                    <input
                                                                        id="borlabs-cookie-<?php
                                                                        echo $cookieData->cookie_id; ?>"
                                                                        tabindex="0"
                                                                        type="checkbox" data-cookie-group="<?php
                                                                    echo $groupData->group_id; ?>"
                                                                        name="cookies[<?php
                                                                        echo $groupData->group_id; ?>][]"
                                                                        value="<?php
                                                                        echo $cookieData->cookie_id; ?>"
                                                                        <?php
                                                                        echo ! empty($groupData->pre_selected) ? ' checked'
                                                                            : ''; ?>
                                                                        data-borlabs-cookie-switch
                                                                    />

                                                                    <span class="_brlbs-slider"></span>

                                                                    <span
                                                                        class="_brlbs-btn-switch-status"
                                                                        data-active="<?php
                                                                        echo $cookieBoxPreferenceTextSwitchStatusActive; ?>"
                                                                        data-inactive="<?php
                                                                        echo $cookieBoxPreferenceTextSwitchStatusInactive; ?>"
                                                                        aria-hidden="true">
                                                                    </span>
                                                                </label>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    } ?>

                                                    <tr>
                                                        <th scope="row"><?php
                                                            echo $cookieBoxCookieDetailsTableName; ?></th>
                                                        <td>
                                                            <label>
                                                                <?php
                                                                echo esc_html($cookieData->name); ?>
                                                            </label>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <th scope="row"><?php
                                                            echo $cookieBoxCookieDetailsTableProvider; ?></th>
                                                        <td><?php
                                                            echo $cookieData->provider; ?></td>
                                                    </tr>

                                                    <?php
                                                    if (! empty($cookieData->purpose)) { ?>
                                                        <tr>
                                                            <th scope="row"><?php
                                                                echo $cookieBoxCookieDetailsTablePurpose; ?></th>
                                                            <td><?php
                                                                echo $cookieData->purpose; ?></td>
                                                        </tr>
                                                        <?php
                                                    } ?>

                                                    <?php
                                                    if (! empty($cookieData->privacy_policy_url)) { ?>
                                                        <tr>
                                                            <th scope="row"><?php
                                                                echo $cookieBoxCookieDetailsTablePrivacyPolicy; ?></th>
                                                            <td class="_brlbs-pp-url">
                                                                <a
                                                                    href="<?php
                                                                    echo esc_url($cookieData->privacy_policy_url); ?>"
                                                                    target="_blank"
                                                                    rel="nofollow noopener noreferrer"
                                                                >
                                                                    <?php
                                                                    echo esc_url($cookieData->privacy_policy_url); ?>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    } ?>

                                                    <?php
                                                    if (! empty($cookieData->hosts)) { ?>
                                                        <tr>
                                                            <th scope="row"><?php
                                                                echo $cookieBoxCookieDetailsTableHosts; ?></th>
                                                            <td><?php
                                                                echo implode(', ', $cookieData->hosts); ?></td>
                                                        </tr>
                                                        <?php
                                                    } ?>

                                                    <?php
                                                    if (! empty($cookieData->cookie_name)) { ?>
                                                        <tr>
                                                            <th scope="row"><?php
                                                                echo $cookieBoxCookieDetailsTableCookieName; ?></th>
                                                            <td><?php
                                                                echo esc_html($cookieData->cookie_name); ?></td>
                                                        </tr>
                                                        <?php
                                                    } ?>

                                                    <?php
                                                    if (! empty($cookieData->cookie_expiry)) { ?>
                                                        <tr>
                                                            <th scope="row"><?php
                                                                echo $cookieBoxCookieDetailsTableCookieExpiry; ?></th>
                                                            <td><?php
                                                                echo esc_html($cookieData->cookie_expiry); ?></td>
                                                        </tr>
                                                        <?php
                                                    } ?>
                                                </table>
                                                <?php
                                            } ?>
                                        </div>
                                    </div>
                                    <?php
                                } ?>
                                <?php
                            } ?>
                            </fieldset>
                        <?php
                    } ?>
                </div>

                <div class="d-flex justify-content-between">
                    <p class="_brlbs-branding flex-fill">
                        <?php
                        if ($supportBorlabsCookie) { ?>
                            <a
                                href="<?php
                                echo esc_attr_x(
                                    'https://borlabs.io/borlabs-cookie/',
                                    'Frontend / Global / URL',
                                    'borlabs-cookie'
                                ); ?>"
                                target="_blank"
                                rel="nofollow noopener noreferrer"
                            >
                                <img src="<?php
                                echo $supportBorlabsCookieLogo; ?>" alt="Borlabs Cookie" width="16" height="16">
                                <?php
                                echo ' ' ?>
                                <?php
                                _ex('powered by Borlabs Cookie', 'Frontend / Global / Text', 'borlabs-cookie'); ?>
                            </a>
                            <?php
                        } ?>
                    </p>

                    <p class="_brlbs-legal flex-fill">
                        <?php
                        if (! empty($cookieBoxPrivacyLink)) { ?>
                            <a href="<?php
                            echo $cookieBoxPrivacyLink; ?>">
                                <?php
                                echo $cookieBoxTextPrivacyLink; ?>
                            </a>
                            <?php
                        } ?>

                        <?php
                        if (! empty($cookieBoxPrivacyLink) && ! empty($cookieBoxImprintLink)) { ?>
                            <span class="_brlbs-separator"></span>
                            <?php
                        } ?>

                        <?php
                        if (! empty($cookieBoxImprintLink)) { ?>
                            <a href="<?php
                            echo $cookieBoxImprintLink; ?>">
                                <?php
                                echo $cookieBoxTextImprintLink; ?>
                            </a>
                            <?php
                        } ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
