// head {
var __nodeId__ = "ss_commander_ui_panel__main";
var __nodeNs__ = "ss_commander_ui_panel";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        __create: function () {
            this.bind();
        },

        bind: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            if (o.reload) {
                setTimeout(function () {
                    w.w('main').render();
                });
            }
        },

        attachedPlugin: false,

        attachPlugin: function (pluginW) {
            this.attachedPlugin = pluginW;
        },

        handleKeyboardEvent: function (e) {
            var w = this;
            var o = w.options;

            var which = e.which;
            var type = e.type;

            var prevent = false;
            var handled = false;

            if (type === 'keyup' && which === 192) { // `
                if (e.ctrlKey) {
                    w.r('togglePluginsPanel');
                } else {
                    if (o.focus === 'plugins') {
                        w.focus('content');
                    } else {
                        if (o.pluginsPanelEnabled) {
                            w.focus('plugins');
                        }
                    }
                }

                handled = true;
                prevent = true;
            }

            if (!handled) {
                if (o.focus === 'plugins' && w.attachedPlugin) {
                    w.attachedPlugin.handleKeyboardEvent(e);
                } else {
                    w.w('content').handleKeyboardEvent(e);
                }
            }

            if (prevent) {
                e.preventDefault();
            }
        },

        /**
         * FOCUS
         */

        rootFocused: function (value) {
            this.w('topBar').rootFocused(value);
        },

        focus: function (section) {
            if (this.options.focus !== section) {
                this.setFocus(section);
                this.updateFocus();
            } else {
                this.w('main').setFocus(this.options.panelName);
            }
        },

        setFocus: function (section) {
            this.options.focus = section;

            this.renderFocus();

            this.w('main').focus(this.options.panelName);
        },

        renderFocus: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            if (o.focus === 'plugins') {
                $(".plugins_panel", $w).find(".focusable").addClass("focus");
            } else {
                $(".main_panel", $w).find(".focusable").addClass("focus");
            }
        },

        updateFocus: function () {
            var w = this;
            var o = w.options;

            w.mr('focus', {
                focus: o.focus
            });
        }
    });
})(__nodeNs__, __nodeId__);
