<?php

/* 
| ----------------------------------------------
|      Assets Helper
| ---------------------------------------------
|
| Contains functions that aid in loading files 
| from the assets folder.
*/

if (!function_exists('assets_url'))
{
    /** 
     * Returns a URL to the assets folder
     * that has $url appended at the end
     * 
     * @param   string  $url    URL to append to the assets URL
     * @return  string  Assets URL
     */
    function assets_url($url)
    {
        return base_url().'/assets/'.$url;
    }
}

if (!function_exists('script_tag'))
{
    /** 
     * Returns a script tag with the source file
     * from the assets folder defined by $asset_path
     * 
     * @param string $assets_path Path to the JS file from the assets file
     * @return string The script tag
     */
    function script_tag($assets_path)
    {

    }
}
