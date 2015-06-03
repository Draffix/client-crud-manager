<?php

/**
 * Author: Jaroslav Klimcik
 * Website: http://jerryklimcik.cz
 * Date: 8.12.2014
 */
class Client_Manager_Admin {

    protected $version;

    public function __construct($version) {
        $this->version = $version;
    }

    public function enqueue_styles() {
        wp_enqueue_style(
            'client-manager-admin',
            plugin_dir_url(__FILE__) . 'css/admin-style.css',
            array(),
            $this->version,
            FALSE
        );
    }

    public function add_admin_menu_client_list() {
        //this is the main item for the menu
        add_menu_page('Klienti', //page title
            'Klienti', //menu title
            'manage_options', //capabilities
            'client_list', //menu slug
            client_list, //function
            plugin_dir_url(__FILE__) . 'img/icon.png' //URL to custom image used as icon
        );

        function client_list() {
            echo "<a href='" . admin_url('admin.php?page=client_create') . "'>Vytvořit nového klienta</a>";
            echo '<h2>Klienti</h2>';
            global $wpdb;

            $table_name = $wpdb->prefix . 'klient';
            $rows = $wpdb->get_results("SELECT id, client_name, client_position, client_company, client_phone, client_email, created, updated from $table_name");
            echo "<table class='wp-list-table widefat fixed'>";
            echo "<tr>
                    <th>Jméno</th>
                    <th>Pozice</th>
                    <th>Firma</th>
                    <th>Telefon</th>
                    <th>Email</th>
                    <th>Vytvořen</th>
                    <th>Aktualizace záznamu</th>
                    <th>&nbsp;</th>
                  </tr>";
            foreach ($rows as $row) {
                echo "<tr>";
                echo "<td>$row->client_name</td>";
                echo "<td>$row->client_position</td>";
                echo "<td>$row->client_company</td>";
                echo "<td>$row->client_phone</td>";
                echo "<td>$row->client_email</td>";
                echo "<td>$row->created</td>";
                echo "<td>$row->updated</td>";
                echo "<td><a href='" . admin_url('admin.php?page=client_update&id=' . $row->id) . "'>Upravit</a></td>";
                echo "</tr>";
            }
            echo "</table>";
        }

    }

    public function add_admin_menu_client_create() {
        //this is a submenu
        add_submenu_page('client_list', //parent slug
            'Přidat nového klienta', //page title
            'Přidat klienta', //menu title
            'manage_options', //capability
            'client_create', //menu slug
            'client_create'); //function

        function client_create() {
            $name = $_POST["client_name"];
            $position = $_POST["client_position"];
            $company = $_POST["client_company"];
            $phone = $_POST["client_phone"];
            $email = $_POST["client_email"];
            //insert
            if (isset($_POST['insert'])) {
                global $wpdb;
                $table_name = $wpdb->prefix . 'klient';
                $wpdb->insert(
                    $table_name, //table
                    array(
                        'client_name' => $name,
                        'client_position' => $position,
                        'client_company' => $company,
                        'client_phone' => $phone,
                        'client_email' => $email,
                        'created' => date('Y-m-d H:i:s'),
                        'updated' => date('Y-m-d H:i:s')), //data
                    array('%s', '%s', '%s', '%s', '%s') //data format
                );
                $message = "Klient přidán";
            }
            ?>
            <div class="wrap">
                <h2>Přidat nového klienta</h2>
                <?php if (isset($message)): ?>
                    <div class="updated"><p><?php echo $message; ?></p></div><?php endif; ?>
                <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                    <table class='wp-list-table widefat fixed'>
                        <tr>
                            <th>Jméno a příjmení</th>
                            <td><input type="text" name="client_name" value="<?php echo $client_name; ?>"/></td>
                        </tr>
                        <tr>
                            <th>Pozice</th>
                            <td>
                                <select name="client_position">
                                    <option value="Manažer" selected="selected">Manažer</option>
                                    <option value="Pracovník">Pracovník</option>
                                    <option value="Kurýr">Kurýr</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>Firma</th>
                            <td>
                                <select name="client_company">
                                    <option value="IBM" selected="selected">IBM</option>
                                    <option value="Microsoft">Microsoft</option>
                                    <option value="Google">Google</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>Telefon</th>
                            <td><input type="text" name="client_phone" value="<?php echo $client_phone; ?>"/></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td><input type="text" name="client_email" value="<?php echo $client_email; ?>"/></td>
                        </tr>
                    </table>
                    <input type='submit' name="insert" value='Přidat klienta' class='button'>
                </form>
            </div>
        <?php
        }
    }

