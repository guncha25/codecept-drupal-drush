<?php

namespace Codeception\Module;

use Codeception\Module;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class DrupalDrush extends Module {

    /**
     * Execute a drush command.
     *
     * @param string $command
     *   Command to run.
     *   e.g. "cc"
     * @param array $arguments
     *   Array of arguments.
     *   e.g. array("all")
     * @param array $options
     *   Array of options .
     *   e.g. array("--uid=1", "-y").
     * @param string $drush
     *   The drush command to use.
     *
     * @return Process
     *   a symfony/process instance to execute.
     */
    public function getDrush($command, array $arguments, $options = array(), $drush = 'drush')
    {
        $args = array($drush, $command);
        $command_args = array_merge($args, $arguments);
        $processBuilder = new ProcessBuilder($command_args);

        // Set working directory if configured.
        if ($pwd = $this->_getConfig('working_directory')) {
          $processBuilder->setWorkingDirectory($pwd);
        }

        foreach ($options as $option) {
          $processBuilder->add($option);
        }

        $this->debugSection('Command', $processBuilder->getProcess()->getCommandLine());
        return $processBuilder->getProcess();
    }

    public function getLoginUri($uid = '') {
      $user = [];
      if (!empty($uid)) {
        $user = ['--uid=' . $uid];
      }
      /** @var \Symfony\Component\Process\Process $process */
      $process = $this->getDrush('uli', $user);
      $process->run();
      $gen_url = str_replace(PHP_EOL, '', $process->getOutput());
      return substr($gen_url, strpos($gen_url, '/user/reset'));
    }
}
