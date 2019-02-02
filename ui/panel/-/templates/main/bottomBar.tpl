<div class="{__NODE_ID__} focusable {FOCUS_CLASS}" instance="{__INSTANCE__}">

    <div class="df w100p jcsb alic">

        <div class="l">
            <!-- if create_buttons -->
            <div class="create_panel_button">
                <div class="icon fa fa-plus"></div>

                <div class="create_panel {CREATE_PANEL_PINNED_CLASS}">
                    <div class="content">
                        {CREATE_PANEL}
                    </div>

                    <div class="buttons">
                        <div class="pin {PIN_BUTTON_PINNED_CLASS}">
                            <div class="icon fa fa-thumb-tack"></div>
                        </div>
                        {__CREATE_PAGE_BUTTON}
                        <div class="create_button create_page_button" title="Создать страницу">
                            <div class="icon icon fa fa-file"></div>
                        </div>
                        {CREATE_CONTAINER_BUTTON}
                        {CREATE_FOLDER_BUTTON}
                        <!-- if create_product_button -->
                        <div class="create_button create_product_button" title="Создать товар">
                            <div class="icon icon fa fa-puzzle-piece"></div>
                        </div>
                        <!-- / -->
                    </div>
                </div>
            </div>
            <div class="create_buttons">
                {__CREATE_PAGE_BUTTON}
                <div class="create_button create_page_button" title="Создать страницу">
                    <div class="icon icon fa fa-file"></div>
                </div>
                {CREATE_CONTAINER_BUTTON}
                {CREATE_FOLDER_BUTTON}
                <!-- if create_product_button -->
                <div class="create_button create_product_button" title="Создать товар">
                    <div class="icon icon fa fa-puzzle-piece"></div>
                </div>
                <!-- / -->
            </div>
            <!-- / -->
        </div>

        <div class="c">
            <!-- copy_button -->
            <div class="copy button {DISABLED_CLASS}">Копировать</div>
            <!-- / -->
            <!-- install_button -->
            <div class="install button {DISABLED_CLASS}">Установить</div>
            <!-- / -->
            <!-- move_button -->
            <div class="move button {DISABLED_CLASS}">Переместить</div>
            <!-- / -->
            <!-- edit_button -->
            <div class="edit button {DISABLED_CLASS}">Редактировать</div>
            <!-- / -->
            <!-- delete_button -->
            <div class="delete button {DISABLED_CLASS}">Удалить</div>
            <!-- / -->
        </div>

        <div class="r">
            <!-- plugins -->
            <div class="plugins_button">
                <div class="icon fa fa-plug"></div>

                <div class="plugin_selector">
                    <!-- plugin -->
                    {BUTTON}
                    <!-- / -->
                    <div class="plugins_button_over">
                        <div class="icon fa fa-plug"></div>
                    </div>
                </div>
            </div>
            <!-- / -->
        </div>
    </div>

</div>
