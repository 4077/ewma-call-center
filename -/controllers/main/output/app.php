<?php namespace ewma\callCenter\controllers\main\output;

class App extends \Controller
{
    public function readOutputData()
    {
        if ($call = $this->unpackModel('call')) {
            $lastOutput = _j($call->last_output);

            if (is_array($lastOutput)) {
                $count = count($lastOutput);

                if ($count > 50) {
                    $lastOutput = array_slice($lastOutput, 0, 50);

                    $lastOutput[] = 'trimmed... total nodes: ' . $count;
                }
            }

            return $lastOutput;
        }
    }

    public function writeOutputData()
    {
        if ($call = $this->unpackModel('call')) {
            $call->last_output = j_($this->data('data'));
            $call->save();
        }
    }
}
