<?php

class Exceed_Orderexport_Helper_ftp extends Mage_Core_Helper_Abstract {

    private $connectionId;
    private $loginOK = false;
    Private $messageArray = array();

    private function logMessage($message) {
        $this->messageArray[] = $message;
    }

    public function getMessages() {
        return $this->messageArray;
    }

    public function connect($server, $ftpUser, $ftpPassword, $isPassive = false) {
        // set up basic connection
        $this->connectionId = ftp_connect($server);

        //login with user name and password
        $loginResult = ftp_login($this->connectionId, $ftpUser, $ftpPassword);

        //set passive mode on/off
        ftp_pasv($this->connectionId, $isPassive);

        //check connection
        if ((!$this->connectionId) || (!$loginResult)) {
            $this->logMessage('FTP connection has failed!');
            $this->logMessage("Attempted to connect to $server for user $ftpUser !");
            return false;
        } else {
            $this->logMessage("Successfully connected to server $server");
            $this->loginOK = true;
            return true;
        }
    }

    public function makeDir($directory) {
        if (ftp_mkdir($this->connectionId, $directory)) {
            $this->logMessage("Directory $directory created Successfully");
            return true;
        } else {
            $this->logMessage("Failed creating directory $directory !");
            return false;
        }
    }

    public function uploadFile($fileFrom, $fileTo) {
        //set the transfer mode
        $mode = $this->setTransferMode($fileFrom);

        //uploade file
        if (ftp_put($this->connectionId, $fileTo, $fileFrom, $mode)) {
            $this->logMessage("Successfully uploaded $fileFrom to $fileTo");
            return true;
        } else {
            $this->logMessage("FTP upload from $fileFrom to $fileTo has failed!");
            return false;
        }
    }

    public function downloadFile($fileFrom, $fileTo) {
        //set the transfer mode
        $mode = $this->setTransferMode($fileFrom);

        //download file
        if(ftp_get($this->connectionId, $fileTo, $fileFrom, $mode, 0)) {
            $this->logMessage("Successfully downloaded $fileFrom to $fileTo");
            return true;
        } else {
            $this->logMessage("FTP download from $fileFrom to $fileTo has failed!");
            return false;
        }
    }

    public function renameFile($fileFrom, $fileTo) {
        if (ftp_rename($this->connectionId, $fileFrom, $fileTo)) {
            $this->logMessage("Successfully renamed from $fileFrom to $fileTo");
            return true;
        } else {
            $this->logMessage("renaming from $fileFrom to $fileTo has failed!");
            return false;
        }
    }

    public function closeConnection() {
        if ($this->connectionId) {
            if (ftp_close($this->connectionId)) {
                $this->logMessage('Successfully disconnected');
                return true;
            } else {
                $this->logMessage("Failed disconnecting from server!");
                return false;
            }
        }
    }

    protected function setTransferMode($fileName) {
        $asciiArray = array('txt', 'csv');
        $extension = end(explode('.', $fileName));
        if (in_array($extension, $asciiArray)) {
            $mode = FTP_ASCII;
        } else {
            $mode = FTP_BINARY;
        }
        return $mode;
    }

}

?>