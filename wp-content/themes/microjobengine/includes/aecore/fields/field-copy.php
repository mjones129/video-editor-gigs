<?php
class AE_copy {

    /**
     * Field Constructor.
     *
     * @param array $field
     * - id
     * - name
     * - placeholder
     * - readonly
     * - class
     * - title
     * @param $value
     * @param $parent
     * @since AEFramework 1.0.0
    */
    function __construct( $field = array(), $value ='', $parent = array() ) {

        //parent::__construct( $parent->sections, $parent->args );
        $this->parent = $parent;
        $this->field = $field;
        $this->value = $value;

    }

    /**
     * Field Render Function.
     *
     * Takes the vars and outputs the HTML for the field in the settings
     *
     * @since AEFramework 1.0.0
    */
    function render() {

        $readonly = isset($this->field['readonly']) ? ' readonly="readonly"' : '';
        $placeholder = (isset($this->field['placeholder']) && !is_array($this->field['placeholder'])) ? ' placeholder="' . esc_attr($this->field['placeholder']) . '" ' : '';

        $success = isset($this->field['success'])? $this->field['success'] :'Field coppied to clipboard';
        $error  = isset($this->field['error'])? $this->field['error'] :'Unable to copy this field.';
        $default = isset($this->field['default']) ? $this->field['default'] : '';
        $value = ($this->field['value']) ? (esc_attr($this->field['value']) ) : $default;

        if( isset( $this->field['label']) && $this->field['label'] !== '') {
            echo '<label for="'. $this->field['id'] .'">'. $this->field['label'] .'</label>';
        }
        echo '<input type="text" success="'.$success.'"  id="' . $this->field['id'] . '" name="' . $this->field['name'] .'" ' . $placeholder . '" value="' . $value . '" class="js-copy-field ' . $this->field['class'] . '" '.$readonly.'  />';

    } //render

}
