<?php

namespace Blog\Presenters;

use Blog\Model\CategoriesModel;
use Blog\Model\UsersModel;
use Blog\Model\Entities\Plant;
use Nette\Application\BadRequestException;
use Blog\Model\PlantsModel;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Http\FileUpload;
use Nette\Utils\DateTime;
use Intervention\Image\ImageManagerStatic as Image;


/**
 * Class PlantPresenter - ukázka základního presenteru pro zobrazení článku/článků
 * @author Stanislav Vojíř
 */
class PlantPresenter extends BasePresenter{
  /** @var  PlantsModel $plantsModel */
  private $plantsModel;
  private $usersModel;

  /**
   * Akce pro zobrazení přehledu článků
   * @param int $category;
   */
  public function renderList($category){
    $this->template->category=$this->categoriesModel->find($category);
    $this->template->plants=$this->plantsModel->findAll($category);
    $this->template->plantsMine=$this->plantsModel->findMine($this->getUser()->getId());
    $this->template->plantsLiked=$this->plantsModel->findLikedFlowers($this->getUser()->getId());
  }

  public function renderListByOwner($owner){
      $this->template->owner=$this->usersModel->findById($owner);
      $this->template->plantsByOwner=$this->plantsModel->findMine($owner);
  }
  /**
   * Akce pro zobrazení jednoho článku
   * @param int $id
   * @throws BadRequestException
   */
  public function renderShow($id){
    $plant=$this->plantsModel->find($id,true);
    if ($plant){
      $this->template->plant=$plant;
    }else{
      throw new BadRequestException('Požadovaná květina nebyla nalezena');
    }
  }

  /**
   * Akce pro přidání nového článku
   * @param int|null $category=null
   */
  public function actionAdd($category=null){
    if ($category && $this->categoriesModel->find($category)){
      $form=$this->getComponent('editForm');
      $form->setDefaults(['category'=>$category]);
    }
  }

  /**
   * Akce pro úpravu existujícího článku
   * @param int $id
   * @throws BadRequestException
   */
  public function actionEdit($id){
    if (!$plant=$this->plantsModel->find($id)){
      throw new BadRequestException('Požadovaná květina nebyla nalezena.');
    }
    $form=$this->getComponent('editForm');
    $form->setDefaults([
      'plant_id'=>$plant->plant_id,
      'name'=>$plant->name,
      'latin_name'=>$plant->latin_name,
      'description'=>$plant->description,
      'owner' => $plant->owner,
      'bought_date'=>$plant->getBoughtDate(),
      'water_frequency'=>$plant->getWaterFrequency(),
      'temperature'=>$plant->getTemperature(),
      'lighting' => $plant->getLightingValue(),
      'origin' => $plant->getOriginValue(),
      'humidity' => $plant->isHumidity(),
      'last_modified' => $plant->getLastModified(),
      'image' => $plant->getImage(),
      'categories' => $plant->getCategories(),
      'last_modified' => new \DateTime()
    ]);
  }

