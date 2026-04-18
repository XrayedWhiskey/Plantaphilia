<div class="px-3 pt-3 pb-4 rounded bg-light shadow-sm">
    <h3 class="border-bottom mb-3"><?php
        _ex('System Status', 'Backend / System Check / Table Headline', 'borlabs-cookie'); ?></h3>
    <div class="row">
        <div class="col-12">
            <table class="table table-striped">
                <tbody>
                <tr>
                    <th class="w-50"><?php
                        _ex(
                            'Borlabs Cookie Status',
                            'Backend / System Check / Table Headline',
                            'borlabs-cookie'
                        ); ?></th>
                    <td><span class="badge status badge-<?php
                        echo $borlabsCookieStatus ? 'success' : 'secondary'; ?>"><?php
                            if ($borlabsCookieStatus) {
                                _ex('Active', 'Backend / System Check / Text', 'borlabs-cookie');
                            } else {
                                _ex('Inactive', 'Backend / System Check / Text', 'borlabs-cookie');
                            }
                            ?></span></td>
                </tr>
                <tr>
                    <th class="w-50"><?php
                        _ex('Current Version', 'Backend / System Check / Table Headline', 'borlabs-cookie'); ?></th>
                    <td><?php
                        echo BORLABS_COOKIE_VERSION; ?></td>
                </tr>
                <tr>
                    <th class="w-50"><?php
                        _ex(
                            'Language (Current / Default)',
                            'Backend / System Check / Table Headline',
                            'borlabs-cookie'
                        ); ?></th>
                    <td><?php
                        echo $language; ?> / <?php
                        echo BORLABS_COOKIE_DEFAULT_LANGUAGE; ?></td>
                </tr>
                <tr>
                    <th class="w-50"><?php
                        _ex('PHP Version', 'Backend / System Check / Table Headline', 'borlabs-cookie'); ?></th>
                    <td><?php
                        if ($statusPHPVersion['success']) {
                            ?><span class="badge status badge-success"><?php
                            _ex('OK', 'Backend / System Check / Text', 'borlabs-cookie'); ?></span><?php
                            echo $statusPHPVersion['message'];
                        } else {
                            ?><span class="badge status badge-danger"><?php
                            _ex('Error', 'Backend / System Check / Text', 'borlabs-cookie'); ?></span>
                            <br>
                            <?php
                            echo $statusPHPVersion['message'];
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-50"><?php
                        _ex('Database Version', 'Backend / System Check / Table Headline', 'borlabs-cookie'); ?></th>
                    <td><?php
                        if ($statusDBVersion['success']) {
                            ?><span class="badge status badge-success"><?php
                            _ex('OK', 'Backend / System Check / Text', 'borlabs-cookie'); ?></span><?php
                            echo $statusDBVersion['message'];
                        } else {
                            ?><span class="badge status badge-danger"><?php
                            _ex('Error', 'Backend / System Check / Text', 'borlabs-cookie'); ?></span>
                            <br>
                            <?php
                            echo $statusDBVersion['message'];
                        }
                        ?></td>
                </tr>
                <tr>
                    <th class="w-50"><?php
                        _ex('SSL', 'Backend / System Check / Table Headline', 'borlabs-cookie'); ?></th>
                    <td>
                        <?php
                        if ($statusSSLSettings['success']) {
                            ?><span class="badge status badge-success"><?php
                            _ex('OK', 'Backend / System Check / Text', 'borlabs-cookie'); ?></span><?php
                        } else {
                            ?><span class="badge status badge-danger"><?php
                            _ex('Error', 'Backend / System Check / Text', 'borlabs-cookie'); ?></span>
                            <br>
                            <?php
                            echo $statusSSLSettings['message'];
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-50"><?php
                        _ex('Cache Folder', 'Backend / System Check / Table Headline', 'borlabs-cookie'); ?></th>
                    <td>
                        <?php
                        if ($statusCacheFolder['success']) {
                            ?><span class="badge status badge-success"><?php
                            _ex('OK', 'Backend / System Check / Text', 'borlabs-cookie'); ?></span><?php
                        } else {
                            ?><span class="badge status badge-danger"><?php
                            _ex('Error', 'Backend / System Check / Text', 'borlabs-cookie'); ?></span>
                            <br>
                            <?php
                            echo $statusCacheFolder['message'];
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-50"><?php
                        _ex(
                            'Table Content Blocker',
                            'Backend / System Check / Table Headline',
                            'borlabs-cookie'
                        ); ?></th>
                    <td>
                        <?php
                        if ($statusTableContentBlocker['success']) {
                            ?><span class="badge status badge-success"><?php
                            _ex('OK', 'Backend / System Check / Text', 'borlabs-cookie'); ?></span><?php
                        } else {
                            ?><span class="badge status badge-danger"><?php
                            _ex('Error', 'Backend / System Check / Text', 'borlabs-cookie'); ?></span>
                            <br>
                            <?php
                            echo $statusTableContentBlocker['message'];
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-50"><?php
                        _ex(
                            'Table Consent ConsentLog',
                            'Backend / System Check / Table Headline',
                            'borlabs-cookie'
                        ); ?></th>
                    <td>
                        <?php
                        if ($statusTableCookieConsentLog['success']) {
                            ?><span class="badge status badge-success"><?php
                            _ex('OK', 'Backend / System Check / Text', 'borlabs-cookie'); ?></span><?php
                        } else {
                            ?><span class="badge status badge-danger"><?php
                            _ex('Error', 'Backend / System Check / Text', 'borlabs-cookie'); ?></span>
                            <br>
                            <?php
                            echo $statusTableCookieConsentLog['message'];
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-50"><?php
                        _ex('Table Cookie Groups', 'Backend / System Check / Table Headline', 'borlabs-cookie'); ?></th>
                    <td>
                        <?php
                        if ($statusTableCookieGroups['success']) {
                            ?><span class="badge status badge-success"><?php
                            _ex('OK', 'Backend / System Check / Text', 'borlabs-cookie'); ?></span><?php
                        } else {
                            ?><span class="badge status badge-danger"><?php
                            _ex('Error', 'Backend / System Check / Text', 'borlabs-cookie'); ?></span>
                            <br>
                            <?php
                            echo $statusTableCookieGroups['message'];
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-50"><?php
                        _ex('Table Cookies', 'Backend / System Check / Table Headline', 'borlabs-cookie'); ?></th>
                    <td>
                        <?php
                        if ($statusTableCookies['success']) {
                            ?><span class="badge status badge-success"><?php
                            _ex('OK', 'Backend / System Check / Text', 'borlabs-cookie'); ?></span><?php
                        } else {
                            ?><span class="badge status badge-danger"><?php
                            _ex('Error', 'Backend / System Check / Text', 'borlabs-cookie'); ?></span>
                            <br>
                            <?php
                            echo $statusTableCookies['message'];
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-50"><?php
                        _ex(
                            'Table Script Blocker',
                            'Backend / System Check / Table Headline',
                            'borlabs-cookie'
                        ); ?></th>
                    <td>
                        <?php
                        if ($statusTableScriptBlocker['success']) {
                            ?><span class="badge status badge-success"><?php
                            _ex('OK', 'Backend / System Check / Text', 'borlabs-cookie'); ?></span><?php
                        } else {
                            ?><span class="badge status badge-danger"><?php
                            _ex('Error', 'Backend / System Check / Text', 'borlabs-cookie'); ?></span>
                            <br>
                            <?php
                            echo $statusTableScriptBlocker['message'];
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-50"><?php
                        _ex(
                            'Table Statistics',
                            'Backend / System Check / Table Headline',
                            'borlabs-cookie'
                        ); ?></th>
                    <td>
                        <?php
                        if ($statusTableStatistics['success']) {
                            ?><span class="badge status badge-success"><?php
                            _ex('OK', 'Backend / System Check / Text', 'borlabs-cookie'); ?></span><?php
                        } else {
                            ?><span class="badge status badge-danger"><?php
                            _ex('Error', 'Backend / System Check / Text', 'borlabs-cookie'); ?></span>
                            <br>
                            <?php
                            echo $statusTableStatistics['message'];
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-50"><?php
                        _ex(
                            'Default Content Blocker',
                            'Backend / System Check / Table Headline',
                            'borlabs-cookie'
                        ); ?></th>
                    <td>
                        <?php
                        if ($statusDefaultContentBlocker['success']) {
                            ?><span class="badge status badge-success"><?php
                            _ex('OK', 'Backend / System Check / Text', 'borlabs-cookie'); ?></span><?php
                        } else {
                            ?><span class="badge status badge-danger"><?php
                            _ex('Error', 'Backend / System Check / Text', 'borlabs-cookie'); ?></span>
                            <br>
                            <?php
                            echo $statusDefaultContentBlocker['message'];
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-50"><?php
                        _ex(
                            'Default Cookie Groups',
                            'Backend / System Check / Table Headline',
                            'borlabs-cookie'
                        ); ?></th>
                    <td>
                        <?php
                        if ($statusDefaultCookieGroups['success']) {
                            ?><span class="badge status badge-success"><?php
                            _ex('OK', 'Backend / System Check / Text', 'borlabs-cookie'); ?></span><?php
                        } else {
                            ?><span class="badge status badge-danger"><?php
                            _ex('Error', 'Backend / System Check / Text', 'borlabs-cookie'); ?></span>
                            <br>
                            <?php
                            echo $statusDefaultCookieGroups['message'];
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-50"><?php
                        _ex('Default Cookies', 'Backend / System Check / Table Headline', 'borlabs-cookie'); ?></th>
                    <td>
                        <?php
                        if ($statusDefaultCookies['success']) {
                            ?><span class="badge status badge-success"><?php
                            _ex('OK', 'Backend / System Check / Text', 'borlabs-cookie'); ?></span><?php
                        } else {
                            ?><span class="badge status badge-danger"><?php
                            _ex('Error', 'Backend / System Check / Text', 'borlabs-cookie'); ?></span>
                            <br>
                            <?php
                            echo $statusDefaultCookies['message'];
                        }
                        ?>
                    </td>
                </tr>

                <tr>
                    <th class="w-50 align-middle">
                        <?php
                        _ex('Total Consent Logs', 'Backend / System Check / Text', 'borlabs-cookie'); ?>
                        <span data-toggle="tooltip" title="<?php
                        echo esc_attr_x(
                            'A click on <strong>Clean up</strong> deletes all consents logs that are older than the set <strong>Cookie Lifetime</strong>. Borlabs Cookie does this automatically once per day.',
                            'Backend / System Check / Tooltip',
                            'borlabs-cookie'
                        ); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                    </th>
                    <td data-total-consents>
                        <span data-total-consent-log-details class="align-middle"><?php
                            echo $totalConsentLogs; ?> / <?php
                            echo $consentLogTableSize; ?> MiB</span>
                        <button data-clean-up-consent-logs type="button" class="btn btn-primary btn-sm"><?php
                            _ex('Clean up', 'Backend / System Check / Button Title', 'borlabs-cookie'); ?></button>
                        <div class="d-inline-block">
                            <div data-borlabs-cookie-loading class="borlabs-hide"><img src="<?php
                                echo $loadingIcon; ?>" alt="" class="fa-spin" style="width: 16px; height: 16px;"></div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
