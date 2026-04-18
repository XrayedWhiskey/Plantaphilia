<?php
if (\BorlabsCookie\Cookie\Backend\License::getInstance()->isPluginUnlocked()) {
?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?page=borlabs-cookie">Borlabs Cookie</a></li>
        <li class="breadcrumb-item"><a href="?page=borlabs-cookie-cookie-groups"><?php _ex('Cookie Groups', 'Backend / Cookie Groups / Breadcrumb', 'borlabs-cookie'); ?></a></li>
        <?php
        if (!empty($cookieGroupData->id)) {
            ?><li class="breadcrumb-item active" aria-current="page"><?php echo sprintf(_x('Edit: %s', 'Backend / Cookie Groups / Breadcrumb', 'borlabs-cookie'), $inputName); ?></li><?php
        } else {
            ?><li class="breadcrumb-item active" aria-current="page"><?php _ex('New', 'Backend / Cookie Groups / Breadcrumb', 'borlabs-cookie'); ?></li><?php
        }
        ?>
    </ol>
</nav>

<?php echo \BorlabsCookie\Cookie\Backend\Messages::getInstance()->getAll(); ?>

<form action="?page=borlabs-cookie-cookie-groups" method="post" id="BorlabsCookieForm" class="needs-validation" novalidate>
    <div class="row no-gutters mb-4">
        <div class="col-12 col-md-8 rounded-left bg-light shadow-sm">
            <div class="px-3 pt-3 pb-4">
                <h3 class="border-bottom mb-3"><?php _ex('Cookie Group Settings', 'Backend / Cookie Groups / Headline', 'borlabs-cookie'); ?></h3>
                <div class="row">
                    <div class="col-12">

                        <div class="form-group row">
                            <label for="groupId" class="col-sm-4 col-form-label"><?php _ex('ID', 'Backend / Cookie Groups / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="groupId" name="groupId" value="<?php echo $inputGroupId; ?>" <?php echo empty($cookieGroupData->id) ? 'required' : 'disabled'; ?> pattern="[a-z-_]{3,}">
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('<strong>ID</strong> must be set. The <strong>ID</strong> must be at least 3 characters long and may only contain: <strong><em>a-z - _</em></strong>', 'Backend / Cookie Groups / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                                <div class="invalid-feedback"><?php _ex('<strong>ID</strong> must be set. The <strong>ID</strong> must be at least 3 characters long and may only contain: <strong><em>a-z - _</em></strong>', 'Backend / Cookie Groups / Validation Message', 'borlabs-cookie'); ?></div>
                            </div>
                        </div>

                        <?php
                        if (!empty($cookieGroupData->id)) {
                        ?>
                        <div class="form-group row align-items-center">
                            <label for="shortcode" class="col-sm-4 col-form-label"><?php _ex('Shortcode', 'Backend / Cookie Groups / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <input id="shortcode" type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" value="[borlabs-cookie id=&quot;<?php echo $inputGroupId; ?>&quot; type=&quot;cookie-group&quot;]<?php _ex('...block this...', 'Backend / Cookie Groups / Input Placeholder', 'borlabs-cookie'); ?>[/borlabs-cookie]" disabled>
                                <span data-clipboard="shortcode" data-toggle="tooltip" title="<?php echo esc_attr_x('Click to copy to clipboard.', 'Backend / Global / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-clone"></i></span>
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('Use this shortcode to unblock JavaScript or content when user opted-in for this <strong>Cookie Group</strong>.', 'Backend / Cookie Groups / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>
                        <?php
                        }
                        ?>

                        <?php
                        if (!empty($cookieGroupData->id) && !empty($languageFlag)) {
                        ?>
                        <div class="form-group row align-items-center">
                            <label class="col-sm-4 col-form-label"><?php _ex('Language', 'Backend / Cookie Groups / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <img src="<?php echo $languageFlag; ?>" alt=""> <?php echo $languageName; ?>
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('Your entry is stored for this language.', 'Backend / Cookie Groups / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>
                        <?php
                        }
                        ?>

                        <?php
                        if (empty($cookieGroupData->group_id) || (!empty($cookieGroupData->group_id) && $cookieGroupData->group_id !== 'essential')) {
                        ?>
                        <div class="form-group row align-items-center">
                            <label class="col-sm-4 col-form-label"><?php _ex('Status', 'Backend / Cookie Groups / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <button type="button" class="btn btn-sm btn-toggle mr-2<?php echo $switchStatus; ?>" data-toggle="button" data-switch-target="status" aria-pressed="<?php echo $inputStatus ? 'true' : 'false'; ?>" autocomplete="off"><span class="handle"></span></button>
                                <input type="hidden" name="status" id="status" value="<?php echo $inputStatus; ?>">
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('The status of this <strong>Cookie Group</strong>. If active (Status: ON) it is displayed to the visitor in the <strong>Cookie Box</strong>.', 'Backend / Cookie Groups / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>
                        <?php
                        } else {
                            ?>
                            <input type="hidden" name="status" id="status" value="1">
                            <?php
                        }
                        ?>

                        <div class="form-group row">
                            <label for="name" class="col-sm-4 col-form-label"><?php _ex('Name', 'Backend / Cookie Groups / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="name" name="name" value="<?php echo $inputName; ?>" required>
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('Choose a name for this <strong>Cookie Group</strong>. It is displayed to the visitor in the <strong>Cookie Box</strong>.', 'Backend / Cookie Groups / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                                <div class="invalid-feedback"><?php _ex('This is a required field and cannot be empty.', 'Backend / Global / Validation Message', 'borlabs-cookie'); ?></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="description" class="col-sm-4 col-form-label"><?php _ex('Description', 'Backend / Cookie Groups / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <textarea class="form-control d-inline-block align-top w-75 mr-2" name="description" id="description" rows="5"><?php echo $textareaDescription; ?></textarea>
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('Enter a description for this <strong>Cookie Group</strong>. It is displayed to the visitor in the <strong>Cookie Box</strong>.', 'Backend / Cookie Groups / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>

                        <?php
                        if (empty($cookieGroupData->group_id) || (!empty($cookieGroupData->group_id) && $cookieGroupData->group_id !== 'essential')) {
                        ?>
                        <div class="form-group row align-items-center">
                            <label class="col-sm-4 col-form-label"><?php _ex('Pre-selected', 'Backend / Cookie Groups / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <button type="button" class="btn btn-sm btn-toggle mr-2<?php echo $switchPreSelected; ?>" data-toggle="button" data-switch-target="preSelected" aria-pressed="<?php echo $inputPreSelected ? 'true' : 'false'; ?>" autocomplete="off"><span class="handle"></span></button>
                                <input type="hidden" name="preSelected" id="preSelected" value="<?php echo $inputPreSelected; ?>">
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('If activated (Status: ON) this <strong>Cookie Group</strong> is pre-selected in the <strong>Cookie Box</strong>. The visitor can de-select it.', 'Backend / Cookie Groups / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>

                            <?php
                            if ($ignorePreSelectStatusIsActive === true) {
                            ?>
                            <div class="form-group row align-items-center">
                                <div class="col-sm-8 offset-4">
                                    <div class="alert alert-warning mt-2"><?php _ex('You have enabled <strong>Ignore Pre-selected Status</strong> in the <strong>Cookie Box</strong> settings, which overwrites this setting.', 'Backend / Cookie Groups / Alert Message', 'borlabs-cookie'); ?></div>
                                </div>
                            </div>
                            <?php
                            }
                            ?>
                        <?php
                        } else {
                            ?>
                            <input type="hidden" name="preSelected" id="preSelected" value="1">
                            <?php
                        }
                        ?>

                        <div class="form-group row">
                            <label for="position" class="col-sm-4 col-form-label"><?php _ex('Position', 'Backend / Cookie Groups / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control form-control-sm d-inline-block w-75 mr-2" id="position" name="position" value="<?php echo $inputPosition; ?>" min="1" step="1">
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('Determine the position where this <strong>Cookie Group</strong> is displayed. Order follows natural numbers.', 'Backend / Cookie Groups / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-8 offset-sm-4">
                        <?php wp_nonce_field('borlabs_cookie_cookie_groups_save'); ?>
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="id" value="<?php echo $inputId; ?>">
                        <button type="submit" class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                        <a class="btn btn-secondary btn-sm" href="?page=borlabs-cookie-cookie-groups"><?php _ex('Go back without saving', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4 rounded-right shadow-sm bg-tips text-light">
            <div class="px-3 pt-3 pb-3 mb-4">
                <h3 class="border-bottom mb-3"><?php _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>
                <h4><?php _ex('Shortcode explained', 'Backend / Cookie Groups / Tips / Headline', 'borlabs-cookie'); ?></h4>
                <p><?php _ex('The shortcode can be used to execute custom code associated with this type of <strong>Cookie Group</strong> once the visitor has given consent to the <strong>Cookie Group</strong>.<p>The shortcode for example can be used in the <strong>Meta Box</strong> of Borlabs Cookie. You can find it for example in <strong>Posts &gt; <em>Your Post</em> &gt; Borlabs Cookie &gt; Custom Code</strong>.', 'Backend / Cookie Groups / Tips / Text', 'borlabs-cookie'); ?>
                </p>
            </div>
        </div>
    </div>
</form>
<?php
} else {
    echo \BorlabsCookie\Cookie\Backend\License::getInstance()->getLicenseMessageActivateKey();
}
?>
