// head {
var __nodeId__ = "ss_commander2_ui__dialogsWrapper";
var __nodeNs__ = "ss_commander2_ui";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        __create: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            $("input:first", $w).focus(); // todo test for page scroll

            $w.bind("mouseup", function () {
                w.w('commander').disableKeyboard();
            });
        }
    });
})(__nodeNs__, __nodeId__);
