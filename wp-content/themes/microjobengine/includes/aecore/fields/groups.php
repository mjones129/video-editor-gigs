<?php
/**
 * Class AE_Group
 * create a group of input element
 * @author Dakachi
*/
class AE_group {
    /**
     * Field Constructor.
     *
     * @param array $params
     * - html tag
     * - id
     * - name
     * - class
     * - title
     * - desc
     * @param $field
     * @param $parent
     * @since AEFramework 1.0.0
    */
    function __construct( $params = array(), $fields = array(), $parent = array() ) {
        $this->parent = $parent;
        $this->params = $params;

        $temp = array ();

        $group_name = isset( $this->params['name'] ) ?  $this->params['name'] : '' ;

        foreach ( $fields as $key => $field) {
            $type = 'AE_'.$field['type'];

            $name = $field['name'];
            if( $group_name == '' ) {
                $value  =   $parent->$name;
            }
            else {
                $value = $parent->$group_name;
                if(is_array($value)) {
                    if (isset($value[$name])) {
                        $value = $value[$name];
                    } else {
                        $value = '';
                    }
                }
            }

            $temp[] = new AE_BackendField( new $type( $field, $value, $parent ), $field );
        }

        $this->fields = $temp;
    }

    /**
     * render group html
     * @author Dakachi
     */
    function render() {
        $fields =   $this->fields;

        echo '<div class="' . $this->params['class'] .' group-wrapper">';
            // Render group title
            echo '<div class="title group-title">';
            echo$this->params['title'];

            // Render group description
            if( !empty( $this->params['desc'] ) ):
                echo '<span class="group-desc">';
                echo $this->params['desc'];
                echo '</span>';
            endif;
            echo '</div>';

            // Render group fields
            echo '<div class="group-fields clearfix">';
            foreach ($fields as $key => $field) {
                $field->render();
            }
            echo '</div>'; // end .group-fields
        echo '</div>'; // end .group-wrapper
    }
}


/**
 * class adapt field to compatible with backend settings
 * @since 1.0
 * @author Dakachi
*/
class AE_BackendField {
    public $field;
    public $field_data;
    function __construct ($field, $field_data) {
        $this->field = $field;
        $this->field_data = $field_data;
    }

    function render() {
        $field_id = $this->field->field['id'];
        $field_wrapper_id = 'field-' . $field_id;
        $field_class = isset( $this->field_data['class'] ) ? $this->field_data['class'] . '-item' : '';
        $restrict_field_ids = apply_filters('ae_render_field_out_of_column', array());

        if( $this->field_data['type'] == 'combine' && !empty( $this->field_data['name'] ) ) {
            $data_name = 'data-name=' . $this->field_data['name'];
        } else {
            $data_name = '';
        }

        if(in_array($field_id, $restrict_field_ids)) {
            echo '<div id="'. $field_wrapper_id .'" class="'. $field_class .' field-item clearfix">';
            $this->field->render();
            echo '</div>';
        } else {
            echo '<div id="'. $field_wrapper_id .'" class="'. $field_class .' field-item clearfix">';
                // field description
                echo '<div class="field-desc col-lg-5 col-md-12 col-sm-12 col-xs-12">';
                    if( isset( $this->field_data['toggle']) ) {
                        echo "<p class='toggle-desc'>{$this->field_data['title']} <i class='fa fa-plus-square'></i></p>";
                    } else {
                        echo "<p>{$this->field_data['title']}</p>";
                    }
                    echo "<span>{$this->field_data['desc']}</span>";
                echo '</div>';

                // field content
                $display_none = isset( $this->field_data['toggle'] ) ? 'style="display: none;"' : '';
                echo '<div ' . $display_none . ' class="field-content form no-margin no-padding no-background col-lg-7 col-md-12 col-sm-12 col-xs-12"><div class="form-item">';
                    echo '<form '. $data_name .'>';
                    $this->field->render();
                    echo '</form>';
                echo '</div></div>';
            echo '</div>';
        }
    }
}