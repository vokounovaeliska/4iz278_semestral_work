<?php

namespace Blog\Model;
use Blog\Model\Entities\User;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Nette\Security\AuthenticationException;
use Nette\Security\Identity;
use Nette\Security\IIdentity;
use \PDO;
use Blog\Model\Entities\Plant;

/**
 * Class UsersModel - třída pro práci s uživateli
 * @package Blog\Model
 */
class UsersModel implements \Nette\Security\IAuthenticator{
  /** @var PDO $pdo */
  private $pdo;

  /**
   * Funkce pro nalezení všech uživatelů
   * @return User[]
   */
  public function findAll(){
    $query=$this->pdo->prepare('SELECT * FROM user');
    $query->execute();
    return $query->fetchAll(PDO::FETCH_CLASS,__NAMESPACE__.'\Entities\User');
  }

  /**
   * Funkce pro nalezení jednoho uživatele podle ID
   * @param int $user_id
   * @return User
   */
  public function findById($user_id){
    $query=$this->pdo->prepare('SELECT * FROM user WHERE user_id=:user_id LIMIT 1;');
    $query->execute([':user_id'=>$user_id]);
    return $query->fetchObject(__NAMESPACE__.'\Entities\User');
  }

  /**
   * Funkce pro nalezení uživatele podle e-mailu
   * @param string $email
   * @return User
   */
  public function findByEmail($email){
    $query=$this->pdo->prepare('SELECT * FROM user WHERE email=:email LIMIT 1;');
    $query->execute([':email'=>$email]);
    return $query->fetchObject(__NAMESPACE__.'\Entities\User');
  }

  /**
   * Funkce pro smazání jednoho článku
   * @param User|int $user_id
   * @return bool
   */
  public function delete($user_id){
    if ($user_id instanceof User){
        $user_id=$user_id->user_id;
    }
    $query=$this->pdo->prepare('DELETE FROM user WHERE user_id=:user_id LIMIT 1;');
    return $query->execute([':user_id'=>$user_id]);
  }

  /**
   * @param User $user
   * @return bool
   */
  public function save(User $user){
    $dataArr=$user->getDataArr();
    $paramsArr=[];
    if (@$user->user_id>0){
      //updatujeme existující uživatele
      $sql='';
      foreach($dataArr as $key=>$value){
        if ($key=='user_id'){continue;}
        $sql.=($sql!=''?',':'').' '.$key.'=:'.$key;
        $paramsArr[':'.$key]=$value;
      }
      $sql='UPDATE user SET '.$sql.' WHERE user_id=:user_id LIMIT 1;';
      $paramsArr[':user_id']=$user->user_id;
      $query=$this->pdo->prepare($sql);
      $result=$query->execute($paramsArr);
    }else{
      //insert nového uživatele
      $sql='INSERT INTO user (';
      $sql.=implode(',',array_keys($dataArr));
      $sql.=')VALUES(';
      foreach($dataArr as $key=>$value){
        $paramsArr[':'.$key]=$value;
      }
      $sql.=implode(',',array_keys($paramsArr));
      $sql.=')';
      $query=$this->pdo->prepare($sql);
      $result=$query->execute($paramsArr);
      $user->user_id=$this->pdo->lastInsertId('user');
    }
    return $result;
  }

  /**
   * Funkce pro odeslání potvrzovacího e-mailu po registraci uživatele
   * @param User $user
   */
  public function sendRegistrationMail(User $user){
    $mail=new Message();
    $mail->setFrom('voke01@vse.cz');//TODO tady by měla bát nějaká rozumná adresa
    $mail->addTo($user->email);
    $mail->setSubject('Registrace na webu...');
    $mail->setHtmlBody('<p>Byli jste úspěšně zaregistrováni, pro přihlášení využijte e-mail <strong>'.$user->email.'</strong></p>');
    $mailer = new SendmailMailer();
    $mailer->send($mail);
  }


  /**
   * Funkce pro autentizaci uživatele (využívá metod frameworku)
   * @param array $credentials
   * @return IIdentity
   * @throws AuthenticationException
   */
  function authenticate(array $credentials){
    list($username,$password)=$credentials;//TODO pamatujete si tuhle konstrukci?
    $user=$this->findByEmail($username);
    if (!$user){
      throw new AuthenticationException('Uživatelský účet nenalezen.',self::IDENTITY_NOT_FOUND);
    }
    if (!$user->isValidPassword($password)){
      throw new AuthenticationException('Chybné heslo.',self::INVALID_CREDENTIAL);
    }
    if (!$user->active){
      throw new AuthenticationException('Uživatelský účet není aktivní.',self::NOT_APPROVED);
    }
    return new Identity($user->user_id,[$user->role],['name'=>$user->name,'email'=>$user->email]);
  }


  /**
   * PlantsModel constructor
   * @param PDO $pdo
   */
  public function __construct(\PDO $pdo){
    $this->pdo=$pdo;
  }


}