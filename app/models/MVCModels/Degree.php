<?php
namespace App\Models\MVCModels;
/**
 * Model for carrying degree data.
 * @author Viggo Lagestedt Ekholm
 */
class Degree{
  public int $ID;
  public string $name;
  public string $field_of_study;
  public string $start_date;
  public string $end_date;
  public string $country;
  public string $city;
  public string $university;
  public ?Array $courses = null;
}