// head {
var __nodeId__ = "ss_commander_ui__main";
var __nodeNs__ = "ss_commander_ui";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        __create: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.bind();
            w.render();

            ewma.bind('ss/commander/enableKeyboard', function () {
                w.enableKeyboard();
            });

            ewma.bind('ss/commander/disableKeyboard', function () {
                w.disableKeyboard();
            });
        },

        render: function () {
            this.renderFocus(this.options.panels.focus);
        },

        keyboard: true,

        bind: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            $w.disableSelection();

            $(document).rebind("keydown." + __nodeId__ + ", keyup." + __nodeId__, function (e) {
                var which = e.which;

                if (w.keyboard) {
                    var changePanel;

                    if (e.type === 'keydown') {
                        if (which === 9) { // tab
                            changePanel = e.shiftKey ? -1 : 1;
                        }

                        if (which === 37) { // arrow left
                            changePanel = -1;
                        }

                        if (which === 39) { // arrow right
                            changePanel = 1;
                        }
                    }

                    if (changePanel) {
                        e.preventDefault();

                        var panelsCount = o.panels.list.length;
                        var focus = o.panels.focus;

                        var focusIndex = o.panels.list.indexOf(focus);

                        focusIndex += changePanel;
                        focusIndex = constrains_loop(focusIndex, 0, panelsCount - 1);

                        w.focus(o.panels.list[focusIndex]);
                    } else {
                        w.w(o.panels.focus).handleKeyboardEvent(e);
                    }
                } else {
                    // no keyboard listen
                }
            });
        },

        disableKeyboard: function () {
            this.keyboard = false;
        },

        enableKeyboard: function () {
            this.keyboard = true;
        },

        /**
         * FOCUS
         */

        focus: function (panelName) {
            if (this.options.panels.focus !== panelName) {
                this.setFocus(panelName);

                this.updateFocus();
            } else {
                this.renderFocus();
            }
        },

        setFocus: function (panelName) {
            this.options.panels.focus = panelName;

            this.renderFocus();
        },

        renderFocus: function () {
            this.element.find(".focusable").removeClass("focus");

            this.w(this.options.panels.focus).renderFocus();

            this.enableKeyboard();
        },

        updateFocus: function () {
            this.mr('focus', {
                value: this.options.panels.focus
            });
        }
    });
})(__nodeNs__, __nodeId__);
