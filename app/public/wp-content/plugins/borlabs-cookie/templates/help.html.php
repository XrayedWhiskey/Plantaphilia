<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?page=borlabs-cookie">Borlabs Cookie</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php _ex('Help', 'Backend / Help / Breadcrumb', 'borlabs-cookie'); ?></li>
    </ol>
</nav>

<?php
if (\BorlabsCookie\Cookie\Backend\License::getInstance()->isPluginUnlocked() === false) {
    echo \BorlabsCookie\Cookie\Backend\License::getInstance()->getLicenseMessageActivateKey();
}
?>

<?php echo \BorlabsCookie\Cookie\Backend\Messages::getInstance()->getAll(); ?>

<div class="row mb-4">
    <div class="col-12 col-md-7">
        <div class="mb-4 px-3 pt-3 pb-4 rounded bg-light shadow-sm">
            <h3 class="border-bottom mb-3"><?php _ex('First Aid', 'Backend / Help / Headline', 'borlabs-cookie'); ?></h3>
            <div class="row">
                <div class="col-12">

                    <h4><?php _ex('Borlabs Cookie does not work? Try these things first:', 'Backend / Help / Headlines', 'borlabs-cookie'); ?></h4>
                    <ol class="help-list">
                        <li><span><?php _ex('Deactivate any caching plugins', 'Backend / Help / Text', 'borlabs-cookie'); ?></span></li>
                        <li><span><?php _ex('Deactivate Autoptimize or similar plugins', 'Backend / Help / Text', 'borlabs-cookie'); ?></span></li>
                        <li><span><?php _ex('Open your website in incognito/privacy mode', 'Backend / Help / Text', 'borlabs-cookie'); ?></span></li>
                        <li><span><?php _ex('Check for JavaScript errors (red text) in the console', 'Backend / Help / Text', 'borlabs-cookie'); ?></span></li>
                    </ol>

                    <h4><?php _ex('How do I open the JavaScript console?', 'Backend / Help / Headlines', 'borlabs-cookie'); ?></h4>
                    <h5><?php _ex('OS X', 'Backend / Help / Headline', 'borlabs-cookie'); ?></h5>
                    <p>
                        <?php _ex('Google Chrome: ALT + CMD + J', 'Backend / Help / Text', 'borlabs-cookie'); ?><br>
                        <?php _ex('Firefox: ALT + CMD + K', 'Backend / Help / Text', 'borlabs-cookie'); ?>
                    </p>

                    <h5><?php _ex('Windows', 'Backend / Help / Headline', 'borlabs-cookie'); ?></h5>
                    <p>
                        <?php _ex('Google Chrome: CTRL + SHIFT + J', 'Backend / Help / Text', 'borlabs-cookie'); ?><br>
                        <?php _ex('Firefox: CTRL + SHIFT + K', 'Backend / Help / Text', 'borlabs-cookie'); ?>
                    </p>

                    <h4><?php _ex('How do I test my website?', 'Backend / Help / Headlines', 'borlabs-cookie'); ?></h4>
                    <p><?php
                        $kbURL = _x('https://borlabs.io/kb/how-to-test-your-website/?utm_source=Borlabs+Cookie&utm_medium=Support+Link&utm_campaign=Analysis', 'Backend / Help / URL', 'borlabs-cookie');
                        $htmlLink = sprintf('<a href="%s" rel="nofollow noopener noreferrer" target="_blank">%s <i class="fas fa-external-link-alt"></i></a>', $kbURL, _x('here', 'Backend / Help / Text', 'borlabs-cookie'));
                        echo sprintf(_x('Detailed instructions on how to test your website for errors can be found %s.', 'Backend / Help / Text', 'borlabs-cookie'), $htmlLink);
                    ?></p>

                    <p class="text-center"><a href="<?php _ex('https://borlabs.io/support/?utm_source=Borlabs+Cookie&utm_medium=Support+Link&utm_campaign=Analysis', 'Backend / Help / URL', 'borlabs-cookie'); ?>" rel="nofollow noopener noreferrer" target="_blank"><?php _ex('Click here for Support', 'Backend / Help / Text', 'borlabs-cookie'); ?> <i class="fas fa-external-link-alt"></i></a></p>
                </div>
            </div>
        </div>

        <div class="mb-4 px-3 pt-3 pb-4 rounded bg-light shadow-sm">
            <h3 class="border-bottom mb-3"><?php _ex('Common Problems', 'Backend / Help / Headline', 'borlabs-cookie'); ?></h3>
            <div class="row">
                <div class="col-12">

                    <h4><?php _ex('Cookie Box appears repeatedly', 'Backend / Help / Headlines', 'borlabs-cookie'); ?></h4>
                    <p><?php _ex('Check if you entered the correct <strong>Domain</strong> under <strong>Settings &gt; Cookie Settings</strong>.', 'Backend / Help / Text', 'borlabs-cookie'); ?></p>

                    <h4><?php _ex('Cookie Box does not close', 'Backend / Help / Headlines', 'borlabs-cookie'); ?></h4>
                    <p><?php _ex('Check whether a JavaScript error (red text) is reported in the console. This is the most common cause of this behavior.', 'Backend / Help / Text', 'borlabs-cookie'); ?></p>

                    <h4><?php _ex('Cookie Box is not displayed', 'Backend / Help / Headlines', 'borlabs-cookie'); ?></h4>
                    <p><?php _ex('Check if <strong>Do Not Track</strong> is enabled in Borlabs Cookie and the browser where the <strong>Cookie Box</strong> does not appear.', 'Backend / Help / Text', 'borlabs-cookie'); ?></p>
                    <p><?php _ex('Check whether a JavaScript error (red text) is reported in the console.', 'Backend / Help / Text', 'borlabs-cookie'); ?></p>
                    <p><?php _ex('Open your website in incognito/privacy mode. This prevents the browser from loading from the cache or from using an already set cookie.', 'Backend / Help / Text', 'borlabs-cookie'); ?></p>

                    <p class="text-center"><a href="<?php _ex('https://borlabs.io/support/?utm_source=Borlabs+Cookie&utm_medium=Support+Link&utm_campaign=Analysis', 'Backend / Help / URL', 'borlabs-cookie'); ?>" rel="nofollow noopener noreferrer" target="_blank"><?php _ex('Click here for Support', 'Backend / Help / Text', 'borlabs-cookie'); ?> <i class="fas fa-external-link-alt"></i></a></p>
                </div>
            </div>
        </div>

    </div>
    <div class="col-12 col-md-5">
        <?php include \BorlabsCookie\Cookie\Backend\Backend::getInstance()->templatePath.'/improve-borlabs-cookie.html.php'; ?>
        <?php include \BorlabsCookie\Cookie\Backend\Backend::getInstance()->templatePath.'/system-check.html.php'; ?>
    </div>
</div>
