<?php
namespace Apigee\ManagementAPI;

interface DeveloperAppAnalyticsInterface {
  public function setEnvironment($env);
  public function getEnvironment();
  public function getAllEnvironments();
  public function getByAppName($developer_id, $app_name, $metric, $time_start, $time_end, $time_unit, $sort_by, $sort_order = 'ASC');
  public function queryEnvironments();

  public static function getMetrics();
  public static function getTimeUnits();
}