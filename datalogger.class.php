<?php

class datalogger
{

public static function datalog(
	string $source, // PAGELOAD
	int $datacode, // 1000
	string $shortdescr, // PAGE LOAD
	string $descr = '',
	string $dump = ''

) {
	try {

        if ($_SESSION['postvars']['global']['pagename'] == null) {
            $page = "Homepage";
        } else {
            $page = $_SESSION['postvars']['global']['pagename'];
        }


		$browsver = (float) $_SESSION['browser']['version'];
		$robot = ($_SESSION['browser']['isRobot']) ? 1 : 0;
		$mobile = ($_SESSION['browser']['isMobile']) ? 1 : 0;
		$unique = ($_SESSION['hitcounter']['unique_visitor']) ? 1 : 0;
		$longitude = (float) $_SESSION['geolocation']['longitude'];
		$latitude = (float) $_SESSION['geolocation']['latitude'];
		$distance = (float) $_SESSION['geolocation']['distance'];
		$id = '';

		$sql = <<<SQL
		INSERT INTO `datalogger` (session_id, hit_time, ip_address, browser,browser_version, platform, mobile, robot, continent,
		                          country, region, city, lattitude, longitude, flag, isp, `connection`, organization, distance, 
		                          unique_visitor, data_source, data_code, short_descr, descr, data_dump, page, data_id, root_url) VALUES (
		                          ?,NOW(),?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,? )
SQL;

	$mysql = mysql::get_connection();
	$stmt = $mysql->prepare($sql);
	$stmt->bind_param("sssdsiissssddsssssdsissssss",
		$_SESSION['hitcounter']['session_id'],
		$_SESSION['browser']['ipAddress'],
		$_SESSION['browser']['name'],
		$browsver,
		$_SESSION['browser']['platform'],
		$mobile,
		$robot,
		$_SESSION['geolocation']['continent'],
		$_SESSION['geolocation']['country'],
		$_SESSION['geolocation']['region'],
		$_SESSION['geolocation']['city'],
		$latitude,
		$longitude,
		$_SESSION['geolocation']['flag'],
		$_SESSION['geolocation']['isp'],
		$_SESSION['geolocation']['connection'],
		$_SESSION['geolocation']['organization'],
		$distance,
		$unique,
		$source,
		$datacode,
		$shortdescr,
		$descr,
		$dump,
		$page,
		$id,
        $_SESSION['browser']['rootURL']
	);

	$stmt->execute();
	$stmt->close();
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}

public static function newSession(string $sessionID) {
	$sql = <<<SQL
		INSERT INTO session_table (session_id, ip_address, isp, city, start_time, last_update) VALUES
		(?,?,?,?,NOW(), NOW())
SQL;

	$mysql = mysql::get_connection();
	$stmt = $mysql->prepare($sql);
	$stmt->bind_param(
		"ssss",
		$sessionID,
		$_SESSION['browser']['ipAddress'],
		$_SESSION['geolocation']['isp'],
		$_SESSION['geolocation']['city']
	);

	$stmt->execute();
	$stmt->close();

}

public static function updateSession(string $id = '') {
	if ($id=='') {
		$id = session_id();
	}
	$sql = <<<SQL
	UPDATE session_table SET last_update = NOW()
	WHERE session_id = ?
SQL;

	$mysql = mysql::get_connection();
	$stmt = $mysql->prepare($sql);
	$stmt->bind_param(
		"s",
		$id
	);

	$stmt->execute();
	$stmt->close();
}

public function expireSessions() {
	$sql = <<<SQL
		UPDATE session_table SET exit_time = NOW(), exit_method='AUTO'
		WHERE exit_method IS NULL 
		AND last_update < (NOW() - INTERVAL 10 MINUTE)
SQL;
	$mysql = mysql::get_connection();
	$stmt = $mysql->prepare($sql);
	$stmt->execute();
    $results = $stmt->affected_rows;
	$stmt->close();

    return $results;
}

public static function validateSession(string $sessionID): Bool {
	$IsValidated = 0;

	$Sql = <<<SQL
		SELECT COUNT(*) FROM `session_table`
		WHERE `session_id` = ?
		AND `exit_time` IS NULL
		AND `exit_method` IS NULL
SQL;

	$mysql = mysql::get_connection();
	$stmt = $mysql->prepare($Sql);
	$stmt->bind_param('s', $session_id);
	$stmt->execute();
	$stmt->bind_result($IsValidated);
	$stmt->fetch();
	$stmt->close();

	return $IsValidated == 1;
}

public static function SetError(int $errorcode, string $shortdescr, string $descr) {
	self::datalog('ERROR', $errorcode, $shortdescr, $descr);
}

public static function SetWarning(int $errorcode, string $shortdescr, string $descr) {
	self::datalog('WARNING', $errorcode, $shortdescr, $descr);
}

public static function autotask(int $code, string $short, string $message, string $method) {
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $url = $_SERVER['SERVER_NAME'];

    $sql = <<<SQL
        INSERT INTO datalogger(session_id, hit_time, ip_address, data_source, data_code, short_descr, descr, page, root_url )
        VALUES('AUTOTASK',NOW(), ?,'AUTO', ?,?,?,?, ?)
SQL;

    $mysql = mysql::get_connection();
    $stmt = $mysql->prepare($sql);
    $stmt->bind_param("sissss", $ip_address, $code, $short, $message, $method, $url);
    $stmt->execute();
    $stmt->close();

}

public static function CheckError() {
	$html='';
	if (isset($_SESSION['error'])) {
		$html = <<<HTML
<div class="error_class">{$_SESSION['error']['code']}&nbsp;:&nbsp;{$_SESSION['error']['message']}</div>
HTML;
	}
	return $html;
}

}
