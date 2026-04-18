<div
    id="BorlabsCookieBox"
    class="BorlabsCookie"
    role="dialog"
    aria-describedby="CookieBoxTextDescription"
    aria-modal="true"
>
    <div class="<?php
    echo $cookieBoxPosition; ?>" style="display: none;">
        <div class="_brlbs-box-wrap">
            <div class="_brlbs-box _brlbs-box-slim">
                <div class="cookie-box">
                    <div class="container">
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
                                            echo esc_attr($cookieBoxTextHeadline); ?>"
                                            aria-hidden="true"
                                        >
                                    <?php
                                    } ?>

                                    <p id="CookieBoxTextDescription"><?php
                                        echo do_shortcode($cookieBoxTextDescription); ?></p>
                                </div>

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
