<div class="mb-4 px-3 pt-3 pb-4 rounded bg-light shadow-sm">
    <h3 class="border-bottom mb-3"><?php
        _ex('Upgrade to Borlabs Cookie 3.0', 'Backend / Upgrade / Table Headline', 'borlabs-cookie'); ?></h3>
    <div class="row">
        <div class="col-12">
            <p><?php _ex('Enable this option to automatically upgrade to Borlabs Cookie 3.0 and import your existing settings.', 'Backend / Upgrade / Text', 'borlabs-cookie'); ?>
                <br>
                <?php echo sprintf(
                    _x('For more information, please read our upgrade article <a href="%s" rel="nofollow noopener noreferrer" target="_blank">here <i class="fas fa-external-link-alt"></i></a>.', 'Backend / Upgrade / Text', 'borlabs-cookie'),
                    _x('https://borlabs.io/kb/upgrade-2-3-to-3-2/', 'Backend / Upgrade / URL', 'borlabs-cookie')
                ); ?>
            </p>
            <div class="row align-items-center">
                <label for="borlabsCookieUpgradeStatus" class="col-sm-4 col-form-label"><?php _ex('Upgrade to Borlabs Cookie 3.0.', 'Backend / Upgrade / Label', 'borlabs-cookie'); ?></label>
                <div class="col-sm-8">
                    <button type="button" class="btn btn-sm btn-toggle mr-2<?php echo $switchBorlabsCookieUpgradeStatus; ?>" data-toggle="button" data-switch-target="borlabsCookieUpgradeStatus" aria-pressed="<?php echo $inputBorlabsCookieUpgradeStatus ? 'true' : 'false'; ?>">
                        <span class="handle"></span>
                    </button>
                    <input type="hidden" name="borlabsCookieUpgradeStatus" id="borlabsCookieUpgradeStatus" value="<?php echo $inputBorlabsCookieUpgradeStatus; ?>">
                    <span data-borlabs-cookie-upgrade-status-saving class="borlabs-hide text-warning"><?php _ex('Saving...', 'Backend / Upgrade / Text', 'borlabs-cookie'); ?></span>
                    <span data-borlabs-cookie-upgrade-status-saved class="borlabs-hide text-success"><?php _ex('Saved.', 'Backend / Upgrade / Text', 'borlabs-cookie'); ?></span>
                </div>
            </div>
            <div class="mt-2 text-center<?php echo $inputBorlabsCookieUpgradeStatus ? '' : ' borlabs-hide'; ?>" data-borlabs-cookie-upgrade-status-enabled><span class="align-middle"><?php _ex('Borlabs Cookie will automatically upgrade to version 3.0 a few days after the update becomes available.', 'Backend / Upgrade / Text', 'borlabs-cookie'); ?></span></div>
        </div>
    </div>
</div>
