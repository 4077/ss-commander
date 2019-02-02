<div class="{__NODE_ID__}" instance="{__INSTANCE__}">

    <div class="main focusable {FOCUS_CLASS}">
        <div class="nav">
            {TREE_SELECTOR}

            <div class="root_indicator {ROOT_INDICATOR_FOCUS_CLASS}">
                <div class="icon fa {ROOT_INDICATOR_ICON}"></div>
            </div>

            <div class="branch">
                <!-- branch_node -->
                <div class="node" cat_id="{ID}">
                    <div class="icon fa fa-chevron-right"></div>
                    <div class="name">{NAME}</div>
                </div>
                <!-- / -->
            </div>
        </div>

        <div class="buttons">
            <!-- force_collapse_buttons -->
            <div class="force_button collapse {COLLAPSE_PRESSED_CLASS}" mode="collapse">
                <div class="icon fa fa-compress"></div>
            </div>
            <div class="force_button expand {EXPAND_PRESSED_CLASS}" mode="expand">
                <div class="icon fa fa-expand"></div>
            </div>
            <!-- / -->
            {ORDERING_TOGGLE_BUTTON}
        </div>
    </div>

    <div class="filters focusable {FOCUS_CLASS}">
        {DIVISION_SELECTOR}
        {WAREHOUSE_SELECTOR}
    </div>

</div>
