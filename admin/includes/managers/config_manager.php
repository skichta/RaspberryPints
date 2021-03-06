<?php

    require_once __DIR__.'/../conn.php';
    require_once __DIR__.'/../config_names.php';
	$stylesheet = getConfigValue(ConfigNames::AdminThemeColor);

	function getAllConfigs()
	{	
		global $mysqli;
		$config = array();
		$sql = "SELECT * FROM config";
		$qry = $mysqli->query($sql);
		while($c = $qry->fetch_array()){
			$config[$c['configName']] = $c['configValue'];
		}
		//Use RFID is no longer a config but the status that there are RFID Readers in the system
		$config[ConfigNames::UseRFID] = False;
		$sql = "SELECT * FROM rfidReaders";
		$qry = $mysqli->query($sql);
		while($c = $qry->fetch_array()){
		    $config[ConfigNames::UseRFID] = True;
		    break;
		}
		return $config;
	}
	
	function getConfigurableConfigs()
	{	
		global $mysqli;
		$config = array();
		$sql = "SELECT * FROM config WHERE showOnPanel = 1 ORDER BY displayName";
		return $mysqli->query($sql);
	}
	function getConfigValue($configName)
	{
		global $mysqli;
		$ret = null;
		$sql="SELECT configValue FROM config WHERE configName ='".$configName."'";
		$qry=$mysqli->query($sql);
		if($c = $qry->fetch_array()){
			$ret = $c['configValue'];
		}
		return $ret;
	}
	function getTapConfigurableConfigs()
	{	
		global $mysqli;
		$config = array();
		$sql = "SELECT * FROM config WHERE configName IN ('".ConfigNames::UseTapValves."','".ConfigNames::Use3WireValves."', '".ConfigNames::UseFanControl."', '".ConfigNames::UseFlowMeter."', '".ConfigNames::UseKegWeightCalc."') ORDER BY id";
		//echo $sql; exit;
		return $mysqli->query($sql);
	}
	
	function getValveConfigurableConfigs()
	{
	    global $mysqli;
	    $config = array();
	    $sql = "SELECT * FROM config WHERE configName IN ('".ConfigNames::UseTapValves."','".ConfigNames::Use3WireValves."') ORDER BY id";
	    //echo $sql; exit;
	    return $mysqli->query($sql);
	}
	
	function saveConfigValue($configName, $configValue, $insert = false)
	{
	    global $mysqli;
	    $sql="UPDATE config set configValue='".$configValue."', modifiedDate = NOW() WHERE configName ='".$configName."'";
	    if($insert){
	        $existVal = getConfigValue(ConfigNames::UpdateDate);
	        if(!$existVal || $existVal === null){
	            $sql="INSERT INTO config (configName, configValue, displayName, showOnPanel, createdDate, modifiedDate) VALUES('".$configName."', '".$configValue."', '".$configName."', 0, NOW(), NOW())";
	        }
	    }
	    return $mysqli->query($sql);
	}
	
	function setConfigurationsFromArray($newValues, &$oldValues)
	{
		foreach( array_keys( $newValues ) as $index=>$key ) {
			$constKey = ucwords($key);
			if(!defined('ConfigNames::'.$constKey))continue;
			$constVal = constant('ConfigNames::'.$constKey);
			if(!isset($oldValues[$constVal]))continue;
			if(saveConfigValue($constVal, $newValues[$key])){
				$oldValues[$constVal] = $newValues[$key];
			}	
		}
	}
	
	function getAllConfigsLike($name)
	{
	    global $mysqli;
	    $config = array();
	    $sql = "SELECT * FROM config WHERE configName LIKE $name";
	    $qry = $mysqli->query($sql);
	    while($c = $qry->fetch_array()){
	        $config[$c['configName']] = $c['configValue'];
	    }
	    return $config;
	}
?>