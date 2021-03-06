<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\TrafficByDeviceType;

use Piwik\Settings\SystemSetting;
use Piwik\Settings\UserSetting;
use Piwik\Piwik;

/**
 * Defines Settings for TrafficByDeviceTypePlugin.
 *
 */
class Settings extends \Piwik\Plugin\Settings
{
    /** @var SystemSetting */
    public $refreshInterval;

    protected function init()
    {
        $this->setIntroduction(Piwik::translate('TrafficByDeviceType_SettingsIntroduction'));

        // System setting --> textbox converted to int defining a validator and filter
        $this->createRefreshIntervalSetting();

    }

    private function createRefreshIntervalSetting()
    {
        $this->refreshInterval        = new SystemSetting('refreshInterval', Piwik::translate('TrafficByDeviceType_SettingsRefreshInterval'));
        $this->refreshInterval->readableByCurrentUser = true;
        $this->refreshInterval->type  = static::TYPE_INT;
        $this->refreshInterval->uiControlType = static::CONTROL_TEXT;
        $this->refreshInterval->uiControlAttributes = array('size' => 3);
        $this->refreshInterval->description     = Piwik::translate('TrafficByDeviceType_SettingsRefreshIntervalDescription');
        $this->refreshInterval->inlineHelp      = Piwik::translate('TrafficByDeviceType_SettingsRefreshIntervalHelp');
        $this->refreshInterval->defaultValue    = '5';
        $this->refreshInterval->validate = function ($value, $setting) {
            if ($value < 1) {
                throw new \Exception('Value is invalid');
            }
        };

        $this->addSetting($this->refreshInterval);
    }

}
