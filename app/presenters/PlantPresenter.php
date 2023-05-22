<?php

namespace Blog\Presenters;

use Blog\Model\CategoriesModel;
use Blog\Model\Entities\Plant;
use Nette\Application\BadRequestException;
use Blog\Model\PlantsModel;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;

/**
 * Class PlantPresenter - ukázka základního presenteru pro zobrazení článku/článků
 * @author Stanislav Vojíř
 */
class PlantPresenter extends BasePresenter{
  /** @var  PlantsModel $plantsModel */
  private $plantsModel;

  /**
   * Akce pro zobrazení přehledu článků
   * @param int $category;
   */
  public function renderList($category){
    $this->template->category=$this->categoriesModel->find($category);
    $this->template->plants=$this->plantsModel->findAll($category);
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
      throw new BadRequestException('Požadovaný článek nebyl nalezen.');
    }
    $form=$this->getComponent('editForm');
    $form->setDefaults([
      'plant_id'=>$plant->plant_id,
      'name'=>$plant->name,
      'latin_name'=>$plant->latin_name,
      'description'=>$plant->description,
      'humidity'=>$plant->humidity,
      'owner'=>$plant->owner,
      'temperature'=>$plant->temperature
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
    $form->addTextArea('description','Desc:')
      ->setRequired('Je nuté zadat desc.')
      ->setHtmlAttribute('class','wysiwyg');
    $categories=$this->categoriesModel->findAll();
    $categoriesArr=[];
    foreach($categories as $category){
      $categoriesArr[$category->id]=$category->name;
    }
    $form->addSelect('category','Kategorie',$categoriesArr)
      ->setPrompt('--vyberte--')
      ->setRequired('Je nutné vybrat kategorii.');
    $form->addSubmit('save','uložit')
      ->onClick[]=function(SubmitButton $button){
      //funkce po úspěšném odeslání formuláře
      $data=$button->form->getValues(true);
      if ($data['plant_id']>0){
        //aktualizujeme existující článek
        $plant=$this->plantsModel->find($data['plant_id']);
          $plant->name=$data['name'];
          $plant->latin_name=$data['latin_name'];
          $plant->description=$data['description'];
        //  $plant->category=$data['category'];
          $plant->owner=$this->user->id;
        $result=$this->plantsModel->save($plant);
      }else{
        //zobrazíme nový článek
        $plant=new Plant();
          $plant->name=$data['name'];
          $plant->latin_name=$data['latin_name'];
          $plant->description=$data['description'];
       // $plant->category=$data['category'];
        $plant->owner=$this->user->id;
        $result=$this->plantsModel->save($plant);
      }
      if ($result){
        $this->flashMessage('Článek byl úspěšně uložen.');
      }else{
        $this->flashMessage('Článek se nepodařilo uložit.','error');
      }
      if ($plant->id>0){
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
      if ($data['id']>0){
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
}