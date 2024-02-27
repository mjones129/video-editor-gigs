<?php
/**
 * class AE_translator render lanaguage list and field to translate it,
 * this class require class AE_Language to work
 * @author Dakachi
 * @version 1.0
*/
class AE_translator {

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
        $language   =   AE_Language::get_instance();
        $langArr    =   $language->get_language_list();
    ?>

		<div class="clearfix">
			<div class="chose-language" >
				<select id="base-language">
					<option class="empty" value=""><?php _e('Choose a Language', 'enginethemes') ?></option>
					<?php foreach ($langArr as $value) {?>
						<option value="<?php echo $value?>"><?php echo $value ?></option>
					<?php }?>
				</select>
			</div>

			<div class="btn-language">
				<button id="save-language"><?php _e('Save', 'enginethemes') ?></button>
			</div>
		</div>
    <?php
    }//render
}


class AE_translator_form extends AE_translator
{
	function render() {
		?>
		<div class="translate-area">
			<div id="translate-form">

			</div>
			<div id="pager"></div>
		</div>
		<?php
	}
}
