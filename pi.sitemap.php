<?php
class Plugin_sitemap extends Plugin
{

  var $meta = array(
    'name'       => 'Sitemap',
    'version'    => '0.1',
    'author'     => 'Max Westen',
    'author_url' => 'http://dlmax.org'
  );

  function __construct()
  {
    parent::__construct();
    $this->site_root = Statamic::get_site_root();
    $this->site_url = Statamic::get_site_url();
    $this->content_root = Statamic::get_content_root();
    $this->data = array();

    /**
     * Parses a maximum of max_entry_limit entries in a folder
     */
    $this->max_entry_limit = 1000;
  }

  public function index()
  {
    $output = false;
    $url = '/';

    $this->parse_tree_data($url);
    
    if (count($this->content) > 0) {
      $output = $this->parse_loop($this->content, $this->data);
    }

    return $output;
  }


  /**
   * Runs trough the navigation tree for as long as needed and adds items to $this->data.
   * @param $url
   */
  private function parse_tree_data($url) {
    $url = Statamic_Helper::resolve_path($url);
    $tree = Statamic::get_content_tree($url, 1, 1000, true, false);

    if (count($tree) > 0) {
      foreach ($tree as $item) {
        if ($item['type'] == 'file') {
          $this->parse_file_item($item, $url);
        }
        if ($item['type'] == 'folder') {
          $this->parse_folder_item($item);
          if ($item['has_children']) {
            $this->parse_tree_data($item['url']);
          }
          if ($item['has_entries']) {
            $list = Statamic::get_content_list($item['url'], $this->max_entry_limit, 0, false, true, 'date', 'desc');
            foreach ($list as $entry) {
              $this->parse_entry_item($entry);
            }
          }
        }
      }
    }
  }


  /**
   * This adds an item to the sitemap containing a folder (checks page.md)
   * @param $item
   */
  private function parse_folder_item($item){
    $data = Statamic::get_content_meta("page", $item['url']);    
    $permalink = Statamic_helper::reduce_double_slashes($this->site_url . '/' .$item['url']);
    $moddate = array_key_exists('last_modified',$data) ? date("Y-m-d", $data['last_modified']) : date("Y-m-d", strtotime("-1 day"));
    $this->data[] = array(
      'loc' => $permalink,
      'lastmod' =>  $moddate,
      'changefreq' => $this->set_frequency($moddate),
      'priority' => $this->set_priority($data),

    );
  }
  
  /**
   * This adds an item to the sitemap containing a file
   * @param $item
   */
  private function parse_file_item($item, $folder=null){
    $data = Statamic::get_content_meta($item['slug'], $folder);
    $moddate  = (array_key_exists('last_modified', $data)) ? $data['last_modified'] : date("Y-m-d", strtotime("-1 day"));
    $permalink = Statamic_helper::reduce_double_slashes($this->site_url . '/' .$item['url']);
    $this->data[] = array(
      'loc' => $permalink,
      'lastmod' => date("Y-m-d", $moddate),
      'changefreq' => $this->set_frequency($moddate),
      'priority' => $this->set_priority($data),

    );
  }

  /**
   * This adds an item to the sitemap containing an entry 
   * @param $item
   */
  private function parse_entry_item($item){
    $this->data[] = array(
      'loc' => $item['permalink'],
      'lastmod' => date("Y-m-d", $item['last_modified']),
      'changefreq' => $this->set_frequency($item['last_modified']),
      'priority' => $this->set_priority($item),
    );
  }

  /**
   * This returns the change frequency based on last modification date.
   * @param $timestamp 
   * @return string
   */
  private function set_frequency($timestamp) {
    if ($timestamp === false) {
      return 'never';
    }
    elseif ($timestamp <= strtotime('-1 year')){      
      return 'yearly';
    } 
    elseif ($timestamp <= strtotime('-1 month')){
      return 'monthly';
    }
    elseif ($timestamp <= strtotime('-1 week')){
      return 'weekly';
    }
    elseif ($timestamp <= strtotime('-1 day')){
      return 'daily';
    }
    else {
      return 'hourly';
    }

  }

  private function set_priority($item) {
    if (array_key_exists('priority', $item)) {
      return $item['priority'];
    } else {
      return 0.5;
    }
  }

}