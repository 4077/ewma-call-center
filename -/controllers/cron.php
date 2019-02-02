<?php namespace ewma\callCenter\controllers;

class Cron extends \Controller
{
    public function run()
    {
        $envId = $this->_env();

        $scheduler = new \GO\Scheduler();

        $scheduledCalls = \ewma\callCenter\models\Call::where('cron_enabled', true)->orderBy('position')->get();

        $output = [];

        foreach ($scheduledCalls as $call) {
            $perform = true;
            if ($call->envs_filter_enabled) {
                $perform = in($envId, $call->envs_filter);
            }

            if ($perform) {
                $scheduler->call(function ($call) use (&$output) {
                    $output[] = $this->performCall($call);
                }, [$call])->at($call->cron_schedule);
            }
        }

        $scheduler->run();

        return $output;
    }

    private function performCall($call)
    {
        $callData = _j($call->data);

        if ($call->async_enabled) {
            $output = $this->async($call->path, $callData);
        } else {
            $output = $this->c($call->path, $callData);
        }

        $call->last_output = j_($output);
        $call->save();

        return $output;
    }
}
