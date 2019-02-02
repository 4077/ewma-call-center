<div class="{__NODE_ID__}" instance="{__INSTANCE__}">

    {SHOW_OUTPUT_BUTTON}

    <div class="call_button {COLOR_CLASS}" hover="hover">
        <div class="buttons" hover="hover">
            {SETTINGS_BUTTON}
            {RENAME_BUTTON}
            {DUPLICATE_BUTTON}
            {DELETE_BUTTON}
        </div>
        <div class="content">
            <div class="name">{NAME}</div>
            <!-- cron_schedule -->
            <div class="cron_schedule">{VALUE}</div>
            <!-- / -->
            <!-- if env -->
            <div class="envs">
                <!-- env -->
                <div class="env {CLASS}">{ID}</div>
                <!-- / -->
            </div>
            <!-- / -->
            <!-- async -->
            <div class="async">&</div>
            <!-- / -->
        </div>
        <div class="cb"></div>
    </div>
    <div class="cb"></div>

    <!-- settings -->
    <div class="settings">
        <div class="path">
            {PATH}
        </div>
        <div class="data">
            {DATA}
        </div>
        <div class="inputs">
            {INPUTS}
        </div>
        <div class="buttons">
            {REQUIRE_CONFIRMATION_TOGGLE_BUTTON}
            {CRON_TOGGLE_BUTTON}
            {ENVS_FILTER_TOGGLE_BUTTON}
            {ASYNC_TOGGLE_BUTTON}
            <div class="cb"></div>
        </div>
        <!-- settings/cron -->
        <div class="cron">
            {SCHEDULE}
        </div>
        <!-- / -->
        <!-- settings/envs -->
        <div class="envs">
            {LIST}
        </div>
        <!-- / -->
        <!-- settings/async -->
        <div class="async">
            {TTL}
        </div>
        <!-- / -->
        <div class="cb"></div>
    </div>
    <!-- / -->

</div>
