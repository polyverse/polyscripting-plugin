<div name="button-dib" class="inline">
<?php
$state = 'off';
if ( isset( $_POST['polyscript-rescramble'] ) ) {
    Polyscript::shift_state('scrambling');
}
if ( isset( $_POST['polyscript-disable'] ) ) {
    Polyscript::shift_state('disabling');
}

if ($state === 'on') { ?>
    <div id="status-label"><strong>&#10004 Polyscripting Enabled</strong></div><?php
} else if ( $state === 'off' ) {?>
    <div class="centered" id="status-label"><strong>&#10007 Polyscripting Disabled</strong></div><?php
} else { ?> <div id="status" class="loader"></div> <?php
}
?>
<form>
<?php if ($state === 'on') {
    ?>
    <button class="submit" title="Generate new PHP syntax."><input class="ps-button ps-rescramble"
                                                                   name="polyscript-rescramble" id="rescramble"
                                                                   value="<?php _e('Rescramble', 'polyscript-settings'); ?>">
    </button>
    <input disabled class="ps-button ps-unscramble" name="" id="unscramble"
           value="<?php _e('Unscramble', 'polyscript-settings'); ?>">
    <?php
} else if ($state === 'scrambling') {
    ?>
    <button disabled class="submit" title="Generating New Scramble."><input
                class="ps-button ps-rescramble ps-disabled" type="submit"
                name="" id="rescramble"
                value="<?php _e('Generating', 'polyscript-settings'); ?>">
    </button>
    <button disabled class="submit" title="Polyscripting not enabled."><input disabled
                class="ps-button ps-unscramble ps-disabled"
                type="submit" name="" id="unscramble"
                value="<?php _e('Unscramble', 'polyscript-settings'); ?>">
    </button>
    <?php
} else {
    ?>
    <button class="submit" title="Generate new PHP syntax."><input class="ps-button ps-rescramble"
                                                                   type="submit"
                                                                   name="polyscript-rescramble" id="rescramble"
                                                                   value="<?php _e('Scramble', 'polyscript-settings'); ?>">
    </button>
    <button class="submit" title="Polyscripting not enabled."><input
                class="ps-button ps-unscramble ps-disabled"
                type="submit" name="polyscript-disable" id="unscramble"
                value="<?php _e('Unscramble', 'polyscript-settings'); ?>">
    </button>
<?php } ?>
</form>
</div>
