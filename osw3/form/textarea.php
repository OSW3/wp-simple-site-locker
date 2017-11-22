<?php

if (!class_exists('OSW3_TextareaType'))
{
    class OSW3_TextareaType extends OSW3_FormType
    {
        // public function render($namespace, $showLabel = true, $wrapper = true, $before = null, $after = null)
        public function render()
        {
            return $this->renderField([
                $this->getAttrName(),
                $this->getAttrId(),
                $this->getAttrClass(),
                $this->getAttrPlaceholder(),
                $this->getAttrCols(),
                $this->getAttrRows(),
                $this->getAttrRequired(),
                $this->getAttrDisabled(),
                $this->getAttrReadonly()
            ]);
            // return $this->renderField("textarea", $showLabel, $wrapper, [
            //     $this->getAttrName($namespace),
            //     $this->getAttrId($namespace),
            //     $this->getAttrClass(),
            //     $this->getAttrPlaceholder(),
            //     $this->getAttrCols(),
            //     $this->getAttrRows(),
            //     $this->getAttrRequired(),
            //     $this->getAttrDisabled(),
            //     $this->getAttrReadonly()
            // ], $before, $after, $this->getValue());
        }
    }
}
