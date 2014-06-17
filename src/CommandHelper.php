<?php
namespace SiteMaster\Plugins\Metric_pa11y;

/**
 * Note: commands to help with testing:
 * 
 * Show all processes: `ps x -o  "%p %r %y %x %c "`
 *  node and phantomjs show up after running pa11y if this fails.
 * 
 * Show parent ids (ppids) `ps x -f`
 * 
 * Class CommandHelper
 * @package SiteMaster\Plugins\Metric_pa11y
 */
class CommandHelper
{
    /**
     * Executes the command with the given arguments. If a timeout is given (in seconds), the command will be terminated if it takes longer.
     *
     * Note - borrowed from: https://github.com/nubs/hiatus/blob/master/src/hiatus.php
     *
     * @param string $command The shell command to execute. This can contain arguments, but make sure to use PHP's escapeshellarg for any arguments supplied by the user.
     * @param float $timeout If given, this will terminate the command if it does not finish before the timeout expires.
     * @param string $stdin A string to pass to the command on stdin.
     * @throws \Exception
     * arguments unescaped. If a key in the array is not numeric, then it will be included as well in a KEY=VALUE format.
     * @return array A 3-member array is returned.
     * * int The exit code of the command.
     * * string The output of the command.
     * * string The stderr output of the command.
     */
    public function exec($command, $timeout = null, $stdin = null)
    {
        $pipes = null;
        $pipeSpec = array(1 => array('pipe', 'w'), 2 => array('pipe', 'w'));
        if ($stdin !== null) {
            $pipeSpec[0] = array('pipe', 'r');
        }

        $process = proc_open($command, $pipeSpec, $pipes);
        if ($process === false) {
            throw new \Exception("Error executing command '{$command}' with proc_open.");
        }

        if ($stdin !== null) {
            fwrite($pipes[0], $stdin);
            fclose($pipes[0]);
        }

        if ($timeout !== null) {
            $timeout *= 1000000;
        }

        stream_set_blocking($pipes[1], 0);
        stream_set_blocking($pipes[2], 0);
        $stdout = '';
        $stderr = '';
        $exitCode = null;
        while ($timeout === null || $timeout > 0) {
            $start = microtime(true);

            $read = [$pipes[1], $pipes[2]];
            $other = [];
            stream_select($read, $other, $other, 0, $timeout);

            $status = proc_get_status($process);

            $stdout .= stream_get_contents($pipes[1]);
            $stderr .= stream_get_contents($pipes[2]);

            if (!$status['running']) {
                $exitCode = $status['exitcode'];
                break;
            }

            if ($timeout !== null) {
                $timeout -= (microtime(true) - $start) * 1000000;
            }
        }

        $status = proc_get_status($process);

        if($status['running'] == true) { //process ran too long, kill it
            //close all pipes that are still open
            fclose($pipes[1]); //stdout
            fclose($pipes[2]); //stderr
            //get the parent pid of the process we want to kill
            $ppid = $status['pid'];
            //use ps to get all the children of this process, and kill them
            $this->killChildren($ppid);
        }

        proc_terminate($process, 9);
        $closeStatus = proc_close($process);

        if ($exitCode === null) {
            $exitCode = $closeStatus;
        }

        return array($exitCode, $stdout, $stderr);
    }

    /**
     * A recursive function to kill parent and child processes
     *
     * @param $ppid - The pid of the parent process to kill (and its children)
     */
    protected function killChildren($ppid)
    {
        //Get the child pids
        $pids = preg_split('/\s+/', `ps -o pid --no-heading --ppid $ppid`);

        //Kill the this parent
        posix_kill($ppid, 9); //9 is the SIGKILL signal

        foreach($pids as $pid) {
            if(is_numeric($pid)) {
                //Kill each child, and their children
                $this->killChildren($pid);
            }
        }


    }
}