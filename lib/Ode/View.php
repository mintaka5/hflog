<?php
namespace Ode;

/**
 * View.php
 *
 * Template creator and decision-maker
 *
 * @author cjwalsh
 * @version 1.0
 * @package Ode
 * @name View
 * @todo May have to move this out, and add the view instance to the controller one
 *
 */
class View extends \Savant3
{
    /**
     * A reference to this instance
     *
     * @var View
     * @access private
     */
    private static $instance = null;

    /**
     * Path on server to template files
     *
     * @var string
     * @access private
     */
    private $path = null;

    protected $manager = null;

    /**
     * @see Savant3 documentation
     *
     * @var string
     * @access private
     */
    private $type = "template";

    /**
     *
     * @var string
     * @access private
     */
    private $layout = false;

    /**
     * Template default extension
     *
     * @var string
     * @access public
     */
    const TEMPLATE_EXT = ".tpl.php";

    private $_formRenderer = false;

    const FORM_ELEMENT_TEMPLATE = "form/element/template.tpl.php";

    const FORM_FIELDSET_CLOSE_TEMPLATE = "form/fieldset/close.tpl.php";

    const FORM_FIELDSET_OPEN_TEMPLATE = "form/fieldset/open.tpl.php";

    const FORM_FIELDSET_HIDDEN_TEMPLATE = "form/fieldset/hidden/open.tpl.php";

    const FORM_HEADER_TEMPLATE = "form/header/template.tpl.php";

    private $_fileCreation = false;

    private $_assetsUri = "";

    private $_page = "";

    /**
     * Retrieve an insance of this class
     *
     * @return View
     * @access public
     */
    public static function getInstance()
    {
        //Util::debug(self::$instance);

        return self::$instance;
    }

    /**
     * Set the instance reference of this class
     *
     * @param \Savant3 $view
     * @return void
     * @access private
     */
    private function setInstance(Savant3 $view)
    {
        self::$instance = $view;
    }

    /**
     * Constructor
     *
     * @param string $path
     * @return void
     * @access public
     */
    public function __construct($path, $ajax = false)
    {
        $this->setPath($this->getType(), $path);

        self::$instance = $this;

        $this->setManager();

        $this->setAuth();

        $this->setFormRenderer();

        if ($ajax === false) {
            Controller::getInstance()->init($this);
            //Util::debug(Ode_Controller::getInstance());

            $this->setLayout("layout" . self::TEMPLATE_EXT);

            $this->setPage();

            $this->setContentTemplate();

            /**
             * had to remove this from constructor, in order to assign MVC global content
             * or content that is not directly part of the MVC, and placed the display method
             * in the site app's init.php file
             */
            //self::getInstance()->display($this->getLayout());
        }

        //Ode_Log::getInstance()->log("Done initializing the View/Template object", PEAR_LOG_INFO);
    }

    /**
     *
     * Sets the form renderer for HTML_QuickForm2
     * @access private
     * @return void
     */
    private function setFormRenderer()
    {
        $this->_formRenderer = \HTML_QuickForm2_Renderer::factory("default");

        $this->_formRenderer->setTemplateForClass('HTML_QuickForm2', '<form{attributes}>{hidden}{content}<qf:reqnote><div class="text-danger top-20">{reqnote}</div></qf:reqnote></form>');
        $this->_formRenderer->setTemplateForClass('HTML_QuickForm2_Element', '');
        $this->_formRenderer->setTemplateForClass('HTML_QuickForm2_Element_InputText', '<div class="form-group"><qf:error><span class="text-danger">{error}</span>&nbsp;</qf:error><qf:label><label for="{id}">{label}</label></qf:label><qf:required><span class="text-danger">*</span></qf:required>{element}</div>');
        $this->_formRenderer->setTemplateForClass('HTML_QuickForm2_Element_Textarea', '<div class="form-group"><qf:error><span class="text-danger">{error}</span>&nbsp;</qf:error><qf:label><label for="{id}">{label}</label></qf:label><qf:required><span class="text-danger">*</span></qf:required>{element}</div>');
        $this->_formRenderer->setTemplateForClass('HTML_QuickForm2_Element_InputPassword', '<div class="form-group"><qf:error><span class="text-danger">{error}</span>&nbsp;</qf:error><qf:label><label for="{id}">{label}</label></qf:label><qf:required><span class="text-danger">*</span></qf:required>{element}</div>');
        $this->_formRenderer->setTemplateForClass('HTML_QuickForm2_Element_Select', '<div class="form-group"><qf:error><span class="text-danger">{error}</span>&nbsp;</qf:error><qf:label><label for="{id}">{label}</label></qf:label><qf:required><span class="text-danger">*</span></qf:required>{element}</div>');
        $this->_formRenderer->setTemplateForClass('HTML_QuickForm2_Element_InputCheckbox', '<div class="checkbox"><qf:error><span class="text-danger">{error}</span>&nbsp;</qf:error><label>{element}<qf:required><span class="text-danger">*</span></qf:required></label></div>');
        $this->_formRenderer->setTemplateForClass('HTML_QuickForm2_Element_InputButton', '<div class="form-group"><button{attributes}>{content}</button></div>');
        $this->_formRenderer->setTemplateForClass('HTML_QuickForm2_Element_Button', '{element}');
        $this->_formRenderer->setTemplateForClass('HTML_QuickForm2_Container_Group', '<div class="{class}"><qf:label><label>{label}</label></qf:label>{content}</div>');
    }

