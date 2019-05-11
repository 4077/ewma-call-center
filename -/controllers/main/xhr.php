<?php namespace ewma\callCenter\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function updateCatsWidth()
    {
        $this->s('~:cats_width', $this->data('width'), RR);
    }

    public function updateCallsWidth()
    {
        $this->s('~:calls_width', $this->data('width'), RR);
    }

    public function updateCatsScroll()
    {
        $this->s('~:cats_scroll', [$this->data('left'), $this->data('top')], RR);
    }

    public function updateCallsScroll()
    {
        $this->s('~:calls_scroll_by_cat/' . $this->s('~:selected_cat_id'), [$this->data('left'), $this->data('top')], RR);
    }

    public function moveCatToRoot()
    {
        if ($cat = \ewma\callCenter\models\Cat::find($this->data('cat_id'))) {
            $rootCat = $this->getRootCat();

            $cat->parent_id = $rootCat->id;
            $cat->save();

            $this->c('~:reload');
        }
    }

//    public function moveCallToRoot()
//    {
//        if ($call = \ewma\callCenter\models\Call::find($this->data('call_id'))) {
//            $rootCat = $this->getRootCat();
//
//            $call->cat_id = $rootCat->id;
//            $call->save();
//
//            $this->c('~:reload');
//        }
//    }

    private function getRootCat()
    {
        if (!$node = \ewma\callCenter\models\Cat::where('parent_id', 0)->first()) {
            $node = \ewma\callCenter\models\Cat::create(['parent_id' => 0]);
        }

        return $node;
    }
}
