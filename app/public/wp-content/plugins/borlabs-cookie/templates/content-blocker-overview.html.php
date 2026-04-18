<?php
if (\BorlabsCookie\Cookie\Backend\License::getInstance()->isPluginUnlocked()) {
?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?page=borlabs-cookie">Borlabs Cookie</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php _ex('Content Blocker', 'Backend / Content Blocker / Breadcrumb', 'borlabs-cookie'); ?></li>
    </ol>
</nav>

<?php echo \BorlabsCookie\Cookie\Backend\Messages::getInstance()->getAll(); ?>

<div class="row">
    <div class="col">
        <div class="px-3 pt-3 pb-3 bg-light shadow-sm rounded-top">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center border-bottom mb-3">
                <h3><?php _ex('Content Blocker', 'Backend / Content Blocker / Headline', 'borlabs-cookie'); ?></h3>
                <a href="?page=borlabs-cookie-content-blocker&amp;action=edit&amp;id=new" class="btn btn-primary btn-sm"><?php _ex('Add New', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></a>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th><?php _ex('Name', 'Backend / Content Blocker / Table Headline', 'borlabs-cookie'); ?></th>
                                    <th><?php _ex('ID', 'Backend / Content Blocker / Table Headline', 'borlabs-cookie'); ?></th>
                                    <th><?php _ex('Host(s)', 'Backend / Content Blocker / Table Headline', 'borlabs-cookie'); ?></th>
                                    <th data-modal-ignore class="text-center"><?php _ex('Status', 'Backend / Content Blocker / Table Headline', 'borlabs-cookie'); ?></th>
                                    <th data-modal-ignore class="text-center"><i class="fas fa-lg fa-trash-alt"></i></th>
                                    <th data-modal-ignore class="text-center"><i class="fas fa-lg fa-edit"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($contentBlocker)) {
                                    foreach ($contentBlocker as $data) {
                                    ?>
                                    <tr>
                                        <td>
                                        <?php
                                            echo esc_html($data->name);

                                            if ($data->content_blocker_id == 'default') {
                                            ?>
                                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('The <strong>Content Blocker: <em>Default</em></strong> is used for all external contents, for which no own <strong>Content Blocker</strong> was created.', 'Backend / Content Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-fw fa-exclamation-circle"></i></span>
                                            <?php
                                            }
                                        ?>
                                        </td>
                                        <td><?php echo esc_html($data->content_blocker_id); ?></td>
                                        <td><?php echo esc_html($data->hosts); ?></td>
                                        <td data-modal-ignore class="text-center">
                                            <a class="icon-link" href="<?php echo wp_nonce_url('?page=borlabs-cookie-content-blocker&amp;action=switchStatus&amp;id='.$data->id, 'switchStatus_'.$data->id); ?>"><i class="fas fa-lg<?php echo !empty($data->status) ? ' fa-toggle-on text-green' : ' fa-toggle-off text-black-50'; ?>"></i></a>
                                        </td>
                                        <td data-modal-ignore class="text-center">
                                        <?php
                                            if($data->undeletable === 0) {
                                                ?>
                                                <a data-href="<?php echo wp_nonce_url('?page=borlabs-cookie-content-blocker&amp;action=delete&amp;id='.$data->id, 'delete_'.$data->id); ?>" data-toggle="modal" data-target="#borlabsModalDelete" href="#" class="icon-link"><i class="fas fa-lg fa-trash-alt"></i></a>
                                                <?php
                                            } else {
                                                ?>-<?php
                                            }
                                        ?>
                                        </td>
                                        <td data-modal-ignore class="text-center">
                                            <a class="icon-link" href="?page=borlabs-cookie-content-blocker&amp;action=edit&amp;id=<?php echo $data->id; ?>"><i class="fas fa-lg fa-edit"></i></a>
                                        </td>
                                    </tr>
                                    <?php
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="6" class="text-center"><?php _ex('No <strong>Content Blocker</strong> configured.', 'Backend / Content Blocker / Alert Message', 'borlabs-cookie'); ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row no-gutters mb-4">
    <div class="col-12 rounded-bottom shadow-sm bg-tips text-light">
        <div class="px-3 pt-3 pb-3 mb-4">
            <h3 class="border-bottom mb-3"><?php _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>

             <div class="accordion" id="accordionContentBlocker">

                <button type="button" data-toggle="collapse" data-target="#accordionContentBlockerOne" aria-expanded="true">
                    <?php _ex('What are Content Blocker?', 'Backend / Content Blocker / Tips / Headline', 'borlabs-cookie'); ?>
                </button>

                <div id="accordionContentBlockerOne" class="collapse show" data-parent="#accordionContentBlocker">
                    <p><?php _ex('Using <strong>Content Blockers</strong>, you can automatically block iframes such as videos from YouTube or oEmbeds such as posts from Facebook. Your visitor sees a message that a content has been blocked and has the possibility to reload this content by clicking on it. You can customize the text and design of the block message to suit your needs and theme. General settings can be found under <strong>Appearance Settings</strong>. You can also customize each <strong>Content Blocker</strong> individually under <strong>Content Blocker &gt; <em>Your Content Blocker</em> &gt; Preview Blocked Content</strong> via HTML and CSS.', 'Backend / Content Blocker / Tips / Text', 'borlabs-cookie'); ?></p>
                </div>

                <button type="button" data-toggle="collapse" data-target="#accordionContentBlockerTwo">
                    <?php _ex('How can I prevent content from being blocked?', 'Backend / Content Blocker / Tips / Headline', 'borlabs-cookie'); ?>
                </button>

                <div id="accordionContentBlockerTwo" class="collapse" data-parent="#accordionContentBlocker">
                    <p><?php _ex('If a <strong>Content Blocker</strong> already exists for this type of content, it is sufficient to deactivate the corresponding <strong>Content Blocker</strong>. If there is no <strong>Content Blocker</strong>, enter the source in the <strong>Host(s) Allow List</strong>.', 'Backend / Content Blocker / Tips / Text', 'borlabs-cookie'); ?></p>
                </div>

                <button type="button" class="collapsed" data-toggle="collapse" data-target="#accordionContentBlockerThree">
                    <?php _ex('Symbols explained', 'Backend / Content Blocker / Tips / Headline', 'borlabs-cookie'); ?>
                </button>

                <div id="accordionContentBlockerThree" class="collapse" data-parent="#accordionContentBlocker">
                    <p class="bg-tips-text mb-0">
                        <i class="fas fa-lg fa-fw fa-trash-alt"></i> <?php _ex('Delete the <strong>Content Blocker</strong>. Not available for the default <strong>Content Blockers</strong>.', 'Backend / Content Blocker / Tips / Text', 'borlabs-cookie'); ?><br>
                        <i class="fas fa-lg fa-fw fa-edit"></i> <?php _ex('Edit the <strong>Content Blocker</strong>.', 'Backend / Content Blocker / Tips / Text', 'borlabs-cookie'); ?><br>
                        <i class="fas fa-lg fa-fw fa-toggle-on text-green"></i> <?php _ex('The <strong>Content Blocker</strong> is active and does block content (iframes) of the configured hosts.', 'Backend / Content Blocker / Tips / Text', 'borlabs-cookie'); ?><br>
                        <i class="fas fa-lg fa-fw fa-toggle-off"></i> <?php _ex('The <strong>Content Blocker</strong> is inactive and does not block content (iframes) of the configured hosts.', 'Backend / Content Blocker / Tips / Text', 'borlabs-cookie'); ?><br>
                    </p>
                </div>

             </div>
        </div>
    </div>
</div>

<form action="?page=borlabs-cookie-content-blocker" method="post">
    <div class="row no-gutters mb-4">
        <div class="col-12 col-md-8 rounded-left bg-light shadow-sm">
            <div class="px-3 pt-3 pb-4">
                <h3 class="border-bottom mb-3"><?php _ex('General Settings', 'Backend / Content Blocker / Headline', 'borlabs-cookie'); ?></h3>
                <div class="row">
                    <div class="col-12">

                        <div class="form-group row">
                            <label for="contentBlockerHostWhitelist" class="col-sm-4 col-form-label"><?php _ex('Host(s) Allow List', 'Backend / Content Blocker / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <textarea class="form-control d-inline-block align-top w-75 mr-2" name="contentBlockerHostWhitelist" id="contentBlockerHostWhitelist" rows="3" autocapitalize="off" autocomplete="off" autocorrect="off" spellcheck="false"><?php echo $textareaHostWhitelist; ?></textarea>
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('One host per line. When a host is recognized (for example within the src-attribute of an iframe) the content will not be blocked.', 'Backend / Content Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>

                        <div class="form-group row align-items-center">
                            <label for="removeIframesInFeeds" class="col-sm-4 col-form-label"><?php _ex('Remove Iframes and more in Feeds', 'Backend / Content Blocker / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <button type="button" class="btn btn-sm btn-toggle mr-2<?php echo $switchRemoveIframesInFeeds; ?>" data-toggle="button" data-switch-target="removeIframesInFeeds" aria-pressed="<?php echo $inputRemoveIframesInFeeds ? 'true' : 'false'; ?>" autocomplete="off"><span class="handle"></span></button>
                                <input type="hidden" name="removeIframesInFeeds" id="removeIframesInFeeds" value="<?php echo $inputRemoveIframesInFeeds; ?>">
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('Removes iframes, blocked content and all output of Borlabs Cookie\'s shortcodes in feeds. Due technical limitations it is not possible to provide the click-to-load functionality in feeds.', 'Backend / Content Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-8 offset-sm-4">
                        <button type="submit" class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4 rounded-right shadow-sm bg-tips text-light">
            <div class="px-3 pt-3 pb-3 mb-4">
                <h3 class="border-bottom mb-3"><?php _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>
                <h4><?php _ex('What is a Host?', 'Backend / Content Blocker / Tips / Headline','borlabs-cookie'); ?></h4>
                <p><?php _ex('The host is a part of a URL, often the domain. For example, if the URL is <strong><em>https://www.example.com/index.html</em></strong> the host would be <strong><em>www.example.com</em></strong>.', 'Backend / Content Blocker / Tips / Text', 'borlabs-cookie'); ?></p>
            </div>
        </div>
    </div>

    <div class="row no-gutters mb-4">
        <div class="col-12 col-md-8 rounded-left bg-light shadow-sm">
            <div class="px-3 pt-3 pb-4">
                <h3 class="border-bottom mb-3"><?php _ex('Appearance Settings', 'Backend / Content Blocker / Headline', 'borlabs-cookie'); ?></h3>
                <div class="row">
                    <div class="col-12">

                        <div class="form-group row">
                            <label for="contentBlockerFontFamily" class="col-sm-4 col-form-label">
                                <?php _ex('Font Family', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('Choose which font you want to use. Your themes font is the default setting.', 'Backend / Content Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group input-group-sm w-75">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <input type="checkbox" name="enablecontentBlockerFontFamily" value="1" data-enable-target="contentBlockerFontFamily"<?php echo !empty($inputContentBlockerFontFamily) ? ' checked' : ''; ?>>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control form-control-sm d-inline-block" id="contentBlockerFontFamily" name="contentBlockerFontFamily" value="<?php echo $inputContentBlockerFontFamily; ?>" placeholder="<?php echo esc_attr_x('Enter custom font family', 'Backend / Content Blocker / Input Placeholder', 'borlabs-cookie'); ?>"<?php echo empty($inputContentBlockerFontFamily) ? ' disabled' : ''; ?>>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="contentBlockerFontSize" class="col-sm-4 col-form-label">
                                <?php _ex('Font Size (Base)', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('Based on the base font size, the font size of all elements are automatically adjusted proportionally.', 'Backend / Content Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group input-group-sm w-50">
                                    <div class="input-group input-group-sm mb-2">
                                        <input type="number" class="form-control form-control-sm d-inline-block w-75" id="contentBlockerFontSize" name="contentBlockerFontSize" min="0" value="<?php echo $inputContentBlockerFontSize; ?>">
                                        <div class="input-group-append">
                                            <span class="input-group-text">px</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label"><?php _ex('Overlay', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <input type="text" class="color-field" id="contentBlockerBgColor" name="contentBlockerBgColor" value="<?php echo $inputContentBlockerBgColor; ?>">
                                        <div>
                                            <?php _ex('Background Color', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="color-field" id="contentBlockerTxtColor" name="contentBlockerTxtColor" value="<?php echo $inputContentBlockerTxtColor; ?>">
                                        <div>
                                            <?php _ex('Text Color', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">
                                <?php _ex('Opacity', 'Backend / Content Blocker / Label', 'borlabs-cookie'); ?>
                                <span data-toggle="tooltip" title="<?php echo esc_attr_x('Defines the visibility of the <strong>Content Blocker Overlay</strong>.', 'Backend / Content Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                            </label>
                            <div class="col-sm-8">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="input-group input-group-sm mb-2">
                                            <input type="number" class="form-control form-control-sm d-inline-block w-50" id="contentBlockerBgOpacity" name="contentBlockerBgOpacity" min="0" max="100" step="1" value="<?php echo $inputContentBlockerBgOpacity; ?>">
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                        <label for="contentBlockerBgOpacity">
                                            <?php _ex('Overlay', 'Backend / Content Blocker / Label', 'borlabs-cookie'); ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label"><?php _ex('Border Radius', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="input-group input-group-sm mb-2">
                                            <input type="number" class="form-control form-control-sm d-inline-block w-50" id="contentBlockerBtnBorderRadius" name="contentBlockerBtnBorderRadius" min="0" value="<?php echo $inputContentBlockerBtnBorderRadius; ?>">
                                            <div class="input-group-append">
                                                <span class="input-group-text">px</span>
                                            </div>
                                        </div>
                                        <label for="contentBlockerBtnBorderRadius">
                                            <?php _ex('Button', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                        </label>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label"><?php _ex('Button Color', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <input type="text" class="color-field" id="contentBlockerBtnColor" name="contentBlockerBtnColor" value="<?php echo $inputContentBlockerBtnColor; ?>">
                                        <div>
                                            <?php _ex('Default', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="color-field" id="contentBlockerBtnHoverColor" name="contentBlockerBtnHoverColor" value="<?php echo $inputContentBlockerBtnHoverColor; ?>">
                                        <div>
                                            <?php _ex('Hover', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label"><?php _ex('Button Text Color', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <input type="text" class="color-field" id="contentBlockerBtnTxtColor" name="contentBlockerBtnTxtColor" value="<?php echo $inputContentBlockerBtnTxtColor; ?>">
                                        <div>
                                            <?php _ex('Default', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="color-field" id="contentBlockerBtnHoverTxtColor" name="contentBlockerBtnHoverTxtColor" value="<?php echo $inputContentBlockerBtnTxtHoverColor; ?>">
                                        <div>
                                            <?php _ex('Hover', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label"><?php _ex('Link Color', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <input type="text" class="color-field" id="contentBlockerLinkColor" name="contentBlockerLinkColor" value="<?php echo $inputContentBlockerLinkColor; ?>">
                                        <div>
                                            <?php _ex('Default', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="color-field" id="contentBlockerLinkHoverColor" name="contentBlockerLinkHoverColor" value="<?php echo $inputContentBlockerLinkHoverColor; ?>">
                                        <div>
                                            <?php _ex('Hover', 'Backend / Global / Styling / Label', 'borlabs-cookie'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-8 offset-sm-4">
                        <?php wp_nonce_field('borlabs_cookie_content_blocker_save_settings'); ?>
                        <input type="hidden" name="action" value="saveSettings">
                        <button type="submit" class="btn btn-primary btn-sm"><?php _ex('Save all settings', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4 rounded-right shadow-sm bg-tips text-light">
            <div class="px-3 pt-3 pb-3 mb-4">
                <h3 class="border-bottom mb-3"><?php _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>
                <h4><?php _ex('Content Blocker Individual Settings', 'Backend / Content Blocker / Tips / Headline','borlabs-cookie'); ?></h4>
                <p><?php _ex('Please note that the settings for <strong>Button Radius</strong>, <strong>Button Color</strong>, and <strong>Button Text Color</strong> are often overwritten with the individual settings of a </strong>Content Blocker</strong>.', 'Backend / Content Blocker / Tips / Text', 'borlabs-cookie'); ?></p>
            </div>
        </div>
    </div>
</form>

<form action="?page=borlabs-cookie-content-blocker" method="post">
    <div class="row no-gutters">
        <div class="col-12 col-md-8 rounded bg-light shadow-sm">
            <div class="px-3 pt-3 pb-4">
                <h3 class="border-bottom mb-3"><?php _ex('Reset Default Content Blocker', 'Backend / Content Blocker / Headline', 'borlabs-cookie'); ?></h3>
                <div class="row">
                    <div class="col-12">

                        <div class="form-group row align-items-center">
                            <label class="col-sm-4 col-form-label"><?php _ex('Confirm Reset', 'Backend / Global / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="bctResetConfirmation" value="1" data-enable-target="executeContentBlockerReset">
                                    <label class="custom-control-label mr-2" for="bctResetConfirmation"><?php _ex('Confirmed', 'Backend / Global / Text', 'borlabs-cookie'); ?></label>
                                    <span class="align-middle" data-toggle="tooltip" title="<?php echo esc_attr_x('Please confirm that you want to reset the default <strong>Content Blockers</strong>.', 'Backend / Content Blocker / Tooltip', 'borlabs-cookie'); ?> <?php echo esc_attr_x('They will be reset to their default settings. Your own <strong>Content Blockers</strong> remain unchanged.', 'Backend / Content Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-8 offset-sm-4">
                                <?php wp_nonce_field('borlabs_cookie_content_blocker_reset_default'); ?>
                                <input type="hidden" name="action" value="resetDefault">
                                <button disabled id="executeContentBlockerReset" type="submit" class="btn btn-danger btn-sm"><?php _ex('Reset', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<?php
} else {
    echo \BorlabsCookie\Cookie\Backend\License::getInstance()->getLicenseMessageActivateKey();
}
?>
