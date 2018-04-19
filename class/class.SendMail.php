<?php 

/**
* 
*/
class SendMail extends Secure
{	
	private $_PHPmail;
	private $_port = 25;
	private $_host = 'mail.ennavidad.com';
	private $_username = 'adornos@ennavidad.com';
	private $_password = 'ennavidad2017';

	// Header
	private $_email_to;
	private $_email_from;
	private $_from_name;
	private $_to_name;

	// Message
	private $_subjet;
	private $_message;
	public $error;
	
	function __construct($email_to,$email_from)
	{
		$this->_PHPmail = new PHPMailer();
		$this->_PHPmail->CharSet = 'UTF-8';
	    if ($this->_PHPmail->ValidateAddress($email_to) AND $this->_PHPmail->ValidateAddress($email_from)) {
			$this->smtp();
			$this->_email_to = $email_to;
			$this->_email_from = $email_from;
	    }else{
	    	return false;
	    }
	    return true;
	}

	private function smtp()
	{
		// Configuramos el protocolo SMTP con autenticación
       $this->_PHPmail->IsSMTP();  
       $this->_PHPmail->SMTPAuth = true;

       // Configuración del servidor SMTP
       $this->_PHPmail->Port = $this->_port;  
       $this->_PHPmail->Host = $this->_host;
       $this->_PHPmail->Username = $this->_username;
       $this->_PHPmail->Password = $this->_password;
	}

	public function headers($from_name,$to_name)
	{	
		// Configuración cabeceras del mensaje
		$this->_from_name = $from_name;
		$this->_to_name = $to_name;
       	$this->_PHPmail->From = $this->_email_from;  
       	$this->_PHPmail->FromName = $this->_from_name;
       	$this->_PHPmail->AddAddress($this->_email_to, $this->_to_name);
	}

	public function content($subjet,$message)
	{
		$this->_subjet = $subjet;
		$this->_message = $message;
	}

	public function send()
	{
		$this->_PHPmail->Subject  = $this->_subjet;
		$this->_PHPmail->SetFrom($this->_email_from,$this->_from_name);   
       	$this->_PHPmail->MsgHTML($this->_message);

       	if (!$this->_PHPmail->Send()) {
       		$this->error = $this->_PHPmail->ErrorInfo;
       		return false;
       	}
       	return true;
	}

}