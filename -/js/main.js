// head {
var __nodeId__ = "ewma_callCenter__main";
var __nodeNs__ = "ewma_callCenter";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        __create: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            var $callsContainer = $("> .calls_container", $w);

            $callsContainer.resizable({
                handles:  'e',
                minWidth: 287,

                resize: function (e, ui) {
                    ui.element.find(".ewma_callCenter_call__main .call").width(ui.element.width() - 65);
                },

                stop: function (e, ui) {
                    w.r('updateCallsWidth', {
                        width: ui.element.width()
                    })
                }
            });

            var $cats = $("> .cats > .tree", $w);
            var $calls = $("> .calls_container > .calls", $w);

            $cats.scrollLeft(o.catsScroll[0]).scrollTop(o.catsScroll[1]);
            $calls.scrollLeft(o.callsScroll[0]).scrollTop(o.callsScroll[1]);

            var scrollTimeout;

            $cats.rebind("scroll." + __nodeId__, function () {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(function () {
                    w.r('updateCatsScroll', {
                        left: $cats.scrollLeft(),
                        top:  $cats.scrollTop()
                    });
                }, 400);
            });

            $calls.rebind("scroll." + __nodeId__, function () {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(function () {
                    w.r('updateCallsScroll', {
                        left: $calls.scrollLeft(),
                        top:  $calls.scrollTop()
                    });
                }, 400);
            });
        }
    });
})(__nodeNs__, __nodeId__);
