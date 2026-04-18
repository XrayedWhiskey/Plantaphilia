<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?page=borlabs-cookie">Borlabs Cookie</a></li>
        <li class="breadcrumb-item active"
            aria-current="page"><?php _ex('Dashboard', 'Backend / Dashboard / Breadcrumb', 'borlabs-cookie'); ?></li>
    </ol>
</nav>

<?php
if (\BorlabsCookie\Cookie\Backend\License::getInstance()->isPluginUnlocked() === false) {
    echo \BorlabsCookie\Cookie\Backend\License::getInstance()->getLicenseMessageActivateKey();
}
?>

<?php echo \BorlabsCookie\Cookie\Backend\Messages::getInstance()->getAll(); ?>

<div class="row">
    <div class="col">
        <div class="px-3 pt-3 pb-3 mb-4 bg-dark text-light shadow-sm rounded">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center border-bottom mb-3">
                <h3><?php printf(_x('Statistics <small class="text-muted">- Cookie Version %s</small>', 'Backend / Dashboard / Headline', 'borlabs-cookie'), $cookieVersion); ?></h3>
                <div class="btn-toolbar">
                    <div class="btn-group">
                        <a class="btn btn-sm <?php echo $statsActive6h ? 'btn-secondary' : 'btn-outline-secondary'; ?>"
                           href="?page=borlabs-cookie&amp;borlabsCookieStats=6h"><?php _ex('6 hours', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></a>
                        <a class="btn btn-sm <?php echo $statsActive7d ? 'btn-secondary' : 'btn-outline-secondary'; ?>"
                           href="?page=borlabs-cookie&amp;borlabsCookieStats=7d"><?php _ex('7 days', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></a>
                        <a class="btn btn-sm <?php echo $statsActive30d ? 'btn-secondary' : 'btn-outline-secondary'; ?>"
                           href="?page=borlabs-cookie"><?php _ex('30 days', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <?php
                    if (!empty($chartData)) {
                        ?>
                        <div style="height: 200px;">
                            <canvas id="borlabsCookieChart"></canvas>
                        </div>
                        <?php
                    } else {
                        ?>
                        <div class="alert alert-warning"
                             role="alert"><?php _ex('No data available yet. Please try again in a few hours.', 'Backend / Dashboard / Alert Message', 'borlabs-cookie'); ?></div><?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12 col-md-7">
        <?php
        if (!empty($news)) {
            ?>
            <div class="mb-4 px-3 pt-3 pb-4 rounded bg-light shadow-sm">
                <h3 class="border-bottom mb-3"><?php _ex('News', 'Backend / Dashboard / Headline', 'borlabs-cookie'); ?></h3>
                <div class="row">
                    <div class="col-12">
                        <?php
                        foreach ($news as $newsData) {
                            ?>
                            <div class="mb-4">
                                <h4 class="d-flex justify-content-between"><?php echo esc_html($newsData->title); ?>
                                    <small
                                        class="text-muted"><?php echo \BorlabsCookie\Cookie\Tools::getInstance()->formatTimestamp($newsData->timestamp, null, false); ?></small>
                                </h4>
                                <?php
                                $newsData->message = strip_tags($newsData->message, '<a><br><p><span><strong>');
                                $newsData->message = preg_replace('/on([a-zA-z])*=/i', 'data-escaped-on$1=', $newsData->message);
                                $newsData->message = str_replace('javascript:', '', $newsData->message);
                                echo $newsData->message; ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>

        <div class="mb-4 px-3 pt-3 pb-4 rounded bg-light shadow-sm">
            <h3 class="border-bottom mb-3"><?php _ex('Quick Start', 'Backend / Dashboard / Headline', 'borlabs-cookie'); ?></h3>
            <div class="row">
                <div class="col-12">
                    <ol class="help-list">
                        <li>
                            <span><?php _ex('Under <strong>Cookies</strong> enter your Cookies/JavaScripts, e.g. Google Analytics or Facebook Pixel. Make sure that you insert the JavaScripts such as Google Analytics <strong>only</strong> in the Borlabs Cookie.', 'Backend / Dashboard / Text', 'borlabs-cookie'); ?></span>
                        </li>
                        <li>
                            <span><?php _ex('Configure Borlabs Cookie under <strong>Settings</strong> and activate the system by turning <strong>Borlabs Cookie Status</strong> on.', 'Backend / Dashboard / Text', 'borlabs-cookie'); ?></span>
                        </li>
                        <li>
                            <span><?php _ex('You can customize its appearance under <strong>Cookie Box</strong>.', 'Backend / Dashboard / Text', 'borlabs-cookie'); ?></span>
                        </li>
                        <li>
                            <span><?php _ex('If you are using a caching plugin, flush its cache.', 'Backend / Dashboard / Text', 'borlabs-cookie'); ?></span>
                        </li>
                        <li>
                            <span><?php _ex('Open your website in incognito/privacy mode for testing.', 'Backend / Dashboard / Text', 'borlabs-cookie'); ?></span>
                        </li>
                    </ol>
                    <p><?php _ex('The Cookie Box should now appear on your website, the opt-in should work.', 'Backend / Dashboard / Text', 'borlabs-cookie'); ?></p>

                    <h4><?php _ex('How do I check my website for cookies?', 'Backend / Dashboard / Headline', 'borlabs-cookie'); ?></h4>
                    <p><?php
                        $kbURL = _x('https://borlabs.io/kb/how-to-check-your-website-for-cookies/?utm_source=Borlabs+Cookie&utm_medium=Dashboard+Link&utm_campaign=Analysis', 'Backend / Dashboard / URL', 'borlabs-cookie');
                        $htmlLink = sprintf('<a href="%s" rel="nofollow noopener noreferrer" target="_blank">%s <i class="fas fa-external-link-alt"></i></a>', $kbURL, _x('here', 'Backend / Dashboard / Text', 'borlabs-cookie'));
                        echo sprintf(_x('Detailed instructions on how to check your website for cookies can be found %s.', 'Backend / Dashboard / Text', 'borlabs-cookie'), $htmlLink);
                        ?></p>
                </div>
            </div>
        </div>

        <div class="mb-4 px-3 pt-3 pb-4 rounded bg-light shadow-sm">
            <h3 class="border-bottom mb-3"><?php _ex('Introduction Borlabs Cookie 2.0', 'Backend / Dashboard / Headline', 'borlabs-cookie'); ?></h3>
            <div class="row">
                <div class="col-12">
                    <a href="<?php _ex('https://borlabs.io/kb/introduction-borlabs-cookie-2-0/?utm_source=Borlabs+Cookie&amp;utm_medium=Dashboard+Video&amp;utm_campaign=Analysis', 'Backend / Dashboard / URL', 'borlabs-cookie'); ?>"
                       target="_blank" rel="nofollow noopener noreferrer"><img
                            src="<?php echo $this->imagePath; ?>/<?php _ex('video-en.jpg', 'Backend / Dashboard / Image', 'borlabs-cookie'); ?>"
                            class="img-fluid" alt=""></a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-5">
        <?php include \BorlabsCookie\Cookie\Backend\Backend::getInstance()->templatePath . '/upgrade-borlabs-cookie.html.php'; ?>
        <?php include \BorlabsCookie\Cookie\Backend\Backend::getInstance()->templatePath . '/improve-borlabs-cookie.html.php'; ?>
        <?php include \BorlabsCookie\Cookie\Backend\Backend::getInstance()->templatePath . '/system-check.html.php'; ?>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12 col-md-7">
        <div class="mb-4 px-3 pt-3 pb-4 rounded bg-light shadow-sm">
            <h3 class="border-bottom mb-3"><?php _ex('Get UID Consent History', 'Backend / Dashboard / Headline', 'borlabs-cookie'); ?></h3>
            <div class="row">
                <div class="col-12">

                    <div class="input-group mb-3">
                        <input type="text" class="form-control form-control-sm d-inline-block w-75"
                               id="borlabsCookieUID" name="borlabsCookieUID"
                               data-borlabs-cookie-language="<?php echo esc_attr($language); ?>" value=""
                               placeholder="UID: A1B2C3D4-E5F6G7H8-I9J1K2L3-M4N5O6P7">
                        <div class="input-group-append">
                            <button data-get-consent-history type="button"
                                    class="btn btn-primary btn-sm"><?php _ex('Send', 'Backend / Global / Button Title', 'borlabs-cookie'); ?></button>
                        </div>
                    </div>

                    <div data-borlabs-cookie-consent-history>
                        <div data-borlabs-cookie-loading class="borlabs-hide">
                            <div class="d-flex justify-content-center">
                                <img src="<?php echo $loadingIcon; ?>" alt="" class="fa-spin"
                                     style="width: 32px; height: 32px;">
                            </div>
                        </div>
                        <div data-borlabs-cookie-result class="borlabs-hide">
                            <table class="table table-striped">
                                <thead class="thead-dark">
                                <tr>
                                    <th><?php _ex('Date', 'Backend / Dashboard / Table Headline', 'borlabs-cookie'); ?></th>
                                    <th class="text-center"><?php _ex('Version', 'Backend / Dashboard / Table Headline', 'borlabs-cookie'); ?></th>
                                    <th><?php _ex('Consents', 'Backend / Dashboard / Table Headline', 'borlabs-cookie'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div data-borlabs-cookie-no-result class="borlabs-hide">
                            <div class="alert alert-warning"
                                 role="alert"><?php _ex('No data for UID found. Make sure the UID exists.', 'Backend / Dashboard / Alert Message', 'borlabs-cookie'); ?></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-5">
        <div class="px-3 pt-3 pb-4 rounded bg-light shadow-sm">
            <h3 class="border-bottom mb-3"><?php _ex('Latest UIDs', 'Backend / Dashboard / Headline', 'borlabs-cookie'); ?></h3>
            <div class="row">
                <div class="col-12">
                    <table class="table table-striped">
                        <thead class="thead-dark">
                        <tr>
                            <th><?php _ex('Date', 'Backend / Dashboard / Table Headline', 'borlabs-cookie'); ?></th>
                            <th class="text-center"><?php _ex('Version', 'Backend / Dashboard / Table Headline', 'borlabs-cookie'); ?></th>
                            <th><?php _ex('UID', 'Backend / Dashboard / Table Headline', 'borlabs-cookie'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (!empty($latestUIDData)) {
                            foreach ($latestUIDData as $logData) {
                                ?>
                                <tr>
                                    <td><?php echo $logData->stamp; ?></td>
                                    <td class="text-center"><?php echo $logData->cookie_version; ?></td>
                                    <td class="borlabs-cursor"
                                        data-clipboard-universal="borlabsCookieUID"><?php echo esc_html(strtoupper($logData->uid)); ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="3"
                                    class="text-center"><?php _ex('No UIDs found.', 'Backend / Dashboard / Alert Message', 'borlabs-cookie'); ?></td>
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

<script type="text/javascript">
    jQuery(document).ready(function () {
        (function ($) {

            <?php
            if (isset($showTelemetryModal) && $showTelemetryModal === true) {
                echo "window.setTimeout(function () { jQuery('#borlabsModalTelemetry').modal('show'); }, 250) ";
            }
            ?>

            var barChartData = <?php echo $chartData; ?>;

            if (Object.entries(barChartData).length) {

                var borlabsCookieChartCanvas = document.getElementById('borlabsCookieChart').getContext('2d');

                window.borlabsCookieBarChart = new Chart(borlabsCookieChartCanvas, {
                    type: <?php echo $statsActive30d ? '\'bar\'' : '\'line\''; ?>,
                    data: barChartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            xAxes: [{
                                gridLines: {
                                    color: "#454d54"
                                },
                                stacked: false,
                                ticks: {
                                    fontColor: "#fff",
                                },
                            }],
                            yAxes: [{
                                gridLines: {
                                    color: "#454d54"
                                },
                                stacked: false,
                                ticks: {
                                    beginAtZero: true,
                                    fontColor: "#fff",
                                    callback: function (value) {
                                        if (value % 1 === 0) {
                                            return value;
                                        }
                                    }
                                },
                            }],
                        },
                        legend: {
                            display: <?php echo $statsActive30d ? 'false' : 'true'; ?>,
                            labels: {
                                fontColor: "#fff",
                            }
                        },
                        title: {
                            display: false,
                        }
                    }
                });
            }
        }(jQuery));
    });
</script>
