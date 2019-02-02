<?php namespace ewma\callCenter\call\controllers;

class Main extends \Controller
{
    private $call;

    public function __create()
    {
        $this->call = $this->unpackModel('call');

        $this->instance_($this->call->id);
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $s = $this->s('|', [
            'settings_visible' => false
        ]);

        $call = $this->call;
        $callPack = pack_model($this->call);
        $callXPack = xpack_model($this->call);

        $content = $call->name or
        $content = $call->path or
        $content = '...';

        $sMaster = $this->s('^');
        $outputCallId = false;
        if (isset($sMaster['output_call_id_by_cat_id'][$sMaster['selected_cat_id']])) {
            $outputCallId = $sMaster['output_call_id_by_cat_id'][$sMaster['selected_cat_id']];
        }

        $v->assign([
                       'COLOR_CLASS'        => $call->require_confirmation ? 'red' : 'blue',
                       'NAME'               => $this->c('\std\ui txt:view', [
                           'path'                => '>xhr:rename',
                           'data'                => [
                               'call' => $callXPack
                           ],
                           'class'               => 'txt',
                           'fitInputToClosest'   => $this->_selector('|') . " .content",
                           'placeholder'         => '...',
                           'editTriggerSelector' => $this->_selector('|') . " .rename.button",
                           'content'             => $content,
                           'contentOnInit'       => $call->name
                       ]),
                       'RENAME_BUTTON'      => $this->c('\std\ui tag:view', [
                           'attrs'   => [
                               'class' => 'rename button',
                               'hover' => 'hover',
                               'title' => 'Переименовать'
                           ],
                           'content' => '<div class="icon"></div>'
                       ]),
                       'SETTINGS_BUTTON'    => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:toggleSettingsVisible|',
                           'data'    => [
                               'call' => $callXPack
                           ],
                           'class'   => 'settings button',
                           'title'   => 'Настройки',
                           'content' => '<div class="icon"></div>'
                       ]),
                       'DUPLICATE_BUTTON'   => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:duplicate',
                           'data'    => [
                               'call' => $callXPack
                           ],
                           'class'   => 'duplicate button',
                           'title'   => 'Дублировать',
                           'content' => '<div class="icon"></div>'
                       ]),
                       'DELETE_BUTTON'      => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:delete',
                           'data'    => [
                               'call' => $callXPack
                           ],
                           'class'   => 'delete button',
                           'title'   => 'Удалить',
                           'content' => '<div class="icon"></div>'
                       ]),
                       'SHOW_OUTPUT_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:showOutput',
                           'data'    => [
                               'call' => $callXPack
                           ],
                           'class'   => 'show_output_button ' . ($outputCallId == $call->id ? 'pressed' : ''),
                           'attrs'   => [
                               'call_id' => $call->id
                           ],
                           'title'   => 'Показать последний ответ',
                           'content' => '<div class="icon"></div>'
                       ])
                   ]);

        if ($call->cron_enabled) {
            $v->assign('cron_schedule', [
                'VALUE' => $call->cron_schedule
            ]);
        }

        if ($call->async_enabled) {
            $v->assign('async');
        }

        if ($call->envs_filter_enabled) {
            $envs = l2a($call->envs_filter);

            $envId = $this->_env();

            foreach ($envs as $env) {
                $v->assign('env', [
                    'ID'    => $env,
                    'CLASS' => $envId == $env ? 'allowed' : ''
                ]);
            }
        }

        if ($s['settings_visible']) {
            $v->assign('settings', [
                'PATH'                               => $this->c('\std\ui txt:view', [
                    'path'              => '>xhr:updatePath',
                    'data'              => [
                        'call' => $callXPack
                    ],
                    'class'             => 'txt',
                    'placeholder'       => 'path',
                    'fitInputToClosest' => '.path',
                    'content'           => $call->path
                ]),
                'DATA'                               => $this->c('\std\ui\data~:view|' . $this->_nodeInstance(), [
                    'read_call'  => $this->_abs('>app:readData', ['call' => $callPack]),
                    'write_call' => $this->_abs('>app:writeData', ['call' => $callPack])
                ]),
                'INPUTS'                             => $this->c('inputs~:view', [
                    'call' => $call
                ]),
                'REQUIRE_CONFIRMATION_TOGGLE_BUTTON' => $this->c('\std\ui button:view', [
                    'path'    => '>xhr:toggleRequireConfirmation',
                    'data'    => [
                        'call' => $callXPack
                    ],
                    'class'   => 'require_confirmation button ' . ($call->require_confirmation ? 'pressed' : ''),
                    'title'   => $call->require_confirmation ? 'Выключить запрос подтверждения' : 'Включить запрос подтверждения',
                    'content' => '<div class="icon"></div>'
                ]),
                'CRON_TOGGLE_BUTTON'                 => $this->c('\std\ui button:view', [
                    'path'    => '>xhr:toggleCron',
                    'data'    => [
                        'call' => $callXPack
                    ],
                    'class'   => 'cron button ' . ($call->cron_enabled ? 'pressed' : ''),
                    'title'   => $call->cron_enabled ? 'Выключить выполнение по расписанию' : 'Включить выполнение по расписанию',
                    'content' => 'cron'
                ]),
                'ASYNC_TOGGLE_BUTTON'                => $this->c('\std\ui button:view', [
                    'path'    => '>xhr:toggleAsync',
                    'data'    => [
                        'call' => $callXPack
                    ],
                    'class'   => 'async button ' . ($call->async_enabled ? 'pressed' : ''),
                    'title'   => $call->async_enabled ? 'Выключить асинхронное выполнение' : 'Включить асинхронное выполнение',
                    'content' => 'async'
                ]),
                'ENVS_FILTER_TOGGLE_BUTTON'          => $this->c('\std\ui button:view', [
                    'path'    => '>xhr:toggleEnvsFilter',
                    'data'    => [
                        'call' => $callXPack
                    ],
                    'class'   => 'envs button ' . ($call->envs_filter_enabled ? 'pressed' : ''),
                    'title'   => $call->envs_filter_enabled ? 'Выключить фильтр окружения' : 'Включить фильтр окружения',
                    'content' => 'envs'
                ]),
            ]);

            if ($call->cron_enabled) {
                $v->assign('settings/cron', [
                    'SCHEDULE' => $this->c('\std\ui txt:view', [
                        'path'              => '>xhr:updateCronSchedule',
                        'data'              => [
                            'call' => $callXPack
                        ],
                        'class'             => 'txt',
                        'placeholder'       => 'cron',
                        'fitInputToClosest' => '.cron',
                        'content'           => $call->cron_schedule
                    ]),
                ]);
            }

            if ($call->envs_filter_enabled) {
                $v->assign('settings/envs', [
                    'LIST' => $this->c('\std\ui txt:view', [
                        'path'              => '>xhr:updateEnvsFilter',
                        'data'              => [
                            'call' => $callXPack
                        ],
                        'class'             => 'txt',
                        'placeholder'       => 'envs',
                        'fitInputToClosest' => '.envs',
                        'content'           => $call->envs_filter
                    ]),
                ]);
            }
        }

        $this->c('\std\ui button:bind', [
            'selector' => $this->_selector('|') . " .call_button",
            'path'     => '>xhr:perform|',
            'data'     => [
                'call' => $callXPack
            ],
            'class'    => 'run_button'
        ]);

        $this->css(':\css\std~, \js\jquery\ui icons');

        $this->widget(':|');

        $this->e('ewma/callCenter/calls/update/require_confirmation')->rebind(':reload');
        $this->e('ewma/callCenter/calls/update/cron_schedule')->rebind(':reload');
        $this->e('ewma/callCenter/calls/update/async')->rebind(':reload');
        $this->e('ewma/callCenter/calls/update/envs_filter')->rebind(':reload');

        return $v;
    }
}
