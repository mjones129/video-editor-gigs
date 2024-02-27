<?php
class AE_combine
{
    public $children;
    public $field;
    public $options;
    public $value;
    public function __construct( $field = array(), $value = '', $options = array() ) {
        $this->field = $field;
        $this->value = $value;
        $this->options = $options;
        $this->children = $field['children'];
    }

    public function render() {
        $parent_name = $this->field['name'];
        $parent_value = $this->options->$parent_name;

        foreach ( $this->children as $child ) {
            $child_name = $child['name'];
            $field_class = 'AE_' . $child['type'];
            if( isset( $parent_value[$child_name] ) ) {
                // If using data-name
                $value = $parent_value[$child_name];
            } elseif( isset( $this->options->$child_name ) ) {
                $value = $this->options->$child_name;
            } else {
                $value = '';
            }

            $field_obj = new $field_class( $child, $value, $this->options );
            $field_class = ! empty( $child['class'] ) ? 'field-combine-' . $child['class'] : '';

            echo '<div class="' . $field_class . ' field-combine-item clearfix">';
                echo '<div class="field-desc">';
                    echo isset( $child['title'] ) ? '<p>' . $child['title'] . '</p>' : '';
                    echo isset( $child['desc'] ) ? '<span>' . $child['desc'] . '</span>' : '';
                echo '</div>';

                echo '<div class="field-content">';
                    $field_obj->render();
                echo '</div>';
            echo '</div>';
        }
    }
}