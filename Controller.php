<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\TrafficByDeviceType;

use Piwik\View;
use Piwik\Piwik;
use Piwik\Common;
use Piwik\Settings\SystemSetting;
use Piwik\Settings\UserSetting;
use Piwik\Settings\Manager as SettingsManager;

/**
 *
 */
class Controller extends \Piwik\Plugin\Controller
{

    private function getPluginSettings()
    {
        $pluginsSettings = SettingsManager::getPluginSettingsForCurrentUser();
        ksort($pluginsSettings);
        return $pluginsSettings;
    }

    public function index()
    {
		$settings = new Settings('TrafficByDeviceType');

        $view = new View('@TrafficByDeviceType/index.twig');
        $this->setBasicVariablesView($view);
        $view->idSite = $this->idSite;
        $view->refreshInterval = (int)($settings->refreshInterval->getValue()*60);

        return $view->render();
    }
}
