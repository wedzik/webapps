<?php $this->showMessage(); ?>
<fieldset>
    <legend><h2>Trade show</h2></legend>
    <fieldset class="fieldset2">
        <legend><h2>List</h2></legend>
        <form name="events-form" method="post">
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
        </form>
    </fieldset>
    <fieldset class="fieldset2">
        <legend><h2>Create</h2></legend>
        <form name="new-event-form" method="post">
            <input  type="text" value="" name="new_event_name"/>
            <input  type="submit" value="Create" style="float: right"/>
        </form>
    </fieldset>
</fieldset>