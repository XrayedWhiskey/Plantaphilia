<div
    id="BorlabsCookieBox"
    class="BorlabsCookie"
    role="dialog"
    aria-describedby="CookieBoxTextDescription"
    aria-modal="true"
>
    <div class="<?php
    echo $cookieBoxPosition; ?>" style="display: none;">
        <div class="_brlbs-bar-wrap">
            <div class="_brlbs-bar _brlbs-bar-slim">
                <div class="cookie-box">
                    <div class="container">
                        <div class="row align-items-center">
                            <?php
                            if ($cookieBoxShowLogo) { ?>
                                <div class="col-2 col-sm-1 text-center _brlbs-no-padding-right">
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
                                </div>
                            <?php
                            } ?>

                            <div class="<?php
                            echo $cookieBoxShowLogo ? 'col-10 col-sm-8' : 'col-12 col-sm-9' ?>">
                                <p id="CookieBoxTextDescription">
                                    <?php
                                    echo do_shortcode($cookieBoxTextDescription); ?>
                                </p>

                                <p class="_brlbs-actions">
                                    <?php if($cookieBoxManageOptionType === 'link'){ ?>

                                        <span class="_brlbs-manage">
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
                                        <span class="_brlbs-separator"></span>

                                    </span>

                                    <?php } ?>

                                    <?php
                                    if ($cookieBoxHideRefuseOption === false) { ?>
                                        <span class="_brlbs-refuse">
                                            <a
                                                href="#"
                                                tabindex="0"
                                                role="button"
                                                class="_brlbs-cursor"
                                                data-cookie-refuse
                                            >
                                                <?php
                                                echo $cookieBoxTextRefuseLink; ?>
                                            </a>
                                        </span>
                                    <?php
                                    } ?>
                                </p>
                            </div>

                            <div class="col-12 col-sm-3">
                                <p class="_brlbs-actions-mobile">
                                    <?php if($cookieBoxManageOptionType === 'link'){ ?>

                                        <span class="_brlbs-manage">
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
                                    <?php } ?>

                                    <?php
                                    if ($cookieBoxHideRefuseOption === false) { ?>
                                        <span class="_brlbs-refuse">
                                            <span class="_brlbs-separator"></span>
                                            <a
                                                href="#"
                                                tabindex="0"
                                                role="button"
                                                class="_brlbs-cursor"
                                                data-cookie-refuse
                                            >
                                                <?php
                                                echo $cookieBoxTextRefuseLink; ?>
                                            </a>
                                        </span>
                                    <?php
                                    } ?>
                                </p>

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

                                <?php if($cookieBoxManageOptionType === 'button'){ ?>

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
