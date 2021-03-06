<?php
class simpleMailTool
{
  private $host;  // 地址
  private $port;  // 端口
  private $user;  // 用户名
  private $pass;  // 密码
  private $debug; // 是否需要 debug
  private $sock;  // sock 会话


  /**
   * 初始化邮件发送系统
   *
   * @param String $host  邮件（SMTP）服务器地址，如 smtp.163.com、smtp.qq.com
   * @param int    $port  邮件（SMTP）端口，一般为 25，加密传输 (SSL/TLS) 端口要询问服务商，一般为 465 或者 587
   * @param String $user  邮件（SMTP）用户名
   * @param String $pass  邮件（SMTP）密码
   * @param String $debug 是否输出调试信息，默认关闭 false
   */
  public function __construct($host,$port,$user,$pass,$debug = false)
  {
    if($port == '25'){                  // 判断端口类型，如果是加密端口则需要在地址前加 ssl://
      $this->host = $host;
    }else{
      $this->host = 'ssl://'.$host;
    }

    $this->port = $port;
    $this->user = $user;
    
    $this->pass = base64_encode($pass);  // 密码需要用 base64 编码（用户名也需要编码，不过是用到的时候再编码）
    $this->debug= $debug;

    if($debug){ echo "调试已打开\n"; }

    $this->connectServer();              // 连接 SMTP 服务器
  }


  /**
   * 调试输出函数
   *
   * @param string $message 需要输出的内容
   */
  protected function debug($message){
    if($this->debug){
      echo '<p><b>debug: </b>'. htmlentities($message,ENT_QUOTES,"UTF-8");$message . PHP_EOL .'</p>';
    }
  }


  /**
   * 连接 SMTP 服务器
   */
  protected function connectServer(){
    $this->sock = fsockopen($this->host,$this->port);  // 根据地址和端口，通过 socket 连接服务器
    if(!$this->sock){
      exit('error:请填写 SMTP 服务器地址或 SMTP 端口号！');
    }
    $response = fgets($this->sock);                    // 读取响应信息
    $this->debug("SMTP 服务器地址：".$this->host.' 端口：'.$this->port);
    $this->debug("连接 SMTP 服务器的返回信息：".$response);

    if(substr($response,0,3) != '220'){                 // 如果响应信息中包有 220 则连接成功
      exit("error:请检查填写的 SMTP 服务器地址或 SMTP 端口号是否正确！");
    }
  }


  /**
   * 向 SMTP 服务器发送命令
   *
   * @param string $cmd 命令内容
   */
  protected function sendCommand($cmd){
    fwrite($this->sock,$cmd);                                      // 发送命令
    $response = fgets($this->sock);                                // 获取服务器的返回信息
    $this->debug('发送的命令：'.$cmd .';服务器的返回信息：'.$response);

    if(substr($response,0,3) == 503){
      return false; 
    }
    return true;
  }


  /**
   * 公共 - 验证 SMTP 用户名和密码是否正确，如果正确，返回 true ，否则返回 false
   */
  public function verifyUser(){
    
    $this->sendCommand("HELO ".$this->host."\r\n");
    $this->sendCommand("AUTH LOGIN\r\n");
    $this->sendCommand(base64_encode($this->user)."\r\n");
    $this->sendCommand($this->pass."\r\n");
    $this->sendCommand("MAIL FROM:<".$this->user.">\r\n");

    $ret = $this->sendCommand("RCPT TO:<test@qq.com>\r\n");

    return $ret;
  }


  /**
   * 公共 - 发送邮件
   *
   * @param string $to 发送到的邮件地址
   * @param string $subject 信件标题
   * @param string $body 信件内容
   * @param string $from 发件人，一般为 SMTP 用户名
   */
  public function sendMail($from,$to,$subject,$body){

    // 按照 SMTP 协议规则封装简单的邮件内容
    $content = 'From:'.$from." <".$this->user.">\r\n".'To:'.$to."\r\n".'Subject:'.$subject."\r\n".'Content-Type: text/html; charset=UTF-8 '."\r\n\r\n".$body;

    $this->debug("发送的邮件内容：".$content."\n");

    $this->sendCommand("HELO ".$this->host."\r\n");
    $this->sendCommand("AUTH LOGIN\r\n");
    $this->sendCommand(base64_encode($this->user)."\r\n");
    $this->sendCommand($this->pass."\r\n");
    $this->sendCommand("MAIL FROM:<".$this->user.">\r\n");
    $this->sendCommand("RCPT TO:<".$to.">\r\n");
    $this->sendCommand("DATA\r\n");
    $this->sendCommand($content."\r\n.\r\n");
    $this->sendCommand("QUIT\r\n");
  }


  /**
   * 结束会话
   */
  public function __destruct()
  {
    fclose($this->sock);
  }

}
?>
