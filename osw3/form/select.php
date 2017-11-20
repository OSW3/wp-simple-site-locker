<?php

if (!class_exists('OSW3_V1_SelectType'))
{
    class OSW3_V1_SelectType extends OSW3_V1_FormType
    {
        public function render($namespace, $showLabel = true, $wrapper = true, $before = null, $after = null)
        {
            return $this->renderField("select", $showLabel, $wrapper, [
                $this->getAttrName($namespace),
                $this->getAttrId($namespace),
                $this->getAttrClass(),
                $this->getAttrPlaceholder(),
                $this->getAttrMultiple(),
                $this->getAttrRequired(),
                $this->getAttrDisabled(),
                $this->getAttrReadonly()
            ], $before, $after);
        }
    }
}
