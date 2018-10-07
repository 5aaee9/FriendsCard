<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * Make friends page cards
 * 
 * @package Friends Card
 * @author Indexyz
 * @version 1.0.0
 * @link https://blog.indexyz.me
 */
class FriendsCard_Plugin implements Typecho_Plugin_Interface {
     /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate() {
        Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = array('FriendsCard_Plugin', 'parse');
        Typecho_Plugin::factory('Widget_Abstract_Contents')->excerptEx = array('FriendsCard_Plugin', 'parse');
        Typecho_Plugin::factory('Widget_Abstract_Comments')->contentEx = array('FriendsCard_Plugin', 'parse');
        Typecho_Plugin::factory('Widget_Archive')->header = array('FriendsCard_Plugin', 'header');
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate() {}

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form) {}

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}


    public static function header() {
        // localhost url
        // $url = Helper::options()->pluginUrl . '/FriendsCard/';
        // echo '<link rel="stylesheet" href="' . $url . 'css/index.css">';
        echo '<link rel="stylesheet" href="https://publish.indexyz.me/css/FirendsCard.css">';
    }

    private static function printToStdout(string $levelName, $data): void {
        if (!is_string($data)) {
            $data = var_export($data, true);
        }
        $fh = fopen('php://stdout', 'w') or die($php_errormsg);
        fwrite($fh, "[" . $levelName . "] ");
        fwrite($fh, "[" . date('m/d/Y h:i:s a', time()) . "]: ");
        fwrite($fh, $data);
        fwrite($fh, "\n");
    }

    public static function info($data): void {
        self::printToStdout("info", $data);
    }

    public static function parse($text, $widget, $lastResult) {
        $text = empty($lastResult) ? $text : $lastResult;
        if ($widget instanceof Widget_Archive
            || $widget instanceof Widget_Abstract_Comments) {
            return preg_replace_callback(
                "/(\[friends\](.*?)\[\/friends\])/is",
                function ($data) {
                    $originData = $data[2];
                    $originData = preg_replace('/<br>/is', '', $originData);
                    $originData = preg_replace('/&quot;/is', '"', $originData);
                    $originData = preg_replace('/<\/p><pre><code>/is', '', $originData);
                    $originData = preg_replace('/<\/code><\/pre><p>/is', '', $originData);

                    $infos = json_decode($originData, true);
                    self::info($originData);
                    $result = '<div class="friends-card">'
                        . '<a class="friends-link" href="'. $infos['url'] . '">'
                        . '<div class="image-overlay"></div>'
                        . '<div class="friends-image" style="background-image:url('. $infos['image'] . ')">'
                        . '<div class="firends-texts">'
                        . '<h2>' . $infos['name'] . '</h2>'
                        . '<h3>' . $infos['descp'] . '</h3>'
                        . '</div></div></a></div>';
                    return $result;
                },
                $text
            );
        }
        return $text;
    }
}
