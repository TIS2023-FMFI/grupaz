<?php

namespace App\Service;

class Logger
{
    private $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function writeLog(string $message, ...$variables)
    {
        $logFilePath = $this->projectDir . '/var/log/dev.log';

        $formattedVariables = $this->formatVariables($variables);
        $logMessage = sprintf("[%s] %s %s\n", date('d.m.Y H:i:s'), $message, $formattedVariables);

        file_put_contents($logFilePath, $logMessage . file_get_contents($logFilePath));
    }

    private function formatVariables(array $variables)
    {
        $formattedVariables = [];
        for ($i = 0; $i < count($variables); $i += 2) {
            $key = $variables[$i];
            $value = json_encode($variables[$i + 1]);
            $formattedVariables[] = sprintf('%s %s', $key, $value);
        }

        return implode(' ', $formattedVariables);
    }
}