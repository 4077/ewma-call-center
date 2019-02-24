<?php namespace ewma\callCenter\controllers;

class Main extends \Controller
{
    public function __create()
    {
        $this->a() or $this->lock();
    }

    public function reload()
    {
        $this->jquery()->replace($this->view());
    }

    public function view()
    {
        $v = $this->v();

        $s = $this->s(false, [
            'selected_cat_id'          => false,
            'output_call_id_by_cat_id' => [],
            'cats_width'               => 250,
            'cats_scroll'              => [0, 0],
            'calls_width'              => 250,
            'calls_scroll_by_cat'      => []
        ]);

        $v->assign([
                       'CATS_CP'     => $this->c('>cats/cp:view'),
                       'CATS'        => $this->c('>cats:view'),
                       'CATS_WIDTH'  => $s['cats_width'],
                       'CALLS'       => $this->c('>calls:view'),
                       'CALLS_WIDTH' => $s['calls_width'],
                       'OUTPUT'      => $this->c('>output:view')
                   ]);

        $this->c('\std\ui resizable:bind', [
            'selector'      => $this->_selector('|') . ' .cats',
            'path'          => '>xhr:updateCatsWidth',
            'pluginOptions' => [
                'handles' => 'e'
            ]
        ]);

//        $this->c('\std\ui resizable:bind', [
//            'selector'      => $this->_selector('|') . ' .calls_container',
//            'path'          => '>xhr:updateCallsWidth',
//            'pluginOptions' => [
//                'handles' => 'e'
//            ]
//        ]);

        $this->c('\std\ui\dialogs~:addContainer:ewma/callCenter');

        $this->css();

        $this->c('\css\fontawesome~:load');

        $this->app->html->setFavicon(abs_url('-/ewma/favicons/dev_callCenter.png'));


        $this->widget(':|', [
            '.r'          => [
                'updateCallsWidth'  => $this->_p('>xhr:updateCallsWidth'),
                'updateCatsScroll'  => $this->_p('>xhr:updateCatsScroll'),
                'updateCallsScroll' => $this->_p('>xhr:updateCallsScroll')
            ],
            'catsScroll'  => $s['cats_scroll'],
            'callsScroll' => ap($s, 'calls_scroll_by_cat/' . $s['selected_cat_id']) ?: [0, 0]
        ]);

        return $v;
    }
}
