<?php

class Zend_Tool_Framework_Client_Console_ResponseDecorator_Colorizer 
    implements Zend_Tool_Framework_Client_Response_ContentDecorator_Interface 
{
    
    protected $_colorOptions = array(
        // blacks
        'black'     => '30m',
        'hiBlack'   => '1;30m',
        'bgBlack'   => '40m',
        // reds
        'red'       => '31m',
        'hiRed'     => '1:31m',
        'bgRed'     => '41m',
        // greens
        'green'     => '32m',
        'hiGreen'   => '1;32m',
        'bgGreen'   => '42m',
        // yellows
        'yellow'    => '33m',
        'hiYellow'  => '1;33m',
        'bgYellow'  => '43m',
        // blues
        'blue'      => '34m',
        'hiBlue'    => '1;34m',
        'bgBlue'    => '44m',
        // magentas
        'magenta'   => '35m',
        'hiMagenta' => '1;35m',
        'bgMagenta' => '45m',
        // cyans
        'cyan'      => '36m',
        'hiCyan'    => '1;36m',
        'bgCyan'    => '46m',
        // whites
        'white'     => '37m',
        'hiWhite'   => '1;37m',
        'bgWhite'   => '47m'
        );
    
    public function getName()
    {
        return 'color';
    }
    
    public function decorate($content, $color)
    {
        if (is_string($color)) {
            $color = array($color);
        }
        
        $newContent = '';
        
        foreach ($color as $c) {
            if (array_key_exists($c, $this->_colorOptions)) {
                $newContent .= "\033[" . $this->_colorOptions[$c];
            }
        }
        
        $newContent .= $content . "\033[m";
        
        return $newContent;
    }
    
    
}
