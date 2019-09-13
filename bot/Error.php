<?php

namespace bot;

class Error
{
    public function __construct()
    {
        set_error_handler([$this, 'error']);
        register_shutdown_function([$this, 'fatalError']);
    }

    public function error($errno, $errorstr, $file, $line)
    {
        $this->writeError($errno, $errorstr, $file, $line);
        return true;
    }

    public function fatalError()
    {
        if (!empty($err = error_get_last()) and $err['type'] & (E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR)) {
            ob_get_clean();
            $this->writeError($err['type'], $err['message'], $err['file'], $err['line']);
        }
    }

    protected function writeError($errno, $errorstr, $file, $line)
    {
        $logName = ERR_LOG_DIRECTORY . "/log_" . date('d-m-Y') . ".txt";
        $errMessage = "=======================\n";
        $errMessage .= date('H:i:s');
        $errMessage .= "\nНомер ошибки: " . $errno . "\nТекст ошибки: " . $errorstr . "\nФайл ошибки:  " . $file . "\nСтрока ошибки: " . $line . "\n";
        file_put_contents($logName, $errMessage, FILE_APPEND);
    }
}
