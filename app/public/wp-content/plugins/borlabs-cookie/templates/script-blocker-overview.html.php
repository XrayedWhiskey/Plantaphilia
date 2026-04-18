<?php
if (\BorlabsCookie\Cookie\Backend\License::getInstance()->isPluginUnlocked()) {
?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?page=borlabs-cookie">Borlabs Cookie</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php _ex('Script Blocker', 'Backend / Script Blocker / Breadcrumb', 'borlabs-cookie'); ?></li>
    </ol>
</nav>

<?php echo \BorlabsCookie\Cookie\Backend\Messages::getInstance()->getAll(); ?>

<div class="row">
    <div class="col">
        <div class="px-3 pt-3 pb-3 bg-light shadow-sm rounded-top">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center border-bottom mb-3">
                <h3><?php _ex('Script Blocker', 'Backend / Script Blocker / Headline', 'borlabs-cookie'); ?></h3>
                <a href="?page=borlabs-cookie-script-blocker&amp;action=wizardStep-1" class="btn btn-primary btn-sm"><?php _ex('Add New', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></a>
            </div>
            <div class="row">
                <div class="col">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th><?php _ex('Name', 'Backend / Script Blocker / Table Headline', 'borlabs-cookie'); ?></th>
                                    <th><?php _ex('ID', 'Backend / Script Blocker / Table Headline', 'borlabs-cookie'); ?></th>
                                    <th><?php _ex('Handles', 'Backend / Script Blocker / Table Headline', 'borlabs-cookie'); ?></th>
                                    <th><?php _ex('Block Phrases', 'Backend / Script Blocker / Table Headline', 'borlabs-cookie'); ?></th>
                                    <th data-modal-ignore class="text-center"><?php _ex('Status', 'Backend / Script Blocker / Table Headline', 'borlabs-cookie'); ?></th>
                                    <th data-modal-ignore class="text-center"><i class="fas fa-lg fa-trash-alt"></i></th>
                                    <th data-modal-ignore class="text-center"><i class="fas fa-lg fa-edit"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($scriptBlocker)) {
                                    foreach ($scriptBlocker as $scriptBlockerData) {
                                    ?>
                                    <tr>
                                        <td>
                                        <?php
                                            echo esc_html($scriptBlockerData->name);
                                        ?>
                                        </td>
                                        <td>
                                        <?php
                                            echo esc_html($scriptBlockerData->script_blocker_id);
                                        ?>
                                        </td>
                                        <td><?php echo esc_html($scriptBlockerData->handles); ?></td>
                                        <td><?php echo esc_html($scriptBlockerData->js_block_phrases); ?></td>
                                        <td data-modal-ignore class="text-center">
                                            <a class="icon-link" href="<?php echo wp_nonce_url('?page=borlabs-cookie-script-blocker&amp;action=switchStatus&amp;id='.$scriptBlockerData->id, 'switchStatus_'.$scriptBlockerData->id); ?>"><i class="fas fa-lg<?php echo !empty($scriptBlockerData->status) ? ' fa-toggle-on text-green' : ' fa-toggle-off text-black-50'; ?>"></i></a>
                                        </td>
                                        <td data-modal-ignore class="text-center">
                                        <?php
                                            if($scriptBlockerData->undeletable === 0) {
                                                ?>
                                                <a data-href="<?php echo wp_nonce_url('?page=borlabs-cookie-script-blocker&amp;action=delete&amp;id='.$scriptBlockerData->id, 'delete_'.$scriptBlockerData->id); ?>" data-toggle="modal" data-target="#borlabsModalDelete" href="#" class="icon-link"><i class="fas fa-lg fa-trash-alt"></i></a>
                                                <?php
                                            } else {
                                                ?>-<?php
                                            }
                                        ?>
                                        </td>
                                        <td data-modal-ignore class="text-center">
                                            <a class="icon-link" href="?page=borlabs-cookie-script-blocker&amp;action=edit&amp;id=<?php echo $scriptBlockerData->id; ?>"><i class="fas fa-lg fa-edit"></i></a>
                                        </td>
                                    </tr>
                                    <?php
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="7" class="text-center"><?php _ex('No <strong>Script Blocker</strong> configured.', 'Backend / Script Blocker / Alert Message', 'borlabs-cookie'); ?></td>
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

             <div class="accordion" id="accordionScriptBlocker">

                <button type="button" data-toggle="collapse" data-target="#accordionScriptBlockerOne" aria-expanded="true">
                    <?php _ex('What is a Script Blocker?', 'Backend / Script Blocker / Tips / Headline', 'borlabs-cookie'); ?>
                </button>

                <div id="accordionScriptBlockerOne" class="collapse show" data-parent="#accordionScriptBlocker">
                    <p>
                        <?php _ex('With <strong>Script Blockers</strong> you can block individual JavaScripts and only run them after the visitor has given their consent. When you set up a <strong>Script Blocker</strong>, Borlabs Cookie searches your Website for used JavaScripts, displays them, and allows you to block them.', 'Backend / Script Blocker / Tips / Text', 'borlabs-cookie'); ?>
                    </p>
                    <p>
                        <?php _ex('With <strong>Script Blockers</strong> you can offer your visitors an opt-in solution for plugins like PixelYourSite.', 'Backend / Script Blocker / Tips / Text', 'borlabs-cookie'); ?>
                    </p>
                </div>

                <button type="button" class="collapsed" data-toggle="collapse" data-target="#accordionScriptBlockerTwo">
                    <?php _ex('Language independent', 'Backend / Script Blocker / Tips / Headline', 'borlabs-cookie'); ?>
                </button>

                <div id="accordionScriptBlockerTwo" class="collapse" data-parent="#accordionScriptBlocker">
                    <p>
                        <?php _ex('<strong>Script Blockers</strong> are language independent and apply to all languages used on the website.', 'Backend / Script Blocker / Tips / Text', 'borlabs-cookie'); ?>
                    </p>
                </div>

                <button type="button" class="collapsed" data-toggle="collapse" data-target="#accordionScriptBlockerThree">
                    <?php _ex('Symbols explained', 'Backend / Script Blocker / Tips / Headline', 'borlabs-cookie'); ?>
                </button>

                <div id="accordionScriptBlockerThree" class="collapse" data-parent="#accordionScriptBlocker">
                    <p class="bg-tips-text mb-0">
                        <i class="fas fa-lg fa-fw fa-trash-alt"></i> <?php _ex('Delete the <strong>Script Blocker</strong>.', 'Backend / Script Blocker / Tips / Text', 'borlabs-cookie'); ?><br>
                        <i class="fas fa-lg fa-fw fa-edit"></i> <?php _ex('Edit the <strong>Script Blocker</strong>.', 'Backend / Script Blocker / Tips / Text', 'borlabs-cookie'); ?><br>
                        <i class="fas fa-lg fa-fw fa-toggle-on text-green"></i> <?php _ex('The <strong>Script Blocker</strong> is active and blocks the configured JavaScript handles.', 'Backend / Script Blocker / Tips / Text', 'borlabs-cookie'); ?><br>
                        <i class="fas fa-lg fa-fw fa-toggle-off"></i> <?php _ex('The <strong>Script Blocker</strong> is inactive and does not block the configured JavaScript handles.', 'Backend / Script Blocker / Tips / Text', 'borlabs-cookie'); ?><br>
                    </p>
                </div>

             </div>
        </div>
    </div>
</div>

<?php
} else {
    echo \BorlabsCookie\Cookie\Backend\License::getInstance()->getLicenseMessageActivateKey();
}
?>
