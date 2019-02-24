<?php namespace ewma\callCenter\controllers\main\cats;

class Cp extends \Controller
{
    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $rootCat = $this->getRootNode();

        $catXPack = xpack_model($rootCat);

        $v->assign([
                       'EXCHANGE_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '@node/xhr:exchangeDialog|',
                           'data'    => [
                               'cat' => $catXPack
                           ],
                           'class'   => 'button exchange',
                           'title'   => 'Импорт/экспорт',
                           'content' => '<div class="icon"></div>'
                       ]),
                       'CREATE_BUTTON'   => $this->c('\std\ui button:view', [
                           'path'    => '@node/xhr:create|',
                           'data'    => [
                               'cat' => $catXPack
                           ],
                           'class'   => 'button create',
                           'title'   => 'Создать',
                           'content' => '<div class="icon"></div>'
                       ]),
                   ]);

        $this->css(':\js\jquery\ui icons');

        $this->css();

        return $v;
    }

    private function getRootNode()
    {
        if (!$node = \ewma\callCenter\models\Cat::where('parent_id', 0)->first()) {
            $node = \ewma\callCenter\models\Cat::create(['parent_id' => 0]);
        }

        return $node;
    }
}
