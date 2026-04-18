<?php
if (\BorlabsCookie\Cookie\Backend\License::getInstance()->isPluginUnlocked()) {

    $scriptTagCounter = 1;
    ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?page=borlabs-cookie">Borlabs Cookie</a></li>
            <li class="breadcrumb-item"><a href="?page=borlabs-cookie-script-blocker"><?php _ex('Script Blocker', 'Backend / Script Blocker / Breadcrumb', 'borlabs-cookie'); ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php _ex('Wizard Step 3: Configure Script Blocker', 'Backend / Script Blocker / Breadcrumb', 'borlabs-cookie'); ?></li>
        </ol>
    </nav>

    <?php echo \BorlabsCookie\Cookie\Backend\Messages::getInstance()->getAll(); ?>

    <form action="?page=borlabs-cookie-script-blocker" method="post" id="BorlabsCookieForm" class="needs-validation" novalidate>
        <div class="row no-gutters mb-4">
            <div class="col-12 col-md-8 rounded bg-light shadow-sm">
                <div class="px-3 pt-3 pb-4">
                    <div class="row">
                        <div class="col">
                            <h3 class="border-bottom mb-3"><?php _ex('Configure Script Blocker', 'Backend / Script Blocker / Headline', 'borlabs-cookie'); ?></h3>

                            <div class="form-group row">
                                <label for="scriptBlockerId" class="col-sm-4 col-form-label"><?php _ex('ID', 'Backend / Script Blocker / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="scriptBlockerId" name="scriptBlockerId" value="<?php echo $inputScriptBlockerId; ?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" required pattern="[a-z-_]{3,}">
                                    <span data-toggle="tooltip" title="<?php echo esc_attr_x('<strong>ID</strong> must be set. The <strong>ID</strong> must be at least 3 characters long and may only contain: <strong><em>a-z - _</em></strong>', 'Backend / Script Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                                    <div class="invalid-feedback"><?php _ex('Invalid <strong>ID</strong> name. Only use <strong><em>a-z - _</em></strong>', 'Backend / Global / Validation Message', 'borlabs-cookie'); ?></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="name" class="col-sm-4 col-form-label"><?php _ex('Name', 'Backend / Script Blocker / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="name" name="name" value="<?php echo $inputName; ?>" autocomplete="off" required>
                                    <span data-toggle="tooltip" title="<?php echo esc_attr_x('Choose a name for this <strong>Script Blocker</strong>.', 'Backend / Script Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                                    <div class="invalid-feedback"><?php _ex('This is a required field and cannot be empty.', 'Backend / Global / Validation Message', 'borlabs-cookie'); ?></div>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label class="col-sm-4 col-form-label"><?php _ex('Status', 'Backend / Script Blocker / Label', 'borlabs-cookie'); ?></label>
                                <div class="col-sm-8">
                                    <button type="button" class="btn btn-sm btn-toggle mr-2<?php echo $switchStatus; ?>" data-toggle="button" data-switch-target="status" aria-pressed="<?php echo $inputStatus ? 'true' : 'false'; ?>" autocomplete="off"><span class="handle"></span></button>
                                    <input type="hidden" name="status" id="status" value="<?php echo $inputStatus; ?>">
                                    <span data-toggle="tooltip" title="<?php echo esc_attr_x('The status of this <strong>Script Blocker</strong>. If active (Status: ON) it will block the configured JavaScript.', 'Backend / Script Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-8 offset-sm-4">
                            <?php wp_nonce_field('borlabs_cookie_script_blocker_create'); ?>
                            <input type="hidden" name="action" value="create">
                            <input type="hidden" name="id" value="new">
                            <button type="submit" class="btn btn-primary btn-sm"><?php _ex('Create Script Blocker', 'Backend / Script Blocker / Button Title', 'borlabs-cookie'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        if (!empty($detectedJavaScripts['handles']['matchedSearchPhrase'])) {
            ?>
            <div class="row no-gutters mb-4">
                <div class="col-12 col-md-8 rounded-left bg-light shadow-sm">
                    <div class="px-3 pt-3 pb-4">
                        <div class="row">
                            <div class="col">
                                <h3 class="border-bottom mb-3"><?php _ex('Matched JavaScript Handles', 'Backend / Script Blocker / Headline', 'borlabs-cookie'); ?></h3>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead class="thead-dark">
                                        <tr>
                                            <th data-modal-ignore class="text-center"><?php _ex('Block', 'Backend / Script Blocker / Table Headline', 'borlabs-cookie'); ?></th>
                                            <th><?php _ex('Handle', 'Backend / Script Blocker / Table Headline', 'borlabs-cookie'); ?></th>
                                            <th><?php _ex('Source', 'Backend / Script Blocker / Table Headline', 'borlabs-cookie'); ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        foreach ($detectedJavaScripts['handles']['matchedSearchPhrase'] as $handleData) {
                                            $inputHandleStatus  = esc_attr(!empty($blockHandles[$handleData['handle']]) || !isset($blockHandles[$handleData['handle']]) ? 1 : 0);
                                            $switchHandleStatus = $inputHandleStatus ? ' active' : '';
                                            ?>
                                            <tr>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-toggle mr-2<?php echo $switchHandleStatus; ?>" data-toggle="button" data-switch-target="block-<?php echo esc_attr($handleData['handle']); ?>" aria-pressed="<?php echo $inputHandleStatus ? 'true' : 'false'; ?>"><span class="handle"></span></button>
                                                    <input type="hidden" name="blockHandles[<?php echo esc_attr($handleData['handle']); ?>]" id="block-<?php echo esc_attr($handleData['handle']); ?>" value="<?php echo $inputHandleStatus; ?>">
                                                </td>
                                                <td><?php echo esc_html($handleData['handle']); ?></td>
                                                <td><?php echo esc_html($handleData['src']); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-8 offset-sm-4">
                                <button type="submit" class="btn btn-primary btn-sm"><?php _ex('Create Script Blocker', 'Backend / Script Blocker / Button Title', 'borlabs-cookie'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4 rounded-right shadow-sm bg-tips text-light">
                    <div class="px-3 pt-3 pb-3 mb-4">
                        <h3 class="border-bottom mb-3"><?php _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>

                        <h4><?php _ex('Matched JavaScript Handles explained', 'Backend / Script Blocker / Tips / Headline', 'borlabs-cookie'); ?></h4>
                        <p><?php _ex('All handles and file names in which the search phrases were found are displayed here.', 'Backend / Script Blocker / Tips / Text', 'borlabs-cookie'); ?></p>
                        <p><?php _ex('A handle is a developer-defined ID used to register a JavaScript file within the WordPress system.', 'Backend / Script Blocker / Tips / Text', 'borlabs-cookie'); ?></p>
                        <p><?php _ex('Normally JavaScript can be identified and blocked by the handle.', 'Backend / Script Blocker / Tips / Text', 'borlabs-cookie'); ?></p>
                    </div>
                </div>
            </div>
            <?php
        }

        if (!empty($detectedJavaScripts['scriptTags']['matchedSearchPhrase'])) {
            ?>
            <div class="row no-gutters mb-4">
                <div class="col-12 col-md-8 rounded-left bg-light shadow-sm">
                    <div class="px-3 pt-3 pb-4">
                        <div class="row">
                            <div class="col">
                                <h3 class="border-bottom mb-3"><?php _ex('Matched JavaScripts', 'Backend / Script Blocker / Headline', 'borlabs-cookie'); ?></h3>
                                <?php
                                foreach ($detectedJavaScripts['scriptTags']['matchedSearchPhrase'] as $scriptTagData) {
                                    $inputScriptTagStatus  = esc_attr(!empty($blockedScriptTags[$scriptTagCounter]) || !isset($blockedScriptTags[$scriptTagCounter]) ? 1 : 0);
                                    $switchScriptTagStatus = $inputScriptTagStatus ? ' active' : '';
                                    $inputScriptTagBlockPhrase = esc_attr(!empty($blockedPhrases[$scriptTagCounter]) ? $blockedPhrases[$scriptTagCounter] : $scriptTagData['matchedPhrase']);
                                    ?>
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <label for="scriptTag-<?php echo $scriptTagCounter; ?>"><?php _ex('Detected JavaScript', 'Backend / Script Blocker / Label', 'borlabs-cookie'); ?></label>
                                            <div class="code-editor"><textarea data-borlabs-html-editor name="scriptTags[<?php echo $scriptTagCounter; ?>]" id="scriptTag-<?php echo $scriptTagCounter; ?>" rows="5"><?php echo esc_textarea($scriptTagData['scriptTag']); ?></textarea></div>
                                        </div>
                                    </div>

                                    <div class="form-group row align-items-center">
                                        <label class="col-sm-4 col-form-label"><?php _ex('Block', 'Backend / Script Blocker / Label', 'borlabs-cookie'); ?></label>
                                        <div class="col-sm-8">
                                            <button type="button" class="btn btn-sm btn-toggle mr-2<?php echo $switchScriptTagStatus; ?>" data-toggle="button" data-switch-target="block-<?php echo $scriptTagCounter; ?>" data-switch-block-phrase aria-pressed="<?php echo $inputScriptTagStatus ? 'true' : 'false'; ?>" autocomplete="off"><span class="handle"></span></button>
                                            <input type="hidden" name="blockScriptTags[<?php echo $scriptTagCounter; ?>]" id="block-<?php echo $scriptTagCounter; ?>" value="<?php echo $inputScriptTagStatus; ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row align-items-center">
                                        <label for="blockPhrase-<?php echo $scriptTagCounter; ?>" class="col-sm-4 col-form-label"><?php _ex('Block Phrase', 'Backend / Script Blocker / Label', 'borlabs-cookie'); ?></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="blockPhrase-<?php echo $scriptTagCounter; ?>" name="blockPhrases[<?php echo $scriptTagCounter; ?>]" value="<?php echo $inputScriptTagBlockPhrase; ?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" <?php echo empty($inputScriptTagStatus) ? 'disabled' : 'required'; ?>>
                                            <span data-toggle="tooltip" title="<?php echo esc_attr_x('A string that must be found in inline JavaScript to block it. The phrase must be at least five characters long.', 'Backend / Script Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                                            <div class="invalid-feedback"><?php _ex('The phrase must be set and found in the inline JavaScript to block it. The phrase must be at least five characters long.', 'Backend / Script Blocker / Validation Message', 'borlabs-cookie'); ?></div>
                                        </div>
                                    </div>

                                    <hr>
                                    <?php
                                    $scriptTagCounter++;
                                }
                                ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-8 offset-sm-4">
                                <button type="submit" class="btn btn-primary btn-sm"><?php _ex('Create Script Blocker', 'Backend / Script Blocker / Button Title', 'borlabs-cookie'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4 rounded-right shadow-sm bg-tips text-light">
                    <div class="px-3 pt-3 pb-3 mb-4">
                        <h3 class="border-bottom mb-3"><?php _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>

                        <h4><?php _ex('Matched JavaScripts explained', 'Backend / Script Blocker / Tips / Headline', 'borlabs-cookie'); ?></h4>
                        <p><?php _ex('All JavaScripts in which the search phrases were found are displayed here.', 'Backend / Script Blocker / Tips / Text', 'borlabs-cookie'); ?></p>
                    </div>
                </div>
            </div>
            <?php
        }

        if (!empty($detectedJavaScripts['handles']['notMatchedSearchPhrase']) && count($detectedJavaScripts['handles']['notMatchedSearchPhrase'], COUNT_RECURSIVE)) {
            ?>
            <div class="row no-gutters mb-4">
                <div class="col-12 col-md-8 rounded-left bg-light shadow-sm">
                    <div class="px-3 pt-3 pb-4">
                        <div class="row">
                            <div class="col">
                                <h3 class="border-bottom mb-3"><?php _ex('Additional JavaScript Handles', 'Backend / Script Blocker / Headline', 'borlabs-cookie'); ?></h3>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead class="thead-dark">
                                        <tr>
                                            <th data-modal-ignore class="text-center"><?php _ex('Block', 'Backend / Script Blocker / Table Headline', 'borlabs-cookie'); ?></th>
                                            <th><?php _ex('Handle', 'Backend / Script Blocker / Table Headline', 'borlabs-cookie'); ?></th>
                                            <th><?php _ex('Source', 'Backend / Script Blocker / Table Headline', 'borlabs-cookie'); ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php

                                        if (!empty($detectedJavaScripts['handles']['notMatchedSearchPhrase']['external'])) {

                                            ksort($detectedJavaScripts['handles']['notMatchedSearchPhrase']['external']);

                                            ?>
                                            <tr>
                                                <td class="bg-danger text-light text-bold" colspan="3">
                                                    <?php _ex('External Scripts', 'Backend / Script Blocker / Table Headline', 'borlabs-cookie'); ?>
                                                </td>
                                            </tr>
                                            <?php

                                            foreach ($detectedJavaScripts['handles']['notMatchedSearchPhrase']['external'] as $handleData) {
                                                $inputHandleStatus  = esc_attr(!empty($blockHandles[$handleData['handle']]) ? 1 : 0);
                                                $switchHandleStatus = $inputHandleStatus ? ' active' : '';
                                                ?>
                                                <tr class="table-danger">
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-toggle mr-2<?php echo $switchHandleStatus; ?>" data-toggle="button" data-switch-target="block-<?php echo esc_attr($handleData['handle']); ?>" aria-pressed="<?php echo $inputHandleStatus ? 'true' : 'false'; ?>" autocomplete="off"><span class="handle"></span></button>
                                                        <input type="hidden" name="blockHandles[<?php echo esc_attr($handleData['handle']); ?>]" id="block-<?php echo esc_attr($handleData['handle']); ?>" value="<?php echo $inputHandleStatus; ?>">
                                                    </td>
                                                    <td><?php echo esc_html($handleData['handle']); ?></td>
                                                    <td><?php echo esc_html($handleData['src']); ?></td>
                                                </tr>
                                                <?php
                                            }
                                        }

                                        if (!empty($detectedJavaScripts['handles']['notMatchedSearchPhrase']['other'])) {

                                            ksort($detectedJavaScripts['handles']['notMatchedSearchPhrase']['other']);

                                            ?>
                                            <tr>
                                                <td class="table-dark text-bold" colspan="3">
                                                    <?php _ex('Other Scripts', 'Backend / Script Blocker / Table Headline', 'borlabs-cookie'); ?>
                                                </td>
                                            </tr>
                                            <?php

                                            foreach ($detectedJavaScripts['handles']['notMatchedSearchPhrase']['other'] as $handleData) {
                                                $inputHandleStatus  = esc_attr(!empty($blockHandles[$handleData['handle']]) ? 1 : 0);
                                                $switchHandleStatus = $inputHandleStatus ? ' active' : '';
                                                ?>
                                                <tr>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-toggle mr-2<?php echo $switchHandleStatus; ?>" data-toggle="button" data-switch-target="block-<?php echo esc_attr($handleData['handle']); ?>" aria-pressed="<?php echo $inputHandleStatus ? 'true' : 'false'; ?>" autocomplete="off"><span class="handle"></span></button>
                                                        <input type="hidden" name="blockHandles[<?php echo esc_attr($handleData['handle']); ?>]" id="block-<?php echo esc_attr($handleData['handle']); ?>" value="<?php echo $inputHandleStatus; ?>">
                                                    </td>
                                                    <td><?php echo esc_html($handleData['handle']); ?></td>
                                                    <td><?php echo esc_html($handleData['src']); ?></td>
                                                </tr>
                                                <?php
                                            }
                                        }

                                        if (!empty($detectedJavaScripts['handles']['notMatchedSearchPhrase']['plugin'])) {

                                            ksort($detectedJavaScripts['handles']['notMatchedSearchPhrase']['plugin']);

                                            ?>
                                            <tr>
                                                <td class="table-dark text-bold" colspan="3">
                                                    <?php _ex('Plugin Scripts', 'Backend / Script Blocker / Table Headline', 'borlabs-cookie'); ?>
                                                </td>
                                            </tr>
                                            <?php

                                            foreach ($detectedJavaScripts['handles']['notMatchedSearchPhrase']['plugin'] as $handleData) {
                                                $inputHandleStatus  = esc_attr(!empty($blockHandles[$handleData['handle']]) ? 1 : 0);
                                                $switchHandleStatus = $inputHandleStatus ? ' active' : '';
                                                ?>
                                                <tr>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-toggle mr-2<?php echo $switchHandleStatus; ?>" data-toggle="button" data-switch-target="block-<?php echo esc_attr($handleData['handle']); ?>" aria-pressed="<?php echo $inputHandleStatus ? 'true' : 'false'; ?>" autocomplete="off"><span class="handle"></span></button>
                                                        <input type="hidden" name="blockHandles[<?php echo esc_attr($handleData['handle']); ?>]" id="block-<?php echo esc_attr($handleData['handle']); ?>" value="<?php echo $inputHandleStatus; ?>">
                                                    </td>
                                                    <td><?php echo esc_html($handleData['handle']); ?></td>
                                                    <td><?php echo esc_html($handleData['src']); ?></td>
                                                </tr>
                                                <?php
                                            }
                                        }

                                        if (!empty($detectedJavaScripts['handles']['notMatchedSearchPhrase']['theme'])) {

                                            ksort($detectedJavaScripts['handles']['notMatchedSearchPhrase']['theme']);

                                            ?>
                                            <tr>
                                                <td class="table-dark text-bold" colspan="3">
                                                    <?php _ex('Theme Scripts', 'Backend / Script Blocker / Table Headline', 'borlabs-cookie'); ?>
                                                </td>
                                            </tr>
                                            <?php

                                            foreach ($detectedJavaScripts['handles']['notMatchedSearchPhrase']['theme'] as $handleData) {
                                                $inputHandleStatus  = esc_attr(!empty($blockHandles[$handleData['handle']]) ? 1 : 0);
                                                $switchHandleStatus = $inputHandleStatus ? ' active' : '';
                                                ?>
                                                <tr>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-toggle mr-2<?php echo $switchHandleStatus; ?>" data-toggle="button" data-switch-target="block-<?php echo esc_attr($handleData['handle']); ?>" aria-pressed="<?php echo $inputHandleStatus ? 'true' : 'false'; ?>" autocomplete="off"><span class="handle"></span></button>
                                                        <input type="hidden" name="blockHandles[<?php echo esc_attr($handleData['handle']); ?>]" id="block-<?php echo esc_attr($handleData['handle']); ?>" value="<?php echo $inputHandleStatus; ?>">
                                                    </td>
                                                    <td><?php echo esc_html($handleData['handle']); ?></td>
                                                    <td><?php echo esc_html($handleData['src']); ?></td>
                                                </tr>
                                                <?php
                                            }
                                        }

                                        if (!empty($detectedJavaScripts['handles']['notMatchedSearchPhrase']['core'])) {

                                            ksort($detectedJavaScripts['handles']['notMatchedSearchPhrase']['core']);

                                            ?>
                                            <tr>
                                                <td class="table-dark text-bold" colspan="3">
                                                    <?php _ex('WordPress Core Scripts', 'Backend / Script Blocker / Table Headline', 'borlabs-cookie'); ?>
                                                </td>
                                            </tr>
                                            <?php

                                            foreach ($detectedJavaScripts['handles']['notMatchedSearchPhrase']['core'] as $handleData) {
                                                $inputHandleStatus  = esc_attr(!empty($blockHandles[$handleData['handle']]) ? 1 : 0);
                                                $switchHandleStatus = $inputHandleStatus ? ' active' : '';
                                                ?>
                                                <tr>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-toggle mr-2<?php echo $switchHandleStatus; ?>" data-toggle="button" data-switch-target="block-<?php echo esc_attr($handleData['handle']); ?>" aria-pressed="<?php echo $inputHandleStatus ? 'true' : 'false'; ?>" autocomplete="off"><span class="handle"></span></button>
                                                        <input type="hidden" name="blockHandles[<?php echo esc_attr($handleData['handle']); ?>]" id="block-<?php echo esc_attr($handleData['handle']); ?>" value="<?php echo $inputHandleStatus; ?>">
                                                    </td>
                                                    <td><?php echo esc_html($handleData['handle']); ?></td>
                                                    <td><?php echo esc_html($handleData['src']); ?></td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-8 offset-sm-4">
                                <button type="submit" class="btn btn-primary btn-sm"><?php _ex('Create Script Blocker', 'Backend / Script Blocker / Button Title', 'borlabs-cookie'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4 rounded-right shadow-sm bg-tips text-light">
                    <div class="px-3 pt-3 pb-3 mb-4">
                        <h3 class="border-bottom mb-3"><?php _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>

                        <h4><?php _ex('Additional JavaScript Handles explained', 'Backend / Script Blocker / Tips / Headline', 'borlabs-cookie'); ?></h4>
                        <p><?php _ex('All handles found on the website that do not contain any of the search phrases are displayed here.', 'Backend / Script Blocker / Tips / Text', 'borlabs-cookie'); ?></p>
                        <p><?php _ex('JavaScripts loaded from an external source are marked red and usually should be blocked as well.', 'Backend / Script Blocker / Tips / Text', 'borlabs-cookie'); ?></p>
                    </div>
                </div>
            </div>
            <?php
        }

        if (!empty($detectedJavaScripts['scriptTags']['notMatchedSearchPhrase'])) {
            ?>
            <div class="row no-gutters mb-4">
                <div class="col-12 col-md-8 rounded-left bg-light shadow-sm">
                    <div class="px-3 pt-3 pb-4">
                        <div class="row">
                            <div class="col">
                                <h3 class="border-bottom mb-3"><?php _ex('Additional JavaScripts', 'Backend / Script Blocker / Headline', 'borlabs-cookie'); ?></h3>
                                <?php
                                foreach ($detectedJavaScripts['scriptTags']['notMatchedSearchPhrase'] as $scriptTagData) {
                                    $inputScriptTagStatus  = esc_attr(!empty($blockedScriptTags[$scriptTagCounter]) ? 1 : 0);
                                    $switchScriptTagStatus = $inputScriptTagStatus ? ' active' : '';
                                    $inputScriptTagBlockPhrase = esc_attr(!empty($blockedPhrases[$scriptTagCounter]) ? $blockedPhrases[$scriptTagCounter] : '');
                                    ?>
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <label for="scriptTag-<?php echo $scriptTagCounter; ?>"><?php _ex('Detected JavaScript', 'Backend / Script Blocker / Label', 'borlabs-cookie'); ?></label>
                                            <div class="code-editor"><textarea data-borlabs-html-editor name="scriptTags[<?php echo $scriptTagCounter; ?>]" id="scriptTag-<?php echo $scriptTagCounter; ?>" rows="5"><?php echo esc_textarea($scriptTagData['scriptTag']); ?></textarea></div>
                                        </div>
                                    </div>

                                    <div class="form-group row align-items-center">
                                        <label class="col-sm-4 col-form-label"><?php _ex('Block', 'Backend / Script Blocker / Label', 'borlabs-cookie'); ?></label>
                                        <div class="col-sm-8">
                                            <button type="button" class="btn btn-sm btn-toggle mr-2<?php echo $switchScriptTagStatus; ?>" data-toggle="button" data-switch-target="block-<?php echo $scriptTagCounter; ?>" data-switch-block-phrase aria-pressed="<?php echo $inputScriptTagStatus ? 'true' : 'false'; ?>" autocomplete="off"><span class="handle"></span></button>
                                            <input type="hidden" name="blockScriptTags[<?php echo $scriptTagCounter; ?>]" id="block-<?php echo $scriptTagCounter; ?>" value="<?php echo $inputScriptTagStatus; ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row align-items-center">
                                        <label for="blockPhrase-<?php echo $scriptTagCounter; ?>" class="col-sm-4 col-form-label"><?php _ex('Block Phrase', 'Backend / Script Blocker / Label', 'borlabs-cookie'); ?></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="blockPhrase-<?php echo $scriptTagCounter; ?>" name="blockPhrases[<?php echo $scriptTagCounter; ?>]" value="<?php echo $inputScriptTagBlockPhrase; ?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" <?php echo empty($inputScriptTagStatus) ? 'disabled' : 'required'; ?>>
                                            <span data-toggle="tooltip" title="<?php echo esc_attr_x('A string that must be found in inline JavaScript to block it. The phrase must be at least five characters long.', 'Backend / Script Blocker / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                                            <div class="invalid-feedback"><?php _ex('The phrase must be set and found in the inline JavaScript to block it. The phrase must be at least five characters long.', 'Backend / Script Blocker / Validation Message', 'borlabs-cookie'); ?></div>
                                        </div>
                                    </div>

                                    <hr>
                                    <?php
                                    $scriptTagCounter++;
                                }
                                ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-8 offset-sm-4">
                                <button type="submit" class="btn btn-primary btn-sm"><?php _ex('Create Script Blocker', 'Backend / Script Blocker / Button Title', 'borlabs-cookie'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4 rounded-right shadow-sm bg-tips text-light">
                    <div class="px-3 pt-3 pb-3 mb-4">
                        <h3 class="border-bottom mb-3"><?php _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>

                        <h4><?php _ex('Additional JavaScripts explained', 'Backend / Script Blocker / Tips / Headline', 'borlabs-cookie'); ?></h4>
                        <p><?php _ex('All JavaScripts found on the website that do not contain any of the search phrases are displayed here.', 'Backend / Script Blocker / Tips / Text', 'borlabs-cookie'); ?></p>
                        <p><?php _ex('If you want to block one of these JavaScript, enter a character string contained in the inline JavaScript into the field <strong>Block Phrase</strong> and activate the blocking via the button.', 'Backend / Script Blocker / Tips / Text', 'borlabs-cookie'); ?></p>
                        <p><?php _ex('The <strong>Script Blocker</strong> searches the JavaScript of the web page and blocks it if the phrase is found.', 'Backend / Script Blocker / Tips / Text', 'borlabs-cookie'); ?></p>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </form>
    <?php
} else {
    echo \BorlabsCookie\Cookie\Backend\License::getInstance()->getLicenseMessageActivateKey();
}
?>
