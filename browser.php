<?php

# Browses a directory, looking for some files.
#
# <code>
# $browser = new PDoc_Browser('./src');
# $files = $browser->search('php', array('test'));
# </code>
#
class PDoc_Browser
{
  public    $basedir;
  protected $exclude_regexp = false;
  
  function __construct($basedir)
  {
    $this->basedir = $basedir;
    if (!is_dir($basedir)) {
      throw new Exception("No such directory: $basedir.");
    }
  }
  
  # Searches a directory for files matching a file extension.
  # It will skip files that match patterns defined in the excludes' array.
  function & search($extension, $excludes=array())
  {
    if (!empty($excludes))
    {
      $excludes = array_map('preg_quote', $excludes);
      $this->exclude_regexp = ':^'.preg_quote(rtrim($this->basedir, '/').'/').'('.implode('|', $excludes).'):';
    }
    else {
      $this->exclude_regexp = false;
    }
    return $this->recursive_search($this->basedir, $extension);
  }
  
  # Recursively parses a directory.
  # 
  # It extracts files that match an extension,
  # skipping those that match the given exclusion patterns.
  protected function & recursive_search($dir, $extension)
  {
    echo "Searching in $dir/\n";
    
    $files = array();
    if ($dh = opendir($dir))
    {
      while (($file = readdir($dh)) !== false)
      {
        if ($file == '.' or $file == '..' or substr($file, 0, 1) == '.') {
          continue;
        }
        $path = rtrim($dir, '/').'/'.ltrim($file, '/');
        
        if ($this->exclude_regexp !== false and preg_match($this->exclude_regexp, $path))
        {
          echo "Skips: $path\n";
          continue;
        }
        
        if (is_dir($path)) {
          $files = array_merge($files, $this->recursive_search($path, $extension));
        }
        elseif (preg_match('/\.'.$extension.'$/', $file))
        {
          echo "Found: $path\n";
          $files[] = $path;
        }
      }
      closedir($dh);
    }
    return $files;
  }
}

?>
