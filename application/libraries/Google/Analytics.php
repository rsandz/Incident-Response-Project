<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Analytics
{
    protected $CI;
    protected $auth_file;
    protected $client;
    protected $analytics;

    public function __construct()
    {
        $this->CI =& get_instance();

        // Use the developers console and download your service account
        // credentials in JSON format. Place them in this directory or
        // change the key file location if necessary.
        $this->auth_file = APPPATH.'/../assets/googleAuth/DrFehmiWeb-1c08d45de240.json';

        //Init CLient
        $this->client = new Google_Client();
        $this->client->setApplicationName("Hello Analytics Reporting");
        $this->client->setAuthConfig($this->auth_file);
        $this->client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);

        //Init Analytics
        $this->analytics = new Google_Service_AnalyticsReporting($this->client);
    }

    /**
     * Queries the Analytics Reporting API V4.
     *
     * @return The Analytics Reporting API V4 response.
     */
    public function get_report() {

      // Replace with your view ID, for example XXXX.
      $VIEW_ID = "178427523";

      //Create the DateRange object.
      $dateRange = new Google_Service_AnalyticsReporting_DateRange();
      $dateRange->setStartDate("yesterday");
      $dateRange->setEndDate("yesterday");

      // Create the Metrics object.
      $sessions = new Google_Service_AnalyticsReporting_Metric();
      $sessions->setExpression("ga:users");
      $sessions->setAlias("users");

      // Create the ReportRequest object.
      $request = new Google_Service_AnalyticsReporting_ReportRequest();
      $request->setViewId($VIEW_ID);
      $request->setDateRanges($dateRange);
      $request->setMetrics($sessions);

      $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
      $body->setReportRequests($request);
      return $this->analytics->reports->batchGet( $body );
    }

    /**
     * Parses and prints the Analytics Reporting API V4 response.
     *
     * @param An Analytics Reporting API V4 response.
     */
    public function print_results($reports) {
      for ( $reportIndex = 0; $reportIndex < count( $reports ); $reportIndex++ ) {
        $report = $reports[ $reportIndex ];
        $header = $report->getColumnHeader();
        $dimensionHeaders = $header->getDimensions();
        $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
        $rows = $report->getData()->getRows();

        for ( $rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
          $row = $rows[ $rowIndex ];
          $dimensions = $row->getDimensions();
          $metrics = $row->getMetrics();
          for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++) {
            print($dimensionHeaders[$i] . ": " . $dimensions[$i] . "\n");
          }

          for ($j = 0; $j < count($metrics); $j++) {
            $values = $metrics[$j]->getValues();
            for ($k = 0; $k < count($values); $k++) {
              $entry = $metricHeaders[$k];
              print($entry->getName() . ": " . $values[$k] . "\n");
            }
          }
        }
      }
    }
    
}

/* End of file G_analytics.php */
/* Location: ./application/libraries/Google/G_analytics.php */
