<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\TrafficByDeviceType;

use Piwik\Piwik;
use Piwik\API\Request;
use \DateTimeZone;
use Piwik\Site;
use Piwik\Common;
use DeviceDetector\Parser\Device\DeviceParserAbstract;

require_once PIWIK_INCLUDE_PATH . '/plugins/TrafficByDeviceType/functions.php';

/**
 * API for plugin ConcurrentsByTrafficSource
 *
 */
class API extends \Piwik\Plugin\API {

	private static function isSocialUrl($url, $socialName = false)
	{
		foreach (Common::getSocialUrls() as $domain => $name) {
	
			if (preg_match('/(^|[\.\/])'.$domain.'([\.\/]|$)/', $url) && ($socialName === false || $name == $socialName)) {
	
				return true;
			}
		}
	
		return false;
	}
	
	private static function get_timezone_offset($remote_tz, $origin_tz = null) {
    		if($origin_tz === null) {
        		if(!is_string($origin_tz = date_default_timezone_get())) {
            			return false; // A UTC timestamp was returned -- bail out!
        		}
    		}
			if (preg_match("/^UTC[-+]*/", $origin_tz)){
				return(substr($origin_tz, 3));
    		}
    		$origin_dtz = new \DateTimeZone($origin_tz);
    		$remote_dtz = new \DateTimeZone($remote_tz);
    		$origin_dt = new \DateTime("now", $origin_dtz);
    		$remote_dt = new \DateTime("now", $remote_dtz);
    		$offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
    		return $offset;
	}
	
	private static function startsWith($haystack, $needle){
    	return $needle === "" || strpos($haystack, $needle) === 0;
	}
	
	/**
     * Retrieves visit count from lastMinutes and peak visit count from lastDays
     * in lastMinutes interval for site with idSite.
     *
     * @param int $idSite
     * @param int $lastMinutes
     * @param int $lastDays
     * @return int
     */
    public static function getTrafficByDeviceType($idSite, $lastMinutes=20)
    {
        \Piwik\Piwik::checkUserHasViewAccess($idSite);
		$timeZoneDiff = API::get_timezone_offset('UTC', Site::getTimezoneFor($idSite));
		if (preg_match("/^UTC[-+]*/", Site::getTimezoneFor($idSite))){
			$origin_dtz = new \DateTimeZone("UTC");
			$origin_dt = new \DateTime("now", $origin_dtz);
			$origin_dt->modify( substr($origin_tz, 3).' hour' );			
    	} else {
			$origin_dtz = new \DateTimeZone(Site::getTimezoneFor($idSite));
			$origin_dt = new \DateTime("now", $origin_dtz);
    	}
		$refTime = $origin_dt->format('Y-m-d H:i:s');
		$resultArray = array();
		$deviceTypes = DeviceParserAbstract::getAvailableDeviceTypes();
		$index = 1;
        $sql = "SELECT COUNT(*)
                FROM " . \Piwik\Common::prefixTable("log_visit") . "
                WHERE idsite = ?
                AND DATE_SUB('".$refTime."', INTERVAL ? MINUTE) < visit_first_action_time
                ";
        $total = \Piwik\Db::fetchOne($sql, array(
           	$idSite, $lastMinutes+($timeZoneDiff/60)
       	));
		foreach ($deviceTypes as $deviceName=>&$deviceType) {
	        $sql = "SELECT COUNT(*)
	                FROM " . \Piwik\Common::prefixTable("log_visit") . "
	                WHERE idsite = ?
	                AND DATE_SUB('".$refTime."', INTERVAL ? MINUTE) < visit_first_action_time
	                AND config_device_type = ".$deviceType."
	                ";
	        $result = \Piwik\Db::fetchOne($sql, array(
            	$idSite, $lastMinutes+($timeZoneDiff/60)
        	));
			if ($result > 0){
				$resultPercentage = ($total==0)?0:round($result/$total*100,2);
				array_push($resultArray, array('id'=>$index, 'name'=>getDeviceTypeLabel($deviceName), 'value'=>$result, 'percentage'=>str_replace(",", ".", sprintf("%01.2f", $resultPercentage))));
			}
			$index++;
		}
		if (count($resultArray)==0){
			return array(array('id'=>1, 'name'=>Piwik::translate('TrafficByDeviceType_NoDevicesFound'), 'value'=>0, 'percentage'=>''));
		} else {
			return $resultArray;
		}
    }

}
