<?php
include_once 'Services/UIComponent/Button/classes/class.ilButtonBase.php';
class ilCreventoBtnOpenModal extends ilButtonBase
{
    protected $data_target; // [string]
    
    public static function getInstance()
    {
        return new self(self::TYPE_LINK);
    }
    
    
    /**
     * Set target
     *
     * @param string $a_value
     */
    public function setDataTarget($a_value)
    {
        $this->data_target = trim($a_value);
    }
    
    /**
     * Get target
     *
     * @return string
     */
    public function getDataTarget()
    {
        return $this->data_target;
    }
    
    
    //
    // render
    //
    
    /**
     * Prepare caption for render
     *
     * @return string
     */
    protected function renderCaption()
    {
        return '&nbsp;'.$this->getCaption().'&nbsp;';
    }
    
    public function render()
    {
        $this->prepareRender();
        
        $attr = array();
        $attr["data-target"] = $this->getDataTarget();
        $attr["data-toggle"] = 'modal';
        
        return '<a'.$this->renderAttributes($attr).'>'.
                        $this->renderCaption().'</a>';
    }	
}