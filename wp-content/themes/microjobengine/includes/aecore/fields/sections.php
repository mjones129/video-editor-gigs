<?php


/**
 * Class AE_section
 * create a section in admin
 * @author Dakachi
*/
Class AE_section {
	/**
     * Field Constructor.
     *
     * @param array $params
     * - html tag
     * - id
     * - name
     * - placeholder
     * - readonly
     * - class
     * - title
     * @param $groups
     * @param $parent
     * @since AEFramework 1.0.0
    */
    function __construct( $params = array(), $groups = array() , $parent = array() ) {
        $this->parent = $parent;
        $this->field = $params;
        $temp = array ();
        $i = 1;
        foreach ($groups as $key => $group) {
            if(!empty($group) || $group != null) {
                if( !isset($group['type']) || $group['type'] == '' )
                    $group['type'] = 'group';

                $type = 'AE_'.$group['type'];

                if( isset( $group['args']['title'] ) ) {
                    $group['args']['title'] = $i . ". " . $group['args']['title'];
                    $i++;
                }

                if($group['type'] == 'list') {
                    $temp[] = new AE_list( $group['args'] , $group['fields'] , array() );
                }else {
                    $temp[] = new $type( $group['args'] , $group['fields'] , $parent );
                }
            }
        }
        $this->groups = $temp;
    }

    function render( $first  =   false) {
        $groups =   $this->groups;
        /**
         * show the first section
         */
        $display   =    '';
        if(!$first) {
            $display    =   'style="display:none"';
        }
        echo '<div '. $display .' class="et-main-main clearfix inner-content '. $this->field['class'] .'" id="'. $this->field['id'] .'" >';

        if(is_array($groups)) {
            /**
             * render group menus
            */
            foreach ( $groups as $key => $group ) {
                $group->render();
            }

        } else {
            $groups->render ();
        }

        echo '</div>';
    }

    function render_menu ( $first =  false ) {
        $class= '';
        $field = $this->field;
        if($first) $class= 'active';
        if( isset( $field['title'] )) {
            if(isset($field['type']) && $field['type'] == 'link') {
                echo "<li><a href='".$field["link"]."' target='_black'>".$field['title']."</a></li>";
            } else {
                echo '<li>
                <a href="#section/'. $this->field['id'] .'" menu-data="" class="'. $class .' section-link">
                    <span class="icon" data-icon="'. $this->field['icon'] .'"></span>'. $this->field['title'] .
                    '</a>
                </li>';
            }
        }
    }

}


