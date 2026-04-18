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
        <div class="_brlbs-box-wrap _brlbs-box-plus-wrap">
            <div class="_brlbs-box _brlbs-box-plus">
                <div class="cookie-box">
                    <div class="container">
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
                                    echo esc_attr($cookieBoxTextHeadline); ?>"
                                    aria-hidden="true"
                                >
                                <?php
                            } ?>

                            <span role="heading" aria-level="3" class="_brlbs-h3" id="CookieBoxTextHeadline"><?php
                                echo $cookieBoxTextHeadline; ?></span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <p id="CookieBoxTextDescription"><?php
                                    echo do_shortcode($cookieBoxTextDescription); ?></p>
                            </div>
                            <div class="col-md-6 col-12">

                                <?php
                                if (!empty($cookieGroups)) { ?>
                                    <fieldset>
                                        <legend class="sr-only"><?php
                                            echo $cookieBoxTextHeadline; ?></legend>
                                        <ul>
                                            <?php
                                            foreach ($cookieGroups as $groupData) {
                                                if (!empty($groupData->hasCookies)) { ?>
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
                                                                echo !empty($groupData->pre_selected) ? ' checked' : ''; ?>
                                                                <?php
                                                                echo $groupData->group_id === 'essential' ? ' disabled'
                                                                    : ''; ?>
                                                                data-borlabs-cookie-checkbox
                                                            >
                                                            <span class="_brlbs-checkbox-indicator"></span>
                                                        </label>
                                                        <p class="_brlbs-service-group-description"><?php
                                                            echo $groupData->description; ?></p>
                                                    </li>
                                                    <?php
                                                }
                                            } ?>
                                        </ul>
                                    </fieldset>

                                    <?php
                                } ?>


                            </div>
                        </div>

                        <div class="row  _brlbs-button-area">
                            <div class="col-md-6 col-12">
                                <p class="_brlbs-accept">
                                    <a
                                        href="#"
                                        tabindex="0"
                                        role="button"
                                        class="_brlbs-btn<?php
                                        echo $cookieBoxShowAcceptAllButton ? ' _brlbs-btn-accept-all'
                                            : ''; ?> _brlbs-cursor"
                                        data-cookie-accept-all
                                    >
                                        <?php
                                        echo $cookieBoxPreferenceTextAcceptAllButton; ?>
                                    </a>
                                </p>
                            </div>
                            <div class="col-md-6 col-12">
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
                            </div>
                            <div class="col-12">
                                <?php
                                if ($cookieBoxHideRefuseOption === false) { ?>
                                    <p class="<?php
                                    echo $cookieBoxRefuseOptionType === 'link' ? '_brlbs-refuse'
                                        : '_brlbs-refuse-btn'; ?>">
                                        <a
                                            class="<?php
                                            echo $cookieBoxRefuseOptionType === 'button' ? '_brlbs-btn '
                                                : ''; ?>_brlbs-cursor"
                                            href="#"
                                            tabindex="0"
                                            role="button"
                                            data-cookie-refuse
                                        >
                                            <?php
                                            echo $cookieBoxTextRefuseLink; ?>
                                        </a>
                                    </p>
                                    <?php
                                } ?>

                            </div>
                            <div class="col-12">
                            <?php if ($cookieBoxManageOptionType === 'button') {    ?>
                                    <p class="_brlbs-manage-btn">
                                        <a
                                            href="#"
                                            tabindex="0"
                                            role="button"
                                            class="_brlbs-cursor _brlbs-btn"
                                            data-cookie-individual
                                        >
                                            <?php
                                            echo $cookieBoxTextManageLink; ?>
                                        </a>
                                    </p>
                                <?php } ?>
                            </div>

                        </div>

                        <div class="_brlbs-legal row _brlbs-flex-center">
                            <?php if ($cookieBoxManageOptionType === 'link') { ?>

                                <span>
                                        <a
                                            href="#"
                                            tabindex="0"
                                            role="button"
                                            class="_brlbs-cursor"
                                            data-cookie-individual
                                        >
                                            <?php
                                            echo $cookieBoxTextManageLink; ?>
                                        </a>

                                    </span>
                                <span class="_brlbs-separator"></span>
                            <?php } ?>


                            <a href="#" class="_brlbs-cursor" tabindex="0" role="button" data-cookie-individual>
                                <?php
                                echo $cookieBoxTextCookieDetailsLink; ?>
                            </a>


                            <?php
                            if (!empty($cookieBoxPrivacyLink)) { ?>
                                <span class="_brlbs-separator"></span>
                                <a href="<?php
                                echo $cookieBoxPrivacyLink; ?>" tabindex="0" role="button">
                                    <?php
                                    echo $cookieBoxTextPrivacyLink; ?>
                                </a>
                                <?php
                            } ?>

                            <?php
                            if (!empty($cookieBoxImprintLink)) { ?>
                                <span class="_brlbs-separator"></span>
                                <a href="<?php
                                echo $cookieBoxImprintLink; ?>" tabindex="0" role="button">
                                    <?php
                                    echo $cookieBoxTextImprintLink; ?>
                                </a>
                                <?php
                            } ?>


                        </div>
                    </div>
                </div>

                <?php
                if (!empty($cookiePreferenceTemplateFile)) {
                    include $cookiePreferenceTemplateFile;
                }
                ?>
            </div>
        </div>
    </div>
</div>
