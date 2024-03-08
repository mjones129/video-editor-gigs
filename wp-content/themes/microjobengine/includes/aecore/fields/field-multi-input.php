<?php
class AE_multi_input{

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

        $data           = $this->field['data'];
        $selected_value = $this->value;
        if( !isset( $this->value ) || empty( $this->value ) ){
            if( isset($this->field['default']) &&  $this->field['default'] != '' ){
                $selected_value = $this->field['default'];
            }
        }
        $placeholder = (isset($this->field['placeholder']) && !is_array($this->field['placeholder'])) ? ' placeholder="' . esc_attr($this->field['placeholder']) . '" ' : '';

        $search     = (isset($this->field['search']) && $this->field['search'] )  ? 'search' : '';
        $multiple   = (isset($this->field['multiple']) && $this->field['multiple'])  ? " multiple = 'multiple' " : '';
        $field_id   = $this->field['id'];
        $name       = $this->field['name'];

        if( isset( $this->field['label']) && $this->field['label'] !== '') {
            echo '<label for="'. $this->field['id'] .'">'. $this->field['label'] .'</label>';
        }
        echo "<select id='{$field_id}' name='{$name}' {$placeholder} class='ui fluid semantic-ui dropdown {$search}  ' $multiple>";
        foreach ($data as $key => $value) {?>
            <option value="<?php echo $key ?>" <?php multi_selected( $selected_value, $key ); ?>>
                <?php echo $value ?>
            </option>
        <?php }
        echo '</select>';

    }
}