    public function getFormRenderer()
    {
        return $this->_formRenderer;
    }

    /**
     *
     * @param string $name
     * @access private
     */
    public function setLayout($name)
    {
        $this->layout = $name;
    }

    /**
     *
     * @return string
     * @access public
     */
    public function getLayout()
    {
        return $this->layout;
    }

    private function setPage()
    {
        $this->_page = Manager::getInstance()->getPage();
    }

    private function getPage()
    {
        return $this->_page;
    }

    /**
     * Creates and assigns content to the view instance
     *
     * @return void
     * @access private
     */
    private function setContentTemplate()
    {
        if (!$this->templateExists($this->getPage() . self::TEMPLATE_EXT)) {
            /**
             * @todo figure out a way to get the template path without using config variable
             */
            if ($this->createFiles() == true) {
                File::writeLine(APP_VIEW_PATH . DIRECTORY_SEPARATOR . $this->getPage() . self::TEMPLATE_EXT, '<div><?= $this->controllerName; ?></div>', FILE_MODE_WRITE);
            } else {
                header("HTTP/1.0 404 Not Found");
                echo $this->fetch("404.tpl.php");
                exit();
            }
        }

        self::getInstance()->assign("contentforlayout", self::getInstance()->fetch($this->getPage() . self::TEMPLATE_EXT));

        //Ode_Log::getInstance()->log("Assigned " . $this->getPage() . self::TEMPLATE_EXT . " to layout content.", PEAR_LOG_INFO);
    }

    private function createFiles()
    {
        return $this->_fileCreation;
    }

    public function setFileCreation($bool = true)
    {
        $this->_fileCreation = $bool;
    }

    /**
     * Retrieve the type of view (template/resource)
     *
     * @see Savant3 documentation
     * @return string
     * @access private
     */
    private function getType()
    {
        return $this->type;
    }

    /**
     * Set the type of view (template/resource)
     *
     * @see Savant3 documentation
     * @param string $type
     */
    private function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Looks for the content template file
     * within the specified template paths
     *
     * @param string $filename
     * @return string/boolean
     */
    private function templateExists($filename)
    {
        $paths = self::getInstance()->getConfig("template_path");
        foreach ($paths as $path) {
            if (file_exists($path . $filename)) {
                return $path;
            }
        }

        return false;
    }

    private function setManager(Manager $manager = null)
    {
        if (!is_null($manager)) {
            $this->manager = $manager;
        } else {
            $this->manager = Manager::getInstance();
        }

        self::$instance->assign("manager", $this->getManager());
    }

    private function getManager()
    {
        return $this->manager;
    }

    /**
     * View-based date formatter
     * @param mixed $date
     * @param string $format
     * @return string|string|boolean
     */
    public function date($date, $format = "m/d/Y")
    {
        if (is_int($date)) {
            return date($format, $date);
        } else if (is_string($date)) {
            return date($format, strtotime($date));
        } else {
            return false;
        }
    }

    public function truncate($str, $limit = 10, $tail = "&#133;")
    {
        $str = strip_tags($str);
        $orig = preg_split("/[\s\t\r\n]+/", $str);
        $new = array_slice($orig, 0, $limit, true);

        if (count($orig) <= count($new)) {
            return $str;
        } else {
            return implode(" ", $new) . $tail;
        }
    }

    public function binToText($binary)
    {
        $args = func_get_args();

        $defaults = array("No", "Yes");

        if (isset($args[1])) {
            $defaults[0] = $args[1];
        }

        if (isset($args[2])) {
            $defaults[1] = $args[2];
        }

        return ($binary == 0) ? $defaults[0] : $defaults[1];
    }

    public function twitterize($str)
    {
        $new_str = preg_replace('@(https?://([-\w\.]+)+(/([\w/_\.]*(\?\S+)?(#\S+)?)?)?)@', '<a href="$1" rel="nofollow" target="_blank">$1</a>', $str);
        $new_str = preg_replace('/@(\w+)/', '<a href="http://twitter.com/$1" rel="nofollow" target="_blank">@$1</a>', $new_str);
        $new_str = preg_replace('/\#(\w+)/', ' <a href="http://search.twitter.com/search?q=%23$1" rel="nofollow" target="_blank">#$1</a>', $new_str);

        $new_str = stripslashes($new_str);

        return $new_str;
    }

    /**
     * @todo Incorporate this into template, so as to separate Ode engine's manager from asset allocation
     */
    /*public function setAssetsURI($uri) {
        $this->_assetsUri = $uri;

        //$this->assign("assets_url", $this->getAssetsURI());
    }

    public function getAssetsURI() {
        return $this->_assetsUri;
    }*/
}