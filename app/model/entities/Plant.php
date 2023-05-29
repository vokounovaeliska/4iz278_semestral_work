<?php

namespace Blog\Model\Entities;

use DateTime;

/**
 * Class Plant
 * @package Blog\Model\Entities
 * @property int $plant_id
 * @property string $name
 * @property string $latin_name
 * @property string $description
 * @property int $owner
 * @property date $bought_date
 * @property int $water_frequency
 * @property int $temperature
 * @property string $lighting
 * @property string $origin
 * @property boolean $humidity
 * @property datetime $last_modified
 * @property int[] $categories
 */
class Plant{


  /**
   * Funkce vracející pole s daty pro ukládání v DB
   * @return array
   */
  public function getDataArr(){
    $result=[
        'name' => $this->name,
        'latin_name' => $this->latin_name,
        'description' => $this->description,
        'owner' => $this->owner,
        'bought_date' => $this->getBoughtDate(),
        'water_frequency' => $this->water_frequency,
        'temperature' => $this->temperature,
        'lighting' => $this->lighting,
        'origin' => $this->origin,
        'humidity' => $this->humidity,
        'last_modified' => $this->last_modified,
        'image' => $this->image,
    ];
    if (!empty($this->plant_id)){
      $result['plant_id']=$this->plant_id;
    }
    return $result;
  }
}