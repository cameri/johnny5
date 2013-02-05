<?php
class IRCClient
{
  private $stream;
  private $server_address;
  private $server_port;

  public function __construct($server_address, $server_port)
  {
    $this->server_address = $server_address;
    $this->server_port = $server_port;
  }

  public function connect()
  {
    if ($this->is_alive())
    {
      throw new Exception("Already connected.");
    }
    else
    {
      echo 'Connecting to IRC @ "' . $this->server_address . ':' . $this->server_port . '"...' . PHP_EOL;
    }
    // open stream
    $this->stream = stream_socket_client(
      "tcp://" . $this->server_address . ":" . $this->server_port,
      $errno, $errstr);
    if (!$this->stream)
      throw new Exception("Unable to open stream.\n\t" .
        "Reason: $errmsg ($errno)." );

    if (!stream_set_blocking($this->stream, 0))
      throw new Exception("Unable to create a non-blocking stream.");

    stream_set_write_buffer($this->stream, 0);
    //  throw new Exception("Unable to create an unbuffered stream.");

    if (!stream_set_timeout($this->stream, 0))
      throw new Exception("Unable to disable stream timeout.");

    sleep(2);
  }

  public function read()
  {
     // RFC 2812 Section 2.3
     // Max. Message Length = 512 (including crlf)
     if(($buf = @fgets($this->stream, 512)) === false)
       return false;
     return $buf;
  }

  public function write($buf)
  {
    $buf = trim($buf);
    $len = strlen($buf);
    if ($len) {
      if (fwrite($this->stream, $buf."\n",  $len+1) === false)
        throw new Exception("Unable to write to stream.");
    }
    usleep(50*1000); // delay between each msg, dont spam server
  }

  public function is_alive()
  {
    return @is_resource($this->stream) && !@feof($this->stream);
  }

  public function disconnect()
  {
    if ($this->is_alive()) {
      echo 'Disconnecting from IRC @ "' . $this->server_address . ':' . $this->server_port . '"...' . PHP_EOL;
      usleep(100*1000); // 100ms
      fclose($this->stream);
    }
  }

  function __destruct()
  {
    // disconnect socket
    $this->disconnect();
    // clean up!
    $this->stream = null;
  }

}
