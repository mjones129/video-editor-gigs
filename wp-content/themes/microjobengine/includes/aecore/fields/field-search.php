<?php
class AE_search {

    /**
     * Field Constructor.
     *
     * @param array $field
     * - id
     * - name
     * - placeholder
     * - class
     * - title
     * @param $value
     * @param $parent
     * @since AEFramework 1.0.0
     */
    function __construct( $field = array() ) {
        $this->field = $field;
    }

    /**
     * Field Render Function.
     *
     * Takes the vars and outputs the HTML for the field in the settings
     *
     * @since AEFramework 1.0.0
     */
    function render() {

        $placeholder = (isset($this->field['placeholder']) && !is_array($this->field['placeholder'])) ? ' placeholder="' . esc_attr($this->field['placeholder']) . '" ' : '';

        $default = isset($this->field['default']) ? $this->field['default'] : '';

        if( isset( $this->field['label']) && $this->field['label'] !== '') {
            echo '<label for="'. $this->field['id'] .'">'. $this->field['label'] .'</label>';
        }
        echo '<input type="search" id="' . $this->field['id'] . '" name="' . $this->field['name'] .'" ' . $placeholder . '"  class=" ' . $this->field['class'] . '" /><br />';

    }//render

}
