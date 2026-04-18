<?php
if (\BorlabsCookie\Cookie\Backend\License::getInstance()->isPluginUnlocked()) {
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?page=borlabs-cookie">Borlabs Cookie</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php _ex('Cookies', 'Backend / Cookies / Breadcrumb', 'borlabs-cookie'); ?></li>
    </ol>
</nav>

<?php echo \BorlabsCookie\Cookie\Backend\Messages::getInstance()->getAll(); ?>

<?php
    if (!empty($cookieGroups)) {
        foreach ($cookieGroups as $cookieGroupData) {
        ?>
        <div class="row mb-4">
            <div class="col">
                <div class="px-3 pt-3 pb-3 bg-light shadow-sm rounded">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center border-bottom mb-3">
                        <h3><?php echo esc_html($cookieGroupData->name); ?></h3>
                        <a href="?page=borlabs-cookie-cookies&amp;action=cookieServices&amp;id=<?php echo $cookieGroupData->id; ?>" class="btn btn-primary btn-sm"><?php _ex('Add New', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></a>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th><?php _ex('Name', 'Backend / Cookies / Table Headline', 'borlabs-cookie'); ?></th>
                                            <th><?php _ex('ID', 'Backend / Cookies / Table Headline', 'borlabs-cookie'); ?></th>
                                            <th class="text-center"><?php _ex('Position', 'Backend / Cookies / Table Headline', 'borlabs-cookie'); ?></th>
                                            <th data-modal-ignore class="text-center"><?php _ex('Status', 'Backend / Cookies / Table Headline', 'borlabs-cookie'); ?></th>
                                            <th data-modal-ignore class="text-center"><i class="fas fa-lg fa-trash-alt"></i></th>
                                            <th data-modal-ignore class="text-center"><i class="fas fa-lg fa-edit"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($cookieGroupData->cookies)) {
                                            foreach ($cookieGroupData->cookies as $cookieData) {
                                            ?>
                                            <tr>
                                                <td>
                                                <?php
                                                    echo esc_html($cookieData->name);
                                                ?>
                                                </td>
                                                <td><?php echo $cookieData->cookie_id; ?></td>
                                                <td class="text-center"><?php echo $cookieData->position; ?></td>
                                                <td data-modal-ignore class="text-center">
                                                    <?php
                                                    if($cookieData->undeletable === 0) {
                                                        ?>
                                                        <a class="icon-link" href="<?php echo wp_nonce_url('?page=borlabs-cookie-cookies&amp;action=switchStatus&amp;id='.$cookieData->id, 'switchStatus_'.$cookieData->id); ?>"><i class="fas fa-lg<?php echo !empty($cookieData->status) ? ' fa-toggle-on text-green' : ' fa-toggle-off text-black-50'; ?>"></i></a>
                                                        <?php
                                                    } else {
                                                        ?>-<?php
                                                    }
                                                    ?>
                                                </td>
                                                <td data-modal-ignore class="text-center">
                                                <?php
                                                    if($cookieData->undeletable === 0) {
                                                        ?>
                                                        <a data-href="<?php echo wp_nonce_url('?page=borlabs-cookie-cookies&amp;action=delete&amp;id='.$cookieData->id, 'delete_'.$cookieData->id); ?>" data-toggle="modal" data-target="#borlabsModalDelete" href="#" class="icon-link"><i class="fas fa-lg fa-trash-alt"></i></a>
                                                        <?php
                                                    } else {
                                                        ?>-<?php
                                                    }
                                                ?>
                                                </td>
                                                <td data-modal-ignore class="text-center">
                                                    <a class="icon-link" href="?page=borlabs-cookie-cookies&amp;action=edit&amp;id=<?php echo $cookieData->id; ?>"><i class="fas fa-lg fa-edit"></i></a>
                                                </td>
                                            </tr>
                                            <?php
                                            }
                                        } else {
                                            ?>
                                            <tr>
                                                <td colspan="6" class="text-center"><?php _ex('No <strong>Cookies</strong> configured.', 'Backend / Cookies / Alert Message', 'borlabs-cookie'); ?></td>
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
        <?php
        }
    }
?>
<div class="row no-gutters mb-4">
    <div class="col-12 rounded shadow-sm bg-tips text-light">
        <div class="px-3 pt-3 pb-3 mb-4">
            <h3 class="border-bottom mb-3"><?php _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>

             <div class="accordion" id="accordionCookies">

                <button type="button" data-toggle="collapse" data-target="#accordionCookiesOne" aria-expanded="true">
                    <?php _ex('What is the purpose of the Cookies section?', 'Backend / Cookies / Tips / Headline', 'borlabs-cookie'); ?>
                </button>

                <div id="accordionCookiesOne" class="collapse show" data-parent="#accordionCookies">
                    <p>
                        <?php _ex('In the <strong>Cookies</strong> section you can document the use of cookies for your visitors as well as integrate JavaScripts, such as Google Analytics.', 'Backend / Cookies / Tips / Text', 'borlabs-cookie'); ?>
                    </p>
                    <p>
                        <?php _ex('Because Borlabs Cookie uses the opt-in process, JavaScripts are only executed after the visitor has given their consent to the <strong>Cookie Group</strong> or <strong>Cookie</strong>.', 'Backend / Cookies / Tips / Text', 'borlabs-cookie'); ?>
                    </p>
                </div>

                <button type="button" class="collapsed" data-toggle="collapse" data-target="#accordionCookiesTwo">
                    <?php _ex('Symbols explained', 'Backend / Cookies / Tips / Headline', 'borlabs-cookie'); ?>
                </button>

                <div id="accordionCookiesTwo" class="collapse" data-parent="#accordionCookies">
                    <p class="bg-tips-text mb-0">
                        <i class="fas fa-lg fa-fw fa-trash-alt"></i> <?php _ex('Delete the <strong>Cookie</strong>. Not available for the <strong>Cookie: <em>Borlabs Cookie</em></strong>.', 'Backend / Cookies / Tips / Text', 'borlabs-cookie'); ?><br>
                        <i class="fas fa-lg fa-fw fa-edit"></i> <?php _ex('Edit the <strong>Cookie</strong>.', 'Backend / Cookies / Tips / Text', 'borlabs-cookie'); ?><br>
                        <i class="fas fa-lg fa-fw fa-toggle-on text-green"></i> <?php _ex('The <strong>Cookie</strong> is active and displayed in the <strong>Cookie Box</strong>. Not available for the <strong>Cookie: <em>Borlabs Cookie</em></strong>.', 'Backend / Cookies / Tips / Text', 'borlabs-cookie'); ?><br>
                        <i class="fas fa-lg fa-fw fa-toggle-off"></i> <?php _ex('The <strong>Cookie</strong> is inactive and not displayed in the <strong>Cookie Box</strong>. Not available for the <strong>Cookie: <em>Borlabs Cookie</em></strong>.', 'Backend / Cookies / Tips / Text', 'borlabs-cookie'); ?><br>
                    </p>
                </div>

             </div>
        </div>
    </div>
</div>

<form action="?page=borlabs-cookie-cookies" method="post">
    <div class="row no-gutters">
        <div class="col-12 col-md-8 rounded bg-light shadow-sm">
            <div class="px-3 pt-3 pb-4">
                <h3 class="border-bottom mb-3"><?php _ex('Reset Default Cookies', 'Backend / Cookies / Headline', 'borlabs-cookie'); ?></h3>
                <div class="row">
                    <div class="col-12">

                        <div class="form-group row align-items-center">
                            <label class="col-sm-4 col-form-label"><?php _ex('Confirm Reset', 'Backend / Global / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="bctResetConfirmation" value="1" data-enable-target="executeCookiesReset">
                                    <label class="custom-control-label mr-2" for="bctResetConfirmation"><?php _ex('Confirmed', 'Backend / Global / Text', 'borlabs-cookie'); ?></label>
                                    <span class="align-middle" data-toggle="tooltip" title="<?php echo esc_attr_x('Please confirm that you want to reset the default <strong>Cookies</strong>.', 'Backend / Cookies / Tooltip', 'borlabs-cookie'); ?> <?php echo esc_attr_x('They will be reset to their default settings. Your own <strong>Cookies</strong> remain unchanged.', 'Backend / Cookies / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-8 offset-sm-4">
                                <?php wp_nonce_field('borlabs_cookie_cookies_reset_default'); ?>
                                <input type="hidden" name="action" value="resetDefault">
                                <button disabled id="executeCookiesReset" type="submit" class="btn btn-danger btn-sm"><?php _ex('Reset', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
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
