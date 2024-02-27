<?php
class MJE_Post_Factory extends AE_PostFact
{
    public function __construct()
    {
        parent::__construct();
        self::$objects = array(
            'post' => MJE_Post::get_instance()
        );
    }
}

/**
 * set a global object factory
 */
global $ae_post_factory;
$ae_post_factory = new MJE_Post_Factory();
$ae_post_factory->set( 'post', new MJE_Post( 'post' ) );