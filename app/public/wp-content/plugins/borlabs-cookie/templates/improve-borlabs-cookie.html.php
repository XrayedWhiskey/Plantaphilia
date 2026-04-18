<div class="mb-4 px-3 pt-3 pb-4 rounded bg-light shadow-sm">
    <h3 class="border-bottom mb-3"><?php
        _ex('Improve Borlabs Cookie', 'Backend / Telemetry / Table Headline', 'borlabs-cookie'); ?></h3>
    <div class="row">
        <div class="col-12">
            <p><?php _ex('Help us improve Borlabs Cookie by providing us with non-personal information about your website.', 'Backend / Telemetry / Text', 'borlabs-cookie'); ?>
                <br>
                <?php echo sprintf(
                    _x('For more information about what data we need and for what purpose, <a href="%s" rel="nofollow noopener noreferrer" target="_blank">click here <i class="fas fa-external-link-alt"></i></a>.', 'Backend / Telemetry / Text', 'borlabs-cookie'),
                    _x('https://borlabs.io/borlabs-cookie/telemetry/', 'Backend / Telemetry / URL', 'borlabs-cookie')
                ); ?>
            </p>
            <div class="row align-items-center">
                <label for="telemetryStatus" class="col-sm-4 col-form-label"><?php _ex('Improve Borlabs Cookie', 'Backend / Telemetry / Label', 'borlabs-cookie'); ?></label>
                <div class="col-sm-8">
                    <button type="button" class="btn btn-sm btn-toggle mr-2<?php echo $switchTelemetryStatus; ?>" data-toggle="button" data-switch-target="telemetryStatus" aria-pressed="<?php echo $inputTelemetryStatus ? 'true' : 'false'; ?>">
                        <span class="handle"></span>
                    </button>
                    <input type="hidden" name="telemetryStatus" id="telemetryStatus" value="<?php echo $inputTelemetryStatus; ?>">
                    <span data-borlabs-cookie-telemetry-saving class="borlabs-hide text-warning"><?php _ex('Saving...', 'Backend / Telemetry / Text', 'borlabs-cookie'); ?></span>
                    <span data-borlabs-cookie-telemetry-saved class="borlabs-hide text-success"><?php _ex('Saved.', 'Backend / Telemetry / Text', 'borlabs-cookie'); ?></span>
                </div>
            </div>
            <div class="mt-2 text-center<?php echo $inputTelemetryStatus ? '' : ' borlabs-hide'; ?>" data-borlabs-cookie-telemetry-thank-you><span class="align-middle"><?php _ex('Thank you!', 'Backend / Telemetry / Text', 'borlabs-cookie'); ?></span> <span class="dashicons dashicons-heart align-middle" style="width: 16px; height: 16px;"></span></div>
        </div>
    </div>
</div>
