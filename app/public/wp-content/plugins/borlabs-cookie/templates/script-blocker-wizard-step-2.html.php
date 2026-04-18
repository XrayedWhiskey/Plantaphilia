<?php
if (\BorlabsCookie\Cookie\Backend\License::getInstance()->isPluginUnlocked()) {
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?page=borlabs-cookie">Borlabs Cookie</a></li>
        <li class="breadcrumb-item"><a href="?page=borlabs-cookie-script-blocker"><?php _ex('Script Blocker', 'Backend / Script Blocker / Breadcrumb', 'borlabs-cookie'); ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php _ex('Wizard Step 2: Scanning for JavaScripts', 'Backend / Script Blocker / Breadcrumb', 'borlabs-cookie'); ?></li>
    </ol>
</nav>

<?php echo \BorlabsCookie\Cookie\Backend\Messages::getInstance()->getAll(); ?>

<form action="?page=borlabs-cookie-script-blocker&action=wizardStep-3" method="post" id="BorlabsCookieForm">
    <div class="row no-gutters mb-4">
        <div class="col-12 col-md-8 rounded-left bg-light shadow-sm">
            <div class="px-3 pt-3 pb-4">
                <h3 class="border-bottom mb-3"><?php _ex('Scanning for JavaScripts', 'Backend / Script Blocker / Headline', 'borlabs-cookie'); ?></h3>
                <div class="row">
                    <div class="col">

                        <div class="alert alert-warning mt-2"><?php _ex('If you use caching and optimization plugins such as Autoptimize, please disable them before scanning.', 'Backend / Script Blocker / Text', 'borlabs-cookie'); ?></div>

                        <div data-borlabs-cookie-loading>
                            <div class="text-center">
                                <img src="<?php echo $loadingIcon; ?>" alt="" class="fa-spin" style="width: 32px; height: 32px; margin-bottom: 1rem;">
                                <p><?php _ex('Please wait...', 'Backend / Global / Loading', 'borlabs-cookie'); ?></p>
                            </div>
                        </div>

                        <div data-borlabs-cookie-scan-error class="borlabs-hide">
                            <div class="alert alert-danger mt-2"><?php _ex('The website could not be scanned. Please <a href="#" target="_blank" rel="noopener noreferrer">click here</a> to scan the website manually. Use incognito mode. After your website has loaded, return to this window.', 'Backend / Script Blocker / Alert Message', 'borlabs-cookie'); ?></div>
                        </div>

                        <div data-borlabs-cookie-scan-complete class="borlabs-hide">
                            <div class="alert alert-success mt-2"><?php _ex('Scan completed. Please click <strong>Next</strong> button to continue.', 'Backend / Script Blocker / Alert Message', 'borlabs-cookie'); ?></div>

                            <input type="hidden" name="scanURL" value="<?php echo $inputScanURL; ?>">
                            <input type="hidden" name="searchPhrases" value="<?php echo $inputSearchPhrases; ?>">

                            <div class="row">
                                <div class="col-sm-8 offset-sm-4">
                                    <button type="submit" class="btn btn-primary btn-sm"><?php _ex('Next', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4 rounded-right shadow-sm bg-tips text-light">
            <div class="px-3 pt-3 pb-3 mb-4">
                <h3 class="border-bottom mb-3"><?php _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>

                <h4><?php _ex('How does it work?', 'Backend / Script Blocker / Tips / Headline', 'borlabs-cookie'); ?></h4>
                <p><?php _ex('Borlabs Cookie scans your website, please be patient.', 'Backend / Script Blocker / Tips / Text', 'borlabs-cookie'); ?></p>
                <p><?php _ex('If you receive an error message, you have to perform the scan again by clicking on the link. Please use the incognito mode of your browser. Otherwise, JavaScripts that are only relevant for the admin will also be displayed.', 'Backend / Script Blocker / Tips / Text', 'borlabs-cookie'); ?></p>
            </div>
        </div>

    </div>
</form>
<?php
} else {
    echo \BorlabsCookie\Cookie\Backend\License::getInstance()->getLicenseMessageActivateKey();
}
?>
