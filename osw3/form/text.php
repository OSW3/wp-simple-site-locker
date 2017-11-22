<?php

if (!class_exists('OSW3_TextType'))
{
    class OSW3_TextType extends OSW3_FormType
    {
        // public function render($namespace, $showLabel = true, $wrapper = true, $before = null, $after = null)
        // public function render( $attrNameAsArray=false, $showLabel = true, $wrapper = true, $before = null, $after = null)
        public function render()
        {
            // echo "<hr>";
            // var_dump($this->getValue());
            // echo "<hr>";
            // var_dump($this->getAttrValue());
            // echo "<hr>";
            // // var_dump($this->attributes);

            return $this->renderField([
                $this->getAttrType(),
                $this->getAttrName(),
                $this->getAttrId(),
                $this->getAttrClass(),
                $this->getAttrValue(),
                $this->getAttrPlaceholder(),
                $this->getAttrRequired(),
                $this->getAttrDisabled(),
                $this->getAttrReadonly()
            ]);
            
                        // return $this->renderField("text", $showLabel, $wrapper, [
                        //     $this->getAttrType(),
                        //     $this->getAttrName(),
                        //     $this->getAttrId(),
            
                        //     $this->getAttrClass(),
                        //     $this->getAttrValue(),
                        //     $this->getAttrPlaceholder(),
                        //     $this->getAttrRequired(),
                        //     $this->getAttrDisabled(),
                        //     $this->getAttrReadonly()
                        // ], $before, $after);
        }
    }
}
