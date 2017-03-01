<?php

#Load config
include 'content/config.php';
#Load all Class files
function dat_loader($class) {
    include 'class/' . $class . '.php';
}
spl_autoload_register('dat_loader');

class TestsMain extends PHPUnit_Framework_TestCase
{

	private $DB;
  private $Verify;
  private $Main;
  private $Contact;

	public function setUp() {
		$this->DB = new Database;
		$this->DB->InitDB();
    $this->Verify = new Verify($this->DB,true);
    $this->Main = new Main($this->DB,$this->Verify);
    $this->Contact = new Contact($this->DB,$this->Verify);
    $this->User = new User($this->DB);
	}

	public function testMySQLConnection() {
	  $this->assertEquals($this->DB->GetConnection()->connect_error,NULL);
	}

  public function testEscape() {
    $result = Page::escape("<script>alert('attacked')</script>");
	  $this->assertEquals($result,"&lt;script&gt;alert(&#039;attacked&#039;)&lt;/script&gt;");
  }

  public function testRegistration() {
    #Add a User
    $password = Page::randomPassword();
    echo "Password used: ".$password;
    $activation_hash = $this->User->registerUser("Tester","test@test.com",$password,$password,"LET",true);
    $this->assertEquals($this->User->getLastError(),NULL);
    $this->assertEquals($this->Verify->checkHash($activation_hash),true);
    $this->assertEquals($this->User->enableUser($activation_hash),true);
    $this->assertEquals($this->Verify->checkHash($activation_hash.'a'),false);
  }

  public function testContact() {
    #Add a Contact
    $activation_hash = $this->Contact->addContact("test@test.com",true);
    $this->assertEquals($this->Contact->getLastError(),NULL);
    $this->assertEquals($this->Verify->checkEmailHash($activation_hash),true);
    $this->assertEquals($this->Verify->checkEmailHash($activation_hash.'a'),false);
    $this->assertEquals($this->Contact->enableContact($activation_hash),NULL);
    $this->assertEquals($this->Contact->enableContact($activation_hash.'a'),"MySQL Error");
  }

  public function testChecks() {
    #Add a Check
    //$this->Main->addCheck("8.8.8.8",22,1,"Google");
    //$this->assertEquals($this->Main->getLastError(),"Invalid EMail"); //Permission error, since Contact (Email) not activated
    #Enable the Added Contact
    //$stmt = $this->DB->GetConnection()->prepare("UPDATE emails SET Status = 1 WHERE ID = 1");
    //$stmt->execute();
    //$stmt->close();
    #Try to add a Check again
    //$this->Main->addCheck("8.8.8.8",22,1,"Google");
    //$this->assertEquals($this->Main->getLastError(),NULL);
  }

}
?>
