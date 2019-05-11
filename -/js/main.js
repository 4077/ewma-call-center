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

            //
            //
            //

            $(".ewma_callCenter__main_cats_cp", $w).droppable({
                accept:      ".std_ui_tree__main[instance='ewma_callCenter__main_cats'] .nodes", //, .ewma_callCenter__main_calls .call",
                activeClass: 'droppable_active',
                hoverClass:  'droppable_hover',
                tolerance:   'pointer',

                drop: function (e, ui) {
                    droppableBlock = true;

                    var draggable = ui.draggable;

                    if (draggable.hasClass("nodes")) {
                        var nodeId = draggable.attr("node_id");

                        w.r('moveCatToRoot', {
                            cat_id: nodeId
                        });
                    }

                    /*if (draggable.hasClass("call")) {
                        var callId = draggable.attr("call_id");

                        w.r('moveCallToRoot', {
                            call_id: callId
                        });
                    }*/

                    setTimeout(function () {
                        droppableBlock = false;
                    }, 0);
                }
            });
        }
    });
})(__nodeNs__, __nodeId__);
