<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\TrafficByDeviceType;

use DeviceDetector\Parser\Device\DeviceParserAbstract as DeviceParser;
use Piwik\Piwik;

function getDeviceTypeLabel($label)
{
    $translations = array(
        'desktop'       => 'General_Desktop',
        'smartphone'    => 'DevicesDetection_Smartphone',
        'tablet'        => 'DevicesDetection_Tablet',
        'feature phone' => 'DevicesDetection_FeaturePhone',
        'console'       => 'DevicesDetection_Console',
        'tv'            => 'DevicesDetection_TV',
        'car browser'   => 'DevicesDetection_CarBbrowser',
        'smart display' => 'DevicesDetection_SmartDisplay',
        'camera'        => 'DevicesDetection_Camera'
    );

    $deviceTypes = DeviceParser::getAvailableDeviceTypes();

    if (is_numeric($label) &&
        in_array($label, $deviceTypes) &&
        isset($translations[array_search($label, $deviceTypes)])) {

        return Piwik::translate($translations[array_search($label, $deviceTypes)]);
    } else if (isset($translations[$label])) {
        return Piwik::translate($translations[$label]);
    } else {
        return Piwik::translate('General_Unknown');
    }
}

function getDeviceTypeLogo($label)
{
    if (is_numeric($label) && in_array($label, DeviceParser::getAvailableDeviceTypes())) {
        $label = array_search($label, DeviceParser::getAvailableDeviceTypes());
    }

    $label = strtolower($label);

    $deviceTypeLogos = Array(
        "desktop"       => "normal.gif",
        "smartphone"    => "smartphone.png",
        "tablet"        => "tablet.png",
        "tv"            => "tv.png",
        "feature phone" => "mobile.gif",
        "console"       => "console.gif",
        "car browser"   => "carbrowser.png",
        "camera"        => "camera.png");

    if (!array_key_exists($label, $deviceTypeLogos)) {
        $label = 'unknown.gif';
    } else {
        $label = $deviceTypeLogos[$label];
    }
    $path = 'plugins/DevicesDetection/images/screens/' . $label;
    return $path;
}
