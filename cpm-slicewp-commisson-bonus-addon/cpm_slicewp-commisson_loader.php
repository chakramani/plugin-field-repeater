<?php

if (!defined('ABSPATH')) {
    exit;
}
function cpm_slicewp_commission_addon_enqueue_script()
{
    wp_enqueue_script('cpm_custom_for_slicewp_script_admin', plugin_dir_url(__FILE__) . '/cpm_custom.js');
    wp_enqueue_style('cpm_custom_for_slicewp_css_admin', plugin_dir_url(__FILE__) . '/cpm_custom.css');
}
add_action('slicewp_enqueue_admin_scripts', 'cpm_slicewp_commission_addon_enqueue_script');


add_action("admin_menu", "cpm_slicewp_options_submenu");
if (!function_exists('cpm_slicewp_options_submenu')) {
    function cpm_slicewp_options_submenu()
    {
        add_submenu_page(
            'plugins.php',
            'SliceWP Commission Bonus',
            'SliceWP Addon Setting',
            'administrator',
            'slice-commission-bonus-addon',
            'cpm_slicewp_bonus_settings_page'
        );
    }
}

function cpm_slicewp_bonus_settings_page()
{
    if (isset($_POST['submit'])) {
        // Sanitize user input.
        if (!isset($_POST['amount']) && !isset($_POST['bonus'])) {
            return;
        }

        $amount[] = $_POST['amount'];
        $rate[] = $_POST['bonus'];

        $total_amount_save = update_option('_slicewp_total_amount', $amount);
        $commission_rate_save = update_option('_slicewp_commission_rate', $rate);
        if ($total_amount_save || $commission_rate_save) {
            echo "data are successfully saved.";
        } else {
            echo "Not submitted try again.";
        }
    } ?>

    <div class="slicewp-addon-main">
        <h3>Commission Bonus Rate:</h3>

        <div class="container" id="labels">
            <div class="label">Earn More Than</div>
            <div class="label">Bonus</div>
        </div>
        <?php
        // var_dump(get_option('_slicewp_total_amount'));
        $total_amount = get_option('_slicewp_total_amount');
        $total_commission = get_option('_slicewp_commission_rate');
        ?>
        <form method="POST" action="">
            <div id="inputs">
                <?php if ($total_amount) {
                    foreach ($total_amount[0] as $key => $amount) : $temp = $key; ?>
                        <div class="container row">
                            <div class="count"><?php echo ++$temp; ?></div>
                            <div class="input">
                                <input type="number" name="amount[]" id="" class="select-css" value="<?php echo !empty($amount) ? $amount : ''; ?>">
                            </div>
                            <div class="input">
                                <input type="number" class="bonus" value="<?php echo !empty($total_commission[0]) ? $total_commission[0][$key] : ''; ?>" name="bonus[]"> <span>USD</span>
                            </div>
                            <button class="remove">X</button>
                        </div>
                    <?php endforeach;
                } else { ?>
                    <div class="container row">
                        <div class="count">1</div>
                        <div class="input">
                            <input type="number" name="amount[]" id="" class="select-css" value="<?php echo !empty($amount) ? $amount : ''; ?>">
                        </div>
                        <div class="input">
                            <input type="number" class="bonus" value="" name="bonus[]"> <span>USD</span>
                        </div>
                        <button class="remove">X</button>
                    </div>
                <?php } ?>
            </div>
            <button id="addRow">add row</button>

            <input type="submit" class="submit" name="submit">
        </form>

    </div>

<?php
}


function bonus_time_intervals($schedules)
{
    // add a 'weekly' interval
    // $schedules['ten_seconds'] = array(
    //     'interval' => 10,
    //     'display' => __('Every Ten Seconds')
    // );
    // add a 'weekly' interval
    $schedules['weekly'] = array(
        'interval' => 604800,
        'display' => __('Once Weekly')
    );
    $schedules['monthly'] = array(
        'interval' => 2635200,
        'display' => __('Once a month')
    );
    return $schedules;
}
add_filter('cron_schedules', 'bonus_time_intervals');

add_action('slicewp_commission_bonus_update', 'update_commission_bonus');


// add_action('wp_footer', 'update_commission_bonus');
function update_commission_bonus()
{

    global $wpdb;
    $total_amount = get_option('_slicewp_total_amount')[0];
    $total_commission = get_option('_slicewp_commission_rate')[0];
    $slicewp_commission_table_name = $wpdb->prefix . "slicewp_commissions";
    $total = $wpdb->get_results("SELECT affiliate_id, sum(amount) from " . $slicewp_commission_table_name . " GROUP BY affiliate_id having sum(amount) > 100", ARRAY_A);
    if (!empty($total)) {
        foreach ($total as $val => $row) {
            // echo($row['affiliate_id']);
            $count = 0;
            foreach ($total_amount as $key => $total_amounts) {

                if ($row['sum(amount)'] > $total_amounts) {
                    if ($count > 0) {
                        // echo $row['affiliate_id']. '=>' .$total_commission[$key].'count => '.$count.' update '.$total_commission[$key-1] .'with'.$total_commission[$key] .'</br>';
                        $wpdb->get_results("UPDATE " . $slicewp_commission_table_name . " SET `amount` = " . $total_commission[$key] . " WHERE `id` = " . $wpdb->insert_id);
                    } else {
                        // echo $row['affiliate_id']. '=>' .$total_commission[$key].' count => '.$count.'</br>';
                        $wpdb->get_results("INSERT INTO " . $slicewp_commission_table_name . " (`affiliate_id`, `type`,`date_created`, `date_modified`, `status`, `origin`, `amount`, `currency`) VALUES (" . $row['affiliate_id'] . ", 'bonus','" . date("Y-m-d h:i:s") . "','" . date("Y-m-d h:i:s") . "'," . " 'unpaid', 'custom', " . $total_commission[$key] . ", 'USD')");
                    }
                    $count++;
                }
            }
        }
    }
}

// add_action('wp', 'activateMe');
// function activateMe()
// {
if (!wp_next_scheduled('slicewp_commission_bonus_update')) {
    wp_schedule_event(time(), 'monthly', 'slicewp_commission_bonus_update');
}
// }
