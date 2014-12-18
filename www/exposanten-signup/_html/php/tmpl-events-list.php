<?php $this->showMessage(); ?>
<fieldset>
    <legend><h2>New trade show</h2></legend>
    <form name="new-event-form" method="post">
        <div class="row">
            <input  type="text" value="" name="new_event_name"/>
            <input  type="submit" value="Create" style="margin-left: 46px;"/>
        </div>
    </form>
</fieldset>

<fieldset>
    <legend><h2>Trade show list</h2></legend>
    <form name="events-form" method="post">
        <div class="row">
            <label></label>
        <select size="<?php echo count($files)+1; ?>" name="selected_events_name" onchange="this.form.submit()">
            <?php
            foreach ($files as $file){
                $name = basename($file,".xml");
                if ($selected_event_name == $name) {
                    echo "<option selected>" . $name . "</option>";
                } else {
                    echo "<option>" . $name . "</option>";
                }
            }
            ?>
        </select>
            </div>
    </form>
</fieldset>