        <div class="footer text-center mt-4">
            <p>
                developed by
                <br>
                <a href="<?php _ex('https://borlabs.io/?utm_source=Borlabs+Cookie&amp;utm_medium=Footer+Logo&amp;utm_campaign=Analysis', 'Backend / Global / Footer / URL', 'borlabs-cookie'); ?>" rel="nofollow noopener noreferrer" target="_blank"><img class="borlabs-logo" src="<?php echo $this->imagePath; ?>/borlabs-logo.svg" alt="Borlabs"></a>
            </p>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="borlabsModalDelete" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content shadow">
                <div class="modal-header bg-danger text-light">
                    <h5 class="modal-title"><?php _ex('Delete selection?', 'Backend / Global / Headline', 'borlabs-cookie'); ?></h5>
                    <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><?php _ex('Close', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                    <a href="#" class="btn btn-primary btn-danger btn-sm" data-confirm><?php _ex('Delete', 'Backend / Global / Text', 'borlabs-cookie'); ?></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="borlabsModalTelemetry" data-backdrop="static" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow">
                <div class="modal-header bg-primary text-light">
                    <h5 class="modal-title"><?php _ex('Improve Borlabs Cookie', 'Backend / Telemetry / Table Headline', 'borlabs-cookie'); ?></h5>
                    <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <img style="max-width: 100%;" src="<?php echo $this->imagePath; ?>/the-telemetry-octopus-that-everybody-loves-with-cookie.png" alt="The telemetry octopus that everybody loves">
                        </div>
                        <div class="col-sm-8">
                            <p><?php _ex('Help us improve Borlabs Cookie by providing us with non-personal information about your website.', 'Backend / Telemetry / Text', 'borlabs-cookie'); ?></p>
                            <p>
                                <?php echo sprintf(
                                    _x('For more information about what data we need and for what purpose, <a href="%s" rel="nofollow noopener noreferrer" target="_blank">click here <i class="fas fa-external-link-alt"></i></a>.', 'Backend / Telemetry / Text', 'borlabs-cookie'),
                                    _x('https://borlabs.io/borlabs-cookie/telemetry/', 'Backend / Telemetry / URL', 'borlabs-cookie')
                                ); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><?php _ex('No, I do not want to help', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                    <button type="button" class="btn btn-primary btn-sm" data-confirm><i class="fa fa-heart"></i> <?php _ex('Yes, I would like to help', 'Backend / Global / Text', 'borlabs-cookie'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div><!-- BorlabsCookie -->
