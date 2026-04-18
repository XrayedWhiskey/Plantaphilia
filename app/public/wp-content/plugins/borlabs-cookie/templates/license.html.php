<?php
echo \BorlabsCookie\Cookie\Backend\Messages::getInstance()->getAll();

if (!empty($licenseData->licenseKey)) {
    ?>
    <div class="row no-gutters mb-4">
        <div class="col-12 col-md-8 rounded bg-light shadow-sm">
            <div class="px-3 pt-3 pb-4">
                <h3 class="border-bottom mb-3"><?php _ex('Your License Information', 'Backend / License / Headline', 'borlabs-cookie'); ?></h3>
                <div class="row">
                    <div class="col-12">

                        <div class="form-group row align-items-center">
                            <label class="col-sm-4 col-form-label"><?php _ex('License Status', 'Backend / License / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8 text-<?php echo $licenseStatus === 'expired' ? 'danger' : 'success'; ?>">
                                <?php echo $licenseStatusMessage; ?>
                            </div>
                        </div>

                        <?php
                        if ($licenseHideLicenseInformation === false) {
                            ?>
                            <div class="form-group row align-items-center">
                                <label class="col-sm-4 col-form-label"><?php _ex('License Type', 'Backend / License / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <?php echo $licenseTypeTitle; ?>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label class="col-sm-4 col-form-label"><?php _ex('Updates Until', 'Backend / License / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <?php echo $licenseValidUntil; ?>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label class="col-sm-4 col-form-label"><?php _ex('Support Until', 'Backend / License / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <?php echo $licenseSupportUntil ?>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label class="col-sm-4 col-form-label"><?php _ex('Max Websites', 'Backend / License / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <?php echo $licenseMaxSites; ?>
                                </div>
                            </div>

                            <form action="?page=borlabs-cookie-license" method="post">
                                <div class="row">
                                    <div class="col-sm-8 offset-sm-4">
                                        <?php wp_nonce_field('borlabs_cookie_license_refresh'); ?>
                                        <input type="hidden" name="action" value="refresh">
                                        <button type="submit" class="btn btn-primary btn-sm"><?php _ex('Refresh License Information', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                                    </div>
                                </div>
                            </form>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>

<div class="row no-gutters mb-4">
    <div class="col-12 col-md-8 rounded-left bg-light shadow-sm">
        <div class="px-3 pt-3 pb-4">
            <form action="?page=borlabs-cookie-license" method="post" id="BorlabsCookieForm">
                <h3 class="border-bottom mb-3"><?php
                    _ex('Your License', 'Backend / License / Headline', 'borlabs-cookie'); ?></h3>
                <div class="row">
                    <div class="col-12">

                        <div class="form-group row">
                            <label for="licenseKey" class="col-sm-4 col-form-label"><?php
                                _ex('License Key', 'Backend / License / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="licenseKey" name="licenseKey" value="" placeholder="<?php
                                echo $inputLicenseKey; ?>" required>
                                <span data-toggle="tooltip" title="<?php
                                echo esc_attr_x(
                                    'Enter your License Key.',
                                    'Backend / License / Tooltip',
                                    'borlabs-cookie'
                                ); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                                <div class="invalid-feedback"><?php
                                    _ex(
                                        'This is a required field and cannot be empty.',
                                        'Backend / Global / Validation Message',
                                        'borlabs-cookie'
                                    ); ?></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-8 offset-sm-4">
                                <?php
                                wp_nonce_field('borlabs_cookie_license_register'); ?>
                                <input type="hidden" name="action" value="register">
                                <button type="submit" class="btn btn-primary btn-sm"><?php
                                    _ex('Save', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="col-12 col-md-4 rounded-right shadow-sm bg-tips text-light">
        <div class="px-3 pt-3 pb-3 mb-4">
            <h3 class="border-bottom mb-3"><?php
                _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>

            <div class="accordion" id="accordionLicense">

                <button type="button" class="collapsed" data-toggle="collapse" data-target="#accordionLicenseOne">
                    <?php
                    _ex(
                        'Transferring a license key to a new website',
                        'Backend / License / Tips / Headline',
                        'borlabs-cookie'
                    ); ?>
                </button>

                <div id="accordionLicenseOne" class="collapse" data-parent="#accordionLicense">
                    <p>
                        <?php
                        _ex(
                            'If you decide to use your license on a new website, you can just enter your license key on your new website. You will get an option to unlink your old website and use the license on the new website.',
                            'Backend / License / Tips / Text',
                            'borlabs-cookie'
                        ); ?>
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
if (! empty($licenseData->licenseKey)) {
    ?>
    <div class="row no-gutters mb-4">
        <div class="col-12 col-md-8 rounded bg-light shadow-sm">
            <div class="px-3 pt-3 pb-4">
                <form action="?page=borlabs-cookie-license" method="post">
                    <h3 class="border-bottom mb-3"><?php
                        _ex('Remove License', 'Backend / License / Headline', 'borlabs-cookie'); ?></h3>
                    <div class="row">
                        <div class="col-12">

                            <div class="form-group row align-items-center">
                                <label class="col-sm-4 col-form-label"><?php
                                    _ex('Confirmation', 'Backend / License / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="removeLicenseStatus" value="1" data-enable-target="removeLicenseBtn">
                                        <label class="custom-control-label mr-2" for="removeLicenseStatus"><?php
                                            _ex('Confirmed', 'Backend / Global / Text', 'borlabs-cookie'); ?></label>
                                        <span class="align-middle" data-toggle="tooltip" title="<?php
                                        echo esc_attr_x(
                                            'Please confirm that you want to remove your license data from this website. After the license data is removed you are able to enter your new license key.',
                                            'Backend / License / Tooltip',
                                            'borlabs-cookie'
                                        ); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-8 offset-sm-4">
                                    <?php
                                    wp_nonce_field('borlabs_cookie_license_remove'); ?>
                                    <input type="hidden" name="action" value="remove">
                                    <button disabled id="removeLicenseBtn" type="submit" class="btn btn-danger btn-sm"><?php
                                        _ex(
                                            'Remove License',
                                            'Backend / Global / Button Title',
                                            'borlabs-cookie'
                                        ); ?></button>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
}
?>

<div class="row no-gutters mb-4">
    <div class="col-12 col-md-8 rounded bg-light shadow-sm">
        <div class="px-3 pt-3 pb-4">
            <form action="?page=borlabs-cookie-license" method="post">
                <h3 class="border-bottom mb-3"><?php
                    _ex('Test Environment', 'Backend / License / Headline', 'borlabs-cookie'); ?></h3>
                <div class="row">
                    <div class="col-12">

                        <div class="form-group row align-items-center">
                            <label for="testEnvironment" class="col-sm-4 col-form-label"><?php
                                _ex('Status', 'Backend / License / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <button type="button" class="btn btn-sm btn-toggle mr-2<?php
                                echo $switchTestEnvironment; ?>" data-toggle="button" data-switch-target="testEnvironment" aria-pressed="<?php
                                echo $inputTestEnvironment ? 'true' : 'false'; ?>"><span class="handle"></span></button>
                                <input type="hidden" name="testEnvironment" id="testEnvironment" value="<?php
                                echo $inputTestEnvironment; ?>">
                                <span data-toggle="tooltip" title="<?php
                                echo esc_attr_x(
                                    'Mark this installation as a test environment to continue using Borlabs Cookie without a license.',
                                    'Backend / License / Tooltip',
                                    'borlabs-cookie'
                                ); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-8 offset-sm-4">
                                <?php
                                wp_nonce_field('borlabs_cookie_license_test_environment'); ?>
                                <input type="hidden" name="action" value="save">
                                <button type="submit" class="btn btn-primary btn-sm"><?php
                                    _ex('Save', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
