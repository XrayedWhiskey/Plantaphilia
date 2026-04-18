<?php
if (\BorlabsCookie\Cookie\Backend\License::getInstance()->isPluginUnlocked()) {
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?page=borlabs-cookie">Borlabs Cookie</a></li>
        <li class="breadcrumb-item"><a href="?page=borlabs-cookie-cookies"><?php _ex('Cookies', 'Backend / Cookies / Breadcrumb', 'borlabs-cookie'); ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php _ex('Step 1: Select a Service', 'Backend / Cookies / Breadcrumb', 'borlabs-cookie'); ?></li>
    </ol>
</nav>

<?php echo \BorlabsCookie\Cookie\Backend\Messages::getInstance()->getAll(); ?>

<form action="?page=borlabs-cookie-cookies" method="post" id="BorlabsCookieForm">
    <div class="row no-gutters mb-4">
        <div class="col-12 col-md-8 rounded bg-light shadow-sm">
            <div class="px-3 pt-3 pb-4">
                <h3 class="border-bottom mb-3"><?php echo esc_html($cookieGroupData->name); ?>: <?php _ex('Select a Service', 'Backend / Cookies / Headline', 'borlabs-cookie'); ?></h3>
                <div class="row">
                    <div class="col-12">

                        <div class="form-group row">
                            <label for="service" class="col-sm-4 col-form-label"><?php _ex('Service', 'Backend / Cookies / Label', 'borlabs-cookie'); ?></label>
                            <div class="col-sm-8">
                                <select class="form-control form-control form-control-sm d-inline-block w-75 mr-2" name="service" id="service">
                                    <?php
                                        if (!empty($cookieServices)) {
                                            foreach ($cookieServices as $service => $name) {
                                                echo "<option value=\"".esc_attr($service)."\"".($service === 'Custom' ? ' selected' : '').">".esc_html($name)."</option>";
                                            }
                                        }
                                    ?>
                                </select>
                                <span data-toggle="tooltip" title="<?php _ex('Select a service or use <strong>Custom</strong> to create a <strong>Cookie</strong>, store JavaScript and provide cookie documentation to your visitors.', 'Backend / Cookies / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-question-circle fa-lg text-dark"></i></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-8 offset-sm-4">
                                <input type="hidden" name="id" value="new">
                                <input type="hidden" name="cookieGroupId" value="<?php echo $cookieGroupData->id; ?>">
                                <input type="hidden" name="action" value="edit">
                                <button type="submit" class="btn btn-primary btn-sm"><?php _ex('Next', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
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
