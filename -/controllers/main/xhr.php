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
}
