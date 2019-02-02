<?php namespace ewma\callCenter\call\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function __create()
    {
        $this->a() or $this->lock();
    }

    public function perform()
    {
        if ($this->data('discarded')) {
            $this->closePerformDialogs();
        } else {
            if ($call = $this->unxpackModel('call')) {
                if ($call->envs_filter_enabled) {
                    $envId = $this->_env();

                    if (!in($envId, $call->envs_filter)) {
                        if ($this->data('ignore_env_filter_confirmed')) {
                            $this->confirmIgnoreEnvFilterDialogClose();
                            $perform = true;
                        } else {
                            $this->confirmIgnoreEnvFilterDialogOpen($call);
                            $perform = false;
                        }
                    } else {
                        $perform = true;
                    }
                } else {
                    $perform = true;
                }

                if ($perform) {
                    $hasInputs = _j($call->inputs);

                    if ($hasInputs) {
                        if ($this->data('inputs_form_confirmed')) {
                            $this->inputsFormDialogClose();
                            $perform = true;
                        } else {
                            $this->inputsFormDialogOpen($call);
                            $perform = false;
                        }
                    } else {
                        $perform = true;
                    }
                }

                if ($perform) {
                    if ($call->require_confirmation) {
                        if ($this->data('confirmation_confirmed')) {
                            $perform = true;
                        } else {
                            $this->confirmDialogOpen($call);
                            $perform = false;
                        }
                    } else {
                        $perform = true;
                    }
                }

                if ($perform) {
                    $this->performCall($call);
                }
            }
        }
    }

    private function performCall($call)
    {
        $callData = _j($call->data);

        $s = $this->s('~inputsForm|');

        if (!empty($s['inputs'])) {
            ra($callData, $s['inputs']);

            foreach (a2f($s['nulls']) as $path => $value) {
                if ($value) {
                    $callDataValue = &ap($callData, $path);
                    $callDataValue = null;
                }
            }
        }

        if ($call->async_enabled) {
            $output = $this->async($call->path, $callData);
        } else {
            $output = $this->c($call->path, $callData);
        }

        $call->last_output = j_($output);
        $call->save();

        $this->closePerformDialogs();

        $this->showOutput();
    }

    private function getCallName($call)
    {
        $callName = $call->name or
        $callName = $call->path or
        $callName = '...';

        return $callName;
    }

    private function inputsFormDialogOpen($call)
    {
        $this->c('\std\ui\dialogs~:open:inputsForm|ewma/callCenter', [
            'path'          => '~inputsForm:view',
            'data'          => [
                'call'         => $this->data['call'],
                'confirm_call' => $this->_abs([':perform|', $this->data]),
                'discard_call' => $this->_abs([':perform|', $this->data])
            ],
            'pluginOptions' => [
                'title'     => 'Данные для вызова ' . $this->getCallName($call),
                'resizable' => 'false'
            ]
        ]);
    }

    private function confirmDialogOpen($call)
    {
        $this->c('\std\ui\dialogs~:open:performConfirm|ewma/callCenter', [
            'path'          => '\std dialogs/confirm~:view',
            'data'          => [
                'confirm_call' => $this->_abs([':perform|', array_merge($this->data, ['confirmation_confirmed' => true])]),
                'discard_call' => $this->_abs([':perform|', $this->data]),
                'message'      => 'Выполнить вызов <b>' . $this->getCallName($call) . '</b>?'
            ],
            'pluginOptions' => [
                'resizable' => 'false'
            ]
        ]);
    }

    private function confirmIgnoreEnvFilterDialogClose()
    {
        $this->c('\std\ui\dialogs~:close:performConfirm|ewma/callCenter');
    }

    private function confirmIgnoreEnvFilterDialogOpen($call)
    {
        $this->c('\std\ui\dialogs~:open:performConfirm|ewma/callCenter', [
            'path'          => '\std dialogs/confirm~:view',
            'data'          => [
                'confirm_call' => $this->_abs([':perform|', array_merge($this->data, ['ignore_env_filter_confirmed' => true])]),
                'discard_call' => $this->_abs([':perform|', $this->data]),
                'message'      => 'Выполнить вызов <b>' . $this->getCallName($call) . '</b> несмотря на фильтр окружений?'
            ],
            'pluginOptions' => [
                'resizable' => 'false'
            ]
        ]);
    }

    private function inputsFormDialogClose()
    {
        $this->c('\std\ui\dialogs~:close:inputsForm|ewma/callCenter');
    }

    private function closePerformDialogs()
    {
        $this->c('\std\ui\dialogs~:close:inputsForm|ewma/callCenter');
        $this->c('\std\ui\dialogs~:close:performConfirm|ewma/callCenter');
    }

    public function toggleSettingsVisible()
    {
        if ($call = $this->unxpackModel('call')) {
            $s = &$this->s('~|');

            invert($s['settings_visible']);

            $this->c('<:reload', [], 'call');
        }
    }

    public function duplicate()
    {
        if ($call = $this->unxpackModel('call')) {
            \ewma\callCenter\models\Call::create($call->toArray());

            $this->e('ewma/callCenter/calls/create', ['cat_id' => $call->cat->id])->trigger(['call' => $call]);
        }
    }

    public function rename()
    {
        if ($call = $this->unxpackModel('call')) {
            $txt = \std\ui\Txt::value($this);

            $call->name = $txt->value;
            $call->save();

            $txt->response(
                $call->name ? $call->name : ($call->path ? $call->path : ''),
                $call->name
            );
        }
    }

    public function updatePath()
    {
        if ($call = $this->unxpackModel('call')) {
            $txt = \std\ui\Txt::value($this);

            $call->path = $txt->value;
            $call->save();

            $txt->response();
        }
    }

    public function toggleRequireConfirmation()
    {
        if ($call = $this->unxpackModel('call')) {
            $call->require_confirmation = !$call->require_confirmation;
            $call->save();

            $this->e('ewma/callCenter/calls/update/require_confirmation', ['call_id' => $call->id])->trigger(['call' => $call]);
        }
    }

    public function updateCronSchedule()
    {
        if ($call = $this->unxpackModel('call')) {
            $txt = \std\ui\Txt::value($this);

            $call->cron_schedule = $txt->value;
            $call->save();

            $txt->response();

            $this->e('ewma/callCenter/calls/update/cron_schedule', ['call_id' => $call->id])->trigger(['call' => $call]);
        }
    }

    public function toggleCron()
    {
        if ($call = $this->unxpackModel('call')) {
            $call->cron_enabled = !$call->cron_enabled;
            $call->save();

            $this->e('ewma/callCenter/calls/update/cron_schedule', ['call_id' => $call->id])->trigger(['call' => $call]);
        }
    }

    public function toggleAsync()
    {
        if ($call = $this->unxpackModel('call')) {
            $call->async_enabled = !$call->async_enabled;
            $call->save();

            $this->e('ewma/callCenter/calls/update/async', ['call_id' => $call->id])->trigger(['call' => $call]);
        }
    }

    public function updateEnvsFilter()
    {
        if ($call = $this->unxpackModel('call')) {
            $txt = \std\ui\Txt::value($this);

            $call->envs_filter = $txt->value;
            $call->save();

            $txt->response();

            $this->e('ewma/callCenter/calls/update/envs_filter', ['call_id' => $call->id])->trigger(['call' => $call]);
        }
    }

    public function toggleEnvsFilter()
    {
        if ($call = $this->unxpackModel('call')) {
            $call->envs_filter_enabled = !$call->envs_filter_enabled;
            $call->save();

            $this->e('ewma/callCenter/calls/update/envs_filter', ['call_id' => $call->id])->trigger(['call' => $call]);
        }
    }

    public function delete()
    {
        if ($this->data('discarded')) {
            $this->c('\std\ui\dialogs~:close:deleteCallConfirm|ewma/callCenter');
        } else {
            if ($call = $this->unxpackModel('call')) {
                if ($this->data('confirmed')) {
                    $call->delete();

                    $this->c('\std\ui\dialogs~:close:deleteCallConfirm|ewma/callCenter');

                    $this->e('ewma/callCenter/calls/delete', ['cat_id' => $call->cat->id])->trigger(['call' => $call]);
                } else {
                    $this->c('\std\ui\dialogs~:open:deleteCallConfirm|ewma/callCenter', [
                        'path'          => '\std dialogs/confirm~:view',
                        'data'          => [
                            'confirm_call' => $this->_abs([':delete', ['call' => $this->data['call']]]),
                            'discard_call' => $this->_abs([':delete', ['call' => $this->data['call']]]),
                            'message'      => 'Удалить вызов <b>' . ($call->name ? $call->name : ($call->path ? $call->path : '...')) . '</b>?'
                        ],
                        'pluginOptions' => [
                            'resizable' => 'false'
                        ]
                    ]);
                }
            }
        }
    }

    public function showOutput()
    {
        if ($call = $this->unxpackModel('call')) {
            $s = &$this->s('^');

            $s['output_call_id_by_cat_id'][$s['selected_cat_id']] = $call->id;

            $this->c('^~output:reload');

            $this->jquery(".show_output_button")->removeClass('pressed');
            $this->jquery(".show_output_button[call_id='" . $call->id . "']")->addClass('pressed');
        }
    }
}
