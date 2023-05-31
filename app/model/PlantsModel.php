<?php

namespace Blog\Model;
use \PDO;
use Blog\Model\Entities\Plant;
use Nette\Security\IIdentity;

/**
 * Class PlantsModel - třída modelu pro práci s články v DB
 * @package Blog\Model
 */
class PlantsModel{
  /** @var PDO $pdo */
  private $pdo;

  /**
   * Funkce pro nalezení všech článků (v případě zadání parametru $category jen v dané kategorii)
   * @param null|int $category
   * @return Plant[]
   */
  public function findAll($category=null){
    if ($category>0){
      $query=$this->pdo->prepare('SELECT plant.*,user.name as owner, user.user_id as owner_id FROM plant
                                        left join plant_category_map  on plant.plant_id = plant_category_map.plant_id
                                        LEFT JOIN user ON plant.owner=user.user_id
                                        where plant_category_map.category_id=:category;');

      $query->execute([':category'=>$category]);
    }else{
      $query=$this->pdo->prepare('SELECT * FROM plant');
      $query->execute();
    }
    return $query->fetchAll(PDO::FETCH_CLASS,__NAMESPACE__.'\Entities\Plant');
  }

    public function findMine($user){
      $query=$this->pdo->prepare('SELECT plant.*,user.name as owner, user.user_id as owner_id 
                                        FROM plant 
                                        LEFT JOIN user ON plant.owner=user.user_id
                                        where owner=:owner;');
      $query->execute([':owner'=>$user]);
      return $query->fetchAll(PDO::FETCH_CLASS,__NAMESPACE__.'\Entities\Plant');
    }

    public function findLikedFlowers($user){
        $query=$this->pdo->prepare('SELECT plant.*,user.name as owner, user.user_id as owner_id FROM plant 
                                        LEFT JOIN user ON plant.owner=user.user_id
                                        left join plant_user_like_map on plant.plant_id = plant_user_like_map.plant_id
                                        where plant_user_like_map.user_id=:user;');
        $query->execute([':user'=>$user]);
        return $query->fetchAll(PDO::FETCH_CLASS,__NAMESPACE__.'\Entities\Plant');
    }

  /**
   * Funkce pro nalezení jednoho článku
   * @param int $id
   * @param bool $includeTextAliases=false - pokud je true, dojde k načtení názvu kategorie a jména autora
   * @return Plant
   */
  public function find($id){
      $sql='SELECT plant.*,user.name as owner, user.user_id as owner_id
            FROM plant 
            LEFT JOIN user ON plant.owner=user.user_id 
            WHERE plant.plant_id=:id LIMIT 1;';
    $query=$this->pdo->prepare($sql);
    $query->execute([':id'=>$id]);
    $plant = $query->fetchObject(__NAMESPACE__.'\Entities\Plant');
      if ($plant){
          $plant->setCategories($this->selectCategoriesByPlant($id));
      }
    return $plant;
  }

  /**
   * Funkce pro smazání jednoho článku
   * @param Plant|int $id
   * @return bool
   */
  public function delete($id){
    if ($id instanceof Plant){
      $id=$id->plant_id;
    }
    $query=$this->pdo->prepare('DELETE FROM plant WHERE plant_id=:id LIMIT 1;');
    return $query->execute([':id'=>$id]);
  }

  /**
   * @param Plant $plant
   * @return bool
   */
  public function save(Plant $plant){
    $dataArr=$plant->getDataArr();
    $paramsArr=[];
    if (@$plant->plant_id>0){
      //updatujeme existující kvetinu
      $sql='';
      foreach($dataArr as $key=>$value){
       if ($key=='id'){continue;}
        if ($value instanceof \DateTime) {
          $value = $value->format('Y-m-d H:i:s'); // Convert DateTime object to string
        }

        $sql.=($sql!=''?',':'').' '.$key.'=:'.$key;
        $paramsArr[':'.$key]=$value;
      }
      $sql='UPDATE plant SET '.$sql.' WHERE plant_id=:id LIMIT 1;';
      $paramsArr[':id']=$plant->plant_id;
      $query=$this->pdo->prepare($sql);
      $result=$query->execute($paramsArr);

      $categories = $plant->getCategories();
         foreach($categories as $category){
            $this->insertIntoPlantCategoryMap($plant->plant_id, $category);
         }
      $deleteCategories = array_diff($this->getAllCategories(), $categories);
        foreach($deleteCategories as $categoryToDelete){
            $this->deletePlantCategoryMap($plant->plant_id, $categoryToDelete);
        }
    }else{
      //insert nového článku
      $sql='INSERT INTO plant (';
      $sql.=implode(',',array_keys($dataArr));
      $sql.=')VALUES(';
      foreach($dataArr as $key=>$value){
          if ($value instanceof \DateTime) {
              $value = $value->format('Y-m-d H:i:s'); // Convert DateTime object to string
          }
        $paramsArr[':'.$key]=$value;
      }
      $sql.=implode(',',array_keys($paramsArr));
      $sql.=')';
      $query=$this->pdo->prepare($sql);
      $result=$query->execute($paramsArr);
      $plant->plant_id=$this->pdo->lastInsertId('plant');
      $categories = $plant->getCategories();
        foreach($categories as $category){
            $this->insertIntoPlantCategoryMap($plant->plant_id, $category);
      }
    }
    return $result;
  }

  public function insertIntoPlantCategoryMap($plant, $category){
      $sqlSelect = 'SELECT * FROM plant_category_map WHERE plant_id = :plant_id AND category_id = :category_id;';

      $query2=$this->pdo->prepare($sqlSelect);
      $query2->execute([':plant_id' => $plant, ':category_id' => $category]);
      if(empty($query2->fetchAll())) {
          $sql = 'INSERT INTO plant_category_map (plant_id, category_id) VALUES (:plant_id, :category_id);';
          $query = $this->pdo->prepare($sql);
          $query->execute([':plant_id' => $plant, ':category_id' => $category]);
      }
  }

    public function deletePlantCategoryMap($plant, $category){
        $sqlSelect = 'delete FROM plant_category_map WHERE plant_id = :plant_id AND category_id = :category_id;';
        $query=$this->pdo->prepare($sqlSelect);
        $query->execute([':plant_id' => $plant, ':category_id' => $category]);
        $query->fetchAll();
    }

  public function selectCategoriesByPlant($plant_id){
          $sql='SELECT category_id
            FROM plant_category_map
            WHERE plant_id=:id;';
          $query=$this->pdo->prepare($sql);
          $query->execute([':id'=>$plant_id]);
          $result = $query->fetchAll(PDO::FETCH_COLUMN, 0);
          return array_map('intval', $result);
  }

  private function getAllCategories(){
      $sql='SELECT category_id
            FROM category;';
      $query=$this->pdo->prepare($sql);
      $query->execute();
      $result = $query->fetchAll(PDO::FETCH_COLUMN, 0);
      return array_map('intval', $result);
  }

  /**
   * PlantsModel constructor
   * @param PDO $pdo
   */
  public function __construct(\PDO $pdo){
    $this->pdo=$pdo;
  }

}