    public function add_admin_menu_client_update() {
        //this submenu is HIDDEN, however, we need to add it anyways
        add_submenu_page(null, //parent slug
            'Upravit klienta', //page title
            'Upravit klienta', //menu title
            'manage_options', //capability
            'client_update', //menu slug
            'client_update'); //function

        function client_update() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'klient';
            $id = $_GET["id"];
            $name = $_POST["client_name"];
            $position = $_POST["client_position"];
            $company = $_POST["client_company"];
            $phone = $_POST["client_phone"];
            $email = $_POST["client_email"];
            //update
            if (isset($_POST['update'])) {
                $wpdb->update(
                    $table_name, //table
                    array(
                        'client_name' => $name,
                        'client_position' => $position,
                        'client_company' => $company,
                        'client_phone' => $phone,
                        'client_email' => $email,
                        'updated' => date('Y-m-d H:i:s')), //data
                    array('id' => $id), //where
                    array('%s', '%s', '%s', '%s', '%s'), //data format
                    array('%s') //where format
                );
            } //delete
            else if (isset($_POST['delete'])) {
                $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id = %s", $id));
            } else {//selecting value to update
                $clients = $wpdb->get_results($wpdb->prepare("SELECT client_name, client_position, client_company, client_phone, client_email from $table_name where id=%s", $id));
                foreach ($clients as $c) {
                    $name = $c->client_name;
                    $position = $c->client_position;
                    $company = $c->client_company;
                    $phone = $c->client_phone;
                    $email = $c->client_email;
                }
            }
            ?>
            <div class="wrap">
                <h2>Úprava klienta</h2>

                <?php if ($_POST['delete']) { ?>
                    <div class="updated"><p>Klient smazán</p></div>
                    <a href="<?php echo admin_url('admin.php?page=client_list') ?>">&laquo; Zpátky na výpis klientů</a>

                <?php } else if ($_POST['update']) { ?>
                    <div class="updated"><p>Klient aktualizován</p></div>
                    <a href="<?php echo admin_url('admin.php?page=client_list') ?>">&laquo; Zpátky na výpis klientů</a>

                <?php } else { ?>
                    <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        <table class='wp-list-table widefat fixed'>
                            <tr>
                                <th>Jméno a přijmení</th>
                                <td><input type="text" name="client_name" value="<?php echo $name; ?>"/></td>
                            </tr>
                            <tr>
                                <th>Pozice</th>
                                <td><input type="text" name="client_position" value="<?php echo $position; ?>"/></td>
                            </tr>
                            <tr>
                                <th>Firma</th>
                                <td><input type="text" name="client_company" value="<?php echo $company; ?>"/></td>
                            </tr>
                            <tr>
                                <th>Telefon</th>
                                <td><input type="text" name="client_phone" value="<?php echo $phone; ?>"/></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td><input type="text" name="client_email" value="<?php echo $email; ?>"/></td>
                            </tr>
                        </table>
                        <input type='submit' name="update" value='Aktualizovat' class='button'> &nbsp;&nbsp;
                        <input type='submit' name="delete" value='Smazat' class='button'
                               onclick="return confirm('Opravdu chcete klienta smazat?')">
                    </form>
                <?php } ?>

            </div>
        <?php
        }
    }
}