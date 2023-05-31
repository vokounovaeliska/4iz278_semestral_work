<?php

namespace Blog\Model\Entities;

use Nette\Utils\DateTime;


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
 * @property int $humidity
 * @property datetime $last_modified
 * @property string $image
 * @property string $image_type
 * @property int[] $categories
 */
class Plant
{


    /**
     * Funkce vracející pole s daty pro ukládání v DB
     * @return array
     */
    public function getDataArr()
    {
        $result = [
            'name' => $this->getName(),
            'latin_name' => $this->getLatinName(),
            'description' => $this->getDescription(),
            'owner' => $this->getOwner(),
            'bought_date' => $this->getBoughtDate(),
            'water_frequency' => $this->getWaterFrequency(),
            'temperature' => $this->getTemperature(),
            'lighting' => $this->getLighting(),
            'origin' => $this->getOrigin(),
            'humidity' => $this->isHumidity(),
            'last_modified' => $this->getLastModified(),
            'image' => $this->getImage(),
            'image_type' => $this->getImageType()
        ];
        if (!empty($this->plant_id)) {
            $result['plant_id'] = $this->plant_id;
        }
        return $result;
    }

    /**
     * @return int
     */
    public function getPlantId(): int
    {
        return $this->plant_id;
    }

    /**
     * @param int $plant_id
     */
    public function setPlantId(int $plant_id): void
    {
        $this->plant_id = $plant_id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getLatinName(): string
    {
        return $this->latin_name;
    }

    /**
     * @param string $latin_name
     */
    public function setLatinName(string $latin_name): void
    {
        $this->latin_name = $latin_name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getOwner(): int
    {
        return $this->owner;
    }

    /**
     * @param int $owner
     */
    public function setOwner(int $owner): void
    {
        $this->owner = $owner;
    }

    private $bought_date;
    private $water_frequency;
    private $temperature;
    private $lighting;
    private $origin;
    private $humidity;
    private $last_modified;
    private  $image;
    private  $image_type;
    private $categories;


    public function getBoughtDate()
    {
        return $this->bought_date;
    }

    /**
     * @param datetime $bought_date
     */
    public function setBoughtDate(string $bought_date)
    {
        $datetime = \DateTime::createFromFormat('Y-m-d', $bought_date);
        $convertedDate = $datetime ? $datetime->format('Y-m-d H:i:s') : null;
        $this->bought_date = $convertedDate;
    }


    public function getWaterFrequency()
    {
        return $this->water_frequency;
    }

    /**
     * @param int $water_frequency
     */
    public function setWaterFrequency(int $water_frequency): void
    {
        $this->water_frequency = $water_frequency;
    }


    public function getTemperature()
    {
        return $this->temperature;
    }

    /**
     * @param int $temperature
     */
    public function setTemperature(int $temperature): void
    {
        $this->temperature = $temperature;
    }

    public function getLighting()
    {
/*        if($this->lighting == 'direct'){
            return $this->lighting=1;
        }
        if($this->lighting == 'indirect'){
            return $this->lighting=2;
        }*/
        return $this->lighting;
    }

    public function getLightingValue()
    {
        if($this->lighting == 'direct'){
          return 1;
        }
         if($this->lighting == 'indirect'){
            return 2;
         }
    }

    /**
     * @param string $lighting
     */
    public function setLighting(string $lighting): void
    {
        $this->lighting = $lighting;
    }

    public function getOrigin()
    {
        return $this->origin;
    }

    public function getOriginValue()
    {
            if($this->origin == 'Europe'){
                    return 1;
                }
                if($this->origin == 'Asia'){
                    return 2;
                }
                if($this->origin == 'South America'){
                    return 3;
                }
                if($this->origin == 'North America'){
                    return 4;
                }
                if($this->origin == 'Australia'){
                    return 5;
                }

        return $this->origin;
    }

    /**
     * @param string $origin
     */
    public function setOrigin(string $origin): void
    {
        $this->origin = $origin;
    }


    public function isHumidity()
    {
        return $this->humidity;
    }

    /**
     * @param int $humidity
     */
    public function setHumidity(int $humidity): void
    {
        $this->humidity = $humidity;
    }

    public function getLastModified()
    {
        return $this->last_modified;
    }

    /**
     * @param date $last_modified
     */
    public function setLastModified(datetime $last_modified): void
    {
        $this->last_modified = $last_modified;
    }

    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param int[] $categories
     */
    public function setCategories(array $categories): void
    {
        $this->categories = $categories;
    }

    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage(string $image)
    {
            $this->image = $image;
    }

    public function getImageType()
    {
        return $this->image_type;
    }

    /**
     * @param string $image_type
     */
    public function setImageType(string $image_type): void
    {
        $this->image_type = $image_type;
    }

}