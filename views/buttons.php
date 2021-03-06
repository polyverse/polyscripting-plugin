<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>


<div name="button-dib" class="inline">
    <?php
    if (isset($_POST['polyscript-rescramble'])) {
        check_admin_referer('polyscript-admin');
        PolyscriptingState::shift_state('rescrambling'); ?>
        <script>window.location.reload()</script>
        <div>Scrambling...</div>
        <?php
    }
    if (isset($_POST['polyscript-scramble'])) {
        check_admin_referer('polyscript-admin');
        PolyscriptingState::shift_state('scrambling'); ?>
        <script>window.location.reload()</script>
        <div>Scrambling...</div>
        <?php
    }
    if (isset($_POST['polyscript-disable'])) {
        check_admin_referer('polyscript-admin');
        PolyscriptingState::shift_state('disabling');
        ?>
        <script>window.location.reload()</script>
        <div>Disabling...</div>
    <?php }
    if ($state === 'on') { ?>
        <div id="status-label"><strong>&#10004 Polyscripting Enabled</strong></div><?php
    } else if ($state === 'off') { ?>
        <div class="centered" id="status-label"><strong>&#10007 Polyscripting Disabled</strong></div><?php
    } else if ($state === 'disabling') { ?>
        <div id="status" class="loader"></div>
        <div>Disabling Polyscripting...</div>
    <?php } else if ($state === 'scrambling' || $state === 'rescrambling') { ?>
        <div id="status" class="loader"></div>
        <div>Scrambling...</div>
    <?php } ?>
    <form action="" method="post" id="polyscript-settings">
        <?php wp_nonce_field('polyscript-admin');
        if ($state === 'on') { ?>
            <button class="submit" title="Generate new PHP syntax.">
                <input class="ps-button ps-rescramble"
                       type="submit"
                       name="polyscript-rescramble"
                       id="rescramble"
                       value="<?php esc_attr_e('Rescramble', 'polyscript-settings'); ?>">
            </button>
            <button class="submit" title="Disable Polyscripting.">
                <input
                    class="ps-button ps-unscramble"
                    type="submit"
                    name="polyscript-disable"
                    id="unscramble"
                    value="<?php esc_attr_e('Unscramble', 'polyscript-settings'); ?>">
            </button>
            <?php
        } else if ($state === 'off') { ?>
            <button disabled class="submit" title="Generating New Scramble.">
                <input
                    class="ps-button ps-rescramble"
                    type="submit"
                    name="polyscript-scramble"
                    id="scramble"
                    value="<?php esc_attr_e('Scramble', 'polyscript-settings'); ?>">
            </button>
            <button disabled class="submit" title="Polyscripting not enabled.">
                <input disabled
                       class="ps-button ps-unscramble ps-disabled"
                       type="submit" name=""
                       id="unscramble"
                       value="<?php esc_attr_e('Unscramble', 'polyscript-settings'); ?>">
            </button>
        <?php } ?>
    </form>
</div>
