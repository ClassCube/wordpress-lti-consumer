<?php

namespace ClassCube;

if ( !class_exists( '\WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class LTI_Table extends \WP_List_Table {

    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items() {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $data = $this->table_data();
        usort( $data, array( &$this, 'sort_data' ) );
        $perPage = 10;
        $currentPage = $this->get_pagenum();
        $totalItems = count( $data );
        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page' => $perPage
        ) );
        $data = array_slice( $data, (($currentPage - 1) * $perPage ), $perPage );
        $this->_column_headers = array( $columns, $hidden, $sortable );
        $this->items = $data;
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns() {
        $columns = array(
            'name' => __( 'Name', 'cc-lti' ),
            'url' => __( 'Base URL', 'cc-lti' )
        );
        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns() {
        return array();
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns() {
        return array( 'title' => array( 'title', false ) );
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data() {
        $data = get_option( 'classcube-lti-tools', [ ] );

        return $data;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'name':
                return '<a href="' . admin_url( 'admin.php?page=cc-lti&view=' . $item[ 'id' ] ) . '">' . $item[ 'name' ] . '</a>';
            case 'url':
                return $item[ 'base_url' ];
            default:
                return print_r( $item, true );
        }
    }

    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data( $a, $b ) {
        // Set defaults
        $orderby = 'name';
        $order = 'asc';
        // If orderby is set, use this as the sort column
        if ( !empty( $_GET[ 'orderby' ] ) ) {
            $orderby = $_GET[ 'orderby' ];
        }
        // If order is set use this as the order
        if ( !empty( $_GET[ 'order' ] ) ) {
            $order = $_GET[ 'order' ];
        }
        $result = strcmp( $a[ $orderby ], $b[ $orderby ] );
        if ( $order === 'asc' ) {
            return $result;
        }
        return -$result;
    }

}
