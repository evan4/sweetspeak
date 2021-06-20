<?php

namespace Mycms;

use Exception;

class View
{

    /**
     * The path to the view file.
     *
     * @var string
     */
    protected $path;

    public $layout = 'default';

    public function __construct($path,  $layout = null)
    {
        $views = explode("@", $path);

        if ($layout) {
            $this->layout = $layout;
        }

        $this->path = VIEWS.$views[0].DIRECTORY_SEPARATOR.$views[1].'.php';

    }

    public function render($data = null, $meta = null)
    {
        if(is_array($data)){
            extract($data);
        }

        if(is_array($meta)){
            extract($meta);
        }

        if(is_file($this->path)){
            ob_start();
            require_once $this->path;
            $content = ob_get_clean();
        }
        else{
            throw new Exception("Не найден вид {$this->path}");
        }

        $layoutFile = VIEWS . "layouts/{$this->layout}.php";

        if (is_file($layoutFile)) {
            require_once $layoutFile;
        } else {
            throw new Exception("Не найден шаблон {$this->layout}");
        }
        
    }

}
