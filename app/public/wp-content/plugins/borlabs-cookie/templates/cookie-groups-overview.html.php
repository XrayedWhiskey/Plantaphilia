<?php
if (\BorlabsCookie\Cookie\Backend\License::getInstance()->isPluginUnlocked()) {
?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?page=borlabs-cookie">Borlabs Cookie</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php _ex('Cookie Groups', 'Backend / Cookie Groups / Breadcrumb', 'borlabs-cookie'); ?></li>
    </ol>
</nav>

<?php echo \BorlabsCookie\Cookie\Backend\Messages::getInstance()->getAll(); ?>

<div class="row">
    <div class="col">
        <div class="px-3 pt-3 pb-3 bg-light shadow-sm rounded-top">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center border-bottom mb-3">
                <h3><?php _ex('Cookie Groups', 'Backend / Cookie Groups / Headline', 'borlabs-cookie'); ?></h3>
                <a href="?page=borlabs-cookie-cookie-groups&amp;action=edit&amp;id=new" class="btn btn-primary btn-sm"><?php _ex('Add New', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></a>
            </div>
            <div class="row">
                <div class="col">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th><?php _ex('Name', 'Backend / Cookie Groups / Table Headline', 'borlabs-cookie'); ?></th>
                                    <th><?php _ex('ID', 'Backend / Cookie Groups / Table Headline', 'borlabs-cookie'); ?></th>
                                    <th class="text-center"><?php _ex('Position', 'Backend / Cookie Groups / Table Headline', 'borlabs-cookie'); ?></th>
                                    <th data-modal-ignore class="text-center"><?php _ex('Status', 'Backend / Cookie Groups / Table Headline', 'borlabs-cookie'); ?></th>
                                    <th data-modal-ignore class="text-center"><i class="fas fa-lg fa-trash-alt"></i></th>
                                    <th data-modal-ignore class="text-center"><i class="fas fa-lg fa-edit"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($cookieGroups)) {
                                    foreach ($cookieGroups as $cookieGroupData) {
                                    ?>
                                    <tr>
                                        <td>
                                        <?php
                                            echo esc_html($cookieGroupData->name);
                                        ?>
                                        </td>
                                        <td><?php echo esc_html($cookieGroupData->group_id); ?></td>
                                        <td class="text-center"><?php echo $cookieGroupData->position; ?></td>
                                        <td data-modal-ignore class="text-center">
                                            <?php
                                            if($cookieGroupData->group_id !== 'essential') {
                                                ?>
                                                <a class="icon-link" href="<?php echo wp_nonce_url('?page=borlabs-cookie-cookie-groups&amp;action=switchStatus&amp;id='.$cookieGroupData->id, 'switchStatus_'.$cookieGroupData->id); ?>"><i class="fas fa-lg<?php echo !empty($cookieGroupData->status) ? ' fa-toggle-on text-green' : ' fa-toggle-off text-black-50'; ?>"></i></a>
                                                <?php
                                            } else {
                                                ?>-<?php
                                            }
                                            ?>
                                        </td>
                                        <td data-modal-ignore class="text-center">
                                        <?php
                                            if($cookieGroupData->undeletable === 0) {
                                                ?>
                                                <a data-href="<?php echo wp_nonce_url('?page=borlabs-cookie-cookie-groups&amp;action=delete&amp;id='.$cookieGroupData->id, 'delete_'.$cookieGroupData->id); ?>" data-toggle="modal" data-target="#borlabsModalDelete" href="#" class="icon-link"><i class="fas fa-lg fa-trash-alt"></i></a>
                                                <?php
                                            } else {
                                                ?>-<?php
                                            }
                                        ?>
                                        </td>
                                        <td data-modal-ignore class="text-center">
                                            <a class="icon-link" href="?page=borlabs-cookie-cookie-groups&amp;action=edit&amp;id=<?php echo $cookieGroupData->id; ?>"><i class="fas fa-lg fa-edit"></i></a>
                                        </td>
                                    </tr>
                                    <?php
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="6" class="text-center"><?php _ex('No <strong>Cookie Groups</strong> configured.', 'Backend / Cookie Groups / Alert Message', 'borlabs-cookie'); ?></td>
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

             <div class="accordion" id="accordionCookieGroups">

                <button type="button" data-toggle="collapse" data-target="#accordionCookieGroupsOne" aria-expanded="true">
                    <?php _ex('What are Cookie Groups?', 'Backend / Cookie Groups / Tips / Headline', 'borlabs-cookie'); ?>
                </button>

                <div id="accordionCookieGroupsOne" class="collapse show" data-parent="#accordionCookieGroups">
                    <p>
                        <?php _ex('<strong>Cookies</strong> can be grouped thematically into <strong>Cookie Groups</strong> which are displayed to the visitor. Unused <strong>Cookie Groups</strong> can be deactivated, new ones can be added. The <strong>Cookie Group: <em>Essential</em></strong> cannot be deactivated. All <strong>Cookies</strong> belonging to this group are always issued.', 'Backend / Cookie Groups / Tips / Text', 'borlabs-cookie'); ?>
                    </p>
                </div>

                <button type="button" class="collapsed" data-toggle="collapse" data-target="#accordionCookieGroupsTwo">
                    <?php _ex('Symbols explained', 'Backend / Cookie Groups / Tips / Headline', 'borlabs-cookie'); ?>
                </button>

                <div id="accordionCookieGroupsTwo" class="collapse" data-parent="#accordionCookieGroups">
                    <p class="bg-tips-text mb-0">
                        <i class="fas fa-lg fa-fw fa-trash-alt"></i> <?php _ex('Delete the <strong>Cookie Group</strong>. Not available for default <strong>Cookie Groups</strong>.', 'Backend / Cookie Groups / Tips / Text', 'borlabs-cookie'); ?><br>
                        <i class="fas fa-lg fa-fw fa-edit"></i> <?php _ex('Edit the <strong>Cookie Group</strong>.', 'Backend / Cookie Groups / Tips / Text', 'borlabs-cookie'); ?><br>
                        <i class="fas fa-lg fa-fw fa-toggle-on text-green"></i> <?php _ex('The <strong>Cookie Group</strong> is active and displayed in the <strong>Cookie Box</strong>. Not available for the <strong>Cookie Group: <em>Essential</em></strong>.', 'Backend / Cookie Groups / Tips / Text', 'borlabs-cookie'); ?><br>
                        <i class="fas fa-lg fa-fw fa-toggle-off"></i> <?php _ex('The <strong>Cookie Group</strong> is inactive and not displayed in the <strong>Cookie Box</strong>. Not available for the <strong>Cookie Group: <em>Essential</em></strong>.', 'Backend / Cookie Groups / Tips / Text', 'borlabs-cookie'); ?><br>
                    </p>
                </div>

             </div>
        </div>
    </div>
</div>

<form action="?page=borlabs-cookie-cookie-groups" method="post">
    <div class="row no-gutters">
        <div class="col-12 col-md-8 rounded bg-light shadow-sm">
            <div class="px-3 pt-3 pb-4">
                <h3 class="border-bottom mb-3"><?php _ex('Reset Default Cookie Groups', 'Backend / Cookie Groups / Headline', 'borlabs-cookie'); ?></h3>
                <div class="row">
                    <div class="col-12">

                        <div class="form-group row align-items-center">
                            <label class="col-sm-4 col-form-label"><?php _ex('Confirm Reset', 'Backend / Global / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="bctResetConfirmation" value="1" data-enable-target="executeCookieGroupsReset">
                                    <label class="custom-control-label mr-2" for="bctResetConfirmation"><?php _ex('Confirmed', 'Backend / Global / Text', 'borlabs-cookie'); ?></label>
                                    <span class="align-middle" data-toggle="tooltip" title="<?php echo esc_attr_x('Please confirm that you want to reset the default <strong>Cookie Groups</strong>.', 'Backend / Cookie Groups / Tooltip', 'borlabs-cookie'); ?> <?php echo esc_attr_x('They will be reset to their default settings. Your own <strong>Cookie Groups</strong> remain unchanged.', 'Backend / Cookie Groups / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-8 offset-sm-4">
                                <?php wp_nonce_field('borlabs_cookie_cookie_groups_reset_default'); ?>
                                <input type="hidden" name="action" value="resetDefault">
                                <button disabled id="executeCookieGroupsReset" type="submit" class="btn btn-danger btn-sm"><?php _ex('Reset', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
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
