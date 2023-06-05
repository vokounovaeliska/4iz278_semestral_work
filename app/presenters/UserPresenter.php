<?php

namespace Blog\Presenters;

use Blog\Model\Entities\User;
use Blog\Model\UsersModel;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Forms\Controls\TextInput;
use \JanuSoftware\Facebook\Facebook;
use Nette\Security\Identity;

/**
 * Class UserPresenter
 * @package Blog\Presenters
 */
class UserPresenter extends BasePresenter{
  /** @var  UsersModel $usersModel */
  private $usersModel;

  /**
   * Akce pro přihlášení uživatele
   */
  public function actionLogin(){
    if ($this->user->isLoggedIn()){
      $this->redirect('Homepage:default');
      return;
    }
  }

  /**
   * Akce pro odhlášení uživatele
   */
  public function actionLogout(){
    if ($this->user->isLoggedIn()){
      $this->flashMessage('Byli jste úspěšně odhlášeni.');
      $this->user->logout(true);
    }
    $this->redirect('Homepage:default');
  }

  /**
   * Akce pro registraci uživatele
   */
  public function actionRegister(){
    if ($this->user->isLoggedIn()){
      $this->flashMessage('Nelze registrovat nový účet, když jste přihlášeni.');
      $this->redirect('Homepage:default');
    }
  }

  /**
   * Formulář pro přihlášení uživatele
   * @return Form
   */
  public function createComponentLoginForm(){
    $form = new Form();
    $form->addText('email','E-mail')
      ->setRequired('Je nutné zadat e-mail.')
      ->addRule(Form::EMAIL,'Je nutné zadat platnou e-mailovou adresu.');
    $form->addPassword('password','Heslo:')
      ->setRequired('Je nutné zadat heslo.');
    $form->addSubmit('ok','přihlásit se')
      ->onClick[]=function(SubmitButton $button){
      $data=$button->form->getValues(true);
      try {
        $this->getUser()->login($data['email'], $data['password']);
      } catch (\Exception $e) {
        $button->form->addError('Nesprávné přihlašovací jméno nebo heslo.');
      }
      if ($this->user->isLoggedIn()){
        $this->flashMessage('Vítejte na našem webu!');
        $this->redirect('Homepage:default');
      }
    };
    return $form;
  }

  /**
   * Formulář pro registraci uživatele
   * @return Form
   */
  public function createComponentRegistrationForm(){
    $form=new Form();
    $form->addText('name','Jméno a příjmení:')
      ->setRequired('Je nutné zadat jméno.');
    $form->addText('email','E-mail:')
      ->setRequired('Je nutné zadat e-mail')
      ->addRule(Form::EMAIL,'Je nutné zadat platnou e-mailovou adresu.')
      ->addRule(function(TextInput $input){
        return !($this->usersModel->findByEmail($input->value));
      },'Uživatel s daným e-mailem již existuje.');
    $password=$form->addPassword('password','Heslo:')
      ->setRequired('Je nutné zadat heslo.')
      ->addRule(Form::MIN_LENGTH,'Heslo musí mít minumálně 4 znaky.',4);
    $form->addPassword('password2','Potvrzení hesla:')
      ->addRule(Form::EQUAL,'Zadaná hesla se neshodují.',$password);
    $form->addSubmit('ok','registrovat se')
      ->onClick[]=function(SubmitButton $button){
      //funkce pro vytvoření nového uživatelského účtu a automatick přihlášení uživatele
      $data = $button->form->getValues(true);
      $user=new User();
      $user->active=true;
      $user->name=$data['name'];
      $user->email=$data['email'];
      $user->password=User::encodePassword($data['password']);
      $user->role=User::DEFAULT_REGISTERED_ROLE;
      if ($this->usersModel->save($user)){
        $this->flashMessage('Registrace byla úspěšně dokončena.');
        $this->usersModel->sendRegistrationMail($user);
      }
      $this->user->login($data['email'],$data['password']);
      $this->redirect('Homepage:default');
    };
    return $form;
  }

    public function renderLogin(){
        $permissions = ['email'];
        $redirectURI = 'https://esotemp.vse.cz/~voke01/final/user/facebookcallback';
        $loginUrl = $this->helper->getLoginUrl($redirectURI, $permissions);
        $this->template->loginUrl = $loginUrl;
    }

    public function actionFacebookCallback()
    {
        try {
            $accessToken = $this->helper->getAccessToken();
        } catch (Exception $e) {
            // Handle any exceptions that occur during the login process
            $this->flashMessage('Facebook login failed.', 'error');
        }
        if(!$accessToken) {
            $this->flashMessage('Facebook login failed.', 'error');
        }

        $oAuth2Client = $this->fb->getOAuth2Client();
        $accessTokenMetadata = $oAuth2Client->debugToken($accessToken);
        $fbUserId = $accessTokenMetadata->getUserId();
        $response = $this->fb->get('/me?fields=name,email', $accessToken);
        $graphUser = $response->getGraphNode();
        $fbUserMail = $graphUser->getField('email');
        $fbUserName = $graphUser->getField('name');
        $fbUserId = $graphUser->getField('id');

        $user = $this->usersModel->findByFacebookId($fbUserId);

        if(!$user) {  //nenasli jsme, tak hledame mail
            $user = $this->usersModel->findByEmail($fbUserMail);
            if($user) {
                $this->usersModel->updateUserAddFacebook($fbUserId, $fbUserMail);
            }
            else {  //zalozime
                $newUser=new User();
                $newUser->active=true;
                $newUser->password='';
                $newUser->name=$fbUserName;
                $newUser->email=$fbUserMail;
                $newUser->role=User::DEFAULT_REGISTERED_ROLE;
                $newUser->facebook_id=$fbUserId;
                if ($this->usersModel->save($newUser)){
                    $this->flashMessage('Registrace byla úspěšně dokončena.');
                    $this->usersModel->sendRegistrationMail($newUser);
                    $user = $newUser;
                }
            }

            }

        if($user) {
            //prihlasime
            $this->user->login([0 => $user->facebook_id, ]);
            $this->flashMessage('Úspěšné přihlášení přes facebook');
            $this->redirect('Homepage:default');
        }
    }

    private $fb;
    private $helper;


  #region injections
  public function injectUsersModel(UsersModel $usersModel){
    $this->usersModel=$usersModel;
    $this->fb=$usersModel->fb;
    $this->helper = $this->fb->getRedirectLoginHelper();
  }
  #endregion injections


}