<?php
 
include_once("./Services/UIComponent/classes/class.ilUserInterfaceHookPlugin.php");
 
/**
 * ilCreventoLogviewerPlugin plugin
 *
 * @author Simon Moor <simon.moor@hslu.ch>
 * @version $Id$
 *
 */
class ilCreventoLogviewerPlugin extends ilUserInterfaceHookPlugin
{
    /** @var $instance ilCreventoLogviewerPlugin */
    protected static $instance;
    
    function getPluginName()
    {
            return "CreventoLogviewer";
    }
    
    /**
     * @return ilCreventoLogviewerPlugin
     */
    public static function getInstance() {
        if (!isset(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
 
?>