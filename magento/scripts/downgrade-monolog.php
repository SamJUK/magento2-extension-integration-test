<?php

use Composer\InstalledVersions;
use Composer\Semver\VersionParser;

/**
 * Monolog has been updated but Magento does not match the signature. That's why we want to downgrade it sometimes.
 * See: https://github.com/magento/magento2/pull/35596
 */

require '/data/vendor/autoload.php';

$version = getenv('MAGENTO_VERSION');
$is244 = substr($version, 0, 5) == '2.4.4';
$is245 = substr($version, 0, 5) == '2.4.5';
$isP1 = substr($version, 6, 8) == 'p1';
$isP2 = substr($version, 6, 8) == 'p2';

if (($is244 && ($isP1 || $isP2)) || $is245) {
    echo 'No monolog changes needed, skipping' . PHP_EOL;
    return;
}

if (!class_exists(InstalledVersions::class)) {
    return;
}

$installed = InstalledVersions::satisfies(new VersionParser(), 'monolog/monolog', '^2.7');

if (!$installed) {
    echo 'monolog/monolog seems not to be installed in version ^2.7.0 so I\'m not downgrading' . PHP_EOL;
    return;
}

$output = null;
$code = null;
exec('composer require monolog/monolog:2.6.0', $output, $code);

if ($code !== 0) {
    echo 'Unable to downgrade monolog' . PHP_EOL;
    die($code);
}
