<?php

/**
 * Message class for parsing IRC raw messages.
 */
class Message
{
  private $nickname;
  private $user;
  private $host;
  private $command;
  private $arguments;
  private $text;
  private $time;
  private $raw;

  /**
   * Constructor for Message objects. Should not be called directly.
   *
   * @param string $nickname
   *   Sender's nickname.
   * @param string $user
   *   Sender's user.
   * @param string $host
   *   Sender's host.
   * @param string $command
   *   Command of the message.
   * @param string $arguments
   *   Arguments of the message.
   * @param string $text
   *   Text or body of the message.
   * @param string $raw
   *   Raw message.
   */
  private function __construct($nickname, $user, $host, $command, $arguments, $text, $raw)
  {
    $this->nickname = $nickname;
    $this->user = $user;
    $this->host = $host;
    $this->command = $command;
    $this->arguments = $arguments;
    $this->text = $text;
    $this->raw = $raw;
    $this->time = time();
  }

  /**
   * Creates a Message object from a string.
   *
   * @param string $raw_message
   *   Raw message from which the Message object will be made.
   */
  public static function from_raw_message($raw_message)
  {
    if (preg_match(
      '/' .
        '(?::' .
          '(?:' .
            '(?<nickname>[^\!]+)!(?<user>[^@]+)@(?<host>[^\s]+)' . //nick!user@host
            '|' .
            '(?<servername>[^\s]+)' . // servername
          ')' .
        '\s)?' .
        '(?<command>\w+)\s?' . // command
        '(?<arguments>' .
          '(?:[^:\s]+?(?:\=\S+|)\s)+' .
        ')?' .
        '(?::' .
          '(?P<text>[^\n\r]*)' .
        ')?' .
      '/',
      $raw_message, $m))
    {
      foreach ($m as &$e)
      {
        $e = trim($e);
      }
      $servername = null;
      $nickname = null;
      $user = null;
      $host = null;
      $command = null;
      $arguments = null;
      $text = null;

      extract($m);

      if ($servername != null)
        $host = $servername;
      if ($arguments != null)
        $arguments = explode(' ', $arguments);
      return new Message($nickname, $user, $host, $command, $arguments, $text, $raw_message);
    }
    return null;
  }

  /**
   * Returns a string containing the raw message (including new line character).
   */
  public function raw()
  {
   return $this->raw;
  }

  /**
   * Returns the nickname of the sender of the message.
   */
  public function nickname()
  {
    return $this->nickname;
  }

  /**
   * Returns the user of the sender of the message.
   */
  public function user()
  {
    return $this->user;
  }

  /**
   * Returns the host of the sender of the message.
   */
  public function host()
  {
    return $this->host;
  }

  /**
   * Returns the command of the message.
   */
  public function command()
  {
    return $this->command;
  }

  /**
   * Returns the arguments of the message.
   */
  public function arguments()
  {
    return $this->arguments;
  }

  /**
   * Returns the  text of the message.
   */
  public function text()
  {
    return $this->text;
  }

  /**
   * Returns the time in seconds when the message was received and processed.
   */
  public function time()
  {
   return $this->time;
  }
}

