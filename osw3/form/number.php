<?php

if (!class_exists('OSW3_NumberType'))
{
    class OSW3_NumberType extends OSW3_FormType
    {
        // public function render($namespace, $showLabel = true, $wrapper = true, $before = null, $after = null)
        public function render()
        {
            return $this->renderField([
                $this->getAttrType(),
                $this->getAttrName(),
                $this->getAttrId(),
                $this->getAttrClass(),
                $this->getAttrValue(),
                $this->getAttrPlaceholder(),
                $this->getAttrRequired(),
                $this->getAttrDisabled(),
                $this->getAttrStep(),
                $this->getAttrReadonly()
            ]);
            // return $this->renderField("number", $showLabel, $wrapper, [
            //     $this->getAttrType(),
            //     $this->getAttrName($namespace),
            //     $this->getAttrId($namespace),
            //     $this->getAttrClass(),
            //     $this->getAttrValue(),
            //     $this->getAttrPlaceholder(),
            //     $this->getAttrRequired(),
            //     $this->getAttrDisabled(),
            //     $this->getAttrStep(),
            //     $this->getAttrReadonly()
            // ], $before, $after);
        }
    }
}
