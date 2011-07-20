<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * A class for generating XML sitemaps
 *
 * @author Philipp Dörner <pd@signalkraft.com>
 * @author Oliver Smith <chemicaloli@gmail.com>
 * @author Sadaoui "SAFAD" Abderrahim <SAFAD.Line@gmail.com>
 * @version 0.7
 * @access public
 * @package sitemaps
 */
class Sitemaps
{

    private $items = array();
    private $error_msg = array();
    private $CI;

    function __construct()
    {
        $this->CI = & get_instance();

        $this->CI->config->load('sitemaps');
    }

    /**
     * Adds a new item to the urlset
     *
     * @param array $new_item
     * @access public
     */
    function add_item($new_item)
    {
        $this->items[] = $new_item;
    }

    /**
     * Adds an array of items to the urlset
     *
     * @param array $new_items array of items
     * @access public
     */
    function add_item_array($new_items)
    {
        $this->items = array_merge($this->items, $new_items);
    }

    /**
     * Generates the sitemap XML data
     *
     * @param string $file_name (optional) if file name is supplied the XML data is saved in it otherwise returned as a string
     * @param bool $gzip (optional) compress sitemap, overwrites config item 'sitemaps_gzip'
     * @access public
     * @return string | bool
     */
    function build($file_name = null, $gzip = NULL)
    {
        $map = $this->CI->config->item('sitemaps_header') . "\n";

        foreach ($this->items as $item)
        {
            $item['loc'] = htmlentities($item['loc'], ENT_QUOTES);
            $map .= "\t<url>\n\t\t<loc>" . $item['loc'] . "</loc>\n";

            $attributes = array("lastmod", "changefreq", "priority");

            foreach ($attributes AS $attr)
            {
                if (isset($item[$attr]))
                {
                    $map .= "\t\t<$attr>" . $item[$attr] . "</$attr>\n";
                }
            }

            $map .= "\t</url>\n\n";
        }

        unset($this->items);

        $map .= $this->CI->config->item('sitemaps_footer');

        if (!is_null($file_name))
        {
            $fh = fopen($file_name, 'w');

            if (!$fh)
            {
                $this->set_error('Could not open sitemaps file for writing: ' . $file_name);
                return FALSE;
            }

            if (!fwrite($fh, $map))
            {
                $this->set_error('Error writing to sitemaps file: ' . $file_name);
                return FALSE;
            }

            fclose($fh);

            if ($this->CI->config->item('sitemaps_filesize_error') && filesize($file_name) > 1024 * 1024 * 10)
            {
                $this->set_error('Your sitemap is bigger than 10MB, most search engines will not accept it.');
                return FALSE;
            }

            if ($gzip OR (is_null($gzip) && $this->CI->config->item('sitemaps_gzip')))
            {
                $gzdata = gzencode($map, 9);
                $file_gzip = str_replace("{file_name}", $file_name, $this->CI->config->item('sitemaps_gzip_path'));

                $fp = fopen($file_gzip, "w");

                if (!$fp)
                {
                    $this->set_error('Unable to open gzipped file path for writing: ' . $fp);
                    return FALSE;
                }

                if (!fwrite($fp, $gzdata))
                {
                    $this->set_error('Error writing to gzipped sitemaps file: ' . $fp);
                    return FALSE;
                }

                fclose($fp);

                // Delete the uncompressed sitemap
                if (!unlink($file_name))
                {
                    $this->set_error('Unable to remove uncompressed sitemap: ' . $file_name);
                }

                return $file_gzip;
            }

            return $file_name;
        }
        else
        {
            return $map;
        }
    }

