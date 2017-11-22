<?php

if (!class_exists('OSW3_ChoicesType'))
{
    class OSW3_ChoicesType extends OSW3_FormType
    {
        // public function render($namespace, $showLabel = true, $wrapper = true, $before = null, $after = null)
        public function render()
        {
            // echo "<hr>";
            // var_dump($this->getValue());
            // echo "<hr>";
            // var_dump($this->getHtmlOptions());
            // echo "<hr>";
            // var_dump( $this->getValue() );

            return $this->renderField([
                $this->getAttrName(),
                $this->getAttrId(),
                $this->getAttrClass(),
                $this->getAttrPlaceholder(),
                $this->getAttrMultiple(),
                $this->getAttrRequired(),
                $this->getAttrDisabled(),
                $this->getAttrReadonly()
            ]);
            // return $this->renderField("select", $showLabel, $wrapper, [
            //     $this->getAttrName($namespace),
            //     $this->getAttrId($namespace),
            //     $this->getAttrClass(),
            //     $this->getAttrPlaceholder(),
            //     $this->getAttrMultiple(),
            //     $this->getAttrRequired(),
            //     $this->getAttrDisabled(),
            //     $this->getAttrReadonly()
            // ], $before, $after);
        }
    }
}
