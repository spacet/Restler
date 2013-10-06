<?php
namespace Luracast\Restler;

/**
 * Utility class for generating html tags in an object oriented way
 *
 * @category   Framework
 * @package    Restler
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc4
 *
 * ============================== magic  methods ===============================
 * @method Tags name(string $value) name attribute
 * @method Tags action(string $value) action attribute
 * @method Tags placeholder(string $value) placeholder attribute
 * @method Tags value(string $value) value attribute
 * @method Tags required(boolean $value) required attribute
 *
 * =========================== static magic methods ============================
 * @method static Tags form() creates a html form
 * @method static Tags input() creates a html input element
 * @method static Tags button() creates a html button element
 *
 */
class Tags
{
    public static $humanReadable = true;
    public $prefix = '';
    public $indent = '    ';
    protected $attributes = array();
    protected $children = array();
    public $tag;
    protected static $instances = array();

    public function __construct($name, array $children = array())
    {
        $this->tag = $name;
        $this->children = $children;
    }

    /**
     * Get Tag by id
     *
     * Retrieve a tag by its id attribute
     *
     * @param string $id
     *
     * @return Tags|null
     */
    public static function byId($id)
    {
        return Util::nestedValue(static::$instances, $id);
    }

    public function toString($prefix = '', $indent = '    ')
    {
        $this->prefix = $prefix;
        $this->indent = $indent;
        return $this->__toString();
    }

    /**
     * @param       $name
     * @param array $children
     *
     * @return Tags
     */
    public static function __callStatic($name, array $children)
    {
        if (isset($children[0]) && is_array($children[0]))
            $children = $children[0];
        return new static($name, $children);
    }

    /**
     * Set the id attribute of the current tag
     *
     * @param string $value
     *
     * @return string
     */
    public function id($value)
    {
        $this->attributes['id'] = isset($value)
            ? (string)$value
            : Util::nestedValue($this->attributes, 'name');
        static::$instances[$value] = $this;
        return $this;
    }

    public function __get($name)
    {
        if (isset($this->attributes[$name]))
            return $this->attributes[$name];
        return;
    }

    /**
     * @param $attribute
     * @param $value
     *
     * @return Tags
     */
    public function __call($attribute, $value)
    {
        $value = $value[0];
        if (is_bool($value)) {

        }
        $this->attributes[$attribute] = is_bool($value)
            ? ($value ? 'true' : 'false')
            : (string)$value;
        return $this;
    }

    public function __toString()
    {
        $children = '';
        if (static::$humanReadable) {
            $lineBreak = false;
            foreach ($this->children as $key => $child) {
                if ($child instanceof $this) {
                    $child->prefix = $this->prefix . $this->indent;
                    $child->indent = $this->indent;
                    $children .= PHP_EOL . $child;
                    $lineBreak = true;
                } else {
                    $children .= $child;
                }
            }
            if ($lineBreak)
                $children .= PHP_EOL . $this->prefix;
        } else {
            $children = implode('', $this->children);
        }
        $attributes = '';
        foreach ($this->attributes as $attribute => &$value)
            $attributes .= " $attribute=\"$value\"";

        if (count($this->children))
            return static::$humanReadable
                ? "$this->prefix<{$this->tag}{$attributes}>"
                . "$children"
                . "</{$this->tag}>"
                : "<{$this->tag}{$attributes}>$children</{$this->tag}>";

        return "$this->prefix<{$this->tag}{$attributes}/>";
    }
} 