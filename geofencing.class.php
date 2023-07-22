<?php

class geofencing
{
    private array $geoData;

    public function __construct(array $data) {
        $this->geoData = $data; // copy to global for future use.
        $this->outOfArea();
    }

    private function outOfArea() {
        if ($_SESSION['postvars']['global']['pagename'] == null) {
            $page = "Homepage";
        } else {
            $page = $_SESSION['postvars']['global']['pagename'];
        }
        try {
            $SQL = <<<SQL
            INSERT INTO `out_of_area` (continent, country, city, hit_time, ip_address, browser, version, isp, isMobile, root_url, page)
            VALUES
            (?,?,?, NOW(),?,?,?,?,?,?,?)
SQL;
            $mysql = mysql::get_connection();
            $stmt = $mysql->prepare($SQL);
            $stmt->bind_param("sssssssiss",
                $this->geoData['geoloc']['continent_name'],
                $this->geoData['geoloc']['country_name'],
                $this->geoData['geoloc']['city'],
                $this->geoData['server']['ipAddress'],
                $this->geoData['server']['name'],
                $this->geoData['server']['version'],
                $this->geoData['geoloc']['isp'],
                $this->geoData['server']['isMobile'],
                $this->geoData['server']['rootURL'],
                $page
            );

            $stmt->execute();
            $stmt->close();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function showData() {
            return $this->geoData;
    }
}
