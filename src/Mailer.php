<?php

namespace Schema31\CiMailer;

/**
 * Classe mailer per CodeIgniter 3.1.11. Permette di prendere le configurazioni da file
 * posto in application/config/email.php oppure con un array di configurazioni da passare 
 * al costruttore di classe.
 */
class Mailer{

    /**
     * Default configurations
     *
     * @var array
     * 
     * All configurations
     * Preference       Default Value       Options             Description
     * useragent        CodeIgniter         None                The “user agent”.
     * protocol         mail                mail|sendmail|smtp  The mail sending protocol.
     * mailpath         /usr/sbin/sendmail  None                The server path to Sendmail.
     * smtp_host        No Default          None                SMTP Server Address.
     * smtp_user        No Default          None                SMTP Username.
     * smtp_pass        No Default          None                SMTP Password.
     * smtp_port        25                  None                SMTP Port.
     * smtp_timeout     5                   None                SMTP Timeout (in seconds).
     * smtp_keepalive   FALSE               TRUE|FALSE          Persistent SMTP connections.
     * smtp_crypto      No Default          tls|ssl             SMTP Encryption
     * wordwrap         TRUE                TRUE|FALSE          Word-wrap.
     * wrapchars        76                  None                Character count to wrap at.
     * mailtype         text                text|html 	        Type of mail.
     * charset          $config['charset']  Character set       (utf-8, iso-8859-1, etc.).
     * validate         FALSE               TRUE|FALSE          Whether to validate the email address.
     * priority         3                   1|2|3|4|5           Email Priority. 1 = highest. 5 = lowest.
     * crlf             \n                  “\r\n”|“\n”|“\r” 	Newline character.
     * newline          \n                  “\r\n”|“\n”|“\r” 	Newline character.
     * bcc_batch_mode   FALSE               TRUE|FALSE          Enable BCC Batch Mode.
     * bcc_batch_size   200                 None                Number of emails in each BCC batch.
     * dsn              FALSE               TRUE|FALSE          Enable notify message from server
     * 
     * from_email       No Default          None                If you want to set a from email value
     * from_name        No Default          None                If you want to set a from name value
     * prefix_subject   No Default          None                If you want to set a prefix for your subject
     */
    private $requiredConfigs = [
        'smtp_host',
        'smtp_user',
        'smtp_pass'
    ];

    private $_ci;
    private $configFromFile = true;
    private $isSent = false;

    private $fromEmail = '';
    private $fromName = '';
    private $prefixSubject = '';
    private $tos = [];
    private $ccs = [];
    private $bccs = [];
    private $subject = '';

    public function __construct($configs = []) {
        $this->_ci = & get_instance();

        if(is_array($configs) && count($configs) != 0){
            $this->configFromFile = false;
            foreach ($this->requiredConfigs as $c) {
                if(!array_key_exists($c, $configs)){
                    throw new \Exception("Missing required configuration options $c");
                }
            }
            $this->_ci->load->library('email');
            $this->_ci->email->initialize($configs);
            if(array_key_exists("from_name", $configs) && is_string($configs['from_name']) && trim($configs['from_name']) != ''){
                $this->fromName = trim($configs['from_name']);
            }
            if(array_key_exists("from_email", $configs) && is_string($configs['from_email']) && trim($configs['from_email']) != ''){
                $this->fromEmail = strtolower(trim($configs['from_email']));
            }

            if(array_key_exists("prefix_subject", $configs) && is_string($configs['prefix_subject']) && trim($configs['prefix_subject']) != ''){
                $this->prefixSubject = strtolower(trim($configs['prefix_subject']));
            }
        }else{
            $this->configFromFile = true;
            $this->_ci->config->load('email');
            $this->_ci->load->library('email');
        }
    }
    
    /**
     * isSent will tell you if an email is sent or not.
     *
     * @return boolean
     */
	public function isSent() {
		return $this->isSent;
    }
    
    /**
     * To set a single email address step by step
     *
     * @param string $input
     * @return void
     * @throws Exception
     */
    public function setSingleTo(string $input){
        $this->setSingleEmailAddress($input, $this->tos);
    }

    /**
     * To set multiple email addresses in one time
     *
     * @param array $inputs
     * @return void
     * @throws Exception
     */
    public function setMultipleTo(array $inputs){
        foreach ($inputs as $input) {
            $this->setSingleTo($input);
        }
    }

    /**
     * To set a single email address in cc step by step
     *
     * @param string $input
     * @return void
     * @throws Exception
     */
    public function setSingleCc(string $input){
        $this->setSingleEmailAddress($input, $this->ccs);
    }

