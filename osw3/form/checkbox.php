<?php

if (!class_exists('OSW3_CheckboxType'))
{
    class OSW3_CheckboxType extends OSW3_FormType
    {
        // public function render($namespace, $showLabel = true, $wrapper = true, $before = null, $after = null)
        public function render()
        {
            // echo "<hr>";
            // var_dump($this->getChecked());
            // echo "<hr>";
            // var_dump($this->getAttrChecked());
            // echo "<hr>";
            // // var_dump( $this->getAttrChecked() );

            return $this->renderField([
                $this->getAttrType(),
                $this->getAttrName(),
                $this->getAttrId(),
                $this->getAttrClass(),
                $this->getAttrValue(),
                $this->getAttrChecked(),
                $this->getAttrRequired(),
                $this->getAttrDisabled(),
                $this->getAttrReadonly()
            ]);
            
                        // return $this->renderField("checkbox", $showLabel, $wrapper, [
                        //     $this->getAttrType(),
                        //     $this->getAttrName($namespace),
                        //     $this->getAttrId($namespace),
                        //     $this->getAttrClass(),
                        //     $this->getAttrValue(),
                        //     $this->getAttrChecked(),
                        //     $this->getAttrRequired(),
                        //     $this->getAttrDisabled(),
                        //     $this->getAttrReadonly()
                        // ], $before, $after);
        }
    }
}