  /**
   * Funkce připravující formulář pro zadání nového/úpravu existujícího článku
   * @return Form
   */
  public function createComponentEditForm(){
    $form = new Form();
    $form->addHidden('plant_id');
    $form->addText('name','Název květiny:',null,200)
      ->setRequired('Je nutné zadat název ');
    $form->addTextArea('latin_name','Latinský název:')
      ->setHtmlAttribute('class','wysiwyg');
    $form->addTextArea('description','Popisek:')
      ->setRequired('Je nuté zadat popisek.')
      ->setHtmlAttribute('class','wysiwyg');
    $form->addText('bought_date', 'Datum pořízení')
        ->setType('date')
        ->setRequired('Je nutné zadat datum')
        ->setHtmlAttribute('class','wysiwyg');
    $form->addInteger('water_frequency', 'Jak často zalévat (po koloka dnech)')
          ->setHtmlAttribute('class','wysiwyg');
    $form->addInteger('temperature', 'Twwplota')
          ->setHtmlAttribute('class','wysiwyg');
    $svetlo=[
      1 => 'direct',
      2 => 'indirect'];
    $form->addSelect('lighting', 'Svetlo', $svetlo)
      ->setHtmlAttribute('class','wysiwyg');
      $origin = [
          1 => 'Europe',
          2 => 'Asia',
          3 => 'South America',
          4 => 'North America',
          5 => 'Australia'
      ];
      $form->addSelect('origin', 'Země původu', $origin)
        ->setHtmlAttribute('class','wysiwyg');
         $AnoNe=[
            1 => 'ano',
            0 => 'ne'];
        $form->addSelect('humidity', 'Vlhkost', $AnoNe)
          ->setHtmlAttribute('class','wysiwyg');
    $categories=$this->categoriesModel->findAll();
    $categoriesArr=[];
    foreach($categories as $category){
        if($category->category_id!=5) {
            $categoriesArr[$category->category_id] = $category->name;
        }
    }
    $form->addCheckboxList('category','Kategorie',$categoriesArr);
    $form->addUpload('image', 'Obrázek')
        ->setHtmlAttribute('accept', '.jpg,.jpeg,.png');
    $form->addSubmit('save','uložit')
      ->onClick[]=function(SubmitButton $button){
      //funkce po úspěšném odeslání formuláře
      $data=$button->form->getValues(true);
      if ($data['plant_id']>0){
        //aktualizujeme existující článek
        $plant=$this->plantsModel->find($data['plant_id']);
      }else{
        //zobrazíme nový článek
        $plant=new Plant();
      }
          $plant->name=$data['name'];
          $plant->latin_name=$data['latin_name'];
          $plant->description=$data['description'];
          $plant->setBoughtDate($data['bought_date']);
          $plant->setWaterFrequency($data['water_frequency']);
          $plant->setTemperature($data['temperature']);
          $plant->setLighting($data['lighting']);
          $plant->setOrigin($data['origin']);
          $plant->setHumidity($data['humidity']);
          $file = $button->form->getValues()->image;
            if ($file->isOk()) {
            $imageContents = base64_encode($file->getContents());
            $plant->setImage($imageContents);
            $imageType = $file->getContentType();
            $plant->setImageType($imageType);
        }


          $selectedCategories = $data['category'];
          $plant->setCategories($selectedCategories);
          $plant->owner=$this->user->id;


        $result=$this->plantsModel->save($plant);

      if ($result){
        $this->flashMessage('Článek byl úspěšně uložen.');
      }else{
        $this->flashMessage('Článek se nepodařilo uložit.','error');
      }
      if ($plant->plant_id>0){
        $this->redirect('Plant:show',['id'=>$plant->plant_id]);
      }else{
        $this->redirect('Homepage:default');
      }
    };
    $form->addSubmit('storno','zrušit')
      ->setValidationScope([])
      ->onClick[]=function(SubmitButton $button){
      //funkce po kliknutí na tlačítko pro zrušení
      $data=$button->form->getValues(true);
      if ($data['plant_id']>0){
        $this->redirect('Plant:show',['id'=>$data['plant_id']]);//přesměrování na zobrazení daného článku
      }elseif($data['category']>0){
        $this->redirect('Plant:list',['category'=>$data['category']]);//přesměrování na zobrazení kategorie
      }else{
        $this->redirect('Homepage:default');
      }
    };
    return $form;
  }
  
  
  #region injections - metody zajišťující automatické načtení potřebných služeb
  /**
   * Funkce pro automatické vložení (injection) požadované služby, která je definována v config.neon
   * @param PlantsModel $articlesModel
   */
  public function injectPlantsModel(PlantsModel $plantsModel){
    $this->plantsModel=$plantsModel;
  }
  #endregion injections
    #region injections
    /**
     * @param UsersModel $userModel
     */
    public function injectUsersModel(UsersModel $userModel){
        $this->usersModel=$userModel;
    }
    #endregion injections


}