    /**
     * To set multiple email addresses in cc in one time
     *
     * @param array $inputs
     * @return void
     * @throws Exception
     */
    public function setMultipleCc(array $inputs){
        foreach ($inputs as $input) {
            $this->setSingleCc($input);
        }
    }

    /**
     * To set a single email address in bcc step by step
     *
     * @param string $input
     * @return void
     * @throws Exception
     */
    public function setSingleBcc(string $input){
        $this->setSingleEmailAddress($input, $this->bccs);
    }

    /**
     * To set multiple email addresses in bcc in one time
     *
     * @param array $inputs
     * @return void
     * @throws Exception
     */
    public function setMultipleBcc(array $inputs){
        foreach ($inputs as $input) {
            $this->setSingleBcc($input);
        }
    }

    /**
     * To set the subject
     *
     * @param string $subject
     * @return void
     */
    public function setSubject(string $subject){
        $this->subject = trim($subject);
    }

    /**
     * To set the message
     *
     * @param string $message
     * @return void
     */
	public function setMessage(string $message) {
		$this->_ci->email->message($message);
	}

    /**
     * To set an attachment step by step
     *
     * @param string $filename Can be local path, URL or buffered content
     * @param string $disposition
     * @param string $newname
     * @param string $mime
     * @return void
     */
	public function setSingleAttach(string $filename, $disposition = 'attachment', string $newname = null, $mime = '') {
		$this->_ci->email->attach($filename, $disposition, $newname, $mime);
    }
    
    /**
     * To set multiple attachments in one time
     *
     * @param array $attachments
     * @return void
     */
    public function setMultipleAttach($attachments = []){
        foreach ($attachments as $attach) {
            $this->setSingleAttach(
                $attach['filename'], 
                array_key_exists('disposition', $attach) ? $attach['disposition'] : 'attachment', 
                array_key_exists('newname', $attach) ? $attach['newname'] : null, 
                array_key_exists('mime', $attach) ? $attach['mime'] : ''
            );
        }
    }
	
	public function printDebugger(){
		return $this->_ci->email->print_debugger();
	}

    public function send() {
        $this->setFullFrom();

        if(count($this->tos) == 0){
            throw new \Exception("At least one email address is required");
        }
        $this->_ci->email->to($this->tos);

        $this->setFullSubject();
        
        $this->isSent = $this->_ci->email->send(TRUE);
        return $this->isSent;
    }

    /**
     * Set the full from. If you provide to set a from_email and a from_name, this function will send the email
     * with the from in uman like mode.a-date
     * 
     * @example If you provide a from_name like "John Doe" and a from_email like "john@doe.com" the email will sent with
     * the from "John Doe <john@doe.com>"
     *
     * @return void
     */
    private function setFullFrom(){
		$from_email = $this->configFromFile ? strtolower(trim(config_item('from_email'))) : $this->fromEmail;
		$from_name = $this->configFromFile ? trim(config_item('from_name')) : $this->fromName;
		
		if (is_string($from_email)) {
            $this->_ci->email->from($from_email, is_string($from_name) ? $from_name : '');
        }
    }

    /**
     * Set the full subject. If you have provided a prefix for the subject, this function will concatenate to 
     * the real subject.
     * 
     * @example if you have a prefix like "MyApp - " and your subject is "First activation" this function
     * tells to send an email with the subject "MyApp - First activation".
     *
     * @return void
     */
    private function setFullSubject() {
        $prefix_subject = $this->configFromFile ? trim(config_item('prefix_subject')) : $this->prefixSubject;
        $this->_ci->email->subject($prefix_subject . $this->subject);
    }
    
    /**
     * To set a single email address step by step
     *
     * @param string $email the email address
     * @param array $whereToStore $this->tos|$this->ccs|$this->
     * @return void
     */
    private function setSingleEmailAddress(string $email, array $whereToStore){
        foreach (explode(",", $email) as $singleEmail) {
            $this->validateEmailAddress(strtolower(trim($singleEmail)));
            $whereToStore[] = strtolower(trim($singleEmail));
        }
    }
    
    /**
     * For validate email address usinf the filter_var function
     *
     * @param string $input
     * @return bool
     * @throws Exception
     */
    private function validateEmailAddress(string $input){
        if((bool) filter_var(trim($input), FILTER_VALIDATE_EMAIL)){
            return true;
        }

        throw new \Exception("validateEmailAddress error for $input");
    }
}