    /**
     * Generate a sitemap index file pointing to other sitemaps you previously built
     *
     * @param array $urls array of urls, each being an array with at least a loc index
     * @param string $file_name (optional) if file name is supplied the XML data is saved in it otherwise returned as a string
     * @param bool $gzip (optional) compress sitemap, overwrites config item 'sitemaps_gzip'
     * @access public
     * @return string | bool
     */
    function build_index($urls, $file_name = null, $gzip = null)
    {

        $index = $this->CI->config->item('sitemaps_index_header') . "\n";

        foreach ($urls as $url)
        {
            $url['loc'] = htmlentities($url['loc'], ENT_QUOTES);
            $index .= "\t<sitemap>\n\t\t<loc>" . $url['loc'] . "</loc>\n";

            if (isset($url['lastmod']))
            {
                $index .= "\t\t<lastmod>" . $url['lastmod'] . "</lastmod>\n";
            }

            $index .= "\t</sitemap>\n\n";
        }

        $index .= $this->CI->config->item('sitemaps_index_footer');

        if (!is_null($file_name))
        {
            $fh = fopen($file_name, 'w');

            if (!$fh)
            {
                $this->set_error('Could not open sitemaps index for writing: ' . $fh);
                return FALSE;
            }

            if (!fwrite($fh, $index))
            {
                $this->set_error('Could not write to sitemaps index: ' . $fh);
                return FALSE;
            }

            fclose($fh);

            if ($this->CI->config->item('sitemaps_filesize_error') && filesize($file_name) > 1024 * 1024 * 10)
            {
                $this->set_error('Your sitemap index is bigger than 10MB, most search engines will not accept it.');
                return FALSE;
            }

            if ($gzip OR (is_null($gzip) && $this->CI->config->item('sitemaps_index_gzip')))
            {
                $gzdata = gzencode($index, 9);
                $file_gzip = str_replace("{file_name}", $file_name, $this->CI->config->item('sitemaps_index_gzip_path'));

                $fp = fopen($file_gzip, "w");

                if (!$fp)
                {
                    $this->set_error('Could not open gzipped sitemaps index for writing: ' . $fp);
                    return FALSE;
                }

                if (!fwrite($fp, $gzdata))
                {
                    $this->set_error('Could not write to gzipped sitemaps index: ' . $fp);
                    return FALSE;
                }

                fclose($fp);

                // Delete the uncompressed sitemap index
                if (!unlink($file_name))
                {
                    $this->set_error('Could not write to remove uncompressed sitemaps index: ' . $file_name);
                }

                return $file_gzip;
            }

            return $file_name;
        }
        else
        {
            return $index;
        }
    }

    /**
     * Notify search engines of your updates sitemap
     *
     * @param string $url_xml absolute URL of your sitemap, use Codeigniter's site_url()
     * @param array $search_engines array of search engines to ping, see config file for notes
     * @access public
     * @return array HTTP reponse codes
     */
    function ping($url_xml, $search_engines = NULL)
    {

        if (is_null($search_engines))
        {
            $search_engines = $this->CI->config->item('sitemaps_search_engines');
        }

        $statuses = array();

        foreach ($search_engines AS $engine)
        {
            $status = 0;
            if ($fp = @fsockopen($engine['host'], 80))
            {
                $engine['url'] = empty($engine['url']) ? "/ping?sitemap=" : $engine['url'];

                $req = 'GET ' . $engine['url'] .
                        urlencode($url_xml) . " HTTP/1.1\r\n" .
                        "Host: " . $engine['host'] . "\r\n" .
                        $this->CI->config->item('sitemaps_user_agent') .
                        "Connection: Close\r\n\r\n";
                fwrite($fp, $req);
                while (!feof($fp))
                {
                    if (@preg_match('~^HTTP/\d\.\d (\d+)~i', fgets($fp, 128), $m))
                    {
                        $status = intval($m[1]);
                        break;
                    }
                }
                fclose($fp);
            }

            $statuses[] = array("host" => $engine['host'], "status" => $status, "request" => $req);
        }

        if ($this->CI->config->item('sitemaps_log_http_responses') OR $this->CI->config->item('sitemaps_debug'))
        {
            foreach ($statuses AS $reponse)
            {
                $message = "Sitemaps: " . $reponse['host'] . " responded with HTTP status " . $reponse['status'];

                if ($this->CI->config->item('sitemaps_log_http_responses'))
                {
                    $level = $reponse['status'] == 200 ? 'debug' : 'error';
                    log_message($level, $message);
                }

                if ($this->CI->config->item('sitemaps_debug'))
                {
                    echo "<p>" . $message . " after request:</p>\n<pre>" . $reponse['request'] . "</pre>\n\n";
                }
            }
        }

        return $statuses;
    }

    /**
     * Show error messages
     *
     * @access	public
     * @param	string
     * @return	string
     */
    function display_errors($open = '<p>', $close = '</p>')
    {
        $str = '';
        foreach ($this->error_msg as $val)
        {
            $str .= $open . $val . $close;
        }

        return $str;
    }

    /**
     * Set error message
     *
     * @access	public
     * @param	string
     * @return	void
     */
    function set_error($msg)
    {
        $CI = & get_instance();

        if (is_array($msg))
        {
            foreach ($msg as $val)
            {
                $this->error_msg[] = $val;
                log_message('error', $val);
            }
        }
        else
        {
            $this->error_msg[] = $msg;
            log_message('error', $msg);
        }
    }

}