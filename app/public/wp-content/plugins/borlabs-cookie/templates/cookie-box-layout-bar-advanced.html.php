<div
    id="BorlabsCookieBox"
    class="BorlabsCookie"
    role="dialog"
    aria-labelledby="CookieBoxTextHeadline"
    aria-describedby="CookieBoxTextDescription"
    aria-modal="true"
>
    <div class="<?php
    echo $cookieBoxPosition; ?>" style="display: none;">
        <div class="_brlbs-bar-wrap">
            <div class="_brlbs-bar _brlbs-bar-advanced">
                <div class="cookie-box">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-12 col-sm-9">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="_brlbs-flex-left">
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
                                                    echo esc_attr($cookieBoxTextHeadline); ?>"
                                                    aria-hidden="true"
                                                >
                                            <?php } ?>
                                            <span role="heading" aria-level="3" class="_brlbs-h3" id="CookieBoxTextHeadline">
                                                <?php
                                                echo $cookieBoxTextHeadline; ?>
                                            </span>
                                        </div>

                                        <p id="CookieBoxTextDescription">
                                            <?php
                                            echo do_shortcode($cookieBoxTextDescription); ?>
                                        </p>
                                    </div>
                                </div>

                                <?php
                                if (! empty($cookieGroups)) { ?>
                                    <fieldset>
                                        <legend class="sr-only"><?php
                                            echo $cookieBoxTextHeadline; ?></legend>
                                        <ul
                                            <?php
                                            echo $cookieBoxShowLogo ? ' class="show-cookie-logo"' : ''; ?>
                                        >
                                            <?php
                                            foreach ($cookieGroups as $groupData) { ?>
                                                <?php
                                                if (! empty($groupData->hasCookies)) { ?>
                                                    <li>
                                                        <label class="_brlbs-checkbox">
                                                            <?php
                                                            echo $groupData->name; ?>
                                                            <input
                                                                id="checkbox-<?php
                                                                echo $groupData->group_id; ?>"
                                                                tabindex="0"
                                                                type="checkbox"
                                                                name="cookieGroup[]"
                                                                value="<?php
                                                                echo $groupData->group_id; ?>"
                                                                <?php
                                                                echo ! empty($groupData->pre_selected) ? ' checked' : ''; ?>
                                                                <?php
                                                                echo $groupData->group_id === 'essential' ? ' disabled'
                                                                    : ''; ?>
                                                                data-borlabs-cookie-checkbox
                                                            >
                                                            <span class="_brlbs-checkbox-indicator"></span>
                                                        </label>
                                                    </li>
                                                <?php
                                                } ?>
                                            <?php
                                            } ?>
                                        </ul>
                                    </fieldset>
                                    <?php
                                } ?>
                            </div>

                            <div class="col-12 col-sm-3">
                                <?php
                                if ($cookieBoxShowAcceptAllButton) { ?>
                                    <p class="_brlbs-accept">
                                        <a
                                            href="#"
                                            tabindex="0"
                                            role="button"
                                            class="_brlbs-btn _brlbs-btn-accept-all _brlbs-cursor"
                                            data-cookie-accept-all
                                        >
                                            <?php
                                            echo $cookieBoxPreferenceTextAcceptAllButton; ?>
                                        </a>
                                    </p>

                                    <p class="_brlbs-accept">
                                        <a
                                            href="#"
                                            tabindex="0"
                                            role="button"
                                            id="CookieBoxSaveButton"
                                            class="_brlbs-btn _brlbs-cursor"
                                            data-cookie-accept
                                        >
                                            <?php
                                            echo $cookieBoxPreferenceTextSaveButton; ?>
                                        </a>
                                    </p>
                                <?php
                                } else { ?>
                                    <p class="_brlbs-accept">
                                        <a
                                            href="#"
                                            tabindex="0"
                                            role="button"
                                            id="CookieBoxSaveButton"
                                            class="_brlbs-btn<?php
                                            echo $cookieBoxShowAcceptAllButton ? ' _brlbs-btn-accept-all'
                                                : ''; ?> _brlbs-cursor"
                                            data-cookie-accept
                                        >
                                            <?php
                                            echo $cookieBoxTextAcceptButton; ?>
                                        </a>
                                    </p>
                                <?php
                                } ?>

                                <?php
                                if ($cookieBoxHideRefuseOption === false) { ?>
                                    <p class="<?php
                                    echo $cookieBoxRefuseOptionType === 'link' ? '_brlbs-refuse'
                                        : '_brlbs-refuse-btn'; ?>">
                                        <a
                                            href="#"
                                            tabindex="0"
                                            role="button"
                                            class="<?php
                                            echo $cookieBoxRefuseOptionType === 'button' ? '_brlbs-btn '
                                                : ''; ?>_brlbs-cursor"
                                            data-cookie-refuse
                                        >
                                            <?php
                                            echo $cookieBoxTextRefuseLink; ?>
                                        </a>
                                    </p>
                                <?php
                                } ?>

                                <p class="<?php echo $cookieBoxManageOptionType === 'button' ? '_brlbs-manage-btn' : '_brlbs-manage'; ?> ">
                                    <a
                                        href="#"
                                        tabindex="0"
                                        role="button"
                                        class="_brlbs-cursor <?php echo $cookieBoxManageOptionType === 'button' ? '_brlbs-btn' : ''; ?> "
                                        data-cookie-individual
                                    >
                                        <?php
                                        echo $cookieBoxTextManageLink; ?>
                                    </a>
                                </p>

                                <p class="_brlbs-legal">
                                    <a
                                        href="#"
                                        tabindex="0"
                                        role="button"
                                        class="_brlbs-cursor"
                                        data-cookie-individual
                                    >
                                        <?php
                                        echo $cookieBoxTextCookieDetailsLink; ?>
                                    </a>

                                    <?php
                                    if (! empty($cookieBoxPrivacyLink)) { ?>
                                        <span class="_brlbs-separator"></span>
                                        <a
                                            tabindex="0"
                                            href="<?php
                                            echo $cookieBoxPrivacyLink; ?>"
                                        >
                                            <?php
                                            echo $cookieBoxTextPrivacyLink; ?>
                                        </a>
                                    <?php
                                    } ?>

                                    <?php
                                    if (! empty($cookieBoxImprintLink)) { ?>
                                        <span class="_brlbs-separator"></span>
                                        <a
                                            tabindex="0"
                                            href="<?php
                                            echo $cookieBoxImprintLink; ?>"
                                        >
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

                <?php
                if (! empty($cookiePreferenceTemplateFile)) {
                    include $cookiePreferenceTemplateFile;
                } ?>
            </div>
        </div>
    </div>
</div>
