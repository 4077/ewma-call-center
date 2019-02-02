// head {
var __nodeId__ = "ewma_callCenter_call__main";
var __nodeNs__ = "ewma_callCenter_call";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, {
        options: {},

        _create: function () {
            var widget = this;
            var $widget = widget.element;

            var $cronButton = $(".cron.button", $widget);
            var $envsButton = $(".envs.button", $widget);

            var $cronSettings = $(".settings > .cron", $widget);
            var $envsSettings = $(".settings > .envs", $widget);

            $cronButton.mouseenter(function () {
                $envsSettings.hide();
                $cronSettings.show();
            });

            $envsButton.mouseenter(function () {
                $cronSettings.hide();
                $envsSettings.show();
            });

            $widget.mouseleave(function () {
                var hasInputInFocus = false;

                $("input", $widget).each(function () {
                    if ($(this).is(":focus")) {
                        hasInputInFocus = true;
                    }
                });

                if (!hasInputInFocus) {
                    $cronSettings.hide();
                    $envsSettings.hide();
                }
            });
        }
    });
})(__nodeNs__, __nodeId__);
