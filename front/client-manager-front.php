<?php

/**
 * Author: Jaroslav Klimcik
 * Website: http://jerryklimcik.cz
 * Date: 8.12.2014
 */
class Client_Manager_Front {

    protected $version;

    public function __construct($version) {
        $this->version = $version;
    }

    public function enqueue_styles() {
        wp_enqueue_style(
            'client-manager-front',
            plugin_dir_url(__FILE__) . 'css/front-style.css',
            array(),
            $this->version,
            FALSE
        );
    }

    public function add_content_to_the_front() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'klient';

        $pagenum = isset($_GET['pagenum']) ? absint($_GET['pagenum']) : 1;
        $limit = 2;
        $offset = ($pagenum - 1) * $limit;
        $sort = isset($_GET['sort']) ? $_GET['sort'] : "id";
        $order = isset($_GET['order']) ? $_GET['order'] : "ASC";
        if (isset($_GET['search'])) {
            $search = $_GET['search'];
            $entries = $wpdb->get_results("SELECT * FROM $table_name WHERE MATCH(client_name, client_company) AGAINST('$search*' IN BOOLEAN MODE) ORDER BY $sort $order LIMIT $offset, $limit");
        } else {
            $entries = $wpdb->get_results("SELECT * FROM $table_name ORDER BY $sort $order LIMIT $offset, $limit");
        }

        $content = '
        <div class="wrap">
            <form action="" method="get">
                <fieldset id="vyhledat">
                    <legend>VYHLEDÁVÁNÍ</legend>
                    <input type="text" id="search" name="search" value="" placeholder="Zadejte příjmení nebo název firmy"/>
                    <input class="button" type="submit" value="vyhledat &raquo;"/>
                </fieldset>
            </form>
            <table class="widefat">
            <thead>
            <tr>
                <th scope="col" class="manage-column column-name" style="">Jméno a příjmení <a href="?sort=client_name&order=asc">&#x25B2;</a> <a href="?sort=client_name&order=desc">&#x25BC;</a></th>
                <th scope="col" class="manage-column column-name" style="">Pozice</th>
                <th scope="col" class="manage-column column-name" style="">Firma <a href="?sort=client_company&order=asc">&#x25B2;</a> <a href="?sort=client_company&order=desc">&#x25BC;</a></th>
                <th scope="col" class="manage-column column-name" style="">Telefon</th>
                <th scope="col" class="manage-column column-name" style="">Email</th>
                <th scope="col" class="manage-column column-name" style="">Klient vytvořen <a href="?sort=created&order=asc">&#x25B2;</a> <a href="?sort=created&order=desc">&#x25BC;</a></th>
                <th scope="col" class="manage-column column-name" style="">Klient aktualizován <a href="?sort=updated&order=asc">&#x25B2;</a> <a href="?sort=updated&order=desc">&#x25BC;</a></th>
            </tr>
            </thead>


            <tbody>
            <?php
            ';
        if ($entries) {
            ?>

            <?php
            foreach ($entries as $entry) {
                $content .= '
                    <tr>
                        <td> ' . $entry->client_name . ' </td>
                        <td>' . $entry->client_position . '</td>
                        <td>' . $entry->client_company . '</td>
                        <td>' . $entry->client_phone . '</td>
                        <td>' . $entry->client_email . '</td>
                        <td>' . $entry->created . '</td>
                        <td>' . $entry->updated . '</td>
                    </tr>

                    <?php
                    ';
            }
            ?>

        <?php
        } else {
            $content .= '
                <tr>
                    <td colspan="7">Nebyly nalezeny žádné záznamy</td>
                </tr>
            <?php ';
        }
        $content .= '
            </tbody>
        </table>

        <?
        ';
        if (isset($_GET['search'])) {
            $search = $_GET['search'];
            $total = $wpdb->get_var("SELECT COUNT(`id`) FROM $table_name WHERE MATCH(client_name, client_company) AGAINST('$search*' IN BOOLEAN MODE)");
        } else {
            $total = $wpdb->get_var("SELECT COUNT(`id`) FROM $table_name");
        }
        $num_of_pages = ceil($total / $limit);
        $page_links = paginate_links(array(
            'base' => add_query_arg('pagenum', '%#%'),
            'format' => '',
            'prev_text' => __('&laquo;', 'aag'),
            'next_text' => __('&raquo;', 'aag'),
            'total' => $num_of_pages,
            'current' => $pagenum
        ));

        if ($page_links) {
            $content .= '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
        }

        $content .= '</div>';

        // Returns the content.
        return $content;
    }
}