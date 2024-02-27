<?php
/**
 * class AE_list
 * render list option and form to control list
 * @version 1.0
 * @package AE
 * @author Dakachi
*/
class AE_list {

	public $item;
	public $data;
	/**
	 * contruct a list settings in backend
	 * @param array $args :
	  - name : required  option name
	  - id
	  - title
	  - form param array contain form args
	 * @param $template
	 	- template the item template path  (php render)
	 	- js_template js item template path (for js app)
	 	- form The form use to submit an item (php render)
	 	- form_js The js form template use to edit an item  (for js app)
	 * @param $data  pack list data
	 * @package AE
	 * @version 1.0
	*/
	function __construct( $args = array(), $template = array() , $data = array()   ){

		$this->data		=	$data;
		$this->params	=	$args;


		if( (!isset($template['fullpath']) || !$template['fullpath'] ) && !empty( $template ) ){
			$this->template = locate_template ($template['template'] );
			$this->js_template = locate_template ($template['js_template']);
			$this->form_template	=	locate_template ($template['form']);
			$this->form_js_template	=	locate_template ($template['form_js']);
		}
		else if( !empty( $template ) && isset($template['fullpath']) && $template['fullpath']  ){
			$this->template = $template['template'] ;
			$this->js_template = $template['js_template'];
			$this->form_template	=	$template['form'];
			$this->form_js_template	=	$template['form_js'];
		}
		else {
			$this->template	=	ae_get_path().'/template/post-item.php';
			$this->js_template	=	ae_get_path().'/template-js/post-item.php';
			$this->form_template	=	ae_get_path().'/template/add-pack-form.php';
			$this->form_js_template	=	ae_get_path().'/template-js/add-pack-form.php';
		}
	} // construct

	/**
	 * render html and template
	*/
	function render() {
		global $ae_post_factory;
		if($this->params['name'] == 'payment_package') {
			$ae_pack = $ae_post_factory->get('pack');
		}else{
			$ae_pack = $ae_post_factory->get($this->params['name']);
		}

		if(!$ae_pack) return ;
        $packs = $ae_pack->fetch($this->params['name']);
        $this->data = $packs;

		echo '<div id="group-'. $this->params['id'] .'-wrapper" class="group-wrapper">';
            echo '<div class="title group-title group-'. $this->params['id'] .'">'. $this->params['title'] .'</div>';
            echo '<div class="group-fields">';
            echo '<div class="desc pack-control " id="control-'. $this->params['name'] .'" data-option-name="'. $this->params['name'] .'" data-template="'. $this->params['name'] .'">';
            echo'
                        <ul class="name-list">
                            <li class="col-md-1 col-sm-1">'. __('SKU', 'enginethemes') .'</li>
                            <li class="col-md-3 col-sm-3">'. __('Package name', 'enginethemes') .'</li>
                            <li class="col-md-2 col-sm-2">'. __('Price', 'enginethemes') .'</li>
                            <li class="col-md-2 col-sm-2">'. __('Duration', 'enginethemes') .'</li>
                            <li class="col-md-2 col-sm-2">'. __('Number of mJob can post', 'enginethemes') .'</li>
                            <li class="col-md-1 col-sm-1"></li>
                        </ul>
                        <div class="clearfix"></div>
                    ';
            echo '<ul class="pay-plans-list sortable" >';
            if( !empty($this->data) ) {
                foreach ($this->data as $key => $item) {
                    $this->item	=	$item;
                    include ( $this->template );
                }
            }
            echo '</ul>';

            echo '<div class="item">';
                load_template( $this->form_template );
            echo '</div>';

            echo '</div>'; // end .pack-control
            echo '</div>'; // end .group-field

            ?>
            <input id="confirm_delete_<?php echo $this->params['name']; ?>" value="<?php _e("Are you sure you want to delete this?", 'enginethemes'); ?>" type="hidden" />

            <!-- edit item form template -->
            <?php load_template( $this->form_js_template ); ?>

            <!-- json data for pack view -->
            <script id="ae_list_<?php echo $this->params['name'];  ?>">
                <?php echo json_encode( $this->data ); ?>
            </script>
            <!-- js template for item view -->
            <script type="text/template" id="ae-template-<?php echo  $this->params['name'];  ?>">
                <?php load_template( $this->js_template ); ?>
            </script>
            <?php
        echo '</div>'; // end .group-wrapper
	}
}