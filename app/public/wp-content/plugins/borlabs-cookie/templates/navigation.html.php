<div class="row">
    <div class="col">
        <nav class="navbar navbar-expand-lg navbar-light mb-4 bg-light shadow-sm rounded-bottom">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link<?php echo !empty($activeModule) && $activeModule == 'Dashboard' ? ' active' : ''; ?>" href="?page=borlabs-cookie"><?php _ex('Dashboard', 'Backend / Global / Navigation Entry', 'borlabs-cookie'); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php echo !empty($activeModule) && $activeModule == 'Settings' ? ' active' : ''; ?>" href="?page=borlabs-cookie-settings"><?php _ex('Settings', 'Backend / Global / Navigation Entry', 'borlabs-cookie'); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php echo !empty($activeModule) && $activeModule == 'CookieBox' ? ' active' : ''; ?>" href="?page=borlabs-cookie-cookie-box"><?php _ex('Cookie Box', 'Backend / Global / Navigation Entry', 'borlabs-cookie'); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php echo !empty($activeModule) && $activeModule == 'CookieGroups' ? ' active' : ''; ?>" href="?page=borlabs-cookie-cookie-groups"><?php _ex('Cookie Groups', 'Backend / Global / Navigation Entry', 'borlabs-cookie'); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php echo !empty($activeModule) && $activeModule == 'Cookies' ? ' active' : ''; ?>" href="?page=borlabs-cookie-cookies"><?php _ex('Cookies', 'Backend / Global / Navigation Entry', 'borlabs-cookie'); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php echo !empty($activeModule) && $activeModule == 'ContentBlocker' ? ' active' : ''; ?>" href="?page=borlabs-cookie-content-blocker"><?php _ex('Content Blocker', 'Backend / Global / Navigation Entry', 'borlabs-cookie'); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php echo !empty($activeModule) && $activeModule == 'ScriptBlocker' ? ' active' : ''; ?>" href="?page=borlabs-cookie-script-blocker"><?php _ex('Script Blocker', 'Backend / Global / Navigation Entry', 'borlabs-cookie'); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php echo !empty($activeModule) && $activeModule == 'ImportExport' ? ' active' : ''; ?>" href="?page=borlabs-cookie-import-export"><?php _ex('Import &amp; Export', 'Backend / Global / Navigation Entry', 'borlabs-cookie'); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php echo !empty($activeModule) && $activeModule == 'License' ? ' active' : ''; ?>" href="?page=borlabs-cookie-license"><?php _ex('License', 'Backend / Global / Navigation Entry', 'borlabs-cookie'); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php echo !empty($activeModule) && $activeModule == 'Help' ? ' active' : ''; ?>" href="?page=borlabs-cookie-help"><?php _ex('Help &amp; Support', 'Backend / Global / Navigation Entry', 'borlabs-cookie'); ?></a>
                    </li>
                </ul>
            </div>
            <?php
            if ($multilanguagePluginIsActive) {
                ?>

                <?php
                    if($needsLanguageChooser) {
                ?>
                       <select id="borlabsCookieLanguageChooser" class="form-control form-control-sm w-auto pr-4">
                           <?php foreach($availableLanguagesForChooser as $availableLanguageForChooser) { ?>
                           <option value="<?php echo $availableLanguageForChooser['code'] ?>" <?php echo $availableLanguageForChooser['code'] == $currentLanguageCode ? 'selected' : '' ?>>
                               <?php echo $availableLanguageForChooser['name']; ?>
                           </option>
                           <?php } ?>
                       </select>
                        <?php
                    } else {
                        ?>
                        <a class="navbar-brand" href="#">
                            <span data-toggle="tooltip" title="<?php echo $currentLanguageTooltipText; ?>"><?php echo $currentFlag; ?></span>
                        </a>
                        <?php
                    }
                        ?>
                <?php
            }
            ?>
        </nav>
    </div>
</